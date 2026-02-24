<?php
include "config.php";

// 1. FEATURE RESTORED: THE SUBMIT LOGIC
// This handles the form submission when "POST REVIEW" is clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $name = htmlspecialchars($_POST['name']);
    $review = htmlspecialchars($_POST['review']);

    if (!empty($name) && !empty($review)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reviews (name, review, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$name, $review]);
            // Refresh to show the new review and prevent double-posting
            header("Location: " . $_SERVER['PHP_SELF'] . "?page=1");
            exit;
        } catch (Exception $e) {
            $error = "Could not save review.";
        }
    }
}

// 2. PAGINATION LOGIC
$limit = 3; 
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
    <title>Renz Loi Okit | Portfolio</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root { --primary: #facc15; --bg-dark: #050810; --glass: rgba(255, 255, 255, 0.03); --glass-border: rgba(255, 255, 255, 0.1); }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { background: var(--bg-dark); color: #e2e8f0; overflow-y: auto; scroll-behavior: smooth; }
        
        /* Navigation */
        nav { display: flex; justify-content: space-between; align-items: center; padding: 20px 8%; background: rgba(5, 8, 16, 0.95); backdrop-filter: blur(20px); position: fixed; top: 0; width: 100%; z-index: 1000; border-bottom: 1px solid var(--glass-border); }
        nav h1 { font-size: 20px; color: var(--primary); font-weight: 700; }
        nav ul { list-style: none; display: flex; gap: 25px; }
        nav ul li a { text-decoration: none; color: #94a3b8; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; cursor: pointer; }
        nav ul li a.active { color: var(--primary); border-bottom: 2px solid var(--primary); padding-bottom: 5px; }

        .main-container { margin-top: 120px; width: 100%; padding: 0 8% 100px; }
        .tab-panel { display: none; animation: fadeIn 0.4s ease-out; }
        .tab-panel.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* RESUME BUTTON: FIXED TO BLACK TEXT */
        .btn-resume { 
            display: inline-flex; align-items: center; gap: 10px; padding: 15px 35px; 
            background: var(--primary); 
            color: #000000 !important; /* Strictly Black */
            text-decoration: none !important; 
            border-radius: 10px; font-weight: 700; transition: 0.3s; 
        }

        /* WORK GRID: SMALLER 4-COLUMN LAYOUT */
        .work-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 25px; }
        .project-card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 15px; overflow: hidden; height: 300px; transition: 0.3s; }
        .project-img { height: 180px; width: 100%; background-size: cover; background-position: center; }
        .project-info { padding: 15px; }
        .project-info h3 { font-size: 0.95rem; margin-top: 5px; }

        /* REVIEWS & FORM */
        .form-box { background: var(--glass); padding: 30px; border-radius: 20px; border: 1px solid var(--primary); margin-bottom: 40px; max-width: 800px; margin-left: auto; margin-right: auto; }
        input, textarea { width: 100%; padding: 12px; margin-bottom: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: white; border-radius: 8px; }
        .review-card { background: var(--glass); padding: 25px; border-radius: 15px; border: 1px solid var(--glass-border); margin-bottom: 15px; max-width: 800px; margin-left: auto; margin-right: auto; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 30px; }
        .page-link { padding: 10px 15px; background: var(--glass); color: white; text-decoration: none; border-radius: 5px; border: 1px solid var(--glass-border); }
        .page-link.active { background: var(--primary); color: #000; font-weight: 700; }
    </style>
</head>
<body>

<nav>
    <h1>RENZ LOI.</h1>
    <ul>
        <li><a onclick="showTab(event, 'intro')" class="nav-link <?= (!isset($_GET['page'])) ? 'active' : '' ?>">Intro</a></li>
        <li><a onclick="showTab(event, 'expertise')" class="nav-link">Expertise</a></li>
        <li><a onclick="showTab(event, 'work')" class="nav-link">Work</a></li>
        <li><a onclick="showTab(event, 'clients')" class="nav-link <?= (isset($_GET['page'])) ? 'active' : '' ?>">Clients</a></li>
        <li><a onclick="showTab(event, 'contact')" class="nav-link">Contact</a></li>
    </ul>
</nav>

<main class="main-container">
    
    <div id="intro" class="tab-panel <?= (!isset($_GET['page'])) ? 'active' : '' ?>">
        <section style="display: flex; align-items: center; gap: 40px; min-height: 60vh;">
            <div>
                <h2 style="font-size: 4rem; font-family: 'Space Grotesk';">Engineering <span>Scalable</span> Systems</h2>
                <p style="color: #94a3b8; margin: 20px 0;">I am Renz Loi Okit, Full Stack Developer.</p>
                <a href="My_CV.pdf" download class="btn-resume">DOWNLOAD RESUME</a>
            </div>
            <img src="Renz.jpg" style="width: 350px; border-radius: 40px;">
        </section>
    </div>

    <div id="work" class="tab-panel">
        <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Selected Works</h2>
        <div class="work-grid">
            <div class="project-card"><div class="project-img" style="background-image: url('p1.jpg');"></div><div class="project-info"><h3>Inventory System</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p2.jpg');"></div><div class="project-info"><h3>Market Analytics</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p3.jpg');"></div><div class="project-info"><h3>Web Storefront</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p4.jpg');"></div><div class="project-info"><h3>Client Portal</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p5.jpg');"></div><div class="project-info"><h3>Retail POS</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p6.jpg');"></div><div class="project-info"><h3>Property Finder</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p7.jpg');"></div><div class="project-info"><h3>Patient EMR</h3></div></div>
            <div class="project-card"><div class="project-img" style="background-image: url('p8.jpg');"></div><div class="project-info"><h3>LMS Platform</h3></div></div>
        </div>
    </div>

    <div id="clients" class="tab-panel <?= (isset($_GET['page'])) ? 'active' : '' ?>">
        <h2 style="text-align: center; margin-bottom: 30px;">Client Voices</h2>
        
        <div class="form-box">
            <form action="" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <textarea name="review" rows="4" placeholder="Your experience..." required></textarea>
                <button type="submit" name="submit_review" class="btn-resume" style="width: 100%; justify-content: center;">POST REVIEW</button>
            </form>
        </div>

        <?php foreach ($reviews as $row): ?>
            <div class="review-card">
                <h4 style="color:var(--primary);"><?= htmlspecialchars($row['name']) ?></h4>
                <p style="margin-top: 10px; font-style: italic;">"<?= htmlspecialchars($row['review']) ?>"</p>
            </div>
        <?php endforeach; ?>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="page-link <?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>

</main>

<script>
    function showTab(event, tabId) {
        const panels = document.querySelectorAll('.tab-panel');
        panels.forEach(p => p.classList.remove('active'));
        const links = document.querySelectorAll('.nav-link');
        links.forEach(l => l.classList.remove('active'));

        document.getElementById(tabId).classList.add('active');
        if(event) event.currentTarget.classList.add('active');
        window.scrollTo(0, 0);
    }
</script>

</body>
</html>
