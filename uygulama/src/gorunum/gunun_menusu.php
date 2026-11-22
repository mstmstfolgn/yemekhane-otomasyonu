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
            <button type="submit" class="dugme">Menüyü Göster</button>
        </div>
    </form>
</section>

<?php if (empty($yemekler)): ?>
    <section class="kart">
        <p>Seçilen tarih ve öğün için tanımlı menü bulunamadı.</p>
    </section>
<?php else: ?>
    <?php
        $tamFiyat = $kullanici['rol'] === 'ogrenci'
            ? (float)$menuBilgi['ogrenci_fiyati']
            : (float)$menuBilgi['personel_fiyati'];
        $yarimFiyat = round($tamFiyat * 0.60, 2);
        $kahvaltiMi = (int)$menuBilgi['ogun_id'] === 1;

        $eslesenYemekUyari = [];
        foreach ($yemekler as &$yemekSatir) {
            $idler = $yemekSatir['alerjen_idler']
                ? array_map('intval', explode(',', (string)$yemekSatir['alerjen_idler']))
                : [];
            $cakisanIdler = array_values(array_intersect($idler, $kullaniciAlerjenIdler));
            $cakisanAdlar = array_map(static fn(int $id): string => $alerjenHarita[$id] ?? '', $cakisanIdler);
            $cakisanAdlar = array_filter($cakisanAdlar);
            $yemekSatir['cakisan_alerjenler'] = $cakisanAdlar;
            if (!empty($cakisanAdlar)) {
                $eslesenYemekUyari[] = $yemekSatir['yemek'] . ' (' . implode(', ', $cakisanAdlar) . ')';
            }
        }
        unset($yemekSatir);
    ?>

    <section class="kart">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; gap:0.75rem; align-items:center;">
            <div>
                <h2><?= e($menuBilgi['yemekhane']) ?> · <?= e($menuBilgi['ogun']) ?> (<?= e($menuBilgi['tarih']) ?>)</h2>
                <p>
                    <strong>Tam Porsiyon:</strong> <?= number_format($tamFiyat, 2, ',', '.') ?> TL
                    <?php if (!$kahvaltiMi): ?>
                        · <strong>Yarım Porsiyon (%60):</strong> <?= number_format($yarimFiyat, 2, ',', '.') ?> TL
                    <?php else: ?>
                        · <em>(Kahvaltıda yarım porsiyon yoktur)</em>
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <span class="rozet">Toplam: <?= (int)$toplamKalori ?> kcal</span>
                <?php if ($doluluk): ?>
                    <span class="rozet">Boş Yer: <?= (int)$doluluk['bos_yer'] ?> / <?= (int)$doluluk['kapasite'] ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($eslesenYemekUyari)): ?>
            <div class="uyari-kutusu" role="alert">
                ⚠️ Alerjen Uyarısı: Profilinizde işaretlediğiniz alerjenleri içeren yemekler var — <?= e(implode('; ', $eslesenYemekUyari)) ?>
            </div>
        <?php endif; ?>

        <div style="overflow-x:auto; margin-top:0.75rem;">
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Yemek</th>
                        <th>Kalori</th>
                        <th>Özellik &amp; Alerjenler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($yemekler as $y): ?>
                        <?php $uyariVar = !empty($y['cakisan_alerjenler']); ?>
                        <tr class="<?= $uyariVar ? 'satir-uyari' : '' ?>">
                            <td><?= e($y['kategori']) ?></td>
                            <td>
                                <strong><?= e($y['yemek']) ?></strong>
                                <?php if ($uyariVar): ?>
                                    <div style="margin-top:0.25rem;">
                                        <span class="rozet rozet-uyari">⚠️ Alerjen Uyarısı: <?= e(implode(', ', $y['cakisan_alerjenler'])) ?></span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= (int)$y['kalori'] ?> kcal</td>
                            <td>
                                <?php if ((int)$y['vejetaryen'] === 1): ?>
                                    <span class="rozet rozet-vejetaryen">Vejetaryen</span>
                                <?php endif; ?>
                                <?php if (!empty($y['alerjenler'])): ?>
                                    <span class="rozet rozet-alerjen"><?= e($y['alerjenler']) ?></span>
                                <?php else: ?>
                                    <span>—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:1rem;">
            <a class="dugme" href="/haftalik-menu?yemekhane_id=<?= (int)$yemekhaneId ?>&baslangic=<?= e($tarih) ?>">
                Rezervasyon İçin Haftalık Menüye Geç
            </a>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/sablon/alt.php'; ?>