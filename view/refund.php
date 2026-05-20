<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';

$userrow = $_SESSION["user"];

$orderObj = new Order();
$orderRefundResult = $orderObj->getAllOrderRefunds();
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Refund Management</title>
</head>


<body>
    <div class="container">
        <?php $pageName = "ORDER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <div class="row">
            <div class="col-md-4 text-start">
                <a href="finance.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Refund Management</h1>
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
                                while ($row = $orderRefundResult->fetch_assoc()) {
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
                                            if ($row["refund_status"] == "Approved") { ?>
                                                <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#processModal" onclick="processRefund('<?php echo $row['refund_id']; ?>','<?php echo $row['refund_amount']; ?>');">
                                                    <i class="bi bi-check-circle"></i> Process
                                                </a>
                                                

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
    <div class="modal fade" id="processModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-info">
                    <h5 class="modal-title">Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/finance_controller.php?status=process_refund" method="post">
    <input type="hidden" name="refund_id" id="process_refund_id">

    <div class="modal-body">
        <!-- Refund Amount Display -->
        <div class="mb-3 p-3 bg-light rounded border">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-muted">Refund Amount:</span>
                <span class="fs-5 fw-bold text-success" id="process_refund_amount">—</span>
            </div>
        </div>

        

        <!-- Payment Method -->
        <div class="mb-3">
            <label for="process_payment_method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
            <select class="form-select" name="payment_method" id="process_payment_method" required>
                <option value="" disabled selected>Select payment method</option>
                <option value="Cash">Cash</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cheque">Cheque</option>
            </select>
        </div>

        <!-- Reference Number -->
        <div class="mb-3">
            <label for="process_ref_no" class="form-label fw-semibold">Reference No. <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="reference_no" id="process_ref_no" placeholder="Enter reference number" required>
        </div>

        <!-- Remarks -->
        <div class="mb-3">
            <label for="process_remarks" class="form-label fw-semibold">Remarks</label>
            <textarea class="form-control" name="remarks" id="process_remarks" rows="3" placeholder="Enter remarks (optional)"></textarea>
        </div>
        <p class="mb-3">Are you sure you want to process this refund?</p>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-info">Process</button>
    </div>
</form>

            </div>
        </div>
    </div>

    <script>
        function processRefund(refund_id,refund_amount) {
            document.getElementById("process_refund_id").value = refund_id;
            document.getElementById('process_refund_amount').textContent = 'Rs.' + parseFloat(refund_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        }
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