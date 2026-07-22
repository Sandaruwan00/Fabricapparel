<?php
include_once '../commons/session.php';
include '../model/price_model.php';
//get user information from session
$userrow = $_SESSION["user"];

$price_id = $_GET['price_id'];
$price_id = base64_decode($_GET["price_id"]);

$priceObj = new Price();
$priceresult = $priceObj->getPrice($price_id);
$pricerow = $priceresult->fetch_assoc();

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Edit Price</title>
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
                    Edit Price
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
                <div class="row justify-content-center" style="margin-top:25px;">
                    <div id="msg" class="col-md-4 text-center">
                        <?php if (isset($_GET["msg"])) { ?>
                            <div class="alert alert-danger text-center">
                                <?php echo base64_decode($_GET["msg"]); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white fw-semibold">
                                Add Price
                            </div>
                            <form id="editprice" action="../controller/price_controller.php?status=update_price" method="post" enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <input type="hidden" name="price_id" value="<?php echo $price_id; ?>">
                                            <input type="hidden" name="select_product" value="<?php echo $pricerow["product_type_id"]; ?>">
                                            <input type="hidden" name="select_size" value="<?php echo $pricerow["size_id"]; ?>">
                                            <label class="form-label">Select Product</label>
                                            <select name="select_product" id="select_product" class="form-control" disabled>
                                                <option value="<?php echo $pricerow["product_type_id"]; ?>" selected>
                                                    <?php echo $pricerow["product_type_name"]; ?>
                                                </option>


                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Select Size</label>
                                            <select name="select_size" id="select_size" class="form-control" disabled>
                                                <option value="<?php echo $pricerow["size_id"]; ?>" selected>
                                                    <?php echo $pricerow["size_short_name"]; ?>
                                                </option>


                                            </select>
                                        </div>

                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Price (LKR)</label>
                                            <input type="number" id="price" name="price" class="form-control" min="1" value="<?php echo $pricerow["price"]; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Size Chart</label>
                                            <input type="file" class="form-control" name="size_chart" id="size_chart" onchange="displayImage(this);">
                                            <br>
                                            <?php
                                            if ($pricerow["size_chart_image"] != "") {
                                                $image = $pricerow["size_chart_image"];


                                            ?>

                                                <img id="img_prev" style="" src="../images/size_charts/<?php echo $image; ?>" height="200px" />

                                            <?php
                                            }

                                            ?>
                                            <img id="img_prev" style=""/>
                                        </div>
                                    </div>

                                    <div class="footer text-end">
                                        
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            Update Price
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
    const msg = document.getElementById('msg');
    const delayTime = 3000;
    setTimeout(() => {
        msg.style.display = 'none';
    }, delayTime);
</script>

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

<script>
    $(document).ready(function() {

        $("#editprice").submit(function() {

            $("#msg").removeClass("alert alert-danger").html("");

            var select_product = $("#select_product").val();
            var select_size = $("#select_size").val();
            var price = $("#price").val().trim();
            var size_chart = $("#size_chart").val();


            if (select_product == "") {
                $("#msg").html("Please Select a Product!");
                $("#msg").addClass("alert alert-danger");
                return false;
            }


            if (select_size == "") {
                $("#msg").html("Please Select a Size!");
                $("#msg").addClass("alert alert-danger");
                return false;
            }


            if (price == "") {
                $("#msg").html("Price Cannot Be Empty!");
                $("#msg").addClass("alert alert-danger");
                return false;
            }


            if (isNaN(price) || parseFloat(price) <= 0) {
                $("#msg").html("Price Must Be Greater Than Zero!");
                $("#msg").addClass("alert alert-danger");
                return false;
            }


            // File validation (if user uploads a file)
            if (size_chart != "") {

                var file = $("#size_chart")[0].files[0];
                var fileExtension = size_chart.split('.').pop().toLowerCase();

                if ($.inArray(fileExtension, ['jpg', 'jpeg', 'png']) == -1) {
                    $("#msg").html("Only JPG, JPEG and PNG Images Are Allowed!");
                    $("#msg").addClass("alert alert-danger");
                    return false;
                }


                if (file.size > 2 * 1024 * 1024) {
                    $("#msg").html("Image Size Must Be Less Than 2MB!");
                    $("#msg").addClass("alert alert-danger");
                    return false;
                }

            }

        });

    });
</script>

</html>