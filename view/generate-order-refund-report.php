<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';
include '../commons/fpdf186/fpdf.php';

class OrderRefundReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Order Refund Report", 0, 1, "C");
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
        $this->SetFont("Arial", "B", 10);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(10, 10, "#", 1, 0, "C", true);
        $this->Cell(30, 10, "Order No", 1, 0, "C", true);
        $this->Cell(55, 10, "Company Name", 1, 0, "C", true);
        $this->Cell(35, 10, "Refund Amount", 1, 0, "C", true);
        $this->Cell(30, 10, "Requested On", 1, 0, "C", true);
        $this->Cell(30, 10, "Status", 1, 1, "C", true);
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

$orderObj = new Order();
$orderRefundResult = $orderObj->getAllOrderRefunds();

// ---------------- FILTER: PROCESSED REFUNDS ONLY ----------------
$refundRows = [];
$totalProcessedRefunds = 0;

while ($row = $orderRefundResult->fetch_assoc()) {

    if ($row["refund_status"] != "Processed") {
        continue;
    }

    $refundDate = date('Y-m-d', strtotime($row["requested_at"]));

    if ($refundDate < $startDate || $refundDate > $endDate) {
        continue;
    }

    $refundRows[] = $row;
    $totalProcessedRefunds += $row["refund_amount"];
}

date_default_timezone_set('Asia/Colombo');
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF INIT ----------------
$pdf = new OrderRefundReport("P", "mm", "A4");
$pdf->startDate = $startDate;
$pdf->endDate = $endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("Order Refund Report - $startDate to $endDate");
$pdf->AddPage();

// ---------------- META INFO ----------------
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ---------------- TABLE ----------------
$pdf->TableHeader();
$pdf->SetFont("Arial", "", 9);

$fill = false;
$count = 1;

if (count($refundRows) > 0) {

    foreach ($refundRows as $row) {

        if ($pdf->GetY() > 270) {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial", "", 9);
        }

        $companyName = iconv('UTF-8', 'windows-1252', $row["company_name"]);
        $requestedOn = date('Y-m-d', strtotime($row["requested_at"]));

        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $pdf->Cell(10, 8, $count++, 1, 0, "C", true);
        $pdf->Cell(30, 8, "ORD" . $row["order_id"], 1, 0, "C", true);
        $pdf->Cell(55, 8, $companyName, 1, 0, "L", true);
        $pdf->Cell(35, 8, number_format($row["refund_amount"], 2), 1, 0, "R", true);
        $pdf->Cell(30, 8, $requestedOn, 1, 0, "C", true);
        $pdf->Cell(30, 8, $row["refund_status"], 1, 1, "C", true);

        $fill = !$fill;
    }

    // Total row
    $pdf->SetFont("Arial", "B", 10);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(95, 9, "Total Processed Refunds", 1, 0, "R", true);
    $pdf->Cell(35, 9, "Rs " . number_format($totalProcessedRefunds, 2), 1, 0, "R", true);
    $pdf->Cell(30, 9, "", 1, 0, "C", true);
    $pdf->Cell(30, 9, "", 1, 1, "C", true);

} else {
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(190, 10, "No processed refunds found for this period.", 1, 1, "C");
}

// ---------------- SUMMARY ----------------
$pdf->Ln(5);
$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0, 6, "Total Processed Refund Records: " . count($refundRows) . " | Total Amount: Rs " . number_format($totalProcessedRefunds, 2), 0, 1, "L");

// ---------------- FOOTER NOTES ----------------
$pdf->Ln(5);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a physical signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidentiality Notice: This document contains internal system data.", 0, "C");

// ---------------- OUTPUT ----------------
if (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output("I", "Order_Refund_Report_Fabric_Apparel_" . $startDate . "_to_" . $endDate . ".pdf");