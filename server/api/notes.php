<?php
require_once __DIR__ . '/../utils/database.php';

// Create note
function create_note() {
    $db = Database::getInstance()->getConnection();
    $data = json_decode(file_get_contents('php://input'), true);

    $title = $data['title'] ?? 'Untitled';
    $content = $data['content'] ?? '';
    $id = bin2hex(random_bytes(8));
    $now = date('Y-m-d H:i:s');

    $stmt = $db->prepare("INSERT INTO notes (id, title, content, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, $title, $content, $now, $now]);

    echo json_encode([
        'success' => true,
        'note' => [
            'id' => $id,
            'title' => $title,
            'content' => $content,
            'created_at' => $now,
            'updated_at' => $now
        ]
    ]);
}

// Get normal note
function get_note() {
    $db = Database::getInstance()->getConnection();
    $id = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    $stmt = $db->prepare("SELECT * FROM notes WHERE id = ?");
    $stmt->execute([$id]);
    $note = $stmt->fetch();

    if (!$note) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Note not found']);
        return;
    }

    echo json_encode(['success' => true, 'note' => $note]);
}

// Update note
function update_note() {
    $db = Database::getInstance()->getConnection();
    $id = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $data = json_decode(file_get_contents('php://input'), true);

    $title = $data['title'] ?? null;
    $content = $data['content'] ?? '';
    $now = date('Y-m-d H:i:s');

    if ($title !== null) {
        $stmt = $db->prepare("UPDATE notes SET title = ?, content = ?, updated_at = ? WHERE id = ?");
        $stmt->execute([$title, $content, $now, $id]);
    } else {
        $stmt = $db->prepare("UPDATE notes SET content = ?, updated_at = ? WHERE id = ?");
        $stmt->execute([$content, $now, $id]);
    }

    $stmt = $db->prepare("SELECT * FROM notes WHERE id = ?");
    $stmt->execute([$id]);
    $note = $stmt->fetch();

    echo json_encode(['success' => true, 'note' => $note]);
}

// Create share
function create_share() {
    $db = Database::getInstance()->getConnection();
    $data = json_decode(file_get_contents('php://input'), true);

    $note_id = $data['note_id'] ?? '';
    $can_edit = $data['can_edit'] ?? true;
    $share_token = bin2hex(random_bytes(16));

    $stmt = $db->prepare("SELECT id FROM notes WHERE id = ?");
    $stmt->execute([$note_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Note not found']);
        return;
    }

    $stmt = $db->prepare("INSERT INTO note_shares (note_id, share_token, can_edit) VALUES (?, ?, ?)");
    $stmt->execute([$note_id, $share_token, $can_edit]);

    echo json_encode(['success' => true, 'share_token' => $share_token]);
}

// Get shared note
function get_shared_note() {
    $db = Database::getInstance()->getConnection();
    $token = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    $stmt = $db->prepare("
        SELECT n.*, s.can_edit 
        FROM notes n 
        JOIN note_shares s ON n.id = s.note_id 
        WHERE s.share_token = ?
    ");
    $stmt->execute([$token]);
    $note = $stmt->fetch();

    if (!$note) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Shared note not found']);
        return;
    }

    echo json_encode(['success' => true, 'note' => $note]);
}

// Update shared note
function update_shared_note() {
    $db = Database::getInstance()->getConnection();
    $token = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $db->prepare("SELECT note_id, can_edit FROM note_shares WHERE share_token = ?");
    $stmt->execute([$token]);
    $share = $stmt->fetch();

    if (!$share) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Share not found']);
        return;
    }

    if (!$share['can_edit']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'No edit permission']);
        return;
    }

    $content = $data['content'] ?? '';
    $title = $data['title'] ?? null;
    $now = date('Y-m-d H:i:s');

    if ($title !== null) {
        $stmt = $db->prepare("UPDATE notes SET title = ?, content = ?, updated_at = ? WHERE id = ?");
        $stmt->execute([$title, $content, $now, $share['note_id']]);
    } else {
        $stmt = $db->prepare("UPDATE notes SET content = ?, updated_at = ? WHERE id = ?");
        $stmt->execute([$content, $now, $share['note_id']]);
    }

    $stmt = $db->prepare("SELECT * FROM notes WHERE id = ?");
    $stmt->execute([$share['note_id']]);
    $note = $stmt->fetch();

    echo json_encode(['success' => true, 'note' => $note]);
}
?>

