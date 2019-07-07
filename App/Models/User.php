<?php

namespace App\Models;

use PDO;
use \App\Token;

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
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) 
		{
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
     * Set default values of categories of payment methods to new user
     *
     * @return boolean  True if set successfully, false otherwise
     */
	public function setDefaultPaymentMethodsToUser()
	{
		$user = static::findByEmail($this->email);
		$user_id = $user->id;
		
		$adding_standard_payment_methods = "INSERT INTO payment_methods_assigned_to_users (name) SELECT name FROM payment_methods_default";
		$adding_user_id_to_payment_methods = "UPDATE payment_methods_assigned_to_users SET user_id = '$user_id' WHERE user_id = 0";

        $db = static::getDB();
        $stmt1 = $db->prepare($adding_standard_payment_methods);
        $stmt2 = $db->prepare($adding_user_id_to_payment_methods);
        $stmt2->bindValue(':user_id', $user_id, PDO::PARAM_STR);

        if ($stmt1->execute() && $stmt2->execute())
		{
			return true;
		}
		
		else
		{
			return false;
		}
	}
	
    /**
     * Set default values of categories of incomes to new user
     *
     * @return boolean  True if set successfully, false otherwise
     */
	public function setDefaultIncomesCategoriesToUser()
	{
		$user = static::findByEmail($this->email);
		$user_id = $user->id;
		
		$adding_standard_incomes_categories = "INSERT INTO incomes_category_assigned_to_users (name) SELECT name FROM incomes_category_default";
		$adding_user_id_to_standard_incomes_categories = "UPDATE incomes_category_assigned_to_users SET user_id = '$user_id' WHERE user_id = 0";

        $db = static::getDB();
        $stmt1 = $db->prepare($adding_standard_incomes_categories);
        $stmt2 = $db->prepare($adding_user_id_to_standard_incomes_categories);
        $stmt2->bindValue(':user_id', $user_id, PDO::PARAM_STR);

        if ($stmt1->execute() && $stmt2->execute())
		{
			return true;
		}
		
		else
		{
			return false;
		}
	}

		
    /**
     * Set default values of categories of expenses to new user
     *
     * @return boolean  True if set successfully, false otherwise
     */
	public function setDefaultExpensesCategoriesToUser()
	{
		$user = static::findByEmail($this->email);
		$user_id = $user->id;
		
		$adding_standard_expenses_categories = "INSERT INTO expenses_category_assigned_to_users (name) SELECT name FROM expenses_category_default";
		$adding_user_id_to_standard_expenses_categories = "UPDATE expenses_category_assigned_to_users SET user_id = '$user_id' WHERE user_id = 0";

        $db = static::getDB();
        $stmt1 = $db->prepare($adding_standard_expenses_categories);
        $stmt2 = $db->prepare($adding_user_id_to_standard_expenses_categories);
        $stmt2->bindValue(':user_id', $user_id, PDO::PARAM_STR);

        if ($stmt1->execute() && $stmt2->execute())
		{
			return true;
		}
		
		else
		{
			return false;
		}
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
        return static::findByEmail($email) !== false;
    }
	
	/**
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
	
	public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }
	
	/**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
	
    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }
}
