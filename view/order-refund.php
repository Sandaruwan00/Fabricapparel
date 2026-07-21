<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';

$userrow = $_SESSION["user"];

$orderObj = new Order();
$orderPaymentRequestResult = $orderObj->getAllOrderRefunds();
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
            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-order.php" class="btn btn-outline-primary">Add Order</a>
                    <a href="view-orders.php" class="btn btn-outline-success">View Orders</a>
                    <a href="order-payments.php" class="btn btn-outline-info">Order Payments</a>
                    <a href="order-refund.php" class="btn btn-outline-secondary active">Refund Requests</a>
                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reportModal">Generate Order Reports</button>
                </div>
            </div>

        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Refund Requests</h1>
            </div>
        </div>


        <div class="row">&nbsp;</div>



        
        <!-- Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle" id="refundtable">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Order No.</th>
                                <th width="20%">Company Name</th>
                                <th width="13%">Refund Amount (Rs)</th>
                                <th width="15%" class="text-center">Refund Status</th>
                                <th width="20%">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php
                                while ($row = $orderPaymentRequestResult->fetch_assoc()) {
                                ?>
                                    <tr height="45">
                                        <td><?php echo $row["refund_id"]; ?></td>
                                        <td><?php echo "ORD".$row["order_id"]; ?></td>
                                        <td><?php echo $row["company_name"]; ?></td>
                                        <td><?php echo $row["refund_amount"]; ?></td>

                                        <?php
                                        if ($row["refund_status"] == "Pending") {
                                            $bg = "bg-warning";
                                        } elseif ($row["refund_status"] == "Approved") {
                                            $bg = "bg-success";
                                        } elseif ($row["refund_status"] == "Processed") {
                                            $bg = "bg-info";
                                        } else {
                                            $bg = "bg-danger";
                                        }
                                        ?>

                                        <td class="text-center <?php echo $bg; ?>"><?php echo $row["refund_status"]; ?></td>
                                        <td>
                                            <?php
                                            if ($row["refund_status"] == "Pending") { ?>
                                                <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal" onclick="approveRefund('<?php echo $row['refund_id']; ?>');">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </a>
                                                <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="rejectRefund('<?php echo $row['refund_id']; ?>');"><i class="bi bi-x-circle"></i> Reject</a>

                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


     <!-- approve modal -->
    <div class="modal fade" id="approveModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Approve Refund</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/order_controller.php?status=approve_refund" method="post">
                    <input type="hidden" name="refund_id" id="approve_refund_id">

                    <div class="modal-body">Are you sure you want to approve this refund?</div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function approveRefund(refund_id) {
            document.getElementById("approve_refund_id").value = refund_id;
        }
    </script>

    <!-- reject modal -->
    <div class="modal fade" id="rejectModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Refund</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/order_controller.php?status=reject_refund" method="post">
                    <input type="hidden" name="refund_id" id="reject_refund_id">

                    <div class="modal-body">Are you sure you want to refund this expense?</div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function rejectRefund(refund_id) {
            document.getElementById("reject_refund_id").value = refund_id;
        }
    </script>
    

    <div class="modal fade" id="reportModal">
    <div class="modal-dialog">
        <form action="generate-order-refund-report.php" method="post" target="_blank">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Order Refund Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label>Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>

                    <br>

                    <label>End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Generate Report
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let today = new Date().toISOString().split("T")[0];

    document.getElementById("start_date").setAttribute("max", today);
    document.getElementById("end_date").setAttribute("max", today);

});
</script>
    
    

    <?php include_once '../includes/footer_includes.php'; ?>

</body>




<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
    $(document).ready(function() {
        $("#refundtable").DataTable();
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