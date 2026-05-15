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
      <div class="col-md-4" style="text-align:center;">

      </div>
      
    </div>
 











  </div>
  <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>

</html>