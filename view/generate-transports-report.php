<?php
include_once '../commons/session.php';
include_once '../model/transport_model.php';
include '../commons/fpdf186/fpdf.php';

class TransportReport extends FPDF
{
    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Transport Report",0,1,"C");
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

    function TableHeader()
    {
        $this->SetFont("Arial","B",9);
        $this->SetFillColor(200,200,200);

        $this->Cell(10,10,"#",1,0,"C",true);
        $this->Cell(25,10,"Transport ID",1,0,"C",true);
        $this->Cell(35,10,"Location",1,0,"C",true);
        $this->Cell(25,10,"Shipment",1,0,"C",true);
        $this->Cell(25,10,"Vehicle",1,0,"C",true);
        $this->Cell(40,10,"Driver",1,0,"C",true);
        $this->Cell(30,10,"Status",1,1,"C",true);
    }
}

$transportObj = new Transport();
$result = $transportObj->getAllTransports();

date_default_timezone_set("Asia/Colombo");

$pdf = new TransportReport("P","mm","A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Transport Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".date("Y-m-d H:i:s"),0,1);
$pdf->Ln(2);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

$pdf->TableHeader();
$pdf->SetFont("Arial","",8);

$count=1;
$total=0;
$fill=false;

$pending=0;
$confirmed=0;
$rejected=0;
$started=0;
$delivered=0;

while($row=$result->fetch_assoc())
{
    if($pdf->GetY()>270)
    {
        $pdf->AddPage();
        $pdf->TableHeader();
        $pdf->SetFont("Arial","",8);
    }

    $location=iconv("UTF-8","windows-1252",$row["district_name"]);
    $driver=iconv("UTF-8","windows-1252","ID:".$row["driver_id"]." - ".$row["driver_name"]);

    if($row["transport_status"]=="Pending")
    {
        $pending++;
    }
    elseif($row["transport_status"]=="Confirmed")
    {
        $confirmed++;
    }
    elseif($row["transport_status"]=="Rejected")
    {
        $rejected++;
    }
    elseif($row["transport_status"]=="Started")
    {
        $started++;
    }
    elseif($row["transport_status"]=="Delivered")
    {
        $delivered++;
    }

    $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

    $pdf->Cell(10,8,$count++,1,0,"C",true);
    $pdf->Cell(25,8,"TRA".$row["transport_id"],1,0,"C",true);
    $pdf->Cell(35,8,$location,1,0,"L",true);
    $pdf->Cell(25,8,"SHIP".$row["shipment_id"],1,0,"C",true);
    $pdf->Cell(25,8,$row["vehicle_number"],1,0,"C",true);
    $pdf->Cell(40,8,$driver,1,0,"L",true);
    $pdf->Cell(30,8,$row["transport_status"],1,1,"C",true);

    $fill=!$fill;
    $total++;
}

if($total==0)
{
    $pdf->Cell(190,10,"No transport records found.",1,1,"C");
}

$pdf->Ln(5);
$pdf->SetFont("Arial","B",10);

$pdf->Cell(
    0,
   6,
   "Total Transports : ".$total.
    " | Pending : ".$pending.
    " | Confirmed : ".$confirmed.
    " | Rejected : ".$rejected.
    " | Started : ".$started.
    " | Delivered : ".$delivered,
    0,
    1
);

$pdf->Ln(5);

$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal transport data.",0,"C");

if(ob_get_length())
{
    ob_end_clean();
}

$pdf->Output("I","Transport_Report_Fabric_Apparel.pdf");
?>