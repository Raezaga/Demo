<?php
session_start();
include "config.php";

$admin_pass = 'YOUR_PASSWORD_HERE'; // KEEP THIS SECURE

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
            <input type="password" name="password" placeholder="Password" style="padding:10px; border-radius:5px; border:1px solid #334155; background:#02040a; color:white; margin-bottom:10px; display:block; width:100%;">
            <button type="submit" name="login" style="width:100%; padding:10px; background:#facc15; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">Login</button>
        </form>
    </body>');
}

// HANDLE ACTIONS
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

// SEARCH LOGIC
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchQuery = $search ? "AND (name LIKE :s OR review LIKE :s OR company LIKE :s)" : "";

$pendingStmt = $pdo->prepare("SELECT * FROM reviews WHERE status = 'pending' $searchQuery ORDER BY created_at DESC");
$liveStmt = $pdo->prepare("SELECT * FROM reviews WHERE status = 'approved' $searchQuery ORDER BY created_at DESC");

if ($search) {
    $pendingStmt->bindValue(':s', "%$search%");
    $liveStmt->bindValue(':s', "%$search%");
}

$pendingStmt->execute();
$liveStmt->execute();

$pending = $pendingStmt->fetchAll();
$live = $liveStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #02040a; color: #f8fafc; font-family: 'Inter', sans-serif; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .search-box { background: #0a0c14; border: 1px solid #1e293b; padding: 10px 20px; border-radius: 10px; color: white; width: 300px; }
        .logout-btn { background: #ef4444; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 0.8rem; }
        table { width: 100%; border-collapse: collapse; background: #0a0c14; border: 1px solid #1e293b; border-radius: 15px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #1e293b; }
        th { background: #1e293b; font-size: 0.7rem; text-transform: uppercase; color: #94a3b8; }
        .btn { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.75rem; }
        .approve { background: #22c55e; color: white; }
        .delete { background: #ef4444; color: white; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <h1 style="color: #facc15; margin:0;">Moderation Panel</h1>
            <p style="color: #94a3b8; font-size: 0.8rem;">Logged in as Administrator</p>
        </div>
        <div style="display:flex; gap:15px; align-items:center;">
            <form method="GET">
                <input type="text" name="search" class="search-box" placeholder="Search reviews..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
        </div>
    </div>

    <h3>Pending (<?php echo count($pending); ?>)</h3>
    <table>
        <thead><tr><th>Client</th><th>Review</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach($pending as $r): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($r['name']); ?></strong><br><small><?php echo htmlspecialchars($r['company']); ?></small></td>
                <td style="max-width: 400px;"><?php echo htmlspecialchars($r['review']); ?></td>
                <td>
                    <a href="?action=approve&id=<?php echo $r['id']; ?>" class="btn approve">APPROVE</a>
                    <a href="?action=delete&id=<?php echo $r['id']; ?>" class="btn delete" onclick="return confirm('Delete forever?')">DELETE</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3 style="margin-top:50px;">Live Reviews (<?php echo count($live); ?>)</h3>
    <table>
        <thead><tr><th>Client</th><th>Review</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach($live as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['name']); ?></td>
                <td><?php echo htmlspecialchars($r['review']); ?></td>
                <td><a href="?action=delete&id=<?php echo $r['id']; ?>" class="btn delete">REMOVE</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
