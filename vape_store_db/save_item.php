<?php
// save_item.php
require 'db.php';

$is_edit = ($_POST['is_edit'] ?? '0') === '1';
$id = $_POST['id_items'] ?? '';
$nama = $_POST['nama_item'] ?? '';
$harga = (int)($_POST['harga'] ?? 0);
$stok = (int)($_POST['stok'] ?? 0);

if ($is_edit) {
    $stmt = $mysqli->prepare("UPDATE items SET nama_item=?, harga=?, stok=? WHERE id_items=?");
    $stmt->bind_param('sdis', $nama, $harga, $stok, $id);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $mysqli->prepare("INSERT INTO items (id_items, nama_item, harga, stok) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssii', $id, $nama, $harga, $stok);
    $stmt->execute();
    $stmt->close();
}

header('Location: items.php');
exit;
