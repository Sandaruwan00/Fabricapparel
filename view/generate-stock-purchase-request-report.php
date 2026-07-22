<?php
include_once '../commons/session.php';
include '../model/stock_model.php';
include '../commons/fpdf186/fpdf.php';

class PurchaseRequestReport extends FPDF
{
    public $startDate;
    public $endDate;

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);

        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Sent Purchase Request Report", 0, 1, "C");

        $this->SetFont("Arial", "", 9);
        $this->Cell(0, 6, "Period : ".$this->startDate." to ".$this->endDate, 0, 1, "C");

        $this->Ln(6);
    }

    function Footer()
    {
        $this->SetY(-20);

        $this->SetFont("Arial", "I", 8);
        $this->Cell(0, 5, "Page ".$this->PageNo()." / {nb}", 0, 1, "C");

        $this->SetFont("Arial", "I", 7);
        $this->Cell(0, 5, "Fabric Apparel (PVT) LTD | www.fabricapparel.com | Confidential", 0, 0, "C");
    }

    function TableHeader()
    {
        $this->SetFont("Arial","B",9);
        $this->SetFillColor(230,230,230);

        $this->Cell(18,8,"ID",1,0,"C",true);
        $this->Cell(75,8,"Item",1,0,"C",true);
        $this->Cell(25,8,"Quantity",1,0,"C",true);
        $this->Cell(35,8,"Requested Date",1,0,"C",true);
        $this->Cell(37,8,"Status",1,1,"C",true);
    }
}

//----------------------------------------------------
// INPUT
//----------------------------------------------------

$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate   = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$startDate)) {
    $startDate = date('Y-m-01');
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$endDate)) {
    $endDate = date('Y-m-d');
}

//----------------------------------------------------
// GET DATA
//----------------------------------------------------

$stockObj = new Stock();
$result = $stockObj->getAllPurchaseRequests();

$rows = [];
$totalRequests = 0;
$totalQty = 0;

while($row = $result->fetch_assoc()){

    $requestDate = date('Y-m-d', strtotime($row["requested_date"]));

    if($requestDate < $startDate || $requestDate > $endDate){
        continue;
    }

    

    $rows[] = $row;
    $totalRequests++;
    $totalQty += $row["requested_qty"];
}

date_default_timezone_set("Asia/Colombo");
$dateTime = date("Y-m-d H:i:s");

//----------------------------------------------------
// PDF
//----------------------------------------------------

$pdf = new PurchaseRequestReport("P","mm","A4");
$pdf->AliasNbPages();

$pdf->startDate = $startDate;
$pdf->endDate = $endDate;

$pdf->SetTitle("Sent Purchase Request Report");

$pdf->AddPage();

$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Generated On : ".$dateTime,0,1);

$pdf->Ln(2);

$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());

$pdf->Ln(5);


//----------------------------------------------------
// DETAILS
//----------------------------------------------------

$pdf->SetFont("Arial","B",12);
$pdf->Cell(0,8,"Purchase Requests",0,1);

$pdf->TableHeader();

$pdf->SetFont("Arial","",8);

$fill = false;

if(count($rows)>0){

    foreach($rows as $row){

        if($pdf->GetY()>270){
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial","",8);
        }

        $item = $row["stock_item_id"].
                " - ".
                $row["stock_item_name"].
                " ".
                $row["stock_item_color_code"];

        $qty = $row["requested_qty"]." ".$row["stock_unit_short_name"];

        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);

        $pdf->Cell(18,7,$row["stock_purchase_request_id"],1,0,"C",true);
        $pdf->Cell(75,7,iconv('UTF-8','windows-1252',$item),1,0,"L",true);
        $pdf->Cell(25,7,$qty,1,0,"C",true);
        $pdf->Cell(35,7,$row["requested_date"],1,0,"C",true);
        $pdf->Cell(37,7,$row["request_status"],1,1,"C",true);

        $fill = !$fill;
    }

}else{

    $pdf->Cell(190,10,"No Sent Purchase Requests Found.",1,1,"C");

}

//----------------------------------------------------
// FOOTER NOTE
//----------------------------------------------------

$pdf->Ln(8);

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
    "Confidentiality Notice: This document contains internal system data.",
    0,
    "C"
);

//----------------------------------------------------
// OUTPUT
//----------------------------------------------------

if(ob_get_length()){
    ob_end_clean();
}

$pdf->Output(
    "I",
    "Sent_Purchase_Request_Report_".$startDate."_to_".$endDate.".pdf"
);