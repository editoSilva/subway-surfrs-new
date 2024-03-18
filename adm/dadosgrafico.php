<?php
    try {
        include './../connection.php';
    
        $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
    
    
        $stmt = $conn->prepare("SELECT D.dia AS dia, 
       IFNULL((SELECT COUNT(A.data_cadastro) FROM appconfig A WHERE DAY(A.data_cadastro) = D.dia AND MONTH(A.data_cadastro) = MONTH(CURDATE()) AND YEAR(A.data_cadastro) = YEAR(CURDATE())), 0) AS total_cadastros
FROM (
    SELECT a.a + (10 * b.a) + (100 * c.a) as dia
    FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
) AS D
WHERE D.dia BETWEEN 1 AND DAY(LAST_DAY(CURDATE()))
ORDER BY D.dia DESC;
");
        
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        if ($result) {
            $results = array();
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
    
            echo json_encode($results);
        } else {
            echo "Erro na consulta SQL: " . $conn->error;
        }
    
        $stmt->close();
        $conn->close();
    
    } catch (Exception $ex) {
        var_dump($ex);
    }
?>
