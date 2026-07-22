<?php
// ---------------- INCLUDE FILES ----------------
include_once '../model/buyer_model.php';
include_once '../commons/fpdf186/fpdf.php';

// ---------------- PDF CLASS ----------------
class BuyerReport extends FPDF
{
    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);

        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Buyer Detailed Report", 0, 1, "C");

        $this->Ln(10);
    }

    function Footer()
    {
        // Move cursor to 20mm from bottom
        $this->SetY(-20);

        // Page number
        $this->SetFont("Arial", "I", 8);
        $this->Cell(0, 5, "Page " . $this->PageNo() . " / {nb}", 0, 1, "C");

        // Footer text
        $this->SetFont("Arial", "I", 7);
        $this->Cell(0, 5, "Fabric Apparel (PVT) LTD | www.fabricapparel.com | confidential", 0, 0, "C");
    }

    function SectionTitle($title)
    {
        $this->SetFont("Arial", "B", 12);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 8, $title, 0, 1, "L", true);
        $this->Ln(2);
    }

    function DataRow($label, $value)
    {
        $this->SetFont("Arial", "B", 10);
        $this->Cell(50, 8, $label, 1);

        $this->SetFont("Arial", "", 10);
        $this->Cell(0, 8, $value, 1, 1);
    }

    function OrderHistoryHeader()
{
    $this->SetFont('Arial', 'B', 9);
    $this->SetFillColor(220, 220, 220);

    $this->Cell(20, 8, 'Order ID', 1, 0, 'C', true);
    $this->Cell(28, 8, 'Order Date', 1, 0, 'C', true);
    $this->Cell(50, 8, 'Status', 1, 0, 'C', true);
    $this->Cell(30, 8, 'Amount (LKR)', 1, 1, 'C', true);
}
}

// ---------------- GET DATA ----------------
$buyerObj = new Buyer();

$company_id = $_GET["company_id"];

$buyerResult = $buyerObj->getBuyerCompany($company_id);
$buyer = $buyerResult->fetch_assoc();

$businessTypeResult = $buyerObj->viewBusinessType($company_id);
$business = $businessTypeResult->fetch_assoc();

// Get Order History
$orderHistoryResult = $buyerObj->getOrderHistory($company_id);

date_default_timezone_set('Asia/Colombo');
$date = date("Y-m-d");
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF INIT ----------------
$pdf = new BuyerReport("P", "mm", "A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Buyer Report - " . $buyer["company_name"]);
$pdf->AddPage();

// ---------------- META ----------------
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);

// Draw horizontal line under date
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ---------------- COMPANY INFO ----------------
$pdf->SectionTitle("Company Information");

$pdf->DataRow("Company Name", $buyer["company_name"]);
$pdf->DataRow("Registration No", $buyer["company_registration"]);
$pdf->DataRow("Business Type", $business["business_type_name"]);
$pdf->DataRow("Website", $buyer["website"]);

// Address (multi-line)
$address = $buyer["company_address_line_1"] . ", " .
           $buyer["company_address_line_2"] . ", " .
           $buyer["company_city"] . ", " .
           $buyer["company_postal_code"] . ", " .
           $buyer["company_country"];

$pdf->DataRow("Address", $address);

// ---------------- CONTACT PERSON ----------------
$pdf->Ln(3);
$pdf->SectionTitle("Contact Person Details");

$pdf->DataRow("Full Name", $buyer["contact_name"]);
$pdf->DataRow("Job Title", $buyer["job_title"]);
$pdf->DataRow("Email", $buyer["contact_email"]);
$pdf->DataRow("NIC", $buyer["contact_nic"]);
$pdf->DataRow("Phone", $buyer["contact_phone"]);

// ---------------- OFFICE ADDRESS ----------------
$pdf->Ln(3);
$pdf->SectionTitle("Office Address");

$officeAddress = $buyer["office_address_line_1"] . ", " .
                 $buyer["office_address_line_2"] . ", " .
                 $buyer["office_address_line_3"];

$pdf->MultiCell(0, 8, $officeAddress, 1);

$pdf->Ln(5);
$pdf->SectionTitle("Order History");

if ($orderHistoryResult->num_rows > 0) {

    $pdf->OrderHistoryHeader();
    $pdf->SetFont('Arial', '', 9);

    $totalOrders = 0;
    $grandTotal = 0;

    while ($row = $orderHistoryResult->fetch_assoc()) {

        $totalOrders++;

        $amount = $row["total_amount"] + $row["delivery_charge"];
        $grandTotal += $amount;

        // Prevent table from running off page
        if ($pdf->GetY() > 255) {
            $pdf->AddPage();
            $pdf->SectionTitle("Order History (Continued)");
            $pdf->OrderHistoryHeader();
            $pdf->SetFont('Arial', '', 9);
        }

        $pdf->Cell(20, 8, "ORD" . $row["order_id"], 1, 0, 'C');
        $pdf->Cell(28, 8, $row["order_date"], 1, 0, 'C');

       
        $pdf->Cell(50, 8, $row["status_name"], 1, 0, 'C');


        // Amount
        $pdf->Cell(30, 8, number_format($amount, 2), 1, 1, 'R');
    }

    // Summary
    $pdf->SetFont('Arial', 'B', 10);

    $pdf->Cell(78, 9, 'Total Orders', 1, 0, 'R');
    $pdf->Cell(20, 9, $totalOrders, 1, 0, 'C');
    $pdf->Cell(30, 9, number_format($grandTotal, 2), 1, 1, 'R');

} else {

    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 8, 'No orders found for this buyer.', 1, 1, 'C');

}

// ---------------- FOOTER NOTES ----------------
$pdf->Ln(5);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidential: Internal system data.", 0, "C");

// ---------------- OUTPUT ----------------
ob_end_clean();
$pdf->Output("I", "Buyer_Report_{$buyer["company_name"]}_$dateTime.pdf");