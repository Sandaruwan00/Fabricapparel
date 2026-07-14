<?php
// ---------------- INCLUDE REQUIRED FILES ----------------

// User model – used to fetch user data from database
include_once '../model/user_model.php';

// FPDF library – used to generate PDF documents
include '../commons/fpdf186/fpdf.php';


// ---------------- PDF CLASS DEFINITION ----------------

// Extend FPDF to customize header, footer, and table layout
class UserReport extends FPDF
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
        $this->Cell(0, 6, "System Users Overview", 0, 1, "C");

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

    // ----------- TABLE HEADER -----------
    function TableHeader()
    {
        // Header font style
        $this->SetFont("Arial", "B", 11);

        // Background color for header row
        $this->SetFillColor(200, 200, 200);

        // Table column headers
        $this->Cell(10, 10, "#", 1, 0, "C", true);
        $this->Cell(50, 10, "Name", 1, 0, "C", true);
        $this->Cell(65, 10, "Email", 1, 0, "C", true);
        $this->Cell(35, 10, "Role", 1, 0, "C", true);
        $this->Cell(20, 10, "Status", 1, 1, "C", true);
    }
}


// ---------------- FETCH DATA FROM DATABASE ----------------

// Create User object
$userObj = new User();

// Get all users (assumed to include role name using JOIN)
$userResult = $userObj->getAllUsersForReport();

// Current date for report
$date = date("Y-m-d");
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF GENERATION ----------------

// Create PDF object (Portrait, mm units, A4 size)
$pdf = new UserReport("P", "mm", "A4");

// Enable total page number alias
$pdf->AliasNbPages();

// Set document title
$pdf->SetTitle("User Report - $date");

// Add first page
$pdf->AddPage();


// ---------------- REPORT META INFO ----------------

// Font for meta information
$pdf->SetFont("Arial", "", 10);

// Report date
$pdf->Cell(0, 8, "Generated On : $dateTime", 0, 1, "L");
$pdf->Ln(2);

// Draw horizontal line under date
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);


// ---------------- TABLE HEADER ----------------
$pdf->TableHeader();


// ---------------- TABLE DATA ----------------

$pdf->SetFont("Arial", "", 10);

// Row background toggle
$fill = false;

// Row counter
$count = 1;

// Status counters
$totalActive = 0;
$totalInactive = 0;

if ($userResult && $userResult->num_rows > 0) {

    // Loop through each user record
    while ($row = $userResult->fetch_assoc()) {

        // Check for page overflow and add new page if needed
        if ($pdf->GetY() > 270) {
            $pdf->AddPage();
            $pdf->TableHeader();
            $pdf->SetFont("Arial", "", 10);
        }

        // Convert status value to readable text
        $status = ($row["user_status"] == 1) ? "Active" : "Deactive";

        // Count active and inactive users
        if ($status == "Active") $totalActive++;
        else $totalInactive++;

        // Set alternating row background color
        if ($fill) $pdf->SetFillColor(245, 245, 245);
        else $pdf->SetFillColor(255, 255, 255);

        // Convert UTF-8 characters to Windows encoding (FPDF requirement)
        $fullName = iconv('UTF-8', 'windows-1252', $row['user_fname'] . " " . $row['user_lname']);
        $email    = iconv('UTF-8', 'windows-1252', $row['user_email']);
        $role     = iconv('UTF-8', 'windows-1252', $row['role_name'] ?? 'N/A');

        // Output table row
        $pdf->Cell(10, 8, $count++, 1, 0, "C", true);
        $pdf->Cell(50, 8, $fullName, 1, 0, "L", true);
        $pdf->Cell(65, 8, $email, 1, 0, "L", true);
        $pdf->Cell(35, 8, $role, 1, 0, "C", true);
        $pdf->Cell(20, 8, $status, 1, 1, "C", true);

        // Toggle row color
        $fill = !$fill;
    }

} else {
    // Message if no users exist
    $pdf->Cell(180, 10, "No users found in the system.", 1, 1, "C");
}


// ---------------- SUMMARY SECTION ----------------

$pdf->Ln(5);
$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0,6,"Total Users: " . ($totalActive + $totalInactive) ." | Active: $totalActive | Inactive: $totalInactive",0,1,"L");




// ---------------- FOOTER NOTES ----------------

$pdf->Ln(5);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a physical signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidentiality Notice: This document contains internal system data.", 0, "C");


// ---------------- OUTPUT PDF ----------------

// Clear output buffer to avoid PDF corruption
ob_end_clean();

// Display PDF in browser
$pdf->Output("I", "User_Report_Fabric_Apparel_$dateTime.pdf");
