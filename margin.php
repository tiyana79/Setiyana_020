<?php
require 'config.php';
$msg = '';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['aksi'] === 'tambah') {
            $pdo->prepare('INSERT INTO margin_penjualan (persen, status, iduser) VALUES (?, ?, ?)')
                ->execute([$_POST['persen'], $_POST['status'], $_POST['iduser']]);
            $msg = 'Data berhasil ditambahkan.';
        } elseif ($_POST['aksi'] === 'ubah') {
            $pdo->prepare('UPDATE margin_penjualan SET persen = ?, status = ?, iduser = ? WHERE idmargin_penjualan = ?')
                ->execute([$_POST['persen'], $_POST['status'], $_POST['iduser'], $_POST['idmargin_penjualan']]);
            $msg = 'Data berhasil diubah.';
        } elseif ($_POST['aksi'] === 'hapus') {
            $pdo->prepare('DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?')->execute([$_POST['idmargin_penjualan']]);
            $msg = 'Data berhasil dihapus.';
        }
    }
} catch (PDOException $e) { $msg = 'Gagal: ' . $e->getMessage(); }
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM margin_penjualan WHERE idmargin_penjualan = ?'); $s->execute([$_GET['edit']]); $edit = $s->fetch();
}
$users = $pdo->query('SELECT iduser, username FROM user')->fetchAll();
$rows = $pdo->query('SELECT m.*, u.username FROM margin_penjualan m JOIN user u ON m.iduser = u.iduser ORDER BY m.idmargin_penjualan')->fetchAll();
?>
<h2>Data Margin Penjualan</h2>
<?= pesan($msg) ?>
<form method="post">
<input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
<?php if ($edit): ?><input type="hidden" name="idmargin_penjualan" value="<?= h($edit['idmargin_penjualan']) ?>"><?php endif; ?>
<input name="persen" type="number" step="0.01" min="0" placeholder="Persen (%)" required value="<?= h($edit['persen'] ?? '') ?>">
<select name="status">
<option value="1" <?= ($edit['status'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif</option>
<option value="0" <?= ($edit['status'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif</option>
</select>
<select name="iduser">
<?php foreach ($users as $u): ?>
<option value="<?= h($u['iduser']) ?>" <?= ($edit['iduser'] ?? '') == $u['iduser'] ? 'selected' : '' ?>><?= h($u['username']) ?></option>
<?php endforeach; ?>
</select>
<button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
<?php if ($edit): ?><a class="btn" href="margin.php">Batal</a><?php endif; ?>
</form>
<table>
<tr><th>ID</th><th>Persen</th><th>Status</th><th>Dibuat Oleh</th><th>Dibuat</th><th>Diubah</th><th>Aksi</th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><?= h($r['idmargin_penjualan']) ?></td><td><?= h($r['persen']) ?>%</td>
<td><?= $r['status'] ? 'Aktif' : 'Nonaktif' ?></td><td><?= h($r['username']) ?></td>
<td><?= h($r['created_at']) ?></td><td><?= h($r['updated_at']) ?></td>
<td><a href="?edit=<?= h($r['idmargin_penjualan']) ?>">Edit</a> <?= tombolHapus('idmargin_penjualan', $r['idmargin_penjualan']) ?></td></tr>
<?php endforeach; ?>
</table>
</body></html>
