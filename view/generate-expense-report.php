<?php
include_once '../commons/session.php';
include '../model/finance_model.php';
include '../commons/fpdf186/fpdf.php';

$userrow = $_SESSION["user"];
include_once '../model/permission_model.php';
$permissionObj = new Permission();
if (!$permissionObj->hasPermission($userrow["user_id"], 15)) {
    header("Location: access_denied.php");
    exit();
}
class ExpenseReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Expense Report", 0, 1, "C");
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

    function CategoryTitle($category, $subtotal)
    {
        $this->SetFont("Arial", "B", 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(140, 8, $category, 1, 0, "L", true);
        $this->Cell(50, 8, "Rs " . number_format($subtotal, 2), 1, 1, "R", true);
    }

    function TableHeader()
    {
        $this->SetFont("Arial", "B", 9);
        $this->SetFillColor(240, 240, 240);
        $this->Cell(20, 8, "ID", 1, 0, "C", true);
        $this->Cell(30, 8, "Date", 1, 0, "C", true);
        $this->Cell(25, 8, "Amount", 1, 0, "C", true);
        $this->Cell(65, 8, "Description", 1, 0, "C", true);
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

$financeObj = new Finance();
$expenseResult = $financeObj->getAllExpenses();

// ---------------- GROUP EXPENSES BY CATEGORY ----------------
$categoryData = []; // category => ["rows" => [...], "subtotal" => 0]
$grandTotal = 0;
$recordCount = 0;

while ($row = $expenseResult->fetch_assoc()) {

    $expenseDate = date('Y-m-d', strtotime($row["expense_date"]));

    if ($expenseDate < $startDate || $expenseDate > $endDate) {
        continue;
    }

    if ($row["expense_status"] != "Approved") {
        continue;
    }

    $category = $row["expense_category"];

    if (!isset($categoryData[$category])) {
        $categoryData[$category] = ["rows" => [], "subtotal" => 0];
    }

    $categoryData[$category]["rows"][] = $row;
    $categoryData[$category]["subtotal"] += $row["expense_amount"];

    $grandTotal += $row["expense_amount"];
    $recordCount++;
}

// Sort categories by subtotal, highest first
uasort($categoryData, function ($a, $b) {
    return $b["subtotal"] <=> $a["subtotal"];
});

date_default_timezone_set('Asia/Colombo');
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF INIT ----------------
$pdf = new ExpenseReport("P", "mm", "A4");
$pdf->startDate = $startDate;
$pdf->endDate = $endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("Expense Report - $startDate to $endDate");
$pdf->AddPage();

// ---------------- META INFO ----------------
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ---------------- CATEGORY SUMMARY TABLE ----------------
$pdf->SetFont("Arial", "B", 12);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(0, 8, "Category Summary", 0, 1, "L");
$pdf->Ln(1);

if (count($categoryData) > 0) {

    $pdf->SetFont("Arial", "B", 10);
    $pdf->SetFillColor(200, 200, 200);
    $pdf->Cell(140, 9, "Category", 1, 0, "C", true);
    $pdf->Cell(50, 9, "Total (Rs)", 1, 1, "C", true);

    $pdf->SetFont("Arial", "", 9);
    $fill = false;

    foreach ($categoryData as $category => $data) {
        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $pdf->Cell(140, 8, $category, 1, 0, "L", true);
        $pdf->Cell(50, 8, number_format($data["subtotal"], 2), 1, 1, "R", true);
        $fill = !$fill;
    }

    $pdf->SetFont("Arial", "B", 10);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(140, 9, "Grand Total", 1, 0, "L", true);
    $pdf->Cell(50, 9, "Rs " . number_format($grandTotal, 2), 1, 1, "R", true);

} else {
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(190, 10, "No expense records found for this period.", 1, 1, "C");
}

// ---------------- DETAILED BREAKDOWN PER CATEGORY ----------------
if (count($categoryData) > 0) {

    $pdf->Ln(8);
    $pdf->SetFont("Arial", "B", 12);
    $pdf->Cell(0, 8, "Detailed Breakdown", 0, 1, "L");
    $pdf->Ln(1);

    foreach ($categoryData as $category => $data) {

        // Keep category block together where possible
        if ($pdf->GetY() > 240) {
            $pdf->AddPage();
        }

        $pdf->CategoryTitle($category, $data["subtotal"]);
        $pdf->TableHeader();

        $pdf->SetFont("Arial", "", 8);
        $fill = false;

        foreach ($data["rows"] as $row) {

            if ($pdf->GetY() > 270) {
                $pdf->AddPage();
                $pdf->CategoryTitle($category . " (cont.)", $data["subtotal"]);
                $pdf->TableHeader();
                $pdf->SetFont("Arial", "", 8);
            }

            $desc = iconv('UTF-8', 'windows-1252', $row["expense_description"]);
            $status = $row["expense_status"];

            $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
            $pdf->Cell(20, 7, $row["expense_id"], 1, 0, "C", true);
            $pdf->Cell(30, 7, $row["expense_date"], 1, 0, "C", true);
            $pdf->Cell(25, 7, number_format($row["expense_amount"], 2), 1, 0, "R", true);
            $pdf->Cell(65, 7, $desc, 1, 0, "L", true);
            $pdf->Cell(30, 7, $status, 1, 1, "C", true);

            $fill = !$fill;
        }

        $pdf->Ln(4);
    }
}

// ---------------- FOOTER NOTES ----------------
$pdf->Ln(4);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a physical signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidentiality Notice: This document contains internal system data.", 0, "C");

// ---------------- OUTPUT ----------------
if (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output("I", "Expense_Report_Fabric_Apparel_" . $startDate . "_to_" . $endDate . ".pdf");