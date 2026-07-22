<?php
include_once '../commons/session.php';
include '../model/stock_model.php';
include '../commons/fpdf186/fpdf.php';

class StockRequestReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Stock Material Request Report", 0, 1, "C");
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

    function TableHeader()
    {
        $this->SetFont("Arial", "B", 9);
        $this->SetFillColor(240, 240, 240);
        $this->Cell(20, 8, "Req ID", 1, 0, "C", true);
        $this->Cell(30, 8, "Req Date", 1, 0, "C", true);
        $this->Cell(22, 8, "Plan ID", 1, 0, "C", true);
        $this->Cell(22, 8, "Order ID", 1, 0, "C", true);
        $this->Cell(66, 8, "Company Name", 1, 0, "C", true);
        $this->Cell(30, 8, "Status", 1, 1, "C", true);
    }
}

// ---------------- INPUT ----------------
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate   = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
    $startDate = date('Y-m-01');
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
    $endDate = date('Y-m-d');
}

$stockObj = new Stock();
$requestsResult = $stockObj->getAllStockRequests();

date_default_timezone_set('Asia/Colombo');
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF INIT ----------------
$pdf = new StockRequestReport("P", "mm", "A4");
$pdf->startDate = $startDate;
$pdf->endDate = $endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("Stock Material Request Report - $startDate to $endDate");
$pdf->AddPage();

// ---------------- META INFO ----------------
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ---------------- REQUESTS TABLE ----------------
$pdf->TableHeader();
$pdf->SetFont("Arial", "", 8);

$fill = false;
$totalRequests = 0;
$pending = 0;
$completed = 0;

while ($row = $requestsResult->fetch_assoc()) {

    $requestDate = date('Y-m-d', strtotime($row["request_date"]));

    if ($requestDate < $startDate || $requestDate > $endDate) {
        continue;
    }

    if ($pdf->GetY() > 270) {
        $pdf->AddPage();
        $pdf->TableHeader();
        $pdf->SetFont("Arial", "", 8);
    }

    $status = $row["stock_request_status"];

    if ($status == "Pending") {
        $pending++;
    } else {
        $completed++;
    }

    $totalRequests++;

    $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
    $pdf->Cell(20, 7, $row["stock_request_id"], 1, 0, "C", true);
    $pdf->Cell(30, 7, $row["request_date"], 1, 0, "C", true);
    $pdf->Cell(22, 7, "PLAN" . $row["plan_id"], 1, 0, "C", true);
    $pdf->Cell(22, 7, "ORD" . $row["order_id"], 1, 0, "C", true);
    $pdf->Cell(66, 7, iconv('UTF-8', 'windows-1252', $row["company_name"]), 1, 0, "L", true);
    $pdf->Cell(30, 7, $status, 1, 1, "C", true);

    $fill = !$fill;
}

if ($totalRequests == 0) {
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(190, 10, "No stock material requests found for this period.", 1, 1, "C");
}

// ---------------- SUMMARY ----------------
$pdf->Ln(6);
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

// ---------------- OUTPUT ----------------
if (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output("I", "Stock_Material_Request_Report_Fabric_Apparel_" . $startDate . "_to_" . $endDate . ".pdf");