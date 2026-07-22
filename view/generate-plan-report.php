<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';
include_once '../model/planning_model.php';
include_once '../model/stock_model.php';
include '../commons/fpdf186/fpdf.php';

class PlanReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Plan Management Report", 0, 1, "C");
        $this->SetFont("Arial", "", 9);
        $this->Cell(0, 6, "Period: " . $this->startDate . "  to  " . $this->endDate, 0, 1, "C");
        $this->Ln(6);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont("Arial", "I", 8);
        $this->Cell(0, 5, "Page " . $this->PageNo() . " / {nb}", 0, 1, "C");
        $this->SetFont("Arial", "I", 7);
        $this->Cell(0, 5, "Fabric Apparel (PVT) LTD | www.fabricapparel.com | confidential", 0, 0, "C");
    }

    function SectionTitle($title)
    {
        $this->SetFont("Arial", "B", 12);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 9, $title, 1, 1, "L", true);
        $this->Ln(1);
    }
}

date_default_timezone_set("Asia/Colombo");

// ---------------- INPUT ----------------
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate   = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
    $startDate = date('Y-m-01');
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
    $endDate = date('Y-m-d');
}

$startDateTime = $startDate . " 00:00:00";
$endDateTime   = $endDate . " 23:59:59";

// ---------------- MODEL-BASED DATA (existing methods) ----------------

$orderObj = new Order();
$planObj = new Planning();

// Confirmed orders (not yet planned) - current snapshot, not date filtered
$confirmedOrdersResult = $orderObj->getAllConfirmedOrders();
$confirmedOrdersCount = $confirmedOrdersResult->num_rows;

// All plans (raw, unfiltered list - will be filtered below using stock request dates)
$planResult = $planObj->getAllPlans();
$allPlanRows = [];

while ($row = $planResult->fetch_assoc()) {
    $allPlanRows[$row["plan_id"]] = $row;
}

// ---------------- RAW SQL DATA (aggregate stock request info, date filtered) ----------------
// NOTE: Replace this with your actual DB connection (e.g. include your
// existing commons/db_connect.php and use its $conn variable) if different.
$conn = new mysqli("localhost", "root", "", "fabricapparel");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$escStart = $conn->real_escape_string($startDateTime);
$escEnd   = $conn->real_escape_string($endDateTime);

// Stock Requests by Status (within date range)
$stockRequestStatusData = [
    "Pending" => 0,
    "Issued" => 0,
    "Hold" => 0,
    "Rejected" => 0
];

$stockRequestStatusQuery = "
    SELECT stock_request_status, COUNT(*) AS total
    FROM stock_requests
    WHERE request_date BETWEEN '$escStart' AND '$escEnd'
    GROUP BY stock_request_status
";

$stockRequestStatusResult = $conn->query($stockRequestStatusQuery);

if ($stockRequestStatusResult) {
    while ($row = $stockRequestStatusResult->fetch_assoc()) {
        if (isset($stockRequestStatusData[$row["stock_request_status"]])) {
            $stockRequestStatusData[$row["stock_request_status"]] = (int)$row["total"];
        }
    }
}

$totalStockRequests = array_sum($stockRequestStatusData);

// Stock Requested Quantities by Category (within date range)
$categoryQuery = "
    SELECT
        sc.stock_category_name AS category_name,
        su.stock_unit_short_name AS unit_short_name,
        SUM(sri.requested_qty) AS total_qty,
        COUNT(sri.stock_request_item_id) AS item_count
    FROM stock_request_items sri
    INNER JOIN stock_requests sr ON sri.stock_request_id = sr.stock_request_id
    INNER JOIN stock_items si ON sri.stock_item_id = si.stock_item_id
    INNER JOIN stock_categories sc ON si.stock_category_id = sc.stock_category_id
    INNER JOIN stock_units su ON si.stock_unit_id = su.stock_unit_id
    WHERE sr.request_date BETWEEN '$escStart' AND '$escEnd'
    GROUP BY sc.stock_category_id, su.stock_unit_id
    ORDER BY total_qty DESC
";

$categoryResult = $conn->query($categoryQuery);
$categoryRows = [];

if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categoryRows[] = $row;
    }
}

// Stock Request Items by Status (within date range)
$itemStatusData = [
    "Pending" => 0,
    "Issued" => 0,
    "Hold" => 0,
    "Rejected" => 0
];

