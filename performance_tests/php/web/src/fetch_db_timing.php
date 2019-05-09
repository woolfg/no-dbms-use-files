<?php

$time_start = microtime(true);

require_once('config.php');
require_once('functions.php');

/**
 * Simulate to request ten randon entries and show them
 */

$ids = generateRandomIDList(10);

$time_config = microtime(true);

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

$time_connection = microtime(true);

$result = $mysqli->query(sprintf('SELECT * FROM data.data WHERE id IN (%s)', implode(",",$ids)));

$time_query = microtime(true);

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

$time_fetching = microtime(true);

echo var_dump($data);

echo "fetched";

$time_output = microtime(true);

echo "<br><br>config: ".(($time_config-$time_start)*1000)."ms<br>";
echo "connection: ".(($time_connection-$time_config)*1000)."ms<br>";
echo "query: ".(($time_query-$time_connection)*1000)."ms<br>";
echo "fetching: ".(($time_fetching-$time_query)*1000)."ms<br>";
echo "output: ".(($time_output-$time_fetching)*1000)."ms<br>";