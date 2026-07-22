<?php
include_once '../commons/session.php';
include_once '../model/finance_model.php';
include_once '../model/order_model.php';
include '../commons/fpdf186/fpdf.php';

class FinanceReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Finance Management Report", 0, 1, "C");
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
        $this->SetFillColor(230, 230, 230);
        $this->Cell(0, 8, $title, 1, 1, "L", true);
        $this->Ln(2);
    }

    function TableHeader($col1, $col2)
    {
        $this->SetFont("Arial", "B", 10);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(120, 10, $col1, 1, 0, "C", true);
        $this->Cell(60, 10, $col2, 1, 1, "C", true);
    }
}

// ---------------- INPUT ----------------
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate   = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

// Basic validation
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
    $startDate = date('Y-m-01');
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
    $endDate = date('Y-m-d');
}

$financeObj = new Finance();
$orderObj = new Order();

$totalIncome = 0;
$totalExpenses = 0;

// ---------------- INCOME (Approved Payments) ----------------
$incomeResult = $financeObj->getAllApprovedPayments();

while ($row = $incomeResult->fetch_assoc()) {
    $paymentDate = date('Y-m-d', strtotime($row["payment_datetime"]));

    if ($paymentDate >= $startDate && $paymentDate <= $endDate) {
        $totalIncome += $row["amount"];
    }
}

// ---------------- EXPENSE CATEGORY DATA ----------------
$expenseCategoryData = [
    "Fuel" => 0,
    "Salary" => 0,
    "Vehicle Maintenance" => 0,
    "Office Bills" => 0,
    "Transport" => 0,
    "Refund" => 0,
    "Order Refunds" => 0,
    "PO Payments" => 0,
    "Other" => 0
];

// Approved Expenses
$expensesResult = $financeObj->getAllExpenses();

while ($row = $expensesResult->fetch_assoc()) {
    $expenseDate = date('Y-m-d', strtotime($row["expense_date"]));

    if (
        $row["expense_status"] == "Approved" &&
        $expenseDate >= $startDate &&
        $expenseDate <= $endDate
    ) {
        $category = $row["expense_category"];

        if (array_key_exists($category, $expenseCategoryData)) {
            $expenseCategoryData[$category] += $row["expense_amount"];
        } else {
            $expenseCategoryData["Other"] += $row["expense_amount"];
        }

        $totalExpenses += $row["expense_amount"];
    }
}

// Paid PO Payments
$poPaymentsResult = $financeObj->getAllPOPayments();

while ($row = $poPaymentsResult->fetch_assoc()) {
    $paymentDate = date('Y-m-d', strtotime($row["po_payment_date"]));

    if (
        $row["po_payment_status"] == "Paid" &&
        $paymentDate >= $startDate &&
        $paymentDate <= $endDate
    ) {
        $expenseCategoryData["PO Payments"] += $row["po_amount"];
        $totalExpenses += $row["po_amount"];
    }
}

// Processed Order Refunds
$orderRefundResult = $orderObj->getAllOrderRefunds();

while ($row = $orderRefundResult->fetch_assoc()) {
    $refundDate = date('Y-m-d', strtotime($row["requested_at"]));

    if (
        $row["refund_status"] == "Processed" &&
        $refundDate >= $startDate &&
        $refundDate <= $endDate
    ) {
        $expenseCategoryData["Order Refunds"] += $row["refund_amount"];
        $totalExpenses += $row["refund_amount"];
    }
}

// Remove zero-value categories for a cleaner table
$expenseCategoryData = array_filter($expenseCategoryData, function ($v) {
    return $v > 0;
});

$netTotal = $totalIncome - $totalExpenses;
date_default_timezone_set('Asia/Colombo');
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF INIT ----------------
$pdf = new FinanceReport("P", "mm", "A4");
$pdf->startDate = $startDate;
$pdf->endDate = $endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("Finance Report - $startDate to $endDate");
$pdf->AddPage();

// ---------------- META INFO ----------------
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ---------------- SUMMARY CARDS (as a small table) ----------------
$pdf->SectionTitle("Overall Summary");

$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(220, 245, 220);
$pdf->Cell(90, 10, "Total Income", 1, 0, "L", true);
$pdf->Cell(90, 10, "Rs " . number_format($totalIncome, 2), 1, 1, "R", true);

$pdf->SetFillColor(245, 220, 220);
$pdf->Cell(90, 10, "Total Expenses", 1, 0, "L", true);
$pdf->Cell(90, 10, "Rs " . number_format($totalExpenses, 2), 1, 1, "R", true);

$pdf->SetFillColor($netTotal >= 0 ? 220 : 245, $netTotal >= 0 ? 245 : 220, 220);
$pdf->Cell(90, 10, "Net (Income - Expenses)", 1, 0, "L", true);
$pdf->Cell(90, 10, "Rs " . number_format($netTotal, 2), 1, 1, "R", true);

$pdf->Ln(8);

// ---------------- EXPENSE CATEGORY TABLE ----------------
$pdf->SectionTitle("Expenses by Category");

if (count($expenseCategoryData) > 0) {
    $pdf->TableHeader("Category", "Amount (Rs)");
    $pdf->SetFont("Arial", "", 9);

    $fill = false;
    foreach ($expenseCategoryData as $category => $amount) {

        if ($pdf->GetY() > 260) {
            $pdf->AddPage();
            $pdf->TableHeader("Category", "Amount (Rs)");
            $pdf->SetFont("Arial", "", 9);
        }

        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $pdf->Cell(120, 8, $category, 1, 0, "L", true);
        $pdf->Cell(60, 8, number_format($amount, 2), 1, 1, "R", true);
        $fill = !$fill;
    }

    // Total row
    $pdf->SetFont("Arial", "B", 9);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(120, 8, "Total Expenses", 1, 0, "L", true);
    $pdf->Cell(60, 8, number_format($totalExpenses, 2), 1, 1, "R", true);
} else {
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(190, 10, "No expense records found for this period.", 1, 1, "C");
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
$pdf->Output("I", "Finance_Report_Fabric_Apparel_" . $startDate . "_to_" . $endDate . ".pdf");