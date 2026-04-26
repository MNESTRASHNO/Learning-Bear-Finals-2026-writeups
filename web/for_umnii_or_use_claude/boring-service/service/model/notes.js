const pool = require('./db');

const createNote = async (title, content, userId) => {
    const query = 'INSERT INTO notes (title, content, user_id) VALUES ($1, $2, $3) RETURNING *';
    const values = [title, content, userId];
    const result = await pool.query(query, values);
    return result.rows[0];
};

const getNoteById = async (id) => {
    const query = 'SELECT * FROM notes WHERE id = $1';
    const values = [id];
    const result = await pool.query(query, values);
    return result.rows[0];
};

const getAllNotes = async (userId) => {
    const query = 'SELECT * FROM notes WHERE user_id = $1';
    const values = [userId];
    const result = await pool.query(query, values);
    return result.rows;
};

const updateNote = async (id, title, content, userId) => {
    const query = 'UPDATE notes SET title = $1, content = $2 WHERE id = $3 and user_id = $4 RETURNING *';
    const values = [title, content, id, userId];
    const result = await pool.query(query, values);
    return result.rows[0];
};

const deleteNote = async (id, userId) => {
    const query = 'DELETE FROM notes WHERE id = $1 and user_id = $2 RETURNING *';
    const values = [id, userId];
    const result = await pool.query(query, values);
    return result.rows[0];
};

module.exports = {
    createNote,
    getNoteById,
    getAllNotes,
    updateNote,
    deleteNote
};