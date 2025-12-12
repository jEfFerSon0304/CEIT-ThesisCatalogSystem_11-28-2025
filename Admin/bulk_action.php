<?php
include "../PHP/db_connect.php";
header("Content-Type: application/json");

// action: delete|available|unavailable
// type: thesis|librarians|borrow_requests
$action = $_POST['action'] ?? '';
$ids = $_POST['ids'] ?? [];
$type = $_POST['type'] ?? 'thesis';

if (!$action || empty($ids)) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$ids = array_map('intval', $ids);
$in = implode(',', $ids);

if ($action === "delete") {
    if ($type === 'thesis') {
        $sql = "DELETE FROM tbl_thesis WHERE thesis_id IN ($in)";
    } elseif ($type === 'librarians') {
        $sql = "DELETE FROM tbl_librarians WHERE librarian_id IN ($in)";
    } elseif ($type === 'borrow_requests') {
        $sql = "DELETE FROM tbl_borrow_requests WHERE request_id IN ($in)";
    } else {
        echo json_encode(["success" => false, "message" => "Invalid type"]);
        exit;
    }
} elseif ($action === "available") {
    if ($type === 'thesis') {
        $sql = "UPDATE tbl_thesis SET availability='Available' WHERE thesis_id IN ($in)";
    } else {
        echo json_encode(["success" => false, "message" => "Action not supported for this type"]);
        exit;
    }
} elseif ($action === "unavailable") {
    if ($type === 'thesis') {
        $sql = "UPDATE tbl_thesis SET availability='Unavailable' WHERE thesis_id IN ($in)";
    } else {
        echo json_encode(["success" => false, "message" => "Action not supported for this type"]);
        exit;
    }
}

if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Bulk action completed!"]);
} else {
    echo json_encode(["success" => false, "message" => "DB Error: " . $conn->error]);
}
