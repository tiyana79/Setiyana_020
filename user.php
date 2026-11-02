<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    aksi(function () use ($pdo) {
        $a = $_POST['aksi'];
        if ($a === 'tambah') {
            $pdo->prepare('INSERT INTO user (username, password, idrole) VALUES (?, SHA2(?, 256), ?)')
                ->execute([$_POST['username'], $_POST['password'], $_POST['idrole']]);
            return 'User berhasil ditambahkan.';
        }
        if ($a === 'ubah') {
            if ($_POST['password'] !== '') {
                $pdo->prepare('UPDATE user SET username = ?, password = SHA2(?, 256), idrole = ? WHERE iduser = ?')
                    ->execute([$_POST['username'], $_POST['password'], $_POST['idrole'], $_POST['iduser']]);
            } else {
                $pdo->prepare('UPDATE user SET username = ?, idrole = ? WHERE iduser = ?')
                    ->execute([$_POST['username'], $_POST['idrole'], $_POST['iduser']]);
            }
            return 'User berhasil diubah.';
        }
        if ($a === 'hapus') {
            $pdo->prepare('DELETE FROM user WHERE iduser = ?')->execute([$_POST['iduser']]);
            return 'User berhasil dihapus.';
        }
        return 'Aksi tidak dikenal.';
    }, 'user.php');
}
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM user WHERE iduser = ?'); $s->execute([$_GET['edit']]); $edit = $s->fetch();
}
$roles = $pdo->query('SELECT idrole, nama_role FROM role ORDER BY nama_role')->fetchAll();
$rows = $pdo->query('SELECT u.iduser, u.username, r.nama_role FROM user u JOIN role r ON u.idrole = r.idrole ORDER BY u.iduser')->fetchAll();
$judul = 'Data User';
include 'header.php';
?>
<div class="card">
  <h3><?= $edit ? 'Edit User' : 'Tambah User' ?></h3>
  <form method="post" class="grid">
    <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
    <?php if ($edit): ?><input type="hidden" name="iduser" value="<?= h($edit['iduser']) ?>"><?php endif; ?>
    <label>Username<input name="username" required value="<?= h($edit['username'] ?? '') ?>"></label>
    <label><?= $edit ? 'Password baru (kosongkan jika tidak diganti)' : 'Password' ?>
      <input name="password" type="password" <?= $edit ? '' : 'required' ?>></label>
    <label>Role
      <select name="idrole">
        <?php foreach ($roles as $r): ?>
          <option value="<?= h($r['idrole']) ?>" <?= ($edit['idrole'] ?? '') == $r['idrole'] ? 'selected' : '' ?>><?= h($r['nama_role']) ?></option>
        <?php endforeach; ?>
      </select></label>
    <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
    <?php if ($edit): ?><a class="btn abu" href="user.php">Batal</a><?php endif; ?>
  </form>
</div>
<div class="card">
  <table>
    <tr><th>ID</th><th>Username</th><th>Role</th><th>Aksi</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr class="<?= $edit && $edit['iduser'] == $r['iduser'] ? 'sedang-edit' : '' ?>">
      <td><?= h($r['iduser']) ?></td><td><?= h($r['username']) ?></td><td><?= h($r['nama_role']) ?></td>
      <td><a class="btn kuning kecil" href="?edit=<?= h($r['iduser']) ?>">Edit</a> <?= tombolHapus('iduser', $r['iduser']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="4" class="kosong">Belum ada data.</td></tr><?php endif; ?>
  </table>
</div>
<?php include 'footer.php'; ?>