<?php
// ---------------- INCLUDE REQUIRED FILES ----------------

// Session check
include_once '../commons/session.php';

// Stock model – used to fetch stock data from database
include_once '../model/stock_model.php';

// FPDF library – used to generate PDF documents
include '../commons/fpdf186/fpdf.php';


// ---------------- PDF CLASS DEFINITION ----------------

// Extend FPDF to customize header, footer, and table layouts
class StockReport extends FPDF
{
    // ----------- PAGE HEADER -----------
    function Header()
    {
        // Company logo (image path, X, Y, width)
        $this->Image("../images/logo/logo_white_background.jpg", 45, 10, 20);

        // Company name
        $this->SetFont("Arial", "B", 16);
        $this->Cell(0, 10, "Fabric Apparel (PVT) LTD.", 0, 1, "C");

        // Report subtitle
        $this->SetFont("Arial", "I", 10);
        $this->Cell(0, 6, "Stock Material Categories", 0, 1, "C");

        // Space after header
        $this->Ln(10);
    }

    // ----------- PAGE FOOTER -----------
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

    // ----------- SECTION TITLE -----------
    function SectionTitle($label)
    {
        $this->SetFont("Arial", "B", 12);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 8, $label, 0, 1, "L", true);
        $this->Ln(2);
    }

   
    // ----------- TABLE HEADER: CATEGORIES -----------
    function TableHeaderCategories()
    {
        $this->SetFont("Arial", "B", 10);
        $this->SetFillColor(200, 200, 200);

        $this->Cell(10, 10, "#", 1, 0, "C", true);
        $this->Cell(45, 10, "Category Name", 1, 0, "C", true);
        $this->Cell(90, 10, "Description", 1, 0, "C", true);
        $this->Cell(25, 10, "Status", 1, 1, "C", true);
    }


}


// ---------------- FETCH DATA FROM DATABASE ----------------

// Create Stock object
$stockObj = new Stock();

// Get all stock data
$stockItemResult     = $stockObj->getAllStockItems();
$stockCategoryResult = $stockObj->getAllStockCategories();
$stockUnitResult     = $stockObj->getAllStockUnits();

// Current date for report
date_default_timezone_set('Asia/Colombo');
$date     = date("Y-m-d");
$dateTime = date("Y-m-d H:i:s");


// ---------------- PDF GENERATION ----------------

// Create PDF object (Portrait, mm units, A4 size)
$pdf = new StockReport("P", "mm", "A4");

// Enable total page number alias
$pdf->AliasNbPages();

// Set document title
$pdf->SetTitle("Stock Material Categories - $date");

// Add first page
$pdf->AddPage();


// ---------------- REPORT META INFO ----------------

$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);

$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);




// =========================================================
// SECTION 2: CATEGORIES
// =========================================================



$pdf->TableHeaderCategories();
$pdf->SetFont("Arial", "", 9);

$fill = false;
$count = 1;
$totalActiveCategories = 0;
$totalInactiveCategories = 0;

if ($stockCategoryResult && $stockCategoryResult->num_rows > 0) {

    while ($row = $stockCategoryResult->fetch_assoc()) {

        if ($pdf->GetY() > 260) {
            $pdf->AddPage();
            $pdf->SectionTitle("2. Categories (cont.)");
            $pdf->TableHeaderCategories();
            $pdf->SetFont("Arial", "", 9);
        }

        $status = ($row["stock_category_status"] == 1) ? "Active" : "Deactive";
        if ($status == "Active") $totalActiveCategories++;
        else $totalInactiveCategories++;

        if ($fill) $pdf->SetFillColor(245, 245, 245);
        else $pdf->SetFillColor(255, 255, 255);

        $categoryName = iconv('UTF-8', 'windows-1252', $row['stock_category_name']);
        $description  = iconv('UTF-8', 'windows-1252', $row['stock_category_description']);

        $pdf->Cell(10, 8, $count++, 1, 0, "C", true);
        $pdf->Cell(45, 8, $categoryName, 1, 0, "L", true);
        $pdf->Cell(90, 8, $description, 1, 0, "L", true);
        $pdf->Cell(25, 8, $status, 1, 1, "C", true);

        $fill = !$fill;
    }
} else {
    $pdf->Cell(170, 10, "No categories found.", 1, 1, "C");
}

$pdf->Ln(3);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0, 6, "Total Categories: " . ($totalActiveCategories + $totalInactiveCategories) . " | Active: $totalActiveCategories | Inactive: $totalInactiveCategories", 0, 1, "L");
$pdf->Ln(6);





// ---------------- FOOTER NOTES ----------------

$pdf->Ln(8);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a physical signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidentiality Notice: This document contains internal system data.", 0, "C");


// ---------------- OUTPUT PDF ----------------

// Clear output buffer to avoid PDF corruption
ob_end_clean();

// Display PDF in browser
$pdf->Output("I", "Stock_Material_Categories_Fabric_Apparel_$dateTime.pdf");