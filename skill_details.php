<?php
// Get the skill name from the URL
$skill_key = isset($_GET['skill']) ? $_GET['skill'] : '';

// Data Array - This acts as your "database" for descriptions
$skills_data = [
    'php' => [
        'title' => 'PHP (PDO)',
        'icon' => 'fa-code',
        'desc' => 'PHP Data Objects (PDO) is a database access layer providing a uniform method of access to multiple databases. It focuses on security by using prepared statements to prevent SQL injection.'
    ],
    'mysql' => [
        'title' => 'MySQL',
        'icon' => 'fa-database',
        'desc' => 'MySQL is an open-source relational database management system. It is the backbone of most web applications, allowing for structured data storage and complex querying.'
    ],
    'js' => [
        'title' => 'JavaScript',
        'icon' => 'fa-js',
        'desc' => 'The engine of the modern web. JavaScript allows for interactive user interfaces, from simple button clicks to complex single-page applications.'
    ],
    'tailwind' => [
        'title' => 'Tailwind CSS',
        'icon' => 'fa-wind',
        'desc' => 'A utility-first CSS framework that allows for rapid UI development directly in the HTML. It results in smaller CSS files and highly customizable designs.'
    ],
    'responsive' => [
        'title' => 'Responsive Design',
        'icon' => 'fa-mobile-alt',
        'desc' => 'The practice of building websites that work on every device and screen size. I use flexible grids and media queries to ensure a perfect experience everywhere.'
    ],
    'rest' => [
        'title' => 'REST APIs',
        'icon' => 'fa-project-diagram',
        'desc' => 'Representational State Transfer (REST) is an architectural style for providing interoperability between computer systems on the internet.'
    ]
];

// Check if skill exists, else show default
$current_skill = isset($skills_data[$skill_key]) ? $skills_data[$skill_key] : null;

if (!$current_skill) {
    header("Location: index.php"); // Redirect back if skill is invalid
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_skill['title']; ?> | Details</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background: #0b1220; color: white; font-family: 'Poppins', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .detail-card { background: #0f172a; padding: 50px; border-radius: 25px; max-width: 600px; text-align: center; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .detail-card i { font-size: 80px; color: #facc15; margin-bottom: 20px; }
        .detail-card h1 { font-size: 2.5rem; margin-bottom: 20px; color: #facc15; }
        .detail-card p { color: #94a3b8; font-size: 1.1rem; line-height: 1.8; margin-bottom: 30px; }
        .back-btn { text-decoration: none; color: #0f172a; background: #facc15; padding: 12px 30px; border-radius: 30px; font-weight: bold; transition: 0.3s; }
        .back-btn:hover { background: #eab308; transform: scale(1.05); }
    </style>
</head>
<body>
    <div class="detail-card">
        <i class="fas <?php echo $current_skill['icon']; ?>"></i>
        <h1><?php echo $current_skill['title']; ?></h1>
        <p><?php echo $current_skill['desc']; ?></p>
        <a href="index.php#resume" class="back-btn">← Back to Portfolio</a>
    </div>
</body>
</html>