USE db_crud_pbd;

CREATE OR REPLACE VIEW v_barang AS
SELECT b.idbarang, b.jenis, b.nama, b.idsatuan, s.nama_satuan, b.harga, b.status
FROM barang b
JOIN satuan s ON b.idsatuan = s.idsatuan;

CREATE OR REPLACE VIEW v_vendor AS
SELECT idvendor, nama_vendor, badan_hukum, status
FROM vendor;

CREATE OR REPLACE VIEW v_satuan AS
SELECT idsatuan, nama_satuan, status
FROM satuan;

CREATE OR REPLACE VIEW v_role AS
SELECT idrole, nama_role
FROM role;

CREATE OR REPLACE VIEW v_user AS
SELECT u.iduser, u.username, u.password, u.idrole, r.nama_role
FROM user u
LEFT JOIN role r ON u.idrole = r.idrole;

CREATE OR REPLACE VIEW v_margin_penjualan AS
SELECT idmargin_penjualan, persen, status, created_at, updated_at, iduser
FROM margin_penjualan;

CREATE OR REPLACE VIEW v_pengadaan AS
SELECT 
    p.idpengadaan, 
    p.timestamp, 
    p.status, 
    p.subtotal_nilai, 
    p.ppn, 
    p.total_nilai, 
    p.user_iduser, 
    u.username AS nama_user,
    p.vendor_idvendor, 
    v.nama_vendor
FROM pengadaan p
LEFT JOIN user u ON p.user_iduser = u.iduser
LEFT JOIN vendor v ON p.vendor_idvendor = v.idvendor;

CREATE OR REPLACE VIEW v_detail_pengadaan AS
SELECT 
    dp.iddetail_pengadaan, 
    dp.idpengadaan, 
    dp.idbarang, 
    b.nama AS nama_barang, 
    dp.jumlah, 
    dp.harga_satuan, 
    dp.subtotal AS sub_total
FROM detail_pengadaan dp
LEFT JOIN barang b ON dp.idbarang = b.idbarang;

CREATE OR REPLACE VIEW v_penerimaan AS
SELECT 
    pr.idpenerimaan, 
    pr.created_at, 
    pr.status, 
    pr.idpengadaan, 
    pr.iduser, 
    u.username AS nama_user
FROM penerimaan pr
LEFT JOIN user u ON pr.iduser = u.iduser;

CREATE OR REPLACE VIEW v_detail_penerimaan AS
SELECT 
    dpr.iddetail_penerimaan, 
    dpr.idpenerimaan, 
    dpr.barang_idbarang, 
    b.nama AS nama_barang, 
    dpr.jumlah_terima, 
    dpr.harga_satuan_terima, 
    dpr.subtotal_terima AS sub_total_terima
FROM detail_penerimaan dpr
LEFT JOIN barang b ON dpr.barang_idbarang = b.idbarang;

CREATE OR REPLACE VIEW v_penjualan AS
SELECT 
    p.idpenjualan, 
    p.created_at, 
    p.subtotal_nilai, 
    p.ppn, 
    p.total_nilai, 
    p.iduser, 
    u.username AS nama_user,
    p.idmargin_penjualan, 
    m.persen AS persen_margin
FROM penjualan p
LEFT JOIN user u ON p.iduser = u.iduser
LEFT JOIN margin_penjualan m ON p.idmargin_penjualan = m.idmargin_penjualan;

CREATE OR REPLACE VIEW v_detail_penjualan AS
SELECT 
    dp.iddetail_penjualan, 
    dp.penjualan_idpenjualan, 
    dp.idbarang, 
    b.nama AS nama_barang, 
    dp.jumlah, 
    dp.harga_satuan, 
    dp.subtotal AS sub_total
FROM detail_penjualan dp
LEFT JOIN barang b ON dp.idbarang = b.idbarang;

CREATE OR REPLACE VIEW v_retur AS
SELECT 
    r.idretur, 
    r.created_at, 
    r.idpenerimaan, 
    r.iduser, 
    u.username AS nama_user
FROM `retur` r
LEFT JOIN user u ON r.iduser = u.iduser;

CREATE OR REPLACE VIEW v_detail_retur AS
SELECT 
    dr.iddetail_retur, 
    dr.idretur, 
    dr.detail_penerimaan_iddetail_penerimaan, 
    dr.jumlah, 
    dr.alasan
FROM detail_retur dr;

CREATE OR REPLACE VIEW v_kartu_stok AS
SELECT 
    ks.idkartu_stok, 
    ks.created_at, 
    ks.idbarang, 
    b.nama AS nama_barang, 
    ks.jenis_transaksi, 
    ks.masuk, 
    ks.keluar, 
    ks.stock, 
    ks.idtransaksi
FROM kartu_stok ks
LEFT JOIN barang b ON ks.idbarang = b.idbarang;mysqlhr