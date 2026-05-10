<?php
// Included DB connection here so we don't have to copy-paste it on every page
require_once 'includes/db.php';
$pageTitle = 'Patient Records';
$pdo = getDbConnection();
require_once 'includes/header.php';
?>

<h2>Patient Medical Record Search</h2>
<p class="para">Search for a patient by their unique Patient Number.</p>

<form method="GET">
    <input type="text" name="patient_id" placeholder="Input Patient No" value="<?php echo e($_GET['patient_id'] ?? ''); ?>">
    <button type="submit">Search</button>
    <a href="patient_record.php" style="margin-left: 10px;">Clear</a>
</form>

<?php
$patientId = $_GET['patient_id'] ?? '';

if (!empty($patientId)): 
    $sql = "SELECT p.PatientName, p.DOB, p.DateAdmitted, w.WardName, cu.UnitNumber, 
                   d.Name AS PrimaryDoctor, mh.ComplaintCode, mh.DateStarted, 
                   mh.TreatmentCode, mh.DateEnded
            FROM PATIENT p
            JOIN WARD w ON p.WardName = w.WardName
            JOIN CARE_UNIT cu ON p.UnitNumber = cu.UnitNumber AND p.WardName = cu.WardName
            JOIN DOCTOR d ON p.PrimaryDoctorID = d.StaffNo
            LEFT JOIN MEDICAL_HISTORY mh ON p.PatientNo = mh.PatientNo
            WHERE p.PatientNo = :patient_id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':patient_id' => $patientId]);
    $records = $stmt->fetchAll();
?>

    <?php if (empty($records)): ?>
        <p class="para">No records found for Patient No: <?php echo e($patientId); ?></p>
    <?php else: ?>
        <p class="para"><strong>Patient Name:</strong> <?php echo e($records[0]['PatientName']); ?> | <strong>DOB:</strong> <?php echo e($records[0]['DOB']); ?></p>
        <p class="para"><strong>Ward:</strong> <?php echo e($records[0]['WardName']); ?> | <strong>Primary Doctor:</strong> <?php echo e($records[0]['PrimaryDoctor']); ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>Complaint Code</th>
                    <th>Treatment Code</th>
                    <th>Date Started</th>
                    <th>Date Ended</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?php echo e($row['ComplaintCode'] ?? 'None'); ?></td>
                        <td><?php echo e($row['TreatmentCode'] ?? 'Pending'); ?></td>
                        <td><?php echo e($row['DateStarted'] ?? '-'); ?></td>
                        <td><?php echo e($row['DateEnded'] ?? 'Ongoing'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>