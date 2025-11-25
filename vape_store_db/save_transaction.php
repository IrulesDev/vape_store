<?php
// save_transaction.php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: transaksi.php'); exit;
}

$id_transaksi = $_POST['id_transaksi'];
$id_kasir = $_POST['id_kasir'] ?: null;
$id_operator = $_POST['id_operator'] ?: null;
$disc = (int)($_POST['disc'] ?? 0);
$ppn = (int)($_POST['ppn'] ?? 0);
$cart = json_decode($_POST['cart_json'], true);

if (empty($cart)) {
    $_SESSION['error'] = "Cart kosong.";
    header('Location: transaksi.php'); exit;
}

// calculate totals
$jumlah = 0;
foreach ($cart as $item) {
    $jumlah += $item['qty'] * $item['harga'];
}
$after_disc = $jumlah - round($jumlah * $disc / 100);
$tax = round($after_disc * $ppn / 100);
$total = $after_disc + $tax;

// start transaction
$mysqli->begin_transaction();

try {
    // insert header
    $stmt = $mysqli->prepare("INSERT INTO transaksi (id_transaksi, id_kasir, id_operator, tanggal, disc, ppn, total) VALUES (?, ?, ?, NOW(), ?, ?, ?)");
    $stmt->bind_param('sssiii', $id_transaksi, $id_kasir, $id_operator, $disc, $ppn, $total);
    $stmt->execute();
    $stmt->close();

    // insert details & update stok
    foreach ($cart as $k => $it) {
        $id_items = $it['id'];
        $qty = (int)$it['qty'];
        $subtotal = $qty * $it['harga'];
        $detail_id = 'DT' . time() . rand(10,99) . $k;

        // insert detail
        $stmt = $mysqli->prepare("INSERT INTO transaksi_detail (id_detail, id_transaksi, id_items, kuantitas, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssiii', $detail_id, $id_transaksi, $id_items, $qty, $subtotal);
        $stmt->execute();
        $stmt->close();

        // reduce stock (check stok first)
        $stmt = $mysqli->prepare("SELECT stok FROM items WHERE id_items=? FOR UPDATE");
        $stmt->bind_param('s', $id_items);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$res || $res['stok'] < $qty) {
            throw new Exception("Stok tidak cukup untuk item $id_items");
        }

        $stmt = $mysqli->prepare("UPDATE items SET stok = stok - ? WHERE id_items = ?");
        $stmt->bind_param('is', $qty, $id_items);
        $stmt->execute();
        $stmt->close();
    }

    $mysqli->commit();
    // clear cart
    unset($_SESSION['cart']);
    $_SESSION['success'] = "Transaksi berhasil disimpan.";
    header('Location: transaksi.php');
    exit;

} catch (Exception $e) {
    $mysqli->rollback();
    $_SESSION['error'] = "Gagal menyimpan: " . $e->getMessage();
    header('Location: transaksi.php');
    exit;
}
