<?php
require 'config.php';

// Semua query membaca information_schema (katalog internal MySQL) untuk database yang sedang dipakai
$views = $pdo->query("SELECT TABLE_NAME, VIEW_DEFINITION
                    FROM information_schema.VIEWS
                    WHERE TABLE_SCHEMA = DATABASE()")->fetchAll();

$seq = $pdo->query("SELECT TABLE_NAME, AUTO_INCREMENT
                    FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE'
                    ORDER BY TABLE_NAME")->fetchAll();

$rutin = $pdo->query("SELECT ROUTINE_NAME, ROUTINE_TYPE
                    FROM information_schema.ROUTINES
                    WHERE ROUTINE_SCHEMA = DATABASE()
                    ORDER BY ROUTINE_TYPE, ROUTINE_NAME")->fetchAll();

$trigger = $pdo->query("SELECT TRIGGER_NAME, ACTION_TIMING, EVENT_MANIPULATION, EVENT_OBJECT_TABLE
                        FROM information_schema.TRIGGERS
                        WHERE TRIGGER_SCHEMA = DATABASE()
                        ORDER BY TRIGGER_NAME")->fetchAll();

$judul = 'Cek Objek Database';
include 'header.php';
?>
<div class="card">
  <h3>View</h3>
  <table>
    <tr><th>Nama View</th><th>Definisi</th></tr>
    <?php foreach ($views as $v): ?>
    <tr><td><code><?= h($v['TABLE_NAME']) ?></code></td><td><code><?= h($v['VIEW_DEFINITION']) ?></code></td></tr>
    <?php endforeach; ?>
    <?php if (!$views): ?><tr><td colspan="2" class="kosong">Belum ada view.</td></tr><?php endif; ?>
  </table>
</div>

<div class="card">
  <h3>Sequence (AUTO_INCREMENT): nomor ID berikutnya per tabel</h3>
  <table>
    <tr><th>Tabel</th><th>ID berikutnya</th></tr>
    <?php foreach ($seq as $s): ?>
    <tr><td><?= h($s['TABLE_NAME']) ?></td><td><?= h($s['AUTO_INCREMENT'] ?? '-') ?></td></tr>
    <?php endforeach; ?>
  </table>
</div>

<div class="card">
  <h3>Function dan Stored Procedure</h3>
  <table>
    <tr><th>Nama</th><th>Jenis</th></tr>
    <?php foreach ($rutin as $r): ?>
    <tr><td><code><?= h($r['ROUTINE_NAME']) ?></code></td><td><?= h($r['ROUTINE_TYPE']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$rutin): ?><tr><td colspan="2" class="kosong">Belum ada function atau procedure.</td></tr><?php endif; ?>
  </table>
</div>

<div class="card">
  <h3>Trigger</h3>
  <table>
    <tr><th>Nama</th><th>Waktu</th><th>Event</th><th>Tabel</th></tr>
    <?php foreach ($trigger as $t): ?>
    <tr><td><code><?= h($t['TRIGGER_NAME']) ?></code></td><td><?= h($t['ACTION_TIMING']) ?></td>
        <td><?= h($t['EVENT_MANIPULATION']) ?></td><td><?= h($t['EVENT_OBJECT_TABLE']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$trigger): ?><tr><td colspan="4" class="kosong">Belum ada trigger.</td></tr><?php endif; ?>
  </table>
</div>
<?php include 'footer.php'; ?>