const express = require('express');
const expressSession = require('express-session');
const { createUser, createAdminUser, getUserById, getUserByUsername, verifyPassword } = require('./model/users');
const { createNote, getAllNotes, updateNote, deleteNote } = require('./model/notes');
const { createImage, getAllImages, deleteImage } = require('./model/images');
const crypto = require('crypto');

const DOWNLOADER_URL = process.env.DOWNLOADER_URL || 'http://127.0.0.1:3940/download';
const FLAG = process.env.FLAG || 'mctf{redacted}';

const app = express();

app.use(express.json());
app.use(expressSession({
    secret: crypto.randomBytes(32).toString('hex'),
    resave: false,
    saveUninitialized: false,
}));

app.use(express.static('public'));

app.post('/register', async (req, res) => {
    const { username, password } = req.body;
    if (!username || !password) {
        return res.status(400).json({ error: 'Username and password are required' });
    }
    if (typeof username !== 'string' || typeof password !== 'string') {
        return res.status(400).json({ error: 'Username and password must be strings' });
    }
    if (password.length > 32) {
        return res.status(400).json({ error: 'Password must be less than 32 characters' });
    }

    const user = await createUser(username, password);
    req.session.userId = user.id;
    res.json(user);
});

app.post('/login', async (req, res) => {
    const { username, password } = req.body;
    if (!username || !password) {
        return res.status(400).json({ error: 'Username and password are required' });
    }
    if (typeof username !== 'string' || typeof password !== 'string') {
        return res.status(400).json({ error: 'Username and password must be strings' });
    }

    const isPasswordValid = await verifyPassword(username, password);
    if (!isPasswordValid) {
        return res.status(401).json({ error: 'Invalid username or password' });
    }
    const user = await getUserByUsername(username);
    req.session.userId = user.id;
    res.json({ message: 'Login successful' });
});

app.get("/userInfo", async (req, res) => {
    const userId = req.session.userId;
    if (!userId) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    const user = await getUserById(userId);
    res.json(user);
});

app.post('/logout', async (req, res) => {
    req.session.destroy();
    res.json({ message: 'Logout successful' });
});

app.post('/createNote', async (req, res) => {
    const { title, content } = req.body;
    if (!title || !content) {
        return res.status(400).json({ error: 'Title and content are required' });
    }
    if (typeof title !== 'string' || typeof content !== 'string') {
        return res.status(400).json({ error: 'Title and content must be strings' });
    }
    const userId = req.session.userId;
    if (!userId) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    const note = await createNote(title, content, userId);
    res.json(note);
});

app.get('/notes', async (req, res) => {
    const userId = req.session.userId;
    if (!userId) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    const notes = await getAllNotes(userId);
    res.json(notes);
});

app.post('/updateNote', async (req, res) => {
    const { id, title, content } = req.body;
    if (!id || !title || !content) {
        return res.status(400).json({ error: 'Id, title and content are required' });
    }
    if (typeof id !== 'string' || typeof title !== 'string' || typeof content !== 'string') {
        return res.status(400).json({ error: 'Id, title and content must be strings' });
    }
    const userId = req.session.userId;
    if (!userId) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    const note = await updateNote(id, title, content, userId);
    res.json(note);
});

app.post('/deleteNote', async (req, res) => {
    const { id } = req.body;
    if (!id) {
        return res.status(400).json({ error: 'Id is required' });
    }
    if (typeof id !== 'string') {
        return res.status(400).json({ error: 'Id must be a string' });
    }
    const userId = req.session.userId;
    if (!userId) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    const note = await deleteNote(id, userId);
    res.json(note);
});

app.post('/createImage', async (req, res) => {
    const { image } = req.body;
    if (!image) {
        return res.status(400).json({ error: 'Image is required' });
    }
    const userId = req.session.userId;
    if (!userId) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    const imageData = await createImage(image, userId);
    res.json(imageData);
});

app.post('/createImageFromUrl', async (req, res) => {
    const { url } = req.body;
    if (!url) {
        return res.status(400).json({ error: 'URL is required' });
    }
    if (typeof url !== 'string') {
        return res.status(400).json({ error: 'URL must be a string' });
    }
    
    const userId = req.session.userId;
    if (!userId) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    
    try {
        const response = await fetch(DOWNLOADER_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ url }),
        });
        
        if (!response.ok) {
            return res.status(400).json({ error: 'Failed to download image from URL' });
        }

        const imageBuffer = await response.arrayBuffer();
        const base64Image = Buffer.from(imageBuffer).toString('base64');
        const dataUrl = `data:image/jpeg;base64,${base64Image}`;
        
        const imageData = await createImage(dataUrl, userId);
        res.json(imageData);
    } catch (error) {
        console.error('Error downloading image:', error);
        res.status(500).json({ error: 'Failed to process image from URL' });
    }
});

app.get('/images', async (req, res) => {
    const userId = req.session.userId;
    if (!userId) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    const images = await getAllImages(userId);
    res.json(images);
});

app.post('/deleteImage', async (req, res) => {
    const { id } = req.body;
    if (!id) {
        return res.status(400).json({ error: 'Id is required' });
    }
    if (typeof id !== 'string') {
        return res.status(400).json({ error: 'Id must be a string' });
    }
    const userId = req.session.userId;
    if (!userId) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    const image = await deleteImage(id, userId);
    res.json(image);
});

app.get('/admin/flag', async (req, res) => {
    const userId = req.session.userId;
    if (!userId) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    const user = await getUserById(userId);
    console.log(user);
    if (user.is_admin !== true) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    res.json({ flag: FLAG });
});

app.post('/internal/init', async (req, res) => {
    const { username, password } = req.body;
    if (!username || !password) {
        return res.status(400).json({ error: 'Username and password are required' });
    }
    if (typeof username !== 'string' || typeof password !== 'string') {
        return res.status(400).json({ error: 'Username and password must be strings' });
    }
    if (password.length > 32) {
        return res.status(400).json({ error: 'Password must be less than 32 characters' });
    }
    
    await createAdminUser(username, password);
    res.json({ message: 'Admin user created successfully' });
});

app.get('/ping', (req, res) => {
    res.send('pong');
});

app.listen(3939, '127.0.0.1', () => {
    console.log('Server is running on port 3939');
});