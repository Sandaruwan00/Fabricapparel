<?php
include_once '../commons/session.php';
include_once '../model/warehouse_model.php';
include '../commons/fpdf186/fpdf.php';

class WarehouseActivityReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Warehouse Activity Report",0,1,"C");
        $this->SetFont("Arial","",9);
        $this->Cell(0,6,"Period: ".$this->startDate." to ".$this->endDate,0,1,"C");
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
        $this->Cell(12,8,"#",1,0,"C",true);
        $this->Cell(25,8,"Date",1,0,"C",true);
        $this->Cell(30,8,"Order ID",1,0,"C",true);
        $this->Cell(50,8,"Company Name",1,0,"C",true);
        $this->Cell(35,8,"Activity",1,0,"C",true);
        $this->Cell(38,8,"Changed By",1,1,"C",true);
    }
}

$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$startDate)) $startDate=date('Y-m-01');
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$endDate)) $endDate=date('Y-m-d');

$warehouseObj = new Warehouse();
$result = $warehouseObj->getWarehouseActivityReport();

date_default_timezone_set("Asia/Colombo");

$pdf = new WarehouseActivityReport("P","mm","A4");
$pdf->startDate = $startDate;
$pdf->endDate = $endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("Warehouse Activity Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".date("Y-m-d H:i:s"),0,1,"L");
$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

$pdf->TableHeader();
$pdf->SetFont("Arial","",8);

$fill=false;
$total=0;
$stored=0;
$dispatched=0;

while($row=$result->fetch_assoc())
{
    $date=date("Y-m-d",strtotime($row["changed_at"]));

    if($date<$startDate || $date>$endDate) continue;

    if($pdf->GetY()>270)
    {
        $pdf->AddPage();
        $pdf->TableHeader();
        $pdf->SetFont("Arial","",8);
    }

    if($row["status_id"]==10)
    {
        $activity="Stored";
        $stored++;
    }
    else
    {
        $activity="Dispatched";
        $dispatched++;
    }

    $total++;

    $pdf->SetFillColor($fill?245:255,$fill?245:255,$fill?245:255);

    $pdf->Cell(12,7,$total,1,0,"C",true);
    $pdf->Cell(25,7,$date,1,0,"C",true);
    $pdf->Cell(30,7,"ORD".$row["order_id"],1,0,"C",true);
    $pdf->Cell(50,7,iconv("UTF-8","windows-1252",$row["company_name"]),1,0,"L",true);
    $pdf->Cell(35,7,$activity,1,0,"C",true);
    $pdf->Cell(38,7,iconv("UTF-8","windows-1252",$row["user_fname"]." ".$row["user_lname"]),1,1,"C",true);

    $fill=!$fill;
}

if($total==0)
{
    $pdf->Cell(190,10,"No warehouse activities found for this period.",1,1,"C");
}

$pdf->Ln(6);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(0,6,"Warehouse Activity Summary",0,1);

$pdf->SetFont("Arial","",9);
$pdf->Cell(0,6,"Total Activities : ".$total,0,1);
$pdf->Cell(0,6,"Stored in Warehouse : ".$stored,0,1);
$pdf->Cell(0,6,"Dispatched from Warehouse : ".$dispatched,0,1);

$pdf->Ln(8);
$pdf->SetFont("Arial","I",9);

$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal warehouse data.",0,"C");

if(ob_get_length()) ob_end_clean();

$pdf->Output("I","Warehouse_Activity_Report_".$startDate."_to_".$endDate.".pdf");
?>