<?php
include_once '../commons/session.php';
include_once '../model/transport_model.php';
include '../commons/fpdf186/fpdf.php';

class TransportSummaryReport extends FPDF
{
    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Transport Management Summary Report",0,1,"C");
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont("Arial","I",8);
        $this->Cell(0,5,"Page ".$this->PageNo()." / {nb}",0,1,"C");
        $this->SetFont("Arial","I",7);
        $this->Cell(0,5,"Fabric Apparel (PVT) LTD | Confidential",0,0,"C");
    }

    function TableHeader($headers)
    {
        $this->SetFont("Arial","B",9);
        $this->SetFillColor(200,200,200);

        foreach($headers as $header)
        {
            $this->Cell($header[0],10,$header[1],1,0,"C",true);
        }

        $this->Ln();
    }
}

$transportObj = new Transport();

$totalTransportResult = $transportObj->getAllTransports();

$total=0;
$started=0;
$rejected=0;
$delivered=0;

while($row=$totalTransportResult->fetch_assoc())
{
    $total++;

    if($row["transport_status"]=="Started")
    {
        $started++;
    }
    elseif($row["transport_status"]=="Rejected")
    {
        $rejected++;
    }
    elseif($row["transport_status"]=="Delivered")
    {
        $delivered++;
    }
}

$ongoingTransports = $transportObj->getOngoingTransports();
$vehicleResult = $transportObj->getAllAvailableVehicles();

date_default_timezone_set("Asia/Colombo");

$pdf = new TransportSummaryReport("P","mm","A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Transport Summary Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".date("Y-m-d H:i:s"),0,1);
$pdf->Ln(2);

$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);


// SUMMARY
$pdf->SetFont("Arial","B",11);
$pdf->Cell(0,8,"Transport Summary",0,1);

$pdf->SetFont("Arial","",10);

$pdf->Cell(45,8,"Total Transports",1,0);
$pdf->Cell(30,8,$total,1,1);

$pdf->Cell(45,8,"Started",1,0);
$pdf->Cell(30,8,$started,1,1);

$pdf->Cell(45,8,"Rejected",1,0);
$pdf->Cell(30,8,$rejected,1,1);

$pdf->Cell(45,8,"Delivered",1,0);
$pdf->Cell(30,8,$delivered,1,1);


// ONGOING TRANSPORTS

$pdf->Ln(8);
$pdf->SetFont("Arial","B",11);
$pdf->Cell(0,8,"Ongoing Transports",0,1);

$pdf->TableHeader([
    [40,"Transport ID"],
    [60,"Status"]
]);

$pdf->SetFont("Arial","",9);

if($ongoingTransports->num_rows>0)
{
    while($row=$ongoingTransports->fetch_assoc())
    {
        $pdf->Cell(40,8,"TRA".$row["transport_id"],1,0,"C");
        $pdf->Cell(60,8,$row["transport_status"],1,1,"C");
    }
}
else
{
    $pdf->Cell(100,8,"No ongoing transport found.",1,1,"C");
}


// AVAILABLE VEHICLES

$pdf->Ln(8);
$pdf->SetFont("Arial","B",11);
$pdf->Cell(0,8,"Available Vehicles",0,1);

$pdf->TableHeader([
    [20,"ID"],
    [40,"Vehicle No"],
    [45,"Type"],
    [40,"Capacity"]
]);

$pdf->SetFont("Arial","",9);

if($vehicleResult->num_rows>0)
{
    while($row=$vehicleResult->fetch_assoc())
    {
        $pdf->Cell(20,8,$row["vehicle_id"],1,0,"C");
        $pdf->Cell(40,8,$row["vehicle_number"],1,0,"C");
        $pdf->Cell(45,8,$row["vehicle_type"],1,0,"C");
        $pdf->Cell(40,8,$row["vehicle_capacity"]." kg",1,1,"C");
    }
}
else
{
    $pdf->Cell(145,8,"No available vehicles found.",1,1,"C");
}


$pdf->Ln(8);
$pdf->SetFont("Arial","I",9);

$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");

$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal transport data.",0,"C");


if(ob_get_length())
{
    ob_end_clean();
}

$pdf->Output("I","Transport_Summary_Report_Fabric_Apparel.pdf");
?>