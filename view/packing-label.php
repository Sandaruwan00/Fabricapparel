<?php
include_once '../model/packing_model.php';
include_once '../model/order_model.php';
include '../commons/fpdf186/fpdf.php';

$packing_id = $_GET["packing_id"];

$packingObj = new Packing();
$orderObj = new Order();

$packing = $packingObj->getPacking($packing_id)->fetch_assoc();
$items = $orderObj->getOrderItems($packing["order_id"]);

class PackingSlip extends FPDF
{
    function Header()
    {

    // Company logo (image path, X, Y, width)
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);

        // Company name
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        // Report subtitle
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "PACKING SLIP", 0, 1, "C");

        // Space after header
        $this->Ln(10);


        // Title Box
        $this->SetFillColor(230, 230, 230);
        $this->SetFont("Arial", "B", 16);
        $this->Ln(3);
    }

    function Footer()
    {
        // Move cursor to 20mm from bottom
        $this->SetY(-20);

        // Page number
        $this->SetFont("Arial", "I", 8);

        // Footer text
        $this->SetFont("Arial", "I", 7);
        $this->Cell(0, 5, "Fabric Apparel (PVT) LTD | www.fabricapparel.com", 0, 0, "C");
    }
}

$pdf = new PackingSlip("P", "mm", "A4");
$pdf->AddPage();


// ---------------- HEADER BOX ----------------
$pdf->SetFont("Arial", "", 12);

// Draw border box
$pdf->SetDrawColor(0, 0, 0);
$pdf->Rect(10, 30, 190, 25);

// Left
$pdf->SetXY(12, 32);
$pdf->Cell(95, 7, "Packing ID : PACK" . $packing["packing_id"], 0, 1);
$pdf->SetX(12);
$pdf->Cell(95, 7, "Order ID   : ORD" . $packing["order_id"], 0, 1);
$pdf->SetX(12);
$pdf->Cell(95, 7, "Production ID : PRO" . $packing["production_id"], 0, 1);

// Right
$pdf->SetXY(110, 32);
$pdf->Cell(90, 7, "Company : " . $packing["company_name"], 0, 1);
$pdf->SetX(110);
$pdf->Cell(90, 7, "Date    : " . date("Y-m-d"), 0, 1);

// Move below box
$pdf->Ln(15);

// ---------------- ITEMS (TABLE STYLE) ----------------
$pdf->Ln(2);

$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(0, 8, "PACKING ITEMS", 0, 1);

$pdf->SetDrawColor(180, 180, 180);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

$pdf->Ln(3);


// ---------------- TABLE HEADER ----------------
$pdf->SetFont("Arial", "B", 11);
$pdf->SetFillColor(200, 200, 200);

$pdf->Cell(15, 8, "#", 1, 0, "C", true);
$pdf->Cell(100, 8, "Product", 1, 0, "C", true);
$pdf->Cell(40, 8, "Size", 1, 0, "C", true);
$pdf->Cell(35, 8, "Qty", 1, 1, "C", true);


// ---------------- TABLE DATA ----------------
$pdf->SetFont("Arial", "", 10);

$count = 1;
$total = 0;
$fill = false;

while ($row = $items->fetch_assoc()) {

    $product = iconv('UTF-8', 'windows-1252', $row["product_type_name"]);
    $size    = iconv('UTF-8', 'windows-1252', $row["size_short_name"]);

    // zebra effect (optional clean look)
    if ($fill) {
        $pdf->SetFillColor(245, 245, 245);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }

    $pdf->Cell(15, 8, $count++, 1, 0, "C", true);
    $pdf->Cell(100, 8, $product, 1, 0, "L", true);
    $pdf->Cell(40, 8, $size, 1, 0, "C", true);
    $pdf->Cell(35, 8, $row["qty"], 1, 1, "C", true);

    $total += $row["qty"];
    $fill = !$fill;
}

// ---------------- TOTAL (LABEL STYLE) ----------------
$pdf->Ln(5);

$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);

$pdf->SetFont("Arial", "B", 14);
$pdf->Cell(0, 12, "TOTAL QTY : " . $total, 0, 1, "C", true);

// reset colors
$pdf->SetTextColor(0, 0, 0);


// ---------------- SIGNATURE SECTION ----------------
$pdf->Ln(15);

$pdf->SetFont("Arial", "", 10);

$pdf->Cell(90, 8, "Packed By: ____________________", 0, 0);
$pdf->Cell(90, 8, "Checked By: ____________________", 0, 1);

$pdf->Ln(5);
$pdf->Cell(90, 8, "Date: ________________", 0, 0);
$pdf->Cell(90, 8, "Signature: ________________", 0, 1);



// ---------------- OUTPUT ----------------
ob_end_clean();
$pdf->Output("I", "Packing_Slip_PACK_$packing_id.pdf");