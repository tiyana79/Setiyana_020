<?php
require 'config.php';
$msg = '';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $aksi = $_POST['aksi'];
        if ($aksi === 'tambah') {
            $pdo->prepare('INSERT INTO satuan (nama_satuan, status) VALUES (?, ?)')
                ->execute([$_POST['nama_satuan'], $_POST['status']]);
            $msg = 'Data berhasil ditambahkan.';
        } elseif ($aksi === 'ubah') {
            $pdo->prepare('UPDATE satuan SET nama_satuan = ?, status = ? WHERE idsatuan = ?')
                ->execute([$_POST['nama_satuan'], $_POST['status'], $_POST['idsatuan']]);
            $msg = 'Data berhasil diubah.';
        } elseif ($aksi === 'hapus') {
            $pdo->prepare('DELETE FROM satuan WHERE idsatuan = ?')->execute([$_POST['idsatuan']]);
            $msg = 'Data berhasil dihapus.';
        }
    }
} catch (PDOException $e) {
    $msg = 'Gagal: ' . $e->getMessage();
}
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM satuan WHERE idsatuan = ?');
    $s->execute([$_GET['edit']]);
    $edit = $s->fetch();
}
$rows = $pdo->query('SELECT * FROM satuan ORDER BY idsatuan')->fetchAll();
?>
<h2>Data Satuan</h2>
<?php if ($msg): ?><div class="msg"><?= h($msg) ?></div><?php endif; ?>

<form method="post">
  <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
  <?php if ($edit): ?><input type="hidden" name="idsatuan" value="<?= h($edit['idsatuan']) ?>"><?php endif; ?>
  <input name="nama_satuan" placeholder="Nama satuan" required value="<?= h($edit['nama_satuan'] ?? '') ?>">
  <select name="status">
    <option value="1" <?= ($edit['status'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif</option>
    <option value="0" <?= ($edit['status'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif</option>
  </select>
  <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
  <?php if ($edit): ?><a class="btn" href="satuan.php">Batal</a><?php endif; ?>
</form>

<table>
  <tr><th>ID</th><th>Nama Satuan</th><th>Status</th><th>Aksi</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><?= h($r['idsatuan']) ?></td>
    <td><?= h($r['nama_satuan']) ?></td>
    <td><?= $r['status'] ? 'Aktif' : 'Nonaktif' ?></td>
    <td>
      <a href="?edit=<?= h($r['idsatuan']) ?>">Edit</a>
      <form method="post" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
        <input type="hidden" name="aksi" value="hapus">
        <input type="hidden" name="idsatuan" value="<?= h($r['idsatuan']) ?>">
        <button>Hapus</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</body></html>
