<?php
/**
 * DEKROYSHOP - Sets Category Deprecated
 * Redirects to Main Cosmetics Catalog
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . APP_URL . '/shop.php');
exit;
