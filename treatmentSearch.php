<?php
// Included DB connection here so we don't have to copy-paste it on every page
require_once 'includes/db.php';
$pageTitle = 'Treatment Date Search';
$pdo = getDbConnection();
require_once 'includes/header.php';
?>

<h2>Query 11: Treatment Search by Date</h2>
<p class="para">Search for treatments given for a particular complaint within a specific timeframe.</p>

<form method="GET" style="margin-left: 23px;">
    <input type="text" name="complaint" placeholder="Complaint Code (e.g. C001)" required value="<?php echo e($_GET['complaint'] ?? ''); ?>">
    <input type="date" name="start_date" required value="<?php echo e($_GET['start_date'] ?? ''); ?>">
    <input type="date" name="end_date" required value="<?php echo e($_GET['end_date'] ?? ''); ?>">
    <button type="submit">Search Dates</button>
    <a href="treatmentSearch.php" style="margin-left: 10px;">Clear</a>
</form>

<?php
if (isset($_GET['complaint']) && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    
    $complaint = $_GET['complaint'];
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];

    $sql = "SELECT TreatmentCode, DateStarted, DateEnded, ComplaintCode 
            FROM MEDICAL_HISTORY 
            WHERE ComplaintCode = :complaint 
              AND DateStarted >= :start_date 
              AND DateStarted <= :end_date 
            ORDER BY TreatmentCode";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':complaint' => $complaint,
        ':start_date' => $startDate,
        ':end_date' => $endDate
    ]);
    
    $results = $stmt->fetchAll();

    if (empty($results)) {
        echo "<p class='para'>No treatments found for complaint <strong>" . e($complaint) . "</strong> between these dates.</p>";
    } else {
        echo "<table><thead><tr><th>Treatment Code</th><th>Complaint Code</th><th>Date Started</th><th>Date Ended</th></tr></thead><tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . e($row['TreatmentCode']) . "</td>";
            echo "<td>" . e($row['ComplaintCode']) . "</td>";
            echo "<td>" . e($row['DateStarted']) . "</td>";
            echo "<td>" . e($row['DateEnded'] ?? 'Ongoing') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
}
?>

<?php require_once 'includes/footer.php'; ?>