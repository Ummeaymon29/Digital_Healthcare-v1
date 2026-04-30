<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); exit();
}

$search = trim($_GET['search'] ?? '');
$medicines = [];

if (!empty($search)) {
    $stmt = $conn->prepare("SELECT id, name, category, price
                            FROM medicines 
                            WHERE name LIKE ? OR category LIKE ?
                            ORDER BY name ASC");
    $searchTerm = "%$search%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $medicines = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medicine Information</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;--text:#1e293b;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);padding:30px;margin:0;color:var(--text);}
.container{max-width:900px;margin:0 auto;}
.card{background:var(--card);padding:25px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.06);margin-bottom:25px;}
h1{margin:0 0 25px;color:var(--primary);}
.search-box{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.search-box input{flex:1;min-width:250px;padding:12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;}
.btn{padding:12px 24px;background:var(--primary);color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;}
.btn:hover{opacity:.9;}
.medicine-list{display:grid;gap:15px;}
.med-item{padding:15px;border:1px solid #e2e8f0;border-radius:8px;cursor:pointer;transition:.3s;}
.med-item:hover{background:#f8fafc;border-color:var(--primary);}
.med-item h3{margin:0 0 5px;color:var(--primary);}
.med-item p{margin:0;color:#64748b;font-size:14px;}
.back{color:#64748b;text-decoration:none;font-size:14px;display:inline-block;margin-bottom:20px;}
</style>
</head>
<body>
<div class="container">
    <a href="patient_dashboard.php" class="back">← Back to Dashboard</a>
    <h1>💊 Medicine Information Database</h1>
    
    <div class="card">
        <form action="medicine_search.php" method="GET">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search medicine by name..." value="<?=htmlspecialchars($_GET['search'] ?? '')?>">
                <button type="submit" class="btn">🔍 Search</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Search Results</h2>
        <?php if(empty($search)): ?>
            <p style="color:#64748b;text-align:center;">🔍 Enter a medicine name to search...</p>
        <?php elseif(empty($medicines)): ?>
            <p style="color:#64748b;text-align:center;">No medicines found for "<?=htmlspecialchars($search)?>"</p>
        <?php else: ?>
            <div class="medicine-list">
                <?php foreach($medicines as $med): ?>
                <a href="medicine_details.php?id=<?=$med['id']?>" style="text-decoration:none;">
                    <div class="med-item">
                        <h3>💊 <?=htmlspecialchars($med['name'])?></h3>
                        <p><strong>Category:</strong> <?=htmlspecialchars($med['category'])?></p>
                        <p><strong>Price:</strong> ৳<?=number_format($med['price'], 2)?></p>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>