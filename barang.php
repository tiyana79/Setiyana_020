<?php
require 'config.php';
$msg = '';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $aksi = $_POST['aksi'];
        if ($aksi === 'tambah') {
            $pdo->prepare('INSERT INTO barang (jenis, nama, idsatuan, status, harga) VALUES (?, ?, ?, ?, ?)')
                ->execute([$_POST['jenis'], $_POST['nama'], $_POST['idsatuan'], $_POST['status'], $_POST['harga']]);
            $msg = 'Data berhasil ditambahkan.';
        } elseif ($aksi === 'ubah') {
            $pdo->prepare('UPDATE barang SET jenis = ?, nama = ?, idsatuan = ?, status = ?, harga = ? WHERE idbarang = ?')
                ->execute([$_POST['jenis'], $_POST['nama'], $_POST['idsatuan'], $_POST['status'], $_POST['harga'], $_POST['idbarang']]);
            $msg = 'Data berhasil diubah.';
        } elseif ($aksi === 'hapus') {
            $pdo->prepare('DELETE FROM barang WHERE idbarang = ?')->execute([$_POST['idbarang']]);
            $msg = 'Data berhasil dihapus.';
        }
    }
} catch (PDOException $e) {
    $msg = 'Gagal: ' . $e->getMessage();
}
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM barang WHERE idbarang = ?');
    $s->execute([$_GET['edit']]);
    $edit = $s->fetch();
}
$satuan = $pdo->query('SELECT idsatuan, nama_satuan FROM satuan WHERE status = 1')->fetchAll();
// Tampilan memakai VIEW v_barang (lihat view_barang.sql)
$rows = $pdo->query('SELECT * FROM v_barang ORDER BY idbarang')->fetchAll();
?>
<h2>Data Barang</h2>
<?php if ($msg): ?><div class="msg"><?= h($msg) ?></div><?php endif; ?>

<form method="post">
  <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
  <?php if ($edit): ?><input type="hidden" name="idbarang" value="<?= h($edit['idbarang']) ?>"><?php endif; ?>
  <select name="jenis">
    <option value="M" <?= ($edit['jenis'] ?? '') === 'M' ? 'selected' : '' ?>>M</option>
    <option value="N" <?= ($edit['jenis'] ?? '') === 'N' ? 'selected' : '' ?>>N</option>
  </select>
  <input name="nama" placeholder="Nama barang" required value="<?= h($edit['nama'] ?? '') ?>">
  <select name="idsatuan">
    <?php foreach ($satuan as $s): ?>
      <option value="<?= h($s['idsatuan']) ?>" <?= ($edit['idsatuan'] ?? '') == $s['idsatuan'] ? 'selected' : '' ?>><?= h($s['nama_satuan']) ?></option>
    <?php endforeach; ?>
  </select>
  <input name="harga" type="number" min="0" placeholder="Harga" required value="<?= h($edit['harga'] ?? '') ?>">
  <select name="status">
    <option value="1" <?= ($edit['status'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif</option>
    <option value="0" <?= ($edit['status'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif</option>
  </select>
  <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
  <?php if ($edit): ?><a class="btn" href="barang.php">Batal</a><?php endif; ?>
</form>

<table>
  <tr><th>ID</th><th>Jenis</th><th>Nama</th><th>Satuan</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><?= h($r['idbarang']) ?></td>
    <td><?= h($r['jenis']) ?></td>
    <td><?= h($r['nama']) ?></td>
    <td><?= h($r['nama_satuan']) ?></td>
    <td><?= number_format($r['harga'], 0, ',', '.') ?></td>
    <td><?= $r['status'] ? 'Aktif' : 'Nonaktif' ?></td>
    <td>
      <a href="?edit=<?= h($r['idbarang']) ?>">Edit</a>
      <form method="post" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
        <input type="hidden" name="aksi" value="hapus">
        <input type="hidden" name="idbarang" value="<?= h($r['idbarang']) ?>">
        <button>Hapus</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</body></html>
