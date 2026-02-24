<?php
include "config.php";

// Pagination Logic
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
    <title>Renz Okit | Creative Developer</title>
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

        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 15% 15%, rgba(250, 204, 21, 0.08) 0%, transparent 45%),
                        radial-gradient(circle at 85% 85%, rgba(56, 189, 248, 0.08) 0%, transparent 45%);
            z-index: -1;
        }

        #cursor-glow {
            position: fixed; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.05) 0%, transparent 70%);
            border-radius: 50%; pointer-events: none; z-index: 0;
            transform: translate(-50%, -50%); transition: 0.1s ease;
        }

        nav {
            display: flex; justify-content: space-between; align-items: center; padding: 25px 8%;
            background: rgba(5, 8, 16, 0.8); backdrop-filter: blur(15px);
            position: fixed; top: 0; width: 100%; z-index: 1000; border-bottom: 1px solid var(--glass-border);
        }
        nav h1 { font-size: 24px; font-weight: 700; color: var(--primary); letter-spacing: -1px; }
        nav ul { list-style: none; display: flex; gap: 35px; }
        nav ul li a { text-decoration: none; color: #94a3b8; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; transition: 0.3s; }
        nav ul li a:hover { color: var(--primary); }

        section { min-height: 100vh; padding: 140px 8% 100px; position: relative; z-index: 2; }

        /* HERO SECTION */
        .hero { display: flex; align-items: center; gap: 60px; }
        .hero-text h2 { font-size: 5.5rem; line-height: 0.9; margin-bottom: 25px; font-weight: 700; }
        .hero-text span { -webkit-text-stroke: 1.5px var(--primary); color: transparent; }
        
        .hero-image img { width: 420px; height: 420px; border-radius: 40px; object-fit: cover; transform: rotate(2deg); transition: 0.6s cubic-bezier(0.23, 1, 0.32, 1); border: 1px solid var(--glass-border); }
        .hero-image:hover img { transform: rotate(0deg) scale(1.02); }

        /* SKILLS GRID */
        .skill-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-top: 40px; }
        .skill-card {
            background: var(--glass); border: 1px solid var(--glass-border);
            padding: 25px; border-radius: 20px; text-decoration: none;
            transition: 0.4s; text-align: center; display: block;
        }
        .skill-card span { color: #fff; font-weight: 500; font-size: 0.9rem; }
        .skill-card:hover { background: rgba(250, 204, 21, 0.1); border-color: var(--primary); transform: translateY(-8px); }

        /* PROJECT GRID - IMPROVED */
        .project-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-top: 50px; }
        .project-card {
            position: relative; overflow: hidden; border-radius: 30px;
            background: var(--glass); border: 1px solid var(--glass-border);
            height: 350px; transition: 0.5s;
        }
        .project-img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s; filter: brightness(0.7); }
        .project-overlay {
            position: absolute; inset: 0; padding: 30px;
            background: linear-gradient(to top, rgba(5, 8, 16, 0.95) 30%, transparent);
            display: flex; flex-direction: column; justify-content: flex-end;
            transform: translateY(20px); opacity: 0; transition: 0.4s;
        }
        .project-card:hover .project-overlay { transform: translateY(0); opacity: 1; }
        .project-card:hover .project-img { transform: scale(1.05); filter: brightness(0.3); }

        /* REVIEWS & BUTTONS */
        .review-box { background: var(--glass); border: 1px solid var(--glass-border); padding: 40px; border-radius: 30px; margin-bottom: 20px; }
        .btn-premium {
            padding: 18px 45px; background: var(--primary); color: #000;
            border-radius: 15px; font-weight: 700; text-decoration: none;
            display: inline-flex; align-items: center; gap: 12px; transition: 0.4s;
            box-shadow: 0 10px 20px rgba(250,204,21,0.2);
        }
        .btn-premium:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(250,204,21,0.4); }

        @media(max-width: 1000px) {
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
    <h1>OKIT.</h1>
    <ul>
        <li><a href="#hero">Intro</a></li>
        <li><a href="#resume">Expertise</a></li>
        <li><a href="#projects">Work</a></li>
        <li><a href="#reviews">Clients</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <h2>Engineering <span>Scalable</span> Web Systems</h2>
        <p>I am Renz Okit, a Full Stack Developer specializing in high-performance PHP architectures and sophisticated UI/UX design.</p>
        <div style="margin-top: 40px;">
            <a href="My_CV.pdf" download class="btn-premium">
                <i class="fas fa-download"></i> DOWNLOAD RESUME
            </a>
        </div>
    </div>
    <div class="hero-image">
        <img src="Renz.jpg" alt="Renz Okit">
    </div>
</section>

<section id="resume">
    <h2 style="font-size: 3rem; margin-bottom: 20px;">Technical <span>Philosophy</span></h2>
    <div class="skill-grid">
        <a href="skill_details.php?skill=php" class="skill-card"><span>PHP (PDO)</span></a>
        <a href="skill_details.php?skill=mysql" class="skill-card"><span>MySQL</span></a>
        <a href="skill_details.php?skill=js" class="skill-card"><span>JavaScript</span></a>
        <a href="skill_details.php?skill=tailwind" class="skill-card"><span>Tailwind CSS</span></a>
        <a href="skill_details.php?skill=responsive" class="skill-card"><span>Responsive</span></a>
        <a href="skill_details.php?skill=rest" class="skill-card"><span>REST APIs</span></a>
    </div>
</section>

<section id="projects">
    <h2 style="font-size: 3rem; margin-bottom: 40px;">Selected <span>Works</span></h2>
    <div class="project-grid">
        <div class="project-card">
            <div class="project-img" style="background: url('project1.jpg') center/cover;"></div>
            <div class="project-overlay">
                <p style="color: var(--primary); font-weight: bold; font-size: 0.7rem; letter-spacing: 2px;">LOGISTICS</p>
                <h3>Enterprise Inventory</h3>
                <p style="color: #94a3b8; font-size: 0.85rem; margin-top: 10px;">End-to-end warehouse management with real-time stock monitoring.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background: url('project2.jpg') center/cover;"></div>
            <div class="project-overlay">
                <p style="color: var(--accent-blue); font-weight: bold; font-size: 0.7rem; letter-spacing: 2px;">FINTECH</p>
                <h3>Crypto Analytics</h3>
                <p style="color: #94a3b8; font-size: 0.85rem; margin-top: 10px;">Market data visualization platform utilizing secure REST API integrations.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background: url('project3.jpg') center/cover;"></div>
            <div class="project-overlay">
                <p style="color: #fb7185; font-weight: bold; font-size: 0.7rem; letter-spacing: 2px;">E-COMMERCE</p>
                <h3>Custom Storefront</h3>
                <p style="color: #94a3b8; font-size: 0.85rem; margin-top: 10px;">A tailor-made shopping experience with a focus on conversion and speed.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-img" style="background: url('project4.jpg') center/cover;"></div>
            <div class="project-overlay">
                <p style="color: #a78bfa; font-weight: bold; font-size: 0.7rem; letter-spacing: 2px;">SAAS</p>
                <h3>CRM Portal</h3>
                <p style="color: #94a3b8; font-size: 0.85rem; margin-top: 10px;">A high-performance portal designed for streamlined customer relationship management.</p>
            </div>
        </div>
    </div>
</section>

<section id="reviews">
    <h2 style="font-size: 3rem; margin-bottom: 40px; text-align: center;">Client <span>Voices</span></h2>
    <div style="max-width: 850px; margin: 0 auto;">
        <div class="review-box" style="border-color: var(--primary);">
            <form action="save_review.php" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <input type="text" name="name" placeholder="Name" required style="width:100%; padding:15px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); color:white; border-radius:12px;">
                    <input type="text" name="company" placeholder="Business" required style="width:100%; padding:15px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); color:white; border-radius:12px;">
                </div>
                <textarea name="review" rows="3" placeholder="Share your experience..." required style="width:100%; padding:15px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); color:white; border-radius:12px; margin-bottom: 20px;"></textarea>
                <button type="submit" style="width:100%; padding:18px; background:var(--primary); border:none; border-radius:12px; font-weight:bold; cursor:pointer;">SUBMIT FEEDBACK</button>
            </form>
        </div>
        <?php foreach ($reviews as $row): ?>
            <div class="review-box">
                <h4 style="color: var(--primary); font-size: 1.2rem;"><?php echo htmlspecialchars($row['name']); ?></h4>
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 15px;"><?php echo htmlspecialchars($row['company']); ?></p>
                <p style="color: #cbd5e1; line-height: 1.6; font-style: italic;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="contact" style="text-align: center; min-height: 70vh;">
    <h2 style="font-size: 4rem; margin-bottom: 20px;">Let's <span>Connect.</span></h2>
    <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 50px; border-radius: 40px; max-width: 600px; margin: 40px auto;">
        <h3 style="font-size: 1.8rem; color: #fff; margin-bottom: 30px;">renzokit.dev@email.com</h3>
        <div style="display: flex; justify-content: center; gap: 20px;">
            <a href="https://github.com/Raezaga" target="_blank" style="color:#fff; font-size: 24px;"><i class="fab fa-github"></i></a>
            <a href="https://www.facebook.com/Raezaga/" target="_blank" style="color:#fff; font-size: 24px;"><i class="fab fa-facebook"></i></a>
            <a href="https://www.linkedin.com/in/renz-loi-okit-13397b393/" target="_blank" style="color:#fff; font-size: 24px;"><i class="fab fa-linkedin"></i></a>
        </div>
    </div>
</section>

<footer style="text-align: center; padding: 50px; color: #475569; font-size: 0.8rem; border-top: 1px solid var(--glass-border);">
    &copy; <?php echo date("Y"); ?> RENZ OKIT. BUILT WITH PHP & PRECISION.
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
