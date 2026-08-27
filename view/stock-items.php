<?php
include_once '../commons/session.php';
include_once '../model/stock_model.php';
$userrow = $_SESSION["user"];

$stockObj = new Stock();
$stockItemResult = $stockObj->getAllStockItems();
$stockCategoryResult = $stockObj->getAllStockCategories();
$stockUnitResult = $stockObj->getAllStockUnits();

include_once '../model/permission_model.php';
$permissionObj = new Permission();
if (!$permissionObj->hasPermission($userrow["user_id"], 29)) {
    header("Location: access_denied.php");
    exit();
}

?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php"; ?>
    <title>Manage Materials - Items</title>
</head>

<body>
    <div class="container">
        <?php $pageName = "STOCK MANAGEMENT"; ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <div class="row">
            <div class="col-md-4 text-start">
                <a href="stock.php" class="btn btn-outline-secondary">Back</a>
            </div>

            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="stock-items.php" class="btn btn-outline-primary active">Materials</a>
                    <a href="stock-list.php" class="btn btn-outline-success">Inventory</a>
                    <a href="stock-material-request.php" class="btn btn-outline-info">Stock Requests</a>
                    <a href="stock-purchase-requests.php" class="btn btn-outline-secondary">Purchase Requests</a>
                    <a href="generate-stock-material-items-report.php" class="btn btn-outline-warning">Generate Report</a>
                </div>
            </div>

        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Materials Management</h1>
            </div>
        </div>


        <div class="row">&nbsp;</div>



        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="stock-items.php">Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stock-categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stock-units.php">Units</a>
                    </li>
                </ul>

                <div class="row">&nbsp;</div>

                <div class="row">
                    <div class="col-md-12">

                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                Add New Item
                            </button>
                        </div>
                        <table class="table table-bordered" id="materialtable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Color</th>
                                    <th>Min Stock</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                while ($row = $stockItemResult->fetch_assoc()) { ?>
                                    <tr>
                                        <td width="5%"><?php echo $count; ?></td>
                                        <td width="15%"><?php echo $row['stock_item_name']; ?></td>
                                        <td width="13%"><?php echo $row['stock_category_name']; ?></td>
                                        <td width="10%"><?php echo $row['stock_unit_name']; ?></td>
                                        <td width="10%" title="<?= $row['stock_item_color_code']; ?>" style="background-color: <?php echo $row['stock_item_color_code']; ?>;">
                                            <?php if ($row['stock_item_color_code'] == "") {
                                                echo "N/A";
                                            } ?>
                                        </td>
                                        <td width="12%"><?php echo $row['min_stock_level'] . " " . $row['stock_unit_short_name'];; ?></td>

                                        <?php
                                        if ($row['stock_item_status'] == 1) {
                                            $status = "Active";
                                            $class = "bg-success";
                                        } else {
                                            $status = "De-active";
                                            $class = "bg-danger";
                                        }
                                        ?>
                                        <td width="10%" class="text-center <?php echo $class; ?>"><?php echo $status; ?></td>


                                        <td width="25%">
                                            <a href="#" class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editItemModal"
                                                onclick="loadeditstockitem( '<?php echo $row['stock_item_id']; ?>','<?php echo $row['stock_item_name']; ?>','<?php echo $row['stock_category_id']; ?>','<?php echo $row['stock_unit_id']; ?>','<?php echo $row['stock_item_color_code']; ?>','<?php echo $row['min_stock_level']; ?>');">
                                                <i class="bi bi-pencil-fill"></i> &nbsp;Edit
                                            </a>

                                            <?php
                                            if ($row['stock_item_status'] == 1) {
                                            ?>
                                                <a href="../controller/stock_controller.php?status=deactivate_stock_item&stock_item_id=<?php echo $row['stock_item_id']; ?>" class='btn btn-sm btn-warning'><i class="bi bi-x-lg"></i>&nbsp;
                                                    De-activate</a>
                                            <?php
                                            } else {
                                            ?>
                                                <a href="../controller/stock_controller.php?status=activate_stock_item&stock_item_id=<?php echo $row['stock_item_id']; ?>" class='btn btn-sm btn-success'><i class="bi bi-check-lg"></i>
                                                    &nbsp;
                                                    Activate</a>
                                            <?php
                                            }
                                            ?>

                                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="loadstockitem( '<?php echo $row['stock_item_id']; ?>');">
                                                <i class="bi bi-trash-fill"></i>
                                                &nbsp
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php
                                    $count++;
                                }
                                ?>
                            </tbody>
                        </table>





                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer_includes.php'; ?>


    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="../controller/stock_controller.php?status=add_stock_item" method="POST">

                    <!-- Header -->
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Add New Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">

                        <!-- Item Name -->
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" name="stock_item_name" class="form-control" required>
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="stock_category_id" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                <?php while ($cat = $stockCategoryResult->fetch_assoc()) { ?>
                                    <option value="<?= $cat['stock_category_id']; ?>">
                                        <?= $cat['stock_category_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Unit -->
                        <div class="mb-3">
                            <label class="form-label">Unit</label>
                            <select name="stock_unit_id" class="form-control" required>
                                <option value="">-- Select Unit --</option>
                                <?php while ($unit = $stockUnitResult->fetch_assoc()) { ?>
                                    <option value="<?= $unit['stock_unit_id']; ?>">
                                        <?= $unit['stock_unit_name']; ?> (<?= $unit['stock_unit_short_name']; ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Color -->
                        <div class="mb-3">
                            <label class="form-label">Color (Optional)</label>
                            <input type="color" name="stock_item_color_code" class="form-control form-control-color">
                        </div>

                        <!-- Min Stock -->
                        <div class="mb-3">
                            <label class="form-label">Minimum Stock Level</label>
                            <input type="number" name="min_stock_level" min="1" class="form-control" required>
                        </div>


                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Item</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- edit item modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="../controller/stock_controller.php?status=update_stock_item" method="POST">

                    <!-- Header -->
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Update New Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">

                        <?php



                        $stockCategoryResult = $stockObj->getAllStockCategories();
                        $stockUnitResult = $stockObj->getAllStockUnits();

                        ?>

                        <input type="hidden" name="stock_item_id" id="stock_item_id" value="<?php echo $row['stock_item_id']; ?>">

                        <!-- Item Name -->
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" name="stock_item_name" id="stock_item_name" value="<?php echo $row['stock_item_name']; ?>" class="form-control" required>
                        </div>

                        <!-- Category -->

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="stock_category_id" id="stock_category_id" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                <?php while ($cat = $stockCategoryResult->fetch_assoc()) { ?>
                                    <option value="<?= $cat['stock_category_id']; ?>"
                                        <?php if (isset($row["stock_category_id"]) && $cat["stock_category_id"] == $row["stock_category_id"]) { ?>
                                        selected
                                        <?php } ?>>
                                        <?= $cat['stock_category_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>



                        <!-- Unit -->
                        <div class="mb-3">
                            <label class="form-label">Unit</label>
                            <select name="stock_unit_id" id="stock_unit_id" class="form-control" required>
                                <option value="">-- Select Unit --</option>
                                <?php while ($unit = $stockUnitResult->fetch_assoc()) { ?>
                                    <option value="<?= $unit['stock_unit_id']; ?>">
                                        <?= $unit['stock_unit_name']; ?> (<?= $unit['stock_unit_short_name']; ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Color -->
                        <div class="mb-3">
                            <label class="form-label">Color (Optional)</label>
                            <input type="color" name="stock_item_color_code" id="stock_item_color_code" value="<?php echo $row["stock_item_color_code"]; ?>" class="form-control form-control-color">
                        </div>

                        <!-- Min Stock -->
                        <div class="mb-3">
                            <label class="form-label">Minimum Stock Level</label>
                            <input type="number" name="min_stock_level" id="min_stock_level" min="1" value="<?php echo $row["min_stock_level"]; ?>" class="form-control" required>
                        </div>


                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- delete item modal -->
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
        $(document).ready(function() {
            $("#materialtable").DataTable();
        });
    </script>
    <script>
        const msg = document.getElementById('msg');
        const delayTime = 3000;
        setTimeout(() => {
            msg.style.display = 'none';
        }, delayTime);
    </script>
    <script>
        function loadstockitem(stock_item_id) {
            document.getElementById("confirmDeleteBtn").href =
                "../controller/stock_controller.php?status=delete_stock_item&stock_item_id=" + stock_item_id;
        }
    </script>
    <script>
        function loadeditstockitem(stock_item_id, stock_item_name, stock_category_id, stock_unit_id, stock_item_color_code, min_stock_level) {
            document.getElementById("stock_item_id").value = stock_item_id;
            document.getElementById("stock_item_name").value = stock_item_name;
            document.getElementById("stock_category_id").value = stock_category_id;
            document.getElementById("stock_unit_id").value = stock_unit_id;
            document.getElementById("stock_item_color_code").value = stock_item_color_code;
            document.getElementById("min_stock_level").value = min_stock_level;
        }
    </script>
</body>

<!-- alert start -->
<?php
$msg = "";
if (isset($_GET["msg"])) {
    $msg = base64_decode($_GET["msg"]);
}
?>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="msgToast" class="toast align-items-center text-bg-secondary border-0" role="alert" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body" id="toastMsg">
                <!-- Message -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let msg = "<?php echo $msg; ?>";

        if (msg !== "") {
            document.getElementById("toastMsg").innerText = msg;

            let toastEl = document.getElementById("msgToast");
            let toast = new bootstrap.Toast(toastEl);

            toast.show();
        }
    });
</script>

<!-- alert end -->

</html>