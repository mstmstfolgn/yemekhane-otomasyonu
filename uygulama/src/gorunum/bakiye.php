<?php require __DIR__ . '/sablon/ust.php'; ?>

<div class="izgara-2">
    <section class="kart">
        <h1>Bakiye Durumu</h1>
        <p style="font-size: 1.6rem; font-weight: 700; color: var(--ana); margin: 0.5rem 0;">
            <?= number_format((float)$kullanici['bakiye'], 2, ',', '.') ?> TL
        </p>
        <p style="color: var(--metin-soluk); font-size: var(--boyut-kucuk); margin-bottom: 0;">
            Hesap: <?= e($kullanici['ad'] . ' ' . $kullanici['soyad']) ?> (<?= e($kullanici['eposta']) ?>)
        </p>
    </section>

    <section class="kart">
        <h2>Bakiye Yükle</h2>
        <form method="post" action="/bakiye-yukle">
            <div style="margin-bottom: 0.75rem;">
                <label for="tutar">Yüklenecek Tutar (10 – 1000 TL)</label>
                <input type="number" id="tutar" name="tutar" min="10" max="1000" step="0.01" required placeholder="Örn: 100">
            </div>
            <button type="submit" class="dugme" style="width: 100%;">Bakiye Yükle</button>
        </form>
    </section>
</div>

<section class="kart">
    <h2>Son Bakiye Hareketleri</h2>
    <?php if (empty($hareketler)): ?>
        <p style="margin: 0;">Henüz bakiye hareketiniz bulunmuyor.</p>
    <?php else: ?>
        <div class="tablo-kaydir">
            <table>
                <thead>
                    <tr>
                        <th>Tarih / Saat</th>
                        <th>İşlem Türü</th>
                        <th>Açıklama</th>
                        <th>Tutar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hareketler as $h): ?>
                        <?php
                            $tur = (string)$h['tur'];
                            $tutar = (float)$h['tutar'];
                            $turEtiket = match ($tur) {
                                'yukleme'     => 'Yükleme',
                                'rezervasyon' => 'Rezervasyon',
                                'iade'        => 'İade',
                                default       => $tur,
                            };
                        ?>
                        <tr>
                            <td style="font-size: var(--boyut-kucuk);"><?= e(substr((string)$h['olusturma'], 0, 16)) ?></td>
                            <td>
                                <span class="rozet <?= $tutar >= 0 ? 'rozet-aktif' : 'rozet-alerjen' ?>">
                                    <?= e($turEtiket) ?>
                                </span>
                            </td>
                            <td><?= e($h['aciklama'] ?: '—') ?></td>
                            <td style="font-weight: 700; color: <?= $tutar >= 0 ? 'var(--basari)' : 'var(--hata)' ?>;">
                                <?= ($tutar > 0 ? '+' : '') . number_format($tutar, 2, ',', '.') ?> TL
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/sablon/alt.php'; ?>