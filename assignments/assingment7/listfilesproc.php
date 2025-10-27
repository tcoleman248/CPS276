<?php
require_once __DIR__ . '/../classes/Db_conn.php';
require_once __DIR__ . '/../classes/Pdo_methods.php';

$output = '';

$pdo = new Pdo_methods();
$sql = "SELECT file_name, file_path FROM pdf_files";
$records = $pdo->selectNotBinded($sql);

if ($records === 'error') {
    $output = "<p style='color:red;'>Error retrieving file list.</p>";
} elseif (count($records) === 0) {
    $output = "<p>No files found.</p>";
} else {
    foreach ($records as $row) {
      $basePath = '/assignments/assignment7/'; 
$output .= "<li><a target='_blank' href='{$basePath}{$row['file_path']}'>{$row['file_name']}</a></li>";

    }
}
?>
