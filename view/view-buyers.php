<?php
include_once '../commons/session.php';
include_once '../model/buyer_model.php';
//get user information from session
$userrow = $_SESSION["user"];
$buyerObj = new Buyer();
$buyerResult = $buyerObj->getAllBuyers();
?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Buyers</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "BUYER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="buyer.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4" style="text-align:center;">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    View Buyers
                </h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-buyer.php" class="btn btn-outline-primary">Add Buyer</a>
                    <a href="view-buyers.php" class="btn btn-outline-success active">View Buyers</a>
                    <a href="generate-buyer-report.php" class="btn btn-outline-warning">Generate Buyer Reports</a>
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
                                        <th>Company Name</th>
                                        <th>Contact Person</th>
                                        <th>Email</th>
                                        <th>&nbsp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($buyerdetailrow = $buyerResult->fetch_assoc()) {
                                        $company_id = $buyerdetailrow["company_id"];
                                        $company_id = base64_encode($company_id);



                                    ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $buyerdetailrow["company_name"];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $buyerdetailrow["contact_name"];
                                                ?>
                                            </td>
                                            
                                            <td><?php
                                            echo $buyerdetailrow["contact_email"];
                                            ?>
                                            </td>
                                            <td>
                                                <span class="d-flex justify-content-between">
                                                    <a href="view-buyer.php?company_id=<?php echo $company_id; ?>" class="btn btn-info">
                                                        <i class="bi bi-eye-fill"></i>
                                                        &nbsp
                                                        View
                                                    </a>
                                                    <a href="edit-buyer.php?company_id=<?php echo $company_id; ?>" class="btn btn-warning">
                                                        <i class="bi bi-pencil-fill"></i>
                                                        &nbsp
                                                        Edit
                                                    </a>
                                                    <a href="add-order.php?company_id=<?php echo $company_id; ?>" class="btn btn-success">
                                                        <i class="bi bi-box-fill"></i>
                                                        &nbsp
                                                        Place Order
                                                    </a>
                                                    
                                                    <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="loadbuyer( '<?php echo $company_id; ?>','<?php echo htmlspecialchars($buyerdetailrow['company_name'], ENT_QUOTES); ?>');">
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
                Are you sure you want to delete <strong id="showBuyername"></strong>?
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

    function loadbuyer(company_id) {
        // alert(company_id);
        var role_id = $("#user_role").val();
        var url = "../controller/user_controller.php?status=load_buyers";
        $.post(url, {
            company_id: company_id
        }, function(data) {
            $("#display_data").html(data).show();
        });
    }
</script>
<script>
    function loadbuyer(company_id, company_name) {
        document.getElementById("showBuyername").innerText = company_name;
        document.getElementById("confirmDeleteBtn").href =
            "../controller/buyer_controller.php?status=delete&company_id=" + company_id;
    }
</script>

</html>