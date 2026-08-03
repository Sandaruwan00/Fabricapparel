<?php
include_once '../commons/session.php';
include_once '../model/finance_model.php';
include '../commons/fpdf186/fpdf.php';

$userrow = $_SESSION["user"];
include_once '../model/permission_model.php';
$permissionObj = new Permission();
if (!$permissionObj->hasPermission($userrow["user_id"], 15)) {
    header("Location: access_denied.php");
    exit();
}

class POPaymentReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Purchase Order Payments Report", 0, 1, "C");
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

    function SectionTitle($title, $fillR, $fillG, $fillB)
    {
        $this->SetFont("Arial", "B", 12);
        $this->SetFillColor($fillR, $fillG, $fillB);
        $this->Cell(0, 9, $title, 1, 1, "L", true);
        $this->Ln(1);
    }

    function TableHeader()
    {
        $this->SetFont("Arial", "B", 9);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(10, 9, "#", 1, 0, "C", true);
        $this->Cell(20, 9, "PO ID", 1, 0, "C", true);
        $this->Cell(45, 9, "Supplier", 1, 0, "C", true);
        $this->Cell(60, 9, "Supplier Email", 1, 0, "C", true);
        $this->Cell(30, 9, "Amount (Rs)", 1, 0, "C", true);
        $this->Cell(25, 9, "Date", 1, 1, "C", true);
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

$financeObj = new Finance();
$poPayments = $financeObj->getAllPOPayments();

// ---------------- SPLIT INTO PAID / PENDING ----------------
$paidRows = [];
$pendingRows = [];
$totalPaid = 0;
$totalPending = 0;

while ($row = $poPayments->fetch_assoc()) {

    $paymentDate = date('Y-m-d', strtotime($row["po_payment_date"]));

    if ($paymentDate < $startDate || $paymentDate > $endDate) {
        continue;
    }

    if ($row["po_payment_status"] == "Paid") {
        $paidRows[] = $row;
        $totalPaid += $row["po_amount"];
    } elseif ($row["po_payment_status"] == "Pending") {
        $pendingRows[] = $row;
        $totalPending += $row["po_amount"];
    }
}

date_default_timezone_set('Asia/Colombo');
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF INIT ----------------
$pdf = new POPaymentReport("P", "mm", "A4");
$pdf->startDate = $startDate;
$pdf->endDate = $endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("PO Payments Report - $startDate to $endDate");
$pdf->AddPage();

// ---------------- META INFO ----------------
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ---------------- SUMMARY CARDS ----------------
$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(220, 245, 220);
$pdf->Cell(95, 10, "Total Paid Amount", 1, 0, "L", true);
$pdf->Cell(95, 10, "Rs " . number_format($totalPaid, 2), 1, 1, "R", true);

$pdf->SetFillColor(255, 243, 205);
$pdf->Cell(95, 10, "Total Pending Amount", 1, 0, "L", true);
$pdf->Cell(95, 10, "Rs " . number_format($totalPending, 2), 1, 1, "R", true);

$pdf->Ln(8);

// ---------------- PAID RECORDS ----------------
$pdf->SectionTitle("Paid Payments (" . count($paidRows) . " records)", 200, 230, 200);

if (count($paidRows) > 0) {

    $pdf->TableHeader();
    $pdf->SetFont("Arial", "", 8);
    $fill = false;
    $count = 1;

    foreach ($paidRows as $row) {

        if ($pdf->GetY() > 260) {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial", "", 8);
        }

        $supplier = iconv('UTF-8', 'windows-1252', $row["supplier_name"]);
        $email = iconv('UTF-8', 'windows-1252', $row["supplier_email"]);
        $paymentDate = date('Y-m-d', strtotime($row["po_payment_date"]));

        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $pdf->Cell(10, 8, $count++, 1, 0, "C", true);
        $pdf->Cell(20, 8, "PO" . $row["po_id"], 1, 0, "C", true);
        $pdf->Cell(45, 8, $supplier, 1, 0, "L", true);
        $pdf->Cell(60, 8, $email, 1, 0, "L", true);
        $pdf->Cell(30, 8, number_format($row["po_amount"], 2), 1, 0, "R", true);
        $pdf->Cell(25, 8, $paymentDate, 1, 1, "C", true);

        $fill = !$fill;
    }

    $pdf->SetFont("Arial", "B", 9);
    $pdf->SetFillColor(220, 245, 220);
    $pdf->Cell(135, 9, "Total Paid", 1, 0, "R", true);
    $pdf->Cell(30, 9, number_format($totalPaid, 2), 1, 0, "R", true);
    $pdf->Cell(25, 9, "", 1, 1, "C", true);

} else {
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(190, 10, "No paid PO payments found for this period.", 1, 1, "C");
}

$pdf->Ln(8);

// ---------------- PENDING RECORDS ----------------
if ($pdf->GetY() > 240) {
    $pdf->AddPage();
}

$pdf->SectionTitle("Pending Payments (" . count($pendingRows) . " records)", 255, 236, 179);

if (count($pendingRows) > 0) {

    $pdf->TableHeader();
    $pdf->SetFont("Arial", "", 8);
    $fill = false;
    $count = 1;

    foreach ($pendingRows as $row) {

        if ($pdf->GetY() > 260) {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial", "", 8);
        }

        $supplier = iconv('UTF-8', 'windows-1252', $row["supplier_name"]);
        $email = iconv('UTF-8', 'windows-1252', $row["supplier_email"]);
        $paymentDate = date('Y-m-d', strtotime($row["po_payment_date"]));

        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $pdf->Cell(10, 8, $count++, 1, 0, "C", true);
        $pdf->Cell(20, 8, "PO" . $row["po_id"], 1, 0, "C", true);
        $pdf->Cell(45, 8, $supplier, 1, 0, "L", true);
        $pdf->Cell(60, 8, $email, 1, 0, "L", true);
        $pdf->Cell(30, 8, number_format($row["po_amount"], 2), 1, 0, "R", true);
        $pdf->Cell(25, 8, $paymentDate, 1, 1, "C", true);

        $fill = !$fill;
    }

    $pdf->SetFont("Arial", "B", 9);
    $pdf->SetFillColor(255, 243, 205);
    $pdf->Cell(135, 9, "Total Pending", 1, 0, "R", true);
    $pdf->Cell(30, 9, number_format($totalPending, 2), 1, 0, "R", true);
    $pdf->Cell(25, 9, "", 1, 1, "C", true);

} else {
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(190, 10, "No pending PO payments found for this period.", 1, 1, "C");
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
$pdf->Output("I", "PO_Payments_Report_Fabric_Apparel_" . $startDate . "_to_" . $endDate . ".pdf");