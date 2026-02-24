<?php
include "config.php";

// Pagination Logic for Reviews
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
    <title>Renz Loi Okit | Portfolio</title>
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

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        html { scroll-behavior: smooth; }
        body { background: var(--bg-dark); color: #e2e8f0; overflow-x: hidden; }
        h1, h2, h3 { font-family: 'Space Grotesk', sans-serif; }

        /* MESH & CURSOR GLOW */
        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 15% 15%, rgba(250, 204, 21, 0.08) 0%, transparent 45%),
                        radial-gradient(circle at 85% 85%, rgba(56, 189, 248, 0.08) 0%, transparent 45%);
            z-index: -1;
        }

        #cursor-glow {
            position: fixed; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.06) 0%, transparent 70%);
            border-radius: 50%; pointer-events: none; z-index: 0;
            transform: translate(-50%, -50%); transition: 0.1s ease;
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

        section { padding: 120px 8% 80px; position: relative; z-index: 2; }

        /* HERO */
        .hero { display: flex; align-items: center; gap: 60px; min-height: 90vh; }
        .hero-text h2 { font-size: 5rem; line-height: 1; margin-bottom: 20px; }
        .hero-text h2 span { -webkit-text-stroke: 1px var(--primary); color: transparent; }
        .hero-image img { width: 400px; height: 400px; border-radius: 50px; object-fit: cover; border: 1px solid var(--glass-border); transform: rotate(3deg); transition: 0.5s; }
        .hero:hover .hero-image img { transform: rotate(0deg); }

        /* EXPERTISE */
        .expertise-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 40px; }
        .skill-card { background: var(--glass); border: 1px solid var(--glass-border); padding: 35px; border-radius: 25px; transition: 0.4s ease; }
        .skill-card i { font-size: 2rem; color: var(--primary); margin-bottom: 20px; display: block; }
        .skill-card h3 { margin-bottom: 10px; font-size: 1.4rem; }
        .skill-card p { font-size: 0.85rem; color: #94a3b8; line-height: 1.6; }
        .skill-card:hover { background: rgba(250, 204, 21, 0.05); border-color: var(--primary); transform: translateY(-8px); }

        /* PROJECT GRID */
        .project-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; margin-top: 50px; }
        .project-card {
            background: var(--glass); border: 1px solid var(--glass-border); border-radius: 24px;
            overflow: hidden; height: 420px; display: flex; flex-direction: column;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .project-img { 
            height: 60%; width: 100%; border-bottom: 1px solid var(--glass-border);
            background-size: cover; background-position: center; 
            /* Fallback background if images aren't found */
            background-color: #1a1a1a; 
        }
        .project-info { height: 40%; padding: 20px; background: rgba(15, 23, 42, 0.3); display: flex; flex-direction: column; justify-content: center; }
        .project-tag { font-size: 0.6rem; text-transform: uppercase; font-weight: 800; letter-spacing: 2px; margin-bottom: 8px; padding: 4px 10px; border-radius: 8px; width: fit-content; }
        .project-card:hover { transform: translateY(-12px); border-color: var(--primary); box-shadow: 0 20px 40px rgba(0,0,0,0.4); }

        .review-box { background: var(--glass); border: 1px solid var(--glass-border); padding: 30px; border-radius: 20px; margin-bottom: 20px; }
        input, textarea { width: 100%; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: white; border-radius: 12px; margin-bottom: 15px; }
        .btn-submit { width: 100%; padding: 15px; background: var(--primary); border: none; border-radius: 12px; font-weight: bold; cursor: pointer; transition: 0.3s; }

        @media (max-width: 900px) {
            .hero { flex-direction: column; text-align: center; }
            .hero-text h2 { font-size: 3.5rem; }
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
        <p>I am <strong>Renz Loi Okit</strong>, a Full Stack Developer specialized in high-performance PHP architectures and sophisticated UI design.</p>
        <div style="margin-top: 30px;">
            <a href="My_CV.pdf" class="btn-submit" style="text-decoration:none; display:inline-block; width:auto; padding: 15px 40px;">GET RESUME</a>
        </div>
    </div>
    <div class="hero-image">
        <img src="Renz.jpg" alt="Renz Loi Okit">
    </div>
</section>

<section id="expertise">
    <h2 style="font-size: 3rem; margin-bottom: 10px;">Technical <span>Philosophy</span></h2>
    <p style="color: #64748b; max-width: 700px;">Architecture over code. I build systems that scale.</p>
    <div class="expertise-grid">
        <div class="skill-card"><i class="fas fa-server"></i><h3>Backend</h3><p>PHP (PDO), MySQL, and Secure API design.</p></div>
        <div class="skill-card"><i class="fas fa-layer-group"></i><h3>Frontend</h3><p>ES6 JavaScript, Modern CSS, and Tailwind.</p></div>
        <div class="skill-card"><i class="fas fa-microchip"></i><h3>Systems</h3><p>Scalable schemas and database optimization.</p></div>
        <div class="skill-card"><i class="fas fa-terminal"></i><h3>Workflow</h3><p>Git, Command Line, and CI/CD pipelines.</p></div>
    </div>
</section>

<section id="projects">
    <h2 style="font-size: 3rem; margin-bottom: 40px;">Selected <span>Works</span></h2>
    <div class="project-grid">
        <div class="project-card">
            <div class="project-img" style="background-image: url('p1.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(250,204,21,0.1); color: var(--primary);">LOGISTICS</span>
                <h3>Enterprise Inventory</h3>
                <p>Full-stack warehouse management system.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p2.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(56,189,248,0.1); color: var(--accent-blue);">FINTECH</span>
                <h3>Market Analytics</h3>
                <p>Live data visualization for financial markets.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p3.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(251,113,133,0.1); color: #fb7185);">COMMERCE</span>
                <h3>Modern Storefront</h3>
                <p>High-conversion e-commerce shopping engine.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p4.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(167,139,250,0.1); color: #a78bfa);">SAAS</span>
                <h3>Client Portal</h3>
                <p>Sophisticated CRM for client relationship management.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p5.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(34,197,94,0.1); color: #4ade80);">BUSINESS</span>
                <h3>Retail POS</h3>
                <p>Fast Point of Sale with offline sync features.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p6.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(244,114,182,0.1); color: #f472b6);">REAL ESTATE</span>
                <h3>Property Finder</h3>
                <p>Geolocation portal for virtual property tours.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p7.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(45,212,191,0.1); color: #2dd4bf);">HEALTHCARE</span>
                <h3>Patient EMR</h3>
                <p>Secure Electronic Medical Records management.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background-image: url('p8.jpg');"></div>
            <div class="project-info">
                <span class="project-tag" style="background: rgba(251,146,60,0.1); color: #fb923c);">EDUCATION</span>
                <h3>LMS Platform</h3>
                <p>Interactive learning and course tracking engine.</p>
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
                <input type="text" name="company" placeholder="Company Name" required>
                <textarea name="review" rows="3" placeholder="Share your experience..." required></textarea>
                <button type="submit" class="btn-submit">POST REVIEW</button>
            </form>
        </div>
        <?php foreach ($reviews as $row): ?>
            <div class="review-box">
                <h4 style="color:var(--primary);"><?php echo htmlspecialchars($row['name']); ?></h4>
                <p style="font-size:0.7rem; color:#64748b;"><?php echo htmlspecialchars($row['company']); ?></p>
                <p style="margin-top:10px; color:#cbd5e1;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="contact" style="text-align: center;">
    <h2 style="font-size: 3rem; margin-bottom: 20px;">Let's <span>Connect.</span></h2>
    <div style="background: var(--glass); padding: 40px; border-radius: 30px; border: 1px solid var(--glass-border); max-width: 500px; margin: 0 auto;">
        <h3>renzloiokit.dev@email.com</h3>
        <div style="margin-top: 20px; font-size: 24px; display: flex; justify-content: center; gap: 20px;">
            <a href="https://github.com/Raezaga" style="color:white;"><i class="fab fa-github"></i></a>
            <a href="https://www.linkedin.com/in/renz-loi-okit-13397b393/" style="color:white;"><i class="fab fa-linkedin"></i></a>
        </div>
    </div>
</section>

<footer style="text-align: center; padding: 50px; color: #475569; font-size: 0.8rem;">
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
