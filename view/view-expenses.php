<?php
include_once '../commons/session.php';
include '../model/finance_model.php';
$userrow = $_SESSION["user"];

$financeObj = new Finance();

$expenseresult = $financeObj->getAllExpenses();

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Finance Management</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "FINANCE MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="finance.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group" role="group">
                    <a href="add-expense.php" class="btn btn-outline-primary">
                        Add Expense
                    </a>

                    <a href="view-expenses.php" class="btn btn-outline-success active">
                        View Expenses
                    </a>

                    <a href="refund.php" class="btn btn-outline-info">
                        Refunds
                       
                    </a>

                    <a href="purchase-order-payments.php" class="btn btn-outline-secondary">
                        PO Payments
                       
                    </a>

                    <button
            class="btn btn-outline-warning"
            data-bs-toggle="modal"
            data-bs-target="#reportModal">

            Generate Report

          </button>
                </div>
            </div>
        </div>
        <div class="row">&nbsp;</div>

        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    View Expenses
                </h1>
            </div>
        </div>

        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover" id="table">

                            <thead class="table-secondary text-center">
                                <tr>
                                    <th width="10%">Expense Id</th>
                                    <th width="15%">Category</th>
                                    <th width="12%">Amount (Rs.)</th>
                                    <th width="10%">Date</th>
                                    <th width="25%">Description</th>
                                    <th width="10%">Status</th>
                                    <th width="18%">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                while ($row = $expenseresult->fetch_assoc()) {
                                ?>
                                    <tr height="45">
                                        <td><?php echo $row["expense_id"]; ?></td>
                                        <td><?php echo $row["expense_category"]; ?></td>
                                        <td><?php echo number_format($row["expense_amount"], 2); ?></td>
                                        <td><?php echo $row["expense_date"]; ?></td>
                                        <td><?php echo $row["expense_description"]; ?></td>

                                        <?php
                                        if ($row["expense_status"] == "Pending") {
                                            $bg = "bg-warning";
                                        } elseif ($row["expense_status"] == "Approved") {
                                            $bg = "bg-success";
                                        } else {
                                            $bg = "bg-danger";
                                        }
                                        ?>

                                        <td class="text-center <?php echo $bg; ?>"><?php echo $row["expense_status"]; ?></td>
                                        <td>
                                            <?php
                                            if ($row["expense_status"] == "Pending") { ?>
                                                <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal" onclick="approveExpense('<?php echo $row['expense_id']; ?>');">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </a>
                                                <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="rejectExpense('<?php echo $row['expense_id']; ?>');"><i class="bi bi-x-circle"></i> Reject</a>

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

    </div>

    <!-- approve modal -->
    <div class="modal fade" id="approveModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Approve Expense</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/finance_controller.php?status=approve_expense" method="post">
                    <input type="hidden" name="expense_id" id="approve_expense_id">

                    <div class="modal-body">Are you sure you want to approve this expense?</div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function approveExpense(expense_id) {
            document.getElementById("approve_expense_id").value = expense_id;
        }
    </script>

    <!-- reject modal -->
    <div class="modal fade" id="rejectModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Expense</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/finance_controller.php?status=reject_expense" method="post">
                    <input type="hidden" name="expense_id" id="reject_expense_id">

                    <div class="modal-body">Are you sure you want to reject this expense?</div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function rejectExpense(expense_id) {
            document.getElementById("reject_expense_id").value = expense_id;
        }
    </script>


  <div class="modal fade" id="reportModal">
    <div class="modal-dialog">
        <form action="generate-expense-report.php" method="post" target="_blank">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Expense Report</h5>
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
        $("#table").DataTable();
    });
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