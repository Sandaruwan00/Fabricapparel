<?php
include_once '../commons/session.php';
include '../model/finance_model.php';
include_once '../model/order_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$financeObj = new Finance();
$orderObj = new Order();

$totalWeeklyIncome = 0;
$totalWeeklyExpenses = 0;
$totalMonthlyIncome = 0;
$totalMonthlyExpenses = 0;
$totalPendingRefunds = 0;
$totalPendingPOPayments = 0;

// Get Weekly Income and Expenses
$incomeResult = $financeObj->getAllApprovedPayments();

$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

while ($row = $incomeResult->fetch_assoc()) {

    $paymentDate = date('Y-m-d', strtotime($row["payment_datetime"]));

    if ($paymentDate >= $startOfWeek && $paymentDate <= $endOfWeek) {
        $totalWeeklyIncome += $row["amount"];
    }

}

$expensesResult = $financeObj->getAllExpenses();

$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

while ($row = $expensesResult->fetch_assoc()) {

    $expenseDate = date('Y-m-d', strtotime($row["expense_date"]));

    if (
        $row["expense_status"] == "Approved" &&
        $expenseDate >= $startOfWeek &&
        $expenseDate <= $endOfWeek
    ) {
        $totalWeeklyExpenses += $row["expense_amount"];
    }

}

// Get Monthly Income and Expenses
$incomeResultForMonthly = $financeObj->getAllApprovedPayments();

$currentMonth = date('m');
$currentYear = date('Y');

while ($row = $incomeResultForMonthly->fetch_assoc()) {

    $paymentDate = strtotime($row["payment_datetime"]);

    if (
        date('m', $paymentDate) == $currentMonth &&
        date('Y', $paymentDate) == $currentYear
    ) {
        $totalMonthlyIncome += $row["amount"];
    }

}

$expensesResultForMonthly = $financeObj->getAllExpenses();

$currentMonth = date('m');
$currentYear = date('Y');

while ($row = $expensesResultForMonthly->fetch_assoc()) {

    $expenseDate = strtotime($row["expense_date"]);

    if (
        $row["expense_status"] == "Approved" &&
        date('m', $expenseDate) == $currentMonth &&
        date('Y', $expenseDate) == $currentYear
    ) {
        $totalMonthlyExpenses += $row["expense_amount"];
    }

}

// Get Pending Refunds and PO Payments
$orderRefundResult = $orderObj->getAllOrderRefunds();
while ($row = $orderRefundResult->fetch_assoc()) {
    if ($row["refund_status"] == "Pending") {
        $totalPendingRefunds += $row["refund_amount"];
    }
}

$poPaymentsResult = $financeObj->getAllPOPayments();
while ($row = $poPaymentsResult->fetch_assoc()) {
    if ($row["po_payment_status"] == "Pending") {
        $totalPendingPOPayments += $row["po_amount"];
    }
}

// Get Expense Category Data
$expenseCategoryData = [
    "Fuel" => 0,
    "Salary" => 0,
    "Vehicle Maintenance" => 0,
    "Office Bills" => 0,
    "Transport" => 0,
    "Refund" => 0,
    "Other" => 0
];

$expenseCategoryResult = $financeObj->getAllExpenses();

$currentMonth = date('m');
$currentYear = date('Y');

while ($row = $expenseCategoryResult->fetch_assoc()) {

    $expenseDate = strtotime($row["expense_date"]);

    if (
        $row["expense_status"] == "Approved" &&
        date('m', $expenseDate) == $currentMonth &&
        date('Y', $expenseDate) == $currentYear
    ) {

        $category = $row["expense_category"];

        if (array_key_exists($category, $expenseCategoryData)) {
            $expenseCategoryData[$category] += $row["expense_amount"];
        }

    }
}

$expenseCategoryData = array_filter($expenseCategoryData);

?>

<html>

