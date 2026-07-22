<?php
include_once '../commons/session.php';
include_once '../model/planning_model.php';
include_once '../model/stock_model.php';
include_once '../model/order_model.php';

$userrow = $_SESSION["user"];

$planObj = new Planning();
$stockObj = new Stock();
$orderObj = new Order();

$plan_id = base64_decode($_GET["plan_id"]);


$planResult = $planObj->getPlan($plan_id);
$planrow = $planResult->fetch_assoc();


$stockRequest = $stockObj->getStockRequest($plan_id);
$stockRequestRow = $stockRequest->fetch_assoc();

$stockRequestResult = $stockObj->getStockRequestItems($plan_id);
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Plan</title>
</head>

<body>
    <div class="container">

        <?php $pageName = "PLANNING MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <!-- Header -->
        <div class="row">
            <div class="col-md-4 text-start">
                <a href="view-plans.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">View Plan</h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-plan.php" class="btn btn-outline-primary">Add Plan</a>
                    <a href="view-plans.php" class="btn btn-outline-success">View Plans</a>
                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reportModal">Generate Plan Reports</button>
                </div>
            </div>
        </div>

        <div class="row">&nbsp;</div>

        <!-- Card -->
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card" style="box-shadow: 0 4px 8px rgba(0,0,0,0.2);">

                    <!-- Header -->
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="height: 70px;">
                        <div>
                            <h3 class="fw-bold mb-1">Plan #<?php echo $planrow["plan_id"]; ?></h3>
                        </div>

                        <?php
                        if ($planrow["plan_status"] == "Pending") {
                            $color_class = "bg-warning";
                        } elseif ($planrow["plan_status"] == "Approved") {
                            $color_class = "bg-success";
                        } else {
                            $color_class = "bg-danger";
                        }


                        ?>

                        <span class="badge <?= $color_class; ?> fs-6">
                            <?php echo $planrow["plan_status"]; ?>
                        </span>
                    </div>

                    <div class="card-body" style="margin:20px;">

                        <!-- Plan Info -->
                        <h4 class="fw-bold"><i class="bi bi-receipt"></i> Order Information</h4>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="fs-5 fw-bold">Order #<?php echo $planrow["order_id"]; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">ORDER DATE:</p>
                                <p class="fs-5"><?php echo $planrow["order_date"]; ?></p>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <p class="fw-bold m-auto">COMMENTS:</p>
                                <p class="fs-5"><?php echo $planrow["comments"]; ?></p>
                            </div>
                        </div>


                        <div class="row">&nbsp;</div>

                        <?php $orderItemsResult = $orderObj->getOrderItems($planrow["order_id"]); ?>

                        <!-- Items -->
                        <h4 class="fw-bold"><i class="bi bi-box-seam"></i> Order Items</h4>
                        <hr>

                        <table class="table table-bordered">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                    <th>Design</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = $orderItemsResult->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $item["product_type_name"]; ?></td>
                                        <td><?php echo $item["size_short_name"]; ?></td>
                                        <td><?php echo $item["qty"]; ?></td>
                                        <td class="text-end"><?php echo number_format($item["unit_price"], 2); ?></td>
                                        <td class="text-end"><?php echo number_format($item["qty"] * $item["unit_price"], 2); ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($item["item_design"])) { ?>
                                                <button type="button"
                                                    class="btn btn-outline-primary btn-sm previewDesignBtn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#designPreviewModal"
                                                    data-file="../files/designs/<?php echo $item["item_design"]; ?>"
                                                    data-filename="<?php echo $item["item_design"]; ?>">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            <?php } else { ?>
                                                <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div class="row">&nbsp;</div>


                        <!-- Items -->
                        <h4 class="fw-bold"><i class="bi bi-clipboard-plus"></i> Stock Request Items</h4>
                        <hr>

                        <table class="table table-bordered">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Stock Item</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                while ($item = $stockRequestResult->fetch_assoc()) {
                                    $no++;
                                ?>
                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo $item['stock_item_id'] . " - " . $item['stock_item_name'] . " " . $item['stock_item_color_code']; ?></td>
                                        <td class="text-end"><?php echo $item["requested_qty"] . " " . $item["stock_unit_short_name"]; ?></td>

                                        <?php
                                        if ($item["stock_request_item_status"] == "Hold") {
                                            $status = "bg-secondary";
                                        } elseif ($item["stock_request_item_status"] == "Rejected") {
                                            $status = "bg-danger";
                                        } elseif ($item["stock_request_item_status"] == "Pending") {
                                            $status = "bg-warning";
                                        } else {
                                            $status = "bg-success";
                                        }

                                        ?>

                                        <td class="text-center <?= $status; ?>"><?php echo $item["stock_request_item_status"]; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div class="row">&nbsp;</div>



                        <div class="row">&nbsp;</div>

                        <?php
                        if ($planrow["plan_status"] == "Pending") {
                        ?>
                            <!-- Buttons -->
                            <div class="row justify-content-end">

                                <div class="col-md-3">
                                    <button href="#" class="btn btn-success w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="bi bi-check-lg"></i> Confirm Plan
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button href="#" type="button" id="rejectBtn" class="btn btn-danger w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="bi bi-slash-circle"></i> Reject Plan
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button href="#" class="btn btn-primary w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewModal" onclick="loadorder('<?php echo $planrow['order_id']; ?>');">
                                        <i class="bi bi-eye-fill"></i> View Order
                                    </button>
                                </div>
                            </div>
                        <?php
                        }
                        ?>



                    </div>

                </div>
            </div>
        </div>

    </div>


    <!-- reject modal -->
    <div class="modal fade" id="rejectModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="rejectBtnLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Reject Plam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/planning_controller.php?status=reject_plan" method="post">
                    <div class="modal-body">
                        <p>Are you sure you want to reject Plan
                            <strong><?php echo $planrow["plan_id"]; ?></strong>?
                        </p>
                        <label class="form-label">Remarks <span class="text-danger">*</span></label>
                        <input type="hidden" name="plan_id" value="<?php echo $planrow["plan_id"]; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $planrow["order_id"]; ?>">
                        <input type="hidden" name="stock_request_id" value="<?php echo $stockRequestRow["stock_request_id"]; ?>">
                        <textarea id="remarks" name="remarks" class="form-control" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="rejectPlan" id="cancelBtn" class="btn btn-danger">Reject Plan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- approve Modal -->
    <div class="modal fade" id="approveModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Plan Confirmation</h5>
                </div>
                <form action="../controller/planning_controller.php?status=approve_plan" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="plan_id" value="<?php echo $planrow["plan_id"]; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $planrow["order_id"]; ?>">
                        <input type="hidden" name="stock_request_id" value="<?php echo $stockRequestRow["stock_request_id"]; ?>">
                        Are you sure you want to confirm Plan <strong><?php echo $planrow["plan_id"]; ?></strong> ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="planapprove" id="approveBtn" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="reportModal">
        <div class="modal-dialog">
            <form action="generate-plan-report.php" method="post" target="_blank">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Generate Plan Report</h5>
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
        document.addEventListener("DOMContentLoaded", function() {

            let today = new Date().toISOString().split("T")[0];

            document.getElementById("start_date").setAttribute("max", today);
            document.getElementById("end_date").setAttribute("max", today);

        });
    </script>

    <!-- item design view modal -->

    <!-- Design Preview Modal -->
    <div class="modal fade" id="designPreviewModal" tabindex="-1" aria-labelledby="designPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="designPreviewModalLabel">Design Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="min-height: 400px;">
                    <div id="designPreviewContent">
                        <!-- populated via JS -->
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="designDownloadBtn" class="btn btn-outline-secondary" download>
                        <i class="bi bi-download"></i> Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("click", function(e) {
            const btn = e.target.closest(".previewDesignBtn");
            if (!btn) return;

            const filePath = btn.dataset.file;
            const fileName = btn.dataset.filename;
            const content = document.getElementById("designPreviewContent");
            const downloadBtn = document.getElementById("designDownloadBtn");

            const ext = fileName.split('.').pop().toLowerCase();
            const imageExts = ["jpg", "jpeg", "png", "gif", "webp"];

            if (ext === "pdf") {
                content.innerHTML = `<iframe src="${filePath}" width="100%" height="500px" style="border:none;"></iframe>`;
            } else if (imageExts.includes(ext)) {
                content.innerHTML = `<img src="${filePath}" class="img-fluid" alt="Design Preview">`;
            } else {
                content.innerHTML = `
                <p class="text-muted">Preview not available for this file type (.${ext}).</p>
                <p><strong>${fileName}</strong></p>
            `;
            }

            downloadBtn.href = filePath;
            downloadBtn.setAttribute("download", fileName);
        });

        // Clean up content when modal closes, so old previews don't flash before new ones load
        document.getElementById("designPreviewModal").addEventListener("hidden.bs.modal", function() {
            document.getElementById("designPreviewContent").innerHTML = "";
        });
    </script>

    <div class="modal fade" id="viewModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
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

            var url = "../controller/order_controller.php?status=load_order";

            $.post(url, {
                order_id: order_id
            }, function(data) {
                $("#display_data").html(data).show();
            });
        }
    </script>

    <?php include_once '../includes/footer_includes.php'; ?>

    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.js"></script>
    <script src="../js/datatable/bootstrap.bundle.min.js"></script>
    <script src="../js/datatable/dataTables.bootstrap5.js"></script>
    <script src="../js/datatable/dataTables.js"></script>

    <!-- alert start -->
    <?php
    $msg = "";
    if (isset($_GET["msg"])) {
        $msg = base64_decode($_GET["msg"]);
    }
    ?>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="msgToast" class="toast align-items-center text-bg-secondary border-0" role="alert" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body" id="toastMsg">
                    <!-- Message -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let msg = "<?php echo $msg; ?>";
            if (msg !== "") {
                document.getElementById("toastMsg").innerText = msg;
                let toastEl = document.getElementById("msgToast");
                let toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    </script>
    <!-- alert end -->

</body>

</html>