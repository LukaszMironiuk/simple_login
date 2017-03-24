<?php

class User
{
	private $db;
	public $errors;

	public function __construct($db_con)
	{
		$this->db = $db_con;
	}

	public function redirect($url)
	{
		header("Location: $url");
	}

	public function register($name,$email,$pass)
	{
		//validacja if false return $ error arrays
		$name = trim($name);
		$email = trim($email);
		$pass = trim($pass);

		if (!$this->validation($name,$email,$pass))
		{
			return $this->errors;
		}

		$pass = password_hash($pass, PASSWORD_BCRYPT);
		$code = md5(uniqid(rand()));
		
		try {
			$stmt = $this->db->prepare("INSERT INTO users(name,email,password,tokenCode) VALUES(:name,:email,:pass,:code)");
			$stmt->execute(array(':name' => $name, ':email' => $email, ':pass' => $pass, ':code' => $code ));
			$this->send_verification_email($email);
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function validation($name,$email,$pass)
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$this->errors[] = "Wrong email address.";
		}
		if (strlen($pass) < 6)
		{
			$this->errors[] = "Password must contain at least 6 characters.";
		}
		if (count($this->errors) != 0)
		{
			return false;
		} else {
			try {
				$stmt = $this->db->prepare("SELECT name,email FROM users WHERE name=:name or email=:email");
				$stmt->execute(array(':name' => $name, ':email' => $email));
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0)
			{
				if ($row['name'] == $name)
				{
					$this->errors[] = "Name already taken.";
				}
				if ($row['email'] == $email)
				{
					$this->errors[] = "Email address already taken.";
				}
				return false;
			} else {
				return true;
			}
		}
	}

	public function activate_user ($id,$code)
	{
		try {
			$stmt = $this->db->prepare("UPDATE users SET active=true WHERE id=:id AND tokenCode=:code");
			$stmt->execute(array(':id' => $id, ':code' => $code));
			return $stmt;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function login($name,$password)
	{
		$pass = password_hash($password, PASSWORD_BCRYPT);
		try {
			$stmt = $this->db->prepare("SELECT id,name,password FROM users WHERE name=:name AND password=:password");
			$stmt->execute(array(':name' => $name, ':password' => $pass));
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount() != 1) {
			$this->errors[] = "Wrong login or password.";
			return false;
		}
		if ($row['name'] == $name && $row['password'] = $password)
		{
			$_SESSION['id'] = $row['id'];
			$_SESSION['name'] = $row['name'];
			$this->redirect("index.php");
		}
	}

	public function is_logged_in()
	{
		if (isset($_SESSION['id']) && isset($_SESSION['name']))
		{
			return true;
		} else {
			return false;
		}
	}

	public function logout()
	{
		unset($_SESSION['id']);
		unset($_SESSION['name']);
		session_destroy();
	}

	public function reset_pass()
	{

	}

	public function send_verification_email($email)
	{
		echo "ver mail now";
		try {
			$stmt = $this->db->prepare("SELECT id,tokenCode FROM users WHERE email=:email");
			$stmt->execute(array(':email' => $email));
		} catch (PDOException $e) {
			//echo $e->getMessage();
			$this->errors[] = $e->getMessage();
		}
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount() != 1)
		{
			print_r($row);
			return false;
		}
		$id = $row['id'];
		$code = $row['tokenCode'];
		$message = "To verify account click on link: " . $_SERVER['REQUEST_URI'] . "?id=" . $id . "&code=" . $code;
		$subject = "Activation link";
		$this->send_email($email,$message,$subject);
		echo "send";
	}

	public function resend_verification_email()
	{

	}

	public function send_email($email,$message,$subject)
	{
		require_once('mailer/class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->IsSMTP(); 
		$mail->SMTPDebug  = 0;                     
		$mail->SMTPAuth   = true;                  
		$mail->SMTPSecure = "ssl";                 
		$mail->Host       = "smtp.wp.pl";
		$mail->Port       = 465;
		$mail->AddAddress($email);
		$mail->Username="fakedev";  
		$mail->Password="Fakedevpass";            
		$mail->SetFrom('fakedev@wp.pl','test site');
		$mail->AddReplyTo("fakedev@wp.pl","test site");
		$mail->Subject    = $subject;
		$mail->MsgHTML($message);
		$mail->Send();
	}
}