<?php

// ---------------- INCLUDE REQUIRED FILES ----------------
include_once '../model/supplier_model.php';
include '../commons/fpdf186/fpdf.php';


// ---------------- PDF CLASS ----------------
class SupplierReport extends FPDF
{
    function Header()
    {
        // Logo
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);

        // Company Name
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        // Subtitle
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Supplier Report", 0, 1, "C");

        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-20);

        $this->SetFont("Arial", "I", 8);
        $this->Cell(0, 5, "Page " . $this->PageNo() . " / {nb}", 0, 1, "C");

        $this->SetFont("Arial", "I", 7);
        $this->Cell(0, 5, "Fabric Apparel (PVT) LTD | www.fabricapparel.com | Confidential", 0, 0, "C");
    }

    function TableHeader()
    {
        $this->SetFont("Arial", "B", 9);
        $this->SetFillColor(200, 200, 200);

        $this->Cell(10, 10, "#", 1, 0, "C", true);
        $this->Cell(45, 10, "Supplier", 1, 0, "C", true);
        $this->Cell(40, 10, "Contact Person", 1, 0, "C", true);
        $this->Cell(55, 10, "Email", 1, 0, "C", true);
        $this->Cell(40, 10, "Phone", 1, 1, "C", true);
    }
}


// ---------------- FETCH DATA ----------------
$supplierObj = new Supplier();
$supplierResult = $supplierObj->getAllSuppliers();

date_default_timezone_set("Asia/Colombo");

$date = date("Y-m-d");
$dateTime = date("Y-m-d H:i:s");


// ---------------- CREATE PDF ----------------
$pdf = new SupplierReport("P", "mm", "A4");

$pdf->AliasNbPages();
$pdf->SetTitle("Supplier Report - $date");

$pdf->AddPage();


// ---------------- REPORT DETAILS ----------------
$pdf->SetFont("Arial", "", 10);

$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");

$pdf->Ln(2);

$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

$pdf->Ln(5);


// ---------------- TABLE HEADER ----------------
$pdf->TableHeader();

$pdf->SetFont("Arial", "", 9);

$fill = false;
$count = 1;
$totalSuppliers = 0;

if ($supplierResult && $supplierResult->num_rows > 0) {

    while ($row = $supplierResult->fetch_assoc()) {

        if ($pdf->GetY() > 270) {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial", "", 9);
        }

        if ($fill)
            $pdf->SetFillColor(245, 245, 245);
        else
            $pdf->SetFillColor(255, 255, 255);

        $supplier = iconv('UTF-8', 'windows-1252', $row['supplier_name']);
        $contact  = iconv('UTF-8', 'windows-1252', $row['supplier_contact_person']);
        $email    = iconv('UTF-8', 'windows-1252', $row['supplier_email']);
        $phone    = iconv('UTF-8', 'windows-1252', $row['supplier_phone']);

        $pdf->Cell(10, 8, $count++, 1, 0, "C", true);
        $pdf->Cell(45, 8, $supplier, 1, 0, "L", true);
        $pdf->Cell(40, 8, $contact, 1, 0, "L", true);
        $pdf->Cell(55, 8, $email, 1, 0, "L", true);
        $pdf->Cell(40, 8, $phone, 1, 1, "C", true);

        $fill = !$fill;
        $totalSuppliers++;
    }

} else {

    $pdf->Cell(190, 10, "No suppliers found.", 1, 1, "C");

}


// ---------------- SUMMARY ----------------
$pdf->Ln(5);

$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0, 6, "Total Suppliers : $totalSuppliers", 0, 1);


// ---------------- FOOTER NOTE ----------------
$pdf->Ln(5);

$pdf->SetFont("Arial", "I", 9);

$pdf->MultiCell(
    0,
    5,
    "This is a computer-generated report and does not require a physical signature.",
    0,
    "C"
);

$pdf->MultiCell(
    0,
    5,
    "Confidentiality Notice: This document contains internal supplier information.",
    0,
    "C"
);


// ---------------- OUTPUT ----------------
ob_end_clean();

$pdf->Output("I", "Supplier_Report_$dateTime.pdf");

?>