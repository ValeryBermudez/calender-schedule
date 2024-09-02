<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $user_id = $_SESSION['user_id'];

    // Depuración
    error_log("Received data: Title - $title, Date - $date, Time - $time, User ID - $user_id");

    $stmt = $conn->prepare("INSERT INTO citas (titulo, fecha, hora, usuario_id) VALUES (:title, :date, :time, :user_id)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':time', $time);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        echo "Event added successfully";
    } else {
        echo "Error adding event";
    }
}
?>
