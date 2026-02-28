<?php
include "config.php";

// 1. Pagination & Review Fetching
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
    <title>Renz Loi Okit | Full Stack Portfolio</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;800&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #facc15;
            --bg: #02040a;
            --card-bg: rgba(255, 255, 255, 0.02);
            --border: rgba(255, 255, 255, 0.07);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Inter', sans-serif; line-height: 1.6; overflow-x: hidden; }

        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 10% 10%, rgba(250, 204, 21, 0.04) 0%, transparent 35%),
                        radial-gradient(circle at 90% 90%, rgba(56, 189, 248, 0.04) 0%, transparent 35%);
            z-index: -1;
        }

        nav {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            width: 90%; max-width: 1000px; padding: 15px 30px;
            background: rgba(5, 8, 16, 0.6); backdrop-filter: blur(20px);
            border: 1px solid var(--border); border-radius: 100px;
            display: flex; justify-content: space-between; align-items: center; z-index: 1000;
        }
        nav h1 { font-family: 'Plus Jakarta Sans'; font-size: 1rem; color: var(--primary); font-weight: 800; }
        nav ul { list-style: none; display: flex; gap: 20px; }
        nav ul a { text-decoration: none; color: var(--text-dim); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }

        section { max-width: 1100px; margin: 0 auto; padding: 120px 5% 60px; position: relative; z-index: 2; }

        /* HERO */
        .hero { display: flex; align-items: center; gap: 40px; min-height: 70vh; }
        .hero-text h2 { font-family: 'Plus Jakarta Sans'; font-size: clamp(2rem, 5vw, 4rem); line-height: 1; font-weight: 800; }
        .hero-text span { color: var(--primary); }
        .hero-image img { width: 300px; height: 300px; border-radius: 30px; border: 1px solid var(--border); object-fit: cover; }

        /* RELATED WORKS (PROJECTS) */
        .project-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-top: 40px; }
        .project-card { 
            background: var(--card-bg); border: 1px solid var(--border); 
            border-radius: 25px; overflow: hidden; transition: 0.3s;
        }
        .project-card:hover { border-color: var(--primary); transform: translateY(-5px); }
        .project-thumbnail { width: 100%; height: 200px; background: #111; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--border); }
        .project-content { padding: 25px; }
        .project-tag { font-size: 0.6rem; color: var(--primary); font-weight: 800; text-transform: uppercase; }

        /* REVIEWS */
        .card { background: var(--card-bg); border: 1px solid var(--border); padding: 30px; border-radius: 25px; margin-bottom: 20px; }

        /* CONTACT FORM */
        .contact-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; }
        input, textarea { 
            width: 100%; padding: 15px; background: rgba(255,255,255,0.03); 
            border: 1px solid var(--border); color: white; border-radius: 12px; margin-bottom: 15px;
            font-family: inherit; outline: none;
        }
        .btn-submit { 
            width: 100%; padding: 18px; background: var(--primary); border: none; 
            border-radius: 12px; font-weight: 800; cursor: pointer; color: #000;
        }

        @media (max-width: 800px) { .hero, .contact-grid { flex-direction: column; text-align: center; } }
    </style>
</head>
<body>

<div class="mesh-bg"></div>

<nav>
    <h1>RENZ LOI.</h1>
    <ul>
        <li><a href="#hero">Intro</a></li>
        <li><a href="#projects">Work</a></li>
        <li><a href="#reviews">Reviews</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <p style="color: var(--primary); font-weight: 800; font-size: 0.7rem; letter-spacing: 2px;">FULL STACK DEVELOPER</p>
        <h2>Engineering <span>Modern</span><br>Digital Solutions</h2>
        <p style="color: var(--text-dim); margin-top: 20px; max-width: 500px;">Specializing in PHP backend architectures and high-performance user interfaces.</p>
    </div>
    <div class="hero-image"><img src="Renz.jpg" alt="Renz"></div>
</section>

