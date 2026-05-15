<?php
include '../commons/session.php';

if (!isset($_GET["status"])) {
?>
    <script>
        window.location = "../view/login.php";
    </script>
    <?php
}

$status = $_GET["status"];

include '../model/user_model.php';
include '../model/login_model.php';
$userObj = new User();
$loginObj = new Login();

switch ($status) {
    case "load_functions":

        $role_id = $_POST["role"];

        $moduleResult = $userObj->getRoleModules($role_id);

    ?>
        <div class="row g-4">
            <?php



            while ($module_row = $moduleResult->fetch_assoc()) {
                $module_id = $module_row["module_id"];
                $functionResult = $userObj->getModuleFunctions($module_id);
            ?>
                <div class="col-md-4">
                    <div class="card" style="border: 2px solid grey; border-radius: 15px;">
                        <div class="card-body">
                            <h5 class="card-title text-center">
                                <?php
                                echo $module_row["module_name"];
                                echo "</br>";
                                ?>
                            </h5>
                            <?php
                            while ($fun_row = $functionResult->fetch_assoc()) {
                            ?>
                                <input type="checkbox" name="fun[]" value="<?php echo $fun_row["function_id"]; ?>" checked />
                                <label for="" class="h6"><?php echo $fun_row["function_name"]; ?></label>
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

        <?php

        break;



    case "add_user":

        $fname  = $_POST["fname"];
        $lname = $_POST["lname"];
        $email = $_POST["email"];
        $dob = $_POST["dob"];
        $nic = $_POST["nic"];
        $cno1  = $_POST["cno1"];
        $cno2  = $_POST["cno2"];
        $user_role  = $_POST["user_role"];

        $user_image = $_FILES["user_image"];

        $user_funcitons = $_POST["fun"];



        try {
            if ($fname == "") {

                throw new Exception("First Name cannot be Empty!!!!");
            }

            ///  uploading image
            $file_name = "";
            if (isset($_FILES["user_image"])) {
                if ($user_image["name"] != "") {
                    $file_name = time() . "_" . $user_image["name"];
                    $path = "../images/user_images/$file_name";
                    move_uploaded_file($user_image["tmp_name"], $path);
                }
            }

            $user_id =  $userObj->addUser($fname, $lname, $email, $dob, $nic, $user_role, $file_name);

            ///  creating a login account
            if ($user_id > 0) {
                $loginObj->addUserLogin($user_id, $email, $nic);

                //add user contact

                $userObj->addUserContact($user_id, $cno1);
                $userObj->addUserContact($user_id, $cno2);

                // add user functions

                foreach ($user_funcitons as $fun_id) {
                    $userObj->addUserFunctions($user_id, $fun_id);
                }

                $msg = "$fname $lname Successfully Added!!!";
                $msg = base64_encode($msg);

        ?>

                <script>
                    window.location = "../view/view-users.php?msg=<?php echo $msg; ?>";
                </script>


            <?php

            }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
            ?>
            <script>
                window.location = "../view/add-user.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        }


        break;

    case "update_user":

        $user_id = $_POST["user_id"];

        $fname  = $_POST["fname"];
        $lname = $_POST["lname"];
        $email = $_POST["email"];
        $dob = $_POST["dob"];
        $nic = $_POST["nic"];
        $cno1  = $_POST["cno1"];
        $cno2  = $_POST["cno2"];
        $user_role  = $_POST["user_role"];


        $user_image = $_FILES["user_image"];

        //missing code

        $user_funcitons = $_POST["fun"];

        try {
            if ($fname == "") {

                throw new Exception("First Name cannot be Empty!!!!");
            }

            $userResult = $userObj->getUser($user_id);
            $userrow = $userResult->fetch_assoc();
            $prev_image = $userrow["user_image"];

            if (isset($_FILES["user_image"])) {
                if ($_FILES["user_image"]["name"] != "") {


                    //upload new image

                    $img = time() . "" . $_FILES["user_image"]["name"];
                    $path = "../images/user_images/";
                    move_uploaded_file($_FILES["user_image"]["tmp_name"], $path . "$img");

                    //remove previous image

                    if (file_exists($path . $prev_image) && $prev_image != "") {
                        unlink($path . $prev_image);
                    }
                } else {
                    $img = $prev_image;
                }
            }

            //update user

            $userObj->updateUser($fname, $lname, $email, $dob, $nic, $user_role, $img, $user_id);

            //update login
            $loginObj->updateLoginUser($user_id, $email, $nic);

            //delete existing contact
            $userObj->removeUserContacts($user_id);

            //insert new contacts
            $userObj->addUserContact($user_id, $cno1);
            $userObj->addUserContact($user_id, $cno2);

            // delete existing functions
            $userObj->removeUserFunctions($user_id);

            // add new functions
            foreach ($user_funcitons as $fun_id) {
                $userObj->addUserFunctions($user_id, $fun_id);
            }

            $msg = "$fname $lname Successfully Updated!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-users.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/edit-user.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        }

        break;

    case "activate":

        $user_id = $_GET["user_id"];
        $user_id = base64_decode($user_id);
        $userObj->activateUser($user_id);
        $msg = "Successfully Activated!!!";
        $msg = base64_encode($msg);
        ?>

        <script>
            window.location = "../view/view-users.php?msg=<?php echo $msg; ?>";
        </script>

    <?php

        break;


    case "deactivate":

        $user_id = $_GET["user_id"];
        $user_id = base64_decode($user_id);
        $userObj->deactivateUser($user_id);
        $msg = "Successfully Deactivated!!!";
        $msg = base64_encode($msg);
    ?>

        <script>
            window.location = "../view/view-users.php?msg=<?php echo $msg; ?>";
        </script>

    <?php




        break;

    case "delete":
        $user_id = $_GET["user_id"];
        $user_id = base64_decode($user_id);
        $userObj->deleteUser($user_id);
        $msg = "Successfully Deleted!!!";
        $msg = base64_encode($msg);
    ?>

        <script>
            window.location = "../view/view-users.php?msg=<?php echo $msg; ?>";
        </script>

<?php




        break;
}