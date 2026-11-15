<?php
declare(strict_types=1);

class YoneticiKontrol
{
    public static function panel(): void
    {
        $kullanici = yoneticiGerekli();

        $yemekhaneler = MenuVeri::yemekhaneler();
        $ogunler      = MenuVeri::ogunler();
        $tumYemekler  = MenuVeri::tumYemekler();

        $filtreTarih = isset($_GET['tarih']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['tarih'])
            ? $_GET['tarih']
            : null;
        $rapor = MenuVeri::yoneticiMenuRaporu($filtreTarih);

        $duzenlenenMenu = null;
        if (isset($_GET['duzenle_id']) && (int)$_GET['duzenle_id'] > 0) {
            $duzenlenenMenu = MenuVeri::menuGetirById((int)$_GET['duzenle_id']);
        }

        $sayfaBasligi = 'Yönetici Paneli';
        require __DIR__ . '/../gorunum/yonetici.php';
    }

    public static function menuKaydet(): void
    {
        yoneticiGerekli();

        $yemekhaneId    = (int)($_POST['yemekhane_id'] ?? 0);
        $tarih          = trim((string)($_POST['tarih'] ?? ''));
        $ogunId         = (int)($_POST['ogun_id'] ?? 0);
        $ogrenciFiyati  = (float)str_replace(',', '.', (string)($_POST['ogrenci_fiyati'] ?? '-1'));
        $personelFiyati = (float)str_replace(',', '.', (string)($_POST['personel_fiyati'] ?? '-1'));
        $yemekIdler     = isset($_POST['yemek_idler']) && is_array($_POST['yemek_idler'])
            ? $_POST['yemek_idler']
            : [];

        if ($yemekhaneId <= 0 || $ogunId <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tarih)) {
            bildirimEkle('hata', 'Lütfen yemekhane, tarih ve öğün bilgilerini eksiksiz seçin.');
            yonlendir('/yonetici');
        }

        if ($ogrenciFiyati < 0 || $personelFiyati < 0) {
            bildirimEkle('hata', 'Fiyatlar 0 veya daha büyük olmalıdır.');
            yonlendir('/yonetici');
        }

        if (empty($yemekIdler)) {
            bildirimEkle('hata', 'Menüye en az bir yemek eklemelisiniz.');
            yonlendir('/yonetici');
        }

        try {
            MenuVeri::menuKaydet($yemekhaneId, $tarih, $ogunId, $ogrenciFiyati, $personelFiyati, $yemekIdler);
            bildirimEkle('basari', 'Menü başarıyla kaydedildi.');
        } catch (PDOException $e) {
            bildirimEkle('hata', procedureHataMesaji($e));
        }

        yonlendir('/yonetici');
    }
}