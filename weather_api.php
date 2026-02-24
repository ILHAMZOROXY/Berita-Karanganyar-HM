<?php
/**
 * Weather API untuk Kecamatan Karanganyar
 * File ini bisa digunakan untuk fetch data cuaca real-time
 * Untuk menggunakan API cuaca real (OpenWeatherMap, WeatherAPI, dll), 
 * tambahkan API key di config/api_keys.php
 */

header('Content-Type: application/json');
header('Cache-Control: max-age=300'); // Cache 5 menit

// Koordinat kecamatan-kecamatan di Karanganyar
$kecamatanCoordinates = [
    'Karanganyar' => ['lat' => -7.6034, 'lon' => 110.9469],
    'Jaten' => ['lat' => -7.5908, 'lon' => 110.9044],
    'Colomadu' => ['lat' => -7.5633, 'lon' => 110.8978],
    'Gondangrejo' => ['lat' => -7.6444, 'lon' => 110.8900],
    'Kebakkramat' => ['lat' => -7.6542, 'lon' => 111.0031],
    'Mojogedang' => ['lat' => -7.6333, 'lon' => 111.0500],
    'Jatipuro' => ['lat' => -7.7167, 'lon' => 111.1167],
    'Jatiyoso' => ['lat' => -7.7500, 'lon' => 111.0667],
    'Jumapolo' => ['lat' => -7.7500, 'lon' => 111.0000],
    'Jumantono' => ['lat' => -7.7333, 'lon' => 110.9500],
    'Matesih' => ['lat' => -7.6833, 'lon' => 110.9167],
    'Tawangmangu' => ['lat' => -7.6667, 'lon' => 111.1333],
    'Ngargoyoso' => ['lat' => -7.6167, 'lon' => 111.1500],
    'Karangpandan' => ['lat' => -7.5500, 'lon' => 111.0500],
    'Kerjo' => ['lat' => -7.5667, 'lon' => 111.1000],
    'Jenawi' => ['lat' => -7.7833, 'lon' => 111.1167],
    'Tasikmadu' => ['lat' => -7.5500, 'lon' => 110.9500]
];

$kecamatan = isset($_GET['kecamatan']) ? trim($_GET['kecamatan']) : 'Karanganyar';

