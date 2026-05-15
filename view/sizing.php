<?php
include_once '../commons/session.php';
include '../model/price_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$priceObj = new Price();
$sizeResult = $priceObj->getAllSizes();
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Sizing</title>
</head>

<body style="border-radius:10px;">

    <div class="container">

        <?php $pageName = "PRICE MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <div class="row">

            <div class="col-md-4 text-start">
                <a href="price.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>

            <div class="col-md-4 text-center">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Sizing
                </h1>
            </div>

            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <a href="product-type.php" class="btn btn-outline-primary">Product Types</a>
                    <a href="sizing.php" class="btn btn-outline-success active">Sizing</a>
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
                        <div class="col-md-6 alert alert-success text-center">
                            <?php echo $msg; ?>
                        </div>
                    </div>

                <?php } ?>

                <div class="card">

                    <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">

                        Size List

                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSizeModal">
                            <i class="bi bi-plus-lg"></i>
                            Add Size
                        </button>

                    </div>


                    <div class="card-body">

                        <div class="row justify-content-center">
                            <div class="col-md-10">

                                <div class="table-responsive">

                                    <table class="table table-bordered table-hover align-middle text-center" id="sizetable">

                                        <thead class="table-secondary align-middle">

                                            <tr>
                                                <th width="10%">SID</th>
                                                <th width="20%">Size</th>
                                                <th width="10%">Short Form</th>
                                                <th width="20%">Status</th>
                                                <th width="40%">Action</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php
                                            while ($sizedetailrow = $sizeResult->fetch_assoc()) {

                                                $size_id = $sizedetailrow["size_id"];
                                                $size_id = base64_encode($size_id);

                                                $status = "Active";
                                                if ($sizedetailrow["size_status"] == 0) {
                                                    $status = "Deactive";
                                                }
                                            ?>

                                                <tr>

                                                    <td>
                                                        <?php echo "SID" . $sizedetailrow["size_id"]; ?>
                                                    </td>

                                                    <td>
                                                        <?php echo $sizedetailrow["size_name"]; ?>
                                                    </td>

                                                    <td>
                                                        <?php echo $sizedetailrow["size_short_name"]; ?>
                                                    </td>

                                                    <td
                                                        <?php if ($sizedetailrow["size_status"] == 1) { ?>
                                                        class="table-success"
                                                        <?php } else { ?>
                                                        class="table-danger"
                                                        <?php } ?>>
                                                        <?php echo $status; ?>
                                                    </td>

                                                    <td>

                                                        <a href="#" class="btn btn-info"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editSizeModal"
                                                            onclick="loadEditSize('<?php echo $sizedetailrow['size_id']; ?>','<?php echo htmlspecialchars($sizedetailrow['size_name']); ?>','<?php echo $sizedetailrow['size_short_name']; ?>');">

                                                            <i class="bi bi-pencil-fill"></i> Edit
                                                        </a>

                                                        <?php if ($sizedetailrow["size_status"] == 0) { ?>

                                                            <a href="../controller/price_controller.php?status=activate_size&size_id=<?php echo $size_id; ?>" class="btn btn-success">
                                                                <i class="bi bi-check-lg"></i> Activate
                                                            </a>

                                                        <?php } ?>

                                                        <?php if ($sizedetailrow["size_status"] == 1) { ?>

                                                            <a href="../controller/price_controller.php?status=deactivate_size&size_id=<?php echo $size_id; ?>" class="btn btn-warning">
                                                                <i class="bi bi-x-lg"></i> Deactivate
                                                            </a>

                                                        <?php } ?>

                                                        <a href="#" class="btn btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal"
                                                            onclick="loadsize('<?php echo $size_id; ?>','<?php echo htmlspecialchars($sizedetailrow['size_name']); ?>');">

                                                            <i class="bi bi-trash-fill"></i> Delete
                                                        </a>

                                                    </td>

                                                </tr>

                                            <?php } ?>

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

    <?php include_once '../includes/footer_includes.php'; ?>

</body>

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

<script>
    function loadsize(size_id, size_name) {
        document.getElementById("showUsername").innerText = size_name;
        document.getElementById("confirmDeleteBtn").href ="../controller/price_controller.php?status=delete_size&size_id=<?php echo $size_id; ?>"
    }

</script>


<!-- ADD SIZE MODAL -->

<div class="modal fade" id="addSizeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Add Size</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="../controller/price_controller.php?status=add_size" method="post">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Size</label>
                        <input type="text" name="size_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Size Short Form</label>
                        <input type="text" name="size_short_name" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Size</button>
                </div>

            </form>

        </div>
    </div>

</div>




<!-- EDIT SIZE MODAL -->
 <div class="modal fade" id="editSizeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Edit Size</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../controller/price_controller.php?status=update_size" method="post">
                <div class="modal-body">
                    <input type="hidden" name="size_id" id="size_id">
                    <div class="mb-3">
                        <label class="form-label">Size</label>
                         <input type="text" value="<?php echo $sizedetailrow["size_name"]; ?>" name="size_name" id="size_name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Size Short Form</label>
                        <input type="text" value="<?php echo $sizedetailrow["size_short_name"]; ?>" name="size_short_name" id="size_short_name" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Update Size</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    function loadEditSize(id, name, short) {

        document.getElementById("size_id").value = id;
        document.getElementById("size_name").value = name;
        document.getElementById("size_short_name").value = short;

    }
</script>



<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
    $(document).ready(function() {
        $("#sizetable").DataTable();
    });
</script>





<script>
    const msg = document.getElementById('msg');

    setTimeout(() => {
        if (msg) msg.style.display = 'none';
    }, 3000);
</script>

</html>