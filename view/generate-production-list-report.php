<?php
include_once '../commons/session.php';
include_once '../model/production_model.php';
include '../commons/fpdf186/fpdf.php';

class ProductionReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Production Report",0,1,"C");
        $this->SetFont("Arial","",9);
        $this->Cell(0,6,"Period : ".$this->startDate." to ".$this->endDate,0,1,"C");
        $this->Ln(6);
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
        $this->SetFont("Arial","B",9);
        $this->SetFillColor(200,200,200);

        $this->Cell(12,10,"#",1,0,"C",true);
        $this->Cell(20,10,"Order",1,0,"C",true);
        $this->Cell(50,10,"Company",1,0,"C",true);
        $this->Cell(35,10,"Started",1,0,"C",true);
        $this->Cell(35,10,"Finished",1,0,"C",true);
        $this->Cell(38,10,"Status",1,1,"C",true);
    }
}

$startDate=$_POST["start_date"];
$endDate=$_POST["end_date"];

$productionObj=new Production();
$result=$productionObj->getAllProductionOrders();

date_default_timezone_set("Asia/Colombo");
$dateTime=date("Y-m-d H:i:s");

$pdf=new ProductionReport("P","mm","A4");
$pdf->startDate=$startDate;
$pdf->endDate=$endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("Production Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".$dateTime,0,1);
$pdf->Ln(2);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

$pdf->TableHeader();
$pdf->SetFont("Arial","",8);

$count=1;
$total=0;
$ongoing=0;
$finished=0;
$fill=false;

while($row=$result->fetch_assoc())
{
    $productionDate=date("Y-m-d",strtotime($row["production_start"]));

    if($productionDate<$startDate || $productionDate>$endDate)
        continue;

    if($pdf->GetY()>270)
    {
        $pdf->AddPage();
        $pdf->TableHeader();
        $pdf->SetFont("Arial","",8);
    }

    $company=iconv("UTF-8","windows-1252",$row["company_name"]);
    $end=$row["production_end"] ? date("Y-m-d H:i:s",strtotime($row["production_end"])) : "-";

    $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

    $pdf->Cell(12,8,$count++,1,0,"C",true);
    $pdf->Cell(20,8,"ORD".$row["order_id"],1,0,"C",true);
    $pdf->Cell(50,8,$company,1,0,"L",true);
    $pdf->Cell(35,8,date("Y-m-d H:i:s",strtotime($row["production_start"])),1,0,"C",true);
    $pdf->Cell(35,8,$end,1,0,"C",true);
    $pdf->Cell(38,8,$row["production_status"],1,1,"C",true);

    $total++;

    if($row["production_status"]=="Ongoing")
        $ongoing++;
    else
        $finished++;

    $fill=!$fill;
}

if($total==0)
{
    $pdf->Cell(190,10,"No production records found for this period.",1,1,"C");
}

$pdf->Ln(5);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(
    0,
    6,
    "Total Productions : ".$total.
    " | Ongoing : ".$ongoing.
    " | Finished : ".$finished,
    0,
    1
);

$pdf->Ln(5);
$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal production data.",0,"C");

if(ob_get_length())
    ob_end_clean();

$pdf->Output("I","Production_Report_".$startDate."_to_".$endDate.".pdf");
?>