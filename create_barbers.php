<?php
require_once 'config/database.php';

$stmt = $pdo->query("SELECT u.userid, u.name as user_name, u.email, s.name as shop_name FROM USERS u LEFT JOIN SHOPS s ON u.shopid = s.shopid WHERE u.role = 'barber' AND u.is_shopowner = 0");
$barbers = $stmt->fetchAll();

$hash = password_hash('password123', PASSWORD_BCRYPT);
foreach ($barbers as $barber) {
    $pdo->prepare("UPDATE USERS SET password_hash = ? WHERE userid = ?")->execute([$hash, $barber['userid']]);
    echo "Shop: " . ($barber['shop_name'] ?? 'None') . " | Barber: " . $barber['user_name'] . " | Email: " . $barber['email'] . " | Password: password123\n";
}
echo "Done resetting passwords for regular barbers.";
?>
