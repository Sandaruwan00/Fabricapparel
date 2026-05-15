<?php
include_once '../commons/session.php';
include '../model/price_model.php';
//get user information from session
$userrow = $_SESSION["user"];
$priceObj = new Price();
$productTypeResult = $priceObj->getAllProductType();
?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Product Types</title>
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
                    Product Type
                </h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="product-type.php" class="btn btn-outline-primary active">Product Types</a>
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
                        <div class="card">
                            <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                                Product Type List
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductTypeModal">
                                    <i class="bi bi-plus-lg"></i>
                                    Add Product Type
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-md-10">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover align-middle text-center" id="producttypetable">
                                                <thead class="table-secondary text-center">
                                                    <tr>
                                                        <th width="10%">ID</th>
                                                        <th width="25%">Product Type</th>
                                                        <th width="15%">Status</th>
                                                        <th width="50%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    while ($productdetailrow = $productTypeResult->fetch_assoc()) {
                                                        $product_type_id = $productdetailrow["product_type_id"];
                                                        $product_type_id = base64_encode($product_type_id);
                                                        $status = "Active";
                                                        if ($productdetailrow["product_type_status"] == 0) {
                                                            $status = "Deactive";
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                echo 'PID' . $productdetailrow["product_type_id"];
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo $productdetailrow["product_type_name"];
                                                                ?>
                                                            </td>
                                                            <td
                                                                <?php
                                                                if ($productdetailrow["product_type_status"] == 1) {
                                                                ?>
                                                                class="text-center table-success"
                                                                <?php
                                                                } else if ($productdetailrow["product_type_status"] == 0) {
                                                                ?>
                                                                class="text-center table-danger"
                                                                <?php
                                                                }
                                                                ?>> <?php echo $status ?>
                                                            </td>
                                                            <td>
                                                                <span>
                                                                    <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#editProductTypeModal" onclick="loadEditProductType('<?php echo $productdetailrow['product_type_id']; ?>','<?php echo htmlspecialchars($productdetailrow['product_type_name']); ?>');">
                                                                        <i class="bi bi-pencil-fill"></i>
                                                                        &nbsp
                                                                        Edit
                                                                    </a>
                                                                    <?php
                                                                    if ($productdetailrow["product_type_status"] == 0) {
                                                                    ?>
                                                                        <a href="../controller/price_controller.php?status=activate_product_type&product_type_id=<?php echo $product_type_id; ?>" class="btn btn-success">
                                                                            <i class="bi bi-check-lg"></i>
                                                                            &nbsp
                                                                            Activate
                                                                        </a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    <?php
                                                                    if ($productdetailrow["product_type_status"] == 1) {
                                                                    ?>
                                                                        <a href="../controller/price_controller.php?status=deactivate_product_type&product_type_id=<?php echo $product_type_id; ?>" class="btn btn-warning">
                                                                            <i class="bi bi-x-lg"></i>
                                                                            &nbsp
                                                                            De-activate
                                                                        </a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="loadproducttype( '<?php echo $productdetailrow['product_type_id']; ?>','<?php echo htmlspecialchars($productdetailrow['product_type_name']); ?>');">
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
                Are you sure you want to delete <strong id="showUsername"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- add product type modal -->
<div class="modal fade" id="addProductTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Add Product Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../controller/price_controller.php?status=add_product_type" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product Type Name</label>
                        <input type="text" name="product_type" class="form-control" placeholder="Enter product type" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" name="submit" class="btn btn-primary">
                        Add Product Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- edit product type modal -->
<div class="modal fade" id="editProductTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Edit Product Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../controller/price_controller.php?status=update_product_type" method="post">
                <div class="modal-body">
                    <input type="hidden" name="product_type_id" id="edit_product_type_id">
                    <div class="mb-3">
                        <label class="form-label">Product Type Name</label>
                        <input type="text" value="<?php echo $productdetailrow["product_type_name"]; ?>" name="product_type_name" id="edit_product_type_name" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Update Product Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
    $(document).ready(function() {
        $("#producttypetable").DataTable();
    });
</script>

<script>
    function loadproducttype(product_type_id, productTypeName) {
        document.getElementById("showUsername").innerText = productTypeName;
        document.getElementById("confirmDeleteBtn").href =
            "../controller/price_controller.php?status=delete_product_type&product_type_id=<?php echo $product_type_id; ?>"
    }

    function loadEditProductType(id, name) {
        document.getElementById("edit_product_type_id").value = id;
        document.getElementById("edit_product_type_name").value = name;
    }
</script>
<script>
    const msg = document.getElementById('msg');
    const delayTime = 3000;
    setTimeout(() => {
        msg.style.display = 'none';
    }, delayTime);
</script>

</html>