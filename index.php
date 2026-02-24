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
        h1, h2, h3, .hero-text h2 { font-family: 'Space Grotesk', sans-serif; }

        /* MESH GRADIENT BACKGROUND */
        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 10% 20%, rgba(250, 204, 21, 0.05) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(56, 189, 248, 0.05) 0%, transparent 40%);
            z-index: -1;
        }

        /* NAVBAR */
        nav {
            display: flex; justify-content: space-between; align-items: center; padding: 20px 8%;
            background: rgba(5, 8, 16, 0.8); backdrop-filter: blur(15px);
            position: fixed; top: 0; width: 100%; z-index: 1000; border-bottom: 1px solid var(--glass-border);
        }
        nav h1 { font-size: 24px; font-weight: 700; color: var(--primary); letter-spacing: -1px; }
        nav ul { list-style: none; display: flex; gap: 30px; }
        nav ul li a { text-decoration: none; color: #94a3b8; font-size: 0.9rem; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; }
        nav ul li a:hover { color: var(--primary); }

        section { min-height: 100vh; padding: 120px 8% 80px; position: relative; }

        /* HERO */
        .hero { display: flex; align-items: center; gap: 60px; }
        .hero-text h2 { font-size: 5rem; line-height: 1; margin-bottom: 20px; }
        .hero-text span { -webkit-text-stroke: 1px var(--primary); color: transparent; }
        .hero-text p { font-size: 1.2rem; color: #94a3b8; max-width: 500px; }
        .hero-image { position: relative; }
        .hero-image img { width: 400px; height: 400px; border-radius: 30px; object-fit: cover; transform: rotate(3deg); transition: 0.5s; }
        .hero-image::after { content: ''; position: absolute; inset: 0; border: 2px solid var(--primary); border-radius: 30px; transform: rotate(-6deg); z-index: -1; transition: 0.5s; }
        .hero-image:hover img, .hero-image:hover::after { transform: rotate(0deg); }

        /* SKILL TAGS (The Highlight) */
        .skill-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-top: 40px; }
        .skill-card {
            background: var(--glass); border: 1px solid var(--glass-border);
            padding: 20px; border-radius: 15px; text-decoration: none;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center; display: block;
        }
        .skill-card span { color: #cbd5e1; font-weight: 500; font-size: 0.9rem; }
        .skill-card:hover { 
            background: rgba(250, 204, 21, 0.1); border-color: var(--primary); 
            transform: translateY(-10px); 
        }

        /* RESUME SECTION */
        .resume-box { 
            background: var(--glass); border: 1px solid var(--glass-border); 
            padding: 60px; border-radius: 40px; display: flex; align-items: center; gap: 40px; 
        }
        .btn-cv {
            padding: 15px 40px; background: var(--primary); color: #000;
            border-radius: 12px; font-weight: 700; text-decoration: none;
            display: inline-flex; align-items: center; gap: 10px; transition: 0.3s;
        }
        .btn-cv:hover { box-shadow: 0 0 30px rgba(250, 204, 21, 0.4); transform: translateY(-3px); }

        /* REVIEWS */
        .review-card {
            background: var(--glass); border: 1px solid var(--glass-border);
            padding: 30px; border-radius: 20px; margin-bottom: 20px;
            backdrop-filter: blur(5px);
        }
        .pagination a { color: var(--primary); border: 1px solid var(--glass-border); padding: 10px 18px; border-radius: 10px; text-decoration: none; margin: 0 5px; }
        .pagination a.active { background: var(--primary); color: #000; }

        /* FOOTER & SOCIALS */
        .social-link {
            width: 60px; height: 60px; background: var(--glass);
            border: 1px solid var(--glass-border); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 20px; text-decoration: none; transition: 0.3s;
        }
        .social-link:hover { border-color: var(--primary); color: var(--primary); transform: scale(1.1); }

        @media(max-width: 900px) {
            .hero, .resume-box { flex-direction: column; text-align: center; }
            .hero-text h2 { font-size: 3.5rem; }
            .hero-image img { width: 300px; height: 300px; }
            nav ul { display: none; }
        }
    </style>
</head>
<body>

<div class="mesh-bg"></div>

<nav>
    <h1>OKIT.</h1>
    <ul>
        <li><a href="#hero">Intro</a></li>
        <li><a href="#resume">Stack</a></li>
        <li><a href="#reviews">Feedback</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <p style="color: var(--primary); letter-spacing: 3px; font-weight: bold;">AVAILABLE FOR HIRE</p>
        <h2>I Build <span>Digital</span> Experiences</h2>
        <p>A Full Stack Developer crafting seamless code and futuristic interfaces from the Philippines.</p>
    </div>
    <div class="hero-image">
        <img src="Renz.jpg" alt="Renz Okit">
    </div>
</section>

<section id="resume">
    <div class="resume-box">
        <div style="flex: 1;">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px;">My Tech <span>Stack</span></h2>
            <p style="color: #94a3b8; margin-bottom: 30px;">I don't just use tools; I master them. Click a technology to see my technical philosophy behind it.</p>
            
            <div class="skill-grid">
                <a href="skill_details.php?skill=php" class="skill-card"><span>PHP / PDO</span></a>
                <a href="skill_details.php?skill=mysql" class="skill-card"><span>MySQL</span></a>
                <a href="skill_details.php?skill=js" class="skill-card"><span>JavaScript</span></a>
                <a href="skill_details.php?skill=tailwind" class="skill-card"><span>Tailwind</span></a>
                <a href="skill_details.php?skill=responsive" class="skill-card"><span>Responsive</span></a>
                <a href="skill_details.php?skill=rest" class="skill-card"><span>REST APIs</span></a>
            </div>

            <div style="margin-top: 50px;">
                <a href="My_CV.pdf" download class="btn-cv">
                    <i class="fas fa-file-download"></i> Get Resume
                </a>
            </div>
        </div>
    </div>
</section>

<section id="reviews">
    <h2 style="text-align: center; margin-bottom: 50px;">Client <span>Voice</span></h2>
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="review-card" style="background: rgba(250, 204, 21, 0.05); border-color: var(--primary);">
            <form action="save_review.php" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <input type="text" name="name" placeholder="Name" required style="padding: 12px; background: transparent; border: 1px solid var(--glass-border); color: white; border-radius: 8px;">
                    <input type="text" name="company" placeholder="Company" required style="padding: 12px; background: transparent; border: 1px solid var(--glass-border); color: white; border-radius: 8px;">
                </div>
                <textarea name="review" rows="3" placeholder="Leave a review..." required style="width: 100%; margin: 15px 0; padding: 12px; background: transparent; border: 1px solid var(--glass-border); color: white; border-radius: 8px;"></textarea>
                <button type="submit" style="width: 100%; padding: 12px; background: var(--primary); border: none; font-weight: bold; border-radius: 8px; cursor: pointer;">Post Feedback</button>
            </form>
        </div>

        <?php foreach ($reviews as $row): ?>
            <div class="review-card">
                <h4 style="color: var(--primary);"><?php echo htmlspecialchars($row['name']); ?></h4>
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 10px;"><?php echo htmlspecialchars($row['company']); ?></p>
                <p style="color: #cbd5e1; font-style: italic;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
            </div>
        <?php endforeach; ?>

        <div class="pagination" style="text-align: center; margin-top: 40px;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>#reviews" class="<?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>
</section>

<section id="contact" style="text-align: center; min-height: 60vh;">
    <h2 style="font-size: 3rem; margin-bottom: 20px;">Ready to <span>Build?</span></h2>
    <p style="color: #94a3b8; margin-bottom: 40px;">Currently taking on new projects. Let's make something iconic.</p>
    
    <div style="display: flex; justify-content: center; gap: 20px;">
        <a href="https://github.com/Raezaga" class="social-link"><i class="fab fa-github"></i></a>
        <a href="https://www.facebook.com/Raezaga/" class="social-link"><i class="fab fa-facebook"></i></a>
        <a href="https://www.linkedin.com/in/renz-loi-okit-13397b393/" class="social-link"><i class="fab fa-linkedin"></i></a>
    </div>
</section>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Renz Okit. Built with Precision.</p>
</footer>

</body>
</html>
