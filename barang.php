<?php //Tambah, ubah, hapus, dan tampil data
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    aksi(function () use ($pdo) {
        $a = $_POST['aksi'];
        if ($a === 'tambah') {
            $pdo->prepare('INSERT INTO barang (jenis, nama, idsatuan, status, harga) VALUES (?, ?, ?, ?, ?)')
                ->execute([$_POST['jenis'], $_POST['nama'], $_POST['idsatuan'], $_POST['status'], $_POST['harga']]);
            return 'Barang berhasil ditambahkan.';
        }
        if ($a === 'ubah') {
            $pdo->prepare('UPDATE barang SET jenis = ?, nama = ?, idsatuan = ?, status = ?, harga = ? WHERE idbarang = ?')
                ->execute([$_POST['jenis'], $_POST['nama'], $_POST['idsatuan'], $_POST['status'], $_POST['harga'], $_POST['idbarang']]);
            return 'Barang berhasil diubah.';
        }
        if ($a === 'hapus') {
            $pdo->prepare('DELETE FROM barang WHERE idbarang = ?')->execute([$_POST['idbarang']]);
            return 'Barang berhasil dihapus.';
        }
        return 'Aksi tidak dikenal.';
    }, 'barang.php');
}
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM barang WHERE idbarang = ?'); $s->execute([$_GET['edit']]); $edit = $s->fetch();
}
$satuan = $pdo->query('SELECT idsatuan, nama_satuan FROM satuan WHERE status = 1 ORDER BY nama_satuan')->fetchAll();
// Tampilan memakai VIEW v_barang
$rows = $pdo->query('SELECT * FROM v_barang ORDER BY idbarang')->fetchAll();
$jn = $edit['jenis'] ?? 'M'; $st = $edit['status'] ?? 1;
$judul = 'Data Barang';
include 'header.php';
?>
<div class="card">
<h3><?= $edit ? 'Edit Barang' : 'Tambah Barang' ?></h3>
<form method="post" class="grid">
    <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
    <?php if ($edit): ?><input type="hidden" name="idbarang" value="<?= h($edit['idbarang']) ?>"><?php endif; ?>
    <label>Jenis
    <select name="jenis">
        <option value="M" <?= $jn === 'M' ? 'selected' : '' ?>>M</option>
        <option value="N" <?= $jn === 'N' ? 'selected' : '' ?>>N</option>
    </select></label>
    <label>Nama barang<input name="nama" required value="<?= h($edit['nama'] ?? '') ?>"></label>
    <label>Satuan
    <select name="idsatuan">
        <?php foreach ($satuan as $s): ?>
        <option value="<?= h($s['idsatuan']) ?>" <?= ($edit['idsatuan'] ?? '') == $s['idsatuan'] ? 'selected' : '' ?>><?= h($s['nama_satuan']) ?></option>
        <?php endforeach; ?>
    </select></label>
    <label>Harga<input name="harga" type="number" min="0" required value="<?= h($edit['harga'] ?? '') ?>"></label>
    <label>Status
    <select name="status">
        <option value="1" <?= $st == 1 ? 'selected' : '' ?>>Aktif</option>
        <option value="0" <?= $st == 0 ? 'selected' : '' ?>>Nonaktif</option>
    </select></label>
    <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
    <?php if ($edit): ?><a class="btn abu" href="barang.php">Batal</a><?php endif; ?>
</form>
</div>
<div class="card">
<table>
    <tr><th>ID</th><th>Jenis</th><th>Nama</th><th>Satuan</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr class="<?= $edit && $edit['idbarang'] == $r['idbarang'] ? 'sedang-edit' : '' ?>">
    <td><?= h($r['idbarang']) ?></td><td><?= h($r['jenis']) ?></td><td><?= h($r['nama']) ?></td>
    <td><?= h($r['nama_satuan']) ?></td>
    <td>Rp <?= number_format($r['harga'], 0, ',', '.') ?></td>
    <td><?= badge($r['status']) ?></td>
    <td><a class="btn kuning kecil" href="?edit=<?= h($r['idbarang']) ?>">Edit</a> <?= tombolHapus('idbarang', $r['idbarang']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="7" class="kosong">Belum ada data.</td></tr><?php endif; ?>
</table>
</div>
<?php include 'footer.php'; ?>