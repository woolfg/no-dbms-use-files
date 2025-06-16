<?php

require_once('config.php');
require_once('functions.php');

/**
 * Simulate to request ten random entries and show them using SQLite
 */

$ids = generateRandomIDList(10);

// SQLite database path - assuming it's in the data directory
$sqlite_db_path = CONFIG_DATA_PATH . '/data.sqlite';

try {
    $pdo = new PDO('sqlite:' . $sqlite_db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    printf("Connect failed: %s\n", $e->getMessage());
    exit();
}

$placeholders = str_repeat('?,', count($ids) - 1) . '?';
$stmt = $pdo->prepare("SELECT * FROM data WHERE id IN ($placeholders)");
$stmt->execute($ids);

$data = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $features = array();

    if ($row['wifi']) { $features[] = "wifi"; }
    if ($row['h24reception']) { $features[] = "24reception"; }
    if ($row['pool']) { $features[] = "pool"; }

    $data[] = array(
        'id'=>$row['id'],
        'name'=>$row['name'],
        'features' => $features,
        'field1'=>$row['field1']
    );
}

echo var_dump($data);

echo "fetched";