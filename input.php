<?php
// 1. Include the connection file (they are in the same folder, so just use the filename)
require 'db.php';

try {
    // 2. Paste the SQL query into a PHP string
    $sql = "SELECT 
                c.Name AS ConsultantName, 
                c.Specialty,
                d.Name AS TeamDoctorName, 
                d.Position AS TeamDoctorPosition
            FROM DOCTOR c
            JOIN DOCTOR d ON c.StaffNo = d.ConsultantID
            WHERE c.Position = 'Consultant'
            ORDER BY c.Name, d.Name";

    // 3. Execute the query using the $conn variable created inside db.php
    $stmt = $conn->query($sql);

    // 4. Loop through the results and display them in HTML
    echo "<table border='1'><tr><th>Consultant</th><th>Specialty</th><th>Team Doctor</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ConsultantName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Specialty']) . "</td>";
        echo "<td>" . htmlspecialchars($row['TeamDoctorName']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch(PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
?>