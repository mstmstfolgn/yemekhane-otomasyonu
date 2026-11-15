<?php require __DIR__ . '/sablon/ust.php'; ?>

<section class="kart">
    <h1>Rezervasyonlarım</h1>
    <p style="color: var(--metin-soluk); margin-bottom: 1rem;">
        Aktif rezervasyonlarınızı son saati geçmeden iptal edebilir, kullanılmış öğünlerinizi değerlendirebilirsiniz.
    </p>

    <?php if (empty($rezervasyonlar)): ?>
        <p style="margin-bottom: 1rem;">Henüz hiç rezervasyonunuz bulunmuyor.</p>
        <a class="dugme" href="/haftalik-menu">Haftalık Menüden Rezervasyon Yap</a>
    <?php else: ?>
        <div class="tablo-kaydir">
            <table>
                <thead>
                    <tr>
                        <th>Tarih / Öğün</th>
                        <th>Yemekhane</th>
                        <th>Ücret</th>
                        <th>Durum</th>
                        <th>Son İptal Saati</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rezervasyonlar as $r): ?>
                        <?php
                            $durum = (string)$r['durum'];
                            $iptalEdilebilir = ((int)$r['iptal_edilebilir'] === 1);
                        ?>
                        <tr>
                            <td>
                                <strong><?= e($r['tarih']) ?></strong><br>
                                <span class="rozet"><?= e($r['ogun']) ?></span>
                            </td>
                            <td><?= e($r['yemekhane']) ?></td>
                            <td><strong><?= number_format((float)$r['ucret'], 2, ',', '.') ?> TL</strong></td>
                            <td>
                                <?php if ($durum === 'aktif'): ?>
                                    <span class="rozet rozet-aktif">Aktif</span>
                                <?php elseif ($durum === 'kullanildi'): ?>
                                    <span class="rozet rozet-kullanildi">Kullanıldı</span>
                                    <?php if (!empty($r['kullanma_zamani'])): ?>
                                        <div style="font-size: 0.78rem; color: var(--metin-soluk); margin-top: 0.2rem;">
                                            <?= e(substr((string)$r['kullanma_zamani'], 0, 16)) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="rozet rozet-iptal">İptal Edildi</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: var(--boyut-kucuk);">
                                <?= e(substr((string)$r['rezervasyon_son'], 0, 16)) ?>
                            </td>
                            <td>
                                <?php if ($durum === 'aktif' && $iptalEdilebilir): ?>
                                    <form method="post" action="/rezervasyon-iptal" style="margin: 0;" onsubmit="return confirm('Bu rezervasyonu iptal etmek istediğinize emin misiniz?');">
                                        <input type="hidden" name="rezervasyon_id" value="<?= (int)$r['id'] ?>">
                                        <button type="submit" class="dugme dugme-tehlike dugme-kucuk">İptal Et</button>
                                    </form>
                                <?php elseif ($durum === 'aktif'): ?>
                                    <button type="button" class="dugme dugme-kucuk" disabled>İptal Süresi Doldu</button>
                                <?php elseif ($durum === 'kullanildi'): ?>
                                    <?php if (!empty($r['degerlendirme_id'])): ?>
                                        <span class="rozet">Puanınız: <?= (int)$r['puan'] ?>/5</span>
                                    <?php else: ?>
                                        <a class="dugme dugme-ikincil dugme-kucuk" href="/degerlendirme">Değerlendir</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: var(--metin-soluk); font-size: var(--boyut-kucuk);">—</span>
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