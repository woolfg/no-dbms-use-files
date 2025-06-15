<?php

require_once('config.php');

function generateHotelPHPCode($id) {
    return "<?php return array(
        'id'=>$id,
        'name'=>'hotel $id',
        'features'=>array('pool','wifi','24hreception'));";
}

function generateHotelSQL($id) {
    return sprintf(
        'INSERT INTO data.data (id,name,pool,wifi,24reception) VALUES (%d,"%s",1,1,1);',
        $id,
        "hotel $id"
    );
}

function generateHotelSQLite($id) {
    return sprintf(
        'INSERT INTO data (id,name,pool,wifi,24reception) VALUES (%d,"%s",1,1,1);',
        $id,
        "hotel $id"
    );
}

if (!is_dir(CONFIG_DATA_PATH)) {
    mkdir(CONFIG_DATA_PATH);
}

// MySQL setup
$mysqli = new mysqli(
    $GLOBALS['CONFIG_MYSQL_HOST'],
    $GLOBALS['CONFIG_MYSQL_USER'],
    $GLOBALS['CONFIG_MYSQL_PASSWORD'],
    '',
    $GLOBALS['CONFIG_MYSQL_PORT']
);

$mysqli->query("CREATE DATABASE IF NOT EXISTS data");
$mysqli->query("CREATE TABLE IF NOT EXISTS data.data (
    id INT UNSIGNED NOT NULL PRIMARY KEY,
    name VARCHAR(100),
    pool TINYINT,
    wifi TINYINT,
    24reception TINYINT
);");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// SQLite setup
$sqlite_db_path = CONFIG_DATA_PATH . '/data.sqlite';
try {
    $pdo = new PDO('sqlite:' . $sqlite_db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create SQLite table
    $pdo->exec("CREATE TABLE IF NOT EXISTS data (
        id INTEGER PRIMARY KEY,
        name TEXT,
        pool INTEGER,
        wifi INTEGER,
        24reception INTEGER
    )");
    
    // Clear existing data
    $pdo->exec("DELETE FROM data");
} catch (PDOException $e) {
    printf("SQLite setup failed: %s\n", $e->getMessage());
    exit();
}

// Generate data for all three storage methods
for ($i=1;$i<=CONFIG_NUMBER_ENTRIES;$i++) {
    // Generate file
    file_put_contents(CONFIG_DATA_PATH."/".$i,generateHotelPHPCode($i));
    
    // Insert into MySQL
    $mysqli->query(generateHotelSQL($i));
    
    // Insert into SQLite
    $pdo->exec(generateHotelSQLite($i));
}

$mysqli->close();
$pdo = null;

echo ($i-1)." hotels generated for files, MySQL, and SQLite";