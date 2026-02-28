<?php
include "config.php";

// 1. Pagination & Review Fetching Logic
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
    <title>Renz Loi Okit | Full Stack Developer</title>
    
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
        body { 
            background-color: var(--bg); 
            color: var(--text-main); 
            font-family: 'Inter', sans-serif; 
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* PREMIUM BACKGROUND */
        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(circle at 10% 10%, rgba(250, 204, 21, 0.04) 0%, transparent 35%),
                radial-gradient(circle at 90% 90%, rgba(56, 189, 248, 0.04) 0%, transparent 35%);
            z-index: -1;
        }

        #cursor-glow {
            position: fixed; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.04) 0%, transparent 70%);
            border-radius: 50%; pointer-events: none; z-index: 0;
            transform: translate(-50%, -50%); transition: 0.1s ease-out;
        }

        /* NAVIGATION */
        nav {
            position: fixed; top: 25px; left: 50%; transform: translateX(-50%);
            width: 90%; max-width: 1000px; padding: 12px 30px;
            background: rgba(5, 8, 16, 0.6); backdrop-filter: blur(20px);
            border: 1px solid var(--border); border-radius: 100px;
            display: flex; justify-content: space-between; align-items: center; z-index: 1000;
        }
        nav h1 { font-family: 'Plus Jakarta Sans'; font-size: 1.1rem; font-weight: 800; color: var(--primary); letter-spacing: -1px; }
        nav ul { list-style: none; display: flex; gap: 25px; }
        nav ul a { text-decoration: none; color: var(--text-dim); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; transition: 0.3s; }
        nav ul a:hover { color: var(--primary); }

        section { max-width: 1100px; margin: 0 auto; padding: 140px 6% 80px; position: relative; z-index: 2; }

        /* HERO */
        .hero { display: flex; align-items: center; gap: 40px; min-height: 80vh; }
        .hero-text h2 { 
            font-family: 'Plus Jakarta Sans'; font-size: clamp(2.5rem, 6vw, 4rem); 
            line-height: 0.95; margin-bottom: 25px; font-weight: 800;
        }
        .hero-text h2 span { color: var(--primary); }
        .hero-image img { 
            width: 100%; max-width: 350px; border-radius: 40px; 
            border: 1px solid var(--border); 
        }

        /* PROJECTS */
        .project-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
        .project-card { 
            background: var(--card-bg); border: 1px solid var(--border); 
            padding: 30px; border-radius: 30px; transition: 0.3s;
        }
        .project-card:hover { border-color: var(--primary); transform: translateY(-5px); }

        /* REVIEWS */
        .review-box { 
            background: var(--card-bg); border: 1px solid var(--border); 
            padding: 30px; border-radius: 25px; margin-bottom: 20px;
        }

        /* CONTACT FORM */
        .contact-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; }
        input, textarea { 
            width: 100%; padding: 18px; background: rgba(255,255,255,0.03); 
            border: 1px solid var(--border); color: white; border-radius: 15px; margin-bottom: 15px;
            font-family: inherit; outline: none; transition: 0.3s;
        }
        input:focus, textarea:focus { border-color: var(--primary); background: rgba(255,255,255,0.06); }
        
        .btn-submit { 
            width: 100%; padding: 20px; background: var(--primary); border: none; 
            border-radius: 15px; font-weight: 800; cursor: pointer; color: #000;
        }

        @media (max-width: 900px) {
            .hero, .contact-grid { flex-direction: column; grid-template-columns: 1fr; text-align: center; }
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
        <li><a href="#projects">Work</a></li>
        <li><a href="#reviews">Reviews</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <span style="color: var(--primary); font-weight: 800; font-size: 0.7rem; letter-spacing: 3px;">FULL STACK ENGINEER</span>
        <h2>Engineering <span>Scalable</span> Web Systems</h2>
        <p style="color: var(--text-dim); margin-bottom: 40px;">Crafting high-performance PHP architectures and sophisticated UI/UX design.</p>
        <a href="My_CV.pdf" download class="btn-submit" style="text-decoration:none; display:inline-block; width:auto; padding: 15px 40px;">DOWNLOAD CV</a>
    </div>
    <div class="hero-image"><img src="Renz.jpg" alt="Renz Loi Okit"></div>
</section>

<section id="projects">
    <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 2.5rem; margin-bottom: 40px;">Featured <span>Work</span></h2>
    <div class="project-grid">
        <div class="project-card">
            <h4 style="color: var(--primary);">E-Commerce System</h4>
            <p style="font-size: 0.85rem; color: var(--text-dim); margin-top: 10px;">Full-stack online store with secure checkout.</p>
        </div>
        <div class="project-card">
            <h4 style="color: var(--primary);">SaaS Dashboard</h4>
            <p style="font-size: 0.85rem; color: var(--text-dim); margin-top: 10px;">Real-time analytics and data management.</p>
        </div>
    </div>
</section>

<section id="reviews">
    <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 2.5rem; margin-bottom: 40px; text-align: center;">Client <span>Voices</span></h2>
    
    <div class="review-box" style="border-color: var(--primary);">
        <h4 style="margin-bottom: 20px;">Leave a Review</h4>
        <form action="save_review.php" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="company" placeholder="Company" required>
            </div>
            <textarea name="review" rows="3" placeholder="How was the experience?" required></textarea>
            <button type="submit" class="btn-submit">POST REVIEW</button>
        </form>
    </div>

    <div id="reviews-list">
        <?php foreach ($reviews as $row): ?>
            <div class="review-box">
                <h4 style="color: var(--primary);"><?php echo htmlspecialchars($row['name']); ?></h4>
                <p style="font-size: 0.75rem; color: var(--text-dim);"><?php echo htmlspecialchars($row['company']); ?></p>
                <p style="margin-top: 10px; font-style: italic;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="contact">
    <div style="text-align: center; margin-bottom: 60px;">
        <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 3rem;">Let's <span>Connect.</span></h2>
    </div>

    <div class="contact-grid">
        <div class="review-box">
            <form id="contactForm">
                <input type="text" name="contact_name" placeholder="Your Name" required>
                <input type="email" name="contact_email" placeholder="Your Email" required>
                <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
                <button type="submit" class="btn-submit">SEND MESSAGE</button>
            </form>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="review-box" style="cursor: pointer;" onclick="navigator.clipboard.writeText('renzloiokit.dev@email.com'); alert('Email copied!');">
                <p style="font-size: 0.7rem; color: var(--primary); font-weight: 800;">EMAIL</p>
                <strong>renzloiokit.dev@email.com</strong>
            </div>
            <a href="https://github.com/Raezaga" target="_blank" class="review-box" style="text-decoration:none; color:white;">GITHUB PROFILE</a>
        </div>
    </div>
</section>

<footer style="text-align: center; padding: 60px; color: var(--text-dim); font-size: 0.8rem; border-top: 1px solid var(--border);">
    &copy; <?php echo date("Y"); ?> RENZ LOI OKIT.
</footer>

<script>
    // Cursor Glow
    const glow = document.getElementById('cursor-glow');
    document.addEventListener('mousemove', (e) => {
        glow.style.left = e.clientX + 'px';
        glow.style.top = e.clientY + 'px';
    });

    // Simple AJAX Contact Form
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch('send_message.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === 'success') {
                alert('Success! Message sent.');
                this.reset();
            } else {
                alert('Server says: ' + data);
            }
        })
        .catch(err => alert('Network error. Check connection.'));
    });
</script>

</body>
</html>
