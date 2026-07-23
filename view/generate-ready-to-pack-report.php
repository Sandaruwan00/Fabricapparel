<?php
include_once '../commons/session.php';
include_once '../model/packing_model.php';
include '../commons/fpdf186/fpdf.php';

class ReadyPackingReport extends FPDF
{
    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Finished Production Orders Ready for Packing Report",0,1,"C");
        $this->Ln(6);
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
        $this->SetFillColor(240,240,240);
        $this->Cell(15,8,"#",1,0,"C",true);
        $this->Cell(35,8,"Production ID",1,0,"C",true);
        $this->Cell(30,8,"Order ID",1,0,"C",true);
        $this->Cell(55,8,"Company",1,0,"C",true);
        $this->Cell(35,8,"Due Date",1,0,"C",true);
        $this->Cell(20,8,"Status",1,1,"C",true);
    }
}

$packingObj = new Packing();
$result = $packingObj->getAllCompletedProductions();

date_default_timezone_set("Asia/Colombo");

$pdf = new ReadyPackingReport("P","mm","A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Ready Packing Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".date("Y-m-d H:i:s"),0,1);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

$pdf->TableHeader();
$pdf->SetFont("Arial","",8);

$count=1;
$total=0;
$fill=false;

while($row=$result->fetch_assoc())
{
    if($pdf->GetY()>270)
    {
        $pdf->AddPage();
        $pdf->TableHeader();
        $pdf->SetFont("Arial","",8);
    }

    $company=iconv("UTF-8","windows-1252",$row["company_name"]);

    $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

    $pdf->Cell(15,7,$count++,1,0,"C",true);
    $pdf->Cell(35,7,"PRO".$row["production_id"],1,0,"C",true);
    $pdf->Cell(30,7,"ORD".$row["order_id"],1,0,"C",true);
    $pdf->Cell(55,7,$company,1,0,"L",true);
    $pdf->Cell(35,7,$row["expected_delivery_date"],1,0,"C",true);
    $pdf->Cell(20,7,"Completed",1,1,"C",true);

    $fill=!$fill;
    $total++;
}

if($total==0)
{
    $pdf->Cell(190,10,"No completed production orders ready for packing.",1,1,"C");
}

$pdf->Ln(5);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(0,6,"Total Ready Packing Orders : ".$total,0,1);

$pdf->Ln(8);
$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal packing data.",0,"C");

if(ob_get_length())
{
    ob_end_clean();
}

$pdf->Output("I","Finished_Production_Ready_Packing_Report.pdf");
?>