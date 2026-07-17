<?php
include_once '../commons/session.php';
include_once '../model/buyer_model.php';
//get user information from session
$userrow = $_SESSION["user"];
$buyerObj = new Buyer();

$company_id = $_GET["company_id"];
$company_id = base64_decode($_GET["company_id"]);

$buyerCompanyResult = $buyerObj->getBuyerCompany($company_id);
$buyerdetailrow = $buyerCompanyResult->fetch_assoc();

$businessTypeResult = $buyerObj->viewBusinessType($company_id);
$businesstypedetailrow = $businessTypeResult->fetch_assoc();

$orderHistoryResult = $buyerObj->getOrderHistory($company_id);

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Buyer</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "BUYER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="view-buyers.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4" style="text-align:center;">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    View Buyer
                </h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-buyer.php" class="btn btn-outline-primary">Add Buyer</a>
                    <a href="view-buyers.php" class="btn btn-outline-success">View Buyers</a>
                    <a href="generate-single-buyer-report.php?company_id=<?php echo $company_id; ?>" class="btn btn-outline-warning">Generate Buyer Reports</a>
                </div>
            </div>
        </div>
        <div class="row">
            &nbsp;
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card" style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
                    <div class="card-header bg-primary" style="color:white">
                        <h3 class="fw-bold"><?php echo $buyerdetailrow["company_name"]; ?></h3>
                        <h5>Reg: <?php echo $buyerdetailrow["company_registration"]; ?></h5>
                    </div>
                    <div class="card-body" style="margin-left: 20px; margin-right:20px;">

                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <h4 class="card-title fw-bold"><i class="bi bi-info-circle"></i> &nbsp;Company Information</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="fw-bold m-auto">BUSINESS TYPE:</p>
                                        <p class="fs-4"><?php echo $businesstypedetailrow["business_type_name"]; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="fw-bold m-auto">WEBSITE:</p>
                                        <p class="fs-5"><a href="<?php echo $buyerdetailrow["website"]; ?>" target="_blank"><?php echo $buyerdetailrow["company_name"]; ?></a></p>
                                    </div>
                                </div>
                                <div class="row">
                                    &nbsp;
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="fw-bold m-auto">ADDRESS:</p>
                                        <p class="fs-5 m-auto"><?php echo $buyerdetailrow["company_address_line_1"] . ","; ?></p>
                                        <p class="fs-5 m-auto"><?php echo $buyerdetailrow["company_address_line_2"] . ","; ?></p>
                                        <p class="fs-5 m-auto"><?php echo $buyerdetailrow["company_city"]; ?></p>
                                        <p class="fs-5 m-auto"><?php echo $buyerdetailrow["company_postal_code"]; ?></p>
                                        <p class="fs-5 m-auto"><?php echo $buyerdetailrow["company_country"]; ?></p>
                                    </div>

                                </div>
                                <div class="row">
                                    &nbsp;
                                </div>
                                <h4 class="card-title fw-bold"><i class="bi bi-person-badge"></i> &nbsp;Contact Person Details</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="fw-bold m-auto">FULL NAME:</p>
                                        <p class="fs-4"><?php echo $buyerdetailrow["contact_name"]; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="fw-bold m-auto">JOB TITLE:</p>
                                        <p class="fs-4"><?php echo $buyerdetailrow["job_title"]; ?></p>
                                    </div>

                                </div>
                                <div class="row">
                                    &nbsp;
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="fw-bold m-auto">EMAIL:</p>
                                        <p class="fs-4"><?php echo $buyerdetailrow["contact_email"]; ?></p>
                                    </div>
                                    <!-- methanin palla hadanna thinooooooo -->
                                    <div class="col-md-6">
                                        <p class="fw-bold m-auto">NIC:</p>
                                        <p class="fs-4"><?php echo $buyerdetailrow["contact_nic"]; ?></p>
                                    </div>


                                </div>
                                <div class="row">


                                    <div class="col-md-6">
                                        <p class="fw-bold m-auto">PHONE NUMBER:</p>
                                        <p class="fs-4"><?php echo $buyerdetailrow["contact_phone"]; ?></p>
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="fw-bold m-auto">OFFICE ADDRESS:</p>
                                        <p class="fs-5 m-auto"><?php echo $buyerdetailrow["office_address_line_1"] . ","; ?></p>
                                        <p class="fs-5 m-auto"><?php echo $buyerdetailrow["office_address_line_2"] . ","; ?></p>
                                        <p class="fs-5 m-auto"><?php echo $buyerdetailrow["office_address_line_3"]; ?></p>
                                    </div>

                                </div>
                                <div class="row">
                                    &nbsp;
                                </div>

                                <div class="row">
                                    &nbsp;
                                </div>
                                <h4 class="card-title fw-bold"><i class="bi bi-clock-history"></i> &nbsp;Order History</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover" id="table">

                                            <thead class="table-secondary text-center">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Order Date</th>
                                                    <th>Status</th>
                                                    <th>Total Amount (LKR)</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php
                                                while ($row = $orderHistoryResult->fetch_assoc()) {

                                                    $orderAmout = $row['total_amount'] + $row['delivery_charge'];
                                                ?>
                                                    <tr>
                                                        <td><?php echo "ORD" . $row['order_id']; ?></td>
                                                        <td><?php echo $row['order_date']; ?></td>
                                                        <td style="background-color: <?php echo $row['color_code']; ?>; text-align:center;">
                                                            <?php echo $row['status_name']; ?>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <?php echo number_format($orderAmout, 2); ?>
                                                        </td>
                                                        <td>
                                                            <button href="#" class="btn btn-primary btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#viewModal" onclick="loadorder('<?php echo $row['order_id']; ?>');">
                                                                <i class="bi bi-eye-fill"></i> View
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    &nbsp;
                                </div>
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-3">
                                        <?php $company_id = base64_encode($company_id); ?>
                                        <a href="edit-buyer.php?company_id=<?php echo $company_id; ?>" class="btn btn-warning w-100">
                                            <i class="bi bi-pencil-fill"></i>
                                            &nbsp
                                            Edit
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="add-order.php?company_id=<?php echo $company_id; ?>" class="btn btn-success w-100">
                                            <i class="bi bi-box-fill"></i>
                                            &nbsp
                                            Place Order
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="#" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="loadbuyer( '<?php echo $company_id; ?>','<?php echo htmlspecialchars($buyerdetailrow['company_name'], ENT_QUOTES); ?>');">
                                            <i class="bi bi-trash-fill"></i>
                                            &nbsp
                                            Delete
                                        </a>
                                    </div>







                                </div>
                            </div>
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

<script>
    function loadbuyer(company_id, company_name) {
        document.getElementById("showBuyername").innerText = company_name;
        document.getElementById("confirmDeleteBtn").href =
            "../controller/buyer_controller.php?status=delete&company_id=" + company_id;
    }
</script>


<div class="modal fade" id="viewModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Order Details</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div id="display_data">
                <div class="modal-body text-center">
                    <div class="spinner-border text-secondary" role="status"></div>
                    <p class="mt-2 text-muted">Loading order details...</p>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>


        </div>
    </div>
</div>


<script>
        function loadorder(order_id) {

            var url = "../controller/warehouse_controller.php?status=load_order";

            $.post(url, {
                order_id: order_id
            }, function(data) {
                $("#display_data").html(data).show();
            });
        }
    </script>



<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>





</html>