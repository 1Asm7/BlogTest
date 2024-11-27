<?php

require_once("config.php");

try {
	$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
	exit;
}

$postsUrl = 'https://jsonplaceholder.typicode.com/posts';
$commentsUrl = 'https://jsonplaceholder.typicode.com/comments';

$postsJson = file_get_contents($postsUrl);
$commentsJson = file_get_contents($commentsUrl);

$posts = json_decode($postsJson, true);
$comments = json_decode($commentsJson, true);

$postStmt = $conn->prepare("INSERT INTO posts (id, user_id, title, body) VALUES (:id, :userId, :title, :body)");
$commentStmt = $conn->prepare("INSERT INTO comments (id, post_id, name, email, body) VALUES (:id, :postId, :name, :email, :body)");

foreach ($posts as $post) {
    $postStmt->execute([
        ':id' => $post['id'],
        ':userId' => $post['userId'],
        ':title' => $post['title'],
        ':body' => $post['body']
    ]);
}

foreach ($comments as $comment) {
    $commentStmt->execute([
        ':id' => $comment['id'],
        ':postId' => $comment['postId'],
        ':name' => $comment['name'],
        ':email' => $comment['email'],
        ':body' => $comment['body']
    ]);
}

$postsNum = count($posts);
$commentsNum = count($comments);
echo "Загружено {$postsNum} записей и {$commentsNum} комментариев";

?>