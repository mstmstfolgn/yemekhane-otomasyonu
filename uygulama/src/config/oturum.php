<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Oturumdaki kullanıcı bilgilerini döner; giriş yapılmamışsa null döner.
 */
function aktifKullanici(): ?array
{
    return $_SESSION['kullanici'] ?? null;
}

/**
 * Kullanıcıyı oturuma kaydeder.
 */
function oturumAc(array $kullanici): void
{
    session_regenerate_id(true);
    $_SESSION['kullanici'] = [
        'id'         => (int)$kullanici['id'],
        'ad'         => (string)$kullanici['ad'],
        'soyad'      => (string)$kullanici['soyad'],
        'eposta'     => (string)$kullanici['eposta'],
        'rol'        => (string)$kullanici['rol'],
        'ogrenci_no' => $kullanici['ogrenci_no'] ?? null,
        'bakiye'     => (float)$kullanici['bakiye'],
    ];
}

/**
 * Oturumdaki bakiye bilgisini günceller.
 */
function oturumBakiyeGuncelle(float $yeniBakiye): void
{
    if (isset($_SESSION['kullanici'])) {
        $_SESSION['kullanici']['bakiye'] = $yeniBakiye;
    }
}

/**
 * Oturumu sonlandırır.
 */
function oturumKapat(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

/**
 * Tek seferlik başarı veya hata mesajı kaydeder (Post-Redirect-Get akışı için).
 */
function bildirimEkle(string $tur, string $mesaj): void
{
    $_SESSION['bildirim'] = [
        'tur'   => $tur, // 'basari' veya 'hata'
        'mesaj' => $mesaj,
    ];
}

/**
 * Varsa kaydedilmiş tek seferlik bildirimi okur ve oturumdan siler.
 */
function bildirimAl(): ?array
{
    if (!isset($_SESSION['bildirim'])) {
        return null;
    }
    $bildirim = $_SESSION['bildirim'];
    unset($_SESSION['bildirim']);
    return $bildirim;
}

/**
 * Giriş yapmamış kullanıcıyı /giris sayfasına yönlendirir.
 */
function girisGerekli(): array
{
    $kullanici = aktifKullanici();
    if (!$kullanici) {
        bildirimEkle('hata', 'Bu sayfayı görüntülemek için giriş yapmalısınız.');
        header('Location: /giris');
        exit;
    }
    return $kullanici;
}

/**
 * Yalnızca yönetici rolündeki kullanıcıların erişimine izin verir.
 */
function yoneticiGerekli(): array
{
    $kullanici = girisGerekli();
    if ($kullanici['rol'] !== 'yonetici') {
        bildirimEkle('hata', 'Bu alana yalnızca yöneticiler erişebilir.');
        header('Location: /');
        exit;
    }
    return $kullanici;
}

/**
 * Güvenli yönlendirme yardımcısı.
 */
function yonlendir(string $yol): never
{
    header('Location: ' . $yol);
    exit;
}