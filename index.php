<?php
// 1. Database Connection
include "config.php";

// 2. Fetch Data
try {
    $stmt = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $reviews = []; 
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, #0f172a, #0b1220);
            color: white;
            line-height: 1.6;
        }

        /* NAVIGATION */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 8%;
            position: sticky;
            top: 0;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            z-index: 1000;
        }

        nav h1 { font-size: 22px; letter-spacing: 2px; }
        nav ul { list-style: none; display: flex; gap: 40px; }
        nav ul li a { text-decoration: none; color: #cbd5e1; transition: .3s; }
        nav ul li a:hover { color: #facc15; }

        /* HERO SECTION */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 80px 8%;
            min-height: 90vh;
        }

        .hero-text { max-width: 550px; }
        .hero-text h2 { font-size: 60px; line-height: 1.1; }
        .hero-text span { color: #facc15; }
        .hero-text p { margin-top: 20px; color: #94a3b8; }

        .hero-image img {
            width: 400px;
            height: 400px;
            border-radius: 50%;
            object-fit: cover;
            border: 8px solid #facc15;
            box-shadow: 0 0 80px rgba(250, 204, 21, 0.3);
            transition: .4s ease;
        }
        .hero-image img:hover { transform: scale(1.05); }

        /* SECTIONS */
        section { padding: 100px 8%; }
        .section-title { font-size: 36px; margin-bottom: 50px; text-align: center; }

        /* SERVICES */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .card {
            background: rgba(30, 41, 59, 0.7);
            padding: 40px 30px;
            border-radius: 20px;
            transition: .4s;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .card:hover {
            transform: translateY(-12px);
            background: #facc15;
            color: #0f172a;
        }

        /* REVIEW FORM (Updated for PHP) */
        .review-form-container {
            max-width: 650px;
            margin: auto;
            background: rgba(30, 41, 59, 0.7);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 40px rgba(0,0,0,0.4);
        }

        .review-form-container input,
        .review-form-container textarea {
            width: 100%;
            padding: 14px;
            margin-bottom: 18px;
            border: 1px solid #334155;
            border-radius: 12px;
            background: #0f172a;
            color: white;
            font-size: 14px;
        }

        .review-form-container button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 30px;
            background: #facc15;
            color: #0f172a;
            font-weight: bold;
            cursor: pointer;
            transition: .3s;
        }

        .review-form-container button:hover { background: #eab308; }

        /* REVIEW LIST */
        .review-list {
            margin-top: 60px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            display: grid;
            gap: 25px;
        }

        .review-item {
            background: rgba(30, 41, 59, 0.8);
            padding: 25px;
            border-radius: 15px;
            border-left: 5px solid #facc15;
            animation: fadeIn .4s ease;
        }

        .review-item h4 { color: #facc15; margin-bottom: 5px; }
        .review-item .company { color: #94a3b8; font-size: 0.85rem; font-weight: normal; }
        .review-item .date { display: block; margin-top: 15px; font-size: 0.75rem; color: #64748b; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        footer { text-align: center; padding: 40px; border-top: 1px solid #1e293b; color: #64748b; }

        @media(max-width: 900px) {
            .hero { flex-direction: column; text-align: center; }
            .hero-image { margin-top: 40px; }
            .hero-image img { width: 280px; height: 280px; }
            .hero-text h2 { font-size: 40px; }
        }
    </style>
</head>
<body>

<nav>
    <h1>RENZ.</h1>
    <ul>
        <li><a href="#services">Services</a></li>
        <li><a href="#reviews">Reviews</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section class="hero">
    <div class="hero-text">
        <h2>Hello, I'm <span>Renz Okit</span></h2>
        <p>Full Stack Developer focused on building modern, efficient, and visually appealing systems.</p>
    </div>
    <div class="hero-image">
        <img src="Renz.jpg" alt="Renz Okit">
    </div>
</section>

<section id="services">
    <h2 class="section-title">What I Do</h2>
    <div class="cards">
        <div class="card">
            <i class="fas fa-code fa-2x" style="margin-bottom:15px;"></i>
            <h3>Web Development</h3>
            <p>Building responsive and scalable websites using modern technologies.</p>
        </div>
        <div class="card">
            <i class="fas fa-server fa-2x" style="margin-bottom:15px;"></i>
            <h3>System Development</h3>
            <p>Developing management systems with secure authentication and reporting.</p>
        </div>
        <div class="card">
            <i class="fas fa-paint-brush fa-2x" style="margin-bottom:15px;"></i>
            <h3>UI/UX Design</h3>
            <p>Designing clean, user-focused interfaces with smooth experience.</p>
        </div>
    </div>
</section>

<section id="reviews">
    <h2 class="section-title">Client Feedback</h2>

    <div class="review-form-container">
        <form action="save_review.php" method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="text" name="company" placeholder="Company Name" required>
            <textarea name="review" rows="4" placeholder="How was your experience?" required></textarea>
            <button type="submit">Post Review</button>
        </form>
    </div>

    <div class="review-list">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $row): ?>
                <div class="review-item">
                    <h4>
                        <?php echo htmlspecialchars($row['name']); ?> 
                        <span class="company">from <?php echo htmlspecialchars($row['company']); ?></span>
                    </h4>
                    <p style="margin-top:10px; color:#cbd5e1; font-style: italic;">
                        "<?php echo htmlspecialchars($row['review']); ?>"
                    </p>
                    <small class="date">
                        <i class="far fa-calendar-alt"></i> 
                        <?php echo date("F j, Y", strtotime($row['created_at'])); ?>
                    </small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center; color:#64748b;">No reviews yet. Be the first to leave one!</p>
        <?php endif; ?>
    </div>
</section>

<section id="contact" class="contact">
    <h2 class="section-title">Let's Connect</h2>
    <div class="socials">
        <a href="https://github.com/Raezaga"><i class="fab fa-github"></i></a>
        <a href="https://www.facebook.com/Raezaga/"><i class="fab fa-facebook"></i></a>
        <a href="https://www.linkedin.com/in/renz-loi-okit-13397b393/"><i class="fab fa-linkedin"></i></a>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> Renz Okit. All Rights Reserved.
</footer>

</body>
</html>