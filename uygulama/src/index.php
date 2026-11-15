<?php
declare(strict_types=1);

require_once __DIR__ . '/config/baglanti.php';
require_once __DIR__ . '/config/oturum.php';

require_once __DIR__ . '/veri/KullaniciVeri.php';
require_once __DIR__ . '/veri/MenuVeri.php';
require_once __DIR__ . '/veri/RezervasyonVeri.php';

require_once __DIR__ . '/kontrol/GirisKontrol.php';
require_once __DIR__ . '/kontrol/MenuKontrol.php';
require_once __DIR__ . '/kontrol/BakiyeKontrol.php';
require_once __DIR__ . '/kontrol/RezervasyonKontrol.php';
require_once __DIR__ . '/kontrol/YoneticiKontrol.php';

$yol   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$yol   = rtrim($yol, '/') ?: '/';
$metot = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($yol === '/giris') {
    if ($metot === 'POST') {
        GirisKontrol::girisYap();
    } else {
        GirisKontrol::girisFormu();
    }
} elseif ($yol === '/cikis') {
    GirisKontrol::cikisYap();
} elseif ($yol === '/' || $yol === '/gunun-menusu') {
    MenuKontrol::gununMenusu();
} elseif ($yol === '/haftalik-menu') {
    MenuKontrol::haftalikMenu();
} elseif ($yol === '/rezervasyon-yap' && $metot === 'POST') {
    RezervasyonKontrol::rezervasyonYap();
} elseif ($yol === '/rezervasyonlarim') {
    RezervasyonKontrol::rezervasyonlarim();
} elseif ($yol === '/rezervasyon-iptal' && $metot === 'POST') {
    RezervasyonKontrol::rezervasyonIptal();
} elseif ($yol === '/bakiye') {
    BakiyeKontrol::bakiyeSayfasi();
} elseif ($yol === '/bakiye-yukle' && $metot === 'POST') {
    BakiyeKontrol::bakiyeYukle();
} elseif ($yol === '/degerlendirme') {
    RezervasyonKontrol::degerlendirmeSayfasi();
} elseif ($yol === '/degerlendirme-kaydet' && $metot === 'POST') {
    RezervasyonKontrol::degerlendirmeKaydet();
} elseif ($yol === '/yonetici') {
    YoneticiKontrol::panel();
} elseif ($yol === '/yonetici/menu-kaydet' && $metot === 'POST') {
    YoneticiKontrol::menuKaydet();
} else {
    http_response_code(404);
    $sayfaBasligi = 'Sayfa Bulunamadı';
    require __DIR__ . '/gorunum/sablon/ust.php';
    echo '<section class="kart"><h1>404 · Sayfa Bulunamadı</h1><p>Aradığınız sayfa mevcut değil.</p><a class="dugme" href="/">Ana Sayfaya Dön</a></section>';
    require __DIR__ . '/gorunum/sablon/alt.php';
}