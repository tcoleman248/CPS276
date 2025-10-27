<?php
require_once __DIR__ . '/../classes/Db_conn.php';
require_once __DIR__ . '/../classes/Pdo_methods.php';

$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['filename']) && isset($_FILES['pdfFile'])) {
        $filename = trim($_POST['filename']);
        $file = $_FILES['pdfFile'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $output = "<p style='color:red;'>Error uploading file.</p>";
        } elseif ($file['size'] > 100000) {
            $output = "<p style='color:red;'>File is too large. Must be under 100000 bytes.</p>";
        } elseif (mime_content_type($file['tmp_name']) !== 'application/pdf') {
            $output = "<p style='color:red;'>File must be a PDF.</p>";
        } else {
            // Move uploaded file
            $uploadDir = __DIR__ . '/../files/';
            $newFilePath = $uploadDir . basename($file['name']);

            if (move_uploaded_file($file['tmp_name'], $newFilePath)) {
                // File path for database (relative to project root)
                $dbFilePath = 'files/' . basename($file['name']);

                // inserting to database
                $pdo = new Pdo_methods();
                $sql = "INSERT INTO pdf_files (file_name, file_path) VALUES (:file_name, :file_path)";
                $bindings = [
                    [':file_name', $filename, 'str'],
                    [':file_path', $dbFilePath, 'str']
                ];
                $result = $pdo->otherBinded($sql, $bindings);

                if ($result === 'error') {
                    $output = "<p style='color:red;'>Database error occurred.</p>";
                } else {
                    $output = "<p style='color:green;'>File uploaded and record saved successfully!</p>";
                }
            } else {
                $output = "<p style='color:red;'>Error moving uploaded file.</p>";
            }
        }
    } else {
        $output = "<p style='color:red;'>Please provide all required fields.</p>";
    }
}
?>
