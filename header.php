<?php //Bagian atas halaman: tag HTML, sidebar menu, judul, dan pesan sukses/gagal
$hal = basename($_SERVER['SCRIPT_NAME']);
$menu = [
    'index.php'  => 'Dashboard',
    'role.php'   => 'Role',
    'user.php'   => 'User',
    'satuan.php' => 'Satuan',
    'vendor.php' => 'Vendor',
    'barang.php' => 'Barang',
    'margin.php' => 'Margin Penjualan',
];
$f = ambilFlash();
?><!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($judul ?? 'Aplikasi PBD') ?> - Proyek PBD</title>
<link rel="stylesheet" href="style.css"></head><body>
<aside class="sidebar">
<h1>Proyek PBD</h1>
<small>Menu</small>
<?php foreach ($menu as $file => $nama): ?>
    <a href="<?= $file ?>" class="<?= $hal === $file ? 'aktif' : '' ?>"><?= $nama ?></a>
<?php endforeach; ?>
</aside>
<main class="konten">
<h2><?= h($judul ?? '') ?></h2>
<?php if ($f): ?><div class="flash <?= $f[1] ?>"><?= h($f[0]) ?></div><?php endif; ?>