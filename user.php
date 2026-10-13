<?php
require 'config.php';
$msg = '';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['aksi'] === 'tambah') {
            $pdo->prepare('INSERT INTO user (username, password, idrole) VALUES (?, SHA2(?, 256), ?)')
                ->execute([$_POST['username'], $_POST['password'], $_POST['idrole']]);
            $msg = 'Data berhasil ditambahkan.';
        } elseif ($_POST['aksi'] === 'ubah') {
            if ($_POST['password'] !== '') {
                $pdo->prepare('UPDATE user SET username = ?, password = SHA2(?, 256), idrole = ? WHERE iduser = ?')
                    ->execute([$_POST['username'], $_POST['password'], $_POST['idrole'], $_POST['iduser']]);
            } else {
                $pdo->prepare('UPDATE user SET username = ?, idrole = ? WHERE iduser = ?')
                    ->execute([$_POST['username'], $_POST['idrole'], $_POST['iduser']]);
            }
            $msg = 'Data berhasil diubah.';
        } elseif ($_POST['aksi'] === 'hapus') {
            $pdo->prepare('DELETE FROM user WHERE iduser = ?')->execute([$_POST['iduser']]);
            $msg = 'Data berhasil dihapus.';
        }
    }
} catch (PDOException $e) { $msg = 'Gagal: ' . $e->getMessage(); }
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM user WHERE iduser = ?'); $s->execute([$_GET['edit']]); $edit = $s->fetch();
}
$roles = $pdo->query('SELECT idrole, nama_role FROM role')->fetchAll();
$rows = $pdo->query('SELECT u.iduser, u.username, r.nama_role FROM user u JOIN role r ON u.idrole = r.idrole ORDER BY u.iduser')->fetchAll();
?>
<h2>Data User</h2>
<?= pesan($msg) ?>
<form method="post">
  <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
  <?php if ($edit): ?><input type="hidden" name="iduser" value="<?= h($edit['iduser']) ?>"><?php endif; ?>
  <input name="username" placeholder="Username" required value="<?= h($edit['username'] ?? '') ?>">
  <input name="password" type="password" placeholder="<?= $edit ? 'Password baru (kosongkan jika tidak diganti)' : 'Password' ?>" <?= $edit ? '' : 'required' ?> size="32">
  <select name="idrole">
    <?php foreach ($roles as $r): ?>
      <option value="<?= h($r['idrole']) ?>" <?= ($edit['idrole'] ?? '') == $r['idrole'] ? 'selected' : '' ?>><?= h($r['nama_role']) ?></option>
    <?php endforeach; ?>
  </select>
  <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
  <?php if ($edit): ?><a class="btn" href="user.php">Batal</a><?php endif; ?>
</form>
<table>
  <tr><th>ID</th><th>Username</th><th>Role</th><th>Aksi</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr><td><?= h($r['iduser']) ?></td><td><?= h($r['username']) ?></td><td><?= h($r['nama_role']) ?></td>
      <td><a href="?edit=<?= h($r['iduser']) ?>">Edit</a> <?= tombolHapus('iduser', $r['iduser']) ?></td></tr>
  <?php endforeach; ?>
</table>
</body></html>
