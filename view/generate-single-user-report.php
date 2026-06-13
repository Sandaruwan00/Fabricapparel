<?php
include_once '../commons/session.php';
include_once '../model/user_model.php';
include_once '../model/module_model.php';
include_once '../commons/fpdf186/fpdf.php';

// ---------------- SECURITY CHECK ----------------
if (!isset($_GET["user_id"]) || empty($_GET["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_GET["user_id"]); // safer than raw value

// ---------------- FETCH DATA ----------------
$userObj = new User();

// Main user
$userResult = $userObj->getUser($user_id);

if (!$userResult || $userResult->num_rows == 0) {
    die("Invalid user.");
}

$userdetailrow = $userResult->fetch_assoc();

// Contacts
$usercontactResult = $userObj->getUserContact($user_id);
$contactrow1 = $usercontactResult ? $usercontactResult->fetch_assoc() : null;
$contactrow2 = $usercontactResult ? $usercontactResult->fetch_assoc() : null;

// Functions
$functionArray = [];
$userfunctionResult = $userObj->getUserFunctions($user_id);
if ($userfunctionResult) {
    while ($row = $userfunctionResult->fetch_assoc()) {
        $functionArray[] = $row["fun_id"];
    }
}

// Modules + permissions
$permissionsData = [];
$moduleResult = $userObj->getRoleModules($userdetailrow["user_role"]);

if ($moduleResult) {
    while ($module_row = $moduleResult->fetch_assoc()) {

        $functions = [];
        $functionResult = $userObj->getModuleFunctions($module_row["module_id"]);

        if ($functionResult) {
            while ($fun_row = $functionResult->fetch_assoc()) {
                $functions[] = [
                    "name" => $fun_row["function_name"],
                    "granted" => in_array($fun_row["function_id"], $functionArray)
                ];
            }
        }

        $permissionsData[] = [
            "module" => $module_row["module_name"],
            "functions" => $functions
        ];
    }
}

$date = date("Y-m-d");
$time = date("H:i:s");
$dateTime = date("Y-m-d H:i:s");

// ---------------- PDF CLASS ----------------
class SingleUserReport extends FPDF
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
        $this->Cell(0, 6, "User Details", 0, 1, "C");

        // Space after header
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
        $this->SetFont("Arial", "B", 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 8, "  " . $title, 0, 1, "L", true);
        $this->Ln(2);
    }

    function InfoRow($label, $value)
    {
        $this->SetFont("Arial", "B", 10);
        $this->Cell(55, 8, $label, 1);

        $this->SetFont("Arial", "", 10);
        $this->Cell(125, 8, $value, 1, 1);
    }

    function SafeText($text)
    {
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $text ?? 'N/A');
    }
}

// ---------------- PDF GENERATION ----------------
$pdf = new SingleUserReport("P", "mm", "A4");
$pdf->AliasNbPages();
$pdf->SetTitle("User Report - " . $userdetailrow["user_fname"] . " " . $userdetailrow["user_lname"]);
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

// ---------------- PERSONAL INFO ----------------
$pdf->SectionTitle("Personal Information");

$fullName = $pdf->SafeText($userdetailrow["user_fname"] . " " . $userdetailrow["user_lname"]);
$pdf->InfoRow("Full Name", $fullName);
$pdf->InfoRow("NIC", $pdf->SafeText($userdetailrow["user_nic"]));
$pdf->InfoRow("DOB", $pdf->SafeText($userdetailrow["user_dob"]));
$pdf->InfoRow("Email", $pdf->SafeText($userdetailrow["user_email"]));
$pdf->InfoRow("Mobile", $pdf->SafeText($contactrow1["contact_number"] ?? "N/A"));
$pdf->InfoRow("Fixed", $pdf->SafeText($contactrow2["contact_number"] ?? "N/A"));

$pdf->Ln(5);

// ---------------- ROLE ----------------
$pdf->SectionTitle("Role & Status");

$status = ($userdetailrow["user_status"] == 1) ? "Active" : "Inactive";

$pdf->InfoRow("Role", $pdf->SafeText($userdetailrow["role_name"]));

// Colored status
$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(55, 8, "Status", 1);

if ($status == "Active") $pdf->SetTextColor(0, 150, 0);
else $pdf->SetTextColor(200, 0, 0);

$pdf->Cell(125, 8, $status, 1, 1);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(5);

// ---------------- PERMISSIONS ----------------
// $pdf->SectionTitle("Permissions");

// if (empty($permissionsData)) {
//     $pdf->Cell(0, 8, "No permissions assigned.", 1, 1, "C");
// } else {

//     foreach ($permissionsData as $moduleData) {

//         if ($pdf->GetY() > 260) $pdf->AddPage();

//         $pdf->SetFont("Arial", "B", 10);
//         $pdf->SetFillColor(230, 240, 255);
//         $pdf->Cell(180, 8, $pdf->SafeText($moduleData["module"]), 1, 1, "L", true);

//         foreach ($moduleData["functions"] as $fun) {

//             if ($pdf->GetY() > 270) $pdf->AddPage();

//             $mark = $fun["granted"] ? "YES" : "NO";

//             if ($fun["granted"]) $pdf->SetTextColor(0, 130, 0);
//             else $pdf->SetTextColor(180, 0, 0);

//             $pdf->SetFont("Arial", "B", 9);
//             $pdf->Cell(20, 7, $mark, 1, 0, "C");

//             $pdf->SetTextColor(0, 0, 0);
//             $pdf->SetFont("Arial", "", 9);
//             $pdf->Cell(160, 7, $pdf->SafeText($fun["name"]), 1, 1);
//         }

//         $pdf->Ln(2);
//     }
// }

// ---------------- FOOTER NOTES ----------------

$pdf->Ln(5);
$pdf->SetFont("Arial", "I", 9);
$pdf->MultiCell(0, 5, "This is a computer-generated report and does not require a physical signature.", 0, "C");
$pdf->MultiCell(0, 5, "Confidentiality Notice: This document contains internal system data.", 0, "C");

// ---------------- OUTPUT ----------------
while (ob_get_level()) ob_end_clean();

$fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $fullName);
$pdf->Output("I", "User_Report_{$fileName}_$dateTime.pdf");