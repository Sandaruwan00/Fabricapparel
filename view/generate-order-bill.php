<?php

include_once '../model/order_model.php';
include '../commons/fpdf186/fpdf.php';

// get user information from session
$userrow = $_SESSION["user"];

include_once '../model/permission_model.php';
$permissionObj = new Permission();
if (!$permissionObj->hasPermission($userrow["user_id"], 23)) {
    header("Location: access_denied.php");
    exit();
}


class OrderBill extends FPDF
{

    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        

        $this->Ln(10);

        // Line
        $this->Line(10,$this->GetY(),200,$this->GetY());

        $this->Ln(5);
    }


    function Footer()
    {
        $this->SetY(-20);

        $this->SetFont("Arial","I",8);

        $this->Cell(0,5,
        "Thank you for your business | Fabric Apparel (PVT) LTD",
        0,1,"C");

        $this->Cell(0,5,
        "Page ".$this->PageNo()."/{nb}",
        0,0,"C");
    }


    function TableHeader()
    {
        $this->SetFont("Arial","B",10);
        $this->SetFillColor(220,220,220);


        $this->Cell(45,8,"Product",1,0,"C",true);
        $this->Cell(20,8,"Size",1,0,"C",true);
        $this->Cell(20,8,"Qty",1,0,"C",true);
        $this->Cell(30,8,"Unit Price",1,0,"C",true);
        $this->Cell(35,8,"Amount",1,1,"C",true);

    }

}




$orderObj = new Order();


$order_id = base64_decode($_GET["order_id"]);


// Get order details
$orderResult = $orderObj->getOrder($order_id);
$order = $orderResult->fetch_assoc();


// Get items
$itemResult = $orderObj->getOrderItems($order_id);


// Get payments
$paymentResult = $orderObj->getOrderPayments($order_id);



$pdf = new OrderBill("P","mm","A4");


$pdf->AliasNbPages();

$pdf->SetTitle("Order Bill");


$pdf->AddPage();



// ---------------- BILL INFORMATION ----------------


$pdf->SetFont("Arial","B",12);

$pdf->Cell(0,8,
"INVOICE / BILL",
0,1,"C");


$pdf->Ln(5);



$pdf->SetFont("Arial","",10);



$pdf->Cell(100,6,
"Order No : ".$order["order_id"],
0,0);


$pdf->Cell(80,6,
"Date : ".$order["order_date"],
0,1);


$pdf->Ln(5);




// CUSTOMER DETAILS


$pdf->SetFont("Arial","B",11);

$pdf->Cell(0,7,"Customer Details",0,1);


$pdf->SetFont("Arial","",10);


$pdf->Cell(0,6,
"Company : ".$order["company_name"],
0,1);


$pdf->Cell(0,6,
"Contact Person : ".$order["contact_name"],
0,1);



$pdf->Ln(8);



// ITEMS TABLE


$pdf->SetFont("Arial","B",11);

$pdf->Cell(0,7,"Order Items",0,1);


$pdf->TableHeader();



$pdf->SetFont("Arial","",10);


$itemTotal = 0;


while($item=$itemResult->fetch_assoc())
{


    $amount = $item["qty"] * $item["unit_price"];

    $itemTotal += $amount;


    $pdf->Cell(45,8,
    $item["product_type_name"],
    1);


    $pdf->Cell(20,8,
    $item["size_short_name"],
    1,"","C");


    $pdf->Cell(20,8,
    $item["qty"],
    1,"","C");


    $pdf->Cell(30,8,
    number_format($item["unit_price"],2),
    1,"","R");


    $pdf->Cell(35,8,
    number_format($amount,2),
    1,1,"R");

}



$pdf->Ln(8);



// PAYMENT CALCULATION


$totalPayment=0;


while($pay=$paymentResult->fetch_assoc())
{
    if($pay["payment_status"]=="Approved")
    {
        $totalPayment += $pay["amount"];
    }
}



$totalCost =
$order["total_amount"] + $order["delivery_charge"];


$due =
$totalCost - $totalPayment;



// SUMMARY


$pdf->SetFont("Arial","B",11);


$pdf->Cell(0,7,"Payment Summary",0,1);



$pdf->SetFont("Arial","",10);


$pdf->Cell(100,7,"Order Amount",0,0);

$pdf->Cell(40,7,
"Rs ".number_format($order["total_amount"],2),
0,1,"R");



$pdf->Cell(100,7,"Delivery Charge",0,0);

$pdf->Cell(40,7,
"Rs ".number_format($order["delivery_charge"],2),
0,1,"R");



$pdf->Cell(100,7,"Total Cost",0,0);

$pdf->Cell(40,7,
"Rs ".number_format($totalCost,2),
0,1,"R");



$pdf->Cell(100,7,"Paid Amount",0,0);

$pdf->Cell(40,7,
"Rs ".number_format($totalPayment,2),
0,1,"R");



$pdf->SetFont("Arial","B",10);


$pdf->Cell(100,7,"Balance Due",0,0);

$pdf->Cell(40,7,
"Rs ".number_format($due,2),
0,1,"R");




// Notes

$pdf->Ln(10);

$pdf->SetFont("Arial","I",9);

$pdf->MultiCell(0,5,
"This invoice is computer generated and does not require a signature.",
0,"C");



ob_end_clean();


$pdf->Output(
"I",
"Order_Bill_".$order_id.".pdf"
);

?>