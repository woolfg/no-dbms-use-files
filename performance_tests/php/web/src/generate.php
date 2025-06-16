<?php

// Increase execution time limit for data generation
set_time_limit(300); // 5 minutes

require_once('config.php');

function generateRandomString($length) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 .,!?;:-()[]{}';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function generateEntryData($id) {
    return array(
        'id' => $id,
        'name' => "hotel $id",
        'field1' => generateRandomString(rand(200, 800))
    );
}

function saveToFile($data, $id) {
    $phpCode = "<?php return array(
        'id'=>{$data['id']},
        'name'=>'{$data['name']}',
        'features'=>array('pool','wifi','24hreception'),
        'field1'=>'" . addslashes($data['field1']) . "');";
    file_put_contents(CONFIG_DATA_PATH."/".$id, $phpCode);
}

function saveToMySQL($data, $mysqli) {
    $sqlQuery = sprintf(
        'INSERT INTO data.data (id,name,pool,wifi,h24reception,field1) VALUES (%d,"%s",1,1,1,"%s");',
        $data['id'],
        $data['name'],
        addslashes($data['field1'])
    );
    $mysqli->query($sqlQuery);
}

function saveToSQLite($data, $pdo) {
    $sqliteQuery = sprintf(
        'INSERT INTO data (id,name,pool,wifi,h24reception,field1) VALUES (%d,"%s",1,1,1,"%s");',
        $data['id'],
        $data['name'],
        addslashes($data['field1'])
    );
    $pdo->exec($sqliteQuery);
}

// Function to count entries in files
function files_count() {
    $files = glob(CONFIG_DATA_PATH . '/*');
    return count($files);
}

// Function to count entries in MySQL
function mysql_count($mysqli) {
    $result = $mysqli->query("SELECT COUNT(*) as count FROM data.data");
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to count entries in SQLite
function sqlite_count($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM data");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['count'];
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
    h24reception TINYINT,
    field1 TEXT
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
        h24reception INTEGER,
        field1 TEXT
    )");
    
    // Clear existing data
    $pdo->exec("DELETE FROM data");
} catch (PDOException $e) {
    printf("SQLite setup failed: %s\n", $e->getMessage());
    exit();
}

// Generate data for all three storage methods
for ($i=1;$i<=CONFIG_NUMBER_ENTRIES;$i++) {
    // Generate the data once per entry to ensure consistency across all storage methods
    $entryData = generateEntryData($i);
    
    // Save to all three storage methods with the same data
    saveToFile($entryData, $i);
    saveToMySQL($entryData, $mysqli);
    saveToSQLite($entryData, $pdo);
}

// Get counts before closing connections
$files_count = files_count();
$mysql_count = mysql_count($mysqli);
$sqlite_count = sqlite_count($pdo);

// Close connections
$mysqli->close();
$pdo = null;

echo ($i-1)." hotels generated for files, MySQL, and SQLite\n";
echo "Files: {$files_count} entries\n";
echo "MySQL: {$mysql_count} entries\n";
echo "SQLite: {$sqlite_count} entries\n";
