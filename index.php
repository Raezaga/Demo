<?php
include "config.php";

// Fetch reviews from database
try {
    $stmt = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $reviews = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renz Loi Okit | Full Stack Developer</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #facc15;
            --bg-dark: #050810;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        /* 1. Reset & Global Layout */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: var(--bg-dark); 
            color: #e2e8f0; 
            height: 100vh;
            overflow: hidden; /* Prevents long scrolling */
        }

        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 15% 15%, rgba(250, 204, 21, 0.08) 0%, transparent 45%),
                        radial-gradient(circle at 85% 85%, rgba(56, 189, 248, 0.08) 0%, transparent 45%);
            z-index: -1;
        }

        /* 2. Navigation */
        nav {
            display: flex; justify-content: space-between; align-items: center; padding: 25px 8%;
            background: rgba(5, 8, 16, 0.95); backdrop-filter: blur(20px);
            position: fixed; top: 0; width: 100%; z-index: 1000; border-bottom: 1px solid var(--glass-border);
        }
        nav h1 { font-size: 22px; color: var(--primary); font-weight: 700; }
        nav ul { list-style: none; display: flex; gap: 25px; }
        nav ul li a { 
            text-decoration: none; color: #94a3b8; font-size: 0.8rem; 
            font-weight: 600; text-transform: uppercase; cursor: pointer; transition: 0.3s; 
        }
        nav ul li a.active, nav ul li a:hover { color: var(--primary); }

        /* 3. The Solo Display Main Stage */
        .main-stage {
            margin-top: 100px;
            height: calc(100vh - 100px);
            width: 100%;
            position: relative;
            padding: 40px 8%;
            overflow-y: auto; /* Internal scrolling only */
        }

        .tab-panel {
            display: none; /* Everything is hidden */
            animation: fadeIn 0.4s ease-out;
        }

        .tab-panel.active {
            display: block; /* Only active section renders */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* 4. Section Specific Content Styles */
        .hero-box { display: flex; align-items: center; gap: 50px; min-height: 60vh; }
        .hero-text h2 { font-size: 4.5rem; line-height: 1.1; margin-bottom: 20px; font-family: 'Space Grotesk'; }
        .hero-text h2 span { -webkit-text-stroke: 1.5px var(--primary); color: transparent; }
        
        .btn-cv {
            display: inline-flex; align-items: center; gap: 10px; padding: 18px 40px;
            background: var(--primary); color: #000; border-radius: 12px;
            text-decoration: none; font-weight: 700; margin-top: 25px; transition: 0.3s;
        }
        .btn-cv:hover { transform: translateY(-3px); background: #fff; }

        .expertise-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .skill-card { background: var(--glass); border: 1px solid var(--glass-border); padding: 30px; border-radius: 20px; transition: 0.3s; }
        .skill-card:hover { border-color: var(--primary); transform: translateY(-5px); }
        .skill-card i { color: var(--primary); font-size: 2rem; margin-bottom: 15px; display: block; }

        .work-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
        .project-card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 20px; overflow: hidden; height: 400px; transition: 0.4s; }
        .project-card:hover { border-color: var(--primary); transform: translateY(-10px); }
        .project-img { height: 60%; width: 100%; background-size: cover; background-position: center; image-rendering: high-quality; border-bottom: 1px solid var(--glass-border); }
        
        .review-card { background: var(--glass); padding: 25px; border-radius: 15px; border: 1px solid var(--glass-border); margin-bottom: 15px; }
        input, textarea { width: 100%; padding: 15px; margin-bottom: 15px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: white; border-radius: 10px; }

        .main-stage::-webkit-scrollbar { width: 6px; }
        .main-stage::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 10px; }

        @media (max-width: 900px) {
            .hero-box { flex-direction: column; text-align: center; }
            .hero-text h2 { font-size: 3rem; }
        }
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

