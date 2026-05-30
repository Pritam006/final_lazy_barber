<?php
require_once 'config/database.php';

try {
    // 1. Add column if it doesn't exist
    $stmt = $pdo->query("SHOW COLUMNS FROM `USERS` LIKE 'is_shopowner'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE `USERS` ADD `is_shopowner` BOOLEAN DEFAULT FALSE AFTER `role`");
        echo "Column is_shopowner added. \n";
    }

    // 2. Assign the first barber of each shop to be the shopowner
    // We can do this by finding the MIN(userid) for each shopid where role='barber'
    $pdo->exec("
        UPDATE USERS 
        SET is_shopowner = 1 
        WHERE userid IN (
            SELECT min_id FROM (
                SELECT MIN(userid) as min_id 
                FROM USERS 
                WHERE role = 'barber' AND shopid IS NOT NULL 
                GROUP BY shopid
            ) as subquery
        )
    ");
    
    echo "Shop owners assigned successfully.\n";

    // 3. Fetch the shop owners to display
    $stmt = $pdo->query("
        SELECT u.name, u.email, s.name as shop_name 
        FROM USERS u 
        JOIN SHOPS s ON u.shopid = s.shopid 
        WHERE u.is_shopowner = 1
    ");
    
    $owners = $stmt->fetchAll();
    echo "\nList of Shop Owners:\n";
    foreach ($owners as $owner) {
        echo "- {$owner['shop_name']}: {$owner['name']} ({$owner['email']})\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
