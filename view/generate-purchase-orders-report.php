<?php

include_once '../commons/session.php';
include_once '../model/purchase_model.php';
include '../commons/fpdf186/fpdf.php';

class PurchaseOrderReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg",45,10,20);

        $this->SetFont("Arial","B",16);
        $this->Cell(0,10,"Fabric Apparel (PVT) LTD.",0,1,"C");

        $this->SetFont("Arial","I",10);
        $this->Cell(0,6,"Purchase Orders Report",0,1,"C");

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
        $this->Cell(0,5,"Fabric Apparel (PVT) LTD | www.fabricapparel.com | confidential",0,0,"C");
    }

    function TableHeader()
    {
        $this->SetFont("Arial","B",9);
        $this->SetFillColor(200,200,200);

        $this->Cell(10,10,"#",1,0,"C",true);
        $this->Cell(18,10,"PO ID",1,0,"C",true);
        $this->Cell(42,10,"Supplier",1,0,"C",true);
        $this->Cell(50,10,"Item",1,0,"C",true);
        $this->Cell(20,10,"Qty",1,0,"C",true);
        $this->Cell(25,10,"Amount",1,0,"C",true);
        $this->Cell(25,10,"Status",1,1,"C",true);
    }
}

// ---------------- INPUT ----------------

$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate   = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate))
    $startDate = date('Y-m-01');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate))
    $endDate = date('Y-m-d');

$purchaseObj = new Purchase();
$result = $purchaseObj->getPOs();

$poRows = [];

$totalAmount = 0;
$totalPending = 0;
$totalConfirmed = 0;
$totalDelivered = 0;
$totalPaid = 0;
$totalRejected = 0;

while($row = $result->fetch_assoc())
{
    // Replace po_date with your PO date column if different
    $poDate = date('Y-m-d', strtotime($row["po_created_at"]));

    if($poDate < $startDate || $poDate > $endDate)
        continue;

    $poRows[] = $row;

    $totalAmount += $row["total_price"];

    switch($row["po_status"])
    {
        case "Pending":
            $totalPending++;
            break;

        case "Confirmed":
            $totalConfirmed++;
            break;

        case "Delivered":
            $totalDelivered++;
            break;

        case "Paid":
            $totalPaid++;
            break;

        case "Rejected":
            $totalRejected++;
            break;
    }
}

date_default_timezone_set("Asia/Colombo");
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF ----------------

$pdf = new PurchaseOrderReport("P","mm","A4");
$pdf->AliasNbPages();

$pdf->startDate = $startDate;
$pdf->endDate = $endDate;

$pdf->SetTitle("Purchase Orders Report");
$pdf->AddPage();

// Meta
$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".$dateTime,0,1);

$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

$pdf->TableHeader();

$pdf->SetFont("Arial","",8);

$fill=false;
$count=1;

if(count($poRows)>0)
{
    foreach($poRows as $row)
    {
        if($pdf->GetY()>270)
        {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial","",8);
        }

        $supplier = iconv('UTF-8','windows-1252',$row["supplier_name"]);
        $item = iconv(
            'UTF-8',
            'windows-1252',
            $row["stock_item_name"]." ".$row["stock_item_color_code"]
        );

        $qty = $row["ordered_qty"]." ".$row["stock_unit_name"];

        $pdf->SetFillColor($fill ? 245 : 255,$fill ? 245 : 255,$fill ? 245 : 255);

        $pdf->Cell(10,8,$count++,1,0,"C",true);
        $pdf->Cell(18,8,$row["po_id"],1,0,"C",true);
        $pdf->Cell(42,8,$supplier,1,0,"L",true);
        $pdf->Cell(50,8,$item,1,0,"L",true);
        $pdf->Cell(20,8,$qty,1,0,"C",true);
        $pdf->Cell(25,8,number_format($row["total_price"],2),1,0,"R",true);
        $pdf->Cell(25,8,$row["po_status"],1,1,"C",true);

        $fill=!$fill;
    }

    // Total Row
    $pdf->SetFont("Arial","B",9);
    $pdf->SetFillColor(220,220,220);

    $pdf->Cell(140,9,"Total Purchase Order Amount",1,0,"R",true);
    $pdf->Cell(25,9,"Rs ".number_format($totalAmount,2),1,0,"R",true);
    $pdf->Cell(25,9,"",1,1,true);

}
else
{
    $pdf->SetFont("Arial","",10);
    $pdf->Cell(190,10,"No purchase orders found for this period.",1,1,"C");
}

// Summary

$pdf->Ln(5);

$pdf->SetFont("Arial","B",10);

$pdf->Cell(0,6,
"Total Orders : ".count($poRows).
" | Pending : ".$totalPending.
" | Confirmed : ".$totalConfirmed.
" | Delivered : ".$totalDelivered.
" | Paid : ".$totalPaid.
" | Rejected : ".$totalRejected,
0,1);

// Notes

$pdf->Ln(5);

$pdf->SetFont("Arial","I",9);

$pdf->MultiCell(
0,
5,
"This is a computer-generated report and does not require a physical signature.",
0,
"C");

$pdf->MultiCell(
0,
5,
"Confidentiality Notice: This document contains internal purchasing data.",
0,
"C");

// Output

if(ob_get_length())
    ob_end_clean();

$pdf->Output(
"I",
"Purchase_Order_Report_".$startDate."_to_".$endDate.".pdf"
);