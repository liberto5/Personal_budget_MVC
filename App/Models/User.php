<?php

namespace App\Models;

use PDO;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $error_name = [];
    public $error_email = [];
    public $error_password = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values
     *
     * @return void
     */
    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
	public function save()
	{
		$this->validate();
		
		if (empty($this->error_name) && empty($this->error_email) && empty($this->error_password)) 
		{
			$password_hash = password_hash($this->password1, PASSWORD_DEFAULT);

			$sql = 'INSERT INTO users (username, email, password) VALUES (:name, :email, :password_hash)';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
			$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
			$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

			return $stmt->execute();
		}
		
		return false;
		
	}

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
       // Name
	   	if ((strlen($this->name)<3) || (strlen($this->name)>20))
		{
			$this->error_name[] = "Name has to consist of 3 to 20 characters";
		}
		
		if (ctype_alnum($this->name) == false)
		{
			$this->error_name[] = "Name has to consist only of alphanumeric characters";
		}

       // email address
		$emailB = filter_var($this->email, FILTER_SANITIZE_EMAIL);
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) === false) || ($emailB != $this->email))
		{
			$this->error_email[] = "Enter the correct e-mail address";
		}
		
		if (static::emailExists($this->email)) 
		{
            $this->error_email[] = 'E-mail is already taken';
        }

       // Password		
		if ((strlen($this->password1) < 8) || (strlen($this->password1) > 20))
		{
			$this->error_password[] = "Password has to consist of 8 to 20 characters";
		}
		
		if ($this->password1 != $this->password2)
		{
			$this->error_password[] = "Passwords are not the same";
		}
	}
	
	/**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch() !== false;
    }
}
