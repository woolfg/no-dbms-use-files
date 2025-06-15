<?php

$time_start = microtime(true);

require_once('config.php');
require_once('functions.php');

/**
 * Simulate to request ten random entries and show them using SQLite with timing
 */

$ids = generateRandomIDList(10);

$time_config = microtime(true);

// SQLite database path - assuming it's in the data directory
$sqlite_db_path = CONFIG_DATA_PATH . '/data.sqlite';

try {
    $pdo = new PDO('sqlite:' . $sqlite_db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    printf("Connect failed: %s\n", $e->getMessage());
    exit();
}

$time_connection = microtime(true);

$placeholders = str_repeat('?,', count($ids) - 1) . '?';
$stmt = $pdo->prepare("SELECT * FROM data WHERE id IN ($placeholders)");
$stmt->execute($ids);

$time_query = microtime(true);

$data = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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