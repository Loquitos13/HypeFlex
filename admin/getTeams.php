
<?php
// getTeams.php
session_start();
require_once '../restapi/Database.php';

header('Content-Type: application/json');

try {
    $result = getTeams();
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function getTeams() {
    $db = new Database();
    $connection = $db->getConnection();
    
    $query = "SELECT t.id_team, t.nome_team, t.descricao_team, t.data_criacao_team, t.status_team,
                     a.nome_admin as created_by_name,
                     COUNT(tm.id_team_member) as member_count
              FROM Teams t
              LEFT JOIN Admins a ON t.created_by_admin = a.id_admin
              LEFT JOIN TeamMembers tm ON t.id_team = tm.id_team AND tm.status_member = 'active'
              WHERE t.status_team = 'active'
              GROUP BY t.id_team
              ORDER BY t.data_criacao_team DESC";
    
    $stmt = $connection->prepare($query);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $teams = [];
    
    while ($row = $result->fetch_assoc()) {
        $teams[] = $row;
    }
    
    $stmt->close();
    $connection->close();
    
    return $teams;
}
?>