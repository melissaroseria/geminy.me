#!/bin/bash

# --- Geminy.me Ultimate Deployer ---
echo "🔥 Geminy.me: Özgürlük Yükleniyor..."

# 1. GitHub'dan Güncel Kodları Al (Klonlama veya Güncelleme)
if [ -d ".git" ]; then
    echo "♻️  Mevcut repo güncelleniyor..."
    git pull origin main
else
    echo "📥 Repo klonlanıyor..."
    # Eğer dizin boş değilse hata vermemesi için geçici bir klasöre çekip içeri taşıyabiliriz
    git clone https://github.com/melissaroseria/geminy.me.git temp_repo
    mv temp_repo/* .
    mv temp_repo/.* . 2>/dev/null
    rm -rf temp_repo
fi

# 2. Dizin ve İzin Ayarları (Sessiz İstila Modu)
echo "📂 Dosya sistemi yapılandırılıyor..."
mkdir -p data assets
chmod -R 777 data  # Yazma izinlerini kökle kanki, hata istemiyoruz

# 3. Data Dosyalarını Sağlamlaştır
if [ ! -f "data/users.json" ]; then
    echo "{}" > data/users.json
fi
if [ ! -f "data/all_global_messages.json" ]; then
    echo "[]" > data/all_global_messages.json
fi

# 4. PHP Sunucusunu Ateşle
echo "----------------------------------------------"
echo "✨ SİSTEM AKTİF! Nicky Romero Remix Başlasın! 🎵"
echo "🛰️  Siber Şube'ye selam, fısıldaşmaya devam..."
echo "----------------------------------------------"

# Replit/Render için port yönlendirmeli başlatma
php -S 0.0.0.0:8000 index.php
