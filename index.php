<?php //Dashboard: jumlah data tiap tabel
require 'config.php';
$jumlah = [
    'Role'   => $pdo->query('SELECT COUNT(*) FROM role')->fetchColumn(),
    'User'   => $pdo->query('SELECT COUNT(*) FROM user')->fetchColumn(),
    'Satuan' => $pdo->query('SELECT COUNT(*) FROM satuan')->fetchColumn(),
    'Vendor' => $pdo->query('SELECT COUNT(*) FROM vendor')->fetchColumn(),
    'Barang' => $pdo->query('SELECT COUNT(*) FROM barang')->fetchColumn(),
    'Margin' => $pdo->query('SELECT COUNT(*) FROM margin_penjualan')->fetchColumn(),
];
$link = ['Role'=>'role','User'=>'user','Satuan'=>'satuan','Vendor'=>'vendor','Barang'=>'barang','Margin'=>'margin'];
$judul = 'Dashboard';
include 'header.php';
?>
<div class="stat">
<?php foreach ($jumlah as $nama => $n): ?>
  <a href="<?= $link[$nama] ?>.php"><b><?= (int)$n ?></b><span><?= h($nama) ?></span></a>
<?php endforeach; ?>
</div>
<div class="card">
  <h3>Selamat datang</h3>
  <p>Pilih menu di sebelah kiri untuk mengelola data master.</p>
</div>
<?php include 'footer.php'; ?>