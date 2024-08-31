<?php
require_once 'inc/config.php';
require_once 'inc/api.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$city = 'Sao_Paulo';
if (isset($_GET['city'])) {
    $city = $_GET['city'];
}
$days = 3;

$results = API::get($city, $days);

if ($results['status'] == 'error') {
    echo $results['message'];
    exit;
}

$data = json_decode($results['data'], true);

// location data
$location = [];
$location['name'] = $data['location']['name'];
$location['region'] = $data['location']['region'];
$location['country'] = $data['location']['country'];
$location['current_time'] = $data['location']['localtime'];

// current weather data
$current = [];
$current['info'] = 'Neste momento:';
$current['temperature'] = $data['current']['temp_c'];
$current['condition'] = $data['current']['condition']['text'];
$current['condition_icon'] = $data['current']['condition']['icon'];
$current['wind_speed'] = $data['current']['wind_kph'];

//forecast weather data
$forecast = [];
foreach ($data['forecast']['forecastday'] as $day) {
    $forecast_day = [];
    $forecast_day['info'] = null;
    $forecast_day['date'] = $day['date'];
    $forecast_day['condition'] = $day['day']['condition']['text'];
    $forecast_day['condition_icon'] = $day['day']['condition']['icon'];
    $forecast_day['max_temp'] = $day['day']['maxtemp_c'];
    $forecast_day['min_temp'] = $day['day']['mintemp_c'];
    $forecast[] = $forecast_day;
}

function city_selected($city, $selected_city)
{
    if ($city == $selected_city) {
        return 'selected';
    }
    return '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsão do Tempo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body class="bg-dark text-white">

    <div class="container-fluid mt-5">
        <div class="row justify-content-center mt-5">

            <div class="col-10 p-5 bg-light text-black">

                <div class="row">
                    <div class="col-9">
                        <h3>Previsão de tempo para a cidade <strong><?= $location['name'] ?></strong></h3>
                        <p class="my-2">Região: <?= $location['region'] ?> | <?= $location['country'] ?> | <?= $location['current_time'] ?> | Previsão para <strong><?= $days ?></strong> dias</p>
                    </div>
                    <div class="col-3 text-end">
                        <select class="form-select">
                            <option value="Lisbon" <?= city_selected('Lisbon', $city) ?>>Lisboa</option>
                            <option value="Madrid" <?= city_selected('Madrid', $city) ?>>Madrid</option>
                            <option value="Paris" <?= city_selected('Paris', $city) ?>>Paris</option>
                            <option value="London" <?= city_selected('London', $city) ?>>Londres</option>
                            <option value="Berlin" <?= city_selected('Berlin', $city) ?>>Berlin</option>
                            <option value="Brasilia" <?= city_selected('Brasilia', $city) ?>>Brasilia</option>
                            <option value="Maputo" <?= city_selected('Maputo', $city) ?>>Maputo</option>
                            <option value="Luanda" <?= city_selected('Luanda', $city) ?>>Luanda</option>
                        </select>
                    </div>
                </div>

                <!-- current -->
                <?php
                $weather_info = $current;
                include 'inc/weather_info.php';
                ?>
                <!-- forecast -->
                <?php foreach ($forecast as $day): ?>
                    <?php
                    $weather_info = $day;
                    include 'inc/weather_info.php';
                    ?>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
    <script>
        const select = document.querySelector('select');
        select.addEventListener('change', (e) => {
            const city = e.target.value;
            window.location.href = `index.php?city=${city}`;
        })
    </script>
</body>

</html>