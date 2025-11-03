<?php
require_once __DIR__ . "/Pdo_Methods.php";

class Date_time {

    public function checkSubmit() {
        $output = "";

        // Determine which form was submitted
        if (isset($_POST['addNote'])) {
            $output = $this->addNote();
        }
        else if (isset($_POST['getNotes'])) {
            $output = $this->getNotes();
        }

        return $output;
    }

    private function addNote() {
        if (empty($_POST['dateTime']) || empty($_POST['note'])) {
            return "<p class='text-danger'>You must enter a date, time, and note.</p>";
        }

        $timestamp = strtotime($_POST['dateTime']);
        $note = trim($_POST['note']);

        $pdo = new PdoMethods();
        $sql = "INSERT INTO notes (note_time, note_text) VALUES (:note_time, :note_text)";
        $bindings = [
            [':note_time', $timestamp, 'int'],
            [':note_text', $note, 'str']
        ];

        $result = $pdo->otherBinded($sql, $bindings);

        if ($result === 'error') {
            return "<p class='text-danger'>There was an error adding the note.</p>";
        }
        else {
            return "<p class='text-success'>Note successfully added.</p>";
        }
    }

    private function getNotes() {
        if (empty($_POST['begDate']) || empty($_POST['endDate'])) {
            return "<p class='text-danger'>No notes found for the date range selected.</p>";
        }

        $begTimestamp = strtotime($_POST['begDate'] . " 00:00:00");
        $endTimestamp = strtotime($_POST['endDate'] . " 23:59:59");

        $pdo = new PdoMethods();
        $sql = "SELECT note_time, note_text FROM notes WHERE note_time BETWEEN :begDate AND :endDate ORDER BY note_time DESC";
        $bindings = [
            [':begDate', $begTimestamp, 'int'],
            [':endDate', $endTimestamp, 'int']
        ];

        $records = $pdo->selectBinded($sql, $bindings);

        if ($records === 'error' || count($records) === 0) {
            return "<p class='text-danger'>No notes found for the date range selected.</p>";
        }

        // Build HTML table
        $output = "<table class='table table-striped table-bordered'>";
        $output .= "<thead><tr><th>Date and Time</th><th>Note</th></tr></thead><tbody>";

        foreach ($records as $row) {
            $formattedDate = date("m/d/Y h:i A", $row['note_time']);
            $output .= "<tr><td>{$formattedDate}</td><td>{$row['note_text']}</td></tr>";
        }

        $output .= "</tbody></table>";
        return $output;
    }
}
?>
