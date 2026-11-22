<?php
declare(strict_types=1);

class RezervasyonVeri
{
    public static function rezervasyonYap(int $kullaniciId, int $menuId, string $porsiyon = 'tam'): array
    {
        $stmt = yazmaDb()->prepare('CALL sp_rezervasyon_yap(?, ?, ?)');
        $stmt->execute([$kullaniciId, $menuId, $porsiyon]);
        $sonuc = $stmt->fetch();
        $stmt->closeCursor();
        return $sonuc ?: [];
    }

    public static function rezervasyonIptal(int $kullaniciId, int $rezervasyonId): array
    {
        $stmt = yazmaDb()->prepare('CALL sp_rezervasyon_iptal(?, ?)');
        $stmt->execute([$kullaniciId, $rezervasyonId]);
        $sonuc = $stmt->fetch();
        $stmt->closeCursor();
        return $sonuc ?: [];
    }

    public static function kullaniciRezervasyonlari(int $kullaniciId, bool $kaynaktan = false): array
    {
        $db = $kaynaktan ? yazmaDb() : okumaDb();
        $sql = 'SELECT r.id, r.menu_id, r.durum, r.porsiyon, r.ucret, r.olusturma,
                       r.kullanma_zamani, r.iptal_zamani,
                       m.tarih, yh.ad AS yemekhane, o.ad AS ogun, o.sira AS ogun_sira,
                       TIMESTAMP(m.tarih, o.son_saat) AS rezervasyon_son,
                       (r.durum = "aktif" AND NOW() < TIMESTAMP(m.tarih, o.son_saat)) AS iptal_edilebilir,
                       d.id AS degerlendirme_id, d.puan, d.yorum
                  FROM rezervasyonlar r
                  JOIN menuler m       ON m.id  = r.menu_id
                  JOIN yemekhaneler yh ON yh.id = m.yemekhane_id
                  JOIN ogunler o       ON o.id  = m.ogun_id
                  LEFT JOIN degerlendirmeler d
                         ON d.menu_id = r.menu_id AND d.kullanici_id = r.kullanici_id
                 WHERE r.kullanici_id = ?
                 ORDER BY m.tarih DESC, o.sira DESC, r.id DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute([$kullaniciId]);
        return $stmt->fetchAll();
    }

    public static function kullanilmisMenuler(int $kullaniciId): array
    {
        $sql = 'SELECT r.id AS rezervasyon_id, r.menu_id, r.kullanma_zamani,
                       m.tarih, yh.ad AS yemekhane, o.ad AS ogun,
                       d.id AS degerlendirme_id, d.puan, d.yorum
                  FROM rezervasyonlar r
                  JOIN menuler m       ON m.id  = r.menu_id
                  JOIN yemekhaneler yh ON yh.id = m.yemekhane_id
                  JOIN ogunler o       ON o.id  = m.ogun_id
                  LEFT JOIN degerlendirmeler d
                         ON d.menu_id = r.menu_id AND d.kullanici_id = r.kullanici_id
                 WHERE r.kullanici_id = ?
                   AND r.durum = "kullanildi"
                 ORDER BY m.tarih DESC, o.sira DESC';
        $stmt = okumaDb()->prepare($sql);
        $stmt->execute([$kullaniciId]);
        return $stmt->fetchAll();
    }

    public static function degerlendirmeKaydet(int $kullaniciId, int $menuId, int $puan, string $yorum): void
    {
        if ($puan < 1 || $puan > 5) {
            throw new RuntimeException('Puan 1 ile 5 arasında olmalıdır.');
        }

        $db = yazmaDb();
        $kontrol = $db->prepare(
            'SELECT id FROM rezervasyonlar WHERE kullanici_id = ? AND menu_id = ? AND durum = "kullanildi" LIMIT 1'
        );
        $kontrol->execute([$kullaniciId, $menuId]);
        if (!$kontrol->fetchColumn()) {
            throw new RuntimeException('Yalnızca kullanılmış rezervasyona ait menüler değerlendirilebilir.');
        }

        $temizYorum = trim($yorum);
        $yorumDeger = $temizYorum === '' ? null : mb_substr($temizYorum, 0, 500);

        $sql = 'INSERT INTO degerlendirmeler (kullanici_id, menu_id, puan, yorum)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    puan = VALUES(puan),
                    yorum = VALUES(yorum),
                    olusturma = NOW()';
        $stmt = $db->prepare($sql);
        $stmt->execute([$kullaniciId, $menuId, $puan, $yorumDeger]);
    }
}