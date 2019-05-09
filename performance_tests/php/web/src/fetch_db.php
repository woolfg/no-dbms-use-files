<?php

require_once('config.php');
require_once('functions.php');

/**
 * Simulate to request ten randon entries and show them
 */

$ids = generateRandomIDList(10);

$mysqli = new mysqli(
    $GLOBALS['CONFIG_MYSQL_HOST'],
    $GLOBALS['CONFIG_MYSQL_USER'],
    $GLOBALS['CONFIG_MYSQL_PASSWORD'],
    '',
    $GLOBALS['CONFIG_MYSQL_PORT']
);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$result = $mysqli->query(sprintf('SELECT * FROM data.data WHERE id IN (%s)', implode(",",$ids)));

$data = array();
while ($row = $result->fetch_assoc()) {
    $features = array();

    if ($row['wifi']) { $features[] = "wifi"; }
    if ($row['24reception']) { $features[] = "24reception"; }
    if ($row['pool']) { $features[] = "pool"; }

    $data[] = array(
        'id'=>$row['id'],
        'name'=>$row['name'],
        'features' => $features
    );
}

echo var_dump($data);

echo "fetched";