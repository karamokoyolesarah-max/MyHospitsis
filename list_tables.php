<?php
$host = '127.0.0.1';
$user = 'root';
$password = '';
$database = 'clinic_bd';
$conn = new mysqli($host, $user, $password, $database);

$res = $conn->query("SHOW TABLES");
while($row = $res->fetch_array()) {
    echo $row[0] . "\n";
}

$conn->close();
?>
