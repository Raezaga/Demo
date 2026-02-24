<?php
// 1. Database Connection
include "config.php";

// 2. Pagination Logic
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
        
        body { 
            background: linear-gradient(135deg, #0f172a, #0b1220); 
            color: white; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAVIGATION */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 8%;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        nav h1 { font-size: 22px; letter-spacing: 2px; color: #facc15; cursor: pointer; }
        nav ul { list-style: none; display: flex; gap: 40px; }
        nav ul li a { 
            text-decoration: none; 
            color: #cbd5e1; 
            transition: .3s; 
            cursor: pointer;
            font-weight: 500;
        }
        nav ul li a:hover, nav ul li a.active { color: #facc15; }

        /* SECTION VISIBILITY LOGIC */
        section { 
            padding: 80px 8%; 
            display: none; /* Hidden by default */
            animation: fadeIn 0.5s ease;
            flex-grow: 1;
        }

        section.active { 
            display: flex; /* Show active section */
            flex-direction: column;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* HERO (Adjusted for Flex) */
        .hero.active { flex-direction: row; align-items: center; justify-content: space-between; }
        .hero-text { max-width: 550px; }
        .hero-text h2 { font-size: 60px; line-height: 1.1; }
        .hero-text span { color: #facc15; }
        .hero-image img { width: 350px; height: 350px; border-radius: 50%; object-fit: cover; border: 8px solid #facc15; box-shadow: 0 0 50px rgba(250,204,21,0.2); }

        /* SHARED STYLES */
        .section-title { font-size: 36px; margin-bottom: 40px; text-align: center; color: #facc15; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .card { background: rgba(30, 41, 59, 0.5); padding: 40px 30px; border-radius: 20px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }

        /* REVIEWS */
        .review-form-container { max-width: 600px; margin: 0 auto 40px; }
        .review-form-container input, .review-form-container textarea { 
            width: 100%; padding: 14px; margin-bottom: 15px; border-radius: 10px; 
            background: #0f172a; color: white; border: 1px solid #334155; 
        }
        .review-form-container button { 
            width: 100%; padding: 14px; border-radius: 30px; background: #facc15; 
            color: #0f172a; font-weight: bold; border: none; cursor: pointer;
        }
        .review-item { background: rgba(30, 41, 59, 0.8); padding: 20px; border-radius: 15px; border-left: 5px solid #facc15; margin-bottom: 20px; }

        /* PAGINATION */
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
        .pagination a { padding: 8px 15px; background: #1e293b; color: white; text-decoration: none; border-radius: 5px; }
        .pagination a.active { background: #facc15; color: #0f172a; }

        /* SOCIALS */
        .socials { display: flex; justify-content: center; gap: 20px; margin-top: 20px; }
        .socials a { font-size: 30px; color: #cbd5e1; transition: 0.3s; }
        .socials a:hover { color: #facc15; transform: translateY(-5px); }

        footer { text-align: center; padding: 30px; border-top: 1px solid #1e293b; color: #64748b; margin-top: auto; }

        @media(max-width: 900px) {
            .hero.active { flex-direction: column; text-align: center; }
            .hero-image { margin-top: 30px; }
        }
    </style>
</head>
<body>

<nav>
    <h1 onclick="showSection('hero')">RENZ.</h1>
    <ul>
        <li><a onclick="showSection('services')" id="nav-services">Services</a></li>
        <li><a onclick="showSection('reviews')" id="nav-reviews">Reviews</a></li>
        <li><a onclick="showSection('contact')" id="nav-contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero active">
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
        <div class="card"><i class="fas fa-code fa-2x"></i><h3>Web Development</h3><p>Responsive websites.</p></div>
        <div class="card"><i class="fas fa-database fa-2x"></i><h3>System Development</h3><p>Secure management tools.</p></div>
        <div class="card"><i class="fas fa-paint-brush fa-2x"></i><h3>UI/UX Design</h3><p>Modern user interfaces.</p></div>
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

    <div style="max-width: 800px; margin: 0 auto; width: 100%;">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $row): ?>
                <div class="review-item">
                    <h4><?php echo htmlspecialchars($row['name']); ?> <small style="color:#94a3b8;">(<?php echo htmlspecialchars($row['company']); ?>)</small></h4>
                    <p style="margin-top:10px; color:#cbd5e1;">"<?php echo htmlspecialchars($row['review']); ?>"</p>
                </div>
            <?php endforeach; ?>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="contact">
    <h2 class="section-title">Let's Connect</h2>
    <p style="text-align:center; color:#94a3b8;">Feel free to reach out for collaborations or inquiries.</p>
    <div class="socials">
        <a href="https://github.com/Raezaga" target="_blank"><i class="fab fa-github"></i></a>
        <a href="https://www.facebook.com/Raezaga/" target="_blank"><i class="fab fa-facebook"></i></a>
        <a href="https://www.linkedin.com/in/renz-loi-okit-13397b393/" target="_blank"><i class="fab fa-linkedin"></i></a>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> Renz Okit. All Rights Reserved.
</footer>

<script>
    function showSection(sectionId) {
        // 1. Hide all sections
        const sections = document.querySelectorAll('section');
        sections.forEach(sec => sec.classList.remove('active'));

        // 2. Remove active class from all nav links
        const navLinks = document.querySelectorAll('nav ul li a');
        navLinks.forEach(link => link.classList.remove('active'));

        // 3. Show the chosen section
        const activeSection = document.getElementById(sectionId);
        activeSection.classList.add('active');

        // 4. Highlight the nav link
        const activeLink = document.getElementById('nav-' + sectionId);
        if(activeLink) activeLink.classList.add('active');
        
        // 5. Update URL hash (Optional: helps with browser 'Back' button)
        window.location.hash = sectionId;
    }

    // Handle initial page load (check if URL has a #page or #section)
    window.onload = () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('page')) {
            showSection('reviews'); // If we just changed pages, stay on reviews
        }
    };
</script>

</body>
</html>
