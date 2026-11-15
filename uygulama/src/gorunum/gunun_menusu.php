<?php require __DIR__ . '/sablon/ust.php'; ?>

<section class="kart">
    <h1>Günün Menüsü</h1>
    <form method="get" action="/gunun-menusu" class="filtre-form">
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
            <label for="tarih">Tarih</label>
            <input type="date" id="tarih" name="tarih" value="<?= e($tarih) ?>">
        </div>

        <div>
            <label for="ogun_id">Öğün</label>
            <select id="ogun_id" name="ogun_id">
                <?php foreach ($ogunler as $og): ?>
                    <option value="<?= (int)$og['id'] ?>" <?= (int)$og['id'] === $ogunId ? 'selected' : '' ?>>
                        <?= e($og['ad']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="dugme" style="width: 100%;">Menüyü Göster</button>
        </div>
    </form>
</section>

<?php if (!$menuBilgi): ?>
    <section class="kart">
        <p style="margin: 0;">Seçilen yemekhane, tarih ve öğün için yayınlanmış menü bulunamadı.</p>
    </section>
<?php else: ?>
    <?php
        $kullaniciFiyati = ($kullanici['rol'] === 'ogrenci')
            ? (float)$menuBilgi['ogrenci_fiyati']
            : (float)$menuBilgi['personel_fiyati'];
    ?>
    <section class="kart">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
            <div>
                <h2 style="margin-bottom: 0.2rem;"><?= e($menuBilgi['yemekhane']) ?> · <?= e($menuBilgi['ogun']) ?></h2>
                <span style="color: var(--metin-soluk); font-size: var(--boyut-kucuk);">
                    Tarih: <strong><?= e($menuBilgi['tarih']) ?></strong> |
                    Son Rezervasyon: <strong><?= e(substr((string)$menuBilgi['rezervasyon_son'], 0, 16)) ?></strong>
                </span>
            </div>
            <a class="dugme" href="/haftalik-menu?yemekhane_id=<?= (int)$yemekhaneId ?>&baslangic=<?= e($tarih) ?>">
                Rezervasyon Ekranına Git
            </a>
        </div>

        <div class="izgara-2" style="margin-bottom: 1rem;">
            <div class="kart" style="margin-bottom: 0; background: var(--zemin);">
                <strong>Ücretiniz:</strong> <?= number_format($kullaniciFiyati, 2, ',', '.') ?> TL
                <div style="font-size: var(--boyut-kucuk); color: var(--metin-soluk);">
                    Öğrenci: <?= number_format((float)$menuBilgi['ogrenci_fiyati'], 2, ',', '.') ?> TL ·
                    Personel: <?= number_format((float)$menuBilgi['personel_fiyati'], 2, ',', '.') ?> TL
                </div>
            </div>

            <div class="kart" style="margin-bottom: 0; background: var(--zemin);">
                <strong>Toplam Enerji:</strong> <?= (int)$toplamKalori ?> kcal
                <?php if ($doluluk): ?>
                    <div style="font-size: var(--boyut-kucuk); color: var(--metin-soluk);">
                        Doluluk: <?= (int)$doluluk['rezerve'] ?> / <?= (int)$doluluk['kapasite'] ?>
                        (Boş yer: <strong><?= (int)$doluluk['bos_yer'] ?></strong> · %<?= e($doluluk['doluluk_yuzde']) ?>)
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="tablo-kaydir">
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Yemek</th>
                        <th>Kalori</th>
                        <th>Beslenme ve Alerjen Bilgisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($yemekler as $yemek): ?>
                        <tr>
                            <td><?= e($yemek['kategori']) ?></td>
                            <td><strong><?= e($yemek['yemek']) ?></strong></td>
                            <td><?= (int)$yemek['kalori'] ?> kcal</td>
                            <td>
                                <?php if ((int)$yemek['vejetaryen'] === 1): ?>
                                    <span class="rozet rozet-vejetaryen">Vejetaryen</span>
                                <?php endif; ?>
                                <?php if (!empty($yemek['alerjenler'])): ?>
                                    <span class="rozet rozet-alerjen">Alerjen: <?= e($yemek['alerjenler']) ?></span>
                                <?php endif; ?>
                                <?php if ((int)$yemek['vejetaryen'] !== 1 && empty($yemek['alerjenler'])): ?>
                                    <span style="color: var(--metin-soluk); font-size: var(--boyut-kucuk);">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/sablon/alt.php'; ?>