<?php
include_once '../commons/session.php';

$userrow = $_SESSION["user"];
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Access Denied</title>
</head>

<body>

    <div class="container">

        <?php $pageName = "ACCESS DENIED" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>



        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                    Back
                </button>
            </div>


        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">

                    <div class="card-header bg-danger text-white text-center fw-semibold">
                        Access Denied
                    </div>

                    <div class="card-body text-center" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">

                        <div class="mb-4">
                            <i class="bi bi-shield-lock-fill text-danger" style="font-size:80px;"></i>
                        </div>

                        <h3 class="text-danger fw-bold">
                            Permission Required
                        </h3>

                        <p class="mt-3">
                            Sorry, you do not have permission to access this page.
                        </p>

                        <p>
                            Please contact your administrator if you need access.
                        </p>

                        <div class="row justify-content-center mt-4">
                            <div class="col-md-4">
                                <a href="dashboard.php" class="btn btn-primary w-100">
                                    Go To Dashboard
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <?php include_once '../includes/footer_includes.php'; ?>

</body>

</html>