<?php
// 1. Database Connection
include "config.php";

// 2. Pagination Logic
$limit = 5; // Number of reviews per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    // Get total count for pagination links
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM reviews");
    $total_reviews = $total_stmt->fetchColumn();
    $total_pages = ceil($total_reviews / $limit);

    // Fetch only the 5 reviews for the current page
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
        /* ... (Keep all your previous CSS here) ... */

        /* ADD THIS NEW CSS FOR PAGINATION BUTTONS */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }

        .pagination a {
            padding: 8px 16px;
            background: rgba(30, 41, 59, 0.7);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: 0.3s;
        }

        .pagination a.active {
            background: #facc15;
            color: #0f172a;
            font-weight: bold;
            border-color: #facc15;
        }

        .pagination a:hover:not(.active) {
            background: rgba(250, 204, 21, 0.2);
            border-color: #facc15;
        }

        /* (Existing CSS continued) */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; scroll-behavior: smooth; }
        body { background: linear-gradient(135deg, #0f172a, #0b1220); color: white; line-height: 1.6; }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 25px 8%; position: sticky; top: 0; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(10px); z-index: 1000; }
        nav h1 { font-size: 22px; letter-spacing: 2px; color: #facc15; }
        nav ul { list-style: none; display: flex; gap: 40px; }
        nav ul li a { text-decoration: none; color: #cbd5e1; transition: .3s; }
        nav ul li a:hover { color: #facc15; }
        .hero { display: flex; align-items: center; justify-content: space-between; padding: 80px 8%; min-height: 90vh; }
        .hero-text { max-width: 550px; }
        .hero-text h2 { font-size: 60px; line-height: 1.1; }
        .hero-text span { color: #facc15; }
        .hero-image img { width: 400px; height: 400px; border-radius: 50%; object-fit: cover; border: 8px solid #facc15; box-shadow: 0 0 80px rgba(250, 204, 21, 0.2); }
        section { padding: 100px 8%; }
        .section-title { font-size: 36px; margin-bottom: 50px; text-align: center; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .card { background: rgba(30, 41, 59, 0.5); padding: 40px 30px; border-radius: 20px; transition: .4s; border: 1px solid rgba(255, 255, 255, 0.05); text-align: center; }
        .card:hover { transform: translateY(-12px); background: #facc15; color: #0f172a; }
        .review-form-container { max-width: 650px; margin: auto; background: rgba(30, 41, 59, 0.7); padding: 40px; border-radius: 20px; }
        .review-form-container input, .review-form-container textarea { width: 100%; padding: 14px; margin-bottom: 18px; border-radius: 12px; background: #0f172a; color: white; border: 1px solid #334155; }
        .review-form-container button { width: 100%; padding: 14px; border-radius: 30px; background: #facc15; color: #0f172a; font-weight: bold; cursor: pointer; border: none; }
        .review-list { margin-top: 60px; max-width: 800px; margin: 60px auto 0; display: grid; gap: 25px; }
        .review-item { background: rgba(30, 41, 59, 0.8); padding: 25px; border-radius: 15px; border-left: 5px solid #facc15; }
        .review-item h4 { color: #facc15; }
        .socials { margin-top: 40px; display: flex; justify-content: center; gap: 25px; }
        .socials a { font-size: 28px; color: #cbd5e1; background: rgba(30, 41, 59, 0.7); width: 65px; height: 65px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: 0.4s; border: 1px solid rgba(255, 255, 255, 0.05); text-decoration: none; }
        .socials a:hover { color: #0f172a; background: #facc15; transform: translateY(-8px); }
        footer { text-align: center; padding: 40px; border-top: 1px solid #1e293b; color: #64748b; }
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
        <div class="card"><h3>Web Development</h3><p>Responsive and scalable websites.</p></div>
        <div class="card"><h3>System Development</h3><p>Secure management systems.</p></div>
        <div class="card"><h3>UI/UX Design</h3><p>Clean, user-focused interfaces.</p></div>
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
                    <h4><?php echo htmlspecialchars($row['name']); ?> <span style="font-weight:normal; font-size:0.9rem; color:#94a3b8;">from <?php echo htmlspecialchars($row['company']); ?></span></h4>
                    <p style="margin-top:10px; font-style: italic; color:#cbd5e1;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
                    <small style="display:block; margin-top:15px; color:#64748b;"><?php echo date("F j, Y", strtotime($row['created_at'])); ?></small>
                </div>
            <?php endforeach; ?>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>#reviews" class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <p style="text-align:center; color:#64748b;">No reviews yet.</p>
        <?php endif; ?>
    </div>
</section>

<section id="contact" class="contact">
    <h2 class="section-title">Let's Connect</h2>
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