<main class="main-stage">
    
    <div id="intro" class="tab-panel active">
        <div class="hero-box">
            <div class="hero-text">
                <h2>Engineering <span>Scalable</span><br>Web Systems</h2>
                <p style="font-size: 1.1rem; color: #94a3b8; max-width: 500px;">I am <strong>Renz Loi Okit</strong>, a Full Stack Developer specialized in building efficient, logic-driven PHP applications.</p>
                <a href="My_CV.pdf" download class="btn-cv">
                    <i class="fas fa-download"></i> DOWNLOAD RESUME
                </a>
            </div>
            <div class="hero-img">
                <img src="Renz.jpg" style="width: 380px; height: 380px; border-radius: 50px; object-fit: cover; border: 1px solid var(--glass-border); transform: rotate(3deg);">
            </div>
        </div>
    </div>

    <div id="expertise" class="tab-panel">
        <h2 style="font-size: 2.5rem; margin-bottom: 30px; font-family: 'Space Grotesk';">Technical <span style="color:var(--primary)">Philosophy</span></h2>
        <div class="expertise-grid">
            <div class="skill-card"><i class="fas fa-server"></i><h3>Backend Arch</h3><p style="color:#94a3b8; font-size:0.9rem;">PHP (PDO), MySQL, and secure RESTful API integration.</p></div>
            <div class="skill-card"><i class="fas fa-layer-group"></i><h3>Frontend Logic</h3><p style="color:#94a3b8; font-size:0.9rem;">ES6 JavaScript, Responsive CSS, and UI state management.</p></div>
            <div class="skill-card"><i class="fas fa-microchip"></i><h3>System Design</h3><p style="color:#94a3b8; font-size:0.9rem;">Database normalization and server-side optimization.</p></div>
            <div class="skill-card"><i class="fas fa-terminal"></i><h3>Workflow</h3><p style="color:#94a3b8; font-size:0.9rem;">Git version control and automated deployment logic.</p></div>
        </div>
    </div>

    <div id="work" class="tab-panel">
        <h2 style="font-size: 2.5rem; margin-bottom: 30px; font-family: 'Space Grotesk';">Selected <span style="color:var(--primary)">Works</span></h2>
        <div class="work-grid">
            <?php 
            $projs = [
                ['p1.jpg', 'Logistics', 'Enterprise Inventory'], ['p2.jpg', 'Fintech', 'Market Analytics'],
                ['p3.jpg', 'Commerce', 'Modern Storefront'], ['p4.jpg', 'SaaS', 'Client Portal'],
                ['p5.jpg', 'Retail', 'POS System'], ['p6.jpg', 'Estate', 'Property Finder'],
                ['p7.jpg', 'Health', 'Patient EMR'], ['p8.jpg', 'Edu', 'LMS Platform']
            ];
            foreach($projs as $p): ?>
            <div class="project-card">
                <div class="project-img" style="background-image: url('<?= $p[0] ?>');"></div>
                <div style="padding: 20px;">
                    <span style="font-size: 0.7rem; color: var(--primary); font-weight: 800;"><?= strtoupper($p[1]) ?></span>
                    <h3 style="margin-top: 5px; font-size: 1.2rem;"><?= $p[2] ?></h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="clients" class="tab-panel">
        <h2 style="font-size: 2.5rem; margin-bottom: 30px; font-family: 'Space Grotesk'; text-align: center;">Client <span style="color:var(--primary)">Voices</span></h2>
        <div style="max-width: 700px; margin: 0 auto;">
            <div class="review-card" style="border-color: var(--primary);">
                <form action="save_review.php" method="POST">
                    <input type="text" name="name" placeholder="Full Name" required>
                    <textarea name="review" rows="3" placeholder="How was your experience working with me?" required></textarea>
                    <button type="submit" style="width:100%; padding:15px; background:var(--primary); border:none; border-radius:10px; font-weight:700; cursor:pointer;">POST FEEDBACK</button>
                </form>
            </div>
            <?php foreach ($reviews as $row): ?>
                <div class="review-card">
                    <h4 style="color:var(--primary);"><?= htmlspecialchars($row['name']) ?></h4>
                    <p style="font-size: 0.9rem; margin-top: 10px; color: #cbd5e1; font-style: italic;">"<?= htmlspecialchars($row['review']) ?>"</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="contact" class="tab-panel">
        <div style="text-align: center; padding-top: 50px;">
            <h2 style="font-size: 3.5rem; font-family: 'Space Grotesk';">Let's <span style="color:var(--primary)">Connect.</span></h2>
            <div style="margin-top: 30px; background: var(--glass); padding: 50px; border-radius: 30px; display: inline-block; border: 1px solid var(--glass-border);">
                <h3 style="font-size: 1.5rem; margin-bottom: 20px;">renzloiokit.dev@email.com</h3>
                <div style="display: flex; justify-content: center; gap: 30px; font-size: 2rem;">
                    <a href="https://github.com/Raezaga" style="color:white;"><i class="fab fa-github"></i></a>
                    <a href="https://www.linkedin.com/in/renz-loi-okit-13397b393/" style="color:white;"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    function showTab(event, tabId) {
        // Hide all inactive panels
        const panels = document.querySelectorAll('.tab-panel');
        panels.forEach(p => p.classList.remove('active'));

        // Reset nav links
        const links = document.querySelectorAll('.nav-link');
        links.forEach(l => l.classList.remove('active'));

        // Show selected panel
        document.getElementById(tabId).classList.add('active');
        event.currentTarget.classList.add('active');

        // Reset scroll to top for new section
        document.querySelector('.main-stage').scrollTop = 0;
    }
</script>

</body>
</html>
