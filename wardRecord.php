<?php
// Included DB connection here so we don't have to copy-paste it on every page
require_once 'includes/db.php';
$pageTitle = 'Ward Records';

$pdo = getDbConnection();

$stmt = $pdo->query('SELECT WardName FROM WARD ORDER BY WardName');
$allWards = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<h2>Administrative Ward Records</h2>
<p class="para">Select a ward from the list below to view current patient admissions.</p>

<form method="GET">
    <select name="ward_name">
        <option value="">All Wards</option>
        <?php foreach ($allWards as $w): ?>
            <option value="<?php echo e($w['WardName']); ?>" 
                <?php echo (isset($_GET['ward_name']) && $_GET['ward_name'] == $w['WardName']) ? 'selected' : ''; ?>>
                <?php echo e($w['WardName']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filter</button>
    <a href="ward_record.php" style="margin-left: 10px;">Clear</a>
</form>

<?php
$wardName = $_GET['ward_name'] ?? '';

$sql = "SELECT PatientNo, PatientName, UnitNumber, BedNo, PrimaryDoctorID, DateAdmitted 
        FROM PATIENT WHERE 1=1";
$params = [];

if (!empty($wardName)) {
    $sql .= " AND WardName = :ward_name";
    $params[':ward_name'] = $wardName;
}

$sql .= " ORDER BY BedNo";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$patients = $stmt->fetchAll();
?>

<?php if (empty($patients)): ?>
    <p class="para">No patients found in the selected ward.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Patient No</th>
                <th>Patient Name</th>
                <th>Care Unit</th>
                <th>Bed No</th>
                <th>Date Admitted</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patients as $p): ?>
                <tr>
                    <td><?php echo e($p['PatientNo']); ?></td>
                    <td><?php echo e($p['PatientName']); ?></td>
                    <td><?php echo e($p['UnitNumber']); ?></td>
                    <td><?php echo e($p['BedNo']); ?></td>
                    <td><?php echo e($p['DateAdmitted']); ?></td>
                    <td><a href="patient_record.php?patient_id=<?php echo e($p['PatientNo']); ?>">View Details</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>