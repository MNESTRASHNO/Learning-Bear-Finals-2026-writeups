const pool = require('./db');
const bcrypt = require('bcrypt');

const createUser = async (username, password) => {
    const hashedPassword = await bcrypt.hash(password, 10);
    const query = 'INSERT INTO users (username, password) VALUES ($1, $2) RETURNING *';
    const values = [username, hashedPassword];
    const result = await pool.query(query, values);
    return result.rows[0];
};

const createAdminUser = async (username, password) => {
    const hashedPassword = await bcrypt.hash(password, 10);
    const query = 'INSERT INTO users (username, password, is_admin) VALUES ($1, $2, $3) RETURNING *';
    const values = [username, hashedPassword, true];
    const result = await pool.query(query, values);
    return result.rows[0];
};

const verifyPassword = async (username, password) => {
    const user = await getUserByUsername(username);
    if (!user) {
        return false;
    }
    return await bcrypt.compare(password, user.password);
};

const getUserById = async (id) => {
    const query = 'SELECT * FROM users WHERE id = $1';
    const values = [id];
    const result = await pool.query(query, values);
    return result.rows[0];
};

const getUserByUsername = async (username) => {
    const query = 'SELECT * FROM users WHERE username = $1';
    const values = [username];
    const result = await pool.query(query, values);
    return result.rows[0];
};

module.exports = {
    createUser,
    createAdminUser,
    getUserById,
    getUserByUsername,
    verifyPassword
};