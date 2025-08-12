<?php
// deleteTeam.php
session_start();
require_once '../restapi/Database.php';

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    
    if(empty($json)) {
        throw new Exception("Empty request body");
    }
    
    $data = json_decode($json, true);
    
    if(json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON: " . json_last_error_msg());
    }
    
    $result = deleteTeam($data);
    echo json_encode([
        'status' => 'success',
        'message' => 'Team deleted successfully',
        'data' => $result
    ]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function deleteTeam($data) {
    if(empty($data['team_id'])) {
        throw new Exception("Team ID is required");
    }
    
    $teamId = intval($data['team_id']);
    
    $db = new Database();
    $connection = $db->getConnection();
    
    // Soft delete - update status instead of actual deletion
    $updateQuery = "UPDATE Teams SET status_team = 'inactive' WHERE id_team = ?";
    $updateStmt = $connection->prepare($updateQuery);
    $updateStmt->bind_param("i", $teamId);
    
    if (!$updateStmt->execute()) {
        $updateStmt->close();
        $connection->close();
        throw new Exception("Failed to delete team");
    }
    
    $affectedRows = $updateStmt->affected_rows;
    $updateStmt->close();
    $connection->close();
    
    if ($affectedRows === 0) {
        throw new Exception("Team not found");
    }
    
    return ['deleted' => true, 'affected_rows' => $affectedRows];
}
?>