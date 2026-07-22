<?php
// ---------------- INCLUDE REQUIRED FILES ----------------

// Session check
include_once '../commons/session.php';

// Stock model – used to fetch stock request data from database
include_once '../model/stock_model.php';

// FPDF library – used to generate PDF documents
include '../commons/fpdf186/fpdf.php';


// ---------------- PDF CLASS DEFINITION ----------------

// Extend FPDF to customize header, footer, and table layouts
class StockRequestReport extends FPDF
{
    // ----------- PAGE HEADER -----------
    function Header()
    {
        // Company logo (image path, X, Y, width)
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);

        // Company name
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        // Report subtitle
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Stock Material Request Report", 0, 1, "C");

        // Space after header
        $this->Ln(10);
    }

    // ----------- PAGE FOOTER -----------
    function Footer()
    {
        // Move cursor to 20mm from bottom
        $this->SetY(-20);

        // Page number
        $this->SetFont("Arial", "I", 8);
        $this->Cell(0, 5, "Page " . $this->PageNo() . " / {nb}", 0, 1, "C");

        // Footer text
        $this->SetFont("Arial", "I", 7);
        $this->Cell(0, 5, "Fabric Apparel (PVT) LTD | www.fabricapparel.com | confidential", 0, 0, "C");
    }

    // ----------- SECTION TITLE -----------
    function SectionTitle($label)
    {
        $this->SetFont("Arial", "B", 12);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 8, $label, 0, 1, "L", true);
        $this->Ln(2);
    }

    // ----------- TABLE HEADER: MATERIAL REQUESTS -----------
    function TableHeaderRequests()
    {
        $this->SetFont("Arial", "B", 9);
        $this->SetFillColor(200, 200, 200);

        $this->Cell(20, 8, "Request ID", 1, 0, "C", true);
        $this->Cell(30, 8, "Request Date", 1, 0, "C", true);
        $this->Cell(25, 8, "Plan ID", 1, 0, "C", true);
        $this->Cell(25, 8, "Order ID", 1, 0, "C", true);
        $this->Cell(60, 8, "Company Name", 1, 0, "C", true);
        $this->Cell(30, 8, "Status", 1, 1, "C", true);
    }
}


// ---------------- READ & VALIDATE DATE RANGE ----------------

$start_date = isset($_POST["start_date"]) ? trim($_POST["start_date"]) : "";
$end_date   = isset($_POST["end_date"]) ? trim($_POST["end_date"]) : "";

// Both dates are required (matches the "required" attributes on the form)
if ($start_date === "" || $end_date === "") {
    die("Start Date and End Date are required to generate this report.");
}

$startTimestamp = strtotime($start_date);
$endTimestamp   = strtotime($end_date);

if ($startTimestamp === false || $endTimestamp === false) {
    die("Invalid date format supplied.");
}

if ($startTimestamp > $endTimestamp) {
    die("Start Date cannot be later than End Date.");
}


// ---------------- FETCH DATA FROM DATABASE ----------------

// Create Stock object
$stockObj = new Stock();

$requestsResult = $stockObj->getAllStockRequests();

// Current date for report
date_default_timezone_set('Asia/Colombo');
$date     = date("Y-m-d");
$dateTime = date("Y-m-d H:i:s");


// ---------------- PDF GENERATION ----------------

// Create PDF object (Portrait, mm units, A4 size)
$pdf = new StockRequestReport("P", "mm", "A4");

// Enable total page number alias
$pdf->AliasNbPages();

// Set document title
$pdf->SetTitle("Stock Material Request Report - $date");

// Add first page
$pdf->AddPage();


// ---------------- REPORT META INFO ----------------

$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Cell(0, 8, "Report Period : " . date("Y-m-d", $startTimestamp) . "  to  " . date("Y-m-d", $endTimestamp), 0, 1, "L");
$pdf->Ln(2);

$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);


$pdf->SectionTitle("Material Requests");

$pdf->TableHeaderRequests();
$pdf->SetFont("Arial", "", 8);

$count = 1;
$fill = false;

$totalRequests = 0;
$pending = 0;
$completed = 0;

while ($row = $requestsResult->fetch_assoc()) {

    // Skip rows that fall outside the selected date range
    $rowTimestamp = strtotime($row["request_date"]);
    if ($rowTimestamp === false || $rowTimestamp < $startTimestamp || $rowTimestamp > strtotime($end_date . " 23:59:59")) {
        continue;
    }

    if ($pdf->GetY() > 260) {
        $pdf->AddPage();
        $pdf->SectionTitle("Material Requests (Continued)");
        $pdf->TableHeaderRequests();
        $pdf->SetFont("Arial", "", 8);
    }

    $status = $row["stock_request_status"];

    if ($status == "Pending") {
        $pending++;
    } else {
        $completed++;
    }

    $totalRequests++;

    if ($fill)
        $pdf->SetFillColor(245, 245, 245);
    else
        $pdf->SetFillColor(255, 255, 255);

    $requestId   = iconv('UTF-8', 'windows-1252', $row["stock_request_id"]);
    $requestDate = iconv('UTF-8', 'windows-1252', $row["request_date"]);
    $planId      = iconv('UTF-8', 'windows-1252', "PLAN" . $row["plan_id"]);
    $orderId     = iconv('UTF-8', 'windows-1252', "ORD" . $row["order_id"]);
    $companyName = iconv('UTF-8', 'windows-1252', $row["company_name"]);
    $statusText  = iconv('UTF-8', 'windows-1252', $status);

    $pdf->Cell(20, 8, $requestId, 1, 0, "C", true);
    $pdf->Cell(30, 8, $requestDate, 1, 0, "C", true);
    $pdf->Cell(25, 8, $planId, 1, 0, "C", true);
    $pdf->Cell(25, 8, $orderId, 1, 0, "C", true);
    $pdf->Cell(60, 8, $companyName, 1, 0, "L", true);
    $pdf->Cell(30, 8, $statusText, 1, 1, "C", true);

    $fill = !$fill;
}

$pdf->Ln(3);

$pdf->SetFont("Arial", "B", 10);

$pdf->Cell(0, 6, "Request Summary", 0, 1);

$pdf->SetFont("Arial", "", 9);

$pdf->Cell(0, 6, "Total Requests : " . $totalRequests, 0, 1);
$pdf->Cell(0, 6, "Pending        : " . $pending, 0, 1);
$pdf->Cell(0, 6, "Completed      : " . $completed, 0, 1);

// ---------------- FOOTER NOTES ----------------

$pdf->Ln(8);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a physical signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidentiality Notice: This document contains internal system data.", 0, "C");


// ---------------- OUTPUT PDF ----------------

// Clear output buffer to avoid PDF corruption
ob_end_clean();

// Display PDF in browser
$pdf->Output("I", "Stock_Material_Request_Report_Fabric_Apparel_$dateTime.pdf");