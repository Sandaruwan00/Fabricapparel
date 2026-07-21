<?php
include_once '../commons/session.php';
$userrow = $_SESSION["user"];
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
                    <a href="add-expense.php" class="btn btn-outline-primary active">
                        Add Expense
                    </a>

                    <a href="view-expenses.php" class="btn btn-outline-success">
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
                    Add Expense
                </h1>
            </div>
        </div>


        <div class="row justify-content-center" style="margin-top:25px;">
            <div id="msg" class="col-md-4 text-center">
                <?php if (isset($_GET["msg"])) { ?>
                    <div class="alert alert-danger text-center">
                        <?php echo base64_decode($_GET["msg"]); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-dark text-white fw-semibold">
                        Expenses Information
                    </div>
                    <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">
                        <form id="expenseForm" action="../controller/finance_controller.php?status=add_expense" method="post">

                            <div class="mb-3">
                                <label class="form-label">Expense Category</label>

                                <select name="expense_category" class="form-select" id="expense_category">
                                    <option value="">-- Select Category --</option>
                                    <option value="Fuel">Fuel</option>
                                    <option value="Salary">Salary</option>
                                    <option value="Vehicle Maintenance">Vehicle Maintenance</option>
                                    <option value="Office Bills">Office Bills</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Refund">Refund</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>



                            <div class="mb-3">
                                <label class="form-label">Expense Amount</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" id="btnGroupAddon">Rs</div>
                                    </div>
                                    <input type="number" step="0.01" name="expense_amount" class="form-control" placeholder="Enter Amount" id="expense_amount">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Expense Date</label>
                                <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" id="expense_date">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="expense_description" rows="4" class="form-control" placeholder="Enter Expense Description" id="expense_description"></textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-danger px-4">Add Expense</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once '../includes/footer_includes.php'; ?>

    <div class="modal fade" id="reportModal">
    <div class="modal-dialog">
        <form action="generate-finance-report.php" method="post" target="_blank">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Finance Report</h5>
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
    
    
</body>
<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>

<script>
    $(document).ready(function() {

        var today = new Date().toISOString().split("T")[0];
        $("#expense_date").attr("min", today);

        $("#expenseForm").submit(function() {

            $("#msg").removeClass("alert alert-danger").html("");

            var expense_category = $("#expense_category").val();
            var expense_amount = $("#expense_amount").val().trim();
            var expense_date = $("#expense_date").val();
            var expense_description = $("#expense_description").val().trim();

            if (expense_category == "") {
                $("#msg").html("Please Select an Expense Category!");
                $("#msg").addClass("alert alert-danger");
                return false;
            }

            if (expense_amount == "") {
                $("#msg").html("Expense Amount Cannot Be Empty!");
                $("#msg").addClass("alert alert-danger");
                return false;
            }

            if (isNaN(expense_amount) || parseFloat(expense_amount) <= 0) {
                $("#msg").html("Expense Amount Must Be Greater Than Zero!");
                $("#msg").addClass("alert alert-danger");
                return false;
            }

            if (expense_date == "") {
                $("#msg").html("Please Select an Expense Date!");
                $("#msg").addClass("alert alert-danger");
                return false;
            }

            if (expense_description == "") {
                $("#msg").html("Expense Description Cannot Be Empty!");
                $("#msg").addClass("alert alert-danger");
                return false;
            }

            if (expense_description.length < 5) {
                $("#msg").html("Expense Description Must Be At Least 5 Characters!");
                $("#msg").addClass("alert alert-danger");
                return false;
            }

        });

    });
</script>

</html>