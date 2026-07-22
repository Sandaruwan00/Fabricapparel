<?php
include_once '../commons/session.php';
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

    // One full product block: info bar at top + a large size chart below it
    function ProductPage($product_type, $size, $price, $status_text, $img_path, $include_size_chart)
    {
        // ---- Product Info Bar ----
        $this->SetFont("Arial", "B", 14);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(0, 12, $product_type, 1, 1, "C", true);

        $this->SetFont("Arial", "B", 11);
        $this->SetFillColor(245, 245, 245);

        $this->Cell(63, 10, "Size: " . $size, 1, 0, "C", true);
        $this->Cell(63, 10, "Price: Rs " . $price, 1, 0, "C", true);
        $this->Cell(64, 10, "Status: " . $status_text, 1, 1, "C", true);

        $this->Ln(6);

        // ---- Large Size Chart ----
        if ($include_size_chart) {

            $this->SetFont("Arial", "B", 12);
            $this->Cell(0, 8, "Size Chart", 0, 1, "L");
            $this->Ln(2);

            // Reserve almost the rest of the page for the image
            $boxX = 15;
            $boxY = $this->GetY();
            $boxWidth = 180;
            $boxHeight = 180; // leaves room for footer

            // Draw a bordered frame for the chart area
            $this->Rect($boxX, $boxY, $boxWidth, $boxHeight);

            if (!empty($img_path) && file_exists($img_path)) {

                // Get actual image dimensions to scale it proportionally
                // while keeping it centered and as large as possible inside the box
                list($imgWidthPx, $imgHeightPx) = getimagesize($img_path);
                $imgRatio = $imgWidthPx / $imgHeightPx;
                $boxRatio = $boxWidth / $boxHeight;

                if ($imgRatio > $boxRatio) {
                    // Image is relatively wider - fit to box width
                    $drawWidth = $boxWidth - 10;
                    $drawHeight = $drawWidth / $imgRatio;
                } else {
                    // Image is relatively taller - fit to box height
                    $drawHeight = $boxHeight - 10;
                    $drawWidth = $drawHeight * $imgRatio;
                }

                $drawX = $boxX + (($boxWidth - $drawWidth) / 2);
                $drawY = $boxY + (($boxHeight - $drawHeight) / 2);

                $this->Image($img_path, $drawX, $drawY, $drawWidth, $drawHeight);

            } else {
                $this->SetFont("Arial", "I", 12);
                $this->SetTextColor(180, 180, 180);
                $this->SetXY($boxX, $boxY + ($boxHeight / 2) - 5);
                $this->Cell($boxWidth, 10, "No Size Chart Available", 0, 1, "C");
                $this->SetTextColor(0, 0, 0);
            }

            $this->SetY($boxY + $boxHeight);
        }
    }
}

// ---------------- INPUT (from generate-price-report.php filter form) ----------------

// Selected product type IDs - empty array means "all"
$selectedProductTypes = isset($_POST['product_type']) ? $_POST['product_type'] : [];

// Selected size IDs - empty array means "all"
$selectedSizes = isset($_POST['size']) ? $_POST['size'] : [];

// Selected statuses (1 = Active, 0 = Inactive) - empty array means "all"
$selectedStatuses = isset($_POST['status']) ? $_POST['status'] : [];


$include_size_chart = true;

// ---------------- FETCH PRICING DATA ----------------

$priceObj = new Price();
$priceResult = $priceObj->getAllPricing();

$data = [];

if ($priceResult && $priceResult->num_rows > 0) {
    while ($row = $priceResult->fetch_assoc()) {

        // Filter by product type (skip filter if nothing was selected)
        if (!empty($selectedProductTypes) && !in_array($row['product_type_id'], $selectedProductTypes)) {
            continue;
        }

        // Filter by size (skip filter if nothing was selected)
        if (!empty($selectedSizes) && !in_array($row['size_id'], $selectedSizes)) {
            continue;
        }

        // Filter by status (skip filter if nothing was selected)
        if (!empty($selectedStatuses) && !in_array((string)$row['pricing_status'], $selectedStatuses)) {
            continue;
        }

        $data[] = $row;
    }
}

date_default_timezone_set('Asia/Colombo');
$dateTime = date("Y-m-d H:i:s");

// ---------------- GENERATE PDF ----------------
$pdf = new PricingReport("P", "mm", "A4");
$pdf->AliasNbPages();
$pdf->SetTitle("Pricing Report - " . date("Y-m-d"));

if (!empty($data)) {

    foreach ($data as $row) {

        $pdf->AddPage();

        // Report Meta (repeated on each product's page)
        $pdf->SetFont("Arial", "", 10);
        $pdf->Cell(0, 8, "Report Date : " . $dateTime, 0, 1, "L");
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(5);

        $status_text = $row['pricing_status'] == 1 ? "Active" : "Inactive";
        $img_path = '../images/size_charts/' . $row['size_chart_image'];

        $pdf->ProductPage(
            $row['product_type_name'],
            $row['size_short_name'],
            number_format($row['price'], 2),
            $status_text,
            $img_path,
            $include_size_chart
        );
    }

} else {
    $pdf->AddPage();
    $pdf->SetFont("Arial", "", 10);
    $pdf->Cell(0, 8, "Report Date : " . $dateTime, 0, 1, "L");
    $pdf->Ln(5);
    $pdf->Cell(190, 10, "No pricing data found matching the selected filters.", 1, 1, "C");
}

// ---------------- OUTPUT ----------------
if (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output("I", "Pricing_Report_" . $dateTime . ".pdf");
exit;