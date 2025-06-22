const express = require('express');
const mysql = require('mysql2/promise');
const sqlite3 = require('sqlite3').verbose();
const fs = require('fs').promises;
const path = require('path');
const CONFIG = require('./config');
const { generateRandomIDList, generateEntryData } = require('./functions');

const app = express();
const port = 80;

// In-memory data storage (replaces file storage from PHP version)
let inMemoryData = new Map();

// Helper function to format data for output
function formatEntryData(data) {
    const features = [];
    if (data.wifi) features.push("wifi");
    if (data.h24reception) features.push("24reception");
    if (data.pool) features.push("pool");
    
    return {
        id: data.id,
        name: data.name,
        features: features,
        field1: data.field1
    };
}

// Routes
app.get('/', (req, res) => {
    res.send(`
        <html>
        <body>
            <h1>Node.js based Performance test</h1>
            <ul>
                <li><a href="/fetch_db">Fetch from Database</a></li>
                <li><a href="/fetch_memory">Fetch from Memory</a></li>
                <li><a href="/fetch_sqlite">Fetch from SQLite</a></li>
                <li><a href="/fetch_db_timing">Database with Timing</a></li>
                <li><a href="/fetch_memory_timing">Memory with Timing</a></li>
                <li><a href="/fetch_sqlite_timing">SQLite with Timing</a></li>
                <li><a href="/generate">Generate Test Data</a></li>
            </ul>
        </body>
        </html>
    `);
});

