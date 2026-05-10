<?php
require_once 'includes/db.php';
$pageTitle = 'Log Medical Complaint';
$pdo = getDbConnection();

$message = '';
$messageColor = 'green';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patientNo = $_POST['patient_no'];
    $staffNo = $_POST['staff_no'];
    $complaintCode = trim($_POST['complaint_code']);
    $dateStarted = $_POST['date_started'];
    
    $treatmentCode = !empty(trim($_POST['treatment_code'])) ? trim($_POST['treatment_code']) : NULL;
    $dateEnded = !empty($_POST['date_ended']) ? $_POST['date_ended'] : NULL;

    if (empty($patientNo) || empty($staffNo) || empty($complaintCode) || empty($dateStarted)) {
        $message = "Please fill in the Patient, Doctor, Complaint Code, and Start Date.";
        $messageColor = "red";
    } else {
        try {
            $sql = "INSERT INTO MEDICAL_HISTORY (PatientNo, StaffNo, ComplaintCode, DateStarted, TreatmentCode, DateEnded) 
                    VALUES (:patientNo, :staffNo, :complaintCode, :dateStarted, :treatmentCode, :dateEnded)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':patientNo' => $patientNo,
                ':staffNo' => $staffNo,
                ':complaintCode' => $complaintCode,
                ':dateStarted' => $dateStarted,
                ':treatmentCode' => $treatmentCode,
                ':dateEnded' => $dateEnded
            ]);
            
            $message = "Success! Medical history record has been logged.";
            $messageColor = "green";
        } catch(PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageColor = "red";
        }
    }
}

$patients = $pdo->query("SELECT PatientNo, PatientName FROM PATIENT ORDER BY PatientName")->fetchAll();
$doctors = $pdo->query("SELECT StaffNo, Name, Specialty FROM DOCTOR ORDER BY Name")->fetchAll();

require_once 'includes/header.php';
?>

<h2>Log a Patient Complaint</h2>
<p class="para">Record a new medical complaint for a patient and assign the treating doctor.</p>

<?php if ($message): ?>
    <p class="para" style="color: <?php echo $messageColor; ?>; font-weight: bold;"><?php echo e($message); ?></p>
<?php endif; ?>

<form method="POST" style="max-width: 600px; display: flex; flex-direction: column; gap: 15px;">
    <div>
        <label><strong>Select Patient:</strong></label><br>
        <select name="patient_no" required style="width: 100%;">
            <option value="">-- Select Patient --</option>
            <?php foreach ($patients as $p): ?>
                <option value="<?php echo e($p['PatientNo']); ?>"><?php echo e($p['PatientName']); ?> (ID: <?php echo e($p['PatientNo']); ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label><strong>Treating Doctor:</strong></label><br>
        <select name="staff_no" required style="width: 100%;">
            <option value="">-- Select Doctor --</option>
            <?php foreach ($doctors as $d): ?>
                <option value="<?php echo e($d['StaffNo']); ?>"><?php echo e($d['Name']); ?> (<?php echo e($d['Specialty']); ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 1;">
            <label><strong>Complaint Code:</strong></label><br>
            <input type="text" name="complaint_code" placeholder="e.g. C005" required style="width: 100%;">
        </div>
        <div style="flex: 1;">
            <label><strong>Treatment Code (Optional):</strong></label><br>
            <input type="text" name="treatment_code" placeholder="e.g. T105" style="width: 100%;">
        </div>
    </div>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 1;">
            <label><strong>Date Started:</strong></label><br>
            <input type="date" name="date_started" required value="<?php echo date('Y-m-d'); ?>" style="width: 100%;">
        </div>
        <div style="flex: 1;">
            <label><strong>Date Ended (Optional):</strong></label><br>
            <input type="date" name="date_ended" style="width: 100%;">
        </div>
    </div>

    <button type="submit" style="margin-top: 10px;">Log Record</button>
</form>

<?php require_once 'includes/footer.php'; ?>