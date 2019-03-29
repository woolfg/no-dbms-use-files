<?php

$time_start = microtime(true);

require_once('config.php');
require_once('functions.php');

/**
 * Simulate to request ten randon entries and show them
 */

$ids = generateRandomIDList(10);

$time_config = microtime(true);

$data = array();
foreach($ids as $id) {
    $data[] = include(CONFIG_DATA_PATH."/".$id);
}

$time_fetching = microtime(true);

echo var_dump($data);

echo "fetched";

$time_output = microtime(true);

echo "<br><br>config: ".(($time_config-$time_start)*1000)."ms<br>";
echo "fetching: ".(($time_fetching-$time_config)*1000)."ms<br>";
echo "output: ".(($time_output-$time_fetching)*1000)."ms<br>";