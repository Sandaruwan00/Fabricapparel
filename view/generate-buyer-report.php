<?php
// ---------------- INCLUDE REQUIRED FILES ----------------

// Buyer model
include_once '../model/buyer_model.php';

// FPDF library
include '../commons/fpdf186/fpdf.php';


// ---------------- PDF CLASS DEFINITION ----------------

class BuyerReport extends FPDF
{
    // ----------- HEADER -----------
    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);

        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Buyers Overview Report", 0, 1, "C");

        $this->Ln(10);
    }

    // ----------- FOOTER -----------
    function Footer()
    {
        $this->SetY(-20);

        $this->SetFont("Arial", "I", 8);
        $this->Cell(0, 5, "Page " . $this->PageNo() . " / {nb}", 0, 1, "C");

        $this->SetFont("Arial", "I", 7);
        $this->Cell(0, 5, "Fabric Apparel (PVT) LTD | www.fabricapparel.com | confidential", 0, 0, "C");
    }

    // ----------- TABLE HEADER -----------
    function TableHeader()
    {
        $this->SetFont("Arial", "B", 11);
        $this->SetFillColor(200, 200, 200);

        $this->Cell(10, 10, "#", 1, 0, "C", true);
        $this->Cell(62, 10, "Company Name", 1, 0, "C", true);
        $this->Cell(50, 10, "Contact Person", 1, 0, "C", true);
        $this->Cell(68, 10, "Email", 1, 1, "C", true);
    }
}


// ---------------- FETCH DATA ----------------

$buyerObj = new Buyer();
$buyerResult = $buyerObj->getAllBuyers();

$date = date("Y-m-d");


// ---------------- PDF GENERATION ----------------

$pdf = new BuyerReport("P", "mm", "A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Buyer Report - $date");
$pdf->AddPage();


// ---------------- REPORT META ----------------

$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Report Date : $date", 0, 1, "L");
$pdf->Ln(2);

$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);


// ---------------- TABLE HEADER ----------------
$pdf->TableHeader();


// ---------------- TABLE DATA ----------------

$pdf->SetFont("Arial", "", 10);

$fill = false;
$count = 1;

$totalBuyers = 0;

if ($buyerResult && $buyerResult->num_rows > 0) {

    while ($row = $buyerResult->fetch_assoc()) {

        // Page break
        if ($pdf->GetY() > 270) {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial", "", 10);
        }

        $totalBuyers++;

        // Row color
        if ($fill) $pdf->SetFillColor(245, 245, 245);
        else $pdf->SetFillColor(255, 255, 255);

        // Encoding
        $company = iconv('UTF-8', 'windows-1252', $row['company_name']);
        $contact = iconv('UTF-8', 'windows-1252', $row['contact_name']);
        $email   = iconv('UTF-8', 'windows-1252', $row['contact_email']);

        // Row
        $pdf->Cell(10, 8, $count++, 1, 0, "C", true);
        $pdf->Cell(62, 8, $company, 1, 0, "L", true);
        $pdf->Cell(50, 8, $contact, 1, 0, "L", true);
        $pdf->Cell(68, 8, $email, 1, 1, "L", true);

        $fill = !$fill;
    }

} else {
    $pdf->Cell(190, 10, "No buyers found in the system.", 1, 1, "C");
}


// ---------------- SUMMARY ----------------

$pdf->Ln(5);
$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0, 6, "Total Buyers: $totalBuyers", 0, 1, "L");


// ---------------- FOOTER NOTES ----------------

$pdf->Ln(5);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a physical signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidentiality Notice: This document contains internal system data.", 0, "C");


// ---------------- OUTPUT ----------------

ob_end_clean();
$pdf->Output("I", "Buyer_Report_Fabric_Apparel_$date.pdf");