$itemStatusQuery = "
    SELECT sri.stock_request_item_status, COUNT(*) AS total
    FROM stock_request_items sri
    INNER JOIN stock_requests sr ON sri.stock_request_id = sr.stock_request_id
    WHERE sr.request_date BETWEEN '$escStart' AND '$escEnd'
    GROUP BY sri.stock_request_item_status
";

$itemStatusResult = $conn->query($itemStatusQuery);

if ($itemStatusResult) {
    while ($row = $itemStatusResult->fetch_assoc()) {
        if (isset($itemStatusData[$row["stock_request_item_status"]])) {
            $itemStatusData[$row["stock_request_item_status"]] = (int)$row["total"];
        }
    }
}

// Per-plan stock request status, restricted to requests within the date range
$planStockInfo = [];

$planStockQuery = "
    SELECT
        sr.plan_id,
        sr.stock_request_status,
        sr.request_date,
        COUNT(sri.stock_request_item_id) AS item_count
    FROM stock_requests sr
    LEFT JOIN stock_request_items sri ON sri.stock_request_id = sr.stock_request_id
    WHERE sr.request_date BETWEEN '$escStart' AND '$escEnd'
    GROUP BY sr.stock_request_id
";

$planStockResult = $conn->query($planStockQuery);

if ($planStockResult) {
    while ($row = $planStockResult->fetch_assoc()) {
        $planStockInfo[$row["plan_id"]] = $row;
    }
}

$conn->close();

// ---------------- FILTER PLANS TO THOSE WITH A STOCK REQUEST IN RANGE ----------------
$planRows = [];
$approvedPlansCount = 0;
$pendingPlansCount = 0;
$rejectedPlansCount = 0;

foreach ($planStockInfo as $planId => $stockInfo) {

    if (!isset($allPlanRows[$planId])) {
        continue;
    }

    $planRow = $allPlanRows[$planId];

    switch ($planRow["plan_status"]) {
        case "Approved":
            $approvedPlansCount++;
            break;
        case "Pending":
            $pendingPlansCount++;
            break;
        case "Rejected":
            $rejectedPlansCount++;
            break;
    }

    $planRows[] = $planRow;
}

$totalPlansCount = count($planRows);

$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF INIT ----------------
$pdf = new PlanReport("P", "mm", "A4");
$pdf->startDate = $startDate;
$pdf->endDate = $endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("Plan Management Report - $startDate to $endDate");
$pdf->AddPage();

// ---------------- META INFO ----------------
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ---------------- PLAN SUMMARY ----------------
$pdf->SectionTitle("Plan Summary");

$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(47, 10, "Confirmed Orders", 1, 0, "C", true);
$pdf->Cell(47, 10, "Approved Plans", 1, 0, "C", true);
$pdf->Cell(47, 10, "Pending Plans", 1, 0, "C", true);
$pdf->Cell(49, 10, "Rejected Plans", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 12);
$pdf->Cell(47, 10, $confirmedOrdersCount, 1, 0, "C", true);
$pdf->Cell(47, 10, $approvedPlansCount, 1, 0, "C", true);
$pdf->Cell(47, 10, $pendingPlansCount, 1, 0, "C", true);
$pdf->Cell(49, 10, $rejectedPlansCount, 1, 1, "C", true);

$pdf->Ln(3);

$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0, 6, "Total Plans (with stock requests in period): $totalPlansCount", 0, 1, "L");

$pdf->Ln(6);

// ---------------- STOCK REQUEST STATUS SUMMARY ----------------
$pdf->SectionTitle("Stock Requests by Status");

$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(95, 9, "Status", 1, 0, "C", true);
$pdf->Cell(95, 9, "No. of Requests", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 9);
$fill = false;

foreach ($stockRequestStatusData as $status => $count) {
    $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
    $pdf->Cell(95, 8, $status, 1, 0, "L", true);
    $pdf->Cell(95, 8, $count, 1, 1, "C", true);
    $fill = !$fill;
}

$pdf->SetFont("Arial", "B", 9);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(95, 9, "Total", 1, 0, "L", true);
$pdf->Cell(95, 9, $totalStockRequests, 1, 1, "C", true);

$pdf->Ln(6);

// ---------------- STOCK REQUEST ITEMS BY STATUS ----------------
$pdf->SectionTitle("Stock Request Items by Status");

$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(95, 9, "Status", 1, 0, "C", true);
$pdf->Cell(95, 9, "No. of Items", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 9);
$fill = false;

foreach ($itemStatusData as $status => $count) {
    $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
    $pdf->Cell(95, 8, $status, 1, 0, "L", true);
    $pdf->Cell(95, 8, $count, 1, 1, "C", true);
    $fill = !$fill;
}

