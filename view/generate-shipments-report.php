<?php
include_once '../commons/session.php';
include_once '../model/warehouse_model.php';
include '../commons/fpdf186/fpdf.php';

class ShipmentReport extends FPDF{

    function Header(){
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Warehouse Shipment Report",0,1,"C");
        $this->Ln(6);
    }

    function Footer(){
        $this->SetY(-15);
        $this->SetFont("Arial","I",8);
        $this->Cell(0,5,"Page ".$this->PageNo()." / {nb}",0,1,"C");
        $this->SetFont("Arial","I",7);
        $this->Cell(0,5,"Fabric Apparel (PVT) LTD | Confidential",0,0,"C");
    }

    function TableHeader(){
        $this->SetFont("Arial","B",9);
        $this->SetFillColor(200,200,200);

        $this->Cell(12,8,"#",1,0,"C",true);
        $this->Cell(35,8,"Shipment ID",1,0,"C",true);
        $this->Cell(45,8,"Location",1,0,"C",true);
        $this->Cell(35,8,"Items",1,0,"C",true);
        $this->Cell(63,8,"Status",1,1,"C",true);
    }
}

$warehouseObj = new Warehouse();

$result = $warehouseObj->getAllShipments();

date_default_timezone_set("Asia/Colombo");

$pdf = new ShipmentReport("P","mm","A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Warehouse Shipment Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",9);

$pdf->Cell(0,6,"Generated On : ".date("Y-m-d H:i:s"),0,1);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(4);

$pdf->TableHeader();

$pdf->SetFont("Arial","",8);

$count=1;
$totalShipments=0;

$pending=0;
$confirmed=0;
$dispatched=0;
$assigned=0;
$rejected=0;

$fill=false;

while($row=$result->fetch_assoc()){

    if($pdf->GetY()>270){
        $pdf->AddPage();
        $pdf->TableHeader();
    }

    $shipment_id=$row["shipment_id"];

    $items=$warehouseObj->getAllShipmentItems($shipment_id);
    $itemCount=0;

    while($item=$items->fetch_assoc()){
        $itemCount++;
    }

    if($row["shipment_status"]=="Pending"){
        $pending++;
    }elseif($row["shipment_status"]=="Confirmed"){
        $confirmed++;
    }elseif($row["shipment_status"]=="Dispatched"){
        $dispatched++;
    }elseif($row["shipment_status"]=="Transport Assigned"){
        $assigned++;
    }else{
        $rejected++;
    }


    $location=iconv("UTF-8","windows-1252",$row["district_name"]);


    $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

    $pdf->Cell(12,7,$count++,1,0,"C",true);
    $pdf->Cell(35,7,"SHIP".$shipment_id,1,0,"C",true);
    $pdf->Cell(45,7,$location,1,0,"L",true);
    $pdf->Cell(35,7,$itemCount,1,0,"C",true);
    $pdf->Cell(63,7,$row["shipment_status"],1,1,"C",true);

    $fill=!$fill;
    $totalShipments++;
}


if($totalShipments==0){
    $pdf->Cell(190,8,"No shipments found.",1,1,"C");
}


$pdf->Ln(5);

$pdf->SetFont("Arial","B",9);

$pdf->Cell(0,6,"Shipment Summary",0,1);
$pdf->SetFont("Arial","",9);

$pdf->Cell(0,6,"Total Shipments : ".$totalShipments,0,1);
$pdf->Cell(0,6,"Pending : ".$pending,0,1);
$pdf->Cell(0,6,"Confirmed : ".$confirmed,0,1);
$pdf->Cell(0,6,"Dispatched : ".$dispatched,0,1);
$pdf->Cell(0,6,"Transport Assigned : ".$assigned,0,1);
$pdf->Cell(0,6,"Rejected : ".$rejected,0,1);

$pdf->Ln(5);
$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal warehouse data.",0,"C");


if(ob_get_length()){
    ob_end_clean();
}

$pdf->Output("I","Warehouse_Shipment_Report.pdf");
?>