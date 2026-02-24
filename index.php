<?php
include "config.php";

// Pagination logic remains for the Clients tab
$limit = 3; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM reviews");
    $total_reviews = $total_stmt->fetchColumn();
    $total_pages = ceil($total_reviews / $limit);

    $stmt = $pdo->prepare("SELECT * FROM reviews ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { 
    $reviews = []; 
    $total_pages = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renz Loi Okit | Portfolio</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root { --primary: #facc15; --bg-dark: #050810; --glass: rgba(255, 255, 255, 0.03); --glass-border: rgba(255, 255, 255, 0.1); }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-dark); color: #e2e8f0; overflow-x: hidden; scroll-behavior: smooth; }
        
        .mesh-bg { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at 15% 15%, rgba(250, 204, 21, 0.08) 0%, transparent 45%), radial-gradient(circle at 85% 85%, rgba(56, 189, 248, 0.08) 0%, transparent 45%); z-index: -1; }

        nav { display: flex; justify-content: space-between; align-items: center; padding: 20px 8%; background: rgba(5, 8, 16, 0.95); backdrop-filter: blur(20px); position: fixed; top: 0; width: 100%; z-index: 1000; border-bottom: 1px solid var(--glass-border); }
        nav h1 { font-size: 20px; color: var(--primary); font-weight: 700; }
        nav ul { list-style: none; display: flex; gap: 25px; }
        nav ul li a { text-decoration: none; color: #94a3b8; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; cursor: pointer; transition: 0.3s; }
        nav ul li a.active { color: var(--primary); }

        .main-container { margin-top: 110px; width: 100%; padding: 0 8% 50px; }
        .tab-panel { display: none; animation: fadeIn 0.4s ease-out; }
        .tab-panel.active { display: block; }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* GRID FIX: Smaller, 4-column layout to fit perfectly */
        .work-grid { 
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 15px; 
            margin-top: 20px; 
        }
        .project-card { 
            background: var(--glass); 
            border: 1px solid var(--glass-border); 
            border-radius: 15px; 
            overflow: hidden; 
            height: 300px; /* Reduced height */
            transition: 0.3s; 
        }
        .project-card:hover { border-color: var(--primary); transform: scale(1.02); }
        .project-img { height: 180px; width: 100%; background-size: cover; background-position: center; image-rendering: high-quality; }
        .project-info { padding: 15px; }
        .project-info span { font-size: 0.6rem; color: var(--primary); font-weight: 700; }
        .project-info h3 { font-size: 1rem; margin-top: 5px; }

        /* RESUME BUTTON FIX: Text is now black, no blue links */
        .btn-resume { 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            padding: 12px 25px; 
            background: var(--primary); 
            color: #000 !important; /* Forces black text */
            text-decoration: none !important; 
            border-radius: 8px; 
            font-weight: 700; 
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .btn-resume:hover { background: #fff; color: #000 !important; }

        .review-card { background: var(--glass); padding: 20px; border-radius: 15px; border: 1px solid var(--glass-border); margin-bottom: 15px; }
        
        @media (max-width: 1100px) { .work-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .work-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="mesh-bg"></div>

<nav>
    <h1>RENZ LOI.</h1>
    <ul>
        <li><a onclick="showTab(event, 'intro')" class="nav-link active">Intro</a></li>
        <li><a onclick="showTab(event, 'expertise')" class="nav-link">Expertise</a></li>
        <li><a onclick="showTab(event, 'work')" class="nav-link">Work</a></li>
        <li><a onclick="showTab(event, 'clients')" class="nav-link">Clients</a></li>
        <li><a onclick="showTab(event, 'contact')" class="nav-link">Contact</a></li>
    </ul>
</nav>

<main class="main-container">
    
    <div id="intro" class="tab-panel active">
        <section style="display: flex; align-items: center; gap: 40px; min-height: 60vh;">
            <div class="hero-text">
                <h2 style="font-size: 4rem; font-family: 'Space Grotesk';">Engineering <span>Scalable</span> Systems</h2>
                <p style="color: #94a3b8; margin: 15px 0 25px;">I am Renz Loi Okit, Full Stack Developer.</p>
                <a href="My_CV.pdf" download class="btn-resume">
                    <i class="fas fa-file-download"></i> DOWNLOAD RESUME
                </a>
            </div>
            <img src="Renz.jpg" style="width: 320px; height: 320px; border-radius: 30px; object-fit: cover;">
        </section>
    </div>

    <div id="expertise" class="tab-panel">
        <h2 style="font-size: 2.5rem; margin-bottom: 30px;">Expertise</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="review-card"><h3>Backend</h3><p>PHP & MySQL</p></div>
            <div class="review-card"><h3>Frontend</h3><p>JS & CSS</p></div>
            <div class="review-card"><h3>Systems</h3><p>Architecture</p></div>
        </div>
    </div>

    <div id="work" class="tab-panel">
        <h2 style="font-size: 2.5rem; margin-bottom: 25px;">Selected Works</h2>
        <div class="work-grid">
            <div class="project-card"><div class="project-img" style="background-image: url('p1.jpg');"></div><div class="project-info"><span>LOGISTICS</span><h3>Inventory</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p2.jpg');"></div><div class="project-info"><span>FINTECH</span><h3>Analytics</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p3.jpg');"></div><div class="project-info"><span>COMMERCE</span><h3>Storefront</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p4.jpg');"></div><div class="project-info"><span>SAAS</span><h3>Portal</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p5.jpg');"></div><div class="project-info"><span>RETAIL</span><h3>POS</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p6.jpg');"></div><div class="project-info"><span>ESTATE</span><h3>Finder</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p7.jpg');"></div><div class="project-info"><span>HEALTH</span><h3>EMR</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p8.jpg');"></div><div class="project-info"><span>EDU</span><h3>LMS</h3></div></div>
        </div>
    </div>

    <div id="clients" class="tab-panel">
        <h2 style="font-size: 2.5rem; margin-bottom: 25px;">Reviews</h2>
        <div style="max-width: 700px;">
            <?php foreach ($reviews as $row): ?>
                <div class="review-card">
                    <h4 style="color:var(--primary);"><?= htmlspecialchars($row['name']) ?></h4>
                    <p style="font-size: 0.9rem;">"<?= htmlspecialchars($row['review']) ?>"</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="contact" class="tab-panel">
        <h2 style="font-size: 3rem; text-align: center;">Contact</h2>
        <p style="text-align: center; margin-top: 20px;">renzloiokit.dev@email.com</p>
    </div>

</main>

<script>
    function showTab(event, tabId) {
        const panels = document.querySelectorAll('.tab-panel');
        panels.forEach(p => p.classList.remove('active'));
        const links = document.querySelectorAll('.nav-link');
        links.forEach(l => l.classList.remove('active'));

        document.getElementById(tabId).classList.add('active');
        if(event) event.currentTarget.classList.add('active');
        window.scrollTo(0, 0);
    }
</script>

</body>
</html>
