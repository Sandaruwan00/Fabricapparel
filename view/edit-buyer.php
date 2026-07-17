<?php
include_once '../commons/session.php';
include_once '../model/buyer_model.php';
$userrow = $_SESSION["user"];

$buyerObj = new Buyer();

$company_id = base64_decode($_GET["company_id"]);
$buyerCompanyResult = $buyerObj->getBuyerCompany($company_id);
$businessTypeResult = $buyerObj->getBusinessType();

$editBuyer = $buyerCompanyResult->fetch_assoc();

?>

<!DOCTYPE html>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Edit Buyer</title>
</head>

<body>
    <div class="container">

        <?php $pageName = "BUYER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>


        <div class="row">
            <div class="col-md-4 text-start">
                <a href="view-buyers.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Edit Buyer</h1>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <a href="add-buyer.php" class="btn btn-outline-primary">Add Buyer</a>
                    <a href="view-buyers.php" class="btn btn-outline-success">View Buyers</a>
                    <a href="generate-buyer-report.php" class="btn btn-outline-warning">Generate Buyer Reports</a>
                </div>
            </div>
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
            <div class="col-md-12">

                <form action="../controller/buyer_controller.php?status=update_buyer" method="post">



                    <div class="row g-4">

                        <!-- Company Information -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow border-0 rounded-4">
                                <div class="card-header bg-dark text-white fw-semibold">
                                    Company Information
                                </div>

                                <div class="card-body" style="background:linear-gradient(90deg,#FDE9E1 0%,#B9D9EB 100%);">
                                    <div class="row g-3">
                                        <input type="hidden" name="company_id" value="<?php echo $editBuyer["company_id"]; ?>">

                                        <div class="col-md-6">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" name="company_name" id="company_name" class="form-control" value="<?php echo $editBuyer["company_name"]; ?>">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Registration No.</label>
                                            <input type="text" name="company_registration" id="company_registration" class="form-control" value="<?php echo $editBuyer["company_registration"]; ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Business Type</label>
                                            <select name="business_type" id="business_type" class="form-select" required>
                                                <option value="">Select Type</option>
                                                <?php
                                                while ($businessTypeRow = $businessTypeResult->fetch_assoc()) {
                                                ?>
                                                    <option value="<?php echo $businessTypeRow["business_type_id"]; ?>"
                                                        <?php
                                                        if ($businessTypeRow["business_type_id"] == $editBuyer["business_type_id"]) {
                                                        ?>
                                                        selected
                                                        <?php
                                                        }
                                                        ?>>
                                                        <?php echo $businessTypeRow["business_type_name"]; ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Website</label>
                                            <input type="url" name="website" id="website" class="form-control" placeholder="https://www.company.com" value="<?php echo $editBuyer["website"]; ?>">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Address</label>
                                            <input type="text" name="company_address_line_1" id="company_address_line_1" class="form-control mb-2" value="<?php echo $editBuyer["company_address_line_1"]; ?>">
                                            <input type="text" name="company_address_line_2" id="company_address_line_2" class="form-control" value="<?php echo $editBuyer["company_address_line_2"]; ?>">
                                        </div>

                                        

                                        <div class="col-md-4">
                                            <label class="form-label">City</label>
                                            <input type="text" name="company_city" id="company_city" class="form-control" value="<?php echo $editBuyer["company_city"]; ?>">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Postal Code</label>
                                            <input type="text" name="company_postal_code" id="company_postal_code" class="form-control" value="<?php echo $editBuyer["company_postal_code"]; ?>">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Country</label>
                                            <input type="text" name="company_country" id="company_country" class="form-control" value="<?php echo $editBuyer["company_country"]; ?>">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Person -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow border-0 rounded-4">
                                <div class="card-header bg-dark text-white fw-semibold">
                                    Contact Person Details
                                </div>

                                <div class="card-body" style="background:linear-gradient(90deg,#FDE9E1 0%,#B9D9EB 100%);">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="contact_name" id="contact_name" class="form-control" value="<?php echo $editBuyer["contact_name"]; ?>">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Job Title</label>
                                            <input type="text" name="job_title" id="job_title" class="form-control" value="<?php echo $editBuyer["job_title"]; ?>">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" name="contact_email" id="contact_email" class="form-control" value="<?php echo $editBuyer["contact_email"]; ?>">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">NIC</label>
                                            <input type="text" name="contact_nic" id="contact_nic" class="form-control" value="<?php echo $editBuyer["contact_nic"]; ?>">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="<?php echo $editBuyer["contact_phone"]; ?>">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Office Address</label>
                                            <input type="text" name="office_address_line_1" id="office_address_line_1" class="form-control mb-1" value="<?php echo $editBuyer["office_address_line_1"]; ?>">
                                            <input type="text" name="office_address_line_2" id="office_address_line_2" class="form-control mb-1" value="<?php echo $editBuyer["office_address_line_2"]; ?>">
                                            <input type="text" name="office_address_line_3" id="office_address_line_3" class="form-control" value="<?php echo $editBuyer["office_address_line_3"]; ?>">
                                        </div>
                                        </div>

                                       
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-5">
                        <button type="submit" class="btn btn-success px-5">Submit</button>
                        <button type="reset" class="btn btn-danger px-5">Reset</button>
                    </div>

                </form>

            </div>
        </div>








    </div>

    <?php include_once '../includes/footer_includes.php'; ?>
    
    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.js"></script>
    <script src="../js/buyer_validation.js"></script>









</body>



</html>