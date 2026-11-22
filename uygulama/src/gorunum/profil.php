<?php require __DIR__ . '/sablon/ust.php'; ?>

<section class="kart">
    <h1>Profil ve Alerjen Tercihleri</h1>
    <p>Seçtiğiniz alerjenleri içeren yemekler Günün Menüsü ve Haftalık Menü ekranlarında uyarı ile vurgulanır.</p>
    <div class="izgara-2" style="margin-top:0.75rem;">
        <div>
            <p><strong>Ad Soyad:</strong> <?= e($kullanici['ad'] . ' ' . $kullanici['soyad']) ?></p>
            <p><strong>E-posta:</strong> <?= e($kullanici['eposta']) ?></p>
        </div>
        <div>
            <p><strong>Rol:</strong> <?= e($kullanici['rol']) ?></p>
            <?php if (!empty($kullanici['ogrenci_no'])): ?>
                <p><strong>Öğrenci No:</strong> <?= e($kullanici['ogrenci_no']) ?></p>
            <?php endif; ?>
            <p><strong>Güncel Bakiye:</strong> <?= number_format((float)$kullanici['bakiye'], 2, ',', '.') ?> TL</p>
        </div>
    </div>
</section>

<section class="kart">
    <h2>Alerjen Seçimi</h2>
    <form method="post" action="/profil/alerjen-kaydet">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:0.6rem; margin:0.9rem 0;">
            <?php foreach ($tumAlerjenler as $alerjen): ?>
                <?php $aid = (int)$alerjen['id']; ?>
                <label style="display:flex; align-items:center; gap:0.5rem; padding:0.55rem 0.7rem; border:1px solid var(--cizgi); border-radius:var(--kose); background:var(--zemin); cursor:pointer;">
                    <input type="checkbox"
                           name="alerjenler[]"
                           value="<?= $aid ?>"
                           <?= in_array($aid, $seciliAlerjenIdler, true) ? 'checked' : '' ?>>
                    <span><?= e($alerjen['ad']) ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="dugme">Alerjen Tercihlerini Kaydet</button>
    </form>
</section>

<?php require __DIR__ . '/sablon/alt.php'; ?>