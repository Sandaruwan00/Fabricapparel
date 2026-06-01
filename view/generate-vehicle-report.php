<?php

include_once '../model/transport_model.php';
include '../commons/fpdf186/fpdf.php';

class VehicleReport extends FPDF
{
    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);

        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Vehicle Management Report", 0, 1, "C");

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
        $this->SetFont("Arial", "B", 11);
        $this->SetFillColor(200, 200, 200);

        $this->Cell(15, 10, "#", 1, 0, "C", true);
        $this->Cell(45, 10, "Vehicle No", 1, 0, "C", true);
        $this->Cell(45, 10, "Vehicle Type", 1, 0, "C", true);
        $this->Cell(40, 10, "Capacity (kg)", 1, 0, "C", true);
        $this->Cell(45, 10, "Status", 1, 1, "C", true);
    }
}

$transportObj = new Transport();
$vehicleResult = $transportObj->getAllVehicles();

$date = date("Y-m-d");
$dateTime = date("Y-m-d H:i:s");

$pdf = new VehicleReport("P", "mm", "A4");

$pdf->AliasNbPages();
$pdf->SetTitle("Vehicle Report - $date");
$pdf->AddPage();

$pdf->SetFont("Arial", "", 10);

$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");

$pdf->Ln(2);

$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

$pdf->Ln(5);

$pdf->TableHeader();

$pdf->SetFont("Arial", "", 10);

$fill = false;
$count = 1;

$totalVehicles = 0;
$availableVehicles = 0;
$assignedVehicles = 0;
$maintenanceVehicles = 0;

if ($vehicleResult && $vehicleResult->num_rows > 0) {

    while ($row = $vehicleResult->fetch_assoc()) {

        if ($pdf->GetY() > 270) {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial", "", 10);
        }

        $totalVehicles++;

        if ($row['vehicle_status'] == 'Available') {
            $availableVehicles++;
        } elseif ($row['vehicle_status'] == 'Assigned') {
            $assignedVehicles++;
        } else {
            $maintenanceVehicles++;
        }

        if ($fill) {
            $pdf->SetFillColor(245, 245, 245);
        } else {
            $pdf->SetFillColor(255, 255, 255);
        }

        $vehicleNo = iconv(
            'UTF-8',
            'windows-1252',
            $row['vehicle_number']
        );

        $vehicleType = iconv(
            'UTF-8',
            'windows-1252',
            $row['vehicle_type']
        );

        $capacity = $row['vehicle_capacity'];

        $status = iconv(
            'UTF-8',
            'windows-1252',
            $row['vehicle_status']
        );

        $pdf->Cell(15, 8, $count++, 1, 0, "C", true);
        $pdf->Cell(45, 8, $vehicleNo, 1, 0, "C", true);
        $pdf->Cell(45, 8, $vehicleType, 1, 0, "C", true);
        $pdf->Cell(40, 8, $capacity, 1, 0, "C", true);



        $pdf->Cell(45, 8, $status, 1, 1, "C", true);

        $pdf->SetTextColor(0, 0, 0);

        $fill = !$fill;
    }
} else {

    $pdf->Cell(
        190,
        10,
        "No vehicle records found.",
        1,
        1,
        "C"
    );
}

$pdf->Ln(5);

$pdf->SetFont("Arial", "B", 10);

$pdf->Cell(0,6,"Total Vehicles: $totalVehicles | Available: $availableVehicles | Assigned: $assignedVehicles | Maintenance: $maintenanceVehicles",0,1,"L");

$pdf->Ln(5);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a physical signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidentiality Notice: This document contains internal system data.", 0, "C");

if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output(
    "I",
    "Vehicle_Report_Fabric_Apparel_" . $dateTime . ".pdf"
);
