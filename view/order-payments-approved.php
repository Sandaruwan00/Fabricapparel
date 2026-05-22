<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';

$userrow = $_SESSION["user"];

$orderObj = new Order();
$orderPaymentResult = $orderObj->getApprovedOrderPayments();
$pendingCount = $orderObj->getPendingOrderPaymentsCount();
$badge = $pendingCount->fetch_assoc();
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Approved Payments</title>
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
                <h1 style="font-size:28px; font-weight:600;">Approved Payments</h1>
            </div>

        </div>


        <div class="row">&nbsp;</div>





        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="order-payments.php">All Payments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="order-payments-pending.php">Pending Payments <span class="badge text-bg-warning"><?php echo $badge["pending_count"];  ?></span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="order-payments-approved.php">Approved Payments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="order-payments-rejected.php">Rejected Payments</a>
            </li>
        </ul>

        <div class="row">&nbsp;</div>

        <!-- Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle" id="ordertable">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Payment Date</th>
                                <th width="10%">Amount</th>
                                <th width="10%">Order ID</th>
                                <th width="10%">Method</th>
                                <th width="15%">Ref. No.</th>
                                <th width="15%" class="text-center">Payment Status</th>
                                <th width="20%">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $orderPaymentResult->fetch_assoc()) {

                                $order_id = base64_encode($row["order_id"]);
                            ?>
                                <tr height="50px">
                                    <td><?php echo $row["order_payment_id"]; ?></td>
                                    <td><?php echo $row["payment_datetime"]; ?></td>
                                    <td><?php echo $row["amount"]; ?></td>
                                    <td><?php echo "ORD".$row["order_id"]; ?></td>
                                    <td><?php echo $row["payment_method"]; ?></td>
                                    <td><?php echo $row["reference_no"]; ?></td>

                                    <?php
                                    if ($row["payment_status"] == "Pending") {

                                    ?>
                                        <td class="text-center bg-warning"><?php echo $row["payment_status"]; ?></td>

                                        <td>

                                            <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal" onclick="loadpayment('<?php echo $row['order_payment_id']; ?>');">
                                                <i class="bi bi-check-square"></i>
                                                &nbsp
                                                Approve
                                            </a>


                                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="loadReject('<?php echo $row['order_payment_id']; ?>');">
                                                <i class="bi bi-x-square"></i>&nbsp;Reject
                                            </a>
                                        </td>

                                    <?php

                                    } elseif ($row["payment_status"] == "Approved") {

                                    ?>
                                        <td class="text-center bg-success"><?php echo $row["payment_status"]; ?></td>

                                        <td>

                                        </td>

                                    <?php

                                    } else {

                                    ?>
                                        <td class="text-center bg-danger"><?php echo $row["payment_status"]; ?></td>

                                        <td>

                                        </td>

                                    <?php

                                    }

                                    ?>

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

<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirm Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to approve payment?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a type="button" id="confirmApproveBtn" class="btn btn-success">Approve</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Confirm Rejection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reject this payment?</p>
                <div class="mb-3">
                    <label for="rejectRemark" class="form-label fw-bold">Remark <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejectRemark" rows="3" placeholder="Enter reason for rejection..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a type="button" id="confirmRejectBtn" class="btn btn-danger">Reject</a>
            </div>
        </div>
    </div>
</div>


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

<script>
    function loadpayment(order_payment_id) {

        document.getElementById("confirmApproveBtn").href =
            "../controller/order_controller.php?status=approve_order_payment&order_payment_id=" + order_payment_id;
    }
</script>

<script>
    function loadReject(order_payment_id) {
        // Reset remark field each time modal opens
        document.getElementById("rejectRemark").value = "";

        document.getElementById("confirmRejectBtn").onclick = function() {
            var remark = document.getElementById("rejectRemark").value.trim();

            if (remark === "") {
                alert("Please enter a remark before rejecting.");
                return;
            }

            window.location.href = "../controller/order_controller.php?status=reject_order_payment&order_payment_id=" + order_payment_id + "&payment_remarks=" + encodeURIComponent(remark);
        };
    }
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