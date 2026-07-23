<?php
include_once '../commons/session.php';
include_once '../model/production_model.php';
include '../commons/fpdf186/fpdf.php';

class StartProductionReport extends FPDF
{
    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Pending Orders to Start Production Report",0,1,"C");
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont("Arial","I",8);
        $this->Cell(0,5,"Page ".$this->PageNo()." / {nb}",0,1,"C");
        $this->SetFont("Arial","I",7);
        $this->Cell(0,5,"Fabric Apparel (PVT) LTD | www.fabricapparel.com | confidential",0,0,"C");
    }

    function TableHeader()
    {
        $this->SetFont("Arial","B",10);
        $this->SetFillColor(200,200,200);

        $this->Cell(12,10,"#",1,0,"C",true);
        $this->Cell(25,10,"Order ID",1,0,"C",true);
        $this->Cell(75,10,"Company",1,0,"C",true);
        $this->Cell(38,10,"Due Date",1,0,"C",true);
        $this->Cell(40,10,"Order Status",1,1,"C",true);
    }
}

$productionObj=new Production();
$result=$productionObj->getAllOrdersInProduction();

date_default_timezone_set("Asia/Colombo");
$dateTime=date("Y-m-d H:i:s");

$pdf=new StartProductionReport("P","mm","A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Pending Orders to Start Production");
$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".$dateTime,0,1);
$pdf->Ln(2);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

$pdf->TableHeader();
$pdf->SetFont("Arial","",9);

$count=1;
$total=0;
$fill=false;

while($row=$result->fetch_assoc())
{
    if($pdf->GetY()>270)
    {
        $pdf->AddPage();
        $pdf->TableHeader();
        $pdf->SetFont("Arial","",9);
    }

    $company=iconv("UTF-8","windows-1252",$row["company_name"]);

    $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

    $pdf->Cell(12,8,$count++,1,0,"C",true);
    $pdf->Cell(25,8,"ORD".$row["order_id"],1,0,"C",true);
    $pdf->Cell(75,8,$company,1,0,"L",true);
    $pdf->Cell(38,8,$row["expected_delivery_date"],1,0,"C",true);
    $pdf->Cell(40,8,$row["status_name"],1,1,"C",true);

    $fill=!$fill;
    $total++;
}

if($total==0)
{
    $pdf->Cell(190,10,"No pending production orders found.",1,1,"C");
}

$pdf->Ln(5);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(0,6,"Total Pending Orders : ".$total,0,1);

$pdf->Ln(5);
$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal production data.",0,"C");

if(ob_get_length()){
    ob_end_clean();
}

$pdf->Output("I","Pending_Orders_To_Start_Production_Report.pdf");