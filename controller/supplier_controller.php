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

include '../model/supplier_model.php';

$supplierObj = new Supplier();

switch ($status) {
    case "add_supplier":

        $supplier_name = $_POST["supplier_name"];
        $supplier_contact_person = $_POST["supplier_contact_person"];
        $supplier_contact_person_nic = $_POST["supplier_contact_person_nic"];
        $supplier_phone = $_POST["supplier_phone"];
        $supplier_email = $_POST["supplier_email"];
        $supplier_address = $_POST["supplier_address"];


        try {

            if ($supplier_name == "") {
                throw new Exception("Supplier Name Cannot Be Empty!");
            }

            if ($supplier_contact_person == "") {
                throw new Exception("Contact Person Cannot Be Empty!");
            }

            if ($supplier_contact_person_nic == "") {
                throw new Exception("Contact Person NIC Cannot Be Empty!");
            }

            if ($supplier_phone == "") {
                throw new Exception("Phone Number Cannot Be Empty!");
            }

            if ($supplier_email == "") {
                throw new Exception("Email Cannot Be Empty!");
            }

            if ($supplier_address == "") {
                throw new Exception("Address Cannot Be Empty!");
            }

            // NIC validation
            if (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $supplier_contact_person_nic)) {
                throw new Exception("Invalid NIC Number!");
            }


            // Mobile validation
            if (!preg_match('/^07[0-9]{8}$/', $supplier_phone)) {
                throw new Exception("Invalid Mobile Number!");
            }


            // Email validation
            if (!filter_var($supplier_email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid Email Address!");
            }

            if ($supplierObj->checkSupplierNIC($supplier_contact_person_nic)) {
                throw new Exception("This NIC Number already exists!");
            }

            if ($supplierObj->checkSupplierEmail($supplier_email)) {
                throw new Exception("This Email already exists!");
            }

            $supplier_id = $supplierObj->addSupplier($supplier_name, $supplier_contact_person, $supplier_contact_person_nic, $supplier_phone, $supplier_email, $supplier_address);


            $msg = "$supplier_name Successfully Added";
            $msg = base64_encode($msg);

    ?>

            <script>
                window.location = "../view/supplier.php?msg=<?php echo $msg; ?>";
            </script>


        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/supplier.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "update_supplier":

        $supplier_id = $_POST["supplier_id"];
        $supplier_name = $_POST["supplier_name"];
        $supplier_contact_person = $_POST["supplier_contact_person"];
        $supplier_contact_person_nic = $_POST["supplier_contact_person_nic"];
        $supplier_phone = $_POST["supplier_phone"];
        $supplier_email = $_POST["supplier_email"];
        $supplier_address = $_POST["supplier_address"];


        try {

            if ($supplier_name == "") {
                throw new Exception("Supplier Name Cannot Be Empty!");
            }

            if ($supplier_contact_person == "") {
                throw new Exception("Contact Person Cannot Be Empty!");
            }

            if ($supplier_contact_person_nic == "") {
                throw new Exception("Contact Person NIC Cannot Be Empty!");
            }

            if ($supplier_phone == "") {
                throw new Exception("Phone Number Cannot Be Empty!");
            }

            if ($supplier_email == "") {
                throw new Exception("Email Cannot Be Empty!");
            }

            if ($supplier_address == "") {
                throw new Exception("Address Cannot Be Empty!");
            }

            // NIC validation
            if (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $supplier_contact_person_nic)) {
                throw new Exception("Invalid NIC Number!");
            }


            // Mobile validation
            if (!preg_match('/^07[0-9]{8}$/', $supplier_phone)) {
                throw new Exception("Invalid Mobile Number!");
            }


            // Email validation
            if (!filter_var($supplier_email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid Email Address!");
            }

            if ($supplierObj->checkSupplierNICUpdate($supplier_contact_person_nic, $supplier_id)) {
                throw new Exception("This NIC Number already exists!");
            }

            if ($supplierObj->checkSupplierEmailUpdate($supplier_email, $supplier_id)) {
                throw new Exception("This Email already exists!");
            }

            $supplierObj->updateSupplier($supplier_id, $supplier_name, $supplier_contact_person, $supplier_contact_person_nic, $supplier_phone, $supplier_email, $supplier_address);


            $msg = "$supplier_name Successfully Updated";
            $msg = base64_encode($msg);

        ?>

            <script>
                window.location = "../view/supplier.php?msg=<?php echo $msg; ?>";
            </script>


        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/supplier.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "delete_supplier":
        $supplier_id = $_GET["supplier_id"];
        $supplierObj->deleteSupplier($supplier_id);
        $msg = "Successfully Deleted!!!";
        $msg = base64_encode($msg);
        ?>

        <script>
            window.location = "../view/supplier.php?msg=<?php echo $msg; ?>";
        </script>

<?php

        break;
}

?>