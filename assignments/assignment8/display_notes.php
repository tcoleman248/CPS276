<?php
require_once 'classes/Date_time.php';
$dt = new Date_time();
$output = $dt->checkSubmit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Display Notes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="mb-4">Display Notes</h2>
  <a href="index.php" class="d-block mb-3">Add Note</a>

  <form method="post" class="border rounded p-4 bg-white shadow-sm mb-4">
    <div class="mb-3">
      <label for="begDate" class="form-label fw-bold">Beginning Date</label>
      <input type="date" class="form-control" id="begDate" name="begDate">
    </div>

    <div class="mb-3">
      <label for="endDate" class="form-label fw-bold">Ending Date</label>
      <input type="date" class="form-control" id="endDate" name="endDate">
    </div>

    <button type="submit" name="getNotes" class="btn btn-primary">Get Notes</button>
  </form>

  <?php echo $output ?? ''; ?>
</div>

</body>
</html>
