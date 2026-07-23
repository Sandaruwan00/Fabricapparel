<?php
include_once '../commons/session.php';
include_once '../model/warehouse_model.php';
include_once '../model/order_model.php';
include '../commons/fpdf186/fpdf.php';

class WarehouseAvailablePackageReport extends FPDF{
    function Header(){
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Fully Paid Warehouse Packages Report",0,1,"C");
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
        $this->Cell(32,8,"Package ID",1,0,"C",true);
        $this->Cell(32,8,"Order ID",1,0,"C",true);
        $this->Cell(50,8,"Buyer",1,0,"C",true);
        $this->Cell(30,8,"Qty",1,0,"C",true);
        $this->Cell(34,8,"Location",1,1,"C",true);
    }
}

$warehouseObj=new Warehouse();
$orderObj=new Order();
$result=$warehouseObj->getInWarehousePackages();

date_default_timezone_set("Asia/Colombo");

$pdf=new WarehouseAvailablePackageReport("P","mm","A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Warehouse Available Package Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",9);
$pdf->Cell(0,6,"Generated On : ".date("Y-m-d H:i:s"),0,1);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(4);

$pdf->TableHeader();
$pdf->SetFont("Arial","",8);

$count=1;
$totalPackages=0;
$fill=false;

while($packageRow=$result->fetch_assoc()){

    $order_id=$packageRow["order_id"];
    $totalPayments=0;
    $approvedOP=$warehouseObj->getApprovedOP($order_id);

    while($paymentrow=$approvedOP->fetch_assoc()){
        if($paymentrow["payment_status"]=="Approved"){
            $totalPayments += $paymentrow["amount"];
        }
    }

    $totalAmount=$packageRow["total_amount"]+$packageRow["delivery_charge"];

    if($totalPayments >= $totalAmount){

        if($pdf->GetY()>270){
            $pdf->AddPage();
            $pdf->TableHeader();
        }

        $buyer=iconv("UTF-8","windows-1252",$packageRow["company_name"]);
        $location=iconv("UTF-8","windows-1252",$packageRow["district_name"]);

        $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

        $pdf->Cell(12,7,$count++,1,0,"C",true);
        $pdf->Cell(32,7,"PACK".$packageRow["packing_id"],1,0,"C",true);
        $pdf->Cell(32,7,"ORD".$order_id,1,0,"C",true);
        $pdf->Cell(50,7,$buyer,1,0,"L",true);

        $orderItems=$orderObj->getOrderItems($order_id);
        $qty=0;

        while($item=$orderItems->fetch_assoc()){
            $qty += $item["qty"];
        }

        $pdf->Cell(30,7,$qty,1,0,"C",true);
        $pdf->Cell(34,7,$location,1,1,"C",true);

        $fill=!$fill;
        $totalPackages++;
    }
}

if($totalPackages==0){
    $pdf->Cell(190,8,"No fully paid packages available in warehouse.",1,1,"C");
}

$pdf->Ln(4);
$pdf->SetFont("Arial","B",9);
$pdf->Cell(0,6,"Total Fully Paid Packages : ".$totalPackages,0,1);

$pdf->Ln(5);
$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal warehouse data.",0,"C");

if(ob_get_length()){
    ob_end_clean();
}

$pdf->Output("I","Warehouse_Fully_Paid_Packages_Report.pdf");
?>