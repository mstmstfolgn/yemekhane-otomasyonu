<?php
declare(strict_types=1);

class ProfilKontrol
{
    public static function profilSayfasi(): void
    {
        $kullanici = girisGerekli();
        $kaynakOku = isset($_GET['kaynak']) && $_GET['kaynak'] === '1';

        $tazeKullanici = KullaniciVeri::idIleBul((int)$kullanici['id'], $kaynakOku);
        if ($tazeKullanici) {
            oturumBakiyeGuncelle((float)$tazeKullanici['bakiye']);
            $kullanici = aktifKullanici();
        }

        $tumAlerjenler = KullaniciVeri::tumAlerjenler();
        $seciliAlerjenIdler = KullaniciVeri::kullaniciAlerjenIdleri((int)$kullanici['id'], $kaynakOku);

        $sayfaBasligi = 'Profil ve Alerjen Tercihleri';
        require __DIR__ . '/../gorunum/profil.php';
    }

    public static function alerjenKaydet(): void
    {
        $kullanici = girisGerekli();
        $secimler  = isset($_POST['alerjenler']) && is_array($_POST['alerjenler'])
            ? $_POST['alerjenler']
            : [];

        try {
            KullaniciVeri::kullaniciAlerjenleriKaydet((int)$kullanici['id'], $secimler);
            bildirimEkle('basari', 'Alerjen tercihleriniz güncellendi.');
        } catch (Throwable $e) {
            bildirimEkle('hata', 'Alerjen tercihleri kaydedilemedi: ' . $e->getMessage());
        }

        yonlendir('/profil?kaynak=1');
    }
}