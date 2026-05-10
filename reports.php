
<?php
// Included DB connection here so we don't have to copy-paste it on every page
require_once 'includes/db.php';
$pageTitle = 'General Reports';
$pdo = getDbConnection();
require_once 'includes/header.php';
?>

<h2>Hospital General Reports</h2>
<p class="para">Select a report from the dropdown below to execute predefined database queries.</p>

<form method="GET">
    <select name="report_type">
        <option value="">-- Select a Report --</option>
        <option value="q1" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'q1') ? 'selected' : ''; ?>>1. Consultants and their Teams</option>
        <option value="q2" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'q2') ? 'selected' : ''; ?>>2. Wards, Care Units & Nurses</option>
        <option value="q3" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'q3') ? 'selected' : ''; ?>>3. Patients, Complaints & Treatments</option>
        <option value="q4" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'q4') ? 'selected' : ''; ?>>4. Junior Housemen & Patients</option>
        <option value="q5" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'q5') ? 'selected' : ''; ?>>5. Consultants with Unique Specialty</option>
        <option value="q6" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'q6') ? 'selected' : ''; ?>>6. Complaints, Treatments & Doctors</option>
        <option value="q7" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'q7') ? 'selected' : ''; ?>>7. Patients with Multiple Complaints</option>
        <option value="q8" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'q8') ? 'selected' : ''; ?>>8. Patients Grouped by Treatment</option>
        <option value="q12" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'q12') ? 'selected' : ''; ?>>12. Hospital Staff Counts</option>
    </select>
    <button type="submit">Generate Report</button>
</form>

<?php
$reportType = $_GET['report_type'] ?? '';
$reportTitle = "";
$sql = "";

// 1. Just define the Title and the SQL query inside the if/else block
if ($reportType == 'q1') {
    $reportTitle = "1. Consultants and Teams";
    $sql = "SELECT c.Name AS 'Consultant Name', c.Specialty, d.Name AS 'Team Doctor', d.Position AS 'Doctor Position' FROM DOCTOR c JOIN DOCTOR d ON c.StaffNo = d.ConsultantID WHERE c.Position = 'Consultant' ORDER BY c.Name, d.Name";

} elseif ($reportType == 'q2') {
    $reportTitle = "2. Wards, Care Units & Nurses";
    $sql = "SELECT w.WardName AS 'Ward Name', cu.UnitNumber AS 'Care Unit', n.NurseName AS 'In-Charge Nurse', n.Role FROM WARD w JOIN CARE_UNIT cu ON w.WardName = cu.WardName JOIN NURSE n ON cu.InchargeNurseID = n.NurseID ORDER BY w.WardName, cu.UnitNumber";

} elseif ($reportType == 'q3') {
    $reportTitle = "3. Patients, Complaints & Treatments";
    $sql = "SELECT p.PatientName AS 'Patient Name', mh.ComplaintCode AS 'Complaint', mh.TreatmentCode AS 'Treatment', mh.DateStarted AS 'Date Started', mh.DateEnded AS 'Date Ended' FROM PATIENT p JOIN MEDICAL_HISTORY mh ON p.PatientNo = mh.PatientNo ORDER BY p.PatientName, mh.DateStarted";

} elseif ($reportType == 'q4') {
    $reportTitle = "4. Junior Housemen & Patients";
    $sql = "SELECT d.Name AS 'Junior Houseman', p.PatientName AS 'Patient Name', n.NurseName AS 'Care Unit Staff Nurse' FROM DOCTOR d JOIN PATIENT p ON d.StaffNo = p.PrimaryDoctorID JOIN CARE_UNIT cu ON p.UnitNumber = cu.UnitNumber AND p.WardName = cu.WardName JOIN NURSE n ON cu.InchargeNurseID = n.NurseID WHERE d.Position = 'Junior Houseman' ORDER BY d.Name, p.PatientName";

} elseif ($reportType == 'q5') {
    $reportTitle = "5. Consultants with Unique Specialty";
    $sql = "SELECT Name AS 'Consultant Name', Specialty AS 'Unique Specialty' FROM DOCTOR WHERE Position = 'Consultant' AND Specialty IN (SELECT Specialty FROM DOCTOR WHERE Position = 'Consultant' GROUP BY Specialty HAVING COUNT(*) = 1)";

} elseif ($reportType == 'q6') {
    $reportTitle = "6. Complaints, Treatments & Doctors";
    $sql = "SELECT mh.ComplaintCode AS 'Complaint', mh.TreatmentCode AS 'Treatment', d.Name AS 'Treating Doctor', d.Position, d.Specialty FROM MEDICAL_HISTORY mh JOIN DOCTOR d ON mh.StaffNo = d.StaffNo ORDER BY mh.ComplaintCode, mh.TreatmentCode";

} elseif ($reportType == 'q7') {
    $reportTitle = "7. Patients with Multiple Complaints";
    $sql = "SELECT p.PatientName AS 'Patient Name', mh.ComplaintCode AS 'Complaint Code', mh.TreatmentCode AS 'Treatment Code' FROM PATIENT p JOIN MEDICAL_HISTORY mh ON p.PatientNo = mh.PatientNo WHERE p.PatientNo IN (SELECT PatientNo FROM MEDICAL_HISTORY GROUP BY PatientNo HAVING COUNT(DISTINCT ComplaintCode) > 1) ORDER BY p.PatientName, mh.ComplaintCode";

} elseif ($reportType == 'q8') {
    $reportTitle = "8. Patients Grouped by Treatment within Complaint";
    $sql = "SELECT mh.ComplaintCode AS 'Complaint Code', mh.TreatmentCode AS 'Treatment Code', p.PatientName AS 'Patient Name' FROM MEDICAL_HISTORY mh JOIN PATIENT p ON mh.PatientNo = p.PatientNo ORDER BY mh.ComplaintCode, mh.TreatmentCode, p.PatientName";

} elseif ($reportType == 'q12') {
    $reportTitle = "12. Hospital Staff Counts";
    $sql = "SELECT PositionOrRole AS 'Position / Role', COUNT(*) AS 'Total Staff' FROM (SELECT Position AS PositionOrRole FROM DOCTOR UNION ALL SELECT Role AS PositionOrRole FROM NURSE) AS AllStaff GROUP BY PositionOrRole ORDER BY 'Total Staff' DESC";
}

// 2. Only run the rendering logic once at the bottom
if ($sql !== "") {
    echo "<h3 class='para'>" . e($reportTitle) . "</h3>";
    $stmt = $pdo->query($sql);
    
    // Fetch as an associative array so we can grab the column names dynamically
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        echo "<p class='para'>No records found for this report.</p>";
    } else {
        echo "<table><thead><tr>";
        
        // Grab the keys (column names) from the very first row and use them as <th>
        $firstRow = $results[0];
        foreach (array_keys($firstRow) as $header) {
            echo "<th>" . e($header) . "</th>";
        }
        
        echo "</tr></thead><tbody>";

        // Loop through all the rows and their respective columns to generate the <td>
        foreach ($results as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . e($value ?? 'N/A') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</tbody></table>";
    }
}
?>

<?php require_once 'includes/footer.php'; ?>