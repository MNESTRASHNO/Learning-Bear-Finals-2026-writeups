const pool = require('./db');

const createImage = async (image, userId) => {
    let base64Data = image;
    if (image.startsWith('data:')) {
        base64Data = image.split(',')[1];
    }
    const imageBuffer = Buffer.from(base64Data, 'base64');
    
    const query = 'INSERT INTO images (image, user_id) VALUES ($1, $2) RETURNING *';
    const values = [imageBuffer, userId];
    const result = await pool.query(query, values);
    return result.rows[0];
};

const getAllImages = async (userId) => {
    const query = 'SELECT * FROM images WHERE user_id = $1';
    const values = [userId];
    const result = await pool.query(query, values);
    return result.rows;
};

const deleteImage = async (id, userId) => {
    const query = 'DELETE FROM images WHERE id = $1 and user_id = $2 RETURNING *';
    const values = [id, userId];
    const result = await pool.query(query, values);
    return result.rows[0];
};

module.exports = {
    createImage,
    getAllImages,
    deleteImage,
};