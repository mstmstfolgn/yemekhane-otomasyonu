<?php require __DIR__ . '/sablon/ust.php'; ?>

<?php
$formYemekhaneId    = $duzenlenenMenu ? (int)$duzenlenenMenu['yemekhane_id'] : 1;
$formTarih          = $duzenlenenMenu ? (string)$duzenlenenMenu['tarih'] : date('Y-m-d');
$formOgunId         = $duzenlenenMenu ? (int)$duzenlenenMenu['ogun_id'] : 2;
$formOgrenciFiyati  = $duzenlenenMenu ? (string)$duzenlenenMenu['ogrenci_fiyati'] : '25.00';
$formPersonelFiyati = $duzenlenenMenu ? (string)$duzenlenenMenu['personel_fiyati'] : '50.00';
$seciliYemekIdler   = $duzenlenenMenu ? $duzenlenenMenu['yemek_idler'] : [];
?>

<section class="kart">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
        <h1 style="margin: 0;"><?= $duzenlenenMenu ? 'Menü Düzenle' : 'Yeni Menü Oluştur / Güncelle' ?></h1>
        <?php if ($duzenlenenMenu): ?>
            <a class="dugme dugme-ikincil dugme-kucuk" href="/yonetici">Yeni Menü Formuna Dön</a>
        <?php endif; ?>
    </div>

    <form method="post" action="/yonetici/menu-kaydet">
        <div class="filtre-form" style="margin-bottom: 0.9rem;">
            <div>
                <label for="yemekhane_id">Yemekhane</label>
                <select id="yemekhane_id" name="yemekhane_id" required>
                    <?php foreach ($yemekhaneler as $yh): ?>
                        <option value="<?= (int)$yh['id'] ?>" <?= (int)$yh['id'] === $formYemekhaneId ? 'selected' : '' ?>>
                            <?= e($yh['ad']) ?> (Kapasite: <?= (int)$yh['kapasite'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="tarih">Tarih</label>
                <input type="date" id="tarih" name="tarih" value="<?= e($formTarih) ?>" required>
            </div>

            <div>
                <label for="ogun_id">Öğün</label>
                <select id="ogun_id" name="ogun_id" required>
                    <?php foreach ($ogunler as $og): ?>
                        <option value="<?= (int)$og['id'] ?>" <?= (int)$og['id'] === $formOgunId ? 'selected' : '' ?>>
                            <?= e($og['ad']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="izgara-2" style="margin-bottom: 0.9rem;">
            <div>
                <label for="ogrenci_fiyati">Öğrenci Fiyatı (TL)</label>
                <input type="number" id="ogrenci_fiyati" name="ogrenci_fiyati" min="0" step="0.01" value="<?= e($formOgrenciFiyati) ?>" required>
            </div>

            <div>
                <label for="personel_fiyati">Personel Fiyatı (TL)</label>
                <input type="number" id="personel_fiyati" name="personel_fiyati" min="0" step="0.01" value="<?= e($formPersonelFiyati) ?>" required>
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Menü Yemekleri (En az bir yemek seçin)</label>
            <div style="max-height: 240px; overflow-y: auto; border: 1px solid var(--cizgi); border-radius: var(--kose); padding: 0.65rem; background: var(--zemin);">
                <?php foreach ($tumYemekler as $y): ?>
                    <?php $yid = (int)$y['id']; ?>
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 400; margin-bottom: 0.4rem; cursor: pointer;">
                        <input type="checkbox" name="yemek_idler[]" value="<?= $yid ?>" style="width: auto;" <?= in_array($yid, $seciliYemekIdler, true) ? 'checked' : '' ?>>
                        <span>
                            <strong>[<?= e($y['kategori']) ?>]</strong> <?= e($y['ad']) ?>
                            <span style="color: var(--metin-soluk); font-size: var(--boyut-kucuk);">(<?= (int)$y['kalori'] ?> kcal)</span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="dugme" style="width: 100%;">Menüyü Kaydet</button>
    </form>
</section>

<section class="kart">
    <h2>Menü Doluluk ve Değerlendirme Raporu</h2>
    <form method="get" action="/yonetici" class="filtre-form" style="margin-bottom: 1rem;">
        <div>
            <label for="filtre_tarih">Tarihe Göre Filtrele</label>
            <input type="date" id="filtre_tarih" name="tarih" value="<?= e($filtreTarih ?? '') ?>">
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="dugme">Filtrele</button>
            <?php if ($filtreTarih): ?>
                <a class="dugme dugme-ikincil" href="/yonetici">Tümünü Göster</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (empty($rapor)): ?>
        <p style="margin: 0;">Kayıtlı menü bulunamadı.</p>
    <?php else: ?>
        <div class="tablo-kaydir">
            <table>
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Yemekhane</th>
                        <th>Öğün</th>
                        <th>Fiyat (Öğr / Per)</th>
                        <th>Doluluk</th>
                        <th>Ortalama Puan</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rapor as $satir): ?>
                        <tr>
                            <td><strong><?= e($satir['tarih']) ?></strong></td>
                            <td><?= e($satir['yemekhane']) ?></td>
                            <td><span class="rozet"><?= e($satir['ogun']) ?></span></td>
                            <td style="font-size: var(--boyut-kucuk);">
                                <?= number_format((float)$satir['ogrenci_fiyati'], 2, ',', '.') ?> /
                                <?= number_format((float)$satir['personel_fiyati'], 2, ',', '.') ?> TL
                            </td>
                            <td>
                                <strong><?= (int)$satir['rezerve'] ?></strong> / <?= (int)$satir['kapasite'] ?>
                                <span style="color: var(--metin-soluk); font-size: var(--boyut-kucuk);">
                                    (%<?= e($satir['doluluk_yuzde']) ?>)
                                </span>
                            </td>
                            <td>
                                <?php if ($satir['ortalama_puan'] !== null): ?>
                                    <strong><?= e($satir['ortalama_puan']) ?> / 5</strong>
                                    <span style="color: var(--metin-soluk); font-size: var(--boyut-kucuk);">
                                        (<?= (int)$satir['oy_sayisi'] ?> oy)
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--metin-soluk); font-size: var(--boyut-kucuk);">Oy yok</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a class="dugme dugme-ikincil dugme-kucuk" href="/yonetici?duzenle_id=<?= (int)$satir['menu_id'] ?><?= $filtreTarih ? '&tarih=' . e($filtreTarih) : '' ?>">
                                    Düzenle
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/sablon/alt.php'; ?>