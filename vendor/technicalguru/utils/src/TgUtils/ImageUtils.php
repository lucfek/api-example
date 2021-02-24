<?php

namespace TgUtils;

class ImageUtils {

	/**
	 * Creates a thumbnail image from the given image at the path.
	 * @param string $imagepath - path of original image
	 * @param int    $maxWidth  - maximum new width
	 * @param int    $maxHeight - maximum new height
	 * @param string $targetDir - target directory
	 * @return string - the path of the new image file, e.g. /target/dir/basename.variation.png (or NULL in case of GD issues)
	 */
	public static function createThumbnail($imagePath, $maxWidth, $maxHeight, $targetDir = NULL) {
		$targetPath = self::getNewPath($imagePath, 'thumbnail', $targetDir);

		if ($targetPath != NULL) {
			if (class_exists('Imagick')) {
				$image = new \Imagick($imagePath);
				$image->thumbnailImage($maxWidth, $maxHeight, TRUE);
				if (!$image->writeImage($targetPath)) {
					$targetPath = NULL;
				}
			} else {
				// We use GD
				$imageDetails = getimagesize($imagePath);
				$image = self::readImage($imagePath, $imageDetails);
				if ($image != NULL) {
					// Compute new dimensions
					$size = self::computeNewSize($imageDetails[0], $imageDetails[1], $maxWidth, $maxHeight);

					// Scale it
					$thumbnail = imagecreatetruecolor($size->width, $size->height);
					imagecopyresized($thumbnail, $image, 0, 0, 0, 0, $size->width, $size->height, $imageDetails[0], $imageDetails[1]);
					// Writing it
					$targetPath = self::writeImage($thumbnail, $targetPath);
					imagedestroy($thumbnail);
					imagedestroy($image);
				} else {
					$targetPath = NULL;
				}
			}
		}
		return $targetPath;
	}

	/**
	 * Reads an image with GD library.
	 * @param string $imagePath - where the image is stored.
	 * @param array  $imageDetails - the result from getimagesize() call when executed before.
	 * @return resource form GD library
	 */
	public static function readImage($imagePath, $imageDetails = NULL) {
		if (file_exists($imagePath)) {
			if ($imageDetails == NULL) $imageDetails = getimagesize($imagePath);
			if ($imageDetails !== FALSE) {
				$gdInfo  = gd_info();
				$image        = NULL;
				switch ($imageDetails[2]) {
				case IMAGETYPE_BMP:
					if ($gdInfo['BMP Support']) {
						$image = imagecreatefrombmp($imagePath);
					}
					break;
				case IMAGETYPE_GIF:
					if ($gdInfo['GIF Read Support']) {
						$image = imagecreatefromgif($imagePath);
					}
					break;
				case IMG_JPEG: 
				case IMG_JPEG: 
					if ($gdInfo['JPEG Support']) {
						$image = imagecreatefromjpeg($imagePath);
					}
					break;
				case IMAGETYPE_PNG:
					if ($gdInfo['PNG Support']) {
						$image = imagecreatefrompng($imagePath);
					}
					break;
				case IMAGETYPE_WBMP:
					if ($gdInfo['WBMP Support']) {
						$image = imagecreatefromwbmp($imagePath);
					}
					break;
				case IMG_XPM:
					if ($gdInfo['XPM Support']) {
						$image = imagecreatefromxpm($imagePath);
					}
					break;
				case IMAGETYPE_WEBP:
					if ($gdInfo['WebP Support']) {
						$image = imagecreatefromwebp($imagePath);
					}
					break;
				}
			}
			return $image;
		}
		return NULL;
	}

	/**
	  * Write an GD image to the specified path.
	  * @param resource $image - the GD image resource
	  * @param string   $path  - the path where to write to (must include the filetype extension)
	  * @return string - the path when writing was successful, NULL otherwise
	  */
	public static function writeImage($image, $path) {
		$gdInfo  = gd_info();
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		switch ($ext) {
		case 'png':
			if (!imagepng($thumbnail, $path)) $path = NULL;
			break;
		case 'jpg':
		case 'jpeg':
			if (!imagejpeg($thumbnail, $path)) $path = NULL;
			break;
		case 'webp':
			if (!imagewebp($thumbnail, $path)) $path = NULL;
			break;
		case 'xpm':
			if (!imagexbm($thumbnail, $path)) $path = NULL;
			break;
		case 'wbmp':
			if (!imagewbmp($thumbnail, $path)) $path = NULL;
			break;
		case 'gif':
			if (!imagegif($thumbnail, $path)) $path = NULL;
			break;
		case 'bmp':
			if (!imagebmp($thumbnail, $path)) $path = NULL;
			break;
		default:
			$path = NULL;
		}
		return $path;
	}

	/**
	 * Returns an object with width and height attributes to resize.
	 * @param int $origWidth  - original width
	 * @param int $origHeight - original height
	 * @param int $maxWidth   - maximum new width
	 * @param int $maxHeight  - maximum new height
	 * @return object with width an height attribute
	 */
	public static function computeNewSize($origWidth, $origHeight, $maxWidth, $maxHeight) {
		// Compute new dimensions
		$rc = new \stdClass;
		$rc->width  = $maxWidth;
		$rc->height = $maxHeight;
		if ($origWidth < $origHeight) {
			// Portrait
			$rc->width = intval($origWidth * $rc->height / $origHeight);
		} else {
			// Landscape or squared
			$rc->height = intval($origHeight * $rc->width / $origWidth);
		}
		return $rc;
	}

