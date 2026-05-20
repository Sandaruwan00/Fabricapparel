<?php
include_once '../commons/session.php';
include '../model/finance_model.php';
include_once '../model/order_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$financeObj = new Finance();
$orderObj = new Order();

//calculate total refunds
$orderRefundResult = $orderObj->getAllOrderRefunds();
$totalRefunds = 0;
$approvedRefundsCount = 0;
while ($refundrow = $orderRefundResult->fetch_assoc()) {
  if ($refundrow["refund_status"] == "Processed") {
    $totalRefunds = $totalRefunds + $refundrow["refund_amount"];
  }
  if ($refundrow["refund_status"] == "Approved") {
    $approvedRefundsCount++;
  }
}

//calculate total expenses
$expensesResult = $financeObj->getAllExpenses();
$totalExpense = 0;
while ($expenserow = $expensesResult->fetch_assoc()) {
  if ($expenserow["expense_status"] == "Approved") {
    $totalExpense = $totalExpense + $expenserow["expense_amount"];
  }
}

$totalCompanyExpenses = $totalRefunds + $totalExpense;

$incomeResult = $financeObj->getAllApprovedPayments();
$totalIncome = 0;
while ($incomerow = $incomeResult->fetch_assoc()) {
  $totalIncome = $totalIncome + $incomerow["amount"];
}

$totalProfit = $totalIncome - $totalCompanyExpenses;

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
        <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>

      </div>
      <div class="col-md-8" style="text-align:right;">
        <div class="btn-group">
          <a href="add-expense.php" class="btn btn-outline-primary">Add Expense</a>
          <a href="view-expenses.php" class="btn btn-outline-success">View Expenses</a>
          <a href="generate-finance-report.php" class="btn btn-outline-warning">Generate Finance Report</a>
        </div>
      </div>

    </div>

    <div class="row">&nbsp;</div>

    <div class="row cardgroupstyle">

      <!-- TOTAL ORDER COST -->
      <div class="col-md-4">
        <div class="p-3 rounded bg-light shadow-lg">
          <p class="text-muted mb-1 small">TOTAL INCOME</p>
          <p class="fs-4 fw-bold mb-0">
            Rs <?php echo number_format($totalIncome, 2); ?>
          </p>
        </div>
      </div>

      <!-- TOTAL PAYMENTS -->
      <div class="col-md-4">
        <div class="p-3 rounded bg-light shadow-lg">
          <p class="text-muted mb-1 small">TOTAL EXPENSES</p>
          <p class="fs-4 fw-bold mb-0 text-danger">
            Rs <?php echo number_format($totalCompanyExpenses, 2); ?>
          </p>
        </div>
      </div>

      <!-- DUE AMOUNT -->
      <div class="col-md-4">
        <div class="p-3 rounded bg-light shadow-lg">
          <p class="text-muted mb-1 small">TOTAL PROFIT</p>
          <p class="fs-4 fw-bold mb-0 text-success">
            Rs <?php echo number_format($totalProfit, 2); ?>
          </p>
        </div>
      </div>

    </div>

    <div class="row">&nbsp;</div>

    <div class="row cardgroupstyle">
      <div class="col-md-3">
        <a href="refund.php" class="text-decoration-none">
          <div class="card shadow-sm text-center p-3">
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
              <?php echo $approvedRefundsCount; ?>
            </span>
            <h4>Refund Management</h4>
            <p>Handle customer refunds and transaction reversals</p>
          </div>
        </a>
      </div>
      <div class="col-md-3">
        <a href="refund.php" class="text-decoration-none">
          <div class="card shadow-sm text-center p-3">
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
              ...
            </span>
            <h4>PO Payments</h4>
            <p>Manage supplier payments for purchase orders</p>
          </div>
        </a>
      </div>
    </div>




  </div>
  <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>

</html>