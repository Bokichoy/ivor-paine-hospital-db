<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?>Ivor Paine Hospital</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/table.css">
</head>
<body>
    <header>
        <div class="top">
            <img src="images/logo.png" alt="Hospital Logo" onerror="this.style.display='none'">
            <h1>Ivor Paine Memorial Hospital</h1>
        </div>

        <nav>
            <a href="index.php">Home</a>
            <a href="wardRecord.php">Ward Records</a>
            <a href="patientRecord.php">Patient Records</a>
            <a href="consultteams.php">Teams</a>
            <a href="reports.php">Reports</a>
            <a href="treatmentSearch.php">Treatment Search</a>
        </nav>
    </header>
    
    <main>