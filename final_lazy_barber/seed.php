<?php
// c:\Users\tprit\Downloads\final_lazy_barber\seed.php
require_once 'config/database.php';

try {
    // 1. Database Redesign
    // Create SHOPS table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `SHOPS` (
            `shopid` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `address` VARCHAR(255) NOT NULL,
            `suburb` VARCHAR(100) NOT NULL,
            `open_time` VARCHAR(100) DEFAULT '09:00 AM - 05:00 PM',
            `phone` VARCHAR(20) DEFAULT '0400 000 000',
            `is_active` BOOLEAN DEFAULT TRUE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Alter USERS table to add shopid
    // Check if shopid column exists first
    $stmt = $pdo->query("SHOW COLUMNS FROM `USERS` LIKE 'shopid'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE `USERS` ADD `shopid` INT NULL DEFAULT NULL AFTER `role`");
        $pdo->exec("ALTER TABLE `USERS` ADD CONSTRAINT `fk_user_shop` FOREIGN KEY (`shopid`) REFERENCES `SHOPS`(`shopid`) ON DELETE CASCADE");
    }

    // Alter SERVICES table to add shopid
    $stmt = $pdo->query("SHOW COLUMNS FROM `SERVICES` LIKE 'shopid'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE `SERVICES` ADD `shopid` INT NULL DEFAULT NULL AFTER `serviceid`");
        $pdo->exec("ALTER TABLE `SERVICES` ADD CONSTRAINT `fk_service_shop` FOREIGN KEY (`shopid`) REFERENCES `SHOPS`(`shopid`) ON DELETE CASCADE");
    }

    // Clear existing dummy data (except customers)
    $pdo->exec("DELETE FROM `SERVICES`");
    $pdo->exec("DELETE FROM `USERS` WHERE `role` = 'barber'");
    $pdo->exec("DELETE FROM `SHOPS`");

    // 2. Data Seeding
    $shopsData = [
        [
            'name' => 'Kings Domain Barber Shop',
            'address' => '27 Harbour View Street, Sydney NSW 2000',
            'suburb' => 'Sydney CBD',
            'barbers' => ['Johnny Russo', 'Marcus Lee', 'Kevin Parker', 'Luca Marino', 'Ibrahim Khan'],
            'services' => [
                ['name' => 'Executive Haircut', 'price' => 55],
                ['name' => 'Skin Fade', 'price' => 60],
                ['name' => 'Beard Sculpting', 'price' => 35],
                ['name' => 'Razor Shave', 'price' => 40],
                ['name' => 'Hair Wash & Style', 'price' => 20],
                ['name' => 'Facial Grooming', 'price' => 25],
            ]
        ],
        [
            'name' => 'UNOIT Barber',
            'address' => '114 Kent Lane, Sydney NSW 2000',
            'suburb' => 'Sydney CBD',
            'barbers' => ['Ali Rahman', 'Hussein Malik', 'Rami George', 'Adam Cole', 'Chris Marino'],
            'services' => [
                ['name' => 'Taper Fade', 'price' => 50],
                ['name' => 'Buzz Cut', 'price' => 30],
                ['name' => 'Beard Line-Up', 'price' => 25],
                ['name' => 'Hot Towel Shave', 'price' => 45],
                ['name' => 'Hair Styling', 'price' => 20],
                ['name' => 'Kids Haircut', 'price' => 28],
            ]
        ],
        [
            'name' => 'Surry Hills Barbers',
            'address' => '82 Cooper Street, Surry Hills NSW 2010',
            'suburb' => 'Surry Hills',
            'barbers' => ['Nick Taylor', 'George Adams', 'Harry Wilson', 'Tom Carter', 'Eli Brown'],
            'services' => [
                ['name' => 'Traditional Haircut', 'price' => 48],
                ['name' => 'Beard Trim', 'price' => 25],
                ['name' => 'Razor Fade', 'price' => 55],
                ['name' => 'Long Hair Styling', 'price' => 65],
                ['name' => 'Hot Towel Treatment', 'price' => 30],
                ['name' => 'Grooming Package', 'price' => 85],
            ]
        ],
        [
            'name' => 'Adilla Barbers',
            'address' => '63 Regent Plaza Street, Sydney NSW 2000',
            'suburb' => 'Sydney CBD',
            'barbers' => ['Moe Hassan', 'Bilal Ahmed', 'Zayn Ali', 'Kareem Yusuf', 'Faris Noor'],
            'services' => [
                ['name' => 'Skin Fade', 'price' => 55],
                ['name' => 'Zero Fade', 'price' => 58],
                ['name' => 'Beard Fade', 'price' => 30],
                ['name' => 'Shape Up', 'price' => 22],
                ['name' => 'Hair Tattoo', 'price' => 35],
                ['name' => 'Razor Finish', 'price' => 18],
            ]
        ],
        [
            'name' => 'Mens Biz Barber Shop',
            'address' => '45 Crown Exchange Road, Sydney NSW 2000',
            'suburb' => 'Sydney CBD',
            'barbers' => ['Liam Brooks', 'Josh Miller', 'Dylan Scott', 'Patrick Green', 'Ryan Cooper'],
            'services' => [
                ['name' => 'Gentleman’s Haircut', 'price' => 60],
                ['name' => 'Beard Styling', 'price' => 35],
                ['name' => 'Luxury Shave', 'price' => 50],
                ['name' => 'Facial Grooming', 'price' => 30],
                ['name' => 'Hair Styling', 'price' => 25],
                ['name' => 'Premium Groom Package', 'price' => 95],
            ]
        ],
        [
            'name' => 'Village Barber',
            'address' => '19 Railway Terrace, Newtown NSW 2042',
            'suburb' => 'Newtown',
            'barbers' => ['Ben Walker', 'Oscar Reed', 'Tyler James', 'Mason Hill', 'Jack Ryan'],
            'services' => [
                ['name' => 'Classic Men’s Cut', 'price' => 45],
                ['name' => 'Scissor Cut', 'price' => 50],
                ['name' => 'Beard Trim', 'price' => 25],
                ['name' => 'Fade Cut', 'price' => 52],
                ['name' => 'Hair Wash', 'price' => 15],
                ['name' => 'Styling Finish', 'price' => 18],
            ]
        ],
        [
            'name' => 'The Barberhood',
            'address' => '132 Harbour Lane, Sydney NSW 2000',
            'suburb' => 'Sydney CBD',
            'barbers' => ['Alex Jordan', 'Daniel Cruz', 'Steven Young', 'Marco Bell', 'Chris White'],
            'services' => [
                ['name' => 'Haircut', 'price' => 50],
                ['name' => 'Skin Fade', 'price' => 58],
                ['name' => 'Beard Trim', 'price' => 28],
                ['name' => 'Hot Towel Shave', 'price' => 45],
                ['name' => 'Buzz Cut', 'price' => 30],
                ['name' => 'Hair Styling', 'price' => 22],
            ]
        ],
        [
            'name' => 'Sterling Barber Co.',
            'address' => '76 Oxford Heights Street, Paddington NSW 2021',
            'suburb' => 'Paddington',
            'barbers' => ['Dean Foster', 'Elijah Ross', 'Nathan Cole', 'Jordan Blake', 'Cole Stevens'],
            'services' => [
                ['name' => 'Precision Cut', 'price' => 62],
                ['name' => 'Skin Fade', 'price' => 60],
                ['name' => 'Beard Sculpting', 'price' => 35],
                ['name' => 'Razor Shave', 'price' => 42],
                ['name' => 'Head Massage', 'price' => 20],
                ['name' => 'Groom Package', 'price' => 100],
            ]
        ],
        [
            'name' => 'Boston Cut Barber Shop',
            'address' => '58 City Central Avenue, Sydney NSW 2000',
            'suburb' => 'Sydney CBD',
            'barbers' => ['Ahmed Karim', 'Yusuf Ali', 'Samir Khan', 'Taha Malik', 'Omar Hassan'],
            'services' => [
                ['name' => 'Fade Haircut', 'price' => 55],
                ['name' => 'Beard Trim', 'price' => 27],
                ['name' => 'Razor Edge-Up', 'price' => 20],
                ['name' => 'Crew Cut', 'price' => 32],
                ['name' => 'Hair Design', 'price' => 38],
                ['name' => 'Styling', 'price' => 18],
            ]
        ],
        [
            'name' => 'Chicanos Barber Shop',
            'address' => '24 Riverside Crescent, Marrickville NSW 2204',
            'suburb' => 'Marrickville',
            'barbers' => ['Rico Martinez', 'Miguel Santos', 'Carlos Vega', 'Diego Ramirez', 'Tony Cruz'],
            'services' => [
                ['name' => 'Pompadour Styling', 'price' => 60],
                ['name' => 'Classic Barber Cut', 'price' => 48],
                ['name' => 'Beard Trim', 'price' => 28],
                ['name' => 'Skin Fade', 'price' => 58],
                ['name' => 'Razor Shave', 'price' => 40],
                ['name' => 'Retro Styling', 'price' => 65],
            ]
        ]
    ];

    $defaultPasswordHash = password_hash('password', PASSWORD_BCRYPT);
    $defaultDuration = 30; // 30 mins

    $shopStmt = $pdo->prepare("INSERT INTO `SHOPS` (`name`, `address`, `suburb`) VALUES (?, ?, ?)");
    $barberStmt = $pdo->prepare("INSERT INTO `USERS` (`name`, `email`, `password_hash`, `role`, `shopid`) VALUES (?, ?, ?, 'barber', ?)");
    $serviceStmt = $pdo->prepare("INSERT INTO `SERVICES` (`shopid`, `name`, `price_aud`, `duration_minutes`) VALUES (?, ?, ?, ?)");

    foreach ($shopsData as $index => $shop) {
        // Insert Shop
        $shopStmt->execute([$shop['name'], $shop['address'], $shop['suburb']]);
        $shopId = $pdo->lastInsertId();

        // Insert Barbers
        foreach ($shop['barbers'] as $barberIndex => $barberName) {
            $email = strtolower(str_replace(' ', '.', $barberName)) . "@example.com";
            $barberStmt->execute([$barberName, $email, $defaultPasswordHash, $shopId]);
        }

        // Insert Services
        foreach ($shop['services'] as $service) {
            $serviceStmt->execute([$shopId, $service['name'], $service['price'], $defaultDuration]);
        }
    }

    echo "Database redesign and seeding completed successfully.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
