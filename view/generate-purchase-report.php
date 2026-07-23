<?php
include_once '../commons/session.php';
include_once '../model/purchase_model.php';
include '../commons/fpdf186/fpdf.php';

class SupplierPurchaseReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);
        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");
        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Supplier Purchase Value Report",0,1,"C");
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
        $this->SetFont("Arial","B",10);
        $this->SetFillColor(200,200,200);
        $this->Cell(15,10,"#",1,0,"C",true);
        $this->Cell(110,10,"Supplier Name",1,0,"C",true);
        $this->Cell(65,10,"Purchase Value (Rs.)",1,1,"C",true);
    }
}

$startDate = $_POST["start_date"];
$endDate = $_POST["end_date"];

$purchaseObj = new Purchase();
$poResults = $purchaseObj->getPOs();

$supplierTotals = [];

while($row = $poResults->fetch_assoc())
{
    $poDate = date('Y-m-d', strtotime($row["po_created_at"]));

    if($poDate < $startDate || $poDate > $endDate)
        continue;

    $supplier = $row["supplier_name"];

    if(!isset($supplierTotals[$supplier]))
        $supplierTotals[$supplier] = 0;

    $supplierTotals[$supplier] += $row["total_price"];
}

date_default_timezone_set("Asia/Colombo");
$dateTime = date("Y-m-d H:i:s");

$pdf = new SupplierPurchaseReport("P","mm","A4");
$pdf->startDate = $startDate;
$pdf->endDate = $endDate;
$pdf->AliasNbPages();
$pdf->SetTitle("Supplier Purchase Value Report");
$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".$dateTime,0,1);
$pdf->Ln(2);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

$pdf->TableHeader();
$pdf->SetFont("Arial","",10);

$count = 1;
$total = 0;
$fill = false;

if(count($supplierTotals)>0)
{
    foreach($supplierTotals as $supplier=>$amount)
    {
        if($pdf->GetY()>270)
        {
            $pdf->AddPage();
            $pdf->TableHeader();
        }

        $pdf->SetFillColor($fill ? 245 : 255,$fill ? 245 : 255,$fill ? 245 : 255);

        $pdf->Cell(15,8,$count++,1,0,"C",true);
        $pdf->Cell(110,8,iconv("UTF-8","windows-1252",$supplier),1,0,"L",true);
        $pdf->Cell(65,8,number_format($amount,2),1,1,"R",true);

        $total += $amount;
        $fill = !$fill;
    }

    $pdf->SetFont("Arial","B",10);
    $pdf->SetFillColor(220,220,220);
    $pdf->Cell(125,10,"Total Purchase Value",1,0,"R",true);
    $pdf->Cell(65,10,"Rs ".number_format($total,2),1,1,"R",true);

}
else
{
    $pdf->Cell(190,10,"No purchase records found for this period.",1,1,"C");
}

$pdf->Ln(5);
$pdf->Cell(0,6,"Total Suppliers : ".count($supplierTotals)." | Total Purchase Value : Rs ".number_format($total,2),0,1);

$pdf->Ln(5);
$pdf->SetFont("Arial","I",9);
$pdf->MultiCell(0,5,"This is a computer-generated report and does not require a physical signature.",0,"C");
$pdf->MultiCell(0,5,"Confidentiality Notice: This document contains internal purchasing data.",0,"C");

if(ob_get_length())
    ob_end_clean();

$pdf->Output("I","Supplier_Purchase_Value_Report_".$startDate."_to_".$endDate.".pdf");
?>