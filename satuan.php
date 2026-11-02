<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    aksi(function () use ($pdo) {
        $a = $_POST['aksi'];
        if ($a === 'tambah') {
            $pdo->prepare('INSERT INTO satuan (nama_satuan, status) VALUES (?, ?)')
                ->execute([$_POST['nama_satuan'], $_POST['status']]);
            return 'Satuan berhasil ditambahkan.';
        }
        if ($a === 'ubah') {
            $pdo->prepare('UPDATE satuan SET nama_satuan = ?, status = ? WHERE idsatuan = ?')
                ->execute([$_POST['nama_satuan'], $_POST['status'], $_POST['idsatuan']]);
            return 'Satuan berhasil diubah.';
        }
        if ($a === 'hapus') {
            $pdo->prepare('DELETE FROM satuan WHERE idsatuan = ?')->execute([$_POST['idsatuan']]);
            return 'Satuan berhasil dihapus.';
        }
        return 'Aksi tidak dikenal.';
    }, 'satuan.php');
}
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM satuan WHERE idsatuan = ?'); $s->execute([$_GET['edit']]); $edit = $s->fetch();
}
$rows = $pdo->query('SELECT * FROM satuan ORDER BY idsatuan')->fetchAll();
$st = $edit['status'] ?? 1;
$judul = 'Data Satuan';
include 'header.php';
?>
<div class="card">
  <h3><?= $edit ? 'Edit Satuan' : 'Tambah Satuan' ?></h3>
  <form method="post" class="grid">
    <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
    <?php if ($edit): ?><input type="hidden" name="idsatuan" value="<?= h($edit['idsatuan']) ?>"><?php endif; ?>
    <label>Nama satuan<input name="nama_satuan" required value="<?= h($edit['nama_satuan'] ?? '') ?>"></label>
    <label>Status
      <select name="status">
        <option value="1" <?= $st == 1 ? 'selected' : '' ?>>Aktif</option>
        <option value="0" <?= $st == 0 ? 'selected' : '' ?>>Nonaktif</option>
      </select></label>
    <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
    <?php if ($edit): ?><a class="btn abu" href="satuan.php">Batal</a><?php endif; ?>
  </form>
</div>
<div class="card">
  <table>
    <tr><th>ID</th><th>Nama Satuan</th><th>Status</th><th>Aksi</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr class="<?= $edit && $edit['idsatuan'] == $r['idsatuan'] ? 'sedang-edit' : '' ?>">
      <td><?= h($r['idsatuan']) ?></td><td><?= h($r['nama_satuan']) ?></td><td><?= badge($r['status']) ?></td>
      <td><a class="btn kuning kecil" href="?edit=<?= h($r['idsatuan']) ?>">Edit</a> <?= tombolHapus('idsatuan', $r['idsatuan']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="4" class="kosong">Belum ada data.</td></tr><?php endif; ?>
  </table>
</div>
<?php include 'footer.php'; ?>