<?php
include '../commons/session.php';
$userrow = $_SESSION["user"];
if (!isset($_GET["status"])) {
?>
    <script>
        window.location = "../view/login.php";
    </script>
    <?php
}
$status = $_GET["status"];
include '../model/stock_model.php';
include '../model/purchase_model.php';
$stockObj = new Stock();
$purchaseObj = new Purchase();
switch ($status) {
    

    
}
