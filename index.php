<?php
// 1. Database Connection
include "config.php";

// 2. Pagination Logic (for the Reviews section)
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
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        /* Enable smooth scrolling for the whole page */
        html { scroll-behavior: smooth; }

        body { 
            background: #0b1220; 
            color: white; 
            overflow-x: hidden;
        }

        /* NAVIGATION - Fixed so it stays while scrolling */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 8%;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0; width: 100%;
            z-index: 1000;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        nav h1 { font-size: 22px; letter-spacing: 2px; color: #facc15; }
        nav ul { list-style: none; display: flex; gap: 40px; }
        nav ul li a { text-decoration: none; color: #cbd5e1; transition: .3s; font-weight: 500; cursor: pointer; }
        nav ul li a:hover { color: #facc15; }

        /* FULL PAGE SECTIONS */
        section { 
            min-height: 100vh; /* Each section takes full screen height */
            padding: 100px 8%; 
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center content vertically */
            border-bottom: 1px solid rgba(255,255,255,0.02);
        }

        /* Alternate backgrounds for visual depth */
        section:nth-child(even) { background: #0f172a; }
        section:nth-child(odd) { background: #0b1220; }

        .section-title { font-size: 3.5rem; margin-bottom: 50px; text-align: center; color: #facc15; }

        /* HERO STYLING */
        .hero { display: flex; flex-direction: row; align-items: center; justify-content: space-between; }
        .hero-text { max-width: 600px; }
        .hero-text h2 { font-size: 4rem; line-height: 1.1; margin-bottom: 20px; }
        .hero-text span { color: #facc15; }
        .hero-text p { color: #94a3b8; font-size: 1.2rem; }
        .hero-image img { 
            width: 400px; height: 400px; border-radius: 50%; object-fit: cover; 
            border: 8px solid #facc15; box-shadow: 0 0 50px rgba(250,204,21,0.3); 
        }

        /* SERVICES CARDS */
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .card { 
            background: rgba(30, 41, 59, 0.5); padding: 50px 30px; border-radius: 20px; 
            text-align: center; border: 1px solid rgba(255,255,255,0.05); transition: 0.4s;
        }
        .card:hover { transform: translateY(-10px); border-color: #facc15; }
        .card i { color: #facc15; font-size: 3rem; margin-bottom: 20px; }

        /* REVIEWS LIST & FORM */
        .review-container { max-width: 900px; margin: 0 auto; width: 100%; }
        .review-form-container { margin-bottom: 50px; background: rgba(15, 23, 42, 0.5); padding: 30px; border-radius: 15px; border: 1px solid #334155; }
        .review-form-container input, .review-form-container textarea { 
            width: 100%; padding: 15px; margin-bottom: 15px; border-radius: 10px; background: #0b1220; color: white; border: 1px solid #334155; 
        }
        .review-form-container button { width: 100%; padding: 15px; border-radius: 30px; background: #facc15; font-weight: bold; border: none; cursor: pointer; }
        
        .review-item { background: rgba(30, 41, 59, 0.5); padding: 25px; border-radius: 15px; border-left: 5px solid #facc15; margin-bottom: 20px; }
        
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
        .pagination a { padding: 10px 20px; background: #1e293b; color: white; text-decoration: none; border-radius: 8px; }
        .pagination a.active { background: #facc15; color: #0f172a; font-weight: bold; }

        /* CONTACT */
        .socials { display: flex; justify-content: center; gap: 30px; }
        .socials a { 
            font-size: 35px; color: #cbd5e1; background: #1e293b; width: 80px; height: 80px; 
            display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: 0.3s; 
        }
        .socials a:hover { background: #facc15; color: #0f172a; transform: scale(1.1); }

        footer { text-align: center; padding: 40px; border-top: 1px solid #1e293b; color: #64748b; }

        @media(max-width: 900px) {
            .hero { flex-direction: column; text-align: center; padding-top: 150px; }
            .hero-image img { width: 280px; height: 280px; margin-top: 30px; }
            .hero-text h2 { font-size: 2.5rem; }
            nav ul { display: none; }
        }
    </style>
</head>
<body>

<nav>
    <h1>RENZ.</h1>
    <ul>
        <li><a href="#hero">Home</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#reviews">Reviews</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <h2>Hello, I'm <span>Renz Okit</span></h2>
        <p>Full Stack Developer focused on building modern, efficient, and visually appealing systems.</p>
        <div style="margin-top: 30px;">
            <a href="#contact" style="background:#facc15; color:#0f172a; padding: 15px 35px; border-radius: 30px; text-decoration:none; font-weight:bold;">Let's Talk</a>
        </div>
    </div>
    <div class="hero-image">
        <img src="Renz.jpg" alt="Renz Okit">
    </div>
</section>

<section id="services">
    <h2 class="section-title">What I Do</h2>
    <div class="cards">
        <div class="card"><i class="fas fa-laptop-code"></i><h3>Web Apps</h3><p>Custom websites built with the latest frameworks.</p></div>
        <div class="card"><i class="fas fa-server"></i><h3>Backend Systems</h3><p>Robust database management and API design.</p></div>
        <div class="card"><i class="fas fa-mobile-alt"></i><h3>Responsive UI</h3><p>Interfaces that look great on any device screen.</p></div>
    </div>
</section>

<section id="reviews">
    <h2 class="section-title">Client Feedback</h2>
    <div class="review-container">
        <div class="review-form-container">
            <form action="save_review.php" method="POST">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="text" name="company" placeholder="Company Name" required>
                <textarea name="review" rows="3" placeholder="How was your experience?" required></textarea>
                <button type="submit">Post Review</button>
            </form>
        </div>

        <div class="review-list">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $row): ?>
                    <div class="review-item">
                        <h4><?php echo htmlspecialchars($row['name']); ?> <span style="font-weight:normal; font-size:0.9rem; color:#94a3b8;">(<?php echo htmlspecialchars($row['company']); ?>)</span></h4>
                        <p style="margin-top:10px; color:#cbd5e1; font-style: italic;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
                    </div>
                <?php endforeach; ?>

                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>#reviews" class="<?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="contact">
    <h2 class="section-title">Let's Connect</h2>
    <p style="text-align:center; margin-bottom: 40px; color:#94a3b8;">I am currently available for new projects and freelance opportunities.</p>
    <div class="socials">
        <a href="https://github.com/Raezaga" target="_blank"><i class="fab fa-github"></i></a>
        <a href="https://www.facebook.com/Raezaga/" target="_blank"><i class="fab fa-facebook"></i></a>
        <a href="https://www.linkedin.com/in/renz-loi-okit-13397b393/" target="_blank"><i class="fab fa-linkedin"></i></a>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> Renz Okit. All Rights Reserved.
</footer>

</body>
</html>
