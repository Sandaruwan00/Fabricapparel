<?php

include_once '../commons/session.php';
include_once '../model/supplier_model.php';



// get user information from session
$userrow = $_SESSION["user"];

$supplierObj = new Supplier();
$supplierResult = $supplierObj->getAllSuppliers();

?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Suppliers</title>
</head>

<body style="border-radius:10px;">
    <div class="container">

        <?php $pageName = "PURCHASING MANAGEMENT"; ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <div class="row">
            <div class="col-md-4 text-start">
                <a href="purchasing.php" class="btn btn-outline-secondary">Back</a>
            </div>

            <div class="col-md-8 text-end">
                <div class="btn-group">
                    <a href="supplier.php" class="btn btn-outline-primary active">
                        Suppliers
                    </a>
                    <a href="purchase-requests.php" class="btn btn-outline-info">
                        Purchase Requests
                    </a>
                    <a href="rfq.php" class="btn btn-outline-secondary">
                        RFQ / Quotations
                    </a>
                    <a href="purchase-orders.php" class="btn btn-outline-success">
                        Purchase Orders
                    </a>
                    <a href="generate-purchase-reports.php" class="btn btn-outline-warning">
                        Reports
                    </a>
                </div>
            </div>
        </div>

        <div class="row">&nbsp;</div>




        

        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                        Add Supplier
                    </button>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Suppliers
                </h1>
            </div>
        </div>



        <div class="row justify-content-center">

            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle" id="suppliertable">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">Supplier Name</th>
                                <th width="20%">Contact Person</th>
                                <th width="30%">Email</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = $supplierResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $row["supplier_id"]; ?></td>
                                    <td><?php echo $row["supplier_name"]; ?></td>
                                    <td><?php echo $row["supplier_contact_person"]; ?></td>
                                    <td><?php echo $row["supplier_email"]; ?></td>
                                    <td>
                                        <a href="#" class="btn btn-success btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewSupplierModal"
                                            onclick="loadViewSupplier(
                                            '<?php echo $row['supplier_name']; ?>',
                                            '<?php echo $row['supplier_contact_person']; ?>',
                                            '<?php echo $row['supplier_nic']; ?>',
                                            '<?php echo $row['supplier_phone']; ?>',
                                            '<?php echo $row['supplier_email']; ?>',
                                            '<?php echo htmlspecialchars($row['supplier_address']); ?>'
                                            );">

                                            <i class="bi bi-eye-fill"></i> View
                                        </a>
                                        <a href="#" class="btn btn-info btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editSupplierModal"
                                            onclick="loadEditSupplier(
                                                '<?php echo $row['supplier_id']; ?>',
                                                '<?php echo htmlspecialchars($row['supplier_name']); ?>',
                                                '<?php echo htmlspecialchars($row['supplier_contact_person']); ?>',
                                                '<?php echo $row['supplier_nic']; ?>',
                                                '<?php echo $row['supplier_phone']; ?>',
                                                '<?php echo $row['supplier_email']; ?>',
                                                '<?php echo htmlspecialchars($row['supplier_address']); ?>'
                                                );">

                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </a>
                                        <a href="#" class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            onclick="loadSupplier(
                                            '<?php echo $row['supplier_id']; ?>',
                                            '<?php echo htmlspecialchars($row['supplier_name']); ?>'
                                            );">

                                            <i class="bi bi-trash-fill"></i> Delete
                                        </a>
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

    <!-- view modal -->
    <div class="modal fade" id="viewSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">

            <!-- Header -->
            <div class="modal-header"
                style="background: linear-gradient(90deg, #36D1DC, #5B86E5);">
                <h5 class="modal-title">
                    <i class="bi bi-building"></i> Supplier Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4"
                style="background: #f8f9fa;">

                <div class="card border-0 shadow-lg p-4">

                    <div class="text-center">
                        <h4 id="view_supplier_name" class="fw-bold text-primary"></h4>
                        <h5 id="view_supplier_contact" class="fw-bold"></h5>
                        <h6 id="view_supplier_contact_nic" class="fw-bold" hidden></h6>
                    </div>

                    <hr>

                    <div class="row g-4">

                       <div class="col-md-6">
                            <div class="p-3 rounded bg-light shadow-lg">
                                <label class="text-muted small">Phone</label>
                                <h6 id="view_supplier_phone" class="fw-semibold mb-0"></h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded bg-light shadow-lg">
                                <label class="text-muted small">Email</label>
                                <h6 id="view_supplier_email" class="fw-semibold mb-0 text-break"></h6>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="p-3 rounded bg-light shadow-lg">
                                <label class="text-muted small">Address</label>
                                <h6 id="view_supplier_address" class="fw-semibold mb-0"></h6>
                            </div>
                        </div>
                         

                    </div>

                </div>

            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

    <script>
        function loadViewSupplier(name, contact, nic, phone, email, address) {

            document.getElementById("view_supplier_name").innerText = name;
            document.getElementById("view_supplier_contact").innerText = contact;
            document.getElementById("view_supplier_contact_nic").innerText = nic;
            document.getElementById("view_supplier_phone").innerText = phone;
            document.getElementById("view_supplier_email").innerText = email;
            document.getElementById("view_supplier_address").innerText = address;

        }
    </script>

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Add Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../controller/supplier_controller.php?status=add_supplier" method="post">

                    <div class="modal-body"
                        style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">


                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Supplier Name</label>
                                <input type="text" name="supplier_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="supplier_contact_person" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Person NIC</label>
                                <input type="text" name="supplier_contact_person_nic" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="supplier_phone" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="supplier_email" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="supplier_address" class="form-control" rows="1" required></textarea>
                            </div>
                        </div>







                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            Add Supplier
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <!-- update supplier modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/supplier_controller.php?status=update_supplier" method="post">

                    <div class="modal-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">

                        <input type="hidden" name="supplier_id" id="supplier_id">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Supplier Name</label>
                                <input type="text" name="supplier_name" id="supplier_name" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Contact Person</label>
                                <input type="text" name="supplier_contact_person" id="supplier_contact_person" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Contact Person NIC</label>
                                <input type="text" name="supplier_contact_person_nic" id="supplier_contact_person_nic" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Phone</label>
                                <input type="text" name="supplier_phone" id="supplier_phone" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="supplier_email" id="supplier_email" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3">
                                <label>Address</label>
                                <textarea name="supplier_address" id="supplier_address" rows="1" class="form-control"></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            Update Supplier
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- delete modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="showSupplier"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadSupplier(supplier_id, supplier_name) {
            document.getElementById("showSupplier").innerText = supplier_name;
            document.getElementById("confirmDeleteBtn").href = "../controller/supplier_controller.php?status=delete_supplier&supplier_id=" + supplier_id;
        }
    </script>

    <?php include_once '../includes/footer_includes.php'; ?>

    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.js"></script>
    <script src="../js/datatable/bootstrap.bundle.min.js"></script>
    <script src="../js/datatable/dataTables.bootstrap5.js"></script>
    <script src="../js/datatable/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $("#suppliertable").DataTable();
        });
    </script>

    <script>
        function loadEditSupplier(id, name, contact, nic, phone, email, address) {

            document.getElementById("supplier_id").value = id;
            document.getElementById("supplier_name").value = name;
            document.getElementById("supplier_contact_person").value = contact;
            document.getElementById("supplier_contact_person_nic").value = nic;
            document.getElementById("supplier_phone").value = phone;
            document.getElementById("supplier_email").value = email;
            document.getElementById("supplier_address").value = address;

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