<?php
declare(strict_types=1);

class RezervasyonKontrol
{
    public static function rezervasyonYap(): void
    {
        $kullanici   = girisGerekli();
        $menuId      = (int)($_POST['menu_id'] ?? 0);
        $porsiyon    = (string)($_POST['porsiyon'] ?? 'tam');
        $yemekhaneId = (int)($_POST['yemekhane_id'] ?? 1);
        $baslangic   = (string)($_POST['baslangic'] ?? date('Y-m-d'));

        try {
            $sonuc = RezervasyonVeri::rezervasyonYap((int)$kullanici['id'], $menuId, $porsiyon);
            if (isset($sonuc['bakiye'])) {
                oturumBakiyeGuncelle((float)$sonuc['bakiye']);
            }
            $ucret = number_format((float)($sonuc['ucret'] ?? 0), 2, ',', '.');
            $porsiyonEtiket = ($sonuc['porsiyon'] ?? $porsiyon) === 'yarim' ? 'Yarım porsiyon' : 'Tam porsiyon';
            bildirimEkle('basari', "Rezervasyonunuz oluşturuldu ({$porsiyonEtiket} · {$ucret} TL).");
        } catch (PDOException $e) {
            bildirimEkle('hata', procedureHataMesaji($e));
        }

        $sorgu = http_build_query([
            'yemekhane_id' => $yemekhaneId,
            'baslangic'    => $baslangic,
            'kaynak'       => '1',
        ]);
        yonlendir('/haftalik-menu?' . $sorgu);
    }

    public static function rezervasyonlarim(): void
    {
        $kullanici = girisGerekli();
        $kaynakOku = isset($_GET['kaynak']) && $_GET['kaynak'] === '1';

        $tazeKullanici = KullaniciVeri::idIleBul((int)$kullanici['id'], $kaynakOku);
        if ($tazeKullanici) {
            oturumBakiyeGuncelle((float)$tazeKullanici['bakiye']);
            $kullanici = aktifKullanici();
        }

        $rezervasyonlar = RezervasyonVeri::kullaniciRezervasyonlari((int)$kullanici['id'], $kaynakOku);

        $sayfaBasligi = 'Rezervasyonlarım';
        require __DIR__ . '/../gorunum/rezervasyonlarim.php';
    }

    public static function rezervasyonIptal(): void
    {
        $kullanici     = girisGerekli();
        $rezervasyonId = (int)($_POST['rezervasyon_id'] ?? 0);

        try {
            $sonuc = RezervasyonVeri::rezervasyonIptal((int)$kullanici['id'], $rezervasyonId);
            if (isset($sonuc['bakiye'])) {
                oturumBakiyeGuncelle((float)$sonuc['bakiye']);
            }
            $iade = number_format((float)($sonuc['iade'] ?? 0), 2, ',', '.');
            bildirimEkle('basari', "Rezervasyon iptal edildi, {$iade} TL bakiyenize iade edildi.");
        } catch (PDOException $e) {
            bildirimEkle('hata', procedureHataMesaji($e));
        }

        yonlendir('/rezervasyonlarim?kaynak=1');
    }

    public static function degerlendirmeSayfasi(): void
    {
        $kullanici = girisGerekli();
        $kullanilmisMenuler = RezervasyonVeri::kullanilmisMenuler((int)$kullanici['id']);

        $sayfaBasligi = 'Menü Değerlendirme';
        require __DIR__ . '/../gorunum/degerlendirme.php';
    }

    public static function degerlendirmeKaydet(): void
    {
        $kullanici = girisGerekli();
        $menuId    = (int)($_POST['menu_id'] ?? 0);
        $puan      = (int)($_POST['puan'] ?? 0);
        $yorum     = (string)($_POST['yorum'] ?? '');

        try {
            RezervasyonVeri::degerlendirmeKaydet((int)$kullanici['id'], $menuId, $puan, $yorum);
            bildirimEkle('basari', 'Değerlendirmeniz kaydedildi.');
        } catch (RuntimeException $e) {
            bildirimEkle('hata', $e->getMessage());
        } catch (PDOException $e) {
            bildirimEkle('hata', procedureHataMesaji($e));
        }

        yonlendir('/degerlendirme');
    }
}