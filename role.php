<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    aksi(function () use ($pdo) {
        $a = $_POST['aksi'];
        if ($a === 'tambah') {
            $pdo->prepare('INSERT INTO role (nama_role) VALUES (?)')->execute([$_POST['nama_role']]);
            return 'Role berhasil ditambahkan.';
        }
        if ($a === 'ubah') {
            $pdo->prepare('UPDATE role SET nama_role = ? WHERE idrole = ?')->execute([$_POST['nama_role'], $_POST['idrole']]);
            return 'Role berhasil diubah.';
        }
        if ($a === 'hapus') {
            $pdo->prepare('DELETE FROM role WHERE idrole = ?')->execute([$_POST['idrole']]);
            return 'Role berhasil dihapus.';
        }
        return 'Aksi tidak dikenal.';
    }, 'role.php');
}
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM role WHERE idrole = ?'); $s->execute([$_GET['edit']]); $edit = $s->fetch();
}
$rows = $pdo->query('SELECT * FROM role ORDER BY idrole')->fetchAll();
$judul = 'Data Role';
include 'header.php';
?>
<div class="card">
  <h3><?= $edit ? 'Edit Role' : 'Tambah Role' ?></h3>
  <form method="post" class="grid">
    <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
    <?php if ($edit): ?><input type="hidden" name="idrole" value="<?= h($edit['idrole']) ?>"><?php endif; ?>
    <label>Nama role<input name="nama_role" required value="<?= h($edit['nama_role'] ?? '') ?>"></label>
    <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
    <?php if ($edit): ?><a class="btn abu" href="role.php">Batal</a><?php endif; ?>
  </form>
</div>
<div class="card">
  <table>
    <tr><th>ID</th><th>Nama Role</th><th>Aksi</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr class="<?= $edit && $edit['idrole'] == $r['idrole'] ? 'sedang-edit' : '' ?>">
      <td><?= h($r['idrole']) ?></td><td><?= h($r['nama_role']) ?></td>
      <td><a class="btn kuning kecil" href="?edit=<?= h($r['idrole']) ?>">Edit</a> <?= tombolHapus('idrole', $r['idrole']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="3" class="kosong">Belum ada data.</td></tr><?php endif; ?>
  </table>
</div>
<?php include 'footer.php'; ?>