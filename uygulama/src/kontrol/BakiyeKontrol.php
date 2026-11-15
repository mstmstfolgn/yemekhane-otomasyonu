<?php
declare(strict_types=1);

class BakiyeKontrol
{
    public static function bakiyeSayfasi(): void
    {
        $kullanici = girisGerekli();
        $kaynakOku = isset($_GET['kaynak']) && $_GET['kaynak'] === '1';

        $tazeKullanici = KullaniciVeri::idIleBul((int)$kullanici['id'], $kaynakOku);
        if ($tazeKullanici) {
            oturumBakiyeGuncelle((float)$tazeKullanici['bakiye']);
            $kullanici = aktifKullanici();
        }

        $hareketler = KullaniciVeri::bakiyeHareketleri((int)$kullanici['id'], $kaynakOku);

        $sayfaBasligi = 'Bakiye ve Hareketler';
        require __DIR__ . '/../gorunum/bakiye.php';
    }

    public static function bakiyeYukle(): void
    {
        $kullanici = girisGerekli();
        $tutar = (float)str_replace(',', '.', (string)($_POST['tutar'] ?? '0'));

        try {
            $sonuc = KullaniciVeri::bakiyeYukle((int)$kullanici['id'], $tutar);
            if (isset($sonuc['bakiye'])) {
                oturumBakiyeGuncelle((float)$sonuc['bakiye']);
            }
            bildirimEkle('basari', number_format($tutar, 2, ',', '.') . ' TL bakiye başarıyla yüklendi.');
        } catch (PDOException $e) {
            bildirimEkle('hata', procedureHataMesaji($e));
        }

        yonlendir('/bakiye?kaynak=1');
    }
}