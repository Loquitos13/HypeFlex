<?php
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
    
    $result = removeTeamMember($data);
    echo json_encode([
        'status' => 'success',
        'message' => 'Team member removed successfully',
        'data' => $result
    ]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function removeTeamMember($data) {
    if(empty($data['member_id'])) {
        throw new Exception("Member ID is required");
    }
    
    $memberId = intval($data['member_id']);
    
    $db = new Database();
    $connection = $db->getConnection();
    
    // Delete team member
    $deleteQuery = "DELETE FROM TeamMembers WHERE id_team_member = ?";
    $deleteStmt = $connection->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $memberId);
    
    if (!$deleteStmt->execute()) {
        $deleteStmt->close();
        $connection->close();
        throw new Exception("Failed to remove team member");
    }
    
    $affectedRows = $deleteStmt->affected_rows;
    $deleteStmt->close();
    $connection->close();
    
    if ($affectedRows === 0) {
        throw new Exception("Team member not found");
    }
    
    return ['removed' => true, 'affected_rows' => $affectedRows];
}
?>