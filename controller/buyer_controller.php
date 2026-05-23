<?php
include '../commons/session.php';

if (!isset($_GET["status"])) {
?>
    <script>
        window.location = "../view/login.php";
    </script>
    <?php
}

$status = $_GET["status"];

include '../model/buyer_model.php';

$buyerObj = new Buyer();

switch ($status) {
    case "add_buyer":

        $company_name = $_POST["company_name"];
        $company_registration = $_POST["company_registration"];
        $business_type = $_POST["business_type"];
        $website = $_POST["website"];
        $company_address_line_1 = $_POST["company_address_line_1"];
        $company_address_line_2 = $_POST["company_address_line_2"];
        $company_city = $_POST["company_city"];
        $company_postal_code = $_POST["company_postal_code"];
        $company_country = $_POST["company_country"];


        $contact_name = $_POST["contact_name"];
        $job_title = $_POST["job_title"];
        $contact_email = $_POST["contact_email"];
        $contact_phone = $_POST["contact_phone"];
        $office_address_line_1 = $_POST["office_address_line_1"];
        $office_address_line_2 = $_POST["office_address_line_2"];
        $office_address_line_3 = $_POST["office_address_line_3"];

        try {

            $company_id = $buyerObj->addBuyerCompany($company_name, $company_registration, $business_type, $website, $company_address_line_1, $company_address_line_2, $company_city, $company_postal_code, $company_country);

            if ($company_id > 0) {
                $buyerObj->addBuyerCompanyPerson($company_id, $contact_name, $job_title, $contact_email, $contact_phone, $office_address_line_1, $office_address_line_2, $office_address_line_3);

                $msg = "$company_name Successfully Added";
                $msg = base64_encode($msg);
                $company_id = base64_encode($company_id);

    ?>

                <script>
                    window.location = "../view/view-buyer.php?company_id=<?php echo $company_id; ?>&msg=<?php echo $msg; ?>";
                </script>


            <?php
            }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
            ?>
            <script>
                window.location = "../view/add-buyer.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }




        break;

    case "delete":
        $company_id = $_GET["company_id"];
        $company_id = base64_decode($company_id);
        $buyerObj->deleteBuyerCompany($company_id);
        $buyerObj->deleteBuyerCompanyPerson($company_id);
        $msg = "Successfully Deleted!!!";
        $msg = base64_encode($msg);
        ?>

        <script>
            window.location = "../view/view-buyers.php?msg=<?php echo $msg; ?>";
        </script>

        <?php




        break;

    case "update_buyer":

        $company_id = $_POST["company_id"];

        $company_name = $_POST["company_name"];
        $company_registration = $_POST["company_registration"];
        $business_type = $_POST["business_type"];
        $website = $_POST["website"];
        $company_address_line_1 = $_POST["company_address_line_1"];
        $company_address_line_2 = $_POST["company_address_line_2"];
        $company_city = $_POST["company_city"];
        $company_postal_code = $_POST["company_postal_code"];
        $company_country = $_POST["company_country"];


        $contact_name = $_POST["contact_name"];
        $job_title = $_POST["job_title"];
        $contact_email = $_POST["contact_email"];
        $contact_phone = $_POST["contact_phone"];
        $office_address_line_1 = $_POST["office_address_line_1"];
        $office_address_line_2 = $_POST["office_address_line_2"];
        $office_address_line_3 = $_POST["office_address_line_3"];

        try {



            $buyerObj->updateBuyerCompany($company_name, $company_registration, $business_type, $website, $company_address_line_1, $company_address_line_2, $company_city, $company_postal_code, $company_country, $company_id);

            $buyerObj->updateBuyerCompanyPerson($company_id, $contact_name, $job_title, $contact_email, $contact_phone, $office_address_line_1, $office_address_line_2, $office_address_line_3);



            $msg = "$company_name Successfully Updated!";
            $msg = base64_encode($msg);
            $company_id = base64_encode($company_id);
        ?>
            <script>
                window.location = "../view/view-buyer.php?company_id=<?php echo $company_id; ?>&msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/edit-buyer.php?msg=<?php echo $msg; ?>";
            </script>
<?php

        }

        break;
}

?>