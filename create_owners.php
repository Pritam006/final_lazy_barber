<?php
require_once 'config/database.php';

$stmt = $pdo->query("SELECT u.userid, u.name as user_name, u.email, s.name as shop_name FROM USERS u JOIN SHOPS s ON u.shopid = s.shopid WHERE u.role = 'barber' AND u.is_shopowner = 1");
$owners = $stmt->fetchAll();

$hash = password_hash('password123', PASSWORD_BCRYPT);
foreach ($owners as $owner) {
    $pdo->prepare("UPDATE USERS SET password_hash = ? WHERE userid = ?")->execute([$hash, $owner['userid']]);
    echo "Shop: " . $owner['shop_name'] . " | Owner: " . $owner['user_name'] . " | Email: " . $owner['email'] . " | Password: password123\n";
}
?>
