<?php require __DIR__ . '/sablon/ust.php'; ?>

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
            <button type="submit" class="dugme">Listele</button>
        </div>
    </form>
</section>

<?php if (empty($menuler)): ?>
    <section class="kart">
        <p>Seçilen tarih aralığında menü bulunamadı.</p>
    </section>
<?php else: ?>
    <?php foreach ($menuler as $m): ?>
        <?php
            $tamFiyat = $kullanici['rol'] === 'ogrenci'
                ? (float)$m['ogrenci_fiyati']
                : (float)$m['personel_fiyati'];
            $yarimFiyat = round($tamFiyat * 0.60, 2);
            $kahvaltiMi = (int)$m['ogun_id'] === 1;
            $suresiGecti = (int)$m['suresi_gecti'] === 1;
            $bosYer = (int)$m['bos_yer'];
            $aktifRez = ($m['rezervasyon_durum'] === 'aktif' || $m['rezervasyon_durum'] === 'kullanildi');

            $alerjenUyarilari = [];
            foreach (($m['yemekler_detay'] ?? []) as $yd) {
                $idler = !empty($yd['alerjen_idler'])
                    ? array_map('intval', explode(',', (string)$yd['alerjen_idler']))
                    : [];
                $cakisan = array_values(array_intersect($idler, $kullaniciAlerjenIdler));
                if (!empty($cakisan)) {
                    $adlar = array_filter(array_map(static fn(int $id): string => $alerjenHarita[$id] ?? '', $cakisan));
                    $alerjenUyarilari[] = $yd['yemek'] . ' (' . implode(', ', $adlar) . ')';
                }
            }
        ?>
        <article class="kart <?= !empty($alerjenUyarilari) ? 'satir-uyari' : '' ?>">
            <div style="display:flex; flex-wrap:wrap; justify-content:space-between; gap:0.5rem; align-items:center;">
                <div>
                    <h2><?= e($m['tarih']) ?> · <?= e($m['ogun']) ?></h2>
                    <p style="font-size:0.9rem; color:var(--metin-soluk);">
                        Son Rezervasyon: <?= e($m['rezervasyon_son']) ?> ·
                        Boş Yer: <strong><?= $bosYer ?> / <?= (int)$m['kapasite'] ?></strong>
                    </p>
                </div>
                <div>
                    <span class="rozet">Tam: <?= number_format($tamFiyat, 2, ',', '.') ?> TL</span>
                    <?php if (!$kahvaltiMi): ?>
                        <span class="rozet">Yarım (%60): <?= number_format($yarimFiyat, 2, ',', '.') ?> TL</span>
                    <?php endif; ?>
                </div>
            </div>

            <p style="margin:0.6rem 0;"><?= e($m['yemek_ozeti'] ?? 'Yemek listesi tanımlanmamış.') ?></p>

            <?php if (!empty($alerjenUyarilari)): ?>
                <div class="uyari-kutusu" role="alert">
                    ⚠️ Alerjen Uyarısı: <?= e(implode('; ', $alerjenUyarilari)) ?>
                </div>
            <?php endif; ?>

            <div style="margin-top:0.75rem;">
                <?php if ($aktifRez): ?>
                    <span class="rozet rozet-aktif">
                        Rezerve Edildi (<?= ($m['rezervasyon_porsiyon'] ?? 'tam') === 'yarim' ? 'Yarım Porsiyon' : 'Tam Porsiyon' ?>)
                    </span>
                <?php elseif ($suresiGecti): ?>
                    <button type="button" class="dugme dugme-ikincil dugme-kucuk" disabled>Süresi Doldu</button>
                <?php elseif ($bosYer <= 0): ?>
                    <button type="button" class="dugme dugme-ikincil dugme-kucuk" disabled>Kapasite Dolu</button>
                <?php else: ?>
                    <form method="post" action="/rezervasyon-yap" style="display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center;">
                        <input type="hidden" name="menu_id" value="<?= (int)$m['menu_id'] ?>">
                        <input type="hidden" name="yemekhane_id" value="<?= (int)$yemekhaneId ?>">
                        <input type="hidden" name="baslangic" value="<?= e($baslangic) ?>">

                        <?php if ($kahvaltiMi): ?>
                            <input type="hidden" name="porsiyon" value="tam">
                            <span class="rozet">Tam Porsiyon (<?= number_format($tamFiyat, 2, ',', '.') ?> TL)</span>
                        <?php else: ?>
                            <select name="porsiyon" aria-label="Porsiyon seçimi" style="max-width:250px;">
                                <option value="tam">Tam Porsiyon (<?= number_format($tamFiyat, 2, ',', '.') ?> TL)</option>
                                <option value="yarim">Yarım Porsiyon · %60 (<?= number_format($yarimFiyat, 2, ',', '.') ?> TL)</option>
                            </select>
                        <?php endif; ?>

                        <button type="submit" class="dugme dugme-kucuk">Rezervasyon Yap</button>
                    </form>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/sablon/alt.php'; ?>