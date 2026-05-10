<?php
require_once 'includes/db.php';
$pageTitle = 'Add New Patient';
$pdo = getDbConnection();

$message = '';
$messageColor = 'green';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patientName = trim($_POST['patient_name']);
    $dob = $_POST['dob'];
    $dateAdmitted = $_POST['date_admitted'];
    $wardName = $_POST['ward_name'];
    $unitNumber = $_POST['unit_number'];
    $bedNo = $_POST['bed_no'];
    $doctorId = $_POST['doctor_id'];

    if (empty($patientName) || empty($wardName) || empty($doctorId) || empty($unitNumber)) {
        $message = "Please fill in all required fields.";
        $messageColor = "red";
    } else {
        try {
            $sql = "INSERT INTO PATIENT (WardName, UnitNumber, PrimaryDoctorID, PatientName, DOB, BedNo, DateAdmitted) 
                    VALUES (:wardName, :unitNumber, :doctorId, :patientName, :dob, :bedNo, :dateAdmitted)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':wardName' => $wardName,
                ':unitNumber' => $unitNumber,
                ':doctorId' => $doctorId,
                ':patientName' => $patientName,
                ':dob' => $dob,
                ':bedNo' => $bedNo,
                ':dateAdmitted' => $dateAdmitted
            ]);
            
            $message = "Success! Patient $patientName has been admitted to the database.";
            $messageColor = "green";
        } catch(PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageColor = "red";
        }
    }
}

// Fetch lists for the dropdown menus
$wards = $pdo->query("SELECT WardName FROM WARD ORDER BY WardName")->fetchAll();
$doctors = $pdo->query("SELECT StaffNo, Name, Specialty FROM DOCTOR ORDER BY Name")->fetchAll();

require_once 'includes/header.php';
?>

<h2>Admit a New Patient</h2>
<p class="para">Use this form to enter a new patient into the hospital database. Ensure you assign them to an active ward and a primary doctor.</p>

<?php if ($message): ?>
    <p class="para" style="color: <?php echo $messageColor; ?>; font-weight: bold;">
        <?php echo e($message); ?>
    </p>
<?php endif; ?>

<form method="POST" style="max-width: 600px; display: flex; flex-direction: column; gap: 15px;">
    
    <div>
        <label><strong>Patient Full Name:</strong></label><br>
        <input type="text" name="patient_name" placeholder="e.g. John Doe" required style="width: 100%;">
    </div>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 1;">
            <label><strong>Date of Birth:</strong></label><br>
            <input type="date" name="dob" required style="width: 100%;">
        </div>
        <div style="flex: 1;">
            <label><strong>Date Admitted:</strong></label><br>
            <input type="date" name="date_admitted" required value="<?php echo date('Y-m-d'); ?>" style="width: 100%;">
        </div>
    </div>

    <div>
        <label><strong>Assign to Ward:</strong></label><br>
        <select name="ward_name" required style="width: 100%;">
            <option value="">-- Select Ward --</option>
            <?php foreach ($wards as $w): ?>
                <option value="<?php echo e($w['WardName']); ?>"><?php echo e($w['WardName']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 1;">
            <label><strong>Care Unit Number:</strong></label><br>
            <input type="number" name="unit_number" placeholder="e.g. 101" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e0; border-radius: 4px;">
        </div>
        <div style="flex: 1;">
            <label><strong>Bed Number:</strong></label><br>
            <input type="number" name="bed_no" placeholder="e.g. 1" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e0; border-radius: 4px;">
        </div>
    </div>

    <div>
        <label><strong>Primary Doctor:</strong></label><br>
        <select name="doctor_id" required style="width: 100%;">
            <option value="">-- Select Doctor --</option>
            <?php foreach ($doctors as $d): ?>
                <option value="<?php echo e($d['StaffNo']); ?>"><?php echo e($d['Name']); ?> (<?php echo e($d['Specialty']); ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" style="margin-top: 10px;">Admit Patient</button>
</form>

<?php require_once 'includes/footer.php'; ?>