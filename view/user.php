<?php

include_once '../commons/session.php';
include_once '../model/module_model.php';
include_once '../model/user_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$moduleObj = new Module();

$moduleResult = $moduleObj->getAllModules();

$userObj = new User();

$activeResult = $userObj->getActiveUserCount();
$active_row = $activeResult->fetch_assoc();
$deactiveResult = $userObj->getDeActiveUserCount();
$deactive_row = $deactiveResult->fetch_assoc();
$allUserResult = $userObj->getAllUserCount();
$alluser_row = $allUserResult->fetch_assoc();
$removedUserResult = $userObj->getRemovedUserCount();
$removeduser_row = $removedUserResult->fetch_assoc();

$roleCountResult = $userObj->getAllUserRoleCount();





?>

<html>

<head>
  <?php include_once "../includes/bootstrap_css_includes.php" ?>
  <title>User Management</title>
  <script src="../js/plotly-3.0.1.min.js" charset="utf-8"></script>
</head>

<body style="border-radius:10px;">
  <div class="container">
    <?php $pageName = "USER MANAGEMENT" ?>
    <?php include_once "../includes/header_row_module_includes.php"; ?>


    <div class="row">
      <div class="col-md-4" style="text-align:left;">
        <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>

      </div>
      <div class="col-md-4" style="text-align:center;">

      </div>
      <div class="col-md-4" style="text-align:right;">
        <div class="btn-group">
          <a href="add-user.php" class="btn btn-outline-primary">Add User</a>
          <a href="view-users.php" class="btn btn-outline-success">View Users</a>
          <a href="generate-user-report.php" class="btn btn-outline-warning">Generate User Report</a>
        </div>
      </div>
    </div>
    <div class="row">
      &nbsp;
    </div>

    <div class="row d-flex justify-content-around align-items-center shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
      <span class="h3 mb-4 fw-bold">User Summary</span>
      <div class="row d-flex justify-content-around text-center">
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">All Users</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $alluser_row["user_count"]; ?>
            </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Active Users</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $active_row["user_count"]; ?>
            </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">De-active Users</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $deactive_row["user_count"]; ?>
            </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Removed Users</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $removeduser_row["user_count"]; ?>
            </h1>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      &nbsp;
    </div>

    <div class="row d-flex justify-content-around align-items-center ">



      <div class="col-md-6">

        <div class="row shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
          <span class="h3 mb-4 fw-bold">User Roles</span>
          <div class="col-md-12">

            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
              <table class="table table-bordered table-hover align-middle" id="usertable">
                <thead class="fs-6 table-secondary text-center">
                  <tr>
                    <th>User Roles</th>
                    <th style="width: 30%;">Role User Count</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  while ($roleRow = $roleCountResult->fetch_assoc()) {
                  ?>
                    <tr>
                      <td><?php echo $roleRow["role_name"]; ?></td>
                      <td><?php echo $roleRow["user_count"]; ?></td>
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
      <div class="col-md-6">
        <div id="tester">

        </div>
      </div>

    </div>


  </div>
  <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>

<script>
  var data = [{
    values: [<?php echo $active_row["user_count"]; ?>, <?php echo $deactive_row["user_count"]; ?>],
    labels: ['Active User Count', 'De Active User Count'],
    type: 'pie'
  }];

  var layout = {
    height: 300,
    width: 650
  };

  Plotly.newPlot('tester', data, layout);
</script>

</html>