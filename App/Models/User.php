<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Models\FinancialOperation;

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
    public $error_category_remove = [];

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
     * @return boolean True if set successfully, false otherwise
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
     * Find a user by email address
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
	
	/**
     * Authenticate if login and password of user are correct
     *
     * @return mixed User object if found, false otherwise
     */
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
     * Find a user by ID
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
	
	/**
     * Change the name of logged in user
     *
     * @return boolean True if the name was changed successfully, false otherwise
     */
	public function changeUserName()
	{
		$this->validateName();
		
		if (empty($this->error_name)) 
		{
			$sql = 'UPDATE users SET username = :username WHERE id = :user_id';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':username', $this->changeNameInput, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);

			return $stmt->execute();
		}
		
		return false;
	}
	
	/**
     * Validate category's name, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validateName()
    {
	   	if ((strlen($this->changeNameInput)<3) || (strlen($this->changeNameInput)>20))
		{
			$this->error_name[] = "Name has to consist of 3 to 20 characters";
		}
		
		if (ctype_alnum($this->changeNameInput) == false)
		{
			$this->error_name[] = "Name has to consist only of alphanumeric characters";
		}
	}
	
	/**
     * Change the email of logged in user
     *
     * @return boolean True if the email was changed successfully, false otherwise
     */
	public function changeUserEmail()
	{
		$this->validateEmail();
		
		if (empty($this->error_email)) 
		{
			$sql = 'UPDATE users SET email = :email WHERE id = :user_id';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':email', $this->changeEmailInput, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);

			return $stmt->execute();
		}
		
		return false;
	}
	
	/**
     * Validate new user's email, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validateEmail()
    {
		$emailB = filter_var($this->changeEmailInput, FILTER_SANITIZE_EMAIL);
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) === false) || ($emailB != $this->changeEmailInput))
		{
			$this->error_email[] = "Enter the correct e-mail address";
		}
		
		if (static::emailExists($this->changeEmailInput)) 
		{
            $this->error_email[] = 'E-mail is already taken';
        }
	}
	
	/**
     * Check if the password is correct before changing it into new one
     *
     * @return boolean True if the email was changed successfully, false otherwise
     */
	public function checkPassword()
	{
		$currentPassword = $this->currentPasswordInput;
		
		$user = static::findByID($_SESSION['user_id']);
		
		if ($user) {
            if (password_verify($currentPassword, $user->password)) {
                return true;
            }
        }

        return false;
	}
	
	/**
     * Change the password of logged in user
     *
     * @return boolean True if the email was changed successfully, false otherwise
     */
	public function changeUserPassword()
	{
		$this->validatePassword();
		
		if (empty($this->error_password)) 
		{
			$password_hash = password_hash($this->newPasswordInput1, PASSWORD_DEFAULT);
			
			$sql = 'UPDATE users SET password = :password WHERE id = :user_id';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);

			return $stmt->execute();
		}
		
		return false;
	}
	
	/**
     * Validate new user's password, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validatePassword()
    {
		if ((strlen($this->newPasswordInput1) < 8) || (strlen($this->newPasswordInput1) > 20))
		{
			$this->error_password[] = "Password has to consist of 8 to 20 characters";
		}
		
		if ($this->newPasswordInput1 != $this->newPasswordInput2)
		{
			$this->error_password[] = "Passwords are not the same";
		}
	}
	
	/**
     * Edit the name of the category of incomes
     *
     * @return void
     */
    public function editIncomeCategoryName($user)
    {
		$this->validateName();
		
		if (empty($this->error_name)) 
		{
			$sql = 'UPDATE incomes_category_assigned_to_users SET name = :name WHERE user_id = :user_id AND name = :oldName';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':name', $this->changeNameInput, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->bindValue(':oldName', $_SESSION['category'], PDO::PARAM_STR);

			return $stmt->execute();
		}
		
		return false;
	}
	
	/**
     * Edit the name of the category of expenses
     *
     * @return void
     */
    public function editExpenseCategoryName()
    {
		$this->validateName();
		
		if (empty($this->error_name)) 
		{
			$sql = 'UPDATE expenses_category_assigned_to_users SET name = :name WHERE user_id = :user_id AND name = :oldName';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':name', $this->changeNameInput, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
			$stmt->bindValue(':oldName', $_SESSION['category'], PDO::PARAM_STR);

			return $stmt->execute();
		}
		
		return false;
	}
	
	/**
     * Remove existing category of incomes
     *
     * @return void
     */
    public function removeIncomeCategory()
    {
		$sql = 'DELETE FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
		$stmt->bindValue(':name', $_SESSION['category'], PDO::PARAM_STR);

		return $stmt->execute();
	}
	
	/**
     * Remove existing category of expenses
     *
     * @return void
     */
    public function removeExpenseCategory()
    {
		$sql = 'DELETE FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
		$stmt->bindValue(':name', $_SESSION['category'], PDO::PARAM_STR);

		return $stmt->execute();
	}
	
	/**
     * Add new name of the category of incomes
     *
     * @return void
     */
    public function addIncomeCategory()
    {
		$this->checkIfIncomeCategoryExistsAlready();
		
		$this->validateName();
		
		if (empty($this->error_name)) 
		{
			$sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
			$stmt->bindValue(':name', $this->changeNameInput, PDO::PARAM_STR);

			return $stmt->execute();
		}
		
		return false;
	}
	
	/**
     * Add new name of the category of expenses
     *
     * @return void
     */
    public function addExpenseCategory()
    {
		$this->checkIfExpenseCategoryExistsAlready();
		
		$this->validateName();
		
		if (empty($this->error_name)) 
		{
			$sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
			$stmt->bindValue(':name', $this->changeNameInput, PDO::PARAM_STR);

			return $stmt->execute();
		}
		
		return false;
	}
	
	/**
     * Check, if category name, that user is trying to add, doesn't exist yet
     *
     * @return void
     */
    public function checkIfIncomeCategoryExistsAlready()
    {
	   	$name = $this->changeNameInput;
		
		$namesFromDatabase = static::getIncomesCategoriesToCheckName();
		
		foreach($namesFromDatabase as $result) 
		{
		   if (strcasecmp($name, $result['name']) == 0) 
			{
				$this->error_name[] = "Category name already exists. Choose another one";
				break;
			};
		}
	}
	
	/**
     * Check, if category name, that user is trying to add, doesn't exist yet
     *
     * @return void
     */
    public function checkIfExpenseCategoryExistsAlready()
    {
	   	$name = $this->changeNameInput;
		
		$namesFromDatabase = static::getExpensesCategoriesToCheckName();
		
		foreach($namesFromDatabase as $result) 
		{
		   if (strcasecmp($name, $result['name']) == 0) 
			{
				$this->error_name[] = "Category name already exists. Choose another one";
				break;
			};
		}
	}
	
	/**
     * Get categories of incomes from database and send them as an array
     *
     * @return array with categories
     */	
	public static function getIncomesCategoriesToCheckName()
	{
		if (isset($_SESSION['user_id']))
		{
			$user_id = $_SESSION['user_id'];
		
			$sql = "SELECT name FROM incomes_category_assigned_to_users WHERE user_id = :user_id";

			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

			$stmt->execute();
			
			return $stmt->fetchAll();
		}
	}
	
	/**
     * Get categories of expenses from database and send them as an array
     *
     * @return array with categories
     */	
	public static function getExpensesCategoriesToCheckName()
	{
		if (isset($_SESSION['user_id']))
		{
			$user_id = $_SESSION['user_id'];
		
			$sql = "SELECT name FROM expenses_category_assigned_to_users WHERE user_id = :user_id";

			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

			$stmt->execute();
			
			return $stmt->fetchAll();
		}
	}
	
	/**
     * Check if any income is already assigned to removing category
     *
     * @return mixed User object if found, false otherwise
     */
    public function isIncomeCategoryEmpty()
    {
        $user_id = $_SESSION['user_id'];
		$category_id = $this->getIncomeId();
		
		$sql = 'SELECT * FROM incomes WHERE user_id = :user_id AND income_category_assigned_to_user_id = :category_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        if ($stmt->fetch())
		{
			$this->error_category_remove[] = "To this category some financial operations have been assigned. Do you really want to remove it and transfer all operations from this category to \"Another\"?";
			
			return true;
		}
		else
		{
			return false;
		}
    }
	
	/**
     * Check if any expense is already assigned to removing category
     *
     * @return mixed User object if found, false otherwise
     */
    public function isExpenseCategoryEmpty()
    {
        $user_id = $_SESSION['user_id'];
		$category_id = $this->getExpenseId();
		
		$sql = 'SELECT * FROM expenses WHERE user_id = :user_id AND expense_category_assigned_to_user_id = :category_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        if ($stmt->fetch())
		{
			$this->error_category_remove[] = "To this category some financial operations have been assigned. Do you really want to remove it and transfer all operations from this category to \"Another\"?";
			
			return true;
		}
		else
		{
			return false;
		}
    }
	
	/**
     * Get the income category id from database
     *
     * @return integer with id of income category
     */
	public function getIncomeId()
	{
		$user_id = $_SESSION['user_id'];
		$income_category = $_SESSION['category'];
		
		$sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $income_category, PDO::PARAM_STR);
		
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
		
		$row = $stmt->fetch();
		
		return $row->id;
	}
	
	/**
     * Get the expense category id from database
     *
     * @return integer with id of expense category
     */
	public function getExpenseId()
	{
		$user_id = $_SESSION['user_id'];
		$income_category = $_SESSION['category'];
		
		$sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE user_id = :id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $income_category, PDO::PARAM_STR);
		
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
		
		$row = $stmt->fetch();
		
		return $row->id;
	}
	
	/**
     * Check if Another category of incomes exists, 
	 * if so, transfer incomes, if not, create it and then transfer incomes
     *
     * @return void
     */
	public function prepareToTransferIncomesToAnotherCategory()
	{
		if($this->checkIfAnotherIncomesCategoryExists())
		{
			$this->transferIncomesToAnotherCategory();
			
			return true;
		}
		else
		{
			$this->createAnotherIncomesCategory();
			
			$this->transferIncomesToAnotherCategory();
			
			return true;
		}
		
		return false;
	}
	
	/**
     * Check if Another category of expenses exists, 
	 * if so, transfer expenses, if not, create it and then transfer expenses
     *
     * @return void
     */
	public function prepareToTransferExpensesToAnotherCategory()
	{
		if($this->checkIfAnotherExpensesCategoryExists())
		{
			$this->transferExpensesToAnotherCategory();
			
			return true;
		}
		else
		{
			$this->createAnotherExpensesCategory();
			
			$this->transferExpensesToAnotherCategory();
			
			return true;
		}
		
		return false;
	}
	
	/**
     * Check if "Another" category exists to transfer incomes there
     *
     * @return void
     */
	public function checkIfAnotherIncomesCategoryExists()
	{
        $user_id = $_SESSION['user_id'];
		$income_category = "Another";
		
		$sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $income_category, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
	}
	
	/**
     * Check if "Another" category exists to transfer expenses there
     *
     * @return void
     */
	public function checkIfAnotherExpensesCategoryExists()
	{
        $user_id = $_SESSION['user_id'];
		$income_category = "Another";
		
		$sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $income_category, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
	}
	
	/**
     * Create "Another" income category
     *
     * @return void
     */
	public function createAnotherIncomesCategory()
	{
        $user_id = $_SESSION['user_id'];
		$income_category = "Another";
		
		$sql = "INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $income_category, PDO::PARAM_STR);

        return $stmt->execute();
	}
	
	/**
     * Create "Another" expense category
     *
     * @return void
     */
	public function createAnotherExpensesCategory()
	{
        $user_id = $_SESSION['user_id'];
		$expense_category = "Another";
		
		$sql = "INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $expense_category, PDO::PARAM_STR);

        return $stmt->execute();
	}
	
	/**
     * Transfer incomes from removed category to "Another"
     *
     * @return void
     */
	public function transferIncomesToAnotherCategory()
	{
		$user_id = $_SESSION['user_id'];
		$oldIncomeCategory = $this->getIncomeId();
		$AnotherCategoryId = $this->getAnotherIncomeCategoryId();
		
		$sql = 'UPDATE incomes SET income_category_assigned_to_user_id = :income_category_assigned_to_user_id WHERE user_id = :user_id AND income_category_assigned_to_user_id = :old_income_category_assigned_to_user_id';

		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':income_category_assigned_to_user_id', $AnotherCategoryId, PDO::PARAM_INT);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindValue(':old_income_category_assigned_to_user_id', $oldIncomeCategory, PDO::PARAM_INT);

		return $stmt->execute();
		
	}
	
	/**
     * Transfer expenses from removed category to "Another"
     *
     * @return void
     */
	public function transferExpensesToAnotherCategory()
	{
		$user_id = $_SESSION['user_id'];
		$oldExpenseCategory = $this->getExpenseId();
		$AnotherCategoryId = $this->getAnotherExpenseCategoryId();
		
		$sql = 'UPDATE expenses SET expense_category_assigned_to_user_id = :expense_category_assigned_to_user_id WHERE user_id = :user_id AND expense_category_assigned_to_user_id = :old_expense_category_assigned_to_user_id';

		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':expense_category_assigned_to_user_id', $AnotherCategoryId, PDO::PARAM_INT);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindValue(':old_expense_category_assigned_to_user_id', $oldExpenseCategory, PDO::PARAM_INT);

		return $stmt->execute();
		
	}
	
	/**
     * Get "Another" category id
     *
     * @return integer with income id
     */
	public function getAnotherIncomeCategoryId()
	{
        $user_id = $_SESSION['user_id'];
		$income_category = "Another";
		
		$sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $income_category, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

		$row = $stmt->fetch();
		
		return $row->id;
	}
	
	/**
     * Get "Another" category id
     *
     * @return integer with expense id
     */
	public function getAnotherExpenseCategoryId()
	{
        $user_id = $_SESSION['user_id'];
		$income_category = "Another";
		
		$sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $income_category, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

		$row = $stmt->fetch();
		
		return $row->id;
	}
	
}
