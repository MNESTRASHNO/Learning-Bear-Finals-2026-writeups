<?php
error_reporting(0);
$page = isset($_GET['page']) ? $_GET['page'] : 'home.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaSphere Technologies</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0a0a0f;
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        nav {
            background: rgba(15, 15, 25, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 1.4rem;
            font-weight: 700;
            background: linear-gradient(135deg, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .nav-links { display: flex; gap: 2rem; list-style: none; }

        .nav-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
            letter-spacing: 0.3px;
        }

        .nav-links a:hover { color: #c084fc; }

        main {
            flex: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 3rem 2rem;
            width: 100%;
        }

        footer {
            text-align: center;
            padding: 2rem;
            color: #475569;
            font-size: 0.8rem;
            border-top: 1px solid rgba(99, 102, 241, 0.1);
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #e0e7ff, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        h2 {
            font-size: 1.6rem;
            color: #a5b4fc;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        p { line-height: 1.7; color: #94a3b8; margin-bottom: 1rem; }

        .hero-sub {
            font-size: 1.15rem;
            color: #64748b;
            max-width: 600px;
            margin-bottom: 2.5rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .card {
            background: rgba(15, 15, 30, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 12px;
            padding: 2rem;
            transition: border-color 0.3s, transform 0.2s;
        }

        .card:hover {
            border-color: rgba(139, 92, 246, 0.4);
            transform: translateY(-2px);
        }

        .card h3 {
            color: #c4b5fd;
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }

        .card p { font-size: 0.9rem; }

        .form-group { margin-bottom: 1.2rem; }

        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            color: #a5b4fc;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            max-width: 480px;
            padding: 0.7rem 1rem;
            background: rgba(15, 15, 30, 0.9);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 0.9rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: rgba(139, 92, 246, 0.6);
        }

        .form-group textarea { resize: vertical; min-height: 120px; }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .team-member {
            text-align: center;
            padding: 2rem 1rem;
            background: rgba(15, 15, 30, 0.6);
            border-radius: 12px;
            border: 1px solid rgba(99, 102, 241, 0.1);
        }

        .avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #c084fc);
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }

        .team-member h4 { color: #c4b5fd; margin-bottom: 0.3rem; }
        .team-member span { font-size: 0.8rem; color: #64748b; }
    </style>
</head>
<body>
    <nav>
        <div class="logo">NovaSphere</div>
        <ul class="nav-links">
            <li><a href="?page=home.php">Home</a></li>
            <li><a href="?page=about.php">About</a></li>
            <li><a href="?page=services.php">Services</a></li>
            <li><a href="?page=contact.php">Contact</a></li>
        </ul>
    </nav>
    <main>
        <?php include('pages/' . $page); ?>
    </main>
    <footer>
        &copy; 2026 NovaSphere Technologies. All rights reserved.
    </footer>
</body>
</html>
