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