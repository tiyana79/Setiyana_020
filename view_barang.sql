USE proyek_pbd;

CREATE OR REPLACE VIEW v_barang AS
SELECT b.idbarang, b.jenis, b.nama, b.idsatuan, s.nama_satuan, b.harga, b.status
FROM barang b
JOIN satuan s ON b.idsatuan = s.idsatuan;
