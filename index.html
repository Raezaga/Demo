<?php
include "config.php";

$limit = 3; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'");
    $total_comments = $total_stmt->fetchColumn();
    $total_pages = ceil($total_comments / $limit);

    $stmt = $pdo->prepare("SELECT * FROM comments WHERE status = 'approved' ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { 
    error_log($e->getMessage());
    $comments = []; 
    $total_pages = 0; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afryl Lou Okit | Senior Financial Operations Partner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Playfair+Display:ital,wght@0,400;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --bg: #05070a; 
            --card-bg: rgba(255, 255, 255, 0.03); 
            --gold: #c5a059; 
            --gold-bright: #e3c184;
            --slate: #94a3b8; 
            --white: #ffffff; 
            --transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg); 
            color: var(--slate); 
            overflow-x: hidden;
            background-image: radial-gradient(circle at 10% 20%, rgba(197, 160, 89, 0.05) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(197, 160, 89, 0.05) 0%, transparent 40%);
        }

        /* --- INTERACTIVE ANIMATIONS --- */
        .reveal { opacity: 0; transform: translateY(30px); transition: var(--transition); }
        .reveal.active { opacity: 1; transform: translateY(0); }

        /* Navigation */
        nav { 
            position: fixed; top: 0; width: 100%; padding: 25px 8%; 
            background: transparent; z-index: 1000; display: flex; 
            justify-content: space-between; align-items: center; transition: var(--transition);
        }
        nav.scrolled { 
            padding: 15px 8%; background: rgba(5, 7, 10, 0.9); 
            backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        nav h1 { 
            font-family: 'Cinzel', serif; font-size: 1.8rem; letter-spacing: 2px;
            background: linear-gradient(to right, #bf953f, #fcf6ba, #aa771c);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        nav ul { display: flex; list-style: none; gap: 40px; }
        nav ul a { 
            text-decoration: none; color: var(--white); font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 2px; transition: 0.3s; opacity: 0.7;
        }
        nav ul a:hover { opacity: 1; color: var(--gold); }

        /* Hero */
        section { padding: 100px 8%; max-width: 1400px; margin: 0 auto; }
        .hero { display: flex; align-items: center; min-height: 100vh; gap: 50px; }
        .hero-text h2 { font-family: 'Playfair Display', serif; font-size: clamp(3rem, 8vw, 5.5rem); color: var(--white); line-height: 1.1; margin-bottom: 30px; }
        
        .img-wrapper { 
            width: 450px; height: 450px; border-radius: 50%; border: 1px solid var(--gold);
            overflow: hidden; position: relative; transition: var(--transition);
            box-shadow: 0 0 50px rgba(197, 160, 89, 0.1);
        }
        .img-wrapper:hover { transform: scale(1.02); box-shadow: 0 0 80px rgba(197, 160, 89, 0.2); }
        .img-wrapper img { width: 100%; height: 100%; object-fit: cover; }

        /* Feedback Cards */
        .feedback-grid { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); 
            gap: 30px; margin-top: 50px; 
        }

        .feedback-item { 
            background: var(--card-bg); padding: 40px; border: 1px solid rgba(255,255,255,0.05); 
            backdrop-filter: blur(10px); transition: var(--transition);
            display: flex; flex-direction: column; justify-content: space-between;
            min-height: 450px; position: relative; overflow: hidden;
        }
        .feedback-item::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            transform: translateX(-100%); transition: 0.6s;
        }
        .feedback-item:hover { 
            transform: translateY(-10px); background: rgba(255,255,255,0.06); 
            border-color: rgba(197, 160, 89, 0.3);
        }
        .feedback-item:hover::before { transform: translateX(100%); }

        .review-content {
            font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--white);
            font-style: italic; line-height: 1.8; max-height: 250px; overflow-y: auto;
            padding-right: 10px; margin-bottom: 30px;
        }

        .author-name {
            font-weight: 500; font-size: 0.8rem; letter-spacing: 2px;
            color: var(--gold); text-transform: uppercase; display: flex; align-items: center; gap: 10px;
        }

        /* Buttons & Forms */
        .btn-gold { 
            background: var(--gold); color: var(--bg); padding: 20px 40px; 
            text-decoration: none; font-weight: 700; font-size: 0.8rem; 
            letter-spacing: 2px; text-transform: uppercase; display: inline-block;
            transition: var(--transition); border: none; cursor: pointer;
        }
        .btn-gold:hover { background: var(--white); transform: translateY(-3px); }

        .form-box input, .form-box textarea, .form-box select {
            width: 100%; background: transparent; border: none;
            border-bottom: 1px solid rgba(255,255,255,0.1); padding: 15px 0;
            color: var(--white); margin-bottom: 25px; transition: 0.3s;
        }
        .form-box input:focus { border-bottom-color: var(--gold); outline: none; }

        /* Pagination */
        .pagination { display: flex; justify-content: center; gap: 15px; margin-top: 50px; }
        .pagination a { 
            width: 45px; height: 45px; display: flex; align-items: center; 
            justify-content: center; border: 1px solid rgba(255,255,255,0.1);
            color: var(--white); text-decoration: none; transition: 0.3s;
        }
        .pagination a.active { background: var(--gold); color: var(--bg); border-color: var(--gold); }

        /* Responsive */
        @media (max-width: 968px) {
            .hero { flex-direction: column-reverse; text-align: center; padding-top: 140px; }
            .img-wrapper { width: 300px; height: 300px; }
            nav ul { display: none; }
            .feedback-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<nav id="navbar">
    <h1>AFRYL LOU</h1>
    <ul>
        <li><a href="#hero">Overview</a></li>
        <li><a href="#feedback">Reviews</a></li>
        <li><a href="#connect">Connect</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text reveal">
        <h2 style="font-weight: 400;">Mastering <span style="color:var(--gold); font-style:italic;">Capital</span> Through Precision.</h2>
        <p style="font-size: 1.1rem; margin-bottom: 40px; max-width: 600px;">
            Strategic Financial Operations for international entities. 17+ years of building audit-ready frameworks that fuel scale.
        </p>
        <a href="#connect" class="btn-gold">Initiate Briefing</a>
    </div>
    <div class="hero-image reveal">
        <div class="img-wrapper">
            <img src="afryl.jpg" alt="Afryl Lou Okit">
        </div>
    </div>
</section>

<section id="feedback">
    <div class="reveal" style="text-align: center; margin-bottom: 60px;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 3rem; color: white;">Executive Testimonials</h3>
        <p style="color: var(--gold); letter-spacing: 5px; font-size: 0.7rem; text-transform: uppercase;">Voices of Success</p>
    </div>

    <div class="feedback-grid">
        <?php if(!empty($comments)): foreach ($comments as $row): ?>
            <div class="feedback-item reveal">
                <div class="review-content">
                    "<?php echo htmlspecialchars($row['comment_text']); ?>"
                </div>
                <div class="feedback-author">
                    <p class="author-name">
                        <?php if(!empty($row['country_code'])): ?>
                            <img src="https://flagcdn.com/w20/<?php echo strtolower(htmlspecialchars($row['country_code'])); ?>.png" width="18" alt="Flag">
                        <?php endif; ?>
                        — <?php echo htmlspecialchars($row['name']); ?> 
                    </p>
                    <p style="font-size: 0.7rem; margin-top: 8px; opacity: 0.6;">
                        <?php echo htmlspecialchars($row['position'] ?? 'Executive'); ?> / <?php echo htmlspecialchars($row['company']); ?>
                    </p>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination reveal">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>#feedback" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>

<section id="connect">
    <div class="reveal" style="background: rgba(255,255,255,0.02); padding: 80px 10%; border: 1px solid rgba(255,255,255,0.05);">
        <div class="form-box" style="max-width: 600px; margin: 0 auto;">
            <h4 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: white; text-align: center; margin-bottom: 40px;">Professional Inquiry</h4>
            <form action="send_email.php" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Business Email" required>
                <textarea name="message" rows="4" placeholder="Brief project scope..." required></textarea>
                <button type="submit" class="btn-gold" style="width:100%">Submit Request</button>
            </form>
        </div>
    </div>
</section>

<footer style="padding: 50px 8%; text-align: center; font-size: 0.7rem; border-top: 1px solid rgba(255,255,255,0.05);">
    <p>&copy; <?php echo date("Y"); ?> AFRYL LOU OKIT | SENIOR FINANCIAL OPERATIONS PARTNER</p>
</footer>

<script>
    // 1. Scroll Reveal Logic
    const reveals = document.querySelectorAll('.reveal');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });

    reveals.forEach(el => revealObserver.observe(el));

    // 2. Navbar Interaction
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('navbar');
        if (window.scrollY > 50) nav.classList.add('scrolled');
        else nav.classList.remove('scrolled');
    });
</script>

</body>
</html>
