<?php
require_once 'db.php';

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'all';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;

$emails = [];

if ($mode === 'random' && $limit > 0) {
    // 隨機寄送幾筆名單
    $query = "SELECT email FROM emails ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // 全部名單寄送
    $query = "SELECT email FROM emails ORDER BY id ASC";
    $result = $conn->query($query);
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }
}

header('Content-Type: application/json');
echo json_encode($emails);
$conn->close();
?>