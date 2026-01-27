<?php
$usersFile = '../data/users.json';
$allUsers = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keşfet | geminy.me</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap');

        body {
            background: #0a0a0f;
            color: white;
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        /* TikTok Tarzı Sabit Üst Bar */
        .search-header-fixed {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(10, 10, 15, 0.8);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            padding: 15px;
            border-bottom: 1px solid rgba(255, 0, 114, 0.15);
        }

        .search-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .back-btn {
            color: white;
            font-size: 1.4rem;
            text-decoration: none;
            transition: 0.3s;
        }

        .search-input-container {
            flex-grow: 1;
            position: relative;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 50px;
            padding: 2px 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: 0.3s;
            display: flex;
            align-items: center;
        }

        .search-input-container:focus-within {
            border-color: #ff0072;
            box-shadow: 0 0 15px rgba(255, 0, 114, 0.2);
            background: rgba(255, 255, 255, 0.12);
        }

        .search-input-container input {
            background: transparent;
            border: none;
            color: white;
            width: 100%;
            padding: 8px 5px;
            outline: none;
            font-size: 0.95rem;
        }

        /* Kullanıcı Listesi Tasarımı */
        .results-wrapper {
            padding: 15px;
        }

        .user-card {
            display: flex;
            align-items: center;
            padding: 12px;
            margin-bottom: 10px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 18px;
            text-decoration: none;
            color: white;
            border: 1px solid transparent;
            transition: 0.3s ease;
        }

        .user-card:active {
            transform: scale(0.97);
            background: rgba(255, 0, 114, 0.05);
            border-color: rgba(255, 0, 114, 0.3);
        }

        .avatar-neon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0072, #9d50bb);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 15px;
            box-shadow: 0 0 10px rgba(255, 0, 114, 0.2);
            flex-shrink: 0;
        }

        .user-details {
            flex-grow: 1;
            overflow: hidden;
        }

        .user-details .username {
            font-weight: 600;
            font-size: 1rem;
            display: block;
            margin-bottom: 2px;
        }

        .user-details .bio-text {
            font-size: 0.8rem;
            color: #8E8E93;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .section-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #ff0072;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 10px 5px 15px;
            opacity: 0.8;
        }
    </style>
</head>
<body>

<div class="search-header-fixed">
    <div class="search-wrapper">
        <a href="../index.php" class="back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="search-input-container">
            <i class="bi bi-search opacity-50 me-2"></i>
            <input type="text" id="mainSearch" placeholder="Arkadaşlarını keşfet..." autofocus autocomplete="off">
        </div>
    </div>
</div>

<div class="results-wrapper">
    <p class="section-label" id="statusText">Önerilen Kişiler</p>
    
    <div id="userList">
        <?php 
        $count = 0;
        foreach($allUsers as $name => $u): 
            if($count >= 12) break; 
        ?>
            <a href="../profile.php?u=<?= $name ?>" class="user-card">
                <div class="avatar-neon"><?= strtoupper($name[0]) ?></div>
                <div class="user-details">
                    <span class="username">@<?= $name ?></span>
                    <span class="bio-text"><?= htmlspecialchars($u['bio'] ?? 'Henüz bir biyografi yok.') ?></span>
                </div>
                <i class="bi bi-chevron-right opacity-25"></i>
            </a>
        <?php $count++; endforeach; ?>
    </div>
</div>

<script>
    const allUsers = <?= json_encode($allUsers) ?>;
    const input = document.getElementById('mainSearch');
    const list = document.getElementById('userList');
    const status = document.getElementById('statusText');

    input.addEventListener('input', function(e) {
        let val = e.target.value.toLowerCase().trim();
        
        if(val.length === 0) {
            status.innerText = 'Önerilen Kişiler';
            // Sayfayı yenilemek yerine orijinal listeyi JS ile de tutabiliriz ama en temiz yol:
            location.reload(); 
            return;
        }

        status.innerText = 'Arama Sonuçları';
        list.innerHTML = '';
        let found = false;

        Object.keys(allUsers).forEach(username => {
            if(username.includes(val)) {
                found = true;
                const bio = allUsers[username].bio || 'Henüz bir biyografi yok.';
                list.innerHTML += `
                    <a href="../profile.php?u=${username}" class="user-card">
                        <div class="avatar-neon">${username[0].toUpperCase()}</div>
                        <div class="user-details">
                            <span class="username">@${username}</span>
                            <span class="bio-text">${bio}</span>
                        </div>
                        <i class="bi bi-stars" style="color: #ff0072;"></i>
                    </a>`;
            }
        });

        if(!found) {
            list.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-person-x opacity-25" style="font-size: 3rem;"></i>
                    <p class="opacity-50 mt-2">Kullanıcı bulunamadı 🫠</p>
                </div>`;
        }
    });
</script>
</body>
</html>