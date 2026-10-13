<?php
require 'config.php';
$msg = '';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['aksi'] === 'tambah') {
            $pdo->prepare('INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES (?, ?, ?)')
                ->execute([$_POST['nama_vendor'], $_POST['badan_hukum'], $_POST['status']]);
            $msg = 'Data berhasil ditambahkan.';
        } elseif ($_POST['aksi'] === 'ubah') {
            $pdo->prepare('UPDATE vendor SET nama_vendor = ?, badan_hukum = ?, status = ? WHERE idvendor = ?')
                ->execute([$_POST['nama_vendor'], $_POST['badan_hukum'], $_POST['status'], $_POST['idvendor']]);
            $msg = 'Data berhasil diubah.';
        } elseif ($_POST['aksi'] === 'hapus') {
            $pdo->prepare('DELETE FROM vendor WHERE idvendor = ?')->execute([$_POST['idvendor']]);
            $msg = 'Data berhasil dihapus.';
        }
    }
} catch (PDOException $e) { $msg = 'Gagal: ' . $e->getMessage(); }
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM vendor WHERE idvendor = ?'); $s->execute([$_GET['edit']]); $edit = $s->fetch();
}
$rows = $pdo->query('SELECT * FROM vendor ORDER BY idvendor')->fetchAll();
$bh = $edit['badan_hukum'] ?? 'Y'; $st = $edit['status'] ?? 'A';
?>
<h2>Data Vendor</h2>
<?= pesan($msg) ?>
<form method="post">
  <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
  <?php if ($edit): ?><input type="hidden" name="idvendor" value="<?= h($edit['idvendor']) ?>"><?php endif; ?>
  <input name="nama_vendor" placeholder="Nama vendor" required value="<?= h($edit['nama_vendor'] ?? '') ?>">
  <select name="badan_hukum">
    <option value="Y" <?= $bh === 'Y' ? 'selected' : '' ?>>Badan hukum</option>
    <option value="T" <?= $bh === 'T' ? 'selected' : '' ?>>Bukan badan hukum</option>
  </select>
  <select name="status">
    <option value="A" <?= $st === 'A' ? 'selected' : '' ?>>Aktif</option>
    <option value="N" <?= $st === 'N' ? 'selected' : '' ?>>Nonaktif</option>
  </select>
  <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
  <?php if ($edit): ?><a class="btn" href="vendor.php">Batal</a><?php endif; ?>
</form>
<table>
  <tr><th>ID</th><th>Nama Vendor</th><th>Badan Hukum</th><th>Status</th><th>Aksi</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr><td><?= h($r['idvendor']) ?></td><td><?= h($r['nama_vendor']) ?></td>
      <td><?= $r['badan_hukum'] === 'Y' ? 'Ya' : 'Tidak' ?></td>
      <td><?= $r['status'] === 'A' ? 'Aktif' : 'Nonaktif' ?></td>
      <td><a href="?edit=<?= h($r['idvendor']) ?>">Edit</a> <?= tombolHapus('idvendor', $r['idvendor']) ?></td></tr>
  <?php endforeach; ?>
</table>
</body></html>
