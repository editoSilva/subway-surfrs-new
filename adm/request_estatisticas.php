<?php
    try {

        $stmt = $conn->prepare("SELECT COUNT(*) AS cadastros FROM appconfig WHERE created_at >= NOW() - INTERVAL 1 DAY;");
        
        $stmt->execute();
    
        $cadastros24h = $stmt->get_result();
        
        if ($app) {
            $results = array();
            while ($row = $app->fetch_assoc()) {
                $results[] = $row;
            }
    
            echo json_encode($results);
        } else {
            echo "Erro na consulta SQL: " . $conn->error;
        }
    
        $stmt->close();
        
    
    } catch (Exception $ex) {
        var_dump($ex);
    }
?>
