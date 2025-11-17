<?php
function getWeather() {
    $acknowledgement = "";
    $output = "";

    if (!isset($_POST['zip_code']) || trim($_POST['zip_code']) === '') {
        $acknowledgement = '<div class="error-message">No zip code provided. Please enter a zip code.</div>';
        return [$acknowledgement, $output];
    }

    $zipCode = trim($_POST['zip_code']);
    $url = "https://russet-v8.wccnet.edu/~sshaper/assignments/assignment10_rest/get_weather_json.php?zip_code=" . urlencode($zipCode);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if ($response === false) {
        $acknowledgement = '<div class="error-message">There was an error retrieving the records.</div>';
        curl_close($ch);
        return [$acknowledgement, $output];
    }
    curl_close($ch);

    $data = json_decode($response, true);

    //errors or empty response
    if ($data === null) {
        $acknowledgement = '<div class="error-message">There was an error retrieving the records.</div>';
        return [$acknowledgement, $output];
    }

    // error responses
    if (isset($data['error'])) {
        $acknowledgement = '<div class="error-message">' . htmlspecialchars($data['error']) . '</div>';
        return [$acknowledgement, $output];
    }

    // output for searched city
    $searched = $data['searched_city'];
    $cityName = htmlspecialchars($searched['name']);
    $cityTemp = $searched['temperature'];
    $cityHumidity = htmlspecialchars($searched['humidity']);
    $forecast = $searched['forecast'];

    $output .= "<h2>$cityName</h2>";
    $output .= "<p>Temperature: $cityTemp</p>";
    $output .= "<p>Humidity: $cityHumidity</p>";

    // 3-day forecast
    $output .= "<h3>3-Day Forecast</h3>";
    $output .= "<ul>";
    foreach ($forecast as $dayForecast) {
        $day = htmlspecialchars($dayForecast['day']);
        $condition = htmlspecialchars($dayForecast['condition']);
        $output .= "<li>$day: $condition</li>";
    }
    $output .= "</ul>";

    // Higher temperatures
    $higher = $data['higher_temperatures'] ?? [];
    if (count($higher) > 0) {
        $output .= "<h3>Up to three cities where temperatures are higher than $cityName</h3>";
        $output .= "<table style='width:100%; border-collapse: collapse;' border='1'>";
        $output .= "<thead><tr><th>City</th><th>Temperature</th></tr></thead><tbody>";
        $count = 0;
        foreach ($higher as $city) {
            if ($count >= 3) break;
            $name = htmlspecialchars($city['name']);
            $temp = $city['temperature'];
            $output .= "<tr><td>$name</td><td>$temp</td></tr>";
            $count++;
        }
        $output .= "</tbody></table>";
     } else {
        $output .= "<p>No cities with temperatures higher than $cityName.</p>";
    }
       // Lower temperatures
    $lower = $data['lower_temperatures'] ?? [];
    if (count($lower) > 0) {
        $output .= "<h3>Up to three cities where themperatures are lower than $cityName</h3>";
        $output .= "<table style='width:100%; border-collapse: collapse;' border='1'>";
        $output .= "<thead><tr><th>City</th><th>Temperature</th></tr></thead><tbody>";
        $count = 0;
        foreach ($lower as $city) {
            if ($count >= 3) break;
            $name = htmlspecialchars($city['name']);
            $temp =($city['temperature']);
            $output .= "<tr><td>$name</td><td>$temp</td></tr>";
            $count++;
        }
        $output .= "</tbody></table>";
    } else {
        $output .= "<p>No cities with temperatures lower than $cityName.</p>";
    }

    return [$acknowledgement, $output];
}
