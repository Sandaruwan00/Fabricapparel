<?php
include_once '../commons/session.php';
include_once '../model/stock_model.php';
$userrow = $_SESSION["user"];
$stockObj = new Stock();
$stockItemResult = $stockObj->getAllStockItems();
$stockCategoryResult = $stockObj->getAllStockCategories();
$stockUnitResult = $stockObj->getAllStockUnits();
?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php"; ?>
    <title>Manage Materials - Categories</title>
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
                    <a href="generate-stock-material-categories-report.php" class="btn btn-outline-warning">Generate Reports</a>
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
                        <a class="nav-link" aria-current="page" href="stock-items.php">Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="stock-categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stock-units.php">Units</a>
                    </li>
                </ul>
                <div class="row">&nbsp;</div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                Add New Category
                            </button>
                        </div>
                        <table class="table table-bordered table-striped" id="categorytable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                while ($row = $stockCategoryResult->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $row['stock_category_name']; ?></td>
                                        <td><?php echo $row['stock_category_description']; ?></td>
                                        <?php
                                        if ($row['stock_category_status'] == 1) {
                                            $status = "Active";
                                            $class = "bg-success";
                                        } else {
                                            $status = "De-active";
                                            $class = "bg-danger";
                                        }
                                        ?>
                                        <td class="text-center <?php echo $class; ?>"><?php echo $status; ?></td>
                                        <td>

                                        <a href="#" class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCategoryModal"
                                                onclick="loadeditstockcategory( '<?php echo $row['stock_category_id']; ?>','<?php echo $row['stock_category_name']; ?>','<?php echo $row['stock_category_description']; ?>');">
                                                <i class="bi bi-pencil-fill"></i> &nbsp;Edit
                                            </a>

                                            <?php
                                            if ($row['stock_category_status'] == 1) {
                                            ?>
                                                <a href="../controller/stock_controller.php?status=deactivate_stock_category&stock_category_id=<?php echo $row['stock_category_id']; ?>" class='btn btn-sm btn-warning'><i class="bi bi-x-lg"></i>&nbsp;
                                                    De-activate</a>
                                            <?php
                                            } else {
                                            ?>
                                                <a href="../controller/stock_controller.php?status=activate_stock_category&stock_category_id=<?php echo $row['stock_category_id']; ?>" class='btn btn-sm btn-success'><i class="bi bi-check-lg"></i>
                                                    &nbsp;
                                                    Activate</a>
                                            <?php
                                            }
                                            ?>
                                            

                                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="loadstockcategory( '<?php echo $row['stock_category_id']; ?>');">
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


    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="../controller/stock_controller.php?status=add_stock_category" method="POST">

                    <!-- Header -->
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Add New Category</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">

                        <!-- Category Name -->
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="stock_category_name" class="form-control" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="stock_category_description" class="form-control" rows="3"></textarea>
                        </div>

                       

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Update Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="../controller/stock_controller.php?status=update_stock_category" method="POST">

                    <!-- Header -->
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Update Category</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">

                    <input type="hidden" name="stock_category_id" id="stock_category_id" value="<?php echo $row['stock_category_id']; ?>">

                        <!-- Category Name -->
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="stock_category_name" id="stock_category_name" class="form-control" value="<?php echo $row['stock_category_name']; ?>" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="stock_category_description" id="stock_category_description" class="form-control" rows="3" value="<?php echo $row['stock_category_description']; ?>"></textarea>
                        </div>

                       

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

      <!-- delete unit modal -->
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


    <?php include_once '../includes/footer_includes.php'; ?>
    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.js"></script>
    <script src="../js/datatable/bootstrap.bundle.min.js"></script>
    <script src="../js/datatable/dataTables.bootstrap5.js"></script>
    <script src="../js/datatable/dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $("#categorytable").DataTable();
        });
    </script>
    
    <script>
        function loadstockcategory(stock_category_id) {
            document.getElementById("confirmDeleteBtn").href =
                "../controller/stock_controller.php?status=delete_stock_category&stock_category_id=" + stock_category_id;
        }
    </script>
    <script>
        function loadeditstockcategory(id, name, description) {
            document.getElementById("stock_category_id").value = id;
            document.getElementById("stock_category_name").value = name;
            document.getElementById("stock_category_description").value = description;
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
    document.addEventListener("DOMContentLoaded", function () {
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