<?php /** @noinspection ALL */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

require_once $_SERVER["DOCUMENT_ROOT"] . "/config/config.php";

class OTP {
    private $conn;
    private $table = 'otp';
    private $validator;
    public $validator_errs;


    public $error;
    public $pass_len = OTP_LEN;
    public $max_tries = OTP_TRIES;
    public $valid = OTP_VALID;
    public $email;
    private $password;
    private $tries;

    public function __construct($db, $user_email) {
        $this->conn = $db;
        $this->email = $user_email;
        $this->validator = v::attribute('email', v::email());
    }

    public function validate() {
        try {
            $this->validator->assert($this);
        } catch(NestedValidationException $exception) {
            $this->validator_errs = $exception->getMessages();
            return false;
        }
        return true;
    }

    public function  generate()
    {
        // Check if exists
        if($this->read_valid()) {
            $this->error = 'Valid OTP already exists';
            return false;
        }
        $this->conn->beginTransaction();
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET user_email = :user_email, password = :password';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind data
        $this->password = getRandomString($this->pass_len);
        $stmt->bindParam(':user_email', $this->email);
        $stmt->bindParam(':password', $this->password);

        // Execute query
        if (!$stmt->execute()) {
            throw new ErrorException($stmt->error);
        }

        // Send pass
        $this->send_pass();

        $this->conn->commit();
        return true;
    }

    public function read_valid() {
        // Create query
        $query = 'SELECT * FROM ' . $this->table . ' 
                    WHERE 
                        user_email=:user_email AND
                        created_at >= CURRENT_TIMESTAMP - INTERVAL :valid MINUTE AND
                        tries < :tries';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind params
        $stmt->bindParam(':user_email', $this->email);
        $stmt->bindParam(':valid', $this->valid, PDO::PARAM_INT);
        $stmt->bindParam(':tries', $this->max_tries, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();


        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->password = $row['password'];
            $this->tries = $row['tries'];
            return true;
        }
        return false;
    }

    private function send_pass() {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = MAIL_AUTH;
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        //Recipients
        $mail->setFrom(MAIL_USER);
        $mail->addAddress($this->email);

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Here is the subject';
        $mail->Body = 'This is your OTP: <b>' . $this->password . '</b>';
        $mail->AltBody = 'This is your OTP: ' . $this->password;

        $mail->send();
    }



    public function challenge($pass) {
        if(!$this->read_valid()) {
            $this->error = "Valid OTP for this email don't exist";
            return false;
        }

        // Challenge OTP
        if($this->password == $pass) {
            $this->delete();
            return true;
        }

        // Increment tries
        $this->tries++;
        $this->error = 'Incorrect OTP '.$this->max_tries-$this->tries.' tries ramaining';
        $this->update();
        return false;

    }

    public function update() {
        // Create query
        $query = 'UPDATE ' . $this->table . ' 
                    SET tries = :tries';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':tries', $this->tries);

        // Execute query
        if(!$stmt->execute()) {
            throw new ErrorException($stmt->error);
        }
        return true;
    }

    // Delete User
    public function delete() {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE user_email = :user_email';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind data
        $stmt->bindParam(':user_email', $this->email);

        // Execute query
        if(!$stmt->execute()) {
            throw new ErrorException($stmt->error);
        }
        return true;
    }
}

// Generate random string
function getRandomString($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}