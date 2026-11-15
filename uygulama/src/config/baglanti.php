<?php
declare(strict_types=1);

/**
 * Kaynak (mysql-primary) yazma bağlantısı döner.
 */
function yazmaDb(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $host = getenv('DB_YAZMA_HOST') ?: 'mysql-primary';
        $port = getenv('DB_PORT') ?: '3306';
        $ad   = getenv('DB_ADI') ?: 'yemekhane';
        $kul  = getenv('DB_KULLANICI') ?: 'yemekhane_app';
        $sif  = getenv('DB_SIFRE') ?: 'Uyg_2026';

        $dsn = "mysql:host={$host};port={$port};dbname={$ad};charset=utf8mb4";
        $pdo = new PDO($dsn, $kul, $sif, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

/**
 * Replika (mysql-replica) salt-okunur okuma bağlantısı döner.
 */
function okumaDb(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $host = getenv('DB_OKUMA_HOST') ?: 'mysql-replica';
        $port = getenv('DB_PORT') ?: '3306';
        $ad   = getenv('DB_ADI') ?: 'yemekhane';
        $kul  = getenv('DB_KULLANICI') ?: 'yemekhane_app';
        $sif  = getenv('DB_SIFRE') ?: 'Uyg_2026';

        $dsn = "mysql:host={$host};port={$port};dbname={$ad};charset=utf8mb4";
        $pdo = new PDO($dsn, $kul, $sif, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

/**
 * Redis bağlantısı döner (Aşama 3 önbellek ve oturum için hazır altyapı).
 */
function redisBaglan(): Redis
{
    static $redis = null;
    if ($redis === null) {
        $host = getenv('REDIS_HOST') ?: 'redis';
        $port = (int)(getenv('REDIS_PORT') ?: 6379);

        $redis = new Redis();
        $redis->connect($host, $port, 2.0);
    }
    return $redis;
}

/**
 * Stored procedure'lerden dönen SQLSTATE 45000 (kod 1644) Türkçe mesajını ayıklar.
 * CHECK kısıtı (3819) gibi ham sistem hatalarını kullanıcıya sızdırmaz.
 */
function procedureHataMesaji(PDOException $e): string
{
    $sqlState = (string)$e->getCode();
    $driverKod = $e->errorInfo[1] ?? null;

    if ($sqlState === '45000' || $driverKod === 1644) {
        if (!empty($e->errorInfo[2])) {
            return (string)$e->errorInfo[2];
        }
        if (preg_match('/1644\s+(.*)$/u', $e->getMessage(), $eslesme)) {
            return trim($eslesme[1]);
        }
    }

    return 'İşlem sırasında beklenmeyen bir veritabanı hatası oluştu.';
}

/**
 * XSS koruması için güvenli HTML çıktısı üretir.
 */
function e(mixed $deger): string
{
    return htmlspecialchars((string)$deger, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}