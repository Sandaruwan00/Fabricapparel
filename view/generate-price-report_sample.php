<?php
include_once '../commons/session.php';
include '../model/price_model.php';

// session user
$userrow = $_SESSION["user"];
$priceObj = new Price();
$productTypeResult = $priceObj->getAllProductType();
$sizeResult = $priceObj->getAllSizes();

?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Generate Price Report</title>
</head>

<body>
    <div class="container">

        <?php $pageName = "PRICE REPORTS"; ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>


        <div class="row">
            <div class="col-md-4 text-start">
                <a href="price.php" class="btn btn-outline-secondary">Back</a>
            </div>

            <div class="col-md-4 text-center">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Generate Reports
                </h1>
            </div>

            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <a href="product-type.php" class="btn btn-outline-primary">Product Types</a>
                    <a href="sizing.php" class="btn btn-outline-success">Sizing</a>
                    <a href="price.php" class="btn btn-outline-success">Pricing</a>
                    <a href="generate-price-report.php" class="btn btn-outline-warning active">Generate Reports</a>
                </div>
            </div>
        </div>


        <div class="row mt-5 justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">

                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Filter Report</h5>
                    </div>

                    <div class="card-body" style="padding-left: 35px;">

                        <form action="../controller/price_controller.php?status=generate_report" method="post" target="_blank">

                            <!-- PRODUCTS -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Products</label>

                                <!-- SELECT ALL -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllProducts">
                                    <label class="form-check-label fw-semibold">Select All</label>
                                </div>

                                <div class="row">
                                    <?php while ($productdetailrow = $productTypeResult->fetch_assoc()) { ?>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input product-checkbox" type="checkbox"
                                                    name="product_type[]"
                                                    value="<?php echo $productdetailrow["product_type_id"]; ?>">
                                                <label class="form-check-label">
                                                    <?php echo $productdetailrow["product_type_name"]; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <small class="text-muted">If nothing selected, all products will be included</small>
                            </div>


                            <!-- SIZES -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Sizes</label>

                                <!-- SELECT ALL -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllSizes">
                                    <label class="form-check-label fw-semibold">Select All</label>
                                </div>

                                <div class="row">
                                    <?php while ($sizedetailrow = $sizeResult->fetch_assoc()) { ?>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input size-checkbox" type="checkbox"
                                                    name="size[]"
                                                    value="<?php echo $sizedetailrow["size_id"]; ?>">
                                                <label class="form-check-label">
                                                    <?php echo $sizedetailrow["size_short_name"]; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <small class="text-muted">If nothing selected, all sizes will be included</small>
                            </div>


                            <!-- STATUS -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>

                                <!-- SELECT ALL -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllStatus">
                                    <label class="form-check-label fw-semibold">Select All</label>
                                </div>

                                <!-- ACTIVE -->
                                <div class="form-check">
                                    <input class="form-check-input status-checkbox" type="checkbox"
                                        name="status[]" value="1">
                                    <label class="form-check-label">Active</label>
                                </div>

                                <!-- INACTIVE -->
                                <div class="form-check">
                                    <input class="form-check-input status-checkbox" type="checkbox"
                                        name="status[]" value="0">
                                    <label class="form-check-label">Deactive</label>
                                </div>

                                <small class="text-muted">If nothing selected, all statuses will be included</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Include Size Charts</label>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="include_size_chart" value="1">
                                    <label class="form-check-label">Yes</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="include_size_chart" value="0" checked>
                                    <label class="form-check-label">No</label>
                                </div>
                            </div>


                            <!-- BUTTONS -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-file-earmark-pdf"></i> Generate PDF Report
                                </button>

                                <button type="reset" class="btn btn-secondary">
                                    Reset
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php include_once '../includes/footer_includes.php'; ?>

    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.js"></script>

</body>

<script>
    $('#selectAllProducts').click(function() {
        $('.product-checkbox').prop('checked', this.checked);
    });

    $('.product-checkbox').click(function() {
        if (!this.checked) {
            $('#selectAllProducts').prop('checked', false);
        }
    });
</script>

<script>
    $('#selectAllSizes').click(function() {
        $('.size-checkbox').prop('checked', this.checked);
    });

    $('.size-checkbox').click(function() {
        if (!this.checked) {
            $('#selectAllSizes').prop('checked', false);
        }
    });
</script>

<script>
    $('#selectAllStatus').click(function() {
        $('.status-checkbox').prop('checked', this.checked);
    });

    $('.status-checkbox').click(function() {
        if (!this.checked) {
            $('#selectAllStatus').prop('checked', false);
        }
    });
</script>

</html>