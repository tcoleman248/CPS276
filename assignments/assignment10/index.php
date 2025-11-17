<?php
$output = "";
$acknowledgement = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'php/rest_client.php';
    $result = getWeather();
    $acknowledgement = $result[0];
    $output = $result[1];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>City Weather</title>
    <style>
      body { font-family: Arial, sans-serif; }
      label { display: block; margin-top: 1em; }
      input[type="text"] { width: 300px; padding: 6px; font-size: 1em; }
      button { margin-top: 10px; background-color: #007bff; border: none; color: white; padding: 8px 12px; font-size: 1em; cursor: pointer; }
      button:hover { background-color: #0056b3; }
      table { border-collapse: collapse; margin-top: 1em; width: 400px; }
      th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
      th { background-color: #f2f2f2; }
      .error-message { margin-top: 0.5em; color: black; }
    </style>
</head>
<body>
    <h1>Enter Zip Code to Get City Weather</h1>
    <?php echo $acknowledgement; ?>
    <form method="post" action="">
        <label for="zip_code">Zip Code:</label>
        <input type="text" id="zip_code" name="zip_code" />
        <br />
        <button type="submit">Submit</button>
    </form>
    <?php echo $output; ?>
</body>
</html>
