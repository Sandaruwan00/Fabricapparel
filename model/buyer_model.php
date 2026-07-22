<?php

include_once '../commons/db_connection.php';

$dbcon = new DbConnection();

class Buyer
{
    public function addBuyerCompany($company_name, $company_registration, $business_type, $website, $company_address_line_1, $company_address_line_2, $company_city, $company_postal_code, $company_country)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO buyer_company (company_name, company_registration, business_type_id, website, company_address_line_1, company_address_line_2, company_city, company_postal_code, company_country) VALUES ('$company_name', '$company_registration', '$business_type', '$website', '$company_address_line_1', '$company_address_line_2', '$company_city', '$company_postal_code', '$company_country')";
        $con->query($sql) or die($con->error);
        $company_id = $con->insert_id;
        return $company_id;
    }


    public function addBuyerCompanyPerson($company_id, $contact_name, $job_title, $contact_email, $contact_nic, $contact_phone, $office_address_line_1, $office_address_line_2, $office_address_line_3)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO buyer_contact_person (company_id, contact_name, job_title, contact_email, contact_nic, contact_phone, office_address_line_1, office_address_line_2, office_address_line_3) VALUES ('$company_id', '$contact_name', '$job_title', '$contact_email', '$contact_nic', '$contact_phone', '$office_address_line_1', '$office_address_line_2', '$office_address_line_3')";
        $result = $con->query($sql) or die($con->error);
    }

    public function getAllBuyers()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM buyer_company bc , buyer_contact_person bcp WHERE bc.company_id = bcp.company_id AND buyer_company_status != -1 AND contact_person_status != -1";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllBuyersForCount()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM buyer_company";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function deleteBuyerCompany($company_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE buyer_company SET buyer_company_status='-1' WHERE company_id='$company_id';";
        $result = $con->query($sql) or die($con->error);
    }

    public function deleteBuyerCompanyPerson($company_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE buyer_contact_person SET contact_person_status='-1' WHERE company_id='$company_id';";
        $result = $con->query($sql) or die($con->error);
    }

    public function getBuyerCompany($company_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM buyer_company bc,buyer_contact_person bcp WHERE bc.company_id='$company_id' AND bcp.company_id='$company_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getBusinessType()
    {

        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM business_type";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function viewBusinessType($company_id)
    {

        $con = $GLOBALS["con"];
        $sql = "SELECT bc.company_id, bc.company_name, bt.business_type_name
            FROM buyer_company bc
            INNER JOIN business_type bt 
            ON bc.business_type_id = bt.business_type_id
            WHERE bc.company_id = '$company_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function updateBuyerCompany($company_name, $company_registration, $business_type, $website, $company_address_line_1, $company_address_line_2, $company_city, $company_postal_code, $company_country, $company_id)
    {

        $con = $GLOBALS["con"];
        $sql = "UPDATE buyer_company SET company_name='$company_name',company_registration='$company_registration',business_type_id='$business_type',website='$website',company_address_line_1='$company_address_line_1',company_address_line_2='$company_address_line_2',company_city='$company_city',company_postal_code='$company_postal_code',company_country='$company_country' WHERE company_id='$company_id';";
        $con->query($sql) or die($con->error);
    }

    public function updateBuyerCompanyPerson($company_id, $contact_name, $job_title, $contact_email, $contact_nic, $contact_phone, $office_address_line_1, $office_address_line_2, $office_address_line_3)
    {

        $con = $GLOBALS["con"];
        $sql = "UPDATE buyer_contact_person SET contact_name='$contact_name',job_title='$job_title',contact_email='$contact_email',contact_nic='$contact_nic',contact_phone='$contact_phone',office_address_line_1='$office_address_line_1',office_address_line_2='$office_address_line_2',office_address_line_3='$office_address_line_3' WHERE company_id='$company_id';";
        $con->query($sql) or die($con->error);
    }

    public function getOrderHistory($company_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM orders o, order_status os WHERE o.company_id='$company_id' AND o.status_id = os.status_id";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getTopFiveBuyers()
    {
        $con = $GLOBALS["con"];

        $sql = "SELECT
                b.company_name,
                SUM(o.total_amount + o.delivery_charge) AS total_sales
            FROM buyer_company b
            INNER JOIN orders o
                ON b.company_id = o.company_id
            GROUP BY b.company_id
            ORDER BY total_sales DESC
            LIMIT 5";

        return $con->query($sql);
    }

    public function checkBuyerEmailExists($contact_email){
        
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM buyer_contact_person WHERE contact_email='$contact_email'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function checkBuyerEmailExistsForUpdate($contact_email, $company_id){
        
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM buyer_contact_person WHERE contact_email='$contact_email' AND company_id != '$company_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function checkBuyerNICExists($contact_nic){
        
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM buyer_contact_person WHERE contact_nic='$contact_nic'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function checkBuyerNICExistsForUpdate($contact_nic, $company_id){
        
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM buyer_contact_person WHERE contact_nic='$contact_nic' AND company_id != '$company_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }
}
