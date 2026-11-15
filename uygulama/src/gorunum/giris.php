<?php require __DIR__ . '/sablon/ust.php'; ?>

<section class="kart" style="max-width: 440px; margin: 2rem auto;">
    <h1>Giriş Yap</h1>
    <p style="color: var(--metin-soluk);">Kampüs yemekhane hesabınızla oturum açın.</p>

    <form method="post" action="/giris">
        <div style="margin-bottom: 0.9rem;">
            <label for="eposta">E-posta Adresi</label>
            <input type="email" id="eposta" name="eposta" required autocomplete="email" placeholder="ornek@ktun.edu.tr">
        </div>

        <div style="margin-bottom: 1.1rem;">
            <label for="sifre">Şifre</label>
            <input type="password" id="sifre" name="sifre" required autocomplete="current-password">
        </div>

        <button type="submit" class="dugme" style="width: 100%;">Giriş Yap</button>
    </form>
</section>

<?php require __DIR__ . '/sablon/alt.php'; ?>