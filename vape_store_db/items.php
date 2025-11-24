<?php
require 'db.php';
include 'header.php';

// handle edit mode (optional read)
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $mysqli->prepare("SELECT * FROM items WHERE id_items=?");
    $stmt->bind_param('s', $_GET['edit']);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$items = $mysqli->query("SELECT * FROM items ORDER BY nama_item");
?>

<div class="d-flex justify-content-between mb-3">
  <h2>Items</h2>
  <a href="items.php?add=1" class="btn btn-primary">Tambah Item</a>
</div>

<?php if(isset($_GET['add']) || $edit): ?>
  <div class="card mb-3">
    <div class="card-body">
      <h5><?= $edit ? 'Edit Item' : 'Tambah Item'?></h5>
      <form method="post" action="save_item.php">
        <input type="hidden" name="is_edit" value="<?= $edit ? '1' : '0' ?>">
        <div class="mb-3">
          <label>ID Item</label>
          <input required class="form-control" name="id_items" <?= $edit ? 'readonly' : '' ?> value="<?= $edit['id_items'] ?? '' ?>">
        </div>
        <div class="mb-3">
          <label>Nama Item</label>
          <input required class="form-control" name="nama_item" value="<?= $edit['nama_item'] ?? '' ?>">
        </div>
        <div class="mb-3">
          <label>Harga</label>
          <input required type="number" class="form-control" name="harga" value="<?= $edit['harga'] ?? 0 ?>">
        </div>
        <div class="mb-3">
          <label>Stok</label>
          <input required type="number" class="form-control" name="stok" value="<?= $edit['stok'] ?? 0 ?>">
        </div>
        <button class="btn btn-success">Simpan</button>
        <a href="items.php" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
<?php endif; ?>

<table class="table table-striped">
  <thead>
    <tr><th>ID</th><th>Nama</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
  </thead>
  <tbody>
    <?php while($r = $items->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['id_items']) ?></td>
        <td><?= htmlspecialchars($r['nama_item']) ?></td>
        <td>Rp <?= number_format($r['harga'],0,',','.') ?></td>
        <td><?= $r['stok'] ?></td>
        <td>
          <a class="btn btn-sm btn-warning" href="items.php?edit=<?= urlencode($r['id_items']) ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="delete_item.php?id=<?= urlencode($r['id_items']) ?>" onclick="return confirm('Hapus item?')">Hapus</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include 'footer.php'; ?>
