<?php
include_once '../commons/session.php';
include_once '../model/warehouse_model.php';
include '../commons/fpdf186/fpdf.php';

class WarehousePackageReport extends FPDF{

    function Header(){
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Warehouse Package Report",0,1,"C");
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
        $this->SetFont("Arial","B",8);
        $this->SetFillColor(200,200,200);
        $this->Cell(10,8,"#",1,0,"C",true);
        $this->Cell(28,8,"Pack ID",1,0,"C",true);
        $this->Cell(28,8,"Order ID",1,0,"C",true);
        $this->Cell(52,8,"Buyer",1,0,"C",true);
        $this->Cell(32,8,"Payment",1,0,"C",true);
        $this->Cell(40,8,"Warehouse Status",1,1,"C",true);
    }
}

$warehouseObj=new Warehouse();
$result=$warehouseObj->getAllWarehousePackages();

date_default_timezone_set("Asia/Colombo");

$pdf=new WarehousePackageReport("P","mm","A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Warehouse Package Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",9);
$pdf->Cell(0,6,"Generated On : ".date("Y-m-d H:i:s"),0,1);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(4);

$pdf->TableHeader();
$pdf->SetFont("Arial","",8);

$count=1;
$total=0;
$paid=0;
$unpaid=0;
$inWarehouse=0;
$assigned=0;
$shipped=0;
$fill=false;

while($row=$result->fetch_assoc()){

    if($pdf->GetY()>270){
        $pdf->AddPage();
        $pdf->TableHeader();
    }

    $order_id=$row["order_id"];
    $totalPayments=0;
    $approvedOP=$warehouseObj->getApprovedOP($order_id);

    while($payment=$approvedOP->fetch_assoc()){
        $totalPayments+=$payment["amount"];
    }

    $totalAmount=$row["total_amount"]+$row["delivery_charge"];

    if($totalPayments >= $totalAmount){
        $paymentStatus="Paid";
        $paid++;
    }else{
        $paymentStatus="Unpaid";
        $unpaid++;
    }

    if($row["warehouse_pkg_status"]=="In Warehouse"){
        $inWarehouse++;
    }elseif($row["warehouse_pkg_status"]=="Shipment Assigned"){
        $assigned++;
    }else{
        $shipped++;
    }

    $buyer=iconv("UTF-8","windows-1252",$row["company_name"]);

    $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

    $pdf->Cell(10,7,$count++,1,0,"C",true);
    $pdf->Cell(28,7,"PACK".$row["packing_id"],1,0,"C",true);
    $pdf->Cell(28,7,"ORD".$order_id,1,0,"C",true);
    $pdf->Cell(52,7,$buyer,1,0,"L",true);
    $pdf->Cell(32,7,$paymentStatus,1,0,"C",true);
    $pdf->Cell(40,7,$row["warehouse_pkg_status"],1,1,"C",true);

    $fill=!$fill;
    $total++;
}

if($total==0){
    $pdf->Cell(190,8,"No warehouse packages found.",1,1,"C");
}

$pdf->Ln(5);
$pdf->SetFont("Arial","B",9);
$pdf->Cell(0,6,"Warehouse Package Summary",0,1);

$pdf->SetFont("Arial","",9);
$pdf->Cell(0,6,"Total Packages : ".$total,0,1);
$pdf->Cell(0,6,"Paid Packages : ".$paid,0,1);
$pdf->Cell(0,6,"Unpaid Packages : ".$unpaid,0,1);
$pdf->Cell(0,6,"In Warehouse : ".$inWarehouse,0,1);
$pdf->Cell(0,6,"Shipment Assigned : ".$assigned,0,1);
$pdf->Cell(0,6,"Shipped : ".$shipped,0,1);

$pdf->Ln(5);
$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal warehouse data.",0,"C");

if(ob_get_length()){
    ob_end_clean();
}

$pdf->Output("I","Warehouse_Package_Report.pdf");
?>