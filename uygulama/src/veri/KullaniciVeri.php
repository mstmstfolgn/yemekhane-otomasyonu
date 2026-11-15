<?php
declare(strict_types=1);

class KullaniciVeri
{
    /**
     * E-posta adresine göre kullanıcıyı replikadan bulur (giriş doğrulaması için).
     */
    public static function epostaIleBul(string $eposta): ?array
    {
        $sql = 'SELECT id, ad, soyad, eposta, sifre_hash, rol, ogrenci_no, bakiye, aktif
                  FROM kullanicilar
                 WHERE eposta = ?
                 LIMIT 1';
        $stmt = okumaDb()->prepare($sql);
        $stmt->execute([$eposta]);
        $satir = $stmt->fetch();
        return $satir ?: null;
    }

    /**
     * ID ile kullanıcı bilgilerini getirir.
     * Rezervasyon/yükleme hemen sonrasında güncel bakiye için $kaynaktan = true kullanılır.
     */
    public static function idIleBul(int $id, bool $kaynaktan = false): ?array
    {
        $db = $kaynaktan ? yazmaDb() : okumaDb();
        $sql = 'SELECT id, ad, soyad, eposta, rol, ogrenci_no, bakiye, aktif
                  FROM kullanicilar
                 WHERE id = ?
                 LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $satir = $stmt->fetch();
        return $satir ?: null;
    }

    /**
     * Kullanıcının son bakiye hareketlerini getirir.
     */
    public static function bakiyeHareketleri(int $kullaniciId, bool $kaynaktan = false): array
    {
        $db = $kaynaktan ? yazmaDb() : okumaDb();
        $sql = 'SELECT id, tur, tutar, rezervasyon_id, aciklama, olusturma
                  FROM bakiye_hareketleri
                 WHERE kullanici_id = ?
                 ORDER BY olusturma DESC, id DESC
                 LIMIT 50';
        $stmt = $db->prepare($sql);
        $stmt->execute([$kullaniciId]);
        return $stmt->fetchAll();
    }

    /**
     * Kaynak veritabanında sp_bakiye_yukle yordamını çağırır.
     */
    public static function bakiyeYukle(int $kullaniciId, float $tutar): array
    {
        $stmt = yazmaDb()->prepare('CALL sp_bakiye_yukle(?, ?)');
        $stmt->execute([$kullaniciId, $tutar]);
        $sonuc = $stmt->fetch();
        $stmt->closeCursor();
        return $sonuc ?: [];
    }
}