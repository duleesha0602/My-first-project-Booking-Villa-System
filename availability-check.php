<?php


header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$room_id   = (int)($_GET['room_id'] ?? 0);
$check_in  = sanitize_input($_GET['check_in'] ?? '');
$check_out = sanitize_input($_GET['check_out'] ?? '');

if ($room_id <= 0 || empty($check_in) || empty($check_out)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Missing required parameters.'
    ]);
    exit();
}

if ($check_out <= $check_in) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Check-out date must be after check-in date.'
    ]);
    exit();
}

$is_available = is_room_available($pdo, $room_id, $check_in, $check_out);
$nights = calculate_nights($check_in, $check_out);

echo json_encode([
    'status'       => 'success',
    'is_available' => $is_available,
    'nights'       => $nights,
    'message'      => $is_available ? 'Room is available for selected dates!' : 'Room is already booked for these dates.'
]);
exit();
?>