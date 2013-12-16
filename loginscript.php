<?php
//test
/* connect to the database connections details bellow! */
/* ERRROR CODES login!!!!!!!
 * 1 succes
 * 2 username not exitst
 * 3 nopassword match 
 *  */
/* ERRROR CODES register!!!!!!!
 * 0 everything was good.
 * 1 username already exits
 * 2 password don't match
 * 3 email is not vaild
 *  */
/* ERRROR CODES change password!!!!!!!
 * 0 everything was good.
 * 1 username does not exits
 * 2 old password don't match
 * 3 email is not vaild
 *  */

class login {

    var $db_host = 'localhost';
    var $db_username = 'mijnrovox';
    var $db_password = 'dbpass';
    var $db_name = 'zadmin_mijnrovox';
    var $db_tabel = 'df';

    public function __construct() {
        //echo 'contruct<br>';
    }

    /* the connect funtion */

    public function connect() {
        mysql_connect($this->db_host, $this->db_username, $this->db_password) or die(mysql_error());
        mysql_select_db($this->db_name) or die(mysql_error());
        //echo 'connect<br>';
    }

    /* the login funtion */

    public function login($username, $password) {
        $username = $this->cleartext($username);
        $password = $this->cleartext($password);
        $this->connect();
        $result = mysql_query("SELECT * FROM users WHERE username = '$username'");
        while ($row = mysql_fetch_array($result)) {
            $usernamedb = $row['username'];
            $passworddb = $row['password'];
        }
        if (empty($usernamedb)) {
            return 2;
        }
        if ($passworddb == $this->hashpassword($this->getsalt($username), $password)) {
            return 0;
        } else {
            return 3;
        }
    }

    /* the register funtion */

    public function register($username, $password, $repassword, $email) {
        $username = $this->cleartext($username);
        $password = $this->cleartext($password);
        $repassword = $this->cleartext($repassword);
        $this->connect();
        if ($this->checkusername($username) == 0) {
            if ($password == $repassword) {
                if ($this->checkemail($email) == 1) {
                    $salt = $this->createsalt();
                    $password = $this->hashpassword($salt, $password);
                    mysql_query("INSERT INTO users (username, password, salt, email) VALUES('$username', '$password', '$salt', '$email') ") or die(mysql_error());
                    return 0;
                } else {
                    return 3;
                }
            } else {
                return 2;
            }
        } else {
            return 1;
        }
    }

    /* the checksalt funtion */

    public function getsalt($username) {
        $this->connect();
        $result = mysql_query("SELECT * FROM users WHERE username = '$username'");
        if (!$result) {
            die('Invalid query: ' . mysql_error());
        }
        //echo 'runn mysql<br>';
        while ($row = mysql_fetch_array($result)) {
            return $row['salt'];
        }
    }

    /* the editaccout funtion */

    public function changepassword($username, $oldpassword, $newpassword) {
        $username = $this->cleartext($username);
        $oldpassword = $this->cleartext($oldpassword);
        $newpassword = $this->cleartext($newpassword);
        $result = mysql_query("SELECT * FROM users WHERE username = '$username'");
        $salt = $this->getsalt($username);
        while ($row = mysql_fetch_array($result)) {
            $passworddb = $row['password'];
        }
        if ($this->checkusername($username) == 1) {
            if ($this->hashpassword($salt, $oldpassword) == $passworddb) {
                $newsalt = $this->createsalt();
                $newpassword = $this->hashpassword($newsalt, $newpassword);
                mysql_query("UPDATE users SET password='$newpassword', salt='$newsalt' WHERE username='$username'") or die(mysql_error());
                return 0;
            } else {
                return 2;
            }
        } else {
            return 1;
        }
    }

    /* the hashpassword funtion */

    public function hashpassword($salt, $password) {
        $hash = hash('sha512', "$salt" . "$password");
        return $hash;
    }

    /* the cleartext funtion */

    public function cleartext($text) {
        $this->connect();
        $clear = mysql_real_escape_string($text);
        return $clear;
    }

    /* the check username function */

    public function checkusername($username) {
        $this->connect();
        $result = mysql_query("SELECT * FROM users WHERE username = '$username'");
        if (mysql_num_rows($result) >= 1) {
            return 1;
        } else {
            return 0;
        }
    }

    /* the check username function */

    public function checkemail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /* the create salt function */

    public function createsalt() {
        return mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
    }

}
