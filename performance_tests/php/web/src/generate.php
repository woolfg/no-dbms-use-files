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

if (!is_dir(CONFIG_DATA_PATH)) {
    mkdir(CONFIG_DATA_PATH);
}

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

for ($i=1;$i<=CONFIG_NUMBER_ENTRIES;$i++) {
    file_put_contents(CONFIG_DATA_PATH."/".$i,generateHotelPHPCode($i));
    $mysqli->query(generateHotelSQL($i));
}

$mysqli->close();

echo ($i-1)." hotels generated";