<?php
include "config.php";

// 1. Pagination Logic (Calculations)
$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
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
} catch (Exception $e) { $reviews = []; $total_pages = 0; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renz Loi Okit | Full Stack Engineer</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #facc15;
            --bg-dark: #050810;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
            --accent-blue: #38bdf8;
        }

        /* RESET & BASE */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        html { scroll-behavior: smooth; }
        
        /* 80% ZOOM INTEGRATION */
        body { 
            background: var(--bg-dark); 
            color: #e2e8f0; 
            overflow-x: hidden;
            
            /* The "80% Zoom" magic */
            zoom: 0.8; /* Chrome, Edge, Safari */
            -moz-transform: scale(0.8); /* Firefox */
            -moz-transform-origin: top center; /* Anchor scaling to the top */
            width: 125%; /* Compensates for the scale reduction (1 / 0.8 = 1.25) */
        }

        h1, h2, h3 { font-family: 'Space Grotesk', sans-serif; }

        /* BACKGROUND EFFECTS */
        #cursor-glow {
            position: fixed; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.07) 0%, transparent 70%);
            border-radius: 50%; pointer-events: none; z-index: 0;
            transform: translate(-50%, -50%); transition: 0.1s ease;
        }

        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 15% 15%, rgba(250, 204, 21, 0.08) 0%, transparent 45%),
                        radial-gradient(circle at 85% 85%, rgba(56, 189, 248, 0.08) 0%, transparent 45%);
            z-index: -1;
        }

        /* NAVIGATION */
        nav {
            display: flex; justify-content: space-between; align-items: center; padding: 25px 8%;
            background: rgba(5, 8, 16, 0.85); backdrop-filter: blur(20px);
            position: fixed; top: 0; width: 100%; z-index: 1000; border-bottom: 1px solid var(--glass-border);
        }
        nav h1 { font-size: 24px; font-weight: 700; color: var(--primary); letter-spacing: -1px; }
        nav ul { list-style: none; display: flex; gap: 30px; }
        nav ul a { text-decoration: none; color: #94a3b8; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; transition: 0.3s; }
        nav ul a:hover { color: var(--primary); }

        section { padding: 140px 8% 80px; position: relative; z-index: 2; }

        /* HERO SECTION */
        .hero { display: flex; align-items: center; gap: 60px; min-height: 85vh; }
        .hero-text h2 { font-size: 5rem; line-height: 1; margin-bottom: 25px; }
        .hero-text h2 span { -webkit-text-stroke: 1.5px var(--primary); color: transparent; }
        
        .btn-premium {
            padding: 18px 45px; background: var(--primary); color: #000;
            border-radius: 15px; font-weight: 700; text-decoration: none;
            display: inline-flex; align-items: center; gap: 12px; transition: 0.4s;
            box-shadow: 0 10px 20px rgba(250,204,21,0.2);
        }
        .btn-premium:hover { transform: translateY(-5px); background: #fff; box-shadow: 0 15px 30px rgba(250,204,21,0.4); }

        .hero-image img { 
            width: 400px; height: 400px; border-radius: 50px; 
            object-fit: cover; border: 1px solid var(--glass-border); 
            transform: rotate(3deg); transition: 0.5s ease-out; 
        }
        .hero:hover .hero-image img { transform: rotate(0deg) scale(1.02); }

        /* EXPERTISE GRID */
        .expertise-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 40px; }
        .skill-card { background: var(--glass); border: 1px solid var(--glass-border); padding: 35px; border-radius: 25px; transition: 0.4s; }
        .skill-card i { font-size: 2.2rem; color: var(--primary); margin-bottom: 20px; display: block; }
        .skill-card h3 { margin-bottom: 10px; font-size: 1.4rem; }
        .skill-card p { font-size: 0.85rem; color: #94a3b8; line-height: 1.6; }
        .skill-card:hover { border-color: var(--primary); transform: translateY(-10px); background: rgba(250, 204, 21, 0.05); }

        /* PROJECT GRID */
        .project-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; margin-top: 50px; }
        .project-card {
            background: var(--glass); border: 1px solid var(--glass-border); border-radius: 24px;
            overflow: hidden; height: 440px; display: flex; flex-direction: column;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .project-img { 
            height: 60%; width: 100%; border-bottom: 1px solid var(--glass-border);
            background-size: cover; background-position: center; background-repeat: no-repeat;
            image-rendering: high-quality;
            background-color: #111; 
        }
        .project-info { height: 40%; padding: 25px; background: rgba(15, 23, 42, 0.4); display: flex; flex-direction: column; justify-content: center; }
        .project-tag { font-size: 0.65rem; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px; padding: 4px 12px; border-radius: 8px; width: fit-content; }
        .project-card h3 { font-size: 1.3rem; margin-bottom: 8px; color: #fff; }
        .project-card p { font-size: 0.85rem; color: #94a3b8; line-height: 1.5; }
        .project-card:hover { transform: translateY(-15px); border-color: var(--primary); }

        /* REVIEWS & PAGINATION */
        .review-box { background: var(--glass); border: 1px solid var(--glass-border); padding: 35px; border-radius: 25px; margin-bottom: 20px; }
        input, textarea { width: 100%; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: white; border-radius: 12px; margin-bottom: 15px; }
        .btn-submit { width: 100%; padding: 18px; background: var(--primary); border: none; border-radius: 12px; font-weight: bold; cursor: pointer; transition: 0.3s; color: #000; }
        
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 40px; }
        .page-link { 
            padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: bold;
            border: 1px solid var(--glass-border); background: var(--glass); color: white; transition: 0.3s;
        }
        .page-link.active { background: var(--primary); color: #000; border-color: var(--primary); }
        .page-link:hover:not(.active) { border-color: var(--primary); color: var(--primary); }

        /* Note: Mobile resets to 100% to keep text readable on phones */
        @media (max-width: 900px) {
            body { zoom: 1; -moz-transform: scale(1); width: 100%; }
            .hero { flex-direction: column; text-align: center; }
            .hero-text h2 { font-size: 3.5rem; }
            nav ul { display: none; }
        }
    </style>
</head>
<body>

<div id="cursor-glow"></div>
<div class="mesh-bg"></div>

<nav>
    <h1>RENZ LOI.</h1>
    <ul>
        <li><a href="#hero">Intro</a></li>
        <li><a href="#expertise">Expertise</a></li>
        <li><a href="#projects">Work</a></li>
        <li><a href="#reviews">Clients</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <h2>Engineering <span>Scalable</span> Web Systems</h2>
        <p>I am <strong>Renz Loi Okit</strong>, a Full Stack Developer specializing in high-performance PHP architectures and sophisticated UI/UX design.</p>
        <div style="margin-top: 40px;">
            <a href="My_CV.pdf" download class="btn-premium">
                <i class="fas fa-download"></i> DOWNLOAD RESUME
            </a>
        </div>
    </div>
    <div class="hero-image">
        <img src="Renz.jpg" alt="Renz Loi Okit">
    </div>
</section>

<section id="expertise">
    <h2 style="font-size: 3rem; margin-bottom: 10px;">Technical <span>Philosophy</span></h2>
    <p style="color: #64748b; margin-bottom: 30px;">Architecture over code. I build systems designed for heavy traffic and data scale.</p>
    <div class="expertise-grid">
        <div class="skill-card"><i class="fas fa-server"></i><h3>Backend Arch</h3><p>PHP (PDO), MySQL, and secure RESTful API development.</p></div>
        <div class="skill-card"><i class="fas fa-layer-group"></i><h3>Frontend Logic</h3><p>JavaScript (ES6), Tailwind CSS, and sophisticated animations.</p></div>
        <div class="skill-card"><i class="fas fa-microchip"></i><h3>System Design</h3><p>Relational database optimization and performance tuning.</p></div>
        <div class="skill-card"><i class="fas fa-terminal"></i><h3>Workflow</h3><p>Git, Command Line proficiency, and CI/CD pipelines.</p></div>
    </div>
</section>

<section id="projects">
    <h2 style="font-size: 3rem; margin-bottom: 40px;">Selected <span>Works</span></h2>
    <div class="project-grid">
        <div class="project-card">
            <div class="project-img" style="background-image: url('p1.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(250,204,21,0.1); color: var(--primary);">Logistics</span>
                <h3>Enterprise Inventory</h3>
                <p>Full-stack warehouse management system with real-time tracking.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p2.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(56,189,248,0.1); color: var(--accent-blue);">Fintech</span>
                <h3>Market Analytics</h3>
                <p>Live data visualization engine for financial and crypto markets.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p3.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(251,113,133,0.1); color: #fb7185);">Commerce</span>
                <h3>Modern Storefront</h3>
                <p>High-conversion e-commerce engine with optimized checkout logic.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p4.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(167,139,250,0.1); color: #a78bfa);">SaaS</span>
                <h3>Client Portal</h3>
                <p>Sophisticated CRM system for secure client data management.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p5.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(34,197,94,0.1); color: #4ade80);">Retail</span>
                <h3>Retail POS</h3>
                <p>Point of Sale system featuring offline-first database synchronization.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p6.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(244,114,182,0.1); color: #f472b6);">Estate</span>
                <h3>Property Finder</h3>
                <p>Geolocation-based portal for property management and search.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p7.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(45,212,191,0.1); color: #2dd4bf);">Health</span>
                <h3>Patient EMR</h3>
                <p>Secure Electronic Medical Records system for healthcare providers.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p8.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(251,146,60,0.1); color: #fb923c);">Edu</span>
                <h3>LMS Platform</h3>
                <p>Interactive learning environment with course tracking engines.</p>
            </div>
        </div>
    </div>
</section>

<section id="reviews">
    <h2 style="font-size: 3rem; margin-bottom: 40px; text-align:center;">Client <span>Voices</span></h2>
    <div style="max-width: 800px; margin: 0 auto;">
        
        <div class="review-box" style="border-color: var(--primary);">
            <form action="save_review.php" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Business Name" required>
                <textarea name="review" rows="3" placeholder="Share your project experience..." required></textarea>
                <button type="submit" class="btn-submit">POST REVIEW</button>
            </form>
        </div>

        <?php foreach ($reviews as $row): ?>
            <div class="review-box">
                <h4 style="color:var(--primary);"><?php echo htmlspecialchars($row['name']); ?></h4>
                <p style="font-size:0.75rem; color:#64748b; margin-bottom:10px;"><?php echo htmlspecialchars($row['company']); ?></p>
                <p style="color:#cbd5e1; font-style: italic;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
            </div>
        <?php endforeach; ?>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>#reviews" 
                   class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                   <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<section id="contact" style="text-align: center;">
    <h2 style="font-size: 3.5rem; margin-bottom: 20px;">Let's <span>Connect.</span></h2>
    <div style="background: var(--glass); padding: 50px; border-radius: 40px; border: 1px solid var(--glass-border); max-width: 600px; margin: 0 auto;">
        <h3 style="color:#fff; font-size: 1.5rem; margin-bottom: 30px;">renzloiokit.dev@email.com</h3>
        <div style="display: flex; justify-content: center; gap: 30px; font-size: 30px;">
            <a href="https://github.com/Raezaga" style="color:white;"><i class="fab fa-github"></i></a>
            <a href="https://www.linkedin.com/in/renz-loi-okit-13397b393/" style="color:white;"><i class="fab fa-linkedin"></i></a>
        </div>
    </div>
</section>

<footer style="text-align: center; padding: 50px; color: #475569; font-size: 0.8rem; border-top: 1px solid var(--glass-border);">
    &copy; <?php echo date("Y"); ?> RENZ LOI OKIT. ALL RIGHTS RESERVED.
</footer>

<script>
    const glow = document.getElementById('cursor-glow');
    document.addEventListener('mousemove', (e) => {
        glow.style.left = e.clientX + 'px';
        glow.style.top = e.clientY + 'px';
    });
</script>

</body>
</html>
