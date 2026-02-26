<?php
$host = '127.0.0.1';
$user = 'root';
$password = '';
$database = 'clinic_bd';
$conn = new mysqli($host, $user, $password, $database);

$res = $conn->query("SELECT id, invoice_number, patient_id FROM invoices LIMIT 50");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Number: " . $row['invoice_number'] . " | PatientID: " . $row['patient_id'] . "\n";
}

$conn->close();
?>
