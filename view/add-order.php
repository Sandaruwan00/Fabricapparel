<?php
include_once '../commons/session.php';
include_once '../model/buyer_model.php';
include_once '../model/price_model.php';
include_once '../model/transport_model.php';

$userrow = $_SESSION["user"];

$buyerObj = new Buyer();


$selectedBuyer = null;

if (isset($_GET["company_id"])) {

    $company_id = base64_decode($_GET["company_id"]);

    $buyerResultSingle = $buyerObj->getBuyerCompany($company_id);

    if ($buyerResultSingle->num_rows > 0) {
        $selectedBuyer = $buyerResultSingle->fetch_assoc();
    }
}

$buyerResult = $buyerObj->getAllBuyers();

$priceObj = new Price();
$productTypeResult = $priceObj->getActiveProductTypes();
$sizeResult = $priceObj->getActiveSizes();
$priceResult = $priceObj->getAllPricing();

$transportObj = new Transport();
$disctrictResult = $transportObj->getAllDistrict();

?>
<!DOCTYPE html>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Add Order</title>
</head>

<body>
    <div class="container">
        <?php $pageName = "ORDER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4 text-start">
                <a href="order.php" type="button" class="btn btn-outline-secondary">Back</a>
            </div>

            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-order.php" class="btn btn-outline-primary active">Add Order</a>
                    <a href="view-orders.php" class="btn btn-outline-success">View Orders</a>
                    <a href="order-payments.php" class="btn btn-outline-info">Order Payments</a>
                    <a href="order-refund.php" class="btn btn-outline-secondary">Refund Requests</a>
                    <button
                        class="btn btn-outline-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#reportModal">

                        Generate Order Reports

                    </button>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Add New Order</h1>
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

        <div class="row mt-4 justify-content-center">
            <div class="col-md-12">
                <form id="addorder" action="../controller/order_controller.php?status=add_order" method="post" enctype="multipart/form-data">
                    <div class="row justify-content-center">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                                        Buyer Information
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#selectBuyerModal">Select Buyer</a>
                                                <a href="add-order.php" class="btn btn-danger">
                                                    Reset
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">
                                        <!-- Add this inside your <form> -->
                                        <input type="hidden" name="company_id" id="company_id_input" value="<?php echo $selectedBuyer ? $selectedBuyer['company_id'] : ''; ?>">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4 text-end"><label for="">Company Name:</label></div>
                                                    <div class="col-md-8 h5">
                                                        <?php
                                                        if ($selectedBuyer) {
                                                            echo $selectedBuyer["company_name"];
                                                        } else {
                                                            echo "-";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 text-end"><label for="">Reg No:</label></div>
                                                    <div class="col-md-8 h5">
                                                        <?php
                                                        if ($selectedBuyer) {
                                                            echo $selectedBuyer["company_registration"];
                                                        } else {
                                                            echo "-";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 text-end"><label for="">Address:</label></div>
                                                    <div class="col-md-8 h5">
                                                        <?php
                                                        if ($selectedBuyer) {
                                                            echo $selectedBuyer["company_address_line_1"] . ', ' . $selectedBuyer["company_address_line_2"] . ', ' . $selectedBuyer["company_city"] . ', ' . $selectedBuyer["company_country"];
                                                        } else {
                                                            echo "-";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4 text-end"><label for="">Contact Person:</label></div>
                                                    <div class="col-md-8 h5">
                                                        <?php
                                                        if ($selectedBuyer) {
                                                            echo $selectedBuyer["contact_name"];
                                                        } else {
                                                            echo "-";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 text-end"><label for="">Title:</label></div>
                                                    <div class="col-md-8 h5">
                                                        <?php
                                                        if ($selectedBuyer) {
                                                            echo $selectedBuyer["job_title"];
                                                        } else {
                                                            echo "-";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 text-end"><label for="">Email:</label></div>
                                                    <div class="col-md-8 h5">
                                                        <?php
                                                        if ($selectedBuyer) {
                                                            echo $selectedBuyer["contact_email"];
                                                        } else {
                                                            echo "-";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 text-end"><label for="">Phone:</label></div>
                                                    <div class="col-md-8 h5">
                                                        <?php
                                                        if ($selectedBuyer) {
                                                            echo $selectedBuyer["contact_phone"];
                                                        } else {
                                                            echo "-";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-dark text-white fw-semibold">
                                        Order Details
                                    </div>
                                    <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Order Date</label>
                                                <input type="date" id="todayDate" name="order_date" class="form-control" readonly>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label>Type</label>
                                                <select name="select_product" id="select_product" class="form-control">
                                                    <option value="">--Select--</option>

                                                    <?php while ($productrow = $productTypeResult->fetch_assoc()) { ?>
                                                        <option value="<?php echo $productrow["product_type_id"]; ?>">
                                                            <?php echo $productrow["product_type_name"]; ?>
                                                        </option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label>Size</label>
                                                <select name="select_size" id="select_size" class="form-control">
                                                    <option value="">--Select--</option>

                                                    <?php
                                                    while ($sizerow = $sizeResult->fetch_assoc()) { ?>
                                                        <option value="<?php echo $sizerow["size_id"]; ?>">
                                                            <?php echo $sizerow["size_short_name"]; ?>
                                                        </option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                            <div class="col-md-1 mb-3">
                                                <label>Qty</label>
                                                <input type="number" name="qty" id="qty" class="form-control" min="1">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label>Design</label>
                                                <input type="file" name="item_design" id="item_design" class="form-control">
                                            </div>
                                            <input type="hidden" name="order_items" id="order_items_input">
                                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                                <button type="button" id="addBtn" class="btn btn-primary w-100">Add</button>
                                            </div>
                                        </div>
                                        <table class="table table-bordered mt-3">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Size</th>
                                                    <th>Qty</th>
                                                    <th>Design</th>
                                                    <th>Amount (Rs.)</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="orderTableBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-dark text-white fw-semibold">
                                        Delivery Details
                                    </div>
                                    <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">
                                        <div class="mb-3">
                                            <label class="form-label">Delivery Address</label>
                                            <input type="text" class="form-control mb-3" name="address_line_1" id="address_line_1" placeholder="Address Line 1">
                                            <input type="text" class="form-control mb-3" name="address_line_2" id="address_line_2" placeholder="Address Line 2">
                                            <input type="text" class="form-control" name="address_line_3" id="address_line_3" placeholder="Address Line 3">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">District</label>
                                                <select name="district" id="district" class="form-control  form-select">
                                                    <option value="">--Select--</option>

                                                    <?php
                                                    while ($districtrow = $disctrictResult->fetch_assoc()) { ?>
                                                        <option value="<?php echo $districtrow["district_id"]; ?>">
                                                            <?php echo $districtrow["district_name"]; ?>
                                                        </option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Expected Delivery Date</label>
                                                <input type="date" name="delivery_date" id="delivery_date" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-4" style="height: 362px;">
                                    <div class="card-header bg-dark text-white fw-semibold">
                                        Optional
                                    </div>
                                    <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Comments</label>
                                                <textarea name="comments" id="comments" class="form-control" rows="9"></textarea>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>



                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4 ">
                                    <div class="card-header bg-dark text-white fw-semibold">
                                        Payment Details
                                    </div>
                                    <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">

                                        <input type="hidden" name="amount" id="totalAmount">
                                        <input type="hidden" name="delivery_charge" id="delivery_charge">

                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">1st Installment</label>
                                                <input type="number" name="installment" id="installment" class="form-control" min="0">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Payment Method</label>
                                                <select name="payment_method" id="payment_method" class="form-select">
                                                    <option value="">------</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Card">Card</option>
                                                    <option value="Online">Online</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Reference No.</label>
                                                <input type="reference_no" name="reference_no" id="reference_no" class="form-control">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-dark text-white fw-semibold">
                                        Order Summary
                                    </div>
                                    <div class="card-body" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);">
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <div class="text-muted">Items Total</div>
                                                <div class="h4 fw-bold" id="summaryItemsTotal">Rs. 0.00</div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="text-muted">Delivery Charge</div>
                                                <div class="h4 fw-bold" id="summaryDeliveryCharge">Rs. 0.00</div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="text-muted">Grand Total</div>
                                                <div class="h4 fw-bold text-primary" id="summaryGrandTotal">Rs. 0.00</div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="text-muted">1st Installment</div>
                                                <div class="h4 fw-bold text-success" id="summaryInstallment">Rs. 0.00</div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6 text-center">
                                                <div class="text-muted">Item Count</div>
                                                <div class="h5 fw-bold" id="summaryItemCount">0</div>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <div class="text-muted">Balance Due</div>
                                                <div class="h5 fw-bold text-danger" id="summaryBalance">Rs. 0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="row mb-4">
                            <div class="col-md-3 offset-md-3">
                                <button type="submit" class="btn btn-success w-100">Submit Order</button>
                            </div>
                            <div class="col-md-3">
                                <button type="reset" class="btn btn-danger w-100">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="selectBuyerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select Buyer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover align-middle" id="buyertable">
                                        <thead class="fs-5 table-secondary text-center">
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Contact Person</th>
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
                                                    <td>
                                                        <span class="d-flex justify-content-between">

                                                            <a href="add-order.php?company_id=<?php echo $company_id; ?>" class="btn btn-success">
                                                                <i class="bi bi-box-fill"></i>
                                                                &nbsp
                                                                Place Order
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
                <div class="modal-footer">
                    <a href="add-buyer.php" class="btn btn-outline-primary">Add Buyer</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reportModal">
    <div class="modal-dialog">
        <form action="generate-order-report.php" method="post" target="_blank">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Order Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label>Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>

                    <br>

                    <label>End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Generate Report
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let today = new Date().toISOString().split("T")[0];

    document.getElementById("start_date").setAttribute("max", today);
    document.getElementById("end_date").setAttribute("max", today);

});
</script>

<?php include_once '../includes/footer_includes.php'; ?>

</body>

    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../js/order_validation.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.js"></script>
    <script src="../js/datatable/bootstrap.bundle.min.js"></script>
    <script src="../js/datatable/dataTables.bootstrap5.js"></script>
    <script src="../js/datatable/dataTables.js"></script>

    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            const dateInput = document.querySelector('#todayDate');
            const today = new Date();
            const year = today.getFullYear();
            let month = today.getMonth() + 1;
            let day = today.getDate();
            if (month < 10) month = `0${month}`;
            if (day < 10) day = `0${day}`;
            dateInput.value = `${year}-${month}-${day}`;

            // Prevent selecting a date before the order date
            const deliveryDate = document.getElementById("delivery_date");
            deliveryDate.min = dateInput.value;

            document.getElementById("installment").addEventListener("input", updateOrderSummary);
        });
    </script>

    <script>
        function setSelectedBuyer(company_id) {
            document.getElementById("company_id_input").value = company_id;
        }
    </script>

    <script>
        $(document).ready(function() {
            $("#buyertable").DataTable();
        });
    </script>

    <script>
        let pricingData = {};

        <?php while ($row = $priceResult->fetch_assoc()) {
            if ($row['pricing_status'] == 1) { ?>
                pricingData["<?php echo $row['product_type_id'] . '_' . $row['size_id']; ?>"] = <?php echo $row['price']; ?>;
        <?php }
        } ?>
    </script>

    <script>
        function updateOrderSummary() {
            let itemsTotal = 0;
            orderItems.forEach(item => {
                itemsTotal += item.qty * item.price;
            });

            let deliveryCharge = parseFloat(document.getElementById("delivery_charge").value) || 0;
            let installmentInput = document.getElementById("installment");
            let installment = parseFloat(installmentInput.value) || 0;

            let grandTotal = itemsTotal + deliveryCharge;

            // Prevent installment from exceeding grand total
            if (installment > grandTotal) {
                installment = grandTotal;
                installmentInput.value = grandTotal.toFixed(2);
                alert("1st Installment cannot be greater than the Grand Total.");
            }

            let balance = grandTotal - installment;

            document.getElementById("summaryItemsTotal").innerText = "Rs. " + itemsTotal.toFixed(2);
            document.getElementById("summaryDeliveryCharge").innerText = "Rs. " + deliveryCharge.toFixed(2);
            document.getElementById("summaryGrandTotal").innerText = "Rs. " + grandTotal.toFixed(2);
            document.getElementById("summaryInstallment").innerText = "Rs. " + installment.toFixed(2);
            document.getElementById("summaryItemCount").innerText = orderItems.length;
            document.getElementById("summaryBalance").innerText = "Rs. " + (balance < 0 ? "0.00" : balance.toFixed(2));
        }
    </script>

    <script>
        let orderItems = []; // metadata for each item (no binary files in here)
        let itemCounter = 0; // permanent unique id per item, never reused
        const totalInput = document.getElementById("totalAmount");
        const form = document.querySelector("form");

        function calculateTotal() {
            let total = 0;
            orderItems.forEach(item => {
                total += item.qty * item.price;
            });
            totalInput.value = total;

            let delivery = total * 0.05;
            if (delivery < 1000) delivery = 1000;
            document.getElementById("delivery_charge").value = delivery.toFixed(2);

            updateOrderSummary();
        }

        // Add item to table
        document.getElementById("addBtn").addEventListener("click", function() {
            let productSelect = document.getElementById("select_product");
            let sizeSelect = document.getElementById("select_size");
            let qtyInput = document.getElementById("qty");
            let designInput = document.getElementById("item_design");

            let product_id = productSelect.value;
            let size_id = sizeSelect.value;
            let qty = parseInt(qtyInput.value);
            let file = designInput.files[0]; // may be undefined

            let productText = productSelect.options[productSelect.selectedIndex].text;
            let sizeText = sizeSelect.options[sizeSelect.selectedIndex].text;

            if (!product_id || !size_id || !qty || qty <= 0 || !file) {
                alert("Please fill all fields correctly!");
                return;
            }

            let key = product_id + "_" + size_id;
            let price = pricingData[key];

            if (!price) {
                alert("Price not found!");
                console.log("Missing key:", key);
                return;
            }

            let existing = orderItems.find(item =>
                item.product_id == product_id && item.size_id == size_id
            );
            if (existing) {
                alert("This product & size already added!");
                return;
            }

            let amount = price * qty;
            let itemKey = itemCounter++;

            orderItems.push({
                item_key: itemKey,
                product_id: product_id,
                size_id: size_id,
                qty: qty,
                price: price,
                design_name: file ? file.name : null
            });

            // Create a dedicated hidden file input for this item's design,
            // so each row's file survives independently until submit.
            if (file) {
                let hiddenInput = document.createElement("input");
                hiddenInput.type = "file";
                hiddenInput.name = `item_design[${itemKey}]`;
                hiddenInput.id = `item_design_${itemKey}`;
                hiddenInput.style.display = "none";

                let dt = new DataTransfer();
                dt.items.add(file);
                hiddenInput.files = dt.files;

                form.appendChild(hiddenInput);
            }

            let row = `
        <tr data-item-key="${itemKey}">
            <td>${productText}</td>
            <td>${sizeText}</td>
            <td class="qtyCell">${qty}</td>
            <td class="designCell">${file ? file.name : "-"}</td>
            <td class="amountCell">${amount}</td>
            <td>
                <button type="button" class="btn btn-warning btn-sm editBtn">Edit</button>
                <button type="button" class="btn btn-success btn-sm saveBtn d-none">Save</button>
                <button type="button" class="btn btn-danger btn-sm removeBtn">Remove</button>
            </td>
        </tr>
    `;
            document.getElementById("orderTableBody").insertAdjacentHTML("beforeend", row);

            // Clear inputs for next entry
            qtyInput.value = "";
            designInput.value = "";
            productSelect.selectedIndex = 0;
            sizeSelect.selectedIndex = 0;

            calculateTotal();
        });

        // Remove row + its file input + update array (matched by item_key, not row position)
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("removeBtn")) {
                let row = e.target.closest("tr");
                let itemKey = row.dataset.itemKey;

                orderItems = orderItems.filter(item => item.item_key != itemKey);

                let hiddenInput = document.getElementById(`item_design_${itemKey}`);
                if (hiddenInput) hiddenInput.remove();

                row.remove();
                calculateTotal();
            }
        });

        // Edit qty
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("editBtn")) {
                let row = e.target.closest("tr");
                let qtyCell = row.querySelector(".qtyCell");
                let currentQty = qtyCell.innerText;

                qtyCell.innerHTML = `<input type="number" class="form-control form-control-sm editQty" value="${currentQty}" min="1">`;

                row.querySelector(".editBtn").classList.add("d-none");
                row.querySelector(".saveBtn").classList.remove("d-none");
            }
        });

        // Save qty (matched by item_key)
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("saveBtn")) {
                let row = e.target.closest("tr");
                let itemKey = row.dataset.itemKey;
                let qtyInput = row.querySelector(".editQty");
                let newQty = parseInt(qtyInput.value);

                if (!newQty || newQty <= 0) {
                    alert("Invalid quantity!");
                    return;
                }

                let item = orderItems.find(i => i.item_key == itemKey);
                let newAmount = item.price * newQty;

                row.querySelector(".qtyCell").innerText = newQty;
                row.querySelector(".amountCell").innerText = newAmount;

                item.qty = newQty;

                row.querySelector(".editBtn").classList.remove("d-none");
                row.querySelector(".saveBtn").classList.add("d-none");

                calculateTotal();
            }
        });

        // Submit: serialize metadata; files already sit in the form as item_design[itemKey]
        form.addEventListener("submit", function(e) {
            document.getElementById("order_items_input").value = JSON.stringify(orderItems);

            if (orderItems.length === 0) {
                e.preventDefault();
                alert("Please add at least one order item!");
            }
        });
    </script>

</html>