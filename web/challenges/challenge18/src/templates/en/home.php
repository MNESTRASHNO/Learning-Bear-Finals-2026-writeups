<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlobalTranslate - Translation Services</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0a0a1a; color: #e0e0e0; min-height: 100vh; }
        nav { background: #12122a; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2a2a4a; }
        nav .logo { font-size: 1.4em; font-weight: bold; color: #7b68ee; }
        nav .links a { color: #b0b0c0; text-decoration: none; margin-left: 25px; font-size: 0.95em; }
        nav .links a:hover { color: #7b68ee; }
        .hero { text-align: center; padding: 80px 20px 40px; }
        .hero h1 { font-size: 2.8em; margin-bottom: 15px; background: linear-gradient(135deg, #7b68ee, #00bcd4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { font-size: 1.1em; color: #888; max-width: 600px; margin: 0 auto; }
        .services { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; padding: 40px; max-width: 1000px; margin: 0 auto; }
        .card { background: #14142a; border: 1px solid #2a2a4a; border-radius: 12px; padding: 30px; transition: transform 0.2s; }
        .card:hover { transform: translateY(-3px); border-color: #7b68ee; }
        .card h3 { color: #7b68ee; margin-bottom: 10px; }
        .card p { color: #888; font-size: 0.9em; line-height: 1.6; }
        .lang-selector { text-align: center; padding: 20px; }
        .lang-selector a { color: #7b68ee; text-decoration: none; margin: 0 10px; padding: 5px 15px; border: 1px solid #2a2a4a; border-radius: 20px; font-size: 0.85em; }
        .lang-selector a:hover { background: #7b68ee; color: #fff; }
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
    <div class="lang-selector">
        <a href="?lang=en&page=home">English</a>
        <a href="?lang=fr&page=home">Fran&ccedil;ais</a>
        <a href="?lang=de&page=home">Deutsch</a>
        <a href="?lang=es&page=home">Espa&ntilde;ol</a>
    </div>
    <div class="hero">
        <h1>Professional Translation Services</h1>
        <p>Bridging languages, connecting cultures. We provide accurate and fast translations for businesses worldwide.</p>
    </div>
    <div class="services">
        <div class="card">
            <h3>Document Translation</h3>
            <p>Legal, medical, and technical document translations with certified accuracy and fast turnaround times.</p>
        </div>
        <div class="card">
            <h3>Website Localization</h3>
            <p>Full website translation and cultural adaptation to reach your global audience effectively.</p>
        </div>
        <div class="card">
            <h3>Real-time Interpretation</h3>
            <p>Live interpretation services for conferences, meetings, and business negotiations.</p>
        </div>
    </div>
    <footer>&copy; 2026 GlobalTranslate Inc. All rights reserved.</footer>
</body>
</html>
