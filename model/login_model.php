<?php

include_once  '../commons/db_connection.php';

$dbcon = new DbConnection();

class Login{

public function validateUser($login_username,$login_password){

    $con=$GLOBALS["con"];
    $login_password=sha1($login_password);
    $sql = "SELECT u.user_id, u.user_role, u.user_status, r.role_name, u.user_fname, u.user_lname FROM user u, login l, role r 
            WHERE u.user_id=l.user_id AND  r.role_id =u.user_role 
            AND l.login_username='$login_username' AND l.login_password='$login_password'";

    $result = $con->query($sql) or die($con->error);
    return $result;
}

public function addUserLogin($user_id, $user_email,$nic) {
    $con=$GLOBALS["con"];
    $pw = sha1($nic);
    $sql="INSERT INTO login(login_username,login_password,user_id)"
            . "VALUES('$user_email','$pw','$user_id')";
     $result = $con->query($sql) or die($con->error);
}

public function updateLoginUser($user_id,$user_email,$nic){
       
        $con=$GLOBALS["con"];
        $pw = sha1($nic);
        $sql = "UPDATE login SET login_username='$user_email',login_password='$pw' WHERE user_id='$user_id'";
        $con->query($sql) or die($con->error);
    }






}