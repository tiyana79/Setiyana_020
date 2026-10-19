<?php //	Koneksi database dan fungsi bantu yang dipakai semua halaman
session_start();

// Koneksi database (Laragon default: root, password kosong)
$pdo = new PDO('mysql:host=127.0.0.1;dbname=proyek_pbd;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function flash($pesan, $tipe = 'ok') { $_SESSION['flash'] = [$pesan, $tipe]; }
function ambilFlash() {
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f;
}
function redirect($url) { header('Location: ' . $url); exit; }

// Terjemahkan error MySQL yang umum jadi pesan yang mudah dibaca
function pesanError(PDOException $e) {
    $kode = $e->errorInfo[1] ?? 0;
    if ($kode == 1451) return 'Data tidak bisa dihapus karena masih dipakai tabel lain.';
    if ($kode == 1452) return 'Data referensi tidak valid (induknya tidak ditemukan).';
    if ($kode == 1062) return 'Data duplikat.';
    return $e->getMessage();
}

// Jalankan satu aksi POST, simpan pesan, lalu redirect (pola Post-Redirect-Get)
function aksi(callable $fn, $kembali) {
    try { flash($fn()); }
    catch (PDOException $e) { flash('Gagal: ' . pesanError($e), 'err'); }
    redirect($kembali);
}

function tombolHapus($field, $id) {
    return '<form method="post" class="inline" onsubmit="return confirm(\'Hapus data ini?\')">'
    . '<input type="hidden" name="aksi" value="hapus">'
    . '<input type="hidden" name="' . $field . '" value="' . h($id) . '">'
    . '<button class="btn merah kecil">Hapus</button></form>';
}
function badge($aktif, $ya = 'Aktif', $tidak = 'Nonaktif') {
    return '<span class="badge ' . ($aktif ? 'ok' : 'off') . '">' . ($aktif ? $ya : $tidak) . '</span>';
}

// Sementara, supaya halaman lama masih jalan. Hapus di tahap 5.
function pesan($m) { return $m ? '<div class="flash ok">' . h($m) . '</div>' : ''; }