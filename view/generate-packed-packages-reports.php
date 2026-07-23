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
        $this->Cell(0,6,"Packed Packages Report",0,1,"C");
        $this->Ln(8);
    }

    function Footer(){
        $this->SetY(-20);
        $this->SetFont("Arial","I",8);
        $this->Cell(0,5,"Page ".$this->PageNo()." / {nb}",0,1,"C");
        $this->SetFont("Arial","I",7);
        $this->Cell(0,5,"Fabric Apparel (PVT) LTD | Confidential",0,0,"C");
    }

    function TableHeader(){
        $this->SetFont("Arial","B",10);
        $this->SetFillColor(200,200,200);
        $this->Cell(15,10,"#",1,0,"C",true);
        $this->Cell(35,10,"Packing ID",1,0,"C",true);
        $this->Cell(35,10,"Order ID",1,0,"C",true);
        $this->Cell(55,10,"Buyer",1,0,"C",true);
        $this->Cell(50,10,"Status",1,1,"C",true);
    }
}

$warehouseObj = new Warehouse();
$result = $warehouseObj->getAllPackedPackages();

date_default_timezone_set("Asia/Colombo");

$pdf = new WarehousePackageReport("P","mm","A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Packed Package Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".date("Y-m-d H:i:s"),0,1);
$pdf->Ln(2);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

$pdf->TableHeader();
$pdf->SetFont("Arial","",9);

$count=1;
$totalPackages=0;
$fill=false;

while($row=$result->fetch_assoc()){
    if($pdf->GetY()>270){
        $pdf->AddPage();
        $pdf->TableHeader();
    }

    $buyer=iconv("UTF-8","windows-1252",$row["company_name"]);

    $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

    $pdf->Cell(15,8,$count++,1,0,"C",true);
    $pdf->Cell(35,8,"PACK".$row["packing_id"],1,0,"C",true);
    $pdf->Cell(35,8,"ORD".$row["order_id"],1,0,"C",true);
    $pdf->Cell(55,8,$buyer,1,0,"L",true);
    $pdf->Cell(50,8,"Packed",1,1,"C",true);

    $fill=!$fill;
    $totalPackages++;
}

if($totalPackages==0){
    $pdf->Cell(190,10,"No packed packages found.",1,1,"C");
}

$pdf->Ln(5);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(0,6,"Total Packages : ".$totalPackages,0,1);

$pdf->Ln(5);
$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal warehouse data.",0,"C");

if(ob_get_length()){
    ob_end_clean();
}

$pdf->Output("I","Packed_Packages_Report.pdf");
?>