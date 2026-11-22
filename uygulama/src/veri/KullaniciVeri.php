<?php
declare(strict_types=1);

class KullaniciVeri
{
    public static function epostaIleBul(string $eposta): ?array
    {
        $sql = 'SELECT id, ad, soyad, eposta, sifre_hash, rol, ogrenci_no, bakiye, aktif
                  FROM kullanicilar
                 WHERE eposta = ?
                 LIMIT 1';
        $stmt = okumaDb()->prepare($sql);
        $stmt->execute([$eposta]);
        $satir =$stmt->fetch();
        return $satir ?: null;
    }

    public static function idIleBul(int $id, bool$kaynaktan = false): ?array
    {
        $db =$kaynaktan ? yazmaDb() : okumaDb();
        $sql = 'SELECT id, ad, soyad, eposta, rol, ogrenci_no, bakiye, aktif
                  FROM kullanicilar
                 WHERE id = ?
                 LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $satir =$stmt->fetch();
        return $satir ?: null;
    }

    public static function bakiyeHareketleri(int $kullaniciId, bool$kaynaktan = false): array
    {
        $db =$kaynaktan ? yazmaDb() : okumaDb();
        $sql = 'SELECT id, tur, tutar, rezervasyon_id, aciklama, olusturma
                  FROM bakiye_hareketleri
                 WHERE kullanici_id = ?
                 ORDER BY olusturma DESC, id DESC
                 LIMIT 50';
        $stmt = $db->prepare($sql);
        $stmt->execute([$kullaniciId]);
        return $stmt->fetchAll();
    }

    public static function bakiyeYukle(int $kullaniciId, float$tutar): array
    {
        $stmt = yazmaDb()->prepare('CALL sp_bakiye_yukle(?, ?)');
        $stmt->execute([$kullaniciId, $tutar]);$sonuc = $stmt->fetch();$stmt->closeCursor();
        return $sonuc ?: [];
    }

    public static function tumAlerjenler(): array
    {
        $stmt = okumaDb()->query('SELECT id, ad FROM alerjenler ORDER BY id');
        return $stmt->fetchAll();
    }

    public static function kullaniciAlerjenIdleri(int $kullaniciId, bool$kaynaktan = false): array
    {
        $db =$kaynaktan ? yazmaDb() : okumaDb();
        $stmt =$db->prepare('SELECT alerjen_id FROM kullanici_alerjenleri WHERE kullanici_id = ? ORDER BY alerjen_id');
        $stmt->execute([$kullaniciId]);
        return array_map('intval', array_column($stmt->fetchAll(), 'alerjen_id'));
    }

    public static function kullaniciAlerjenleriKaydet(int $kullaniciId, array$alerjenIdler): void
    {
        $db = yazmaDb();$db->beginTransaction();
        try {
            $sil =$db->prepare('DELETE FROM kullanici_alerjenleri WHERE kullanici_id = ?');
            $sil->execute([$kullaniciId]);

            $ekle =$db->prepare('INSERT INTO kullanici_alerjenleri (kullanici_id, alerjen_id) VALUES (?, ?)');
            foreach (array_unique(array_map('intval', $alerjenIdler)) as$alerjenId) {
                if ($alerjenId > 0) {$ekle->execute([$kullaniciId,$alerjenId]);
                }
            }
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {$db->rollBack();
            }
            throw $e;
        }
    }
}