app.get('/fetch_db', async (req, res) => {
    try {
        const ids = generateRandomIDList(10);
        
        const connection = await mysql.createConnection({
            host: CONFIG.MYSQL.HOST,
            port: CONFIG.MYSQL.PORT,
            user: CONFIG.MYSQL.USER,
            password: CONFIG.MYSQL.PASSWORD,
            database: CONFIG.MYSQL.DATABASE
        });
        
        const [rows] = await connection.execute(
            `SELECT * FROM data WHERE id IN (${ids.join(',')})`,
        );
        
        const data = rows.map(formatEntryData);
        
        await connection.end();
        
        res.json({ data, message: "fetched" });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.get('/fetch_memory', (req, res) => {
    try {
        const ids = generateRandomIDList(10);
        const data = [];
        
        for (const id of ids) {
            const entry = inMemoryData.get(id);
            if (entry) {
                data.push(formatEntryData(entry));
            }
        }
        
        res.json({ data, message: "fetched" });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.get('/fetch_sqlite', (req, res) => {
    try {
        const ids = generateRandomIDList(10);
        const sqlitePath = path.join(CONFIG.DATA_PATH, 'data.sqlite');
        
        const db = new sqlite3.Database(sqlitePath);
        
        const query = `SELECT * FROM data WHERE id IN (${ids.join(',')})`;
        
        db.all(query, [], (err, rows) => {
            if (err) {
                return res.status(500).json({ error: err.message });
            }
            
            const data = rows.map(formatEntryData);
            res.json({ data, message: "fetched" });
            
            db.close();
        });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Timing versions
app.get('/fetch_db_timing', async (req, res) => {
    const timeStart = Date.now();
    
    try {
        const ids = generateRandomIDList(10);
        const timeConfig = Date.now();
        
        const connection = await mysql.createConnection({
            host: CONFIG.MYSQL.HOST,
            port: CONFIG.MYSQL.PORT,
            user: CONFIG.MYSQL.USER,
            password: CONFIG.MYSQL.PASSWORD,
            database: CONFIG.MYSQL.DATABASE
        });
        
        const timeConnection = Date.now();
        
        const [rows] = await connection.execute(
            `SELECT * FROM data WHERE id IN (${ids.join(',')})`,
        );
        
        const timeQuery = Date.now();
        
        const data = rows.map(formatEntryData);
        
        const timeFetching = Date.now();
        
        await connection.end();
        
        const timeOutput = Date.now();
        
        const timings = {
            config: timeConfig - timeStart,
            connection: timeConnection - timeConfig,
            query: timeQuery - timeConnection,
            fetching: timeFetching - timeQuery,
            output: timeOutput - timeFetching
        };
        
        res.json({ data, message: "fetched", timings });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.get('/fetch_memory_timing', (req, res) => {
    const timeStart = Date.now();
    
    try {
        const ids = generateRandomIDList(10);
        const timeConfig = Date.now();
        
        // No connection needed for memory
        const timeConnection = Date.now();
        
        const data = [];
        for (const id of ids) {
            const entry = inMemoryData.get(id);
            if (entry) {
                data.push(formatEntryData(entry));
            }
        }
        
        const timeQuery = Date.now();
        const timeFetching = Date.now();
        const timeOutput = Date.now();
        
        const timings = {
            config: timeConfig - timeStart,
            connection: timeConnection - timeConfig,
            query: timeQuery - timeConnection,
            fetching: timeFetching - timeQuery,
            output: timeOutput - timeFetching
        };
        
        res.json({ data, message: "fetched", timings });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.get('/fetch_sqlite_timing', (req, res) => {
    const timeStart = Date.now();
    
    try {
        const ids = generateRandomIDList(10);
        const timeConfig = Date.now();
        
        const sqlitePath = path.join(CONFIG.DATA_PATH, 'data.sqlite');
        const db = new sqlite3.Database(sqlitePath);
        
        const timeConnection = Date.now();
        
        const query = `SELECT * FROM data WHERE id IN (${ids.join(',')})`;
        
        const timeQuery = Date.now();
        
        db.all(query, [], (err, rows) => {
            if (err) {
                return res.status(500).json({ error: err.message });
            }
            
            const timeFetching = Date.now();
            const data = rows.map(formatEntryData);
            const timeOutput = Date.now();
            
            const timings = {
                config: timeConfig - timeStart,
                connection: timeConnection - timeConfig,
                query: timeQuery - timeConnection,
                fetching: timeFetching - timeQuery,
                output: timeOutput - timeFetching
            };
            
            res.json({ data, message: "fetched", timings });
            
            db.close();
        });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.get('/generate', async (req, res) => {
    try {
        console.log('Starting data generation...');
        
        // Clear existing in-memory data
        inMemoryData.clear();
        
        // Create data directory if it doesn't exist
        try {
            await fs.mkdir(CONFIG.DATA_PATH, { recursive: true });
        } catch (err) {
            // Directory might already exist
        }
        
        // MySQL setup
        const connection = await mysql.createConnection({
            host: CONFIG.MYSQL.HOST,
            port: CONFIG.MYSQL.PORT,
            user: CONFIG.MYSQL.USER,
            password: CONFIG.MYSQL.PASSWORD
        });
        
        await connection.execute('CREATE DATABASE IF NOT EXISTS data');
        await connection.changeUser({ database: CONFIG.MYSQL.DATABASE });
        
        await connection.execute(`
            CREATE TABLE IF NOT EXISTS data (
                id INT UNSIGNED NOT NULL PRIMARY KEY,
                name VARCHAR(100),
                pool TINYINT,
                wifi TINYINT,
                h24reception TINYINT,
                field1 TEXT
            )
        `);
        
        // Clear existing MySQL data
        await connection.execute('DELETE FROM data');
        
        // SQLite setup
        const sqlitePath = path.join(CONFIG.DATA_PATH, 'data.sqlite');
        const db = new sqlite3.Database(sqlitePath);
        
        await new Promise((resolve, reject) => {
            db.serialize(() => {
                db.run(`CREATE TABLE IF NOT EXISTS data (
                    id INTEGER PRIMARY KEY,
                    name TEXT,
                    pool INTEGER,
                    wifi INTEGER,
                    h24reception INTEGER,
                    field1 TEXT
                )`);
                
                db.run('DELETE FROM data', resolve);
            });
        });
        
        // Generate data
        for (let i = 1; i <= CONFIG.NUMBER_ENTRIES; i++) {
            const entryData = generateEntryData(i);
            
            // Save to in-memory storage
            inMemoryData.set(i, entryData);
            
            // Save to MySQL
            await connection.execute(
                'INSERT INTO data (id, name, pool, wifi, h24reception, field1) VALUES (?, ?, ?, ?, ?, ?)',
                [entryData.id, entryData.name, entryData.pool, entryData.wifi, entryData.h24reception, entryData.field1]
            );
            
            // Save to SQLite
            await new Promise((resolve, reject) => {
                db.run(
                    'INSERT INTO data (id, name, pool, wifi, h24reception, field1) VALUES (?, ?, ?, ?, ?, ?)',
                    [entryData.id, entryData.name, entryData.pool, entryData.wifi, entryData.h24reception, entryData.field1],
                    resolve
                );
            });
            
            if (i % 10000 === 0) {
                console.log(`Generated ${i} entries...`);
            }
        }
        
        // Get counts
        const memoryCount = inMemoryData.size;
        
        const [mysqlRows] = await connection.execute('SELECT COUNT(*) as count FROM data');
        const mysqlCount = mysqlRows[0].count;
        
        const sqliteCount = await new Promise((resolve) => {
            db.get('SELECT COUNT(*) as count FROM data', [], (err, row) => {
                resolve(row ? row.count : 0);
            });
        });
        
        await connection.end();
        db.close();
        
        const message = `${CONFIG.NUMBER_ENTRIES} hotels generated for memory, MySQL, and SQLite\n` +
                       `Memory: ${memoryCount} entries\n` +
                       `MySQL: ${mysqlCount} entries\n` +
                       `SQLite: ${sqliteCount} entries`;
        
        console.log(message);
        res.json({ message, counts: { memory: memoryCount, mysql: mysqlCount, sqlite: sqliteCount } });
        
    } catch (error) {
        console.error('Generation error:', error);
        res.status(500).json({ error: error.message });
    }
});

app.listen(port, () => {
    console.log(`Node.js performance test server running on port ${port}`);
});