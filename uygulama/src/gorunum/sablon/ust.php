<?php
$aktifKul = aktifKullanici();
$bildirim = bildirimAl();
$mevcutYol = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/') ?: '/';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($sayfaBasligi ?? 'Kampüs Yemekhane') ?> · Yemekhane Otomasyonu</title>
    <link rel="stylesheet" href="/css/temel.css">
    <style>
        .ust-cubuk {
            background: var(--ana-koyu);
            color: #fff;
            padding: 0.75rem 0;
            margin-bottom: 1.25rem;
        }
        .ust-ic {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }
        .marka {
            font-weight: 700;
            font-size: 1.15rem;
            color: #fff;
            text-decoration: none;
        }
        .kullanici-ozet {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            font-size: var(--boyut-kucuk);
        }
        .bakiye-rozet {
            background: var(--vurgu);
            color: #1F2A2E;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
        }
        .menu-cubugu {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.18);
        }
        .menu-cubugu a {
            color: #fff;
            text-decoration: none;
            padding: 0.45rem 0.75rem;
            border-radius: var(--kose);
            font-size: var(--boyut-kucuk);
            font-weight: 600;
            background: rgba(255,255,255,0.1);
        }
        .menu-cubugu a:hover,
        .menu-cubugu a.aktif {
            background: var(--vurgu);
            color: #1F2A2E;
        }
        .menu-cubugu a.cikis-link {
            background: rgba(179, 38, 30, 0.85);
            margin-left: auto;
        }
        .kart {
            background: var(--yuzey);
            border: 1px solid var(--cizgi);
            border-radius: var(--kose);
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .izgara-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1rem;
        }
        .filtre-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0.75rem;
            align-items: end;
        }
        .rozet {
            display: inline-block;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: var(--zemin);
            color: var(--metin);
            border: 1px solid var(--cizgi);
        }
        .rozet-vejetaryen { background: #E6F2E6; color: #2E6B30; border-color: #B7D9B8; }
        .rozet-alerjen    { background: #FCE8E6; color: #B3261E; border-color: #F2B8B5; }
        .rozet-uyari      { background: #B3261E; color: #FFFFFF; border-color: #8C1D18; }
        .rozet-aktif      { background: #E6F2E6; color: #2E6B30; border-color: #B7D9B8; }
        .rozet-kullanildi { background: #E8F0FE; color: #1967D2; border-color: #AECBFA; }
        .rozet-iptal      { background: #F1F3F4; color: #5F6368; border-color: #DADCE0; }
        .uyari-kutusu {
            background: #FCE8E6;
            border: 1px solid #F2B8B5;
            border-left: 4px solid #B3261E;
            color: #7A1C16;
            padding: 0.65rem 0.85rem;
            border-radius: var(--kose);
            margin: 0.65rem 0;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .satir-uyari {
            background: #FFF5F4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.92rem;
        }
        th, td {
            text-align: left;
            padding: 0.65rem 0.5rem;
            border-bottom: 1px solid var(--cizgi);
            vertical-align: top;
        }
        th {
            background: var(--zemin);
            font-weight: 700;
        }
        .dugme-ikincil { background: var(--metin-soluk); }
        .dugme-tehlike { background: var(--hata); }
        .dugme-kucuk {
            min-height: 36px;
            padding: 0.4rem 0.75rem;
            font-size: var(--boyut-kucuk);
        }
        .alt-bilgi {
            text-align: center;
            color: var(--metin-soluk);
            font-size: var(--boyut-kucuk);
            padding: 1.5rem 0 2rem;
        }
        @media (max-width: 480px) {
            .menu-cubugu a {
                flex: 1 1 calc(50% - 0.4rem);
                text-align: center;
            }
            .menu-cubugu a.cikis-link {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<header class="ust-cubuk">
    <div class="kapsayici">
        <div class="ust-ic">
            <a href="/" class="marka">Kampüs Yemekhane</a>
            <?php if ($aktifKul): ?>
                <div class="kullanici-ozet">
                    <span><?= e($aktifKul['ad'] . ' ' . $aktifKul['soyad']) ?> (<?= e($aktifKul['rol']) ?>)</span>
                    <span class="bakiye-rozet"><?= number_format((float)$aktifKul['bakiye'], 2, ',', '.') ?> TL</span>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($aktifKul): ?>
            <nav class="menu-cubugu" aria-label="Ana Menü">
                <a href="/" class="<?= ($mevcutYol === '/' || $mevcutYol === '/gunun-menusu') ? 'aktif' : '' ?>">Günün Menüsü</a>
                <a href="/haftalik-menu" class="<?= $mevcutYol === '/haftalik-menu' ? 'aktif' : '' ?>">Haftalık Menü</a>
                <a href="/rezervasyonlarim" class="<?= $mevcutYol === '/rezervasyonlarim' ? 'aktif' : '' ?>">Rezervasyonlarım</a>
                <a href="/bakiye" class="<?= $mevcutYol === '/bakiye' ? 'aktif' : '' ?>">Bakiye</a>
                <a href="/degerlendirme" class="<?= $mevcutYol === '/degerlendirme' ? 'aktif' : '' ?>">Değerlendirme</a>
                <a href="/profil" class="<?= $mevcutYol === '/profil' ? 'aktif' : '' ?>">Profil &amp; Alerjenler</a>
                <?php if ($aktifKul['rol'] === 'yonetici'): ?>
                    <a href="/yonetici" class="<?= $mevcutYol === '/yonetici' ? 'aktif' : '' ?>">Yönetici Paneli</a>
                <?php endif; ?>
                <a href="/cikis" class="cikis-link">Çıkış</a>
            </nav>
        <?php endif; ?>
    </div>
</header>

<main class="kapsayici">
    <?php if ($bildirim): ?>
        <p class="bildirim <?= $bildirim['tur'] === 'basari' ? 'bildirim-basari' : 'bildirim-hata' ?>" role="alert">
            <?= e($bildirim['mesaj']) ?>
        </p>
    <?php endif; ?>