<?php
require_once '../php/listFilesProc.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Files</title>
</head>
<body>
    <h1>Available PDF Files</h1>
    <a href="index.php">Upload Another File</a>
    <ul>
        <?php echo $output; ?>
    </ul>
</body>
</html>
