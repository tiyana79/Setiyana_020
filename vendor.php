<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    aksi(function () use ($pdo) {
        $a = $_POST['aksi'];
        if ($a === 'tambah') {
            $pdo->prepare('INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES (?, ?, ?)')
                ->execute([$_POST['nama_vendor'], $_POST['badan_hukum'], $_POST['status']]);
            return 'Vendor berhasil ditambahkan.';
        }
        if ($a === 'ubah') {
            $pdo->prepare('UPDATE vendor SET nama_vendor = ?, badan_hukum = ?, status = ? WHERE idvendor = ?')
                ->execute([$_POST['nama_vendor'], $_POST['badan_hukum'], $_POST['status'], $_POST['idvendor']]);
            return 'Vendor berhasil diubah.';
        }
        if ($a === 'hapus') {
            $pdo->prepare('DELETE FROM vendor WHERE idvendor = ?')->execute([$_POST['idvendor']]);
            return 'Vendor berhasil dihapus.';
        }
        return 'Aksi tidak dikenal.';
    }, 'vendor.php');
}
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM vendor WHERE idvendor = ?'); $s->execute([$_GET['edit']]); $edit = $s->fetch();
}
$rows = $pdo->query('SELECT * FROM vendor ORDER BY idvendor')->fetchAll();
$bh = $edit['badan_hukum'] ?? 'Y'; $st = $edit['status'] ?? 'A';
$judul = 'Data Vendor';
include 'header.php';
?>
<div class="card">
  <h3><?= $edit ? 'Edit Vendor' : 'Tambah Vendor' ?></h3>
  <form method="post" class="grid">
    <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
    <?php if ($edit): ?><input type="hidden" name="idvendor" value="<?= h($edit['idvendor']) ?>"><?php endif; ?>
    <label>Nama vendor<input name="nama_vendor" required value="<?= h($edit['nama_vendor'] ?? '') ?>"></label>
    <label>Badan hukum
      <select name="badan_hukum">
        <option value="Y" <?= $bh === 'Y' ? 'selected' : '' ?>>Ya</option>
        <option value="T" <?= $bh === 'T' ? 'selected' : '' ?>>Tidak</option>
      </select></label>
    <label>Status
      <select name="status">
        <option value="A" <?= $st === 'A' ? 'selected' : '' ?>>Aktif</option>
        <option value="N" <?= $st === 'N' ? 'selected' : '' ?>>Nonaktif</option>
      </select></label>
    <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
    <?php if ($edit): ?><a class="btn abu" href="vendor.php">Batal</a><?php endif; ?>
  </form>
</div>
<div class="card">
  <table>
    <tr><th>ID</th><th>Nama Vendor</th><th>Badan Hukum</th><th>Status</th><th>Aksi</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr class="<?= $edit && $edit['idvendor'] == $r['idvendor'] ? 'sedang-edit' : '' ?>">
      <td><?= h($r['idvendor']) ?></td><td><?= h($r['nama_vendor']) ?></td>
      <td><?= $r['badan_hukum'] === 'Y' ? 'Ya' : 'Tidak' ?></td>
      <td><?= badge($r['status'] === 'A') ?></td>
      <td><a class="btn kuning kecil" href="?edit=<?= h($r['idvendor']) ?>">Edit</a> <?= tombolHapus('idvendor', $r['idvendor']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="5" class="kosong">Belum ada data.</td></tr><?php endif; ?>
  </table>
</div>
<?php include 'footer.php'; ?>