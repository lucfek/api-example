<?php /** @noinspection ALL */
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

require_once $_SERVER["DOCUMENT_ROOT"] . '/vendor/autoload.php';

class User {
    private $conn;
    private $table = 'users';
    private $validator;
    public $validator_errs;

    // Post Properties
    public $uuid;
    public $firstname;
    public $lastname;
    public $email;
    public $phone;
    public $password;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
        $this->validator = v::attribute('firstname', v::optional(v::stringType()->length(1, 32)))
            ->attribute('lastname', v::optional(v::stringType()->length(1, 32)))
            ->attribute('email', v::optional(v::email()))
            ->attribute('phone', v::optional(v::phone()))
            ->attribute('password', v::optional(v::stringType()->length(8, 32)));
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

    // Get Single User
    public function read_single_by_email() {
        // Create query
        $query = 'SELECT *
                        FROM ' . $this->table . '
                        WHERE
                            email = ?
                        LIMIT 0,1';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind ID
        $stmt->bindParam(1, $this->email);

        // Execute query
        $stmt->execute();

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // Set properties
            $this->uuid = $row['uuid'];
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->password = $row['password'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Create User
    public function create() {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET firstname = :firstname, lastname = :lastname, email = :email, phone = :phone, password = :password';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->password =  password_hash($this->password, PASSWORD_DEFAULT);

        // Bind data
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':password', $this->password);


        // Execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    // Update User
    public function update() {
        // Create query
        $password_set=!empty($this->password) ? ", password = :password" : "";
        $query = 'UPDATE ' . $this->table . "
                    SET firstname = :firstname, lastname = :lastname, phone = :phone {$password_set}
                    WHERE uuid = :uuid";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->uuid = htmlspecialchars(strip_tags($this->uuid));
        if(!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }


        // Bind data
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':uuid', $this->uuid);


        // Execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

}