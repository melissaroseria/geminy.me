<?php
$usersFile = 'data/users.json';
$allMessagesFile = 'data/all_global_messages.json'; 

if (!file_exists('data')) mkdir('data', 0775, true);
if (!file_exists($usersFile)) file_put_contents($usersFile, json_encode([]));
if (!file_exists($allMessagesFile)) file_put_contents($allMessagesFile, json_encode([]));

// Kayıt İşlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
    $username = preg_replace('/[^a-z0-9_]/', '', strtolower($_POST['username']));
    $bio = htmlspecialchars($_POST['bio'], ENT_QUOTES, 'UTF-8');
    
    // Hataları kökten çözen kontrol: isset ve null birleştirme operatörü (??)
    $socials = [
        'instagram' => preg_replace('/[^a-z0-9_.]/', '', $_POST['instagram'] ?? ''),
        'tiktok'    => preg_replace('/[^a-z0-9_.]/', '', $_POST['tiktok'] ?? ''),
        'pinterest' => preg_replace('/[^a-z0-9_.]/', '', $_POST['pinterest'] ?? ''),
        'twitter'   => preg_replace('/[^a-z0-9_.]/', '', $_POST['twitter'] ?? '')
    ];
    
    $users = json_decode(file_get_contents($usersFile), true);
    if (!empty($username) && !isset($users[$username])) {
        $users[$username] = ['bio' => $bio, 'socials' => $socials, 'date' => date('d.m.Y H:i')];
        file_put_contents($usersFile, json_encode($users, JSON_UNESCAPED_UNICODE));
        header("Location: profile.php?u=$username");
        exit;
    }
}

