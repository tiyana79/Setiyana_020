<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    aksi(function () use ($pdo) {
        $a = $_POST['aksi'];
        if ($a === 'tambah') {
            // Memanggil STORED PROCEDURE: margin lama dinonaktifkan, margin baru aktif
            $pdo->prepare('CALL sp_tambah_margin(?, ?)')->execute([$_POST['persen'], $_POST['iduser']]);
            return 'Margin baru ditambahkan dan diaktifkan (margin lama dinonaktifkan).';
        }
        if ($a === 'ubah') {
            $pdo->prepare('UPDATE margin_penjualan SET persen = ?, status = ?, iduser = ? WHERE idmargin_penjualan = ?')
                ->execute([$_POST['persen'], $_POST['status'], $_POST['iduser'], $_POST['idmargin_penjualan']]);
            return 'Margin berhasil diubah.';
        }
        if ($a === 'hapus') {
            $pdo->prepare('DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?')->execute([$_POST['idmargin_penjualan']]);
            return 'Margin berhasil dihapus.';
        }
        return 'Aksi tidak dikenal.';
    }, 'margin.php');
}
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM margin_penjualan WHERE idmargin_penjualan = ?'); $s->execute([$_GET['edit']]); $edit = $s->fetch();
}
$users = $pdo->query('SELECT iduser, username FROM user ORDER BY username')->fetchAll();
$rows = $pdo->query('SELECT m.*, u.username FROM margin_penjualan m JOIN user u ON m.iduser = u.iduser ORDER BY m.idmargin_penjualan')->fetchAll();
$st = $edit['status'] ?? 1;
$judul = 'Margin Penjualan';
include 'header.php';
?>
<div class="card">
  <h3><?= $edit ? 'Edit Margin' : 'Tambah Margin (via stored procedure)' ?></h3>
  <form method="post" class="grid">
    <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
    <?php if ($edit): ?><input type="hidden" name="idmargin_penjualan" value="<?= h($edit['idmargin_penjualan']) ?>"><?php endif; ?>
    <label>Persen (%)<input name="persen" type="number" step="0.01" min="0" required value="<?= h($edit['persen'] ?? '') ?>"></label>
    <?php if ($edit): ?>
    <label>Status
      <select name="status">
        <option value="1" <?= $st == 1 ? 'selected' : '' ?>>Aktif</option>
        <option value="0" <?= $st == 0 ? 'selected' : '' ?>>Nonaktif</option>
      </select></label>
    <?php endif; ?>
    <label>Dibuat oleh
      <select name="iduser">
        <?php foreach ($users as $u): ?>
          <option value="<?= h($u['iduser']) ?>" <?= ($edit['iduser'] ?? '') == $u['iduser'] ? 'selected' : '' ?>><?= h($u['username']) ?></option>
        <?php endforeach; ?>
      </select></label>
    <button><?= $edit ? 'Simpan Perubahan' : 'Tambah' ?></button>
    <?php if ($edit): ?><a class="btn abu" href="margin.php">Batal</a><?php endif; ?>
  </form>
</div>
<div class="card">
  <table>
    <tr><th>ID</th><th>Persen</th><th>Status</th><th>Dibuat Oleh</th><th>Dibuat</th><th>Diubah</th><th>Aksi</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr class="<?= $edit && $edit['idmargin_penjualan'] == $r['idmargin_penjualan'] ? 'sedang-edit' : '' ?>">
      <td><?= h($r['idmargin_penjualan']) ?></td><td><?= h($r['persen']) ?>%</td><td><?= badge($r['status']) ?></td>
      <td><?= h($r['username']) ?></td><td><?= h($r['created_at']) ?></td><td><?= h($r['updated_at'] ?? '-') ?></td>
      <td><a class="btn kuning kecil" href="?edit=<?= h($r['idmargin_penjualan']) ?>">Edit</a> <?= tombolHapus('idmargin_penjualan', $r['idmargin_penjualan']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="7" class="kosong">Belum ada data.</td></tr><?php endif; ?>
  </table>
</div>
<?php include 'footer.php'; ?>