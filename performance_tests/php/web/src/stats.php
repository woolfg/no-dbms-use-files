<?php

require_once('config.php');

// Function to count entries in files
function files_count() {
    if (!is_dir(CONFIG_DATA_PATH)) {
        return 0;
    }
    $files = glob(CONFIG_DATA_PATH . '/*');
    // Exclude the SQLite database file from count
    $files = array_filter($files, function($file) {
        return !str_ends_with($file, '.sqlite');
    });
    return count($files);
}

// Function to count entries in MySQL
function mysql_count() {
    try {
        $mysqli = new mysqli(
            $GLOBALS['CONFIG_MYSQL_HOST'],
            $GLOBALS['CONFIG_MYSQL_USER'],
            $GLOBALS['CONFIG_MYSQL_PASSWORD'],
            'data',
            $GLOBALS['CONFIG_MYSQL_PORT']
        );

        if ($mysqli->connect_errno) {
            return "Error: " . $mysqli->connect_error;
        }

        $result = $mysqli->query("SELECT COUNT(*) as count FROM data");
        if (!$result) {
            $mysqli->close();
            return "Error: Table not found or query failed";
        }
        
        $row = $result->fetch_assoc();
        $count = $row['count'];
        $mysqli->close();
        return $count;
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

// Function to count entries in SQLite
function sqlite_count() {
    $sqlite_db_path = CONFIG_DATA_PATH . '/data.sqlite';
    
    if (!file_exists($sqlite_db_path)) {
        return 0;
    }
    
    try {
        $pdo = new PDO('sqlite:' . $sqlite_db_path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM data");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $row['count'];
        $pdo = null;
        return $count;
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}

// Get counts for all storage methods
$files_count = files_count();
$mysql_count = mysql_count();
$sqlite_count = sqlite_count();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Storage Statistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }
        .stat-label {
            font-weight: bold;
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .error {
            color: #dc3545;
            font-size: 14px;
        }
        .refresh-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        .refresh-btn:hover {
            background-color: #0056b3;
        }
        .timestamp {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Data Storage Statistics</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Files Storage</div>
                <div class="stat-value">
                    <?php echo $files_count; ?>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">MySQL Database</div>
                <div class="stat-value">
                    <?php echo $mysql_count; ?>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">SQLite Database</div>
                <div class="stat-value">
                    <?php echo $sqlite_count; ?>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Expected Entries</div>
                <div class="stat-value">
                    <?php echo number_format(CONFIG_NUMBER_ENTRIES); ?>
                </div>
            </div>
        </div>
        
        <button class="refresh-btn" onclick="window.location.reload()">
            Refresh Statistics
        </button>
        
        <div class="timestamp">
            Last updated: <?php echo date('Y-m-d H:i:s'); ?>
        </div>
    </div>
</body>
</html>