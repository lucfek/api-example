<?php

namespace TgUtils;

/**
 * Helps to slugify words.
 * @author ralph
 *        
 */
class Slugify {

    /** The default slug map table */
    protected static $defaultMappingTable;
    
    /**
     * Slugify the given string.
     * @param string $s - the text to slugify.
     * @param array $mappingTable - replacement map for characters in UTF8
     * @param string the slug
     */
    public static function slugify($s, $mappingTable = NULL) {
        if ($mappingTable == NULL) {
            $mappingTable = self::getDefaultMappingTable();
        }
		return strtolower(strtr(self::normalize($s), $mappingTable));
    }
    
    /**
     * Returns the default mapping table.
     * @return array a replacement map for characters in UTF8
     */
    public static function getDefaultMappingTable() {
        if (self::$defaultMappingTable == NULL) {
		    self::$defaultMappingTable = array(
			    'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
			    'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			    'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
			    'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
			    'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
			    'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
			    'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
			    'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', ' '=>'-'
		    );
        }
		return self::$defaultMappingTable;
	}

	/**
	 * Replaces and normalizes a string.
	 * <p>This function is usually used before slugifying.
	 * @param string $s - the string to normalize
	 * @return string the normalized string.
	 */
	public static function normalize($s) {
	    // We might need this later
	    $originalString = $s;
	    
		// Normalizer-class missing!
		if (!class_exists("Normalizer", FALSE)) return $s;
	   	   
		// maps German (umlauts) and other European characters onto two characters before just removing diacritics
		$s = preg_replace('@\x{00c4}@u', "AE", $s);    // umlaut Ä => AE
		$s = preg_replace('@\x{00d6}@u', "OE", $s);    // umlaut Ö => OE
		$s = preg_replace('@\x{00dc}@u', "UE", $s);    // umlaut Ü => UE
		$s = preg_replace('@\x{00e4}@u', "ae", $s);    // umlaut ä => ae
		$s = preg_replace('@\x{00f6}@u', "oe", $s);    // umlaut ö => oe
		$s = preg_replace('@\x{00fc}@u', "ue", $s);    // umlaut ü => ue
		$s = preg_replace('@\x{00f1}@u', "ny", $s);    // ñ => ny
		$s = preg_replace('@\x{00ff}@u', "yu", $s);    // ÿ => yu
	   
		// maps special characters (characters with diacritics) on their base-character followed by the diacritical mark
		// exmaple:  Ú => U´,  á => a`
		$s = \Normalizer::normalize($s, \Normalizer::FORM_D);
	   
		$s = preg_replace('@\pM@u',      "",   $s);    // removes diacritics
		$s = preg_replace('@\x{00df}@u', "ss", $s);    // maps German ß onto ss
		$s = preg_replace('@\x{00c6}@u', "AE", $s);    // Æ => AE
		$s = preg_replace('@\x{00e6}@u', "ae", $s);    // æ => ae
		$s = preg_replace('@\x{0132}@u', "IJ", $s);    // ? => IJ
		$s = preg_replace('@\x{0133}@u', "ij", $s);    // ? => ij
		$s = preg_replace('@\x{0152}@u', "OE", $s);    // Œ => OE
		$s = preg_replace('@\x{0153}@u', "oe", $s);    // œ => oe
		$s = preg_replace('@\x{00d0}@u', "D",  $s);    // Ð => D
		$s = preg_replace('@\x{0110}@u', "D",  $s);    // Ð => D
		$s = preg_replace('@\x{00f0}@u', "d",  $s);    // ð => d
		$s = preg_replace('@\x{0111}@u', "d",  $s);    // d => d
		$s = preg_replace('@\x{0126}@u', "H",  $s);    // H => H
		$s = preg_replace('@\x{0127}@u', "h",  $s);    // h => h
		$s = preg_replace('@\x{0131}@u', "i",  $s);    // i => i
		$s = preg_replace('@\x{0138}@u', "k",  $s);    // ? => k
		$s = preg_replace('@\x{013f}@u', "L",  $s);    // ? => L
		$s = preg_replace('@\x{0141}@u', "L",  $s);    // L => L
		$s = preg_replace('@\x{0140}@u', "l",  $s);    // ? => l
		$s = preg_replace('@\x{0142}@u', "l",  $s);    // l => l
		$s = preg_replace('@\x{014a}@u', "N",  $s);    // ? => N
		$s = preg_replace('@\x{0149}@u', "n",  $s);    // ? => n
		$s = preg_replace('@\x{014b}@u', "n",  $s);    // ? => n
		$s = preg_replace('@\x{00d8}@u', "O",  $s);    // Ø => O
		$s = preg_replace('@\x{00f8}@u', "o",  $s);    // ø => o
		$s = preg_replace('@\x{017f}@u', "s",  $s);    // ? => s
		$s = preg_replace('@\x{00de}@u', "T",  $s);    // Þ => T
		$s = preg_replace('@\x{0166}@u', "T",  $s);    // T => T
		$s = preg_replace('@\x{00fe}@u', "t",  $s);    // þ => t
		$s = preg_replace('@\x{0167}@u', "t",  $s);    // t => t
	   
	    // remove all non-ASCii characters
		$s = preg_replace('@[^\0-\x80]@u', "", $s);
	   
	   
		// possible errors in UTF8-regular-expressions
		if (empty($s)) {
			return $originalString;
		}
		return $s;
	}
}

