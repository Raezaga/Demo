<?php
include "config.php";
// Pagination Logic remains the same
$limit = 5; 
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
} catch (Exception $e) { $reviews = []; $total_pages = 0; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renz Loi | Software Architect</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;800&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #facc15;
            --bg: #030408;
            --card-bg: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            background-color: var(--bg); 
            color: var(--text-main); 
            font-family: 'Inter', sans-serif; 
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* PREMIUM GRADIENT BACKGROUND */
        .bg-glow {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(circle at 80% 20%, rgba(250, 204, 21, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 20% 80%, rgba(56, 189, 248, 0.05) 0%, transparent 40%);
            z-index: -1;
        }

        /* ULTRA SLEEK NAV */
        nav {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            width: 90%; max-width: 1200px; padding: 15px 30px;
            background: rgba(10, 10, 15, 0.7); backdrop-filter: blur(12px);
            border: 1px solid var(--border); border-radius: 100px;
            display: flex; justify-content: space-between; align-items: center; z-index: 1000;
        }
        nav h1 { font-family: 'Plus Jakarta Sans'; font-size: 1.2rem; font-weight: 800; letter-spacing: -1px; }
        nav ul { display: flex; gap: 25px; list-style: none; }
        nav a { text-decoration: none; color: var(--text-dim); font-size: 0.85rem; font-weight: 500; transition: 0.3s; }
        nav a:hover { color: var(--primary); }

        section { max-width: 1300px; margin: 0 auto; padding: 120px 5%; }

        /* HERO SECTION - Agency Style */
        .hero { text-align: center; padding-top: 180px; }
        .hero h2 { 
            font-family: 'Plus Jakarta Sans'; font-size: clamp(2.5rem, 8vw, 5.5rem); 
            font-weight: 800; line-height: 0.95; letter-spacing: -3px; margin-bottom: 30px;
        }
        .hero h2 span { color: var(--primary); }
        .hero p { max-width: 600px; margin: 0 auto 40px; color: var(--text-dim); font-size: 1.1rem; }

        .btn-main {
            background: var(--text-main); color: var(--bg); padding: 16px 35px;
            border-radius: 100px; font-weight: 700; text-decoration: none;
            display: inline-flex; align-items: center; gap: 10px; transition: 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        }
        .btn-main:hover { transform: scale(1.05); background: var(--primary); }

        /* BENTO GRID EXPERTISE */
        .bento-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); grid-template-rows: repeat(2, 200px);
            gap: 20px; margin-top: 60px;
        }
        .bento-item { 
            background: var(--card-bg); border: 1px solid var(--border); 
            border-radius: 30px; padding: 30px; transition: 0.4s;
            display: flex; flex-direction: column; justify-content: flex-end;
        }
        .bento-item:nth-child(1) { grid-column: span 2; grid-row: span 2; background: linear-gradient(45deg, rgba(250,204,21,0.05), transparent); }
        .bento-item:nth-child(2) { grid-column: span 2; }
        .bento-item:hover { border-color: var(--primary); background: rgba(255,255,255,0.05); }
        .bento-item i { color: var(--primary); font-size: 1.5rem; margin-bottom: 15px; }

        /* PROJECT CARDS - High End Dashboard feel */
        .project-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 30px; margin-top: 80px;
        }
        .project-card {
            position: relative; border-radius: 40px; overflow: hidden;
            aspect-ratio: 4/5; border: 1px solid var(--border);
        }
        .project-card img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s cubic-bezier(0.23, 1, 0.32, 1); }
        .project-overlay {
            position: absolute; bottom: 0; left: 0; width: 100%; height: 40%;
            background: linear-gradient(to top, var(--bg), transparent);
            padding: 40px; display: flex; flex-direction: column; justify-content: flex-end;
        }
        .project-card:hover img { transform: scale(1.1); }

        /* REVIEWS */
        .review-card {
            background: var(--card-bg); border-radius: 30px; padding: 40px;
            border-left: 4px solid var(--primary); margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .bento-grid { grid-template-columns: 1fr; grid-template-rows: auto; }
            .bento-item { grid-column: span 1 !important; grid-row: span 1 !important; }
            .hero h2 { font-size: 3rem; }
        }
    </style>
</head>
<body>

<div class="bg-glow"></div>

