<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';

$userrow = $_SESSION["user"];

$orderObj = new Order();
$orderResult = $orderObj->getAllPendingOrders();
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Pending Orders</title>
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
                <h1 style="font-size:28px; font-weight:600;">View Orders</h1>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <a href="add-order.php" class="btn btn-outline-primary">Add Order</a>
                    <a href="view-orders.php" class="btn btn-outline-success active">View Orders</a>
                    <a href="generate-order-report.php" class="btn btn-outline-warning">Generate Order Reports</a>
                </div>
            </div>
        </div>
        

        <div class="row">&nbsp;</div>

        <?php $currentPage = "pending-orders" ?>
        <?php include_once "../includes/view_orders_nav_includes.php"; ?>

        <div class="row">&nbsp;</div>


        <!-- Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle" id="ordertable">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th width="8%">Order ID</th>
                                <th width="22%">Company</th>
                                <th width="17%">Contact Person</th>
                                <th width="13%">Order Date</th>
                                <th width="10%">Total (Rs)</th>
                                <th width="10%">Due Date</th>
                                <th width="10%">Status</th>
                                <th width="10%">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $orderResult->fetch_assoc()) {

                                $order_id = base64_encode($row["order_id"]);
                            ?>
                                <tr>
                                    <td><?php echo "ORD".$row["order_id"]; ?></td>
                                    <td><?php echo $row["company_name"]; ?></td>
                                    <td><?php echo $row["contact_name"]; ?></td>
                                    <td><?php echo $row["order_date"]; ?></td>
                                    <td><?php echo $row["total_amount"]; ?></td>
                                    <td class="text-center
                                    <?php
                                        $expected = $row["expected_delivery_date"];
                                        $today = date("Y-m-d");

                                        $days = ceil((strtotime($expected) - strtotime($today)) / (60 * 60 * 24));

                                        if ($days > 0) {
                                            echo "bg-success text-white";
                                        } elseif ($days == 0) {
                                            echo "bg-warning text-dark";
                                        } else {
                                            echo "bg-danger text-white";
                                        }
                                    ?>
                                    ">
                                    <?php
                                        if ($days > 0) {
                                            echo "$days days left";
                                        } elseif ($days == 0) {
                                            echo "Due Today";
                                        } else {
                                            echo abs($days) . " days overdue";
                                        }
                                    ?>
                                    </td>
                                    <td class="text-center" style="background-color: <?php echo $row["color_code"]; ?> ;"><?php echo $row["status_name"]; ?></td>


                                    <td>
                                        <span class="d-flex justify-content-between">

                                            <a href="view-order.php?order_id=<?php echo $order_id; ?>" class="btn btn-info">
                                                <i class="bi bi-eye-fill"></i> View
                                            </a>



                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
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

<!-- alert start -->
<?php
$msg = "";
if (isset($_GET["msg"])) {
    $msg = base64_decode($_GET["msg"]);
}
?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="msgToast" class="toast align-items-center text-bg-secondary border-0" role="alert" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body" id="toastMsg">
                <!-- Message -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let msg = "<?php echo $msg; ?>";
        if (msg !== "") {
            document.getElementById("toastMsg").innerText = msg;
            let toastEl = document.getElementById("msgToast");
            let toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    });
</script>
<!-- alert end -->

</html>