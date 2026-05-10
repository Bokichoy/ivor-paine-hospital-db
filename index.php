<?php
// Included DB connection here so we don't have to copy-paste it on every page
require_once 'includes/db.php';
$pageTitle = 'Home';
require_once 'includes/header.php';
?>

<h2>Hospital Database Dashboard</h2>
<p class="para">Welcome to the Ivor Paine Memorial Hospital Database.</p>
<br>
<p class="para">Use the tabs above to navigate through the wards, patients, and staff reports.</p>
<p class="para">This project is built for CS204 to digitize the hospital's manual records.</p>

<?php require_once 'includes/footer.php'; ?>