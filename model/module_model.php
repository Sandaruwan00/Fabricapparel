<?php

include_once '../commons/db_connection.php';

$dbcon = new DbConnection();

class Module
{

    function getAllModules()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM module";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    function getAllModuleRoles($role_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM role_module r, module m WHERE r.module_id = m.module_id AND r.role_id='$role_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    function getRoles($role_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM role WHERE role_id = '$role_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    function getModules($module_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM module WHERE module_id = '$module_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getRoleModules($role_id)
    {
        $conn = $GLOBALS["con"];
        $sql = "SELECT * FROM role_module r,module m WHERE m.module_id=r.module_id AND r.role_id='$role_id'";
        $result = $conn->query($sql) or die($conn->error);
        return $result;
    }
}
