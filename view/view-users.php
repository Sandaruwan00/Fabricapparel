<?php


include_once '../commons/session.php';
include_once '../model/module_model.php';
include_once '../model/user_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$moduleObj = new Module();

$userObj = new User();

$moduleResult = $moduleObj->getAllModules();

$userResult = $userObj->getAllUsers();






?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Users</title>
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
                    View Users
                </h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-user.php" class="btn btn-outline-primary">Add User</a>
                    <a href="view-users.php" class="btn btn-outline-success active">View Users</a>
                    <a href="generate-user-report.php" class="btn btn-outline-warning">Generate User Report</a>
                </div>
            </div>
        </div>

        <div class="row">
            &nbsp;
        </div>

        <div class="row">

            <div class="col-md-12">

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

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover align-middle" id="usertable">
                                <thead class="fs-5 table-secondary text-center">
                                    <tr>
                                        <th>&nbsp</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>&nbsp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($userdetailrow = $userResult->fetch_assoc()) {

                                        $user_id = $userdetailrow["user_id"];
                                        $user_id = base64_encode($user_id);

                                        $img_path = "../images/user_images/";
                                        if ($userdetailrow["user_image"] == "") {
                                            $img_path = $img_path . "user_img.png";
                                        } else {
                                            $img_path = $img_path . $userdetailrow["user_image"];
                                        }

                                        $status = "Active";
                                        if ($userdetailrow["user_status"] == 0) {
                                            $status = "Deactive";
                                        }

                                    ?>
                                        <tr>
                                            <td class="text-center"><img src="<?php echo $img_path ?>" height="60px"></td>
                                            <td>
                                                <?php

                                                echo $userdetailrow["user_fname"] . " " . $userdetailrow["user_lname"];

                                                ?>
                                            </td>
                                            <td>
                                                <?php

                                                echo $userdetailrow["user_email"];

                                                ?>
                                            </td>
                                            <td
                                                <?php
                                                if ($userdetailrow["user_status"] == 1) {
                                                ?>
                                                class="text-center table-success"

                                                <?php
                                                } else if ($userdetailrow["user_status"] == 0) {
                                                ?>
                                                class="text-center table-danger"

                                                <?php
                                                }
                                                ?>> <?php echo $status ?>
                                            </td>
                                            <td>
                                                <span class="d-flex justify-content-between">
                                                    <a href="view-user.php?user_id=<?php echo $user_id; ?>" class="btn btn-primary">
                                                        <i class="bi bi-eye-fill"></i>
                                                        &nbsp
                                                        View
                                                    </a>

                                                    <a href="edit-user.php?user_id=<?php echo $user_id; ?>" class="btn btn-info">
                                                        <i class="bi bi-pencil-fill"></i>
                                                        &nbsp
                                                        Edit
                                                    </a>

                                                    <?php
                                                    if ($userdetailrow["user_status"] == 0) {

                                                    ?>
                                                        <a href="../controller/user_controller.php?status=activate&user_id=<?php echo $user_id; ?>" class="btn btn-success">
                                                            <i class="bi bi-check-lg"></i>
                                                            &nbsp
                                                            Activate
                                                        </a>
                                                    <?php
                                                    }

                                                    ?>


                                                    <?php
                                                    if ($userdetailrow["user_status"] == 1) {

                                                    ?>
                                                        <a href="../controller/user_controller.php?status=deactivate&user_id=<?php echo $user_id; ?>" class="btn btn-warning">
                                                            <i class="bi bi-x-lg"></i>
                                                            &nbsp
                                                            De-activate
                                                        </a>
                                                    <?php
                                                    }

                                                    ?>

                                                    <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="loaduser( '<?php echo $userdetailrow['user_id']; ?>','<?php echo htmlspecialchars($userdetailrow['user_fname'].' '. $userdetailrow['user_lname'], ENT_QUOTES); ?>');">
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


<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>





<script>
    const msg = document.getElementById('msg');

    const delayTime = 3000;

    setTimeout(() => {
        msg.style.display = 'none';
    }, delayTime);
</script>

<script>
    $(document).ready(function() {
        $("#usertable").DataTable();
    });

    function loaduser(user_id) {
        // alert(user_id);

        var role_id = $("#user_role").val();
        var url = "../controller/user_controller.php?status=load_users";

        $.post(url, {
            user_id: user_id
        }, function(data) {
            $("#display_data").html(data).show();
        });
    }
</script>

<script>
    function loaduser(user_id, username) {
        document.getElementById("showUsername").innerText = username;

        document.getElementById("confirmDeleteBtn").href =
            "../controller/user_controller.php?status=delete&user_id=<?php echo $user_id; ?>"
    }
</script>



</html>