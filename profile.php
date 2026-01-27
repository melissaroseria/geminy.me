<?php
$username = preg_replace('/[^a-z0-9_]/', '', $_GET['u']);
$usersFile = 'data/users.json';
$msgFile = "data/msg_$username.json";
$repliesDir = 'data/replies/';
$allMessagesFile = 'data/all_global_messages.json';

if (!file_exists('data')) mkdir('data', 0775, true);
if (!file_exists($repliesDir)) mkdir($repliesDir, 0775, true);

$users = json_decode(file_get_contents($usersFile), true);
if (!isset($users[$username])) header("Location: index.php");

$userData = $users[$username];
$socials = isset($userData['socials']) ? $userData['socials'] : [];

// Mesaj Kaydı ve Yanıtlama
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $time = date('H:i');

    if (isset($_POST['message'])) { // Ana İtiraf
        $msgId = uniqid();
        $msgText = htmlspecialchars($_POST['message']);
        
        $myMessages = file_exists($msgFile) ? json_decode(file_get_contents($msgFile), true) : [];
        array_unshift($myMessages, ['id' => $msgId, 'text' => $msgText, 'time' => $time]);
        file_put_contents($msgFile, json_encode($myMessages, JSON_UNESCAPED_UNICODE));
        
        $globalMessages = file_exists($allMessagesFile) ? json_decode(file_get_contents($allMessagesFile), true) : [];
        array_unshift($globalMessages, ['text' => $msgText, 'to' => $username, 'time' => $time]);
        file_put_contents($allMessagesFile, json_encode(array_slice($globalMessages, 0, 50), JSON_UNESCAPED_UNICODE));
    } 
    elseif (isset($_POST['reply_text'], $_POST['parent_id'])) { // Yanıt Kaydı
        $parentId = $_POST['parent_id'];
        $replyFile = $repliesDir . "rep_" . $parentId . ".json";
        $replies = file_exists($replyFile) ? json_decode(file_get_contents($replyFile), true) : [];
        $replies[] = ['text' => htmlspecialchars($_POST['reply_text']), 'time' => $time];
        file_put_contents($replyFile, json_encode($replies, JSON_UNESCAPED_UNICODE));
    }
    header("Location: profile.php?u=$username");
    exit;
}

$myMessages = file_exists($msgFile) ? json_decode(file_get_contents($msgFile), true) : [];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $username ?> | geminy.me</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root { --neon-pink: #ff0072; --glass: rgba(255, 255, 255, 0.05); }
        .tg-header { display: flex; align-items: center; padding: 15px; background: rgba(255, 0, 114, 0.1); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255, 0, 114, 0.2); position: sticky; top: 0; z-index: 100; }
        .tg-avatar { width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(45deg, #9d50bb, #ff0072); display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 12px; }
        .tweet-box { background: var(--glass); border: 1px solid rgba(255, 0, 114, 0.2); border-radius: 20px; padding: 15px; margin: 20px 0; }
        .tweet-btn { background: var(--neon-pink); border: none; color: white; padding: 12px; border-radius: 15px; font-weight: 600; width: 100%; margin-top: 10px; } /* Buton Genişliği */
        .social-link { color: white; opacity: 0.7; font-size: 1.2rem; transition: 0.3s; }
        .social-link:hover { color: #ff0072; opacity: 1; }
        .reddit-msg { border-left: 2px solid rgba(255, 0, 114, 0.3); margin-bottom: 30px; padding-left: 15px; }
        .reply-box { margin-left: 20px; margin-top: 8px; font-size: 0.85rem; background: rgba(255, 255, 255, 0.03); padding: 10px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); }
    </style>
</head>
<body>

<div class="tg-header">
    <a href="index.php" class="text-white me-3"><i class="bi bi-arrow-left fs-4"></i></a>
    <div class="tg-avatar"><?= strtoupper($username[0]) ?></div>
    <div class="flex-grow-1">
        <div class="fw-bold">@<?= $username ?></div>
        <div class="small opacity-50"><?= htmlspecialchars($userData['bio']) ?></div>
        <div class="d-flex gap-3 mt-1">
            <?php foreach($socials as $platform => $handle): if(!empty($handle)): ?>
                <a href="https://<?= $platform ?>.com/<?= $handle ?>" target="_blank" class="social-link"><i class="bi bi-<?= $platform ?>"></i></a>
            <?php endif; endforeach; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="tweet-box">
        <form method="POST">
            <textarea name="message" class="form-control bg-transparent border-0 text-white shadow-none" rows="3" placeholder="Neler oluyor? İtiraf et..." required></textarea>
            <button type="submit" class="tweet-btn">İtiraf Et 🚀</button>
        </form>
    </div>

    <div class="mt-4">
        <?php foreach($myMessages as $m): ?>
            <div class="reddit-msg">
                <div class="small opacity-50 mb-1">🕒 <?= $m['time'] ?> • Anonim</div>
                <div class="fs-5"><?= $m['text'] ?></div>
                <div class="mt-2"><span class="text-neon small" style="cursor:pointer;" onclick="toggleReply('<?= $m['id'] ?>')"><i class="bi bi-chat-dots"></i> Yanıtla</span></div>
                
                <?php 
                $replyFile = $repliesDir . "rep_" . $m['id'] . ".json";
                if(file_exists($replyFile)): 
                    foreach(json_decode(file_get_contents($replyFile), true) as $r): ?>
                        <div class="reply-box"><?= $r['text'] ?> <span class="float-end opacity-25" style="font-size:0.6rem"><?= $r['time'] ?></span></div>
                <?php endforeach; endif; ?>

                <div id="form-<?= $m['id'] ?>" class="d-none mt-2">
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="parent_id" value="<?= $m['id'] ?>">
                        <input type="text" name="reply_text" class="form-control form-control-glass shadow-none" placeholder="Yanıtın..." required>
                        <button type="submit" class="btn btn-neon p-2" style="width:50px;"><i class="bi bi-send"></i></button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function toggleReply(id) {
        const form = document.getElementById('form-' + id);
        form.classList.toggle('d-none');
    }
</script>
</body>
</html>
