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
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Inter', sans-serif; overflow-x: hidden; }

        /* MESH BACKGROUND */
        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 10% 10%, rgba(250, 204, 21, 0.04) 0%, transparent 35%),
                        radial-gradient(circle at 90% 90%, rgba(56, 189, 248, 0.04) 0%, transparent 35%);
            z-index: -1;
        }

        #cursor-glow {
            position: fixed; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.05) 0%, transparent 70%);
            border-radius: 50%; pointer-events: none; z-index: 0; transform: translate(-50%, -50%);
        }

        /* NAVIGATION */
        nav {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            width: 90%; max-width: 1100px; padding: 15px 35px;
            background: rgba(2, 4, 10, 0.7); backdrop-filter: blur(15px);
            border: 1px solid var(--border); border-radius: 100px;
            display: flex; justify-content: space-between; align-items: center; z-index: 1000;
        }
        nav h1 { font-family: 'Plus Jakarta Sans'; font-size: 1rem; color: var(--primary); font-weight: 800; }
        nav ul { display: flex; gap: 30px; list-style: none; }
        nav ul a { text-decoration: none; color: var(--text-dim); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; transition: 0.3s; }
        nav ul a:hover { color: var(--primary); }

        section { max-width: 1200px; margin: 0 auto; padding: 120px 5% 60px; position: relative; z-index: 2; }

        /* HERO SECTION */
        .hero { display: flex; align-items: center; min-height: 85vh; gap: 50px; }
        .hero-content h2 { font-family: 'Plus Jakarta Sans'; font-size: clamp(2.5rem, 6vw, 4.5rem); line-height: 1; font-weight: 800; }
        .hero-content h2 span { -webkit-text-stroke: 1px var(--primary); color: transparent; }
        .hero-image img { width: 350px; height: 350px; border-radius: 30px; border: 1px solid var(--border); object-fit: cover; }

        /* REVIEWS */
        .review-card { background: var(--card-bg); border: 1px solid var(--border); padding: 30px; border-radius: 25px; margin-bottom: 20px; }
        .review-form { background: rgba(250, 204, 21, 0.02); border: 1px solid var(--primary); padding: 30px; border-radius: 25px; margin-bottom: 40px; }

        /* CONTACT FORM */
        .contact-container { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; }
        .contact-form-box { background: linear-gradient(145deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01)); padding: 40px; border-radius: 35px; border: 1px solid var(--border); }
        
        .input-group { margin-bottom: 20px; }
        .input-group label { font-size: 0.7rem; color: var(--primary); font-weight: 800; text-transform: uppercase; display: block; margin-bottom: 8px; }
        
        input, textarea { width: 100%; padding: 16px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 12px; color: white; font-family: inherit; transition: 0.3s; }
        input:focus, textarea:focus { border-color: var(--primary); outline: none; background: rgba(255,255,255,0.05); }

        .btn-submit { width: 100%; padding: 18px; background: var(--primary); border: none; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.4s; color: #000; }
        .btn-submit:hover { transform: scale(1.02); filter: brightness(1.1); }

        .social-card { background: var(--card-bg); border: 1px solid var(--border); padding: 25px; border-radius: 25px; text-decoration: none; color: white; display: flex; align-items: center; gap: 15px; transition: 0.3s; }
        .social-card:hover { border-color: var(--primary); transform: translateX(10px); }

        @media (max-width: 900px) { .hero, .contact-container { grid-template-columns: 1fr; flex-direction: column; text-align: center; } .hero-image img { width: 280px; height: 280px; } }
    </style>
</head>
<body>

<div id="cursor-glow"></div>
<div class="mesh-bg"></div>

<nav>
    <h1>RENZ LOI.</h1>
    <ul>
        <li><a href="#hero">Home</a></li>
        <li><a href="#reviews">Reviews</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-content">
        <p style="color: var(--primary); font-weight: 800; font-size: 0.7rem; letter-spacing: 2px;">FULL STACK DEVELOPER</p>
        <h2>Building <span>Modern</span><br>Digital Solutions</h2>
        <p style="color: var(--text-dim); margin: 20px 0 40px; max-width: 500px;">Crafting high-performance web applications with clean code and intuitive user interfaces.</p>
        <a href="My_CV.pdf" download style="text-decoration: none;" class="btn-submit">DOWNLOAD MY CV</a>
    </div>
    <div class="hero-image">
        <img src="Renz.jpg" alt="Renz">
    </div>
</section>

<section id="reviews">
    <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 2.5rem; margin-bottom: 40px; text-align: center;">Client <span>Feedback</span></h2>
    <div style="max-width: 800px; margin: 0 auto;">
        
        <div class="review-form">
            <h4 style="margin-bottom: 20px;">Submit a Review</h4>
            <form action="save_review.php" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <input type="text" name="name" placeholder="Full Name" required>
                    <input type="text" name="company" placeholder="Company Name" required>
                </div>
                <textarea name="review" rows="3" placeholder="How was the project experience?" style="margin-top: 15px;" required></textarea>
                <button type="submit" class="btn-submit" style="margin-top: 15px; padding: 12px;">POST REVIEW</button>
            </form>
        </div>

        <?php foreach ($reviews as $row): ?>
            <div class="review-card">
                <h4 style="color: var(--primary);"><?php echo htmlspecialchars($row['name']); ?></h4>
                <p style="font-size: 0.7rem; color: var(--text-dim); text-transform: uppercase;"><?php echo htmlspecialchars($row['company']); ?></p>
                <p style="margin-top: 10px; font-style: italic;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="contact">
    <div style="text-align: center; margin-bottom: 50px;">
        <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 3rem;">Get In <span>Touch.</span></h2>
    </div>

    <div class="contact-container">
        <div class="contact-form-box">
            <form id="contactForm">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="input-group">
                        <label>Your Name</label>
                        <input type="text" name="contact_name" placeholder="John Doe" required>
                    </div>
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="contact_email" placeholder="john@example.com" required>
                    </div>
                </div>
                <div class="input-group">
                    <label>Message</label>
                    <textarea name="message" rows="6" placeholder="Tell me about your project..." required></textarea>
                </div>
                <button type="submit" id="submitBtn" class="btn-submit">
                    <span id="btnText">SEND MESSAGE</span>
                </button>
            </form>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="social-card" style="cursor: pointer; display: block;" onclick="copyEmail()">
                <p style="font-size: 0.6rem; color: var(--primary); font-weight: 800;">DIRECT EMAIL</p>
                <p id="emailAddr" style="font-size: 1.1rem; margin: 5px 0;">renzloiokit.dev@email.com</p>
                <p id="copyMsg" style="font-size: 0.6rem; color: var(--primary); opacity: 0;">Click to copy address</p>
            </div>
            
            <a href="https://github.com/Raezaga" target="_blank" class="social-card">
                <i class="fab fa-github" style="font-size: 1.5rem;"></i>
                <div>
                    <p style="font-size: 0.6rem; color: var(--primary); font-weight: 800;">FOLLOW ON</p>
                    <p>GitHub Profile</p>
                </div>
            </a>

            <a href="https://linkedin.com" target="_blank" class="social-card">
                <i class="fab fa-linkedin" style="font-size: 1.5rem;"></i>
                <div>
                    <p style="font-size: 0.6rem; color: var(--primary); font-weight: 800;">CONNECT ON</p>
                    <p>LinkedIn</p>
                </div>
            </a>
        </div>
    </div>
</section>

<footer style="text-align: center; padding: 60px; color: var(--text-dim); font-size: 0.7rem; border-top: 1px solid var(--border);">
    &copy; <?php echo date("Y"); ?> RENZ LOI OKIT.
</footer>

<script>
    // Cursor Tracking
    const glow = document.getElementById('cursor-glow');
    document.addEventListener('mousemove', (e) => {
        glow.style.left = e.clientX + 'px';
        glow.style.top = e.clientY + 'px';
    });

    // Copy Email
    function copyEmail() {
        navigator.clipboard.writeText("renzloiokit.dev@email.com");
        const msg = document.getElementById('copyMsg');
        msg.style.opacity = "1";
        msg.innerText = "COPIED TO CLIPBOARD!";
        setTimeout(() => { msg.style.opacity = "0"; }, 2000);
    }

    // --- WORKING AJAX LOGIC ---
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        btnText.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> SENDING...';
        submitBtn.style.pointerEvents = 'none';
        submitBtn.style.opacity = '0.7';

        const formData = new FormData(this);

        fetch('send_message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            const result = data.trim();
            if (result === 'success') {
                submitBtn.style.background = '#22c55e';
                btnText.innerHTML = '<i class="fas fa-check"></i> MESSAGE SENT';
                contactForm.reset();
            } else {
                submitBtn.style.background = '#ef4444';
                btnText.innerHTML = '<i class="fas fa-times"></i> ERROR OCCURRED';
                console.log("Server Response:", result);
            }
            
            setTimeout(() => {
                submitBtn.style.background = '';
                submitBtn.style.opacity = '1';
                submitBtn.style.pointerEvents = 'auto';
                btnText.innerHTML = 'SEND MESSAGE';
            }, 4000);
        })
        .catch(() => {
            btnText.innerHTML = 'CONNECTION FAILED';
            submitBtn.style.background = '#ef4444';
        });
    });
</script>

</body>
</html>
