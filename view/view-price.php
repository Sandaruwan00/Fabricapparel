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
    <title>View Price</title>
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
                    View Price
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
                        <div class=" col-md-6 alert alert-success text-center">
                            <?php echo $msg; ?>
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="card shadow-lg border-0 rounded-4">

                                    <!-- Header -->
                                    <div class="card-header bg-dark text-white text-center rounded-top-4 py-4 position-relative">
                                        <h3 class="mb-1 fw-bold">
                                            <?php echo $pricerow["product_type_name"]; ?>
                                        </h3>

                                        <!-- Status Badge -->
                                        <h3>
                                            <?php
                                            if ($pricerow["pricing_status"] == 1) {
                                                $badgeClass = "bg-success";
                                                $badgeText = "Active";
                                            } else {
                                                $badgeClass = "bg-danger";
                                                $badgeText = "Deactive";
                                            }
                                            ?>
                                            <span class="position-absolute top-0 end-0 m-3 badge <?php echo $badgeClass; ?>">
                                                <?php echo $badgeText; ?>
                                            </span>
                                        </h3>
                                    </div>

                                    <!-- Body -->
                                    <div class="card-body p-4">
                                        <div class="row mb-3 align-items-center">

                                            <!-- LEFT SIDE -->
                                            <div class="col-md-6 d-flex flex-column justify-content-center gap-4">

                                                <!-- Size Box -->
                                                <div class="p-4 rounded-4 bg-light shadow-lg text-center">
                                                    <h6 class="text-muted mb-2">Size</h6>
                                                    <h1 class="fw-bold mb-0">
                                                        <?php echo $pricerow["size_short_name"]; ?>
                                                    </h1>
                                                </div>


                                            </div>
                                            <div class="col-md-6 d-flex flex-column justify-content-center gap-4">

                                               

                                                <!-- Price Box -->
                                                <div class="p-4 rounded-4 bg-light shadow-lg text-center">
                                                    <h6 class="text-muted mb-2">Price</h6>
                                                    <h1 class="text-primary fw-bold mb-0">
                                                        LKR <?php echo number_format($pricerow["price"], 2); ?>
                                                    </h1>
                                                </div>

                                            </div>

                                            

                                        </div>
                                        <!-- RIGHT SIDE -->
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <div class="border rounded-4 p-3 shadow-lg bg-white">
                                                        <?php if (!empty($pricerow["size_chart_image"])) { ?>
                                                            <img src="../images/size_charts/<?php echo $pricerow["size_chart_image"]; ?>"
                                                                class="img-fluid rounded-3"
                                                                style="max-height:500px; object-fit:contain;">
                                                        <?php } else { ?>
                                                            <div class="text-muted py-5">
                                                                <i class="bi bi-image" style="font-size:80px;"></i>
                                                                <p>No image available</p>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>

                                    <!-- Footer -->
                                     <?php $price_id = base64_encode($price_id); ?>
                                    <div class="card-footer text-end bg-white border-0 pb-4">


                                        <!-- Optional Edit Button -->
                                        <a href="edit-price.php?price_id=<?php echo $price_id; ?>" class="btn btn-primary px-4 ms-2">
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </a>
                                        <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            onclick="loadPrice('<?php echo $price_id; ?>');">
                                            <i class="bi bi-trash-fill"></i> &nbsp Delete
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once '../includes/footer_includes.php'; ?>
</body>

<!-- delete modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="deleteModalBody">
                Are you sure you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
    function loadPrice(price_id) {
        document.getElementById("confirmDeleteBtn").href =
            "../controller/price_controller.php?status=delete_price&price_id=" + price_id;
    }
</script>






<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>

<script>
    const msg = document.getElementById('msg');
    const delayTime = 3000;
    setTimeout(() => {
        msg.style.display = 'none';
    }, delayTime);
</script>




</html>