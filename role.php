<?php
require 'config.php';
$msg = '';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['aksi'] === 'tambah') {
            $pdo->prepare('INSERT INTO role (nama_role) VALUES (?)')->execute([$_POST['nama_role']]);
            $msg = 'Data berhasil ditambahkan.';
        } elseif ($_POST['aksi'] === 'ubah') {
            $pdo->prepare('UPDATE role SET nama_role = ? WHERE idrole = ?')->execute([$_POST['nama_role'], $_POST['idrole']]);
            $msg = 'Data berhasil diubah.';
        } elseif ($_POST['aksi'] === 'hapus') {
            $pdo->prepare('DELETE FROM role WHERE idrole = ?')->execute([$_POST['idrole']]);
            $msg = 'Data berhasil dihapus.';
        }
    }
} catch (PDOException $e) { $msg = 'Gagal: ' . $e->getMessage(); }
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM role WHERE idrole = ?'); $s->execute([$_GET['edit']]); $edit = $s->fetch();
}
$rows = $pdo->query('SELECT * FROM role ORDER BY idrole')->fetchAll();
?>
<h2>Data Role</h2>
<?= pesan($msg) ?>
<form method="post">
  <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
  <?php if ($edit): ?><input type="hidden" name="idrole" value="<?= h($edit['idrole']) ?>"><?php endif; ?>
  <input name="nama_role" placeholder="Nama role" required value="<?= h($edit['nama_role'] ?? '') ?>">
  <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
  <?php if ($edit): ?><a class="btn" href="role.php">Batal</a><?php endif; ?>
</form>
<table>
  <tr><th>ID</th><th>Nama Role</th><th>Aksi</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr><td><?= h($r['idrole']) ?></td><td><?= h($r['nama_role']) ?></td>
      <td><a href="?edit=<?= h($r['idrole']) ?>">Edit</a> <?= tombolHapus('idrole', $r['idrole']) ?></td></tr>
  <?php endforeach; ?>
</table>
</body></html>