<nav>
    <h1>RENZ LOI OKIT</h1>
    <ul>
        <li><a href="#work">Work</a></li>
        <li><a href="#expertise">Services</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section class="hero">
    <div class="badge" style="border: 1px solid var(--primary); color: var(--primary); padding: 5px 15px; border-radius: 20px; display: inline-block; font-size: 0.7rem; font-weight: 800; margin-bottom: 20px; letter-spacing: 2px;">AVAILABLE FOR PROJECTS</div>
    <h2>Building the <span>Future</span> of Web.</h2>
    <p>Premium Full-Stack Architecture for modern businesses. I specialize in turning complex logic into seamless digital experiences.</p>
    <a href="#work" class="btn-main">VIEW MY WORKS <i class="fas fa-arrow-right"></i></a>
</section>

<section id="expertise">
    <div style="margin-bottom: 40px;">
        <h3 style="font-family: 'Plus Jakarta Sans'; font-size: 2.5rem;">Expertise</h3>
        <p style="color: var(--text-dim);">The intersection of performance and design.</p>
    </div>
    <div class="bento-grid">
        <div class="bento-item">
            <i class="fas fa-code"></i>
            <h4>Backend Systems</h4>
            <p style="font-size: 0.8rem; color: var(--text-dim);">Scalable PHP architectures with PDO security and high-efficiency MySQL indexing.</p>
        </div>
        <div class="bento-item">
            <i class="fas fa-paint-brush"></i>
            <h4>UI/UX Engineering</h4>
            <p style="font-size: 0.8rem; color: var(--text-dim);">Sophisticated interfaces built with modern CSS and JavaScript logic.</p>
        </div>
        <div class="bento-item">
            <i class="fas fa-rocket"></i>
            <h4>Performance</h4>
            <p style="font-size: 0.8rem; color: var(--text-dim);">Optimization that ensures 90+ Lighthouse scores.</p>
        </div>
        <div class="bento-item">
            <i class="fas fa-shield-halved"></i>
            <h4>DevOps</h4>
            <p style="font-size: 0.8rem; color: var(--text-dim);">Secure deployment and CI/CD automation.</p>
        </div>
    </div>
</section>

<section id="work">
    <h3 style="font-family: 'Plus Jakarta Sans'; font-size: 2.5rem; text-align: center;">Selected Work</h3>
    <div class="project-grid">
        <div class="project-card">
            <img src="p1.jpg" alt="Work">
            <div class="project-overlay">
                <span style="font-size: 0.6rem; font-weight: 800; color: var(--primary);">FINTECH</span>
                <h4>Global Asset Manager</h4>
            </div>
        </div>
        <div class="project-card">
            <img src="p2.jpg" alt="Work">
            <div class="project-overlay">
                <span style="font-size: 0.6rem; font-weight: 800; color: var(--primary);">E-COMMERCE</span>
                <h4>Luxury Storefront</h4>
            </div>
        </div>
        </div>
</section>

<section id="reviews">
    <h3 style="font-family: 'Plus Jakarta Sans'; font-size: 2.5rem; margin-bottom: 50px;">Trusted By</h3>
    <?php foreach ($reviews as $row): ?>
        <div class="review-card">
            <p style="font-size: 1.2rem; color: #fff; margin-bottom: 15px;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 40px; height: 40px; background: var(--primary); border-radius: 50%; display: grid; place-items: center; color: #000; font-weight: 800;">
                    <?php echo substr($row['name'], 0, 1); ?>
                </div>
                <div>
                    <h5 style="font-size: 0.9rem;"><?php echo htmlspecialchars($row['name']); ?></h5>
                    <p style="font-size: 0.7rem; color: var(--text-dim);"><?php echo htmlspecialchars($row['company']); ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<footer style="padding: 100px 5%; border-top: 1px solid var(--border); text-align: center;">
    <h2 style="font-size: 3rem; margin-bottom: 30px;">Let's Build Something <br>Great Together.</h2>
    <p style="color: var(--text-dim); margin-bottom: 40px;">Currently accepting new projects for 2026.</p>
    <a href="mailto:renzloiokit.dev@email.com" class="btn-main">HIRE ME NOW</a>
    <div style="margin-top: 60px; font-size: 0.7rem; color: var(--text-dim);">
        &copy; <?php echo date("Y"); ?> RENZ LOI OKIT. ALL RIGHTS RESERVED.
    </div>
</footer>

</body>
</html>
