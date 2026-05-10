<?php
require_once 'includes/db.php';
$pageTitle = 'Add New Doctor';
$pdo = getDbConnection();

$message = '';
$messageColor = 'green';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['doctor_name']);
    $position = $_POST['position'];
    $specialty = trim($_POST['specialty']);
    $consultantId = !empty($_POST['consultant_id']) ? $_POST['consultant_id'] : NULL;

    if (empty($name) || empty($position) || empty($specialty)) {
        $message = "Please fill in Name, Position, and Specialty.";
        $messageColor = "red";
    } else {
        try {
            $sql = "INSERT INTO DOCTOR (ConsultantID, Name, Position, Specialty) 
                    VALUES (:consultantId, :name, :position, :specialty)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':consultantId' => $consultantId,
                ':name' => $name,
                ':position' => $position,
                ':specialty' => $specialty
            ]);
            
            $message = "Success! Dr. $name has been added to the staff directory.";
            $messageColor = "green";
        } catch(PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageColor = "red";
        }
    }
}

$consultants = $pdo->query("SELECT StaffNo, Name, Specialty FROM DOCTOR WHERE Position = 'Consultant' ORDER BY Name")->fetchAll();

require_once 'includes/header.php';
?>

<h2>Add New Doctor</h2>
<p class="para">Enter a new doctor into the hospital system. If they are not a Consultant, please assign them to a Consultant's team.</p>

<?php if ($message): ?>
    <p class="para" style="color: <?php echo $messageColor; ?>; font-weight: bold;"><?php echo e($message); ?></p>
<?php endif; ?>

<form method="POST" style="max-width: 600px; display: flex; flex-direction: column; gap: 15px;">
    <div>
        <label><strong>Doctor Name:</strong></label><br>
        <input type="text" name="doctor_name" placeholder="e.g. Dr. Sarah Jenkins" required style="width: 100%;">
    </div>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 1;">
            <label><strong>Position:</strong></label><br>
            <select name="position" required style="width: 100%;">
                <option value="">-- Select Position --</option>
                <option value="Consultant">Consultant</option>
                <option value="Senior Registrar">Senior Registrar</option>
                <option value="Registrar">Registrar</option>
                <option value="Senior Houseman">Senior Houseman</option>
                <option value="Junior Houseman">Junior Houseman</option>
            </select>
        </div>
        <div style="flex: 1;">
            <label><strong>Specialty:</strong></label><br>
            <input type="text" name="specialty" placeholder="e.g. Cardiology" required style="width: 100%;">
        </div>
    </div>

    <div>
        <label><strong>Supervising Consultant (Leave blank if hiring a Consultant):</strong></label><br>
        <select name="consultant_id" style="width: 100%;">
            <option value="">-- None (Is a Consultant) --</option>
            <?php foreach ($consultants as $c): ?>
                <option value="<?php echo e($c['StaffNo']); ?>"><?php echo e($c['Name']); ?> (<?php echo e($c['Specialty']); ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" style="margin-top: 10px;">Add Doctor</button>
</form>

<?php require_once 'includes/footer.php'; ?>