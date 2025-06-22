const CONFIG = {
    NUMBER_ENTRIES: 100000,
    DATA_PATH: "/tmp/data",
    MYSQL: {
        HOST: process.env.MYSQL_HOST || 'localhost',
        PORT: process.env.MYSQL_PORT || 3306,
        USER: process.env.MYSQL_USER || 'root',
        PASSWORD: process.env.MYSQL_PASSWORD || 'rootdbpassword',
        DATABASE: 'data'
    }
};

module.exports = CONFIG;