<?php

require_once("config.php");

$query = "";
$search_result = "";

if (isset($_GET['query'])) {

    $query = trim($_GET['query']);

    if (strlen($query) < 3) {
        $search_result = "Введите минимум 3 символа для поиска.";
    }
	else {
		try {
			$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
			exit;
		}

		$sql = "SELECT p.title, c.body 
				FROM comments c 
				JOIN posts p ON c.post_id = p.id 
				WHERE c.body LIKE :query";
		
		$stmt = $conn->prepare($sql);
		$stmt->execute(['query' => '%' . $query . '%']);
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if ($results) {
			$search_result =  "<h2>Результаты поиска для: " . htmlspecialchars($query) . "</h2>";
			foreach ($results as $result) {
				$search_result .=  "<div><b>" . htmlspecialchars($result['title']) . "</b></div>";
				$search_result .=  "<div>" . htmlspecialchars($result['body']) . "</div><br />";
			}
		} else {
			$search_result = "Нет результатов для вашего запроса.";
		}
	}
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск комментариев</title>
</head>
<body>
    <h1>Поиск комментариев</h1>
    <form action="." method="GET">
        <input type="text" name="query" placeholder="Введите текст для поиска" required minlength="3" value="<?= $query; ?>">
        <button type="submit">Найти</button>
    </form>
	<div>
	
	<?= $search_result; ?>
	
	</div>
</body>
</html>