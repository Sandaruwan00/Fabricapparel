<?php
include_once '../commons/session.php';
include_once '../model/module_model.php';
include_once '../model/user_model.php';

if (!isset($_GET["user_id"])) {
?>
    <script>
        window.location = "login.php";
    </script>
<?php
}

// to get the information from the session
$userrow = $_SESSION["user"];

$userObj = new User();
$user_id = $_GET["user_id"];
$user_id = base64_decode($_GET["user_id"]);
$userResult = $userObj->getUser($user_id);
$userdetailrow = $userResult->fetch_assoc();

$usercontactResult = $userObj->getUserContact($user_id);
$contactrow1 = $usercontactResult->fetch_assoc();
$contactrow2 = $usercontactResult->fetch_assoc();


$moduleObj = new Module();
$userObj = new User();

$moduleResult = $moduleObj->getAllModules();
$userResult = $userObj->getAllUsers();

$functionArray = array();
$userfunctionResult = $userObj->getUserFunctions($user_id);
while ($fun_row = $userfunctionResult->fetch_assoc()) {
    array_push($functionArray, $fun_row["fun_id"]);
}

//print_r($moduleResult);
// print_r($userrow);
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View User</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "USER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>


        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="view-users.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4" style="text-align:center;">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    <?php echo $userdetailrow["user_fname"] . " " . $userdetailrow["user_lname"]; ?>
                </h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-user.php" class="btn btn-outline-primary">Add User</a>
                    <a href="view-users.php" class="btn btn-outline-success">View Users</a>
                    <a href="generate-single-user-report.php?user_id=<?php echo $_GET["user_id"]; ?>" class="btn btn-outline-warning">Generate User Report</a>
                </div>
            </div>
        </div>

        <div class="row">
            &nbsp;
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="row text-center">
                    <div class="col-md-12 mb-3">
                        <?php
                        $img = $userdetailrow["user_image"];
                        if ($img == "") {
                            $img = "user_img.png";
                        }
                        ?>
                        <img src="../images/user_images/<?php echo $img; ?>" width="150px">
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="usertable">
                                <tbody class="fs-6">
                                    <tr>
                                        <th style="width: 25%;">Name</th>
                                        <td style="width: 75%;">
                                            <?php echo $userdetailrow["user_fname"] . " " . $userdetailrow["user_lname"]; ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <th>NIC</th>
                                        <td>
                                            <?php echo $userdetailrow["user_nic"]; ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>
                                            <?php echo $userdetailrow["user_dob"]; ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>
                                            <?php echo $userdetailrow["user_email"]; ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <th>Contact Mobile</th>
                                        <td>
                                            <?php echo $contactrow1["contact_number"]; ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <th>Contact Fixed</th>
                                        <td>
                                            <?php echo $contactrow2["contact_number"]; ?>
                                        </td>

                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php $user_id = base64_encode($user_id); ?>
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <a href="edit-user.php?user_id=<?php echo $user_id; ?>" class="btn btn-success w-100">
                            <i class="bi bi-pencil-fill"></i>
                            &nbsp
                            Edit
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="loaduser( '<?php echo $userdetailrow['user_id']; ?>','<?php echo htmlspecialchars($userdetailrow['user_fname'].' '. $userdetailrow['user_lname'], ENT_QUOTES); ?>');">
                                                        <i class="bi bi-trash-fill"></i>
                                                        &nbsp
                                                        Delete
                                                    </a>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="">
                            <table class="table table-bordered align-middle" id="usertable">
                                <tbody class="fs-6">
                                    <tr>
                                        <th class="text-center fs-4">
                                            <?php echo $userdetailrow["role_name"]; ?>
                                        </th>

                                    </tr>
                                    <tr>
                                        <td>
                                            <div id="display_functions">
                                                <?php
                                                $role_id = $userrow["user_role"];

                                                $moduleResult = $userObj->getRoleModules($role_id);

                                                ?>
                                                <div class="row g-4">
                                                    <?php



                                                    while ($module_row = $moduleResult->fetch_assoc()) {
                                                        $module_id = $module_row["module_id"];
                                                        $functionResult = $userObj->getModuleFunctions($module_id);
                                                    ?>
                                                        <div class="col-md-6">
                                                            <div class="card" style="border: 2px solid grey; border-radius: 15px; height: 100%;">
                                                                <div class="card-body">
                                                                    <h6 class="card-title text-center">
                                                                        <?php
                                                                        echo $module_row["module_name"];
                                                                        echo "</br>";
                                                                        ?>
                                                                    </h6>
                                                                    <?php
                                                                    while ($fun_row = $functionResult->fetch_assoc()) {
                                                                    ?>
                                                                        <input type="checkbox" name="fun[]" value="<?php echo $fun_row["function_id"]; ?>" onclick="return false;" readonly="readonly"
                                                                            <?php
                                                                            if (in_array($fun_row["function_id"], $functionArray)) {
                                                                            ?>

                                                                            checked


                                                                            <?php
                                                                            }
                                                                            ?> />
                                                                        <label for=""><?php echo $fun_row["function_name"]; ?></label>
                                                                        <br />
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </td>

                                    </tr>
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
<script src="../js/uservalidation.js"></script>


<script>
    function loaduser(user_id, username) {
        document.getElementById("showUsername").innerText = username;

        document.getElementById("confirmDeleteBtn").href =
            "../controller/user_controller.php?status=delete&user_id=<?php echo $user_id; ?>"
    }
</script>


</html>


<!-- rounded" style="background: linear-gradient(180deg, rgba(0,141,196,0.5) 0%, rgba(255,204,133,0.5) 100%); height:450px; -->