<?php
session_start();
require_once 'includes/db.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(['error' => '未經授權']);
    exit;
}

$user_id = $_SESSION['user_id'];
$att_id = intval($_GET['id']);

// 查核之前到底有沒有點過讚
$stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND attraction_id = ?");
$stmt->execute([$user_id, $att_id]);

if ($stmt->fetch()) {
    // 點過讚 -> 進行取消收回讚
    $del = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND attraction_id = ?");
    $del->execute([$user_id, $att_id]);
    echo json_encode(['status' => 'removed']);
} else {
    // 沒讚過 -> 寫入建立最愛關聯
    $ins = $pdo->prepare("INSERT INTO favorites (user_id, attraction_id) VALUES (?, ?)");
    $ins->execute([$user_id, $att_id]);
    echo json_encode(['status' => 'added']);
}
exit;
?>