<?php
include_once '../commons/session.php';
include_once '../model/module_model.php';
include_once '../model/user_model.php';


// get user information from session
$userrow = $_SESSION["user"];

$moduleObj = new Module();
$moduleResult = $moduleObj->getRoleModules($userrow["user_role"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php include_once "../includes/bootstrap_css_includes.php"; ?>
</head>
<body class="bg-light">

    <div class="container">
        <?php $pageName = "DASHBOARD"; ?>
        <?php include_once "../includes/header_row_includes.php"; ?>

        <div class="row g-4 mt-1">
            <?php while ($module_row = $moduleResult->fetch_assoc()) { ?> 
                <div class="col-md-3">
                    <a href="<?php echo $module_row['module_url']; ?>" class="text-decoration-none text-dark">
                        <div class="card h-100 shadow-lg border-0" style="border-radius: 20px; background: linear-gradient(180deg, rgba(0,141,196,1) 0%, rgba(255,204,133,1) 100%);">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                                <div class="height: 100">
                                    <i class="bi <?php echo $module_row['module_icon']; ?>" style="font-size: 70px;"></i>
                                </div>
                                
                                    
                                
                               
                                <h5 class="card-title fw-bold">
                                    <?php echo $module_row['module_name']; ?>
                                </h5>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>    
        </div>
    </div>

    <?php include_once '../includes/footer_includes.php'; ?>

    <script src="../js/jquery-3.7.1.js"></script>
</body>
</html>


<!-- <img src="../images/icons/<?php echo $module_row['module_icon']; ?>" 
                                     alt="<?php echo $module_row['module_name']; ?>" 
                                     height="100" class="mb-3" /> -->
