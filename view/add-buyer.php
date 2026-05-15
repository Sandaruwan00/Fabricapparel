<?php
include_once '../commons/session.php';
$userrow = $_SESSION["user"];

include_once '../model/buyer_model.php';
$buyerObj = new Buyer();

$businessTypeResult = $buyerObj->getBusinessType();
?>

<!DOCTYPE html>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Add New Buyer</title>
</head>

<body>
    <div class="container">

        <?php $pageName = "BUYER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>


        <div class="row">
            <div class="col-md-4 text-start">
                <a href="buyer.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Add New Buyer</h1>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <a href="add-buyer.php" class="btn btn-outline-primary active">Add Buyer</a>
                    <a href="view-buyers.php" class="btn btn-outline-success">View Buyers</a>
                    <a href="generate-buyer-report.php" class="btn btn-outline-warning">Generate Buyer Reports</a>
                </div>
            </div>
        </div>

        <div class="row">
            &nbsp;
        </div>

        <div class="row justify-content-center">
            <div class="col-md-12">

                <form action="../controller/buyer_controller.php?status=add_buyer" method="post">


                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-dark text-white fw-semibold">
                                    Company Information
                                </div>

                                <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" name="company_name" id="company_name" class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Company Registration Number</label>
                                            <input type="text" name="company_registration" id="company_registration" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Business Type</label>
                                            <select name="business_type" id="business_type" class="form-select" required>
                                                <option value="">Select Type</option>
                                                <?php
                                        while ($businessTypeRow = $businessTypeResult->fetch_assoc()) {
                                        ?>
                                            <option value="<?php echo $businessTypeRow["business_type_id"]; ?>">
                                                <?php echo $businessTypeRow["business_type_name"]; ?>
                                            </option>
                                        <?php
                                        }
                                        ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="url" name="website" id="website" class="form-control" placeholder="http://www.company.com" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Address</label>
                                            <input type="text" name="company_address_line_1" id="company_address_line_1" class="form-control" placeholder="Address Line 1" required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <input type="text" name="company_address_line_2" id="company_address_line_2" class="form-control" placeholder="Address Line 2" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <input type="text" name="company_city" id="company_city" class="form-control" placeholder="City" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <input type="text" name="company_postal_code" id="company_postal_code" class="form-control" placeholder="Postal Code" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <input type="text" name="company_country" id="company_country" class="form-control" placeholder="Country" required>
                                        </div>


                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-dark text-white fw-semibold">
                                    Contact Person Details (Representative)
                                </div>

                                <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">

                                    <div class="row">
                                        

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="contact_name" id="contact_name" class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Job Title</label>
                                            <input type="text" name="job_title" id="job_title" class="form-control" required>
                                            
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" name="contact_email" id="contact_email" class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="contact_phone" id="contact_phone" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Office Address</label>
                                            <input type="text" name="office_address_line_1" id="office_address_line_1" class="form-control" placeholder="Address Line 1" required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <input type="text" name="office_address_line_2" id="office_address_line_2" class="form-control" placeholder="Address Line 2" required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <input type="text" name="office_address_line_3" id="office_address_line_3" class="form-control" placeholder="Address Line 3" required>
                                        </div>
                                        
                                        
                                        


                                    </div>

                                </div>
                            </div>
                        </div>


                    </div>





                    <!-- BUTTONS -->
                    <div class="row mb-4">
                        <div class="col-md-3 offset-md-3">
                            <button type="submit" class="btn btn-success w-100">Submit</button>
                        </div>
                        <div class="col-md-3">
                            <button type="reset" class="btn btn-danger w-100">Reset</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>





    </div>

    <?php include_once '../includes/footer_includes.php'; ?>
    <script src="../bootstrap/dist/js/bootstrap.js"></script>









</body>



</html>