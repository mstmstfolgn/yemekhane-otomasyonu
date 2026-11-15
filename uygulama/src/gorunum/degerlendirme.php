<?php require __DIR__ . '/sablon/ust.php'; ?>

<section class="kart">
    <h1>Menü Değerlendirme</h1>
    <p style="color: var(--metin-soluk); margin-bottom: 0;">
        Yalnızca turnikeden geçiş yaparak kullandığınız öğünleri 1 ile 5 arasında puanlayabilirsiniz.
    </p>
</section>

<?php if (empty($kullanilmisMenuler)): ?>
    <section class="kart">
        <p style="margin: 0;">Değerlendirilebilecek kullanılmış bir menü kaydınız henüz bulunmuyor.</p>
    </section>
<?php else: ?>
    <div class="izgara-2">
        <?php foreach ($kullanilmisMenuler as $m): ?>
            <section class="kart">
                <h2 style="margin-bottom: 0.25rem;"><?= e($m['yemekhane']) ?> · <?= e($m['ogun']) ?></h2>
                <p style="font-size: var(--boyut-kucuk); color: var(--metin-soluk); margin-bottom: 0.9rem;">
                    Tarih: <strong><?= e($m['tarih']) ?></strong>
                    <?php if (!empty($m['kullanma_zamani'])): ?>
                        · Kullanım: <?= e(substr((string)$m['kullanma_zamani'], 0, 16)) ?>
                    <?php endif; ?>
                </p>

                <form method="post" action="/degerlendirme-kaydet">
                    <input type="hidden" name="menu_id" value="<?= (int)$m['menu_id'] ?>">

                    <div style="margin-bottom: 0.75rem;">
                        <label for="puan_<?= (int)$m['menu_id'] ?>">Puan (1–5)</label>
                        <select id="puan_<?= (int)$m['menu_id'] ?>" name="puan" required>
                            <?php $seciliPuan = (int)($m['puan'] ?? 5); ?>
                            <?php for ($p = 5; $p >= 1; $p--): ?>
                                <option value="<?= $p ?>" <?= $seciliPuan === $p ? 'selected' : '' ?>>
                                    <?= $p ?> Yıldız
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div style="margin-bottom: 0.9rem;">
                        <label for="yorum_<?= (int)$m['menu_id'] ?>">Yorum (İsteğe Bağlı)</label>
                        <textarea id="yorum_<?= (int)$m['menu_id'] ?>" name="yorum" rows="3" maxlength="500" placeholder="Yemek kalitesi ve porsiyon hakkında görüşünüz..."><?= e($m['yorum'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="dugme" style="width: 100%;">
                        <?= !empty($m['degerlendirme_id']) ? 'Değerlendirmeyi Güncelle' : 'Değerlendirmeyi Gönder' ?>
                    </button>
                </form>
            </section>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/sablon/alt.php'; ?>