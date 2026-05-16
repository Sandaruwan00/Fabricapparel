<?php

include_once '../commons/session.php';
include_once '../model/module_model.php';
include_once '../model/user_model.php';

//get user information from session
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
        <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>

      </div>
      <div class="col-md-8" style="text-align:right;">
        <div class="btn-group">
          <a href="add-expense.php" class="btn btn-outline-primary">Add Expense</a>
          <a href="" class="btn btn-outline-success">View Expenses</a>
          <a href="" class="btn btn-outline-warning">Generate Finance Report</a>
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
            Rs 10,000.00
          </p>
        </div>
      </div>

      <!-- TOTAL PAYMENTS -->
      <div class="col-md-4">
        <div class="p-3 rounded bg-light shadow-lg">
          <p class="text-muted mb-1 small">TOTAL EXPENSES</p>
          <p class="fs-4 fw-bold mb-0 text-danger">
            Rs 10,000.00
          </p>
        </div>
      </div>

      <!-- DUE AMOUNT -->
      <div class="col-md-4">
        <div class="p-3 rounded bg-light shadow-lg">
          <p class="text-muted mb-1 small">TOTAL PROFIT</p>
          <p class="fs-4 fw-bold mb-0 text-success">
            Rs 10,000.00
          </p>
        </div>
      </div>

    </div>

    <div class="row">&nbsp;</div>

    <div class="row cardgroupstyle">
      <div class="col-md-3">
        <a href="" class="text-decoration-none">
          <div class="card shadow-sm text-center p-3">
            <h4>Refund Management</h4>
            <p>Handle customer refunds and transaction reversals</p>
          </div>
        </a>
      </div>
    </div>




  </div>
  <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>

</html>