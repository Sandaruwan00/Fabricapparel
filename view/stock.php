<?php

include_once '../commons/session.php';
include_once '../model/stock_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$stockObj = new Stock();
$stockItemResult = $stockObj->getAllStockItems();

$materialsCount = 0;
$materialResult = $stockItemResult->fetch_assoc();
while ($row = $stockItemResult->fetch_assoc()) {
  $materialsCount++;
}

$lowstockCount = 0;
$lowStockItems = $stockObj->getAllStocks();
while ($row = $lowStockItems->fetch_assoc()) {
  if ($row["quantity"] <= $row["min_stock_level"]) {
    $lowstockCount++;
  }
}

$outOfStockCount = 0;
$outOfStockResult = $stockObj->getOutOfStocksCount();
$outOfStockRow = $outOfStockResult->fetch_assoc();
$outOfStockCount = $outOfStockRow["total"];

$materialRequestCount = 0;
$requests = $stockObj->getAllStockRequests();
while ($row = $requests->fetch_assoc()) {
  if ($row["stock_request_status"] == "Pending") {
    $materialRequestCount++;
  }
}

$stockResult = $stockObj->getAllStocks();

$statusCounts = [
  "In Stock" => 0,
  "Low Stock" => 0,
  "Out of Stock" => 0
];
$categoryQty = []; // category_name => total quantity

while ($row = $stockResult->fetch_assoc()) {
  $qty = $row['quantity'];
  $min = $row['min_stock_level'];

  if ($qty == 0) {
    $status = "Out of Stock";
  } elseif ($qty <= $min) {
    $status = "Low Stock";
  } else {
    $status = "In Stock";
  }
  $statusCounts[$status]++;

  $catName = $row['stock_category_name'];
  if (!isset($categoryQty[$catName])) {
    $categoryQty[$catName] = 0;
  }
  $categoryQty[$catName] += $qty;
}



// Top 5 highest demand materials (based on total Stock OUT quantity)
$transactionsForDemand = $stockObj->getAllTransactions();
$demandByItem = []; // item name => total OUT quantity

while ($row = $transactionsForDemand->fetch_assoc()) {
  if ($row['transaction_type'] == 'OUT') {
    $itemName = $row['stock_item_name'] . " " . $row['stock_item_color_code'];
    if (!isset($demandByItem[$itemName])) {
      $demandByItem[$itemName] = 0;
    }
    $demandByItem[$itemName] += $row['quantity'];
  }
}

asort($demandByItem); // sort descending by quantity
$top5Demand = array_slice($demandByItem, 0, 5, true);
?>

<html>

<head>
  <?php include_once "../includes/bootstrap_css_includes.php" ?>
  <title>Stock Management</title>
  <script src="../js/plotly-3.0.1.min.js" charset="utf-8"></script>
</head>

<body style="border-radius:10px;">
  <div class="container">
    <?php $pageName = "STOCK MANAGEMENT" ?>
    <?php include_once "../includes/header_row_module_includes.php"; ?>

    <div class="row">
      <div class="col-md-4" style="text-align:left;">
        <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>
      </div>
      <div class="col-md-8" style="text-align:right;">
        <div class="btn-group">
          <a href="stock-items.php" class="btn btn-outline-primary">Materials</a>
          <a href="stock-list.php" class="btn btn-outline-success">Inventory</a>
          <a href="stock-material-request.php" class="btn btn-outline-info">Stock Requests</a>
          <a href="stock-purchase-requests.php" class="btn btn-outline-secondary">Purchase Requests</a>
          <a href="generate-stock-report.php" class="btn btn-outline-warning">Generate Reports</a>
        </div>
      </div>
    </div>

    <div class="row">&nbsp;</div>

    <div class="row d-flex justify-content-around align-items-center shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
      <span class="h3 mb-4 fw-bold">Stock Summary</span>
      <div class="row d-flex justify-content-around text-center">
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Materials</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $materialsCount; ?>
            </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Low Stocks</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $lowstockCount; ?>
            </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Out of Stocks</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $outOfStockCount; ?>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Pending Material Requests</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $materialRequestCount; ?>
            </h1>
          </div>
        </div>

      </div>
    </div>

    <div class="row">&nbsp;</div>


    <div class="row mt-4">
      <div class="col-md-6">
        <div id="statusChart"></div>
      </div>
      <div class="col-md-6">
        <div id="topDemandChart"></div>
      </div>
    </div>




    <div class="row">&nbsp;</div>




  </div>
  <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<!-- Plotly Charts -->
<script>
  // Status breakdown pie chart
  var statusLabels = <?php echo json_encode(array_keys($statusCounts)); ?>;
  var statusValues = <?php echo json_encode(array_values($statusCounts)); ?>;

  var statusData = [{
    labels: statusLabels,
    values: statusValues,
    type: 'pie',
    marker: {
      colors: ['#198754', '#ffc107', '#dc3545'] // In Stock, Low Stock, Out of Stock
    },
    textinfo: 'label+percent',
    hoverinfo: 'label+value'
  }];

  var statusLayout = {
    height: 300,
    width: 650,
    title: {
      text: "In Stock Vs Low Stocks"
    },
    margin: {
      t: 30,
      b: 30,
      l: 30,
      r: 30
    },
    showlegend: true,
    paper_bgcolor: 'rgba(0,0,0,0)'
  };

  Plotly.newPlot('statusChart', statusData, statusLayout, {
    responsive: true
  });

  // Quantity by category bar chart
  var categoryLabels = <?php echo json_encode(array_keys($categoryQty)); ?>;
  var categoryValues = <?php echo json_encode(array_values($categoryQty)); ?>;

  var categoryData = [{
    x: categoryLabels,
    y: categoryValues,
    type: 'bar',
    marker: {
      color: '#0d6efd'
    },
    text: categoryValues,
    textposition: 'auto'
  }];

  var categoryLayout = {
    margin: {
      t: 10,
      b: 60,
      l: 40,
      r: 10
    },
    xaxis: {
      title: ''
    },
    yaxis: {
      title: 'Total Quantity'
    },
    plot_bgcolor: 'rgba(0,0,0,0)',
    paper_bgcolor: 'rgba(0,0,0,0)'
  };

  Plotly.newPlot('categoryChart', categoryData, categoryLayout, {
    responsive: true
  });
</script>


<script>
  var demandLabels = <?php echo json_encode(array_keys($top5Demand)); ?>;
  var demandValues = <?php echo json_encode(array_values($top5Demand)); ?>;

  var demandData = [{
    x: demandValues,
    y: demandLabels,
    type: 'bar',
    orientation: 'h',
    marker: {
      color: '#0048ff'
    },
    text: demandValues,
    textposition: 'auto'
  }];

  var demandLayout = {
    height: 300,
    width: 650,
    title: {
      text: "Top 5 Highest Demand Materials"
    },
    margin: {
      t: 30,
      b: 40,
      l: 150,
      r: 20
    },
    xaxis: {
      title: 'Total Quantity Used'
    },
    yaxis: {
      automargin: true
    },
    plot_bgcolor: 'rgba(0,0,0,0)',
    paper_bgcolor: 'rgba(0,0,0,0)'
  };

  Plotly.newPlot('topDemandChart', demandData, demandLayout, {
    responsive: true
  });
</script>

</html>