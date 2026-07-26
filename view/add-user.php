<?php

include_once '../commons/session.php';
include_once '../model/user_model.php';


//get user information from session
$userrow = $_SESSION["user"];

$userObj = new User();

$roleResult = $userObj->getAllRoles();

include_once '../model/permission_model.php';
$permissionObj = new Permission();
if (!$permissionObj->hasPermission($userrow["user_id"], 1)) {
    header("Location: access_denied.php");
    exit();
}

?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Add User</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "USER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>


        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="user.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4" style="text-align:center;">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Add New User
                </h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-user.php" class="btn btn-outline-primary active">Add User</a>
                    <a href="view-users.php" class="btn btn-outline-success">View Users</a>
                    <a href="generate-user-report.php" class="btn btn-outline-warning">Generate User Report</a>
                </div>
            </div>
        </div>

        <div class="row">
            &nbsp;
        </div>

        <div class="row justify-content-center" style="margin-top:25px;">
            <div id="msg" class="col-md-4 text-center">
                <?php if (isset($_GET["msg"])) { ?>
                    <div class="alert alert-danger text-center">
                        <?php echo base64_decode($_GET["msg"]); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="row justify-content-center">


            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">

                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white fw-semibold">
                                User Information
                            </div>
                            <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">
                                <form action="../controller/user_controller.php?status=add_user" method="post" enctype="multipart/form-data">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">First Name</label>
                                                <div class="input-group">
                                                    <input type="text" id="fname" name="fname" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Last Name</label>
                                                <div class="input-group">
                                                    <input type="text" id="lname" name="lname" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Date of Birth</label>
                                                <div class="input-group">
                                                    <input type="date" id="dob" name="dob" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">NIC</label>
                                                <div class="input-group">
                                                    <input type="text" id="nic" name="nic" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <div class="input-group">
                                                    <input type="email" id="email" name="email" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Profile Photo</label>
                                                <input type="file" class="form-control" name="user_image" id="user_image" onchange="displayImage(this);">
                                                <br>
                                                <img id="img_prev" style="" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Contact Mobile</label>
                                                <div class="input-group">
                                                    <input type="text" id="cno1" name="cno1" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Contact Fixed</label>
                                                <div class="input-group">
                                                    <input type="text" id="cno2" name="cno2" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Role</label>
                                                <select name="user_role" id="user_role" class="form-select">
                                                    <option value="">--------</option>
                                                    <?php
                                                    while ($roleRow = $roleResult->fetch_assoc()) {
                                                    ?>
                                                        <option value="<?php echo $roleRow["role_id"]; ?>">
                                                            <?php echo $roleRow["role_name"]; ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        &nbsp;
                                    </div>
                                    <div class="row">
                                        <div id="display_functions">
                                        </div>
                                    </div>
                                    <div class="row">
                                        &nbsp;
                                    </div>
                                    <div class="row d-flex justify-content-center align-items-center">
                                        <div class="col-md-3">
                                            <input type="submit" id="submit" name="submit" class="btn btn-success w-100" value="Submit" />
                                        </div>
                                        <div class="col-md-3">
                                            <input type="reset" id="reset" name="reset" class="btn btn-danger w-100" value="Reset" />
                                        </div>
                                    </div>
                                </form>
                            </div>
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
<script src="../js/uservalidation.js"></script>

<script>
    function displayImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $("#img_prev").attr('src', e.target.result).height(60);

            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>


</html>


<!-- rounded" style="background: linear-gradient(180deg, rgba(0,141,196,0.5) 0%, rgba(255,204,133,0.5) 100%); height:450px; -->