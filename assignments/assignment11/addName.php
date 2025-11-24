<?php
header('Content-Type: application/json');
require_once "../classes/Pdo_methods.php";

try {
    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['name'])) {
        echo json_encode(['masterstatus' => 'error', 'msg' => 'Name cannot be empty']);
        exit;
    }

    $name = trim($data['name']);

    // Split first and last name
    $parts = explode(" ", $name);
    if (count($parts) < 2) {
        echo json_encode(['masterstatus' => 'error', 'msg' => 'Please enter both first and last name']);
        exit;
    }

    $firstName = $parts[0];
    $lastName = $parts[1];

    $formattedName = $lastName . ", " . $firstName;

    // Insert into database
    $pdo = new PdoMethods();
    $sql = "INSERT INTO names (name) VALUES (:name)";
    $bindings = [
        [":name", $formattedName, "str"]
    ];

    $result = $pdo->otherBinded($sql, $bindings);

    if ($result === "noerror") {
        echo json_encode(['masterstatus' => 'success', 'msg' => 'Name added successfully']);
    } else {
        echo json_encode(['masterstatus' => 'error', 'msg' => 'Failed to add name']);
    }

} catch (Exception $e) {
    echo json_encode(['masterstatus' => 'error', 'msg' => $e->getMessage()]);
}
