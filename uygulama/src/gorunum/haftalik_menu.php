<?php require __DIR__ . '/sablon/ust.php'; ?>

<?php
$oncekiHafta  = date('Y-m-d', strtotime($baslangic . ' -7 days'));
$sonrakiHafta = date('Y-m-d', strtotime($baslangic . ' +7 days'));
?>

<section class="kart">
    <h1>Haftalık Menü ve Rezervasyon</h1>
    <form method="get" action="/haftalik-menu" class="filtre-form">
        <div>
            <label for="yemekhane_id">Yemekhane</label>
            <select id="yemekhane_id" name="yemekhane_id">
                <?php foreach ($yemekhaneler as $yh): ?>
                    <option value="<?= (int)$yh['id'] ?>" <?= (int)$yh['id'] === $yemekhaneId ? 'selected' : '' ?>>
                        <?= e($yh['ad']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="baslangic">Başlangıç Tarihi (7 Gün)</label>
            <input type="date" id="baslangic" name="baslangic" value="<?= e($baslangic) ?>">
        </div>

        <div>
            <button type="submit" class="dugme" style="width: 100%;">Listele</button>
        </div>
    </form>

    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 0.5rem; margin-top: 0.9rem;">
        <a class="dugme dugme-ikincil dugme-kucuk" href="/haftalik-menu?yemekhane_id=<?= (int)$yemekhaneId ?>&baslangic=<?= e($oncekiHafta) ?>">
            &larr; Önceki 7 Gün
        </a>
        <span style="font-size: var(--boyut-kucuk); color: var(--metin-soluk); align-self: center;">
            Aralık: <strong><?= e($baslangic) ?></strong> – <strong><?= e($bitis) ?></strong>
        </span>
        <a class="dugme dugme-ikincil dugme-kucuk" href="/haftalik-menu?yemekhane_id=<?= (int)$yemekhaneId ?>&baslangic=<?= e($sonrakiHafta) ?>">
            Sonraki 7 Gün &rarr;
        </a>
    </div>
</section>

<section class="kart">
    <?php if (empty($menuler)): ?>
        <p style="margin: 0;">Seçilen tarih aralığında menü kaydı bulunamadı.</p>
    <?php else: ?>
        <div class="tablo-kaydir">
            <table>
                <thead>
                    <tr>
                        <th>Tarih / Öğün</th>
                        <th>Menü İçeriği</th>
                        <th>Ücret</th>
                        <th>Boş Yer</th>
                        <th>Son Saat</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menuler as $m): ?>
                        <?php
                            $fiyat = ($kullanici['rol'] === 'ogrenci')
                                ? (float)$m['ogrenci_fiyati']
                                : (float)$m['personel_fiyati'];
                            $durum = $m['rezervasyon_durum'] ?? null;
                            $suresiGecti = ((int)$m['suresi_gecti'] === 1);
                            $bosYer = (int)$m['bos_yer'];
                        ?>
                        <tr>
                            <td>
                                <strong><?= e($m['tarih']) ?></strong><br>
                                <span class="rozet"><?= e($m['ogun']) ?></span>
                            </td>
                            <td>
                                <?= e($m['yemek_ozeti'] ?: 'Yemek listesi girilmemiş') ?>
                                <div style="margin-top: 0.25rem;">
                                    <a href="/gunun-menusu?yemekhane_id=<?= (int)$m['yemekhane_id'] ?>&tarih=<?= e($m['tarih']) ?>&ogun_id=<?= (int)$m['ogun_id'] ?>" style="font-size: var(--boyut-kucuk);">
                                        Kalori ve alerjen detayı
                                    </a>
                                </div>
                            </td>
                            <td><strong><?= number_format($fiyat, 2, ',', '.') ?> TL</strong></td>
                            <td>
                                <strong><?= $bosYer ?></strong> / <?= (int)$m['kapasite'] ?>
                            </td>
                            <td style="font-size: var(--boyut-kucuk);">
                                <?= e(substr((string)$m['rezervasyon_son'], 0, 16)) ?>
                            </td>
                            <td>
                                <?php if ($durum === 'aktif'): ?>
                                    <span class="rozet rozet-aktif">Rezerve Edildi</span>
                                <?php elseif ($durum === 'kullanildi'): ?>
                                    <span class="rozet rozet-kullanildi">Kullanıldı</span>
                                <?php elseif ($suresiGecti): ?>
                                    <button type="button" class="dugme dugme-kucuk" disabled>Süre Doldu</button>
                                <?php elseif ($bosYer <= 0): ?>
                                    <button type="button" class="dugme dugme-kucuk" disabled>Kapasite Dolu</button>
                                <?php else: ?>
                                    <form method="post" action="/rezervasyon-yap" style="margin: 0;">
                                        <input type="hidden" name="menu_id" value="<?= (int)$m['menu_id'] ?>">
                                        <input type="hidden" name="yemekhane_id" value="<?= (int)$yemekhaneId ?>">
                                        <input type="hidden" name="baslangic" value="<?= e($baslangic) ?>">
                                        <button type="submit" class="dugme dugme-kucuk">Rezervasyon Yap</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/sablon/alt.php'; ?>