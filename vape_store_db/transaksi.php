<?php
// transaksi.php
session_start();
require 'config.php';
include 'header.php';

// add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id = $_POST['id_items'];
    $qty = max(1, (int)$_POST['qty']);
    // load item
    $stmt = $mysqli->prepare("SELECT id_items, nama_item, harga, stok FROM items WHERE id_items=?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($res) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]['qty'] += $qty;
        else $_SESSION['cart'][$id] = ['id'=>$id,'nama'=>$res['nama_item'],'harga'=>$res['harga'],'qty'=>$qty,'stok'=>$res['stok']];
    }
    header('Location: transaksi.php');
    exit;
}

// remove from cart
if (isset($_GET['remove'])) {
    $rm = $_GET['remove'];
    if (isset($_SESSION['cart'][$rm])) unset($_SESSION['cart'][$rm]);
    header('Location: transaksi.php');
    exit;
}

// fetch items for add form
$items = $mysqli->query("SELECT * FROM items WHERE stok>0 ORDER BY nama_item");

?>
<h2>Transaksi Penjualan</h2>

<div class="row">
  <div class="col-md-6">
    <div class="card mb-3">
      <div class="card-body">
        <h5>Tambah ke Cart</h5>
        <form method="post">
          <div class="mb-3">
            <label>Item</label>
            <select name="id_items" class="form-control" required>
              <?php while($it = $items->fetch_assoc()): ?>
                <option value="<?= $it['id_items'] ?>"><?= htmlspecialchars($it['nama_item']) ?> (Rp <?= number_format($it['harga'],0,',','.') ?>) — stok <?= $it['stok'] ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Qty</label>
            <input type="number" name="qty" class="form-control" value="1" min="1">
          </div>
          <button name="add_to_cart" class="btn btn-primary">Tambah</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card mb-3">
      <div class="card-body">
        <h5>Cart</h5>
        <?php $cart = $_SESSION['cart'] ?? []; ?>
        <?php if (empty($cart)): ?>
          <p>Cart kosong</p>
        <?php else: ?>
          <table class="table">
            <thead><tr><th>Nama</th><th>Qty</th><th>Harga</th><th>Subtotal</th><th></th></tr></thead>
            <tbody>
              <?php $total=0; foreach($cart as $c): $sub=$c['qty']*$c['harga']; $total += $sub; ?>
                <tr>
                  <td><?= htmlspecialchars($c['nama']) ?></td>
                  <td><?= $c['qty'] ?></td>
                  <td>Rp <?= number_format($c['harga'],0,',','.') ?></td>
                  <td>Rp <?= number_format($sub,0,',','.') ?></td>
                  <td><a href="transaksi.php?remove=<?= urlencode($c['id']) ?>" class="btn btn-sm btn-danger">X</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <form action="save_transaction.php" method="post">
            <div class="mb-3">
              <label>ID Transaksi</label>
              <input class="form-control" name="id_transaksi" required value="TRX<?= time() ?>">
            </div>
            <div class="mb-3">
              <label>Kasir (id_kasir)</label>
              <input class="form-control" name="id_kasir" value="K1" required>
            </div>
            <div class="mb-3">
              <label>Operator (id_operator)</label>
              <input class="form-control" name="id_operator" value="OP1">
            </div>
            <div class="mb-3">
              <label>Diskon (%)</label>
              <input type="number" class="form-control" name="disc" value="0">
            </div>
            <div class="mb-3">
              <label>PPN (%)</label>
              <input type="number" class="form-control" name="ppn" value="10">
            </div>
            <div class="mb-3">
              <strong>Total: Rp <?= number_format($total,0,',','.') ?></strong>
            </div>
            <input type="hidden" name="cart_json" value="<?= htmlspecialchars(json_encode($cart), ENT_QUOTES) ?>">
            <button class="btn btn-success">Simpan Transaksi</button>
          </form>

        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
