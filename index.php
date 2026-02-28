<?php

include "config.php";



// 1. Logic Fully Restored: Pagination & Fetching

$limit = 5; 

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;



try {

    $total_stmt = $pdo->query("SELECT COUNT(*) FROM reviews");

    $total_reviews = $total_stmt->fetchColumn();

    $total_pages = ceil($total_reviews / $limit);



    // Fetching the actual review data

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

    <title>Renz Loi Okit | Full Stack Architect</title>

    

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



        /* RESET & BASE */

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body { 

            background-color: var(--bg); 

            color: var(--text-main); 

            font-family: 'Inter', sans-serif; 

            line-height: 1.6;

            overflow-x: hidden;

        }



        /* PREMIUM BACKGROUND MESH */

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

            transform: translate(-50%, -50%); transition: 0.15s ease-out;

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



        section { max-width: 1250px; margin: 0 auto; padding: 140px 6% 80px; position: relative; z-index: 2; }



        /* HERO */

        .hero { display: flex; align-items: center; gap: 40px; min-height: 90vh; }

        .hero-text h2 { 

            font-family: 'Plus Jakarta Sans'; font-size: clamp(2.5rem, 6vw, 5rem); 

            line-height: 0.95; margin-bottom: 25px; font-weight: 800; letter-spacing: -3px;

        }

        .hero-text h2 span { -webkit-text-stroke: 1px var(--primary); color: transparent; }

        

        .btn-premium {

            padding: 18px 40px; background: var(--text-main); color: var(--bg);

            border-radius: 100px; font-weight: 700; text-decoration: none;

            display: inline-flex; align-items: center; gap: 12px; transition: 0.4s;

        }

        .btn-premium:hover { transform: translateY(-5px); background: var(--primary); }



        .hero-image img { 

            width: 100%; max-width: 380px; aspect-ratio: 1; border-radius: 40px; 

            object-fit: cover; border: 1px solid var(--border); 

        }



        /* BENTO EXPERTISE */

        .expertise-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }

        .skill-card { background: var(--card-bg); border: 1px solid var(--border); padding: 40px; border-radius: 35px; transition: 0.4s; }

        .skill-card:nth-child(1) { grid-column: span 2; grid-row: span 2; }

        .skill-card:nth-child(2) { grid-column: span 2; }

        .skill-card i { font-size: 1.8rem; color: var(--primary); margin-bottom: 25px; display: block; }

        .skill-card h3 { font-family: 'Plus Jakarta Sans'; font-size: 1.5rem; margin-bottom: 12px; }



        /* PROJECTS */

        .project-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; margin-top: 50px; }

        .project-card {

            background: var(--card-bg); border: 1px solid var(--border); border-radius: 35px;

            overflow: hidden; height: 480px; transition: 0.5s;

        }

        .project-img { height: 65%; width: 100%; background-size: cover; background-position: center; transition: 0.6s; }

        .project-info { padding: 30px; }

        .project-tag { font-size: 0.6rem; font-weight: 800; color: var(--primary); margin-bottom: 10px; display: block; }



        /* REVIEWS SECTION - RESTORED & ENHANCED */

        .reviews-container { max-width: 900px; margin: 0 auto; }

        .review-box { 

            background: var(--card-bg); border: 1px solid var(--border); 

            padding: 35px; border-radius: 30px; margin-bottom: 25px; 

            position: relative; transition: 0.3s;

        }

        .review-box:hover { border-color: rgba(250, 204, 21, 0.3); transform: scale(1.01); }

        

        .review-quote { position: absolute; top: 20px; right: 30px; font-size: 3rem; color: rgba(255,255,255,0.05); font-family: serif; }



        input, textarea { 

            width: 100%; padding: 18px; background: rgba(255,255,255,0.03); 

            border: 1px solid var(--border); color: white; border-radius: 15px; margin-bottom: 15px; 

            font-family: 'Inter'; outline: none; transition: 0.3s;

        }

        input:focus, textarea:focus { border-color: var(--primary); }

        

        .btn-submit { 

            width: 100%; padding: 18px; background: var(--primary); border: none; 

            border-radius: 15px; font-weight: 800; cursor: pointer; transition: 0.3s; color: #000;

        }



        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 50px; }

        .page-link { 

            padding: 12px 20px; border-radius: 12px; text-decoration: none; 

            border: 1px solid var(--border); color: white; transition: 0.3s; font-size: 0.8rem;

        }

        .page-link.active { background: var(--primary); color: #000; border-color: var(--primary); font-weight: 800; }



        /* CONTACT GRID */

        .contact-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; }



        @media (max-width: 900px) {

            .hero { flex-direction: column; text-align: center; }

            .expertise-grid, .contact-grid { grid-template-columns: 1fr; }

            .skill-card { grid-column: span 1 !important; grid-row: span 1 !important; }

            nav ul { display: none; }

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

        <li><a href="#expertise">Services</a></li>

        <li><a href="#projects">Work</a></li>

        <li><a href="#reviews">Clients</a></li>

        <li><a href="#contact">Contact</a></li>

    </ul>

</nav>



<section id="hero" class="hero">

    <div class="hero-text">

        <span style="color: var(--primary); font-weight: 800; font-size: 0.7rem; letter-spacing: 3px; margin-bottom: 20px; display: block;">FULL STACK ENGINEER</span>

        <h2>Engineering <span>Scalable</span> Web Systems</h2>

        <p style="color: var(--text-dim); margin-bottom: 40px; max-width: 550px;">I specialize in high-performance PHP architectures and sophisticated UI/UX design for data-heavy applications.</p>

        <div style="display: flex; gap: 20px;">

            <a href="My_CV.pdf" download class="btn-premium"><i class="fas fa-file-invoice"></i> DOWNLOAD CV</a>

        </div>

    </div>

    <div class="hero-image">

        <img src="Renz.jpg" alt="Renz Loi Okit">

    </div>

</section>



<section id="expertise">

    <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 2.5rem; margin-bottom: 40px;">Capabilities</h2>

    <div class="expertise-grid">

        <div class="skill-card"><i class="fas fa-server"></i><h3>Backend Arch</h3><p>Secure PHP (PDO) logic with optimized MySQL relational schemas and RESTful API structures.</p></div>

        <div class="skill-card"><i class="fas fa-layer-group"></i><h3>Frontend Logic</h3><p>ES6 JavaScript and Tailwind-optimized interfaces for seamless user flow.</p></div>

        <div class="skill-card"><i class="fas fa-microchip"></i><h3>System Design</h3><p>Performance tuning and database normalization for enterprise-grade scale.</p></div>

        <div class="skill-card"><i class="fas fa-terminal"></i><h3>Workflow</h3><p>Modern CI/CD, Git version control, and Unix-based server management.</p></div>

    </div>

</section>



<section id="projects">

    <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 2.5rem; margin-bottom: 40px;">Selected <span>Work</span></h2>

    <div class="project-grid">

        <?php 

        $project_list = [

            ['tag' => 'Logistics', 'title' => 'Enterprise Inventory', 'desc' => 'Warehouse management system.', 'img' => 'p1.jpg'],

            ['tag' => 'Fintech', 'title' => 'Market Analytics', 'desc' => 'Live data visualization engine.', 'img' => 'p2.jpg'],

            ['tag' => 'Commerce', 'title' => 'Modern Storefront', 'desc' => 'High-conversion e-commerce.', 'img' => 'p3.jpg'],

        ];

        foreach($project_list as $p): ?>

        <div class="project-card">

            <div class="project-img" style="background-image: url('<?php echo $p['img']; ?>');"></div>

            <div class="project-info">

                <span class="project-tag"><?php echo $p['tag']; ?></span>

                <h3><?php echo $p['title']; ?></h3>

                <p style="font-size: 0.8rem; color: var(--text-dim);"><?php echo $p['desc']; ?></p>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</section>



<section id="reviews">

    <div style="text-align: center; margin-bottom: 60px;">

        <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 2.8rem;">Client <span>Voices</span></h2>

        <p style="color: var(--text-dim);">Feedback from recent engineering collaborations.</p>

    </div>



    <div class="reviews-container">

        <div class="review-box" style="border-color: var(--primary); background: rgba(250, 204, 21, 0.02);">

            <h4 style="margin-bottom: 20px; font-family: 'Plus Jakarta Sans'; color: var(--primary);">Post a Review</h4>

            <form action="save_review.php" method="POST">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">

                    <input type="text" name="name" placeholder="Full Name" required>

                    <input type="text" name="company" placeholder="Company/Business" required>

                </div>

                <textarea name="review" rows="3" placeholder="How was the project experience?" required></textarea>

                <button type="submit" class="btn-submit">SUBMIT REVIEW</button>

            </form>

        </div>



        <div id="reviews-list">

            <?php if (empty($reviews)): ?>

                <p style="text-align: center; color: var(--text-dim);">No reviews yet. Be the first to leave one!</p>

            <?php else: ?>

                <?php foreach ($reviews as $row): ?>

                    <div class="review-box">

                        <div class="review-quote">"</div>

                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">

                            <div style="width: 50px; height: 50px; background: linear-gradient(45deg, var(--primary), #fbbf24); border-radius: 50%; display: grid; place-items: center; color: #000; font-weight: 800; font-size: 1.2rem;">

                                <?php echo strtoupper(substr($row['name'], 0, 1)); ?>

                            </div>

                            <div>

                                <h4 style="font-family: 'Plus Jakarta Sans'; font-size: 1.1rem; color: #fff;">

                                    <?php echo htmlspecialchars($row['name']); ?>

                                </h4>

                                <p style="font-size: 0.75rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px;">

                                    <?php echo htmlspecialchars($row['company']); ?>

                                </p>

                            </div>

                        </div>

                        <p style="color: #cbd5e1; font-style: italic; line-height: 1.7; font-size: 1rem; position: relative; z-index: 1;">

                            "<?php echo htmlspecialchars($row['review']); ?>"

                        </p>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>



        <?php if ($total_pages > 1): ?>

        <div class="pagination">

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>

                <a href="?page=<?php echo $i; ?>#reviews" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">

                    <?php echo $i; ?>

                </a>

            <?php endfor; ?>

        </div>

        <?php endif; ?>

    </div>

</section>



<section id="contact">

    <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 3rem; text-align: center; margin-bottom: 50px;">Let's <span>Connect.</span></h2>

    <div class="contact-grid">

        <div style="background: var(--card-bg); padding: 40px; border-radius: 40px; border: 1px solid var(--border);">

            <form id="contactForm" action="send_message.php" method="POST" style="display: grid; gap: 20px;">

                <input type="text" name="contact_name" placeholder="Name" required>

                <input type="email" name="contact_email" placeholder="Email" required>

                <textarea name="message" rows="4" placeholder="Message" required></textarea>

                <button type="submit" id="submitBtn" class="btn-submit">SEND MESSAGE</button>

            </form>

        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">

            <div style="background: var(--card-bg); border: 1px solid var(--border); padding: 30px; border-radius: 30px;">

                <p style="font-size: 0.7rem; color: var(--text-dim); margin-bottom: 5px;">DIRECT EMAIL</p>

                <h4 style="cursor: pointer; color: #fff;" onclick="copyEmail()" id="emailText">renzloiokit.dev@email.com</h4>

                <p id="copyMsg" style="font-size: 0.6rem; color: var(--primary); opacity: 0;">Click to copy</p>

            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">

                <a href="https://github.com/Raezaga" target="_blank" style="background: var(--card-bg); border: 1px solid var(--border); padding: 25px; border-radius: 25px; text-align: center; color: white; text-decoration: none;"><i class="fab fa-github"></i></a>

                <a href="https://www.linkedin.com/in/renz-loi-okit-13397b393/" target="_blank" style="background: var(--card-bg); border: 1px solid var(--border); padding: 25px; border-radius: 25px; text-align: center; color: white; text-decoration: none;"><i class="fab fa-linkedin"></i></a>

            </div>

        </div>

    </div>

</section>



<footer style="text-align: center; padding: 60px; color: var(--text-dim); font-size: 0.75rem; border-top: 1px solid var(--border);">

    &copy; <?php echo date("Y"); ?> RENZ LOI OKIT. ALL RIGHTS RESERVED.

</footer>



<script>

    const glow = document.getElementById('cursor-glow');

    document.addEventListener('mousemove', (e) => {

        glow.style.left = e.clientX + 'px';

        glow.style.top = e.clientY + 'px';

    });



    function copyEmail() {

        navigator.clipboard.writeText("renzloiokit.dev@email.com");

        const msg = document.getElementById('copyMsg');

        msg.style.opacity = "1";

        msg.innerText = "COPIED!";

        setTimeout(() => { msg.style.opacity = "0"; }, 2000);

    }



    // --- INTEGRATED STATUS HANDLER ---

    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get('status') === 'success') {

        alert("✨ Success! Your message has been sent to Renz.");

        window.history.replaceState({}, document.title, window.location.pathname);

    } else if (urlParams.get('status') === 'error') {

        alert("❌ Error: Message delivery failed. Please check your connection.");

        window.history.replaceState({}, document.title, window.location.pathname);

    }



    // --- BUTTON LOADING STATE ---

    const contactForm = document.getElementById('contactForm');

    const submitBtn = document.getElementById('submitBtn');



    contactForm.addEventListener('submit', function() {

        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> SENDING...';

        submitBtn.style.opacity = "0.7";

        submitBtn.style.pointerEvents = "none";

    });

</script>



</body>

</html>
