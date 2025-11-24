CREATE DATABASE vape_store;
USE vape_store;

CREATE TABLE kasir 
(
	id_kasir VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(50)
);

CREATE TABLE karyawan 
(
	id_karyawan VARCHAR(20) PRIMARY KEY,
    nik VARCHAR(30),
    nama_lengkap VARCHAR(50),
    alamat VARCHAR(150),
    no_telepon VARCHAR(15)
);

CREATE TABLE operator
(
	id_operator VARCHAR(20) PRIMARY KEY,
    username VARCHAR(30),
    password VARCHAR(100),
    id_karyawan VARCHAR(20),
    FOREIGN KEY (id_karyawan) REFERENCES karyawan(id_karyawan)
);

CREATE TABLE shift 
(
	id_shift VARCHAR(20) PRIMARY KEY,
    nama_shift VARCHAR(30),
    jam_mulai TIME,
    jam_selesai TIME
);

CREATE TABLE jadwal
(
	id_jadwal VARCHAR(20) PRIMARY KEY,
    id_karyawan VARCHAR(20),
    id_shift VARCHAR(20),
    id_kasir VARCHAR(20),
    tanggal DATE,
    status_hadir VARCHAR(20),
    check_in TIME,
    check_out TIME,
    FOREIGN KEY (id_karyawan) REFERENCES karyawan(id_karyawan),
    FOREIGN KEY (id_shift) REFERENCES shift(id_shift),
    FOREIGN KEY (id_kasir) REFERENCES kasir(id_kasir)
);

CREATE TABLE items 
(
	id_transaksi VARCHAR(20) PRIMARY KEY,
    id_kasir VARCHAR(20),
    id_operator VARCHAR(20),
    tanggal DATETIME,
    disc INT,
    ppn INT,
    total INT,
    FOREIGN KEY (id_kasir) REFERENCES kasir(id_kasir),
    FOREIGN KEY (id_operator) REFERENCES operator(id_operator)
);

DROP TABLE items;

CREATE TABLE items
(
	id_items VARCHAR(20) PRIMARY KEY,
    nama_item VARCHAR(20),
    harga INT,
    stock INT
);

CREATE TABLE transaksi
(
	id_transaksi VARCHAR(20) PRIMARY KEY,
    id_kasir VARCHAR(20),
    id_operator VARCHAR(20),
    tanggal DATETIME,
    disc INT,
    ppn INT,
    total INT,
    FOREIGN KEY (id_kasir) REFERENCES kasir(id_kasir),
    FOREIGN KEY (id_operator) REFERENCES operator(id_operator)
);

CREATE TABLE transaksi_detail
(
	id_detail VARCHAR(20) PRIMARY KEY,
	id_transaksi VARCHAR(20),
	id_items VARCHAR(20),
	kuantitas INT,
	subtotal INT,
	FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
	FOREIGN KEY (id_items) REFERENCES items(id_items)
);

