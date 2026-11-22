<?php require __DIR__ . '/sablon/ust.php'; ?>

<section class="kart">
    <h1>Rezervasyonlarım</h1>
    <?php if (empty($rezervasyonlar)): ?>
        <p>Henüz rezervasyonunuz bulunmuyor.</p>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Tarih &amp; Öğün</th>
                        <th>Yemekhane</th>
                        <th>Porsiyon</th>
                        <th>Ücret</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rezervasyonlar as $r): ?>
                        <tr>
                            <td>
                                <strong><?= e($r['tarih']) ?></strong> · <?= e($r['ogun']) ?><br>
                                <small style="color:var(--metin-soluk);">Son iptal: <?= e($r['rezervasyon_son']) ?></small>
                            </td>
                            <td><?= e($r['yemekhane']) ?></td>
                            <td><?= ($r['porsiyon'] ?? 'tam') === 'yarim' ? 'Yarım (%60)' : 'Tam' ?></td>
                            <td><?= number_format((float)$r['ucret'], 2, ',', '.') ?> TL</td>
                            <td>
                                <?php if ($r['durum'] === 'aktif'): ?>
                                    <span class="rozet rozet-aktif">Aktif</span>
                                <?php elseif ($r['durum'] === 'kullanildi'): ?>
                                    <span class="rozet rozet-kullanildi">Kullanıldı</span>
                                <?php else: ?>
                                    <span class="rozet rozet-iptal">İptal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$r['iptal_edilebilir'] === 1): ?>
                                    <form method="post" action="/rezervasyon-iptal">
                                        <input type="hidden" name="rezervasyon_id" value="<?= (int)$r['id'] ?>">
                                        <button type="submit" class="dugme dugme-tehlike dugme-kucuk">İptal Et</button>
                                    </form>
                                <?php else: ?>
                                    <span>—</span>
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