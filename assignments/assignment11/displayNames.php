<?php
header('Content-Type: application/json');
require_once "../classes/Pdo_methods.php";

try {
    $pdo = new PdoMethods();
    $sql = "SELECT name FROM names ORDER BY name ASC";
    $result = $pdo->selectNotBinded($sql);

    if ($result === 'error') {
        echo json_encode(['masterstatus' => 'error', 'msg' => 'Failed to retrieve names']);
        exit;
    }

    if (empty($result)) {
        echo json_encode(['masterstatus' => 'success', 'names' => '<p>No names in the database.</p>']);
        exit;
    }

    $output = "<ul>";
    foreach ($result as $row) {
        $output .= "<li>" . htmlspecialchars($row['name']) . "</li>";
    }
    $output .= "</ul>";

    echo json_encode(['masterstatus' => 'success', 'names' => $output]);

} catch (Exception $e) {
    echo json_encode(['masterstatus' => 'error', 'msg' => $e->getMessage()]);
}
