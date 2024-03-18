<?php
    try {
        include './../connection.php';
    
        $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
    
    
        $stmt = $conn->prepare("SELECT D.dia                                                  AS dia,
       IFNULL((SELECT SUM(T.valor)
               FROM confirmar_deposito T
               WHERE DAY(T.data) = D.dia
                 AND MONTH(T.data) = MONTH(CURDATE())
                 AND YEAR(T.data) = YEAR(CURDATE())
                 AND T.status = 'PAID_OUT'), 0)               AS total_deposito,
       COALESCE((SELECT SUM(COALESCE(S.VALOR, 0))
        FROM saques S
        WHERE DAY(S.created_at) = D.dia
          AND MONTH(S.created_at) = MONTH(CURDATE())
          AND YEAR(S.created_at) = YEAR(CURDATE())
          AND S.status = 'PAID'), 0) +
       COALESCE((SELECT SUM(COALESCE(SA.VALOR, 0))
                  FROM saque_afiliado SA
                  WHERE DAY(SA.created_at) = D.dia
                    AND MONTH(SA.created_at) = MONTH(CURDATE())
                    AND YEAR(SA.created_at) = YEAR(CURDATE())
                    AND SA.status = 'PAID'), 0) AS total_saques
FROM (SELECT a.a + (10 * b.a) + (100 * c.a) as dia
      FROM (SELECT 0 AS a
            UNION ALL
            SELECT 1
            UNION ALL
            SELECT 2
            UNION ALL
            SELECT 3
            UNION ALL
            SELECT 4
            UNION ALL
            SELECT 5
            UNION ALL
            SELECT 6
            UNION ALL
            SELECT 7
            UNION ALL
            SELECT 8
            UNION ALL
            SELECT 9) AS a
               CROSS JOIN (SELECT 0 AS a
                           UNION ALL
                           SELECT 1
                           UNION ALL
                           SELECT 2
                           UNION ALL
                           SELECT 3
                           UNION ALL
                           SELECT 4
                           UNION ALL
                           SELECT 5
                           UNION ALL
                           SELECT 6
                           UNION ALL
                           SELECT 7
                           UNION ALL
                           SELECT 8
                           UNION ALL
                           SELECT 9) AS b
               CROSS JOIN (SELECT 0 AS a
                           UNION ALL
                           SELECT 1
                           UNION ALL
                           SELECT 2
                           UNION ALL
                           SELECT 3
                           UNION ALL
                           SELECT 4
                           UNION ALL
                           SELECT 5
                           UNION ALL
                           SELECT 6
                           UNION ALL
                           SELECT 7
                           UNION ALL
                           SELECT 8
                           UNION ALL
                           SELECT 9) AS c) AS D
WHERE D.dia BETWEEN 1 AND DAY(LAST_DAY(CURDATE()))
ORDER BY D.dia DESC LIMIT 100;

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