<head>
  <?php include_once "../includes/bootstrap_css_includes.php" ?>
  <title>Finance Management</title>
  <script src="../js/plotly-3.0.1.min.js" charset="utf-8"></script>
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

          <a href="purchase-order-payments.php" class="btn btn-outline-secondary">
            PO Payments
            
          </a>

          <a href="generate-finance-report.php" class="btn btn-outline-warning">
            Generate Report
          </a>
        </div>
      </div>

    </div>

    <div class="row">&nbsp;</div>

    <div class="row">
      <div class="col-md-6">
<div class="row cardgroupstyle">
      <span class="h3 mb-4 fw-bold">Finance Summary</span>

      <div class="row">
       
        <div class="col-md-6">
          <div class="p-3 rounded bg-success shadow-lg">
            <p class="mb-1 small text-white">This Week Income</p>
            <p class="fs-4 fw-bold mb-0 text-white">
              Rs <?php echo number_format($totalWeeklyIncome, 2); ?>
            </p>
          </div>
        </div>
       
        <div class="col-md-6">
          <div class="p-3 rounded bg-danger shadow-lg">
            <p class="mb-1 small text-white">This Week Expenses</p>
            <p class="fs-4 fw-bold mb-0 text-white">
              Rs <?php echo number_format($totalWeeklyExpenses, 2); ?>
            </p>
          </div>
        </div>
       
        
        
      </div>

      <div class="row">&nbsp;</div>

      <div class="row">
        <div class="col-md-6">
          <div class="p-3 rounded bg-success shadow-lg">
            <p class="mb-1 small text-white">Monthly Income</p>
            <p class="fs-4 fw-bold mb-0 text-white">
              Rs <?php echo number_format($totalMonthlyIncome, 2); ?>
            </p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-3 rounded bg-danger shadow-lg">
            <p class="mb-1 small text-white">Monthly Expenses</p>
            <p class="fs-4 fw-bold mb-0 text-white">
              Rs <?php echo number_format($totalMonthlyExpenses, 2); ?>
            </p>
          </div>
        </div>

      </div>

    <div class="row">&nbsp;</div>
      
      <div class="row">
    
        <div class="col-md-6">
          <div class="p-3 rounded bg-warning shadow-lg">
            <p class="mb-1 small text-dark">Pending Refunds</p>
            <p class="fs-4 fw-bold mb-0 text-dark">
              Rs <?php echo number_format($totalPendingRefunds, 2); ?>
            </p>
          </div>
        </div>
       
        <div class="col-md-6">
          <div class="p-3 rounded bg-warning shadow-lg">
            <p class="mb-1 small text-dark">Pending PO Payments</p>
            <p class="fs-4 fw-bold mb-0 text-dark">
              Rs <?php echo number_format($totalPendingPOPayments, 2); ?>
            </p>
          </div>
        </div>
      
        
        
      </div>

    </div>
      </div>


      <div class="col-md-6">
        <div id="expenseChart"></div>
      </div>
      
    </div>

    

    <div class="row">&nbsp;</div>

    




  </div>
  <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>


<script>

var data = [{
    type: "pie",
    values: <?php echo json_encode(array_values($expenseCategoryData)); ?>,
    labels: <?php echo json_encode(array_keys($expenseCategoryData)); ?>,
    textinfo: "label+percent",
    textposition: "outside",
    automargin: true
}];

var layout = {
    height: 400,
    width: 650,
    showlegend: true,
    title: {
    text: "Monthly Expenses by Category"
}
};

Plotly.newPlot('expenseChart', data, layout);

</script>

<!-- <script>
  var data = [{
    values: <?php echo json_encode(array_values($expenseCategoryData)); ?>,
    labels: <?php echo json_encode(array_keys($expenseCategoryData)); ?>,
    type: 'pie'
  }];

  var layout = {
    height: 300,
    width: 650
  };

  Plotly.newPlot('expenseChart', data, layout);
</script> -->

</html>