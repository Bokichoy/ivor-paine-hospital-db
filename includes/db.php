<?php
function getDbConnection() {
    $serverName = "DESKTOP-5I7HVOH\\SQLEXPRESS"; 
    $database = "HospDB";
    
    try {
        $pdo = new PDO("sqlsrv:server=$serverName;Database=$database");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>