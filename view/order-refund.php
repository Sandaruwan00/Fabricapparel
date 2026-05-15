<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';

$userrow = $_SESSION["user"];

$orderObj = new Order();
$orderPaymentResult = $orderObj->getAllOrderPayments();
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Refund Requests</title>
</head>


<body>
    <div class="container">
        <?php $pageName = "ORDER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <div class="row">
            <div class="col-md-4 text-start">
                <a href="order.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Refund Requests</h1>
            </div>

        </div>


        <div class="row">&nbsp;</div>



        <!-- Message -->
        <?php if (isset($_GET["msg"])) { ?>
            <div class="row justify-content-center" id="msg">
                <div class="col-md-6 alert alert-success text-center">
                    <?php echo base64_decode($_GET["msg"]); ?>
                </div>
            </div>
        <?php } ?>

        <!-- Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle" id="ordertable">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Order No.</th>
                                <th width="20%">Company Name</th>
                                <th width="13%">Order Amount</th>
                                <th width="12%">Payments</th>
                                <th width="15%" class="text-center">Payment Status</th>
                                <th width="20%">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer_includes.php'; ?>

</body>




<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
    $(document).ready(function() {
        $("#ordertable").DataTable();
    });

    // Hide message
    setTimeout(() => {
        let msg = document.getElementById("msg");
        if (msg) msg.style.display = "none";
    }, 3000);
</script>


</html>