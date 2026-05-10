<?php
require_once 'includes/db.php';
$pageTitle = 'Consultant Teams';
$pdo = getDbConnection();
require_once 'includes/header.php';
?>

<h2>Consultant Team Record</h2>
<p class="para">Enter a Consultant's Staff Number to view the doctors assigned to their team.</p>

<form method="GET">
    <input type="text" name="staff_no" placeholder="Input Staff No" value="<?php echo e($_GET['staff_no'] ?? ''); ?>">
    <button type="submit">Search</button>
    <a href="consultant_team.php" style="margin-left: 10px;">Clear</a>
</form>

<?php
$staffNo = $_GET['staff_no'] ?? '';

if (!empty($staffNo)): 
    $consultantSql = "SELECT Name, Specialty FROM DOCTOR WHERE StaffNo = :staff_no AND Position = 'Consultant'";
    $cStmt = $pdo->prepare($consultantSql);
    $cStmt->execute([':staff_no' => $staffNo]);
    $consultant = $cStmt->fetch();

    if (!$consultant): ?>
        <p class="para">No consultant found with Staff No: <?php echo e($staffNo); ?>. Please ensure the ID belongs to a Consultant.</p>
    <?php else: 
        $teamSql = "SELECT StaffNo, Name, Position, Specialty FROM DOCTOR WHERE ConsultantID = :staff_no";
        $tStmt = $pdo->prepare($teamSql);
        $tStmt->execute([':staff_no' => $staffNo]);
        $team = $tStmt->fetchAll();
    ?>
        <p class="para"><strong>Consultant Name:</strong> <?php echo e($consultant['Name']); ?></p>
        <p class="para"><strong>Specialty:</strong> <?php echo e($consultant['Specialty']); ?></p>
        
        <?php if (empty($team)): ?>
            <p class="para">No junior doctors currently assigned to this team.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Staff No</th>
                        <th>Doctor Name</th>
                        <th>Position</th>
                        <th>Specialty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($team as $member): ?>
                        <tr>
                            <td><?php echo e($member['StaffNo']); ?></td>
                            <td><?php echo e($member['Name']); ?></td>
                            <td><?php echo e($member['Position']); ?></td>
                            <td><?php echo e($member['Specialty']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>