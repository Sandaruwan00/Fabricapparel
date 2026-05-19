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
                <div class="btn-group">
                    <a href="add-expense.php" class="btn btn-outline-primary active">Add Expense</a>
                    <a href="view-expenses.php" class="btn btn-outline-success">View Expenses</a>
                    <a href="generate-finance-report.php" class="btn btn-outline-warning">Generate Finance Report</a>
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

        <div class="row">&nbsp;</div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-dark text-white fw-semibold">
                        Expenses Information
                    </div>
                    <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">
                        <form action="../controller/finance_controller.php?status=add_expense" method="post">

                            <div class="mb-3">
                                <label class="form-label">Expense Category</label>

                                <select name="expense_category" class="form-select" required>
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
                                <input type="number" step="0.01" name="expense_amount" class="form-control" placeholder="Enter Amount" required>
                            </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Expense Date</label>
                                <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="expense_description" rows="4" class="form-control" placeholder="Enter Expense Description" required></textarea>
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
</body>
<script src="../js/jquery-3.7.1.js"></script>

</html>