	/**
	  * Cuts and scale the given image to new height and new width. The image will not be stretched or densed.
	  * @param string $imagePath - path of image to read from
	  * @param string $variation - The identifier to be included in the filename when writing the new image
	  * @param int    $newHeight - new height of image 
	  * @param int    $newWidth  - new width of image 
	  * @param string $targetDir - target directory
	  * @return string the path to the new image file or NULL if action failed
	  * @return string - the path of the new image file, e.g. /target/dir/basename.variation.png (or NULL in case of GD issues)
	  */
	public static function cropAndScale($imagePath, $variation, $newHeight, $newWidth, $options = NULL, $targetDir = NULL) {
		$targetPath = self::getNewPath($imagePath, $variation, $targetDir);
		if ($options == NULL) $options = self::cropAndScaleOptions();

		if ($targetPath != NULL) {
			$actions = NULL;
			if (class_exists('Imagick')) {
				$image = new \Imagick($imagePath);
				$width  = $image->getImageWidth();
				$height = $image->getImageHeight();
				$actions = self::computeCropAndScale($width, $height, $newWidth, $newHeight, $options);
				if ($actions->scale != 1) {
					$image->scaleImage($actions->factor * $width, 0);
				}
				if ($image->cropImage($actions->width, $actions->height, $actions->x, $actions->y)) {
					// Writing it
					if (!$image->writeImage($targetPath)) {
						$targetPath = NULL;
					}
				} else {
					$targetPath = NULL;
				}
			} else {
				// We use GD
				$imageDetails = getimagesize($imagePath);
				$actions = self::computeCropAndScale($imageDetails[0], $imageDetails[1], $newWidth, $newHeight, $options);
				
				// Scale it
				$target = imagecreatetruecolor($actions->width, $actions->height);
				imagecopyresized($target, $image, 0, 0, $actions->x / $actions->factor, $actions->y / $actions->factor, $newWidth, $newHeight, $imageDetails[0] / $actions->factor, $imageDetails[1] / $actions->factor);
				$targetPath = self::writeImage($target, $targetPath);
				imagedestroy($target);
				imagedestroy($image);
			}
		}
		return $targetPath;
	}

	/**
	  * Creates options for the cropAndScale() function.
	  * @param boolean $cropX   - whether cropping width is permitted
	  * @param boolean $cropY   - whether cropping height is permitted
	  * @param boolean $centerX - whether new image will be centered at X axis in case of cropping
	  * @param boolean $centerY - whether new image will be centered at Y axis in case of cropping
	  * @return object - the options for the  cropAndScale() function
	  */
	public static function cropAndScaleOptions($centerX = TRUE, $centerY = FALSE) {
		$rc = new \stdClass;
		$rc->centerX = $centerX;
		$rc->centerY = $centerY;
		return $rc;
	}

	public static function computeCropAndScale($origWidth, $origHeight, $maxWidth, $maxHeight, $options = NULL) {
		$rc = new \stdClass;
		if ($options == NULL) $options = self::cropAndScaleOptions();


		// Assume X scaling to maxWidth
		$rc->factor = $maxWidth / $origWidth;

		// Positioning for cropping
		$rc->x = 0;
		$rc->y = 0;

		// Compute potential target
		$rc->width   = $maxWidth;
		$rc->height  = $rc->factor * $origHeight;
		$rc->options = $options;

		if ($rc->height < $maxHeight) {
			// scale up and crop along X axis
			$rc->factor = $maxHeight / $origHeight;
			$rc->width  = $rc->factor * $origWidth;
			$rc->height = $maxHeight;
			if ($options->centerX) {
				$rc->x = ($rc->width - $maxWidth) / 2;
			}
		} else if ($rc->height > $maxHeight) {
			if ($options->centerY) {
				$rc->y = ($rc->height - $maxHeight) / 2;
			}
		}
		$rc->width  = $maxWidth;
		$rc->height = $maxHeight;

		return $rc;
	}

	/**
	  * Computes the variation path of an image file using a priority: PNG, JPEG, WEBP, XPM, WBMP, GIF, BMP
	  * @param string $imagePath - the original image path
	  * @param string $variation - the identifier to be included in new filename
	  * @param string $targetDir - the target directory
	  * @return string - the new path of the image file, e.g. /target/dir/basename.variation.png (or NULL in case of GD issues)
	  */
	public static function getNewPath($imagePath, $variation, $targetDir = NULL) {
		$pathinfo   = pathinfo($imagePath);
		$targetPath = '.';
		if ($targetDir == NULL) {
			$targetPath = $pathinfo['dirname'].'/'.$pathinfo['filename'].'.'.$variation.'.';
		} else {
			$targetPath = $targetDir.'/'.$pathinfo['filename'].'.'.$variation.'.';
		}
		if (class_exists('Imagick')) {
			$targetPath .= 'png';
		} else {
			$gdInfo  = gd_info();
			// Preferential order PNG, JPEG, WEBP, XPM, WBMP, GIF, BMP 
			if ($gdInfo['PNG Support']) {
				$targetPath .= 'png';
			} else if ($gdInfo['JPEG Support']) {
				$targetPath .= 'jpg';
			} else if ($gdInfo['WebP Support']) {
				$targetPath .= 'webp';
			} else if ($gdInfo['XPM Support']) {
				$targetPath .= 'xpm';
			} else if ($gdInfo['WBMP Support']) {
				$targetPath .= 'wbmp';
			} else if ($gdInfo['GIF Create Support']) {
				$targetPath .= 'gif';
			} else if ($gdInfo['BMP Support']) {
				$targetPath .= 'bmp';
			} else {
				$targetPath = NULL;
			}
		}
		return $targetPath;
	}
}

