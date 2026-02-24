<?php
// 1. Database Connection - Ensure config.php exists and has $pdo
include "config.php";

// 2. Pagination Logic for Reviews
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
    <title>Renz Okit | Portfolio</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        /* BASE RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        html { scroll-behavior: smooth; }
        body { background: #0b1220; color: white; overflow-x: hidden; line-height: 1.6; }

        /* FIXED NAVIGATION */
        nav {
            display: flex; justify-content: space-between; align-items: center; padding: 20px 8%;
            background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px);
            position: fixed; top: 0; width: 100%; z-index: 1000; border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        nav h1 { font-size: 22px; letter-spacing: 2px; color: #facc15; }
        nav ul { list-style: none; display: flex; gap: 30px; }
        nav ul li a { text-decoration: none; color: #cbd5e1; transition: .3s; font-weight: 500; }
        nav ul li a:hover { color: #facc15; }

        /* SECTION ARCHITECTURE */
        section { min-height: 100vh; padding: 120px 8% 80px; display: flex; flex-direction: column; justify-content: center; }
        section:nth-child(even) { background: #0f172a; }
        section:nth-child(odd) { background: #0b1220; }

        .section-title { font-size: 3rem; margin-bottom: 50px; text-align: center; color: #facc15; }
        .section-title::after { content: ''; display: block; width: 50px; height: 4px; background: #facc15; margin: 10px auto; border-radius: 2px; }

        /* HERO SECTION */
        .hero { display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 40px; }
        .hero-text h2 { font-size: 4rem; line-height: 1.1; }
        .hero-text span { color: #facc15; }
        .hero-text p { font-size: 1.2rem; color: #94a3b8; margin-top: 20px; }
        .hero-image img { width: 380px; height: 380px; border-radius: 50%; object-fit: cover; border: 8px solid #facc15; box-shadow: 0 0 50px rgba(250,204,21,0.2); transition: 0.4s; }

        /* RESUME & CLICKABLE SKILL TAGS */
        .resume-container { display: flex; gap: 50px; align-items: center; max-width: 1000px; margin: 0 auto; }
        .resume-text { flex: 1; }
        .skill-tags { display: flex; flex-wrap: wrap; gap: 12px; margin: 25px 0 35px; }
        
        .skill-tags a { 
            text-decoration: none; 
            transition: transform 0.3s ease;
        }

        .skill-tags span { 
            background: rgba(250, 204, 21, 0.1); 
            color: #facc15; 
            padding: 10px 22px; 
            border-radius: 25px; 
            font-size: 0.9rem; 
            border: 1px solid rgba(250, 204, 21, 0.2); 
            display: inline-block;
            transition: all 0.3s ease;
        }

        /* Hover effect for clickable skills */
        .skill-tags a:hover span {
            background: #facc15;
            color: #0f172a;
            border-color: #facc15;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(250, 204, 21, 0.3);
        }
        
        .btn-download {
            display: inline-flex; align-items: center; gap: 12px;
            background: #facc15; color: #0f172a; padding: 18px 40px;
            border-radius: 50px; text-decoration: none; font-weight: bold;
            transition: 0.3s; box-shadow: 0 10px 20px rgba(250,204,21,0.15);
        }
        .btn-download:hover { transform: translateY(-5px); background: #eab308; }

        /* SERVICES CARDS */
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .card { background: rgba(30, 41, 59, 0.4); padding: 50px 30px; border-radius: 20px; text-align: center; border: 1px solid rgba(255,255,255,0.03); transition: 0.4s; }
        .card:hover { transform: translateY(-10px); border-color: #facc15; }
        .card i { color: #facc15; font-size: 3rem; margin-bottom: 25px; }

        /* REVIEWS FORM & LIST */
        .review-wrapper { max-width: 800px; margin: 0 auto; width: 100%; }
        .review-form-box { background: rgba(30, 41, 59, 0.3); padding: 35px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 50px; }
        .review-form-box input, .review-form-box textarea { 
            width: 100%; padding: 15px; margin-bottom: 15px; background: #0b1220; 
            border: 1px solid #334155; color: white; border-radius: 10px; outline: none; 
        }
        .review-form-box button { width: 100%; padding: 16px; background: #facc15; border: none; font-weight: bold; border-radius: 30px; cursor: pointer; transition: 0.3s; }
        
        .review-item { background: rgba(30, 41, 59, 0.5); padding: 25px; border-radius: 15px; border-left: 5px solid #facc15; margin-bottom: 20px; }
        
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 35px; }
        .pagination a { padding: 10px 20px; background: #1e293b; color: white; text-decoration: none; border-radius: 8px; transition: 0.3s; }
        .pagination a.active { background: #facc15; color: #0f172a; font-weight: bold; }

        /* CONTACT & SOCIALS */
        .socials { display: flex; justify-content: center; gap: 25px; margin-top: 40px; }
        .socials a { 
            font-size: 30px; color: #cbd5e1; background: #1e293b; width: 75px; height: 75px; 
            display: flex; align-items: center; justify-content: center; border-radius: 50%; 
            transition: 0.4s; border: 1px solid rgba(255,255,255,0.05); 
        }
        .socials a:hover { background: #facc15; color: #0f172a; transform: translateY(-10px); box-shadow: 0 10px 20px rgba(250,204,21,0.2); }

        /* FOOTER */
        footer { text-align: center; padding: 40px; border-top: 1px solid #1e293b; color: #64748b; font-size: 0.9rem; }

        @media(max-width: 900px) {
            .hero, .resume-container { flex-direction: column; text-align: center; }
            .hero-text h2 { font-size: 3rem; }
            nav ul { display: none; }
        }
    </style>
</head>
<body>

<nav>
    <h1>RENZ.</h1>
    <ul>
        <li><a href="#hero">Home</a></li>
        <li><a href="#resume">Resume</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#reviews">Reviews</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <h2>Hello, I'm <span>Renz Okit</span></h2>
        <p>Full Stack Developer focused on building modern, efficient, and visually appealing systems.</p>
    </div>
    <div class="hero-image">
        <img src="Renz.jpg" alt="Renz Okit">
    </div>
</section>

<section id="resume">
    <h2 class="section-title">Resume & Skills</h2>
    <div class="resume-container">
        <div class="resume-text">
            <h3>Interactive Tech Stack</h3>
            <p style="margin-top: 15px; color: #94a3b8;">
                Below are the core technologies I work with. Click on each tag to see a detailed breakdown of how I apply them in my projects.
            </p>
            
            <div class="skill-tags">
                <a href="skill_details.php?skill=php"><span>PHP (PDO)</span></a>
                <a href="skill_details.php?skill=mysql"><span>MySQL</span></a>
                <a href="skill_details.php?skill=js"><span>JavaScript</span></a>
                <a href="skill_details.php?skill=tailwind"><span>Tailwind CSS</span></a>
                <a href="skill_details.php?skill=responsive"><span>Responsive Design</span></a>
                <a href="skill_details.php?skill=rest"><span>REST APIs</span></a>
            </div>

            <a href="My_CV.pdf" download class="btn-download">
                <i class="fas fa-file-pdf"></i> Download My CV
            </a>
        </div>
        <div class="resume-icon" style="font-size: 180px; color: rgba(250, 204, 21, 0.05);">
            <i class="fas fa-laptop-code"></i>
        </div>
    </div>
</section>

<section id="services">
    <h2 class="section-title">What I Do</h2>
    <div class="cards">
        <div class="card"><i class="fas fa-code"></i><h3>Web Apps</h3><p>Custom sites built with high efficiency.</p></div>
        <div class="card"><i class="fas fa-database"></i><h3>Systems</h3><p>Secure database architecture management.</p></div>
        <div class="card"><i class="fas fa-mobile-alt"></i><h3>UI/UX Focus</h3><p>Perfect experience on every device.</p></div>
    </div>
</section>

<section id="reviews">
    <h2 class="section-title">Client Feedback</h2>
    <div class="review-wrapper">
        <div class="review-form-box">
            <form action="save_review.php" method="POST">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="text" name="company" placeholder="Business Name" required>
                <textarea name="review" rows="3" placeholder="How was your experience?" required></textarea>
                <button type="submit">Submit Feedback</button>
            </form>
        </div>

        <div class="review-list">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $row): ?>
                    <div class="review-item">
                        <h4><?php echo htmlspecialchars($row['name']); ?> <small style="color:#94a3b8; font-weight:normal;">from <?php echo htmlspecialchars($row['company']); ?></small></h4>
                        <p>"<?php echo htmlspecialchars($row['review']); ?>"</p>
                    </div>
                <?php endforeach; ?>

                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>#reviews" class="<?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php else: ?>
                <p style="text-align:center; color:#64748b;">No reviews yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="contact">
    <div style="text-align: center;">
        <h2 class="section-title">Let's Connect</h2>
        <div class="socials">
            <a href="https://github.com/Raezaga" target="_blank"><i class="fab fa-github"></i></a>
            <a href="https://www.facebook.com/Raezaga/" target="_blank"><i class="fab fa-facebook"></i></a>
            <a href="https://www.linkedin.com/in/renz-loi-okit-13397b393/" target="_blank"><i class="fab fa-linkedin"></i></a>
        </div>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> Renz Okit. All Rights Reserved.
</footer>

</body>
</html>
