<?php

include_once '../commons/session.php';
include_once '../model/stock_model.php';
include '../commons/fpdf186/fpdf.php';

class PurchaseRequestReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);

        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");

        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Purchase Requests Report",0,1,"C");

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

        $this->Cell(10,10,"#",1,0,"C",true);
        $this->Cell(25,10,"Request ID",1,0,"C",true);
        $this->Cell(65,10,"Item",1,0,"C",true);
        $this->Cell(25,10,"Qty",1,0,"C",true);
        $this->Cell(35,10,"Date",1,0,"C",true);
        $this->Cell(30,10,"Status",1,1,"C",true);
    }
}


// ---------------- INPUT ----------------

$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date("Y-m-01");
$endDate   = isset($_POST['end_date']) ? $_POST['end_date'] : date("Y-m-d");

$stockObj = new Stock();
$result = $stockObj->getAllPurchaseRequests();

$requestRows = [];

$totalPending = 0;
$totalSent = 0;
$totalCompleted = 0;

while($row = $result->fetch_assoc())
{
    $requestDate = date("Y-m-d",strtotime($row["requested_date"]));

    if($requestDate < $startDate || $requestDate > $endDate)
        continue;

    $requestRows[] = $row;

    if($row["request_status"]=="Pending")
        $totalPending++;

    if($row["request_status"]=="Sent")
        $totalSent++;

    if($row["request_status"]=="Completed")
        $totalCompleted++;
}

date_default_timezone_set("Asia/Colombo");
$dateTime = date("Y-m-d H:i:s");


// ---------------- PDF ----------------

$pdf = new PurchaseRequestReport("P","mm","A4");

$pdf->startDate=$startDate;
$pdf->endDate=$endDate;

$pdf->AliasNbPages();
$pdf->SetTitle("Purchase Requests Report");

$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".$dateTime,0,1);

$pdf->Ln(2);

$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());

$pdf->Ln(5);

$pdf->TableHeader();

$pdf->SetFont("Arial","",9);

$fill=false;
$count=1;

if(count($requestRows)>0)
{
    foreach($requestRows as $row)
    {
        if($pdf->GetY()>270)
        {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial","",9);
        }

        $item = iconv(
            'UTF-8',
            'windows-1252',
            $row["stock_item_name"]." ".$row["stock_item_color_code"]
        );

        $qty = $row["requested_qty"]." ".$row["stock_unit_name"];

        $pdf->SetFillColor($fill ? 245 : 255,$fill ? 245 : 255,$fill ? 245 : 255);

        $pdf->Cell(10,8,$count++,1,0,"C",true);
        $pdf->Cell(25,8,$row["stock_purchase_request_id"],1,0,"C",true);
        $pdf->Cell(65,8,$item,1,0,"L",true);
        $pdf->Cell(25,8,$qty,1,0,"C",true);
        $pdf->Cell(35,8,$row["requested_date"],1,0,"C",true);
        $pdf->Cell(30,8,$row["request_status"],1,1,"C",true);

        $fill=!$fill;
    }

}
else
{
    $pdf->Cell(190,10,"No purchase requests found for this period.",1,1,"C");
}


// ---------------- SUMMARY ----------------

$pdf->Ln(5);

$pdf->SetFont("Arial","B",10);

$pdf->Cell(
    0,
    6,
    "Total Requests : ".count($requestRows).
    "   |   Pending : ".$totalPending.
    "   |   Sent : ".$totalSent.
    "   |   Completed : ".$totalCompleted,
    0,
    1
);


// ---------------- FOOTER NOTES ----------------

$pdf->Ln(5);

$pdf->SetFont("Arial","I",9);

$pdf->MultiCell(
    0,
    5,
    "This is a computer-generated report and does not require a physical signature.",
    0,
    "C"
);

$pdf->MultiCell(
    0,
    5,
    "Confidentiality Notice: This document contains internal purchasing data.",
    0,
    "C"
);


// ---------------- OUTPUT ----------------

if(ob_get_length())
{
    ob_end_clean();
}

$pdf->Output(
    "I",
    "Purchase_Request_Report_".$startDate."_to_".$endDate.".pdf"
);

?>