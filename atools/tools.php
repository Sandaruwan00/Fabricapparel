<?php
$pageTitle = "System Tools";
?>

<html>

<head>

    <?php include_once "../includes/bootstrap_css_includes.php"; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <title><?php echo $pageTitle; ?></title>

</head>

<body style="background-color:#f5f7fb;">

<div class="container py-4">

    <!-- PAGE TITLE -->
    <div class="row mb-4">

        <div class="col-md-12 text-center">

            <h1 class="fw-bold">
                ⚙️ <?php echo $pageTitle; ?>
            </h1>

            <p class="text-muted">
                Quick access to management and utility pages
            </p>

        </div>

    </div>

    <!-- TOOLS -->
    <div class="row g-4">

        <!-- ORDER STATUS -->
        <div class="col-md-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <div class="mb-3" style="font-size:50px;">
                        🎨
                    </div>

                    <h5 class="fw-bold">
                        Order Status
                    </h5>

                    <p class="text-muted small">
                        Manage order statuses and colors
                    </p>

                    <a href="status.php"
                       class="btn btn-primary w-100">

                        Open

                    </a>

                </div>

            </div>

        </div>

        <!-- CREATE SHIPMENT -->
        <!-- <div class="col-md-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <div class="mb-3" style="font-size:50px;">
                        🚚
                    </div>

                    <h5 class="fw-bold">
                        Create Shipment
                    </h5>

                    <p class="text-muted small">
                        Assign warehouse packages to shipments
                    </p>

                    <a href="create-shipment.php"
                       class="btn btn-success w-100">

                        Open

                    </a>

                </div>

            </div>

        </div> -->

        <!-- VIEW SHIPMENTS -->
        <!-- <div class="col-md-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <div class="mb-3" style="font-size:50px;">
                        📦
                    </div>

                    <h5 class="fw-bold">
                        View Shipments
                    </h5>

                    <p class="text-muted small">
                        Track and manage shipment records
                    </p>

                    <a href="view-shipments.php"
                       class="btn btn-info text-white w-100">

                        Open

                    </a>

                </div>

            </div>

        </div> -->

        <!-- REPORTS -->
        <!-- <div class="col-md-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body text-center">

                    <div class="mb-3" style="font-size:50px;">
                        📊
                    </div>

                    <h5 class="fw-bold">
                        Reports
                    </h5>

                    <p class="text-muted small">
                        Generate warehouse and shipment reports
                    </p>

                    <a href="generate-warehouse-reports.php"
                       class="btn btn-warning w-100">

                        Open

                    </a>

                </div>

            </div>

        </div> -->

    </div>

</div>

<?php include_once "../includes/footer_includes.php"; ?>

</body>

<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</html>