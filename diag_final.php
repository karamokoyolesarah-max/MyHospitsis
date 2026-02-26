<?php
$host = '127.0.0.1';
$user = 'root';
$password = '';
$database = 'clinic_bd';
$conn = new mysqli($host, $user, $password, $database);

echo "--- Invoices with ID 21 ---\n";
$res = $conn->query("SELECT * FROM invoices WHERE id = 21");
while($row = $res->fetch_assoc()) print_r($row);

echo "\n--- Invoices with Number 21 ---\n";
$res = $conn->query("SELECT * FROM invoices WHERE invoice_number = '21'");
while($row = $res->fetch_assoc()) print_r($row);

echo "\n--- Patient with ID 36 (Gaston) ---\n";
$res = $conn->query("SELECT id, email FROM patients WHERE id = 36");
while($row = $res->fetch_assoc()) print_r($row);

$conn->close();
?>