$pdf->Ln(6);

// ---------------- STOCK REQUESTED BY CATEGORY ----------------


$pdf->SectionTitle("Stock Requested Quantities by Category");

if (count($categoryRows) > 0) {

    $pdf->SetFont("Arial", "B", 9);
    $pdf->SetFillColor(200, 200, 200);
    $pdf->Cell(90, 9, "Category", 1, 0, "C", true);
    $pdf->Cell(30, 9, "Item Count", 1, 0, "C", true);
    $pdf->Cell(70, 9, "Total Qty", 1, 1, "C", true);

    $pdf->SetFont("Arial", "", 9);
    $fill = false;

    foreach ($categoryRows as $row) {
        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $pdf->Cell(90, 8, $row["category_name"], 1, 0, "L", true);
        $pdf->Cell(30, 8, $row["item_count"], 1, 0, "C", true);
        $pdf->Cell(70, 8, number_format($row["total_qty"], 2) . " " . $row["unit_short_name"], 1, 1, "R", true);
        $fill = !$fill;
    }
} else {
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(190, 10, "No stock request items found for this period.", 1, 1, "C");
}

// ---------------- DETAILED PLAN RECORDS ----------------
$pdf->AddPage();
$pdf->SectionTitle("Plan Records");

$pdf->SetFont("Arial", "B", 9);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(20, 9, "Plan No", 1, 0, "C", true);
$pdf->Cell(20, 9, "Order No", 1, 0, "C", true);
$pdf->Cell(45, 9, "Company", 1, 0, "C", true);
$pdf->Cell(25, 9, "Delivery Date", 1, 0, "C", true);
$pdf->Cell(20, 9, "Plan Status", 1, 0, "C", true);
$pdf->Cell(30, 9, "Stock Req. Status", 1, 0, "C", true);
$pdf->Cell(30, 9, "No. of Items", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 8);
$fill = false;

if (count($planRows) > 0) {

    foreach ($planRows as $row) {

        if ($pdf->GetY() > 265) {
            $pdf->AddPage();
            $pdf->SetFont("Arial", "B", 9);
            $pdf->SetFillColor(200, 200, 200);
            $pdf->Cell(20, 9, "Plan No", 1, 0, "C", true);
            $pdf->Cell(20, 9, "Order No", 1, 0, "C", true);
            $pdf->Cell(45, 9, "Company", 1, 0, "C", true);
            $pdf->Cell(25, 9, "Delivery Date", 1, 0, "C", true);
            $pdf->Cell(20, 9, "Plan Status", 1, 0, "C", true);
            $pdf->Cell(30, 9, "Stock Req. Status", 1, 0, "C", true);
            $pdf->Cell(30, 9, "No. of Items", 1, 1, "C", true);
            $pdf->SetFont("Arial", "", 8);
        }

        $company = iconv('UTF-8', 'windows-1252', $row["company_name"]);
        $deliveryDate = $row["expected_delivery_date"] ? date('Y-m-d', strtotime($row["expected_delivery_date"])) : "-";

        $stockStatus = "-";
        $itemCount = 0;

        if (isset($planStockInfo[$row["plan_id"]])) {
            $stockStatus = $planStockInfo[$row["plan_id"]]["stock_request_status"];
            $itemCount = $planStockInfo[$row["plan_id"]]["item_count"];
        }

        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $pdf->Cell(20, 8, "PLAN" . $row["plan_id"], 1, 0, "C", true);
        $pdf->Cell(20, 8, "ORD" . $row["order_id"], 1, 0, "C", true);
        $pdf->Cell(45, 8, $company, 1, 0, "L", true);
        $pdf->Cell(25, 8, $deliveryDate, 1, 0, "C", true);
        $pdf->Cell(20, 8, $row["plan_status"], 1, 0, "C", true);
        $pdf->Cell(30, 8, $stockStatus, 1, 0, "C", true);
        $pdf->Cell(30, 8, $itemCount, 1, 1, "C", true);

        $fill = !$fill;
    }
} else {
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(190, 10, "No plans with stock requests found for this period.", 1, 1, "C");
}

// ---------------- FOOTER NOTES ----------------
$pdf->Ln(8);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a physical signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidentiality Notice: This document contains internal system data.", 0, "C");

// ---------------- OUTPUT ----------------
if (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output("I", "Plan_Report_Fabric_Apparel_" . $startDate . "_to_" . $endDate . ".pdf");