<section id="projects">
    <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 2.2rem; margin-bottom: 30px;">Related <span>Works</span></h2>
    <div class="project-grid">
        <div class="project-card">
            <div class="project-thumbnail"><i class="fas fa-shopping-cart fa-3x" style="color: var(--border);"></i></div>
            <div class="project-content">
                <span class="project-tag">E-Commerce</span>
                <h4 style="margin: 10px 0;">Premium Store Platform</h4>
                <p style="font-size: 0.8rem; color: var(--text-dim);">A secure PHP-driven shop with real-time inventory management.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-thumbnail"><i class="fas fa-chart-line fa-3x" style="color: var(--border);"></i></div>
            <div class="project-content">
                <span class="project-tag">SaaS</span>
                <h4 style="margin: 10px 0;">Analytics Dashboard</h4>
                <p style="font-size: 0.8rem; color: var(--text-dim);">Complex data visualization and user management portal.</p>
            </div>
        </div>
        <div class="project-card">
            <div class="project-thumbnail"><i class="fas fa-database fa-3x" style="color: var(--border);"></i></div>
            <div class="project-content">
                <span class="project-tag">Backend</span>
                <h4 style="margin: 10px 0;">API Gateway</h4>
                <p style="font-size: 0.8rem; color: var(--text-dim);">Custom REST API structure built for high-traffic scalability.</p>
            </div>
        </div>
    </div>
</section>

<section id="reviews">
    <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 2.2rem; margin-bottom: 30px; text-align: center;">Client <span>Voices</span></h2>
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="card" style="border: 1px solid var(--primary); background: rgba(250, 204, 21, 0.02);">
            <h4 style="margin-bottom: 15px;">Add Review</h4>
            <form action="save_review.php" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="text" name="company" placeholder="Company" required>
                </div>
                <textarea name="review" rows="3" placeholder="Project feedback..." required></textarea>
                <button type="submit" class="btn-submit" style="padding: 12px;">POST REVIEW</button>
            </form>
        </div>

        <?php foreach ($reviews as $row): ?>
            <div class="card">
                <h4 style="color: var(--primary);"><?php echo htmlspecialchars($row['name']); ?></h4>
                <p style="font-size: 0.7rem; color: var(--text-dim); text-transform: uppercase;"><?php echo htmlspecialchars($row['company']); ?></p>
                <p style="margin-top: 10px; font-style: italic; color: #cbd5e1;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="contact">
    <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 2.2rem; margin-bottom: 40px; text-align: center;">Get In <span>Touch</span></h2>
    <div class="contact-grid">
        <div class="card">
            <form id="contactForm">
                <input type="text" name="contact_name" placeholder="Name" required>
                <input type="email" name="contact_email" placeholder="Email" required>
                <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
                <button type="submit" class="btn-submit">SEND MESSAGE</button>
            </form>
        </div>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="card" style="cursor: pointer;" onclick="navigator.clipboard.writeText('renzloiokit.dev@email.com'); alert('Email Copied!');">
                <p style="font-size: 0.6rem; color: var(--primary); font-weight: 800;">EMAIL ADDRESS</p>
                <strong>renzloiokit.dev@email.com</strong>
            </div>
            <a href="https://github.com/Raezaga" target="_blank" class="card" style="text-decoration:none; color:white;">GITHUB PROFILE</a>
        </div>
    </div>
</section>

<footer style="text-align: center; padding: 40px; color: var(--text-dim); font-size: 0.7rem; border-top: 1px solid var(--border);">
    &copy; <?php echo date("Y"); ?> RENZ LOI OKIT.
</footer>

<script>
    // THE FUNCTIONAL SCRIPT: No animations, just the data transfer.
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable button to prevent double clicks
        const btn = this.querySelector('button');
        btn.disabled = true;
        btn.innerText = 'SENDING...';

        fetch('send_message.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === 'success') {
                alert('Success! Your message has been sent.');
                this.reset();
            } else {
                alert('Server Response: ' + data);
            }
            btn.disabled = false;
            btn.innerText = 'SEND MESSAGE';
        })
        .catch(err => {
            alert('Connection failed. Please check your internet.');
            btn.disabled = false;
            btn.innerText = 'SEND MESSAGE';
        });
    });
</script>

</body>
</html>
