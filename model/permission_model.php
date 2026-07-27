<?php

include_once '../commons/db_connection.php';

$dbcon = new DbConnection();

class Permission
{

    public function hasPermission($user_id, $fun_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT 1 FROM function_user WHERE user_id = '$user_id' AND fun_id = '$fun_id'";
        $result = $con->query($sql);
        return ($result->num_rows > 0);
    }
}
