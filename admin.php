<?php
session_start();
include "config.php";

// 1. SIMPLE PASSWORD PROTECTION
$admin_pass = '337051'; // Change this!

if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_pass) {
        $_SESSION['is_admin'] = true;
    }
}

if (!isset($_SESSION['is_admin'])) {
    exit('
    <body style="background:#02040a; color:white; display:grid; place-items:center; height:100vh; font-family:sans-serif;">
        <form method="POST" style="background:#0a0c14; padding:40px; border-radius:20px; border:1px solid #1e293b;">
            <h2 style="margin-bottom:20px;">Admin Access</h2>
            <input type="password" name="password" placeholder="Password" style="padding:10px; border-radius:5px; border:1px solid #334155; background:#02040a; color:white;">
            <button type="submit" name="login" style="padding:10px 20px; background:#facc15; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">Login</button>
        </form>
    </body>');
}

// 2. HANDLE APPROVE / DELETE ACTIONS
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'approve') {
        $pdo->prepare("UPDATE reviews SET status = 'approved' WHERE id = ?")->execute([$id]);
    } elseif ($_GET['action'] === 'delete') {
        $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$id]);
    }
    header("Location: admin.php");
    exit();
}

// 3. FETCH DATA
$pending = $pdo->query("SELECT * FROM reviews WHERE status = 'pending' ORDER BY created_at DESC")->fetchAll();
$live = $pdo->query("SELECT * FROM reviews WHERE status = 'approved' ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body { background: #02040a; color: #f8fafc; font-family: sans-serif; padding: 50px; }
        h1 { color: #facc15; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #0a0c14; border-radius: 15px; overflow: hidden; }
        th, td { padding: 20px; text-align: left; border-bottom: 1px solid #1e293b; }
        th { background: #1e293b; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; }
        .btn { padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 0.8rem; }
        .approve { background: #22c55e; color: white; margin-right: 10px; }
        .delete { background: #ef4444; color: white; }
        .status-badge { padding: 4px 8px; border-radius: 5px; font-size: 0.7rem; font-weight: bold; background: #334155; }
    </style>
</head>
<body>
    <h1>Portfolio Moderation</h1>

    <h3>Pending Approval (<?php echo count($pending); ?>)</h3>
    <table>
        <thead><tr><th>Client</th><th>Feedback</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach($pending as $r): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($r['name']); ?></strong><br><small><?php echo htmlspecialchars($r['company']); ?></small></td>
                <td>"<?php echo htmlspecialchars($r['review']); ?>"</td>
                <td>
                    <a href="?action=approve&id=<?php echo $r['id']; ?>" class="btn approve">Approve</a>
                    <a href="?action=delete&id=<?php echo $r['id']; ?>" class="btn delete">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3 style="margin-top:50px;">Live on Website</h3>
    <table>
        <thead><tr><th>Client</th><th>Feedback</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach($live as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['name']); ?></td>
                <td><?php echo htmlspecialchars($r['review']); ?></td>
                <td><a href="?action=delete&id=<?php echo $r['id']; ?>" class="btn delete">Remove</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>