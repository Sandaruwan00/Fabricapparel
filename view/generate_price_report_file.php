<?php
include_once '../model/price_model.php';
include '../commons/fpdf186/fpdf.php';

// ---------------- PDF CLASS ----------------
class PricingReport extends FPDF
{
    function Header()
    {
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Pricing Report", 0, 1, "C");

        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont("Arial", "I", 8);
        $this->Cell(0, 5, "Page " . $this->PageNo() . " / {nb}", 0, 1, "C");

        $this->SetFont("Arial", "I", 7);
        $this->Cell(0, 5, "Fabric Apparel (PVT) LTD | www.fabricapparel.com | confidential", 0, 0, "C");
    }

    function TableHeader()
    {
        $this->SetFont("Arial", "B", 11);
        $this->SetFillColor(200, 200, 200);

        $this->Cell(60, 10, "Product Type", 1, 0, "C", true);
        $this->Cell(35, 10, "Size",         1, 0, "C", true);
        $this->Cell(35, 10, "Price",        1, 0, "C", true);
        $this->Cell(60, 10, "Status",       1, 0, "C", true);

        $this->Ln();
    }

    // Pricing data row (4 columns, full width = 190mm)
    function PricingRow($product_type, $size, $price, $status_text, $fill)
    {
        $this->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $this->SetFont("Arial", "", 10);

        $this->Cell(60, 10, $product_type, 1, 0, "L", true);
        $this->Cell(35, 10, $size,         1, 0, "C", true);
        $this->Cell(35, 10, $price,        1, 0, "C", true);
        $this->Cell(60, 10, $status_text,  1, 0, "C", true);

        $this->Ln();
    }

    // Size chart row — spans full width, renders image or fallback text
    function SizeChartRow($img_path, $fill)
    {
        $img_height  = 45; // height of the image row in mm
        $row_padding = 2;  // padding inside the cell

        // Check page break before drawing
        if ($this->GetY() + $img_height > 270) {
            $this->AddPage();
            $this->TableHeader();
            $this->SetFont("Arial", "", 10);
        }

        $this->SetFillColor($fill ? 240 : 250, $fill ? 240 : 250, $fill ? 248 : 255);
        $this->SetFont("Arial", "I", 9);

        // Label cell on the left
        $this->Cell(30, $img_height, "Size Chart:", 1, 0, "R", true);

        $x = $this->GetX();
        $y = $this->GetY();

        // Image area cell (remaining width = 160mm)
        if (!empty($img_path) && file_exists($img_path)) {
            $this->Cell(160, $img_height, '', 1, 0, 'C', true); // bordered placeholder
            $this->Image($img_path, $x + $row_padding, $y + $row_padding, 0, $img_height - ($row_padding * 2));
        } else {
            $this->SetTextColor(180, 180, 180);
            $this->Cell(160, $img_height, 'No Size Chart Available', 1, 0, 'C', true);
            $this->SetTextColor(0, 0, 0);
        }

        $this->Ln();

        // Small spacer between groups
        $this->Ln(2);
    }
}


// ---------------- GENERATE PDF ----------------
$pdf = new PricingReport("P", "mm", "A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Pricing Report - " . date("Y-m-d"));
$pdf->AddPage();

// Report Meta
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Report Date : " . date("Y-m-d"), 0, 1, "L");
$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// Table Header
$pdf->TableHeader();

$fill = false;

if (!empty($data)) {
    foreach ($data as $row) {

        $status_text = $row['pricing_status'] == 1 ? "Active" : "Inactive";

        // Page break check for pricing row (10mm) + possible chart row (45mm)
        $needed_height = 10 + ($include_size_chart ? 47 : 0);
        if ($pdf->GetY() + $needed_height > 270) {
            $pdf->AddPage();
            $pdf->TableHeader();
        }

        // 1. Pricing data row
        $pdf->PricingRow(
            $row['product_type_name'],
            $row['size_short_name'],
            $row['price'],
            $status_text,
            $fill
        );

        // 2. Size chart row (separate, below the data row)
        if ($include_size_chart) {
            $img_path = '../images/size_charts/' . $row['size_chart_image'];
            $pdf->SizeChartRow($img_path, $fill);
        }

        $fill = !$fill;
    }
} else {
    $pdf->Cell(190, 10, "No pricing data found.", 1, 1, "C");
}

// ---------------- OUTPUT ----------------
ob_end_clean();
$pdf->Output("I", "Pricing_Report_" . date("Y-m-d") . ".pdf");
exit;
?>