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
    <title>Pricing</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "PRICE MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4" style="text-align:center;">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Pricing
                </h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="product-type.php" class="btn btn-outline-primary">Product Types</a>
                    <a href="sizing.php" class="btn btn-outline-success">Sizing</a>
                    <a href="price.php" class="btn btn-outline-success active">Pricing</a>
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
                        <div class="card">
                            <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                                Product Type List
                                <a href="add-price.php" class="btn btn-primary">
                                    <i class="bi bi-plus-lg"></i>
                                    Add Price
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover align-middle text-center" id="pricetable">
                                                <thead class="table-secondary text-center">
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="15%">Product Type</th>
                                                        <th width="10%">Size</th>
                                                        <th width="15%">Price</th>
                                                        <th width="10%">Status</th>
                                                        <th width="45%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $x = 0;
                                                    while ($pricedetailrow = $priceResult->fetch_assoc()) {
                                                        $price_id = $pricedetailrow["price_id"];
                                                        $price_id = base64_encode($price_id);
                                                        $x++;
                                                        $status = "Active";
                                                        if ($pricedetailrow["pricing_status"] == 0) {
                                                            $status = "Deactive";
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $x; ?></td>
                                                            <td>
                                                                <?php
                                                                echo $pricedetailrow["product_type_name"];
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo $pricedetailrow["size_short_name"];
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo 'LKR ' . $pricedetailrow["price"];
                                                                ?>
                                                            </td>
                                                            <td
                                                                <?php
                                                                if ($pricedetailrow["pricing_status"] == 1) {
                                                                ?>
                                                                class="text-center table-success"
                                                                <?php
                                                                } else if ($pricedetailrow["pricing_status"] == 0) {
                                                                ?>
                                                                class="text-center table-danger"
                                                                <?php
                                                                }
                                                                ?>> <?php echo $status ?>
                                                            </td>
                                                            <td>
                                                                <span>
                                                                    <a href="view-price.php?price_id=<?php echo $price_id; ?>" class="btn btn-primary">
                                                                        <i class="bi bi-eye-fill"></i>
                                                                        &nbsp
                                                                        View
                                                                    </a>
                                                                    <a href="edit-price.php?price_id=<?php echo $price_id; ?>" class="btn btn-info">
                                                                        <i class="bi bi-pencil-fill"></i>
                                                                        &nbsp
                                                                        Edit
                                                                    </a>

                                                                    <?php
                                                                    if ($pricedetailrow["pricing_status"] == 0) {
                                                                    ?>
                                                                        <a href="../controller/price_controller.php?status=activate_price&price_id=<?php echo $price_id; ?>" class="btn btn-success">
                                                                            <i class="bi bi-check-lg"></i>
                                                                            &nbsp
                                                                            Activate
                                                                        </a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    <?php
                                                                    if ($pricedetailrow["pricing_status"] == 1) {
                                                                    ?>
                                                                        <a href="../controller/price_controller.php?status=deactivate_price&price_id=<?php echo $price_id; ?>" class="btn btn-warning">
                                                                            <i class="bi bi-x-lg"></i>
                                                                            &nbsp
                                                                            De-activate
                                                                        </a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="loadPrice( '<?php echo $price_id; ?>');">
                                                                        <i class="bi bi-trash-fill"></i>
                                                                        &nbsp
                                                                        Delete
                                                                    </a>
                                                                </span>
                                                            </td>
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
            <div class="modal-body">
                Are you sure you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>





<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>
<script>
    function loadPrice(price_id) {
    document.getElementById("confirmDeleteBtn").href =
        "../controller/price_controller.php?status=delete_price&price_id=" + price_id;
}

</script>
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
        $("#pricetable").DataTable();
    });
</script>

</html>