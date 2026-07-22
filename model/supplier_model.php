<?php

include_once '../commons/db_connection.php';

$dbcon = new DbConnection();

class Supplier
{
    public function addSupplier($supplier_name, $supplier_contact_person, $supplier_contact_person_nic, $supplier_phone, $supplier_email, $supplier_address)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO supplier (supplier_name, supplier_contact_person, supplier_nic, supplier_phone, supplier_email, supplier_address) VALUES ('$supplier_name', '$supplier_contact_person', '$supplier_contact_person_nic', '$supplier_phone', '$supplier_email', '$supplier_address')";
        $con->query($sql) or die($con->error);
        $supplier_id = $con->insert_id;
        return $supplier_id;
    }

    public function getAllSuppliers()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM supplier WHERE supplier_status != -1";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function updateSupplier($supplier_id, $supplier_name, $supplier_contact_person, $supplier_contact_person_nic, $supplier_phone, $supplier_email, $supplier_address)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE supplier SET supplier_name = '$supplier_name', supplier_contact_person = '$supplier_contact_person', supplier_nic = '$supplier_contact_person_nic', supplier_phone = '$supplier_phone', supplier_email = '$supplier_email', supplier_address = '$supplier_address' WHERE supplier_id = '$supplier_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function deleteSupplier($supplier_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE supplier SET supplier_status = -1 WHERE supplier_id = '$supplier_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllSupplierCount()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT COUNT(supplier_id) as supplier_count FROM supplier";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getSupplier($supplier_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM supplier WHERE supplier_id = '$supplier_id'";
        $result = $con->query($sql) or die($con->error);
        return $result->fetch_assoc();
    }

    public function checkSupplierNIC($nic)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM supplier WHERE supplier_nic = '$nic'";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkSupplierEmail($email)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM supplier WHERE supplier_email = '$email'";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkSupplierNICUpdate($nic, $supplier_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM supplier 
            WHERE supplier_nic = '$nic' 
            AND supplier_id != '$supplier_id'";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function checkSupplierEmailUpdate($email, $supplier_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM supplier 
            WHERE supplier_email = '$email' 
            AND supplier_id != '$supplier_id'";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}
