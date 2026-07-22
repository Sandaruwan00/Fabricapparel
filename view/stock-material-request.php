<?php
include_once '../commons/session.php';
include_once '../model/stock_model.php';
include_once '../model/production_model.php';

$userrow = $_SESSION["user"];

$stockObj = new Stock();
$requests = $stockObj->getAllStockRequests();

$productionObj = new Production();
$productionStockRequests = $stockObj->getAllProductionStockRequest();

$psrCount = $stockObj->getAllProductionStockRequest();

$productionStockRequestsCount = 0;
while ($rowcount = $psrCount->fetch_assoc()) {
    if ($rowcount["psr_status"] == "Pending") {
        $productionStockRequestsCount++;
    }
}
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php"; ?>
    <title>Stock Material Requests</title>
</head>

<body>
    <div class="container">

        <?php $pageName = "STOCK MANAGEMENT"; ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <!-- Back Button -->
        <div class="row">
            <div class="col-md-4">
                <a href="stock.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-8" style="text-align:right;">
        <div class="btn-group">
          <a href="stock-items.php" class="btn btn-outline-primary">Materials</a>
          <a href="stock-list.php" class="btn btn-outline-success">Inventory</a>
          <a href="stock-material-request.php" class="btn btn-outline-info active">Stock Requests</a>
          <a href="stock-purchase-requests.php" class="btn btn-outline-secondary">Purchase Requests</a>
          <a href="generate-stock-report.php" class="btn btn-outline-warning">Generate Reports</a>
        </div>
      </div>

        </div>
        <div class="row mt-4">
            <div class="col-md-4"></div>
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Material Requests</h1>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#productionStockRequestModal">
                    View Production Stock Requests <span class="badge text-bg-warning"><?php echo $productionStockRequestsCount; ?></span>
                </button>
            </div>

        </div>
        



        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-md-12">

                <table class="table table-bordered table-striped" id="materialRequestTable">
                    <thead>
                        <tr>
                            <th width="15%">Stock Request ID</th>
                            <th width="15%">Request Date</th>
                            <th width="10%">Plan ID</th>
                            <th width="10%">Order ID</th>
                            <th width="25%">Company Name</th>
                            <th width="15%">Request Status</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $requests->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['stock_request_id']; ?></td>
                                <td><?= $row['request_date']; ?></td>
                                <td><?= "PLAN" . $row['plan_id']; ?></td>
                                <td><?= "ORD" . $row['order_id']; ?></td>
                                <td><?= $row['company_name']; ?></td>

                                <?php
                                if ($row['stock_request_status'] == "Pending") {
                                    $status_color = "bg-warning";
                                } else {
                                    $status_color = "bg-success";
                                }

                                ?>

                                <td class="text-center <?= $status_color; ?>"><?= $row['stock_request_status']; ?></td>
                                <td>
                                    <button href="#" class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewModal" onclick="loadstockrequestitem( '<?php echo $row['stock_request_id']; ?>' , '<?php echo $row['stock_request_status']; ?>');">
                                        <i class="bi bi-eye-fill"></i> View
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>

    <div class="modal fade" id="viewModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Request Details</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div id="display_data">
                    <div class="modal-body text-center">
                        <div class="spinner-border text-secondary" role="status"></div>
                        <p class="mt-2 text-muted">Loading order details...</p>
                    </div>
                </div>
                

            </div>
        </div>
    </div>


    <!--  View Production Stock Requests modal -->
    <div class="modal fade" id="productionStockRequestModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Request Stocks for Production</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <div class="container-fluid">



                        <!-- Table -->
                        <table class="table table-bordered table-striped" id="productionStockRequestsTable">
                            <thead>
                                <tr>
                                    <th width="12%">Request ID</th>
                                    <th width="30%">Stock Item</th>
                                    <th width="10%">Quantity</th>
                                    <th width="18%">Request Date</th>
                                    <th width="15%">Request Status</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($row = $productionStockRequests->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?= $row['psr_id']; ?></td>
                                        <td><?= $row['stock_item_id'] . " - " . $row['stock_item_name'] . " " . $row['stock_item_color_code'] . " | Unit - " . $row['stock_unit_name']; ?></td>
                                        <td><?= $row['psr_qty'] . " " . $row['stock_unit_short_name']; ?></td>
                                        <td><?= $row['psr_date']; ?></td>

                                        <?php
                                        if ($row['psr_status'] == "Pending") {
                                            $status_color = "bg-warning";
                                        } elseif ($row['psr_status'] == "Issued") {
                                            $status_color = "bg-success";
                                        } else {
                                            $status_color = "bg-danger";
                                        }

                                        ?>

                                        <td class="text-center <?= $status_color; ?>"><?= $row['psr_status']; ?></td>
                                        <td>
                                            <?php 
                                            if($row['psr_status'] == "Pending"){ ?>
                                            <a href="../controller/stock_controller.php?status=issue_psr&psr_id=<?php echo $row['psr_id']; ?>&stock_item_id=<?php echo $row['stock_item_id']; ?>&quantity=<?php echo $row['psr_qty']; ?>" class="btn btn-success btn-sm">Issue</a>
                                            <a href="../controller/stock_controller.php?status=reject_psr&psr_id=<?php echo $row['psr_id']; ?>" class="btn btn-danger btn-sm">Reject</a>

                                            <?php
                                            }
                                            ?>
                                            
                                        </td>

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>





    <?php include_once '../includes/footer_includes.php'; ?>

    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.js"></script>
    <script src="../js/datatable/bootstrap.bundle.min.js"></script>
    <script src="../js/datatable/dataTables.bootstrap5.js"></script>
    <script src="../js/datatable/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $("#materialRequestTable").DataTable();
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#productionStockRequestsTable").DataTable();
        });
    </script>

    <script>
        function loadstockrequestitem(stock_request_id, stock_request_status) {

            var url = "../controller/stock_controller.php?status=load_stock_request";

            $.post(url, {
                stock_request_id: stock_request_id,
                stock_request_status: stock_request_status
            }, function(data) {
                $("#display_data").html(data).show();
            });
        }
    </script>


</body>

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


</html>