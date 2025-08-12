<?php
// createTeam.php
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
    
    $result = createTeam($data);
    echo json_encode([
        'status' => 'success',
        'message' => 'Team created successfully',
        'data' => $result
    ]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function createTeam($data) {
    if(empty($data['nome_team'])) {
        throw new Exception("Team name is required");
    }
    
    $teamName = trim($data['nome_team']);
    $teamDescription = isset($data['descricao_team']) ? trim($data['descricao_team']) : '';
    $createdBy = isset($data['created_by_admin']) ? intval($data['created_by_admin']) : 1;
    
    if (strlen($teamName) < 3) {
        throw new Exception("Team name must be at least 3 characters long");
    }
    
    $db = new Database();
    $connection = $db->getConnection();
    
    // Check if team name already exists
    $checkQuery = "SELECT id_team FROM Teams WHERE nome_team = ? AND status_team = 'active'";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bind_param("s", $teamName);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $checkStmt->close();
        $connection->close();
        throw new Exception("Team name already exists");
    }
    $checkStmt->close();
    
    // Create team
    $insertQuery = "INSERT INTO Teams (nome_team, descricao_team, created_by_admin) VALUES (?, ?, ?)";
    $insertStmt = $connection->prepare($insertQuery);
    $insertStmt->bind_param("ssi", $teamName, $teamDescription, $createdBy);
    
    if (!$insertStmt->execute()) {
        $insertStmt->close();
        $connection->close();
        throw new Exception("Failed to create team");
    }
    
    $newTeamId = $connection->insert_id;
    $insertStmt->close();
    $connection->close();
    
    return ['id' => $newTeamId, 'created' => true];
}
?>
