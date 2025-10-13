<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/php-test', function () {
    echo "✅ PHP работает<br>";

    $phpVersion = phpversion();
    echo "Версия PHP: " . $phpVersion . "<br>";

    if (function_exists('opcache_get_status')) {
        echo "OPcache: включён<br>";
    } else {
        echo "OPcache: выключен или недоступен<br>";
    }

    if (function_exists('curl_init')) {
        echo "cURL доступен<br>";
    } else {
        echo "cURL не установлен<br>";
    }

    try {
        $pdo = new PDO('mysql:host=localhost', 'root', '');
        echo "MySQL доступен<br>";
    } catch (Exception $e) {
        echo "MySQL недоступен<br>";
    }

    echo "<br>Время сервера: " . date('Y-m-d H:i:s');
});
