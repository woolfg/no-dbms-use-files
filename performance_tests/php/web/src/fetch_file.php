<?php

require_once('config.php');
require_once('functions.php');

/**
 * Simulate to request ten randon entries and show them
 */

$ids = generateRandomIDList(10);

$data = array();
foreach($ids as $id) {
    $data[] = include(CONFIG_DATA_PATH."/".$id);
}

echo var_dump($data);

echo "fetched";