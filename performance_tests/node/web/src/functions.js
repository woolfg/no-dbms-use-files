const CONFIG = require('./config');

function getRandomEntryID() {
    return Math.floor(Math.random() * CONFIG.NUMBER_ENTRIES) + 1;
}

function generateRandomIDList(count) {
    const ids = [];
    for (let i = 0; i < count; i++) {
        ids.push(getRandomEntryID());
    }
    return ids;
}

function generateRandomString(length) {
    const characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 .,!?;:-()[]{}';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
}

function generateEntryData(id) {
    return {
        id: id,
        name: `hotel ${id}`,
        pool: 1,
        wifi: 1,
        h24reception: 1,
        field1: generateRandomString(Math.floor(Math.random() * 600) + 200)
    };
}

module.exports = {
    getRandomEntryID,
    generateRandomIDList,
    generateRandomString,
    generateEntryData
};