$allUsers = json_decode(file_get_contents($usersFile), true);
$globalMessages = json_decode(file_get_contents($allMessagesFile), true);
$lastTwoMessages = array_slice($globalMessages, 0, 2); 
$limitedUsers = array_slice($allUsers, -3, 3, true);   
$limitedUsers = array_reverse($limitedUsers); 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>geminy.me | Keşfet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lobster&family=Outfit:wght@400;600&display=swap');

        body { background: #0a0a0f; padding-bottom: 110px; font-family: 'Outfit', sans-serif; color: white; }

        /* TikTok Style Header (Neon Mor & Pembe) */
        .tiktok-header {
            position: sticky; top: 0; z-index: 1000;
            background: rgba(10, 10, 15, 0.7);
            backdrop-filter: blur(25px);
            border-bottom: 1px solid rgba(255, 0, 114, 0.2);
            padding: 12px 15px;
            display: flex; align-items: center; justify-content: space-between;
        }

        .brand-font {
            font-family: 'Lobster', cursive; font-size: 1.9rem;
            background: linear-gradient(45deg, #ff0072 0%, #9d50bb 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .search-bar-wrapper { flex-grow: 1; margin: 0 15px; position: relative; }
        .tiktok-search-input {
            width: 100%; background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 50px;
            padding: 8px 15px; color: white; font-size: 0.85rem; outline: none; transition: 0.3s;
        }
        .tiktok-search-input:focus {
            border-color: #ff0072; background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 15px rgba(255, 0, 114, 0.2);
        }

        /* Footer Navigasyon */
        .nav-bottom { 
            position: fixed; bottom: 0; width: 100%; 
            background: rgba(10, 10, 15, 0.85); backdrop-filter: blur(20px); 
            border-top: 1px solid rgba(255, 255, 255, 0.05); z-index: 1000; padding: 10px 0 25px 0; 
        }
        .nav-link-custom { color: #8E8E93; font-size: 0.7rem; text-align: center; flex: 1; text-decoration: none; display: flex; flex-direction: column; align-items: center; }
        .nav-link-custom i { font-size: 1.6rem; margin-bottom: 2px; }
        .nav-link-custom.active { color: #ff0072; text-shadow: 0 0 10px rgba(255, 0, 114, 0.5); }

        /* Form Yazı Renkleri */
        .form-control-glass {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important; 
            border-radius: 12px !important;
        }
        .form-control-glass::placeholder { color: rgba(255, 255, 255, 0.4) !important; }

        .section-title { font-size: 0.75rem; font-weight: 600; color: #8E8E93; text-transform: uppercase; letter-spacing: 1.5px; padding: 0 15px; margin-top: 25px; }
        .msg-bubble { border-left: 4px solid #ff0072 !important; background: rgba(255, 255, 255, 0.03); border-radius: 15px; padding: 12px; margin-bottom: 10px; }
        .user-avatar-neon {
            width: 50px; height: 50px; border-radius: 50%;
            background: linear-gradient(45deg, #ff0072, #9d50bb);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; box-shadow: 0 0 15px rgba(255, 0, 114, 0.3);
        }
    </style>
</head>
<body>

<header class="tiktok-header">
    <a href="index.php" class="brand-font">geminy.me</a>
    
    <div class="search-bar-wrapper">
        <div onclick="location.href='users/search.php'" style="cursor: pointer;">
            <input type="text" class="tiktok-search-input" placeholder="Kimi aramıştın?" readonly>
        </div>
    </div>

    </div>
</header>

<div class="container">
    <p class="section-title">Canlı Akış 🔥</p>
    <div class="px-2">
        <?php if(empty($lastTwoMessages)): ?>
            <div class="glass-card text-center opacity-50 py-3">Henüz fısıltı yok...</div>
        <?php else: foreach($lastTwoMessages as $m): ?>
            <div class="msg-bubble shadow-sm">
                <span class="d-block mb-1 fw-bold" style="font-size: 0.75rem; color: #ff0072;">@<?= htmlspecialchars($m['to']) ?></span>
                <span style="font-size: 0.95rem;"><?= htmlspecialchars($m['text']) ?></span>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <p class="section-title">Sana Özel Keşfet</p>
    <div class="row g-3 px-2">
        <?php foreach($limitedUsers as $name => $u): ?>
        <div class="col-4">
            <div class="glass-card text-center p-3 h-100 border-0" onclick="location.href='profile.php?u=<?= $name ?>'" style="cursor: pointer; background: rgba(255,255,255,0.03); border-radius: 20px;">
                <div class="user-avatar-neon mx-auto"><?= strtoupper($name[0]) ?></div>
                <div class="small text-truncate fw-medium mt-2">@<?= $name ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="nav-bottom d-flex justify-content-around">
    <a href="index.php" class="nav-link-custom active">
        <i class="bi bi-house-heart-fill"></i><span>Ana Sayfa</span>
    </a>
    <a href="#" class="nav-link-custom" data-bs-toggle="modal" data-bs-target="#registerModal">
        <i class="bi bi-plus-circle-fill" style="font-size: 2.2rem; color: #ff0072;"></i>
    </a>
    <a href="users/about/index.html" class="nav-link-custom">
        <i class="bi bi-info-circle-fill"></i><span>Keşfet</span>
    </a>
</div>

<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body glass-card m-0 shadow-lg" style="border-top: 3px solid #ff0072; background: rgba(15, 15, 20, 0.95);">
                <h5 class="fw-bold mb-3 text-center text-white">Profilini Oluştur ✨</h5>
                <form action="index.php" method="POST">
                    <div class="mb-2">
                        <label class="small opacity-50 mb-1">Kullanıcı Adı</label>
                        <input type="text" name="username" class="form-control form-control-glass" placeholder="Örn: geminy_01" required maxlength="15">
                    </div>
                    <div class="mb-3">
                        <label class="small opacity-50 mb-1">Biyografi</label>
                        <input type="text" name="bio" class="form-control form-control-glass" placeholder="Kendini tanıt..." required maxlength="60">
                    </div>
                    
                    <p class="small mb-2 fw-bold text-uppercase" style="font-size: 0.6rem; color: #ff0072;">Sosyal Medya Hesapların</p>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><input type="text" name="instagram" class="form-control form-control-glass small" placeholder="Instagram"></div>
                        <div class="col-6"><input type="text" name="tiktok" class="form-control form-control-glass small" placeholder="TikTok"></div>
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-6"><input type="text" name="pinterest" class="form-control form-control-glass small" placeholder="Pinterest"></div>
                        <div class="col-6"><input type="text" name="twitter" class="form-control form-control-glass small" placeholder="Twitter (X)"></div>
                    </div>
                    
                    <button type="submit" class="btn w-100 fw-bold" style="background: linear-gradient(45deg, #ff0072, #9d50bb); color: white; padding: 12px; border-radius: 12px; border: none;">Kayıt Ol ve Başla 🥂</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>