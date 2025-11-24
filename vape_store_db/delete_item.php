<?php
require 'db.php';
if (!isset($_GET['id'])) { header('Location: items.php'); exit; }
$id = $_GET['id'];
$stmt = $mysqli->prepare("DELETE FROM items WHERE id_items=?");
$stmt->bind_param('s',$id);
$stmt->execute();
$stmt->close();
header('Location: items.php');
exit;