// Data cuaca simulasi (untuk demo tanpa API key)
// Ganti dengan fetch ke API cuaca real jika sudah punya API key
$weatherSimulation = [
    'Karanganyar' => [
        'temp' => rand(25, 29),
        'condition' => ['Cerah', 'Berawan'][rand(0, 1)],
        'humidity' => rand(65, 75),
        'windSpeed' => rand(5, 15),
        'icon' => '01d'
    ],
    'Jaten' => [
        'temp' => rand(26, 30),
        'condition' => 'Berawan',
        'humidity' => rand(60, 70),
        'windSpeed' => rand(5, 15),
        'icon' => '02d'
    ],
    'Colomadu' => [
        'temp' => rand(25, 29),
        'condition' => 'Cerah',
        'humidity' => rand(60, 70),
        'windSpeed' => rand(5, 15),
        'icon' => '01d'
    ],
    'Gondangrejo' => [
        'temp' => rand(24, 28),
        'condition' => 'Berawan',
        'humidity' => rand(68, 78),
        'windSpeed' => rand(5, 15),
        'icon' => '02d'
    ],
    'Kebakkramat' => [
        'temp' => rand(23, 27),
        'condition' => 'Hujan Rintik',
        'humidity' => rand(75, 85),
        'windSpeed' => rand(10, 20),
        'icon' => '10d'
    ],
    'Mojogedang' => [
        'temp' => rand(22, 26),
        'condition' => 'Mendung',
        'humidity' => rand(70, 80),
        'windSpeed' => rand(8, 18),
        'icon' => '03d'
    ],
    'Jatipuro' => [
        'temp' => rand(21, 25),
        'condition' => 'Hujan Rintik',
        'humidity' => rand(78, 88),
        'windSpeed' => rand(10, 20),
        'icon' => '10d'
    ],
    'Jatiyoso' => [
        'temp' => rand(20, 24),
        'condition' => 'Mendung',
        'humidity' => rand(75, 85),
        'windSpeed' => rand(8, 18),
        'icon' => '03d'
    ],
    'Jumapolo' => [
        'temp' => rand(24, 28),
        'condition' => 'Berawan',
        'humidity' => rand(65, 75),
        'windSpeed' => rand(5, 15),
        'icon' => '02d'
    ],
    'Jumantono' => [
        'temp' => rand(23, 27),
        'condition' => 'Cerah',
        'humidity' => rand(62, 72),
        'windSpeed' => rand(5, 15),
        'icon' => '01d'
    ],
    'Matesih' => [
        'temp' => rand(22, 26),
        'condition' => 'Berawan',
        'humidity' => rand(68, 78),
        'windSpeed' => rand(5, 15),
        'icon' => '02d'
    ],
    'Tawangmangu' => [
        'temp' => rand(18, 22),
        'condition' => 'Hujan Rintik',
        'humidity' => rand(80, 90),
        'windSpeed' => rand(12, 22),
        'icon' => '10d'
    ],
    'Ngargoyoso' => [
        'temp' => rand(17, 21),
        'condition' => 'Hujan',
        'humidity' => rand(82, 92),
        'windSpeed' => rand(15, 25),
        'icon' => '09d'
    ],
    'Karangpandan' => [
        'temp' => rand(21, 25),
        'condition' => 'Mendung',
        'humidity' => rand(72, 82),
        'windSpeed' => rand(8, 18),
        'icon' => '03d'
    ],
    'Kerjo' => [
        'temp' => rand(23, 27),
        'condition' => 'Berawan',
        'humidity' => rand(66, 76),
        'windSpeed' => rand(5, 15),
        'icon' => '02d'
    ],
    'Jenawi' => [
        'temp' => rand(19, 23),
        'condition' => 'Hujan Rintik',
        'humidity' => rand(78, 88),
        'windSpeed' => rand(10, 20),
        'icon' => '10d'
    ],
    'Tasikmadu' => [
        'temp' => rand(25, 29),
        'condition' => 'Cerah',
        'humidity' => rand(62, 72),
        'windSpeed' => rand(5, 15),
        'icon' => '01d'
    ]
];

// Check if API key exists for real weather data
$apiKeyFile = __DIR__ . '/config/api_keys.php';
$useRealAPI = false;

if (file_exists($apiKeyFile)) {
    include $apiKeyFile;
    if (defined('OPENWEATHER_API_KEY') && OPENWEATHER_API_KEY !== '') {
        $useRealAPI = true;
    }
}

$response = [
    'success' => false,
    'kecamatan' => $kecamatan,
    'data' => null
];

// Fetch real weather data if API key available
if ($useRealAPI && isset($kecamatanCoordinates[$kecamatan])) {
    $coords = $kecamatanCoordinates[$kecamatan];
    $apiKey = OPENWEATHER_API_KEY;
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$coords['lat']}&lon={$coords['lon']}&units=metric&lang=id&appid={$apiKey}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $result = curl_exec($ch);
    curl_close($ch);
    
    if ($result) {
        $data = json_decode($result, true);
        if (isset($data['main'])) {
            $response['success'] = true;
            $response['data'] = [
                'temp' => round($data['main']['temp']),
                'condition' => ucfirst($data['weather'][0]['description']),
                'humidity' => $data['main']['humidity'],
                'windSpeed' => round($data['wind']['speed'] * 3.6), // m/s to km/h
                'icon' => $data['weather'][0]['icon'],
                'source' => 'api'
            ];
        }
    }
} 

// Fallback to simulation if API not available or failed
if (!$response['success']) {
    if (isset($weatherSimulation[$kecamatan])) {
        $response['success'] = true;
        $response['data'] = $weatherSimulation[$kecamatan];
        $response['data']['source'] = 'simulation';
    } else {
        $response['message'] = 'Kecamatan tidak ditemukan';
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);
