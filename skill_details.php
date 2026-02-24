<?php
// Get the skill name from the URL
$skill_key = isset($_GET['skill']) ? $_GET['skill'] : '';

// Data Array
$skills_data = [
    'php' => [
        'title' => 'PHP (PDO)',
        'icon' => 'fa-code',
        'tagline' => 'Secure Backend Architecture',
        'desc' => 'I utilize PHP Data Objects (PDO) to create a secure, uniform database access layer. By implementing prepared statements, I ensure every application I build is resilient against SQL injection attacks while maintaining high performance.'
    ],
    'mysql' => [
        'title' => 'MySQL',
        'icon' => 'fa-database',
        'tagline' => 'Relational Data Mastery',
        'desc' => 'Data is the heart of any system. I design optimized relational schemas in MySQL, ensuring data integrity through foreign keys, indexing for speed, and complex queries that turn raw data into meaningful user experiences.'
    ],
    'js' => [
        'title' => 'JavaScript',
        'icon' => 'fa-js',
        'tagline' => 'Dynamic Client-Side Logic',
        'desc' => 'Beyond simple scripts, I use JavaScript to build reactive interfaces. From asynchronous API fetching to DOM manipulation, I focus on creating a fluid, "app-like" feeling for users directly in their browsers.'
    ],
    'tailwind' => [
        'title' => 'Tailwind CSS',
        'icon' => 'fa-wind',
        'tagline' => 'Utility-First Design',
        'desc' => 'I leverage Tailwind to build custom designs without the bloat of traditional CSS frameworks. It allows me to maintain a consistent design system and rapid prototyping capabilities while keeping the final production code extremely lean.'
    ],
    'responsive' => [
        'title' => 'Responsive Design',
        'icon' => 'fa-mobile-alt',
        'tagline' => 'Universal Accessibility',
        'desc' => 'In a multi-device world, I ensure your platform looks stunning on everything from a 4-inch smartphone to a 32-inch ultra-wide monitor using mobile-first strategies and fluid grid layouts.'
    ],
    'rest' => [
        'title' => 'REST APIs',
        'icon' => 'fa-project-diagram',
        'tagline' => 'Seamless Integration',
        'desc' => 'I design and consume RESTful APIs to connect decoupled systems. My focus is on clean endpoints, proper HTTP methods, and JSON-based communication to ensure different platforms can talk to each other effortlessly.'
    ]
];

$current_skill = isset($skills_data[$skill_key]) ? $skills_data[$skill_key] : null;

if (!$current_skill) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_skill['title']; ?> | Technical Breakdown</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #facc15;
            --bg-dark: #050810;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-dark); color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; overflow: hidden; }

        /* MATCHING MESH BACKGROUND */
        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 10% 20%, rgba(250, 204, 21, 0.07) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(56, 189, 248, 0.07) 0%, transparent 40%);
            z-index: -1;
        }

        .detail-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 60px 40px;
            border-radius: 40px;
            max-width: 700px;
            width: 100%;
            text-align: center;
            border: 1px solid var(--glass-border);
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-box {
            width: 100px; height: 100px; background: rgba(250, 204, 21, 0.1);
            border-radius: 25px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 30px; border: 1px solid rgba(250, 204, 21, 0.2);
        }
        .icon-box i { font-size: 45px; color: var(--primary); }

        .detail-card h4 { color: var(--primary); text-transform: uppercase; letter-spacing: 4px; font-size: 0.8rem; margin-bottom: 10px; }
        .detail-card h1 { font-family: 'Space Grotesk', sans-serif; font-size: 3rem; margin-bottom: 20px; color: white; }
        .detail-card p { color: #94a3b8; font-size: 1.15rem; line-height: 1.8; margin-bottom: 40px; max-width: 550px; margin-left: auto; margin-right: auto; }

        .back-btn {
            display: inline-flex; align-items: center; gap: 10px;
            text-decoration: none; color: #000; background: var(--primary);
            padding: 15px 35px; border-radius: 15px; font-weight: 700;
            transition: 0.3s;
        }
        .back-btn:hover { transform: scale(1.05); box-shadow: 0 0 30px rgba(250, 204, 21, 0.3); }

        @media(max-width: 600px) {
            .detail-card h1 { font-size: 2rem; }
            .detail-card { padding: 40px 20px; }
        }
    </style>
</head>
<body>
    <div class="mesh-bg"></div>

    <div class="detail-card">
        <div class="icon-box">
            <i class="fas <?php echo $current_skill['icon']; ?>"></i>
        </div>
        <h4><?php echo $current_skill['tagline']; ?></h4>
        <h1><?php echo $current_skill['title']; ?></h1>
        <p><?php echo $current_skill['desc']; ?></p>
        
        <a href="index.php#resume" class="back-btn">
            <i class="fas fa-arrow-left"></i> Return to Stack
        </a>
    </div>
</body>
</html>
