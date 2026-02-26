<?php

// Database configuration
$host = '127.0.0.1';
$user = 'root';
$password = '';
$database = 'clinic_bd';

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update Invoice #21 to belong to Patient #36 (Gaston)
$sql = "UPDATE invoices SET patient_id = 36 WHERE id = 21";

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully. Invoice #21 now belongs to Patient #36.\n";
} else {
    echo "Error updating record: " . $conn->error . "\n";
}

// Verify the change
$sql_verify = "SELECT id, invoice_number, patient_id FROM invoices WHERE id = 21";
$result = $conn->query($sql_verify);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Verification - ID: " . $row["id"]. " - Invoice Number: " . $row["invoice_number"]. " - Patient ID: " . $row["patient_id"]. "\n";
    }
} else {
    echo "Invoice #21 not found after update.\n";
}

$conn->close();
?>
