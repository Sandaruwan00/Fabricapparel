<div class="row" style="height: 200px;">

    <div class="row mb-3" style="height: 30px;"></div>


    <div class="row" style="height: 100px;">
        <div class="col-md-12 text-center">
            <a href="dashboard.php" style="text-decoration: none;">
                <img src="../images/logo/logo_transparent.png" alt="Logo" width="60px">
            </a>
            
                <label class="h1 fw-bold mt-2 mb-0">Fabric Apparel (PVT) LTD.</label>
            
        </div>
    </div>


    <div class="bg-dark text-white rounded d-flex align-items-center justify-content-between px-4" style="height: 70px;">

        <h1 class="h4 fw-semibold m-0">
            <?php echo $pageName; ?>
        </h1>


        <div class="d-flex align-items-center gap-3 fs-6">

            <a href="dashboard.php" class="text-white text-decoration-none d-flex align-items-center">
                <i class="bi bi-speedometer2 me-2"></i>
                <span>Dashboard</span>
            </a>


            <a href="useraccount.php?user_id=<?php echo $userrow["user_id"]; ?>" class="text-white text-decoration-none d-flex align-items-center">
                <i class="bi bi-person-circle me-2"></i>
                <span><?php echo ucwords($userrow["user_fname"] . " " . $userrow["user_lname"]); ?></span>
            </a>


            <a href="../controller/login_controller.php?status=logout" class="text-white fs-5">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row mt-1">&nbsp;</div>
