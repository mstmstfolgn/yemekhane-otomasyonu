<?php
declare(strict_types=1);

class GirisKontrol
{
    public static function girisFormu(): void
    {
        if (aktifKullanici() !== null) {
            yonlendir('/');
        }
        $sayfaBasligi = 'Giriş Yap';
        require __DIR__ . '/../gorunum/giris.php';
    }

    public static function girisYap(): void
    {
        $eposta = trim((string)($_POST['eposta'] ?? ''));
        $sifre  = (string)($_POST['sifre'] ?? '');

        if ($eposta === '' || $sifre === '') {
            bildirimEkle('hata', 'E-posta ve şifre alanları zorunludur.');
            yonlendir('/giris');
        }

        $kullanici = KullaniciVeri::epostaIleBul($eposta);
        if (!$kullanici || !password_verify($sifre, $kullanici['sifre_hash'])) {
            bildirimEkle('hata', 'E-posta adresi veya şifre hatalı.');
            yonlendir('/giris');
        }

        if ((int)$kullanici['aktif'] !== 1) {
            bildirimEkle('hata', 'Hesabınız pasif durumdadır.');
            yonlendir('/giris');
        }

        oturumAc($kullanici);
        bildirimEkle('basari', 'Hoş geldiniz, ' . $kullanici['ad'] . ' ' . $kullanici['soyad'] . '.');
        yonlendir('/');
    }

    public static function cikisYap(): void
    {
        oturumKapat();
        session_start();
        bildirimEkle('basari', 'Başarıyla çıkış yaptınız.');
        yonlendir('/giris');
    }
}