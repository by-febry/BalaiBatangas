<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Unauthorized access');
}

$query = "SELECT * FROM library ORDER BY festival_id DESC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['festival_id']}</td>";
    echo "<td><img src='img/{$row['festival_image']}' alt='{$row['festival_name']}' style='width:50px;height:50px;object-fit:cover;'></td>";
    echo "<td>{$row['festival_name']}</td>";
    echo "<td>{$row['location']}</td>";
    echo "<td>{$row['date_celebrated']}</td>";
    echo "<td>
            <button onclick='editFestival({$row['festival_id']})' class='edit-btn'>Edit</button>
            <button onclick='deleteFestival({$row['festival_id']})' class='delete-btn'>Delete</button>
          </td>";
    echo "</tr>";
}
?> 