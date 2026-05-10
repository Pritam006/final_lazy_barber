<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php?msg=Please sign in to secure your appointment");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerid = $_SESSION['userid'];
    $shopid = $_POST['shop_id'] ?? null;
    $barberid = $_POST['barber_id'] ?? null;
    $serviceid = $_POST['service_id'] ?? null;
    $date = $_POST['date'] ?? null;
    $time = $_POST['time'] ?? null;

    if ($barberid && $serviceid && $date && $time) {
        // Fetch service to get price and duration
        $stmt = $pdo->prepare("SELECT price_aud, duration_minutes FROM services WHERE serviceid = ?");
        $stmt->execute([$serviceid]);
        $service = $stmt->fetch();
        
        if ($service) {
            $total_price = $service['price_aud'];
            
            // Calculate end time
            $start_time_obj = new DateTime($time);
            $end_time_obj = clone $start_time_obj;
            $end_time_obj->add(new DateInterval('PT' . $service['duration_minutes'] . 'M'));
            $end_time = $end_time_obj->format('H:i:s');

            // Double Booking Check (Simple check for same time slot)
            $checkStmt = $pdo->prepare("
                SELECT appointmentid FROM appointments 
                WHERE barberid = ? AND appointment_date = ? AND time_slot = ? AND status != 'cancelled'
            ");
            $checkStmt->execute([$barberid, $date, $time]);
            
            if ($checkStmt->fetch()) {
                $_SESSION['smart_route_msg'] = "Sorry, that barber is already booked at this time. Please choose another time or barber.";
                header("Location: shop_profile.php?id=" . $shopid);
                exit;
            }

            // Insert Booking
            $insertStmt = $pdo->prepare("
                INSERT INTO appointments (customerid, barberid, serviceid, appointment_date, time_slot, total_price, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending')
            ");
            // Note: Schema uses 'time_slot' instead of 'start_time' and 'end_time' in appointments table.
            if ($insertStmt->execute([$customerid, $barberid, $serviceid, $date, $time, $total_price])) {
                $_SESSION['smart_route_msg'] = "Booking successful! You can view it in your dashboard.";
                header("Location: dashboard.php");
                exit;
            }
        }
    }
}

$_SESSION['smart_route_msg'] = "Failed to process booking. Please try again.";
header("Location: shops.php");
exit;
?>
