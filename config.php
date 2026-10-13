<?php
// Koneksi database (XAMPP default: user root, password kosong)
$pdo = new PDO('mysql:host=localhost;dbname=proyek_pbd;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
function tombolHapus($idField, $id) {
    return '<form method="post" style="display:inline" onsubmit="return confirm(\'Hapus data ini?\')">'
         . '<input type="hidden" name="aksi" value="hapus">'
         . '<input type="hidden" name="' . $idField . '" value="' . h($id) . '"><button>Hapus</button></form>';
}
function pesan($m) { return $m ? '<div class="msg">' . h($m) . '</div>' : ''; }
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8">
<style>
body{font-family:Arial,sans-serif;margin:30px;max-width:850px}
table{border-collapse:collapse;width:100%;margin-top:15px}
th,td{border:1px solid #ccc;padding:7px;text-align:left}th{background:#eee}
input,select{padding:6px;margin:3px 6px 3px 0}
.msg{background:#e8f5e9;padding:8px;margin:10px 0}
button,.btn{padding:6px 12px;cursor:pointer}
</style></head><body>
<p><a href="index.php">&larr; Menu</a></p>
