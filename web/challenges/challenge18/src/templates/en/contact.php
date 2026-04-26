<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - GlobalTranslate</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0a0a1a; color: #e0e0e0; min-height: 100vh; }
        nav { background: #12122a; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2a2a4a; }
        nav .logo { font-size: 1.4em; font-weight: bold; color: #7b68ee; }
        nav .links a { color: #b0b0c0; text-decoration: none; margin-left: 25px; font-size: 0.95em; }
        nav .links a:hover { color: #7b68ee; }
        .content { max-width: 700px; margin: 60px auto; padding: 0 20px; }
        .content h1 { color: #7b68ee; margin-bottom: 20px; }
        .content p { color: #888; line-height: 1.8; margin-bottom: 15px; }
        form { margin-top: 30px; }
        label { display: block; color: #aaa; margin-bottom: 5px; font-size: 0.9em; }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 20px; background: #14142a; border: 1px solid #2a2a4a; border-radius: 6px; color: #e0e0e0; font-size: 0.95em; }
        input:focus, textarea:focus { outline: none; border-color: #7b68ee; }
        button { background: #7b68ee; color: #fff; border: none; padding: 12px 30px; border-radius: 6px; cursor: pointer; font-size: 1em; }
        button:hover { background: #6a5acd; }
        footer { text-align: center; padding: 30px; color: #555; font-size: 0.8em; border-top: 1px solid #1a1a3a; margin-top: 60px; }
    </style>
</head>
<body>
    <nav>
        <div class="logo">GlobalTranslate</div>
        <div class="links">
            <a href="?lang=en&page=home">Home</a>
            <a href="?lang=en&page=about">About</a>
            <a href="?lang=en&page=contact">Contact</a>
        </div>
    </nav>
    <div class="content">
        <h1>Contact Us</h1>
        <p>Have a project in mind? Get in touch with our team for a free quote.</p>
        <form method="POST" action="#">
            <label>Name</label>
            <input type="text" name="name" placeholder="Your name">
            <label>Email</label>
            <input type="email" name="email" placeholder="your@email.com">
            <label>Message</label>
            <textarea name="message" rows="5" placeholder="Tell us about your project..."></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>
    <footer>&copy; 2026 GlobalTranslate Inc. All rights reserved.</footer>
</body>
</html>
