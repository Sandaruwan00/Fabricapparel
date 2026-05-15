<?php
include_once '../commons/session.php';
include '../model/price_model.php';
//get user information from session
$userrow = $_SESSION["user"];
$priceObj = new Price();
$productTypeResult = $priceObj->getActiveProductTypes();
$sizeResult = $priceObj->getActiveSizes();
$priceResult = $priceObj->getAllPricing();
$priceResult2 = $priceObj->getAllPricing();

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Add Price</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "PRICE MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="price.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4" style="text-align:center;">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Add Price
                </h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="product-type.php" class="btn btn-outline-primary">Product Types</a>
                    <a href="sizing.php" class="btn btn-outline-success">Sizing</a>
                    <a href="price.php" class="btn btn-outline-success">Pricing</a>
                    <a href="generate-price-report.php" class="btn btn-outline-warning">Generate Reports</a>
                </div>
            </div>
        </div>
        <div class="row mt-4 justify-content-center">
            <div class="col-md-10">
                <?php
                if (isset($_GET["msg"])) {
                    $msg = base64_decode($_GET["msg"]);
                ?>
                    <div class="row justify-content-center" id="msg">
                        <div class=" col-md-6 alert alert-danger text-center">
                            <?php echo $msg; ?>
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white fw-semibold">
                                Add Price
                            </div>
                            <form action="../controller/price_controller.php?status=add_price" method="post" enctype="multipart/form-data">
                                <div class="card-body cardgroupstyle">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Select Product</label>
                                            <select name="select_product" id="select_product" class="form-control" required>
                                                <option value="">--Select--</option>

                                                <?php while ($sizerow = $productTypeResult->fetch_assoc()) { ?>
                                                    <option value="<?php echo $sizerow["product_type_id"]; ?>">
                                                        <?php echo $sizerow["product_type_name"]; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Select Size</label>
                                            <select name="select_size" id="select_size" class="form-control" required>
                                                <option value="">--Select--</option>

                                                <?php
                                                $editpriceResult = $priceObj->getPrice($price_id);
                                                while ($sizerow = $sizeResult->fetch_assoc()) { ?>
                                                    <option value="<?php echo $sizerow["size_id"]; ?>">
                                                        <?php echo $sizerow["size_short_name"]; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>

                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Price (LKR)</label>
                                            <input type="number" id="price" name="price" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Size Chart</label>
                                            <input type="file" class="form-control" name="size_chart" id="size_chart" onchange="displayImage(this);">
                                            <br>
                                            <img id="img_prev" style="" />
                                        </div>
                                    </div>

                                    <div class="footer text-end">
                                        <button type="reset" class="btn btn-secondary">
                                            Reset
                                        </button>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Set Price
                                        </button>
                                    </div>


                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once '../includes/footer_includes.php'; ?>
</body>




<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>


<script>
    function displayImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $("#img_prev").attr('src', e.target.result).height(200);

            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</html>