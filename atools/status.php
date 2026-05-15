<?php

include_once '../commons/db_connection.php';

$dbcon = new DbConnection();
$con = $GLOBALS['con'];



// ---------------- UPDATE STATUS ----------------
if (isset($_POST["btn_update"])) {

    $status_id = $_POST["status_id"];
    $new_status_id = $_POST["edit_status_id"];
    $status_name = $_POST["status_name"];
    $description = $_POST["description"];
    $color_code = $_POST["color_code"];
    $is_active = $_POST["is_active"];

    $sql = "UPDATE order_status
            SET
                status_id='$new_status_id',
                status_name='$status_name',
                description='$description',
                color_code='$color_code',
                is_active='$is_active'
            WHERE status_id='$status_id'";

    $con->query($sql);

    header("Location: status.php?msg=updated");
    exit();
}


// ---------------- ADD NEW STATUS ----------------
if (isset($_POST["btn_add"])) {

    $status_id = $_POST["new_status_id"];
    $status_name = $_POST["new_status_name"];
    $description = $_POST["new_description"];
    $color_code = $_POST["new_color_code"];

    $sql = "INSERT INTO order_status
            (status_id, status_name, description, is_active, color_code)
            VALUES
            ('$status_id','$status_name','$description',1,'$color_code')";

    $con->query($sql);

    header("Location: status.php?msg=added");
    exit();
}
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php"; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Manage Order Status</title>
</head>

<body>

<div class="container">

    <div class="row mt-4">
        <div class="col-md-12">

            <h2 class="text-center mb-4">
                Manage Order Status
            </h2>

        </div>
    </div>

    <!-- ADD NEW STATUS -->
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                Add New Status
            </h5>
        </div>

        <div class="card-body">

            <form method="post">

                <div class="row">

                    <div class="col-md-2">
                        <label class="form-label">Status ID</label>
                        <input type="number"
                               name="new_status_id"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status Name</label>
                        <input type="text"
                               name="new_status_name"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <input type="text"
                               name="new_description"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Color</label>
                        <input type="color"
                               name="new_color_code"
                               class="form-control form-control-color"
                               value="#0d6efd">
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit"
                                name="btn_add"
                                class="btn btn-success w-100">
                            Add
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>

    <!-- STATUS TABLE -->
    <div class="card shadow-sm">

        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                Order Status List
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-secondary text-center">
                    <tr>
                        <th>ID</th>
                        <th>Status Name</th>
                        <th>Description</th>
                        <th>Color</th>
                        <th>Preview</th>
                        <th>Active</th>
                        <th width="10%">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php
                    $result = $con->query("SELECT * FROM order_status ORDER BY status_id ASC");

                    while ($row = $result->fetch_assoc()) {
                    ?>

                        <form method="post">

                            <tr>

                                <td class="text-center">

                                    

                                    <input type="hidden"
                                           name="status_id"
                                           value="<?php echo $row['status_id']; ?>">
                                    
                                           <input type="number"
                                           name="edit_status_id"
                                           value="<?php echo $row['status_id']; ?>">

                                </td>

                                <td>
                                    <input type="text"
                                           name="status_name"
                                           class="form-control"
                                           value="<?php echo $row['status_name']; ?>">
                                </td>

                                <td>
                                    <input type="text"
                                           name="description"
                                           class="form-control"
                                           value="<?php echo $row['description']; ?>">
                                </td>

                                <td class="text-center">

                                    <input type="color"
                                           name="color_code"
                                           class="form-control form-control-color mx-auto"
                                           value="<?php echo $row['color_code']; ?>">

                                    <div class="small mt-1">
                                        <?php echo $row['color_code']; ?>
                                    </div>

                                </td>

                                <td class="text-center">

                                    <span class="badge"
                                          style="
                                            background-color: <?php echo $row['color_code']; ?>;
                                            color:white;
                                            padding:10px 15px;
                                            font-size:14px;
                                          ">

                                        <?php echo $row['status_name']; ?>

                                    </span>

                                </td>

                                <td class="text-center">

                                    <select name="is_active"
                                            class="form-select">

                                        <option value="1"
                                            <?php
                                            if ($row['is_active'] == 1) {
                                                echo "selected";
                                            }
                                            ?>>
                                            Active
                                        </option>

                                        <option value="0"
                                            <?php
                                            if ($row['is_active'] == 0) {
                                                echo "selected";
                                            }
                                            ?>>
                                            Inactive
                                        </option>

                                    </select>

                                </td>

                                <td class="text-center">

                                    <button type="submit"
                                            name="btn_update"
                                            class="btn btn-primary btn-sm">

                                        Update

                                    </button>

                                </td>

                            </tr>

                        </form>

                    <?php
                    }
                    ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include_once '../includes/footer_includes.php'; ?>

</body>

<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</html>