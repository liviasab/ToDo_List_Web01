<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$host = '127.0.0.1:3306';
$db = 'todo_list';
$user = 'root';
$password = '@40028922maconhA';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $conn->query('SELECT * FROM tasks');
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tasks);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'];
        $completed = ($data['completed'] === 'true' || $data['completed'] === 1) ? 1 : 0;

        $stmt = $conn->prepare('INSERT INTO tasks (title, completed) VALUES (:title, :completed)');
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':completed', $completed);
        $stmt->execute();

        $taskID = $conn->lastInsertId();
        $response = [
            'id' => $taskID,
            'title' => $title,
            'completed' => $completed
        ];
        echo json_encode($response);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $taskID = $_GET['id'];
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['completed'])) {
            $completed = ($data['completed'] === 'true' || $data['completed'] === 1) ? 1 : 0;

            $stmt = $conn->prepare('UPDATE tasks SET completed = :completed WHERE id = :id');
            $stmt->bindParam(':completed', $completed);
            $stmt->bindParam(':id', $taskID);
            $stmt->execute();
        }

        if (isset($data['title'])) {
            $title = $data['title'];

            $stmt = $conn->prepare('UPDATE tasks SET title = :title WHERE id = :id');
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':id', $taskID);
            $stmt->execute();
        }

        echo json_encode(['message' => 'Task updated successfully']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $taskID = $_GET['id'];

        $stmt = $conn->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->bindParam(':id', $taskID);
        $stmt->execute();

        echo json_encode(['message' => 'Task deleted successfully']);
        exit;
    }
} catch (PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}
