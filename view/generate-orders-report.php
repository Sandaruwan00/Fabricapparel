<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';
include '../commons/fpdf186/fpdf.php';

class OrdersReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Orders Report", 0, 1, "C");
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
        $this->SetFillColor(200, 200, 200);
        $this->Cell(20, 9, "Order No", 1, 0, "C", true);
        $this->Cell(45, 9, "Company", 1, 0, "C", true);
        $this->Cell(35, 9, "Contact Person", 1, 0, "C", true);
        $this->Cell(25, 9, "Order Date", 1, 0, "C", true);
        $this->Cell(25, 9, "Total (Rs)", 1, 0, "C", true);
        $this->Cell(25, 9, "Due Date", 1, 0, "C", true);
        $this->Cell(15, 9, "Status", 1, 1, "C", true);
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
$orderResult = $orderObj->getAllOrders();

// ---------------- FILTER + GROUP BY STATUS ----------------
$orderRows = [];
$statusSummary = []; // status => ["count" => 0, "total" => 0]
$grandTotalAmount = 0;
$grandTotalCount = 0;

while ($row = $orderResult->fetch_assoc()) {

    $orderDate = date('Y-m-d', strtotime($row["order_date"]));

    if ($orderDate < $startDate || $orderDate > $endDate) {
        continue;
    }

    $status = $row["status_name"];

    if (!isset($statusSummary[$status])) {
        $statusSummary[$status] = ["count" => 0, "total" => 0];
    }

    $statusSummary[$status]["count"]++;
    $statusSummary[$status]["total"] += $row["total_amount"];

    $grandTotalAmount += $row["total_amount"];
    $grandTotalCount++;

    $orderRows[] = $row;
}

// Sort orders by order date for the detail listing
usort($orderRows, function ($a, $b) {
    return strtotime($a["order_date"]) <=> strtotime($b["order_date"]);
});

date_default_timezone_set('Asia/Colombo');
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF INIT ----------------
$pdf = new OrdersReport("P", "mm", "A4");
$pdf->startDate = $startDate;
$pdf->endDate = $endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("Orders Report - $startDate to $endDate");
$pdf->AddPage();

// ---------------- META INFO ----------------
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ---------------- STATUS SUMMARY TABLE ----------------
$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(0, 8, "Orders by Status", 0, 1, "L");
$pdf->Ln(1);

if (count($statusSummary) > 0) {

    $pdf->SetFont("Arial", "B", 10);
    $pdf->SetFillColor(200, 200, 200);
    $pdf->Cell(70, 9, "Status", 1, 0, "C", true);
    $pdf->Cell(50, 9, "No. of Orders", 1, 0, "C", true);
    $pdf->Cell(70, 9, "Total Amount (Rs)", 1, 1, "C", true);

    $pdf->SetFont("Arial", "", 9);
    $fill = false;

    foreach ($statusSummary as $status => $data) {
        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $pdf->Cell(70, 8, $status, 1, 0, "L", true);
        $pdf->Cell(50, 8, $data["count"], 1, 0, "C", true);
        $pdf->Cell(70, 8, number_format($data["total"], 2), 1, 1, "R", true);
        $fill = !$fill;
    }

    $pdf->SetFont("Arial", "B", 10);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(70, 9, "Grand Total", 1, 0, "L", true);
    $pdf->Cell(50, 9, $grandTotalCount, 1, 0, "C", true);
    $pdf->Cell(70, 9, "Rs " . number_format($grandTotalAmount, 2), 1, 1, "R", true);

} else {
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(190, 10, "No orders found for this period.", 1, 1, "C");
}

// ---------------- DETAILED ORDER RECORDS ----------------
if (count($orderRows) > 0) {

    $pdf->Ln(8);
    $pdf->SetFont("Arial", "B", 12);
    $pdf->Cell(0, 8, "Order Records", 0, 1, "L");
    $pdf->Ln(1);

    $pdf->TableHeader();
    $pdf->SetFont("Arial", "", 8);

    $fill = false;

    foreach ($orderRows as $row) {

        if ($pdf->GetY() > 265) {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial", "", 8);
        }

        $company = iconv('UTF-8', 'windows-1252', $row["company_name"]);
        $contact = iconv('UTF-8', 'windows-1252', $row["contact_name"]);
        $orderDate = date('Y-m-d', strtotime($row["order_date"]));
        $dueDate = $row["expected_delivery_date"] ? date('Y-m-d', strtotime($row["expected_delivery_date"])) : "-";
        $status = $row["status_name"];

        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $pdf->Cell(20, 8, "ORD" . $row["order_id"], 1, 0, "C", true);
        $pdf->Cell(45, 8, $company, 1, 0, "L", true);
        $pdf->Cell(35, 8, $contact, 1, 0, "L", true);
        $pdf->Cell(25, 8, $orderDate, 1, 0, "C", true);
        $pdf->Cell(25, 8, number_format($row["total_amount"], 2), 1, 0, "R", true);
        $pdf->Cell(25, 8, $dueDate, 1, 0, "C", true);
        $pdf->Cell(15, 8, $status, 1, 1, "C", true);

        $fill = !$fill;
    }
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
$pdf->Output("I", "Orders_Report_Fabric_Apparel_" . $startDate . "_to_" . $endDate . ".pdf");