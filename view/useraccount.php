<?php
include_once '../commons/session.php';
include_once '../model/user_model.php';


$userrow = $_SESSION["user"];
$userObj = new User();

$userResult = $userObj->getUser($userrow["user_id"]);
$userdetailrow = $userResult->fetch_assoc();


?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php"; ?>
    <title>My Account</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "MY ACCOUNT"; ?>
        <?php include_once "../includes/header_row_includes.php"; ?>

        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                    Back
                </button>
            </div>
            
        </div>

        <div class="row">
            <div class="col-md-4 text-center mt-5">
                <?php
                $img = $userdetailrow["user_image"];
                if ($img == "") {
                    $img = "user_img.png";
                }
                ?>
                <img src="../images/user_images/<?php echo $img; ?>" alt="user-image" width="50%">
                <div class="row">&nbsp;</div>
                <h3><?php echo $userdetailrow["user_fname"] . " " . $userdetailrow["user_lname"]; ?></h3>
            </div>
            <div class="col-md-5 mt-5">
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-hover align-middle" id="usertable">
                        <thead class="fs-6 table-secondary text-center">
                            <tr>
                                <th style="width: 100%;" class="h5" colspan="2">Profile Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width: 30%; font-weight:1000;">Name</td>
                                <td><?php echo $userdetailrow["user_fname"] . " " . $userdetailrow["user_lname"]; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 30%; font-weight:1000;">Role</td>
                                <td><?php echo $userdetailrow["role_name"]; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 30%; font-weight:1000;">NIC</td>
                                <td><?php echo $userdetailrow["user_nic"]; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 30%; font-weight:1000;">Email</td>
                                <td><?php echo $userdetailrow["user_email"]; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 30%; font-weight:1000;">Date of Birth</td>
                                <td><?php echo $userdetailrow["user_dob"]; ?></td>
                            </tr>
                            <tr>
                                <?php
                                $dob = $userdetailrow["user_dob"];
                                $today = new DateTime();
                                $birthDate = new DateTime($dob);

                                $age = $today->diff($birthDate)->y;


                                ?>
                                <td style="width: 30%; font-weight:1000;">Age</td>
                                <td><?php echo $age ?></td>
                            </tr>
                            <tr>
                                <td style="width: 30%; font-weight:1000;">Status</td>
                                <td
                                    <?php

                                    $status = "Active";
                                    if ($userdetailrow["user_status"] == 0) {
                                        $status = "Deactive";
                                    }


                                    if ($userdetailrow["user_status"] == 1) {
                                    ?>
                                    class="text-center table-success"

                                    <?php
                                    } else if ($userdetailrow["user_status"] == 0) {
                                    ?>
                                    class="text-center table-danger"

                                    <?php
                                    }
                                    ?>> <?php echo $status; ?>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 18px;">
            &nbsp;
        </div>


    </div>

    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <?php include_once '../includes/footer_includes.php'; ?>
</body>

</html>