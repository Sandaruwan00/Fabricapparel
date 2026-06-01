<?php

include_once '../model/transport_model.php';
include '../commons/fpdf186/fpdf.php';

class DriverReport extends FPDF
{
    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);

        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Driver Management Report", 0, 1, "C");

        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-20);

        $this->SetFont("Arial", "I", 8);
        $this->Cell(0, 5, "Page " . $this->PageNo() . " / {nb}", 0, 1, "C");

        $this->SetFont("Arial", "I", 7);
        $this->Cell(0,5,"Fabric Apparel (PVT) LTD | www.fabricapparel.com | confidential",0,0,"C");
    }

    function TableHeader()
    {
        $this->SetFont("Arial", "B", 10);
        $this->SetFillColor(200, 200, 200);

        $this->Cell(10, 10, "#", 1, 0, "C", true);
        $this->Cell(45, 10, "Name", 1, 0, "C", true);
        $this->Cell(35, 10, "NIC", 1, 0, "C", true);
        $this->Cell(35, 10, "Phone", 1, 0, "C", true);
        $this->Cell(40, 10, "License No", 1, 0, "C", true);
        $this->Cell(25, 10, "Status", 1, 1, "C", true);
    }
}

// ---------------- DATA ----------------

$transportObj = new Transport();
$driverResult = $transportObj->getAllDrivers();

$date = date("Y-m-d");
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF INIT ----------------

$pdf = new DriverReport("P", "mm", "A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Driver Report - $date");
$pdf->AddPage();

// ---------------- META INFO ----------------

$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);

$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ---------------- TABLE HEADER ----------------

$pdf->TableHeader();
$pdf->SetFont("Arial", "", 9);

// ---------------- VARIABLES ----------------

$fill = false;
$count = 1;

$totalDrivers = 0;
$availableDrivers = 0;
$assignedDrivers = 0;
$deactiveDrivers = 0;

// ---------------- TABLE DATA ----------------

if ($driverResult && $driverResult->num_rows > 0) {

    while ($row = $driverResult->fetch_assoc()) {

        // Page break handling
        if ($pdf->GetY() > 270) {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial", "", 9);
        }

        $totalDrivers++;

        $status = $row['driver_status'];

        if ($status == 'Available') {
            $availableDrivers++;
        } elseif ($status == 'Assigned') {
            $assignedDrivers++;
        } else {
            $deactiveDrivers++;
        }

        // Row color
        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);

        // Encode text
        $name = iconv('UTF-8', 'windows-1252', $row['driver_name']);
        $nic = iconv('UTF-8', 'windows-1252', $row['driver_nic']);
        $phone = iconv('UTF-8', 'windows-1252', $row['driver_phone']);
        $license = iconv('UTF-8', 'windows-1252', $row['driver_license_no']);
        $statusTxt = iconv('UTF-8', 'windows-1252', $status);

       

        // Row output
        $pdf->Cell(10, 8, $count++, 1, 0, "C", true);
        $pdf->Cell(45, 8, $name, 1, 0, "L", true);
        $pdf->Cell(35, 8, $nic, 1, 0, "C", true);
        $pdf->Cell(35, 8, $phone, 1, 0, "C", true);
        $pdf->Cell(40, 8, $license, 1, 0, "C", true);
        $pdf->Cell(25, 8, $statusTxt, 1, 1, "C", true);

        $pdf->SetTextColor(0, 0, 0);

        $fill = !$fill;
    }

} else {

    $pdf->Cell(190, 10, "No driver records found.", 1, 1, "C");
}

// ---------------- SUMMARY ----------------

$pdf->Ln(5);
$pdf->SetFont("Arial", "B", 10);

$pdf->Cell(0,6,"Total Drivers: $totalDrivers | Available: $availableDrivers | Assigned: $assignedDrivers | Deactive: $deactiveDrivers",0,1,"L");

// ---------------- FOOTER NOTES ----------------

$pdf->Ln(5);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a physical signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidentiality Notice: This document contains internal system data.", 0, "C");

// ---------------- OUTPUT ----------------

if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output("I", "Driver_Report_Fabric_Apparel_$dateTime.pdf");