<?php
require_once 'classes/Date_time.php';
$dt = new Date_time();
$message = $dt->checkSubmit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Note</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="mb-4">Add Note</h2>
  <a href="display_notes.php" class="d-block mb-3">Display Notes</a>
<!-- makes it more of a window in the form -->
  <form method="post" class="border rounded p-4 bg-white shadow-sm">
    <?php echo $message ?? ''; ?>

    <div class="mb-3">
      <label for="dateTime" class="form-label fw-bold">Date and Time</label>
      <input type="datetime-local" class="form-control" id="dateTime" name="dateTime" placeholder="mm/dd/yyyy --:-- --">
    </div>

    <div class="mb-3">
      <label for="note" class="form-label fw-bold">Note</label>
      <textarea class="form-control" id="note" name="note" rows="13"></textarea>
    </div>

    <button type="submit" name="addNote" class="btn btn-primary">Add Note</button>
  </form>
</div>

</body>
</html>
