<?php
header('Content-Type: application/json');
require_once "../classes/Pdo_methods.php";

try {
    $pdo = new PdoMethods();
    $sql = "DELETE FROM names";
    $result = $pdo->otherNotBinded($sql);

    if ($result === "noerror") {
        echo json_encode(['masterstatus' => 'success', 'msg' => 'All names cleared']);
    } else {
        echo json_encode(['masterstatus' => 'error', 'msg' => 'Failed to clear names']);
    }

} catch (Exception $e) {
    echo json_encode(['masterstatus' => 'error', 'msg' => $e->getMessage()]);
}
