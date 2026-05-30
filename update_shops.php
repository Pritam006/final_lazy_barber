<?php
require_once 'config/database.php';

$updates = [
    'Kings Domain Barber Shop' => ['address' => '27 Harbour View Street, Bondi Junction NSW 2022', 'suburb' => 'Bondi Junction'],
    'UNOIT Barber' => ['address' => '114 Kent Lane, Parramatta NSW 2150', 'suburb' => 'Parramatta'],
    'Surry Hills Barbers' => ['address' => '82 Cooper Street, Surry Hills NSW 2010', 'suburb' => 'Surry Hills'],
    'Adilla Barbers' => ['address' => '63 Regent Plaza Street, Liverpool NSW 2170', 'suburb' => 'Liverpool'],
    'Mens Biz Barber Shop' => ['address' => '45 Crown Exchange Road, Chatswood NSW 2067', 'suburb' => 'Chatswood'],
    'Village Barber' => ['address' => '19 Railway Terrace, Newtown NSW 2042', 'suburb' => 'Newtown'],
    'The Barberhood' => ['address' => '132 Harbour Lane, Blacktown NSW 2148', 'suburb' => 'Blacktown'],
    'Sterling Barber Co.' => ['address' => '76 Oxford Heights Street, Paddington NSW 2021', 'suburb' => 'Paddington'],
    'Boston Cut Barber Shop' => ['address' => '58 City Central Avenue, Burwood NSW 2134', 'suburb' => 'Burwood'],
    'Chicanos Barber Shop' => ['address' => '24 Riverside Crescent, Marrickville NSW 2204', 'suburb' => 'Marrickville']
];

$stmt = $pdo->prepare("UPDATE SHOPS SET address = ?, suburb = ? WHERE name = ?");
foreach ($updates as $name => $data) {
    $stmt->execute([$data['address'], $data['suburb'], $name]);
}
echo "Shop details updated successfully.";
?>
