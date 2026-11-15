<?php
declare(strict_types=1);

class MenuKontrol
{
    public static function gununMenusu(): void
    {
        $kullanici = girisGerekli();

        $yemekhaneler = MenuVeri::yemekhaneler();
        $ogunler      = MenuVeri::ogunler();

        $yemekhaneId = isset($_GET['yemekhane_id']) ? (int)$_GET['yemekhane_id'] : 1;
        $tarih       = isset($_GET['tarih']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['tarih'])
            ? $_GET['tarih']
            : date('Y-m-d');
        $ogunId      = isset($_GET['ogun_id']) ? (int)$_GET['ogun_id'] : 2; // Varsayılan: Öğle

        $yemekler = MenuVeri::gununMenusuDetay($yemekhaneId, $tarih, $ogunId);

        $toplamKalori = 0;
        $menuBilgi = null;
        $doluluk = null;

        if (!empty($yemekler)) {
            $menuBilgi = $yemekler[0];
            foreach ($yemekler as $satir) {
                $toplamKalori += (int)$satir['kalori'];
            }
            $doluluk = MenuVeri::menuDoluluk((int)$menuBilgi['menu_id']);
        }

        $sayfaBasligi = 'Günün Menüsü';
        require __DIR__ . '/../gorunum/gunun_menusu.php';
    }

    public static function haftalikMenu(): void
    {
        $kullanici = girisGerekli();

        $yemekhaneler = MenuVeri::yemekhaneler();
        $yemekhaneId  = isset($_GET['yemekhane_id']) ? (int)$_GET['yemekhane_id'] : 1;
        $baslangic    = isset($_GET['baslangic']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['baslangic'])
            ? $_GET['baslangic']
            : date('Y-m-d');
        $bitis        = date('Y-m-d', strtotime($baslangic . ' +6 days'));

        $menuler = MenuVeri::haftalikMenuler($yemekhaneId, $baslangic, $bitis, (int)$kullanici['id']);

        // Rezervasyon hemen sonrasında bakiye kaynaktan (mysql-primary) okunur
        $kaynakOku = isset($_GET['kaynak']) && $_GET['kaynak'] === '1';
        $tazeKullanici = KullaniciVeri::idIleBul((int)$kullanici['id'], $kaynakOku);
        if ($tazeKullanici) {
            oturumBakiyeGuncelle((float)$tazeKullanici['bakiye']);
            $kullanici = aktifKullanici();
        }

        $sayfaBasligi = 'Haftalık Menü ve Rezervasyon';
        require __DIR__ . '/../gorunum/haftalik_menu.php';
    }
}