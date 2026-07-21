<?php
include_once '../commons/session.php';
include_once '../model/finance_model.php';

$userrow = $_SESSION["user"];

$financeObj = new Finance();

$poPayments = $financeObj->getAllPOPayments();

?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Purchase Order Payments</title>
</head>


<body>
    <div class="container">
        <?php $pageName = "FINANCE MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <div class="row">
            <div class="col-md-4 text-start">
                <a href="finance.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group" role="group">
                    <a href="add-expense.php" class="btn btn-outline-primary">
                        Add Expense
                    </a>

                    <a href="view-expenses.php" class="btn btn-outline-success">
                        View Expenses
                    </a>

                    <a href="refund.php" class="btn btn-outline-info">
                        Refunds
                       
                    </a>

                    <a href="purchase-order-payments.php" class="btn btn-outline-secondary active">
                        PO Payments
                       
                    </a>

                    <a href="generate-finance-report.php" class="btn btn-outline-warning">
                        Generate Report
                    </a>
                </div>
            </div>
            

        </div>
        
        <div class="row">&nbsp;</div>

        <div class="row justify-content-center">
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Purchase Order Payments</h1>
            </div>
        </div>


        <div class="row">&nbsp;</div>




        <!-- Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle" id="table">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th>#</th>
                                <th>PO ID</th>
                                <th>Supplier</th>
                                <th>Supplier Email</th>
                                <th>Amount (Rs)</th>
                                <th>Date</th>
                                <th class="text-center">Payment Status</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = $poPayments->fetch_assoc()) {
                            ?>
                                <tr height="45">
                                    <td><?php echo $row["po_payment_id"]; ?></td>
                                    <td><?php echo "PO" . $row["po_id"]; ?></td>
                                    <td><?php echo $row["supplier_name"]; ?></td>
                                    <td><?php echo $row["supplier_email"]; ?></td>
                                    <td><?php echo $row["po_amount"]; ?></td>
                                    <td><?php echo $row["po_payment_date"]; ?></td>

                                    <?php
                                    if ($row["po_payment_status"] == "Pending") {
                                        $bg = "bg-warning";
                                    } else {
                                        $bg = "bg-success";
                                    }
                                    ?>

                                    <td class="text-center <?php echo $bg; ?>"><?php echo $row["po_payment_status"]; ?></td>
                                    <td>
                                        <?php
                                        if ($row["po_payment_status"] == "Pending") { ?>
                                            <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#paidModal" onclick="pay('<?php echo $row['po_payment_id']; ?>','<?php echo $row['po_amount']; ?>','<?php echo $row['po_id']; ?>');">
                                                <i class="bi bi-cash"></i> Pay
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
    <div class="modal fade" id="paidModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-info">
                    <h5 class="modal-title">Pay Purchase Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/finance_controller.php?status=pay_po" method="post">
                    <input type="hidden" name="po_payment_id" id="po_payment_id">
                    <input type="hidden" name="po_id" id="po_id">

                    <div class="modal-body">
                        <!-- Refund Amount Display -->
                        <div class="mb-3 p-3 bg-light rounded border">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold text-muted">Amount:</span>
                                <span class="fs-5 fw-bold text-success" id="po_amount">—</span>
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



                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Pay</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function pay(po_payment_id, po_amount, po_id) {
            document.getElementById("po_payment_id").value = po_payment_id;
            document.getElementById("po_amount").textContent = 'Rs.' + parseFloat(po_amount).toLocaleString('en-LK', {
                minimumFractionDigits: 2
            });
            document.getElementById("po_id").value = po_id;
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
        $("#table").DataTable();
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