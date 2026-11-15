<?php
declare(strict_types=1);

class MenuVeri
{
    public static function yemekhaneler(): array
    {
        $stmt = okumaDb()->query('SELECT id, ad, konum, kapasite FROM yemekhaneler ORDER BY id');
        return $stmt->fetchAll();
    }

    public static function ogunler(): array
    {
        $stmt = okumaDb()->query('SELECT id, ad, sira, son_saat, servis_baslangic, servis_bitis FROM ogunler ORDER BY sira');
        return $stmt->fetchAll();
    }

    public static function tumYemekler(): array
    {
        $sql = 'SELECT y.id, y.ad, y.kalori, y.vejetaryen, k.ad AS kategori, k.sira AS kategori_sira
                  FROM yemekler y
                  JOIN yemek_kategorileri k ON k.id = y.kategori_id
                 ORDER BY k.sira, y.ad';
        $stmt = okumaDb()->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Günün menüsü ekranı için v_menu_detay görünümünden yemek listesini çeker.
     */
    public static function gununMenusuDetay(int $yemekhaneId, string $tarih, int $ogunId): array
    {
        $sql = 'SELECT menu_id, tarih, yemekhane_id, yemekhane, ogun_id, ogun, ogun_sira,
                       rezervasyon_son, ogrenci_fiyati, personel_fiyati,
                       yemek_id, yemek, kategori_id, kategori, kategori_sira,
                       kalori, vejetaryen, alerjenler, alerjen_idler
                  FROM v_menu_detay
                 WHERE yemekhane_id = ? AND tarih = ? AND ogun_id = ?
                 ORDER BY kategori_sira, yemek';
        $stmt = okumaDb()->prepare($sql);
        $stmt->execute([$yemekhaneId, $tarih, $ogunId]);
        return $stmt->fetchAll();
    }

    /**
     * Menü doluluk ve boş yer bilgisini v_menu_doluluk görünümünden getirir.
     */
    public static function menuDoluluk(int $menuId): ?array
    {
        $sql = 'SELECT menu_id, tarih, yemekhane_id, yemekhane, ogun_id, ogun,
                       kapasite, rezerve, bos_yer, doluluk_yuzde, rezervasyon_son
                  FROM v_menu_doluluk
                 WHERE menu_id = ?
                 LIMIT 1';
        $stmt = okumaDb()->prepare($sql);
        $stmt->execute([$menuId]);
        $satir = $stmt->fetch();
        return $satir ?: null;
    }

    /**
     * Haftalık menü ve rezervasyon ekranı için tarih aralığındaki menüleri listeler.
     * Son saat kontrolü veritabanı saat dilimine (+03:00) göre hesaplanır.
     */
    public static function haftalikMenuler(int $yemekhaneId, string $baslangic, string $bitis, int $kullaniciId): array
    {
        $sql = 'SELECT d.menu_id, d.tarih, d.yemekhane_id, d.yemekhane,
                       d.ogun_id, d.ogun, o.sira AS ogun_sira,
                       d.kapasite, d.rezerve, d.bos_yer, d.doluluk_yuzde, d.rezervasyon_son,
                       m.ogrenci_fiyati, m.personel_fiyati,
                       (NOW() >= d.rezervasyon_son) AS suresi_gecti,
                       r.id AS rezervasyon_id, r.durum AS rezervasyon_durum,
                       (SELECT GROUP_CONCAT(y.ad ORDER BY k.sira, y.ad SEPARATOR ", ")
                          FROM menu_yemekleri my
                          JOIN yemekler y ON y.id = my.yemek_id
                          JOIN yemek_kategorileri k ON k.id = y.kategori_id
                         WHERE my.menu_id = d.menu_id) AS yemek_ozeti
                  FROM v_menu_doluluk d
                  JOIN menuler m ON m.id = d.menu_id
                  JOIN ogunler o ON o.id = d.ogun_id
                  LEFT JOIN rezervasyonlar r
                         ON r.menu_id = d.menu_id AND r.kullanici_id = ?
                 WHERE d.yemekhane_id = ?
                   AND d.tarih BETWEEN ? AND ?
                 ORDER BY d.tarih ASC, o.sira ASC';
        $stmt = okumaDb()->prepare($sql);
        $stmt->execute([$kullaniciId, $yemekhaneId, $baslangic, $bitis]);
        return $stmt->fetchAll();
    }

    /**
     * Yönetici paneli için menü bazında doluluk ve ortalama puan raporu.
     */
    public static function yoneticiMenuRaporu(?string $tarih = null): array
    {
        $kosul = '';
        $param = [];
        if ($tarih !== null && $tarih !== '') {
            $kosul = 'WHERE d.tarih = ?';
            $param[] = $tarih;
        }

        $sql = "SELECT d.menu_id, d.tarih, d.yemekhane_id, d.yemekhane,
                       d.ogun_id, d.ogun, d.kapasite, d.rezerve, d.bos_yer, d.doluluk_yuzde,
                       m.ogrenci_fiyati, m.personel_fiyati,
                       COALESCE(p.oy_sayisi, 0) AS oy_sayisi,
                       p.ortalama_puan
                  FROM v_menu_doluluk d
                  JOIN menuler m ON m.id = d.menu_id
                  JOIN ogunler o ON o.id = d.ogun_id
                  LEFT JOIN v_menu_puan p ON p.menu_id = d.menu_id
                  {$kosul}
                 ORDER BY d.tarih DESC, d.yemekhane_id ASC, o.sira ASC
                 LIMIT 60";
        $stmt = okumaDb()->prepare($sql);
        $stmt->execute($param);
        return $stmt->fetchAll();
    }

    /**
     * Düzenleme formu için tek bir menüyü ve seçili yemek ID'lerini getirir.
     */
    public static function menuGetirById(int $menuId): ?array
    {
        $sql = 'SELECT m.id, m.yemekhane_id, m.tarih, m.ogun_id, m.ogrenci_fiyati, m.personel_fiyati,
                       yh.ad AS yemekhane, o.ad AS ogun
                  FROM menuler m
                  JOIN yemekhaneler yh ON yh.id = m.yemekhane_id
                  JOIN ogunler o ON o.id = m.ogun_id
                 WHERE m.id = ?
                 LIMIT 1';
        $stmt = okumaDb()->prepare($sql);
        $stmt->execute([$menuId]);
        $menu = $stmt->fetch();
        if (!$menu) {
            return null;
        }

        $yStmt = okumaDb()->prepare('SELECT yemek_id FROM menu_yemekleri WHERE menu_id = ?');
        $yStmt->execute([$menuId]);
        $menu['yemek_idler'] = array_map('intval', array_column($yStmt->fetchAll(), 'yemek_id'));

        return $menu;
    }

    /**
     * Yönetici panelinden yeni menü ekler veya mevcut (yemekhane + tarih + öğün) menüsünü günceller.
     * Tüm yazma işlemleri mysql-primary üzerinde gerçekleşir.
     */
    public static function menuKaydet(
        int $yemekhaneId,
        string $tarih,
        int $ogunId,
        float $ogrenciFiyati,
        float $personelFiyati,
        array $yemekIdler
    ): int {
        $db = yazmaDb();
        $db->beginTransaction();
        try {
            $bulStmt = $db->prepare(
                'SELECT id FROM menuler WHERE yemekhane_id = ? AND tarih = ? AND ogun_id = ? LIMIT 1'
            );
            $bulStmt->execute([$yemekhaneId, $tarih, $ogunId]);
            $mevcutId = $bulStmt->fetchColumn();

            if ($mevcutId) {
                $menuId = (int)$mevcutId;
                $upd = $db->prepare(
                    'UPDATE menuler SET ogrenci_fiyati = ?, personel_fiyati = ? WHERE id = ?'
                );
                $upd->execute([$ogrenciFiyati, $personelFiyati, $menuId]);
            } else {
                $ins = $db->prepare(
                    'INSERT INTO menuler (yemekhane_id, tarih, ogun_id, ogrenci_fiyati, personel_fiyati)
                     VALUES (?, ?, ?, ?, ?)'
                );
                $ins->execute([$yemekhaneId, $tarih, $ogunId, $ogrenciFiyati, $personelFiyati]);
                $menuId = (int)$db->lastInsertId();
            }

            $sil = $db->prepare('DELETE FROM menu_yemekleri WHERE menu_id = ?');
            $sil->execute([$menuId]);

            $ekle = $db->prepare('INSERT INTO menu_yemekleri (menu_id, yemek_id) VALUES (?, ?)');
            foreach (array_unique(array_map('intval', $yemekIdler)) as $yemekId) {
                if ($yemekId > 0) {
                    $ekle->execute([$menuId, $yemekId]);
                }
            }

            $db->commit();
            return $menuId;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}