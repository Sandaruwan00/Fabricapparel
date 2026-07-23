<?php
include_once '../commons/session.php';
include_once '../model/transport_model.php';
include_once '../model/warehouse_model.php';
include '../commons/fpdf186/fpdf.php';

class TransportReport extends FPDF
{
    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Transport Shipment Report",0,1,"C");
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
        $this->SetFont("Arial","B",10);
        $this->SetFillColor(200,200,200);
        $this->Cell(15,10,"#",1,0,"C",true);
        $this->Cell(35,10,"Shipment ID",1,0,"C",true);
        $this->Cell(55,10,"Delivery Location",1,0,"C",true);
        $this->Cell(35,10,"No. Of Orders",1,0,"C",true);
        $this->Cell(50,10,"Status",1,1,"C",true);
    }
}

$transportObj = new Transport();
$warehouseObj = new Warehouse();

$result = $transportObj->getDispatchedShipments();

date_default_timezone_set("Asia/Colombo");

$pdf = new TransportReport("P","mm","A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Transport Shipment Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".date("Y-m-d H:i:s"),0,1);
$pdf->Ln(2);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

$pdf->TableHeader();
$pdf->SetFont("Arial","",9);

$count=1;
$totalShipments=0;
$fill=false;

while($row=$result->fetch_assoc())
{
    if($pdf->GetY()>270)
    {
        $pdf->AddPage();
        $pdf->TableHeader();
        $pdf->SetFont("Arial","",9);
    }

    $shipment_id=$row["shipment_id"];
    $shipmentItems=$warehouseObj->getAllShipmentItems($shipment_id);
    $orderCount=0;

    while($item=$shipmentItems->fetch_assoc())
    {
        $orderCount++;
    }

    $location=iconv("UTF-8","windows-1252",$row["district_name"]);

    $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

    $pdf->Cell(15,8,$count++,1,0,"C",true);
    $pdf->Cell(35,8,"SHIP".$shipment_id,1,0,"C",true);
    $pdf->Cell(55,8,$location,1,0,"L",true);
    $pdf->Cell(35,8,$orderCount,1,0,"C",true);
    $pdf->Cell(50,8,"Dispatched",1,1,"C",true);

    $fill=!$fill;
    $totalShipments++;
}

if($totalShipments==0)
{
    $pdf->Cell(190,10,"No dispatched shipments found.",1,1,"C");
}

$pdf->Ln(5);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(0,6,"Total Dispatched Shipments : ".$totalShipments,0,1);

$pdf->Ln(5);
$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal transport data.",0,"C");

if(ob_get_length())
{
    ob_end_clean();
}

$pdf->Output("I","Transport_Shipment_Report.pdf");
?>