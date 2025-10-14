<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Algorithme de Dijkstra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select {
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background: #e8f5e8;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .graph-info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .path {
            font-weight: bold;
            color: #28a745;
            font-size: 18px;
        }
        .distance {
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Algorithme de Dijkstra</h1>
        
        <div class="graph-info">
            <h3>📊 Graphe utilisé :</h3>
            <pre>
    A --1-- B
    |       | \
    4       2  5
    |       |   \
    C --3-- D -- E
            |   /
            1  2
            | /
            F
            </pre>
        </div>

        <form method="POST" action="">
            <div class="form-group">
                <label for="start">Point de départ :</label>
                <select name="start" id="start" required>
                    <option value="">Sélectionnez...</option>
                    <option value="A" <?= isset($_POST['start']) && $_POST['start'] == 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= isset($_POST['start']) && $_POST['start'] == 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= isset($_POST['start']) && $_POST['start'] == 'C' ? 'selected' : '' ?>>C</option>
                    <option value="D" <?= isset($_POST['start']) && $_POST['start'] == 'D' ? 'selected' : '' ?>>D</option>
                    <option value="E" <?= isset($_POST['start']) && $_POST['start'] == 'E' ? 'selected' : '' ?>>E</option>
                    <option value="F" <?= isset($_POST['start']) && $_POST['start'] == 'F' ? 'selected' : '' ?>>F</option>
                </select>
            </div>

            <div class="form-group">
                <label for="end">Point d'arrivée :</label>
                <select name="end" id="end" required>
                    <option value="">Sélectionnez...</option>
                    <option value="A" <?= isset($_POST['end']) && $_POST['end'] == 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= isset($_POST['end']) && $_POST['end'] == 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= isset($_POST['end']) && $_POST['end'] == 'C' ? 'selected' : '' ?>>C</option>
                    <option value="D" <?= isset($_POST['end']) && $_POST['end'] == 'D' ? 'selected' : '' ?>>D</option>
                    <option value="E" <?= isset($_POST['end']) && $_POST['end'] == 'E' ? 'selected' : '' ?>>E</option>
                    <option value="F" <?= isset($_POST['end']) && $_POST['end'] == 'F' ? 'selected' : '' ?>>F</option>
                </select>
            </div>

            <button type="submit" name="calculate">Calculer le plus court chemin</button>
        </form>

        <?php
        // Définition du graphe
        $graph = [
            'A' => ['B' => 1, 'C' => 4],
            'B' => ['A' => 1, 'D' => 2, 'E' => 5],
            'C' => ['A' => 4, 'D' => 3],
            'D' => ['B' => 2, 'C' => 3, 'E' => 1, 'F' => 1],
            'E' => ['B' => 5, 'D' => 1, 'F' => 2],
            'F' => ['D' => 1, 'E' => 2]
        ];

        // Fonction Dijkstra
        function dijkstra($graph, $start, $end = null) {
            // Initialisation des distances
            $distances = [];
            $predecessors = [];
            $queue = new SplPriorityQueue();
            
            foreach ($graph as $node => $neighbors) {
                $distances[$node] = PHP_INT_MAX;
                $predecessors[$node] = null;
            }
            
            $distances[$start] = 0;
            $queue->insert($start, 0);
            
            while (!$queue->isEmpty()) {
                $currentNode = $queue->extract();
                
                // Si on a atteint le nœud destination
                if ($end && $currentNode === $end) {
                    break;
                }
                
                // Parcourir les voisins
                foreach ($graph[$currentNode] as $neighbor => $weight) {
                    $newDistance = $distances[$currentNode] + $weight;
                    
                    if ($newDistance < $distances[$neighbor]) {
                        $distances[$neighbor] = $newDistance;
                        $predecessors[$neighbor] = $currentNode;
                        // Utiliser une priorité négative car SplPriorityQueue est un max-heap
                        $queue->insert($neighbor, -$newDistance);
                    }
                }
            }
            
            return ['distances' => $distances, 'predecessors' => $predecessors];
        }

        // Fonction pour reconstruire le chemin
        function reconstruireChemin($predecessors, $end) {
            $chemin = [];
            $current = $end;
            
            while ($current !== null) {
                array_unshift($chemin, $current);
                $current = $predecessors[$current];
            }
            
            return $chemin;
        }

        // Traitement du formulaire
        if (isset($_POST['calculate']) && !empty($_POST['start']) && !empty($_POST['end'])) {
            $start = $_POST['start'];
            $end = $_POST['end'];
            
            echo '<div class="result">';
            
            // Vérification que les nœuds existent dans le graphe
            if (!array_key_exists($start, $graph) || !array_key_exists($end, $graph)) {
                echo '<div class="error">';
                echo "<h3>❌ Erreur</h3>";
                echo "Un des points sélectionnés n'existe pas dans le graphe.";
                echo '</div>';
            } else {
                // Exécution de Dijkstra
                $result = dijkstra($graph, $start, $end);
                $chemin = reconstruireChemin($result['predecessors'], $end);
                $distance = $result['distances'][$end];
                
                echo "<h3>✅ Résultat :</h3>";
                echo "<p><strong>De :</strong> $start</p>";
                echo "<p><strong>À :</strong> $end</p>";
                echo "<p class='distance'>Distance la plus courte : " . $distance . "</p>";
                echo "<p class='path'>Chemin : " . implode(' → ', $chemin) . "</p>";
                
                // Affichage de toutes les distances depuis le point de départ
                echo "<h4>📊 Distances depuis $start :</h4>";
                echo "<ul>";
                foreach ($result['distances'] as $node => $dist) {
                    echo "<li>$node : " . ($dist === PHP_INT_MAX ? '∞' : $dist) . "</li>";
                }
                echo "</ul>";
            }
            
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>