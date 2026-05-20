<?php

include_once '../commons/session.php';

//get user information from session
$userrow = $_SESSION["user"];


?>

<html>

<head>
  <?php include_once "../includes/bootstrap_css_includes.php" ?>
  <title>Stock Management</title>
</head>

<body style="border-radius:10px;">
  <div class="container">
    <?php $pageName = "STOCK MANAGEMENT" ?>
    <?php include_once "../includes/header_row_module_includes.php"; ?>

    <div class="row">
      <div class="col-md-4" style="text-align:left;">
        <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>
      </div>
    </div>

    <div class="row">&nbsp;</div>

    <div class="row d-flex justify-content-around align-items-center shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
      <span class="h3 mb-4 fw-bold">Stock Summary</span>
      <div class="row d-flex justify-content-around text-center">
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">---------</div>
          <div class="card-body">
            <h1 class="card-title">
              0 </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">---------</div>
          <div class="card-body">
            <h1 class="card-title">
              0</h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">---------</div>
          <div class="card-body">
            <h1 class="card-title">
              0
            </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">--------</div>
          <div class="card-body">
            <h1 class="card-title">
              0
          </div>
        </div>
      </div>
    </div>

    <div class="row">&nbsp;</div>


    

    <div class="row mt-4 d-flex justify-content-around align-items-center shadow-lg cardgroupstyle">

      <div class="col-md-3">
        <a href="stock-items.php" class="text-decoration-none">
          <div class="card shadow-sm text-center p-3">
            <h4>Materials</h4>
            <p>Manage items, categories, and units</p>
          </div>
        </a>
      </div>

      <div class="col-md-3">
        <a href="stock-list.php" class="text-decoration-none">
          <div class="card shadow-sm text-center p-3">
            <h4>Inventory</h4>
            <p>View and manage stock levels</p>
          </div>
        </a>
      </div>

      <div class="col-md-3">
        <a href="stock-material-request.php" class="text-decoration-none">
          <div class="card shadow-sm text-center p-3">
            <h4>Stock Requests</h4>
            <p>Approve and issue materials</p>
          </div>
        </a>
      </div>

      <div class="col-md-3">
        <a href="stock-purchase-requests.php" class="text-decoration-none">
          <div class="card shadow-sm text-center p-3">
            <h4>Purchase Requests</h4>
            <p>Handle low stock purchasing</p>
          </div>
        </a>
      </div>

    </div>

    <div class="row">&nbsp;</div>




  </div>
  <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>

</html>