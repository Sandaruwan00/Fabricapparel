<?php
include_once '../commons/session.php';
include_once '../model/stock_model.php';
include '../commons/fpdf186/fpdf.php';

class MaterialRequestReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Production Material Requests Report",0,1,"C");
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
        $this->Cell(23,10,"Request ID",1,0,"C",true);
        $this->Cell(80,10,"Stock Item",1,0,"C",true);
        $this->Cell(25,10,"Qty",1,0,"C",true);
        $this->Cell(25,10,"Date",1,0,"C",true);
        $this->Cell(25,10,"Status",1,1,"C",true);
    }
}

$startDate=$_POST["start_date"];
$endDate=$_POST["end_date"];

$stockObj=new Stock();
$result=$stockObj->getAllProductionStockRequest();

date_default_timezone_set("Asia/Colombo");
$dateTime=date("Y-m-d H:i:s");

$pdf=new MaterialRequestReport("P","mm","A4");
$pdf->startDate=$startDate;
$pdf->endDate=$endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("Production Material Requests Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".$dateTime,0,1);
$pdf->Ln(2);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

$pdf->TableHeader();
$pdf->SetFont("Arial","",8);

$count=1;
$totalRequests=0;
$pending=0;
$issued=0;
$rejected=0;
$fill=false;

while($row=$result->fetch_assoc())
{
    $requestDate=date("Y-m-d",strtotime($row["psr_date"]));

    if($requestDate<$startDate || $requestDate>$endDate)
        continue;

    if($pdf->GetY()>270)
    {
        $pdf->AddPage();
        $pdf->TableHeader();
        $pdf->SetFont("Arial","",8);
    }

    $item=iconv(
        "UTF-8",
        "windows-1252",
        $row["stock_item_name"]." ".$row["stock_item_color_code"]
    );

    $qty=$row["psr_qty"]." ".$row["stock_unit_short_name"];

    $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

    $pdf->Cell(12,8,$count++,1,0,"C",true);
    $pdf->Cell(23,8,$row["psr_id"],1,0,"C",true);
    $pdf->Cell(80,8,$item,1,0,"L",true);
    $pdf->Cell(25,8,$qty,1,0,"C",true);
    $pdf->Cell(25,8,$requestDate,1,0,"C",true);
    $pdf->Cell(25,8,$row["psr_status"],1,1,"C",true);

    $totalRequests++;

    if($row["psr_status"]=="Pending")
        $pending++;
    elseif($row["psr_status"]=="Issued")
        $issued++;
    else
        $rejected++;

    $fill=!$fill;
}

if($totalRequests==0)
{
    $pdf->Cell(190,10,"No material requests found for this period.",1,1,"C");
}

$pdf->Ln(5);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(
    0,
    6,
    "Total Requests : ".$totalRequests.
    " | Pending : ".$pending.
    " | Issued : ".$issued.
    " | Rejected : ".$rejected,
    0,
    1
);

$pdf->Ln(5);
$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal production data.",0,"C");

if(ob_get_length())
    ob_end_clean();

$pdf->Output("I","Production_Material_Request_Report_".$startDate."_to_".$endDate.".pdf");