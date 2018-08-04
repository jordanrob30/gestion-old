<?php
/**
 * User Class
 * User: Jordan Robinson
 * Date: 04/08/2018
 * Time: 16:23
 *
 * Customised from Original Framework Developer By Tim Oliver
 * https://github.com/TimOliver/PHP-Framework-Classes/blob/master/user.class.php
 */

define("COOKIE_TIMEOUT", 604800); //1 week

//Regex pattern used to validate login name
define( 'USERNAME_EXP',	'%[^a-z0-9\-\[\]\.\_=!\$\%\^&*(){}?@#$+\'"\/]+%is' );
define( 'EMAIL_EXP', '%[a-z0-9._-]+@[a-z0-9_-]+\.[a-z.]+%i' );

if( !defined( 'TIME_NOW' ) )
    define( 'TIME_NOW', time() );

class User
{
    //the connection
    private $db;

    //the users information array
    public $userinfo;

    //users logged in status
    private $logged_in;

    //user privelige level
    private $user_level = 0;

    private $logout_hash;

    private $errors = null;

    function __construct()
    {
        global $db;
        //setting the db connection within the class
        $this->db = $db;

        //initialize the errors arrau
        $this->errors = array();

        //ets set up the session for use
        session_name("Login");
        session_start();

        $session_id = session_id();

        //check if a session exists
        if(isset($_SESSION["userid"]))
        {
            //lets get the user information if the session already exists
            $user_q = "SELECT * FROM users WHERE userid='{$_SESSION["userid"]}' LIMIT 1";
            $user_r = $this->db->query($user_q);

            $this->userinfo = $this->db->fetch_assoc($user_r);

            if($this->userinfo != null)
            {
                $this->logged_in = true;
            }
        }

        //session failed lets try the cookies
        if($this->logged_in == false && isset($_COOKIE['ID']))
        {
            $id = intval($_COOKIE["ID"]);
            $username = strval($_COOKIE["username"]);
            $password = strval($_COOKIE["password"]);

            if($id && $username && $password)
            {
                $this->login($username, $password, true);
            }

        }

    }

    //Login Function
    function login($username='', $password='', $remember_me=false)
    {
        if(!strlen($username) || !strlen($username))
        {
            $this->errors[] = "No Username Or Password Entered";
            return false;
        }

        $username = addslashes($username);
        $password = addslashes($password);

        $user_q = "SELECT * FROM users WHERE username='{$username}' LIMIT 1";
        $user_r = $this->db->query($user_q);

        $userinfo = $this->db->fetch_assoc($user_r);

        if($userinfo == NULL)
        {
            $this->errors[] = "No Username Found";
            return false;
        }

        //check the password is the on we want
        $pass_check = password_verify($password, $userinfo["password"]);

        if(!$pass_check)
        {
            $this->errors[] = "Incorrect Password";
            return false;
        }

        //at this point the user has been validated
        $this->logged_in = true;

        //if remember me was set and no cookies exist create the cookies
        if($remember_me && !isset($_COOKIEE["ID"]))
        {
            setcookie("ID", $userinfo->userid, TIME_NOW + COOKIE_TIMEOUT, '');
            setcookie("username", $userinfo->username, TIME_NOW + COOKIE_TIMEOUT, '');
            setcookie("password", $userinfo->password, TIME_NOW + COOKIE_TIMEOUT, '');
        }

        //finally set up the session
        $this->userinfo = $userinfo;
        $_SESSION["userid"] = $userinfo["userid"];
        $this->logout_hash = md5($userinfo["userid"] . $userinfo["username"]);

        return true;
    }

    //logout function
    function logout($hash = '')
    {
        //check hash
        if(strcmp( $hash, $this->logout_hash ) != 0)
        {
            return false;
        }

        //valid hash lets logout, first lets destroy the cookies
        if(isset($_COOKIE["ID"]))
        {
            //lets just let the browser purge the cookies

            setcookie('ID', '', TIME_NOW - 3600 );
            setcookie('username', '', TIME_NOW - 3600 );
            setcookie('password', '', TIME_NOW - 3600 );
        }

        //destroy the session
        unset($_SESSION["userid"]);
        session_destroy();

        //tell the class the user is no longer logged in
        $this->logged_in = false;

        return true;
    }

    function addUser($username, $password, $email, $firstname, $lastname, $role)
    {
        //check we have everything
        if(!$username || !$password || !$email || !$firstname || !$lastname || !$role)
        {
            return false;
        }

        if(preg_match(USERNAME_EXP, $username))
        {
            $this->errors[] = "Username contained invalid characters";
            return false;
        }

        if( !preg_match( EMAIL_EXP, $email ) )
        {
            $this->errors[] = 'Email address wasn\'t valid.';
            return FALSE;
        }

        //check that a user with that email and/or username doesn't already exist
        if( $this->username_exists( $username ) )
        {
            $this->errors[] = 'An account with that username already exists.';
            return false;
        }

        //here we've confirmed valid credentials
        $username = addslashes($username);
        $password = addslashes($password);
        $email = addslashes($email);
        $firstname = addslashes($firstname);
        $lastname = addslashes($lastname);
        $role = addslashes($role);

        $password = password_hash($password, PASSWORD_BCRYPT);

        $insert_q = "INSERT INTO users (firstname,lastname,email,username,password,role) VALUES('$firstname', '$lastname', '$email', '$username', '$password','$role')";
echo $insert_q;
        $this->db->query($insert_q);

        //return the id for use of the module code.
        return $this->db->get_last_insert_id();
    }

    function username_exists($username)
    {
        if(!$username)
        {
            return false;
        }

        $user_q = "SELECT * FROM users WHERE username='{$username}' LIMIT 1";
        $user_r = $this->db->query($user_q);

        $user_rows = $this->db->num_rows($user_r);

        if($user_rows > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function get_last_error()
    {
        if(count($this->errors) > 0)
        {
            return $this->errors[count($this->errors) - 1];
        }
    }

    function get_user_info()
    {
        if(isset($this->userinfo))
        {
            return $this->userinfo;
        }
    }
}