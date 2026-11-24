<?php
/**
 * ====================================================================
 * Common Helper Functions
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 
 */

// Include database if not already loaded
require_once __DIR__ . '/../config/database.php';

/**
 * 1. Clean and sanitize user input data to prevent XSS attacks
 */
function sanitize_input($data) {
    if (is_null($data)) return '';
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * 2. Format monetary amounts as Sri Lankan Rupees (LKR)
 */
function format_currency($amount) {
    return 'LKR ' . number_format((float)$amount, 2);
}

/**
 * 3. Format standard MySQL dates (YYYY-MM-DD) into readable dates (e.g., Nov 25, 2026)
 */
function format_date($date_string) {
    if (empty($date_string) || $date_string === '0000-00-00') return '-';
    return date('M d, Y', strtotime($date_string));
}

/**
 * 4. Calculate total number of nights between check-in and check-out
 */
function calculate_nights($check_in, $check_out) {
    $d1 = new DateTime($check_in);
    $d2 = new DateTime($check_out);
    $interval = $d1->diff($d2);
    return max(1, (int)$interval->days);
}

/**
 * 5. Check if a room is available for given dates

 */
function is_room_available($pdo, $room_id, $check_in, $check_out, $exclude_booking_id = null) {
    $sql = "SELECT COUNT(*) AS conflict_count FROM bookings 
            WHERE room_id = :room_id 
              AND booking_status = 'Confirmed'
              AND check_in < :check_out 
              AND check_out > :check_in";

    // Optional: exclude current booking ID if updating/verifying a specific record
    if ($exclude_booking_id) {
        $sql .= " AND id != :exclude_id";
    }

    $stmt = $pdo->prepare($sql);
    $params = [
        ':room_id'   => $room_id,
        ':check_in'  => $check_in,
        ':check_out' => $check_out
    ];
    if ($exclude_booking_id) {
        $params[':exclude_id'] = $exclude_booking_id;
    }

    $stmt->execute($params);
    $result = $stmt->fetch();

    // Available if 0 conflicts exist
    return ((int)$result['conflict_count'] === 0);
}

/**
 * 6. Generate a unique booking reference number (e.g., CV-2026-4821)
 */
function generate_booking_reference($pdo) {
    $year = date('Y');
    do {
        $randomNumber = rand(1000, 9999);
        $reference = "CV-{$year}-{$randomNumber}";

        // Check uniqueness in database
        $stmt = $pdo->prepare("SELECT id FROM bookings WHERE booking_reference = ?");
        $stmt->execute([$reference]);
        $exists = $stmt->fetch();
    } while ($exists);

    return $reference;
}

/**
 * 7. Retrieve general Villa Information from database
 */
function get_villa_details($pdo) {
    $stmt = $pdo->query("SELECT * FROM villa_details LIMIT 1");
    $details = $stmt->fetch();
    if (!$details) {
        // Fallback default details if table is empty
        return [
            'villa_name'     => 'Coastal Villa Sanctuary',
            'tagline'        => 'Your Private Luxury Escape by the Indian Ocean',
            'description'    => 'A boutique oceanfront villa in southern Sri Lanka.',
            'address'        => 'No. 45, Beach Road, Mirissa, Sri Lanka',
            'location'       => 'Mirissa, Sri Lanka',
            'contact_number' => '+94 41 223 8899',
            'email'          => 'reservations@coastalvilla.com',
            'facilities'     => 'Swimming Pool, Free Wi-Fi, Beach Access, Restaurant, Air Conditioning, Parking',
            'facebook_link'  => '#',
            'instagram_link' => '#',
            'twitter_link'   => '#'
        ];
    }
    return $details;
}

/**
 * 8. Return styled HTML badge for booking status
 */
function get_status_badge($status) {
    switch ($status) {
        case 'Confirmed':
            return '<span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Confirmed</span>';
        case 'Pending':
            return '<span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> Pending</span>';
        case 'Cancelled':
            return '<span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Cancelled</span>';
        default:
            return '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
    }
}

/**
 * 9. Flash notification messages (stored in session and displayed once)
 */
function set_flash($type, $message) {
    $_SESSION['flash_message'] = [
        'type'    => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function display_flash() {
    if (!empty($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        $type = htmlspecialchars($flash['type']);
        $msg = htmlspecialchars($flash['message']);
        echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$msg}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        unset($_SESSION['flash_message']);
    }
}
?>