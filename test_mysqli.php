<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'clinic_bd';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "--- DIRECT DB CHECK ---\n";

// Get Patient
$res = $conn->query("SELECT id, name, first_name FROM patients WHERE email = 'Gaston@gmail.com'");
if ($res->num_rows > 0) {
    $p = $res->fetch_assoc();
    echo "Patient found: ID=" . $p['id'] . ", Name=" . $p['first_name'] . " " . $p['name'] . "\n";
    $pid = $p['id'];

    // Check Invoice 21
    $res2 = $conn->query("SELECT id, invoice_number, patient_id FROM invoices WHERE id = 21");
    if ($res2->num_rows > 0) {
        $i = $res2->fetch_assoc();
        echo "Invoice 21 found: ID=" . $i['id'] . ", Number=" . $i['invoice_number'] . ", Owner_ID=" . $i['patient_id'] . "\n";
        if ($i['patient_id'] == $pid) {
            echo "MATCH: Invoice belongs to this patient.\n";
        } else {
            echo "MISMATCH: Invoice belongs to patient ID " . $i['patient_id'] . "\n";
        }
    } else {
        echo "Invoice 21 NOT FOUND by ID.\n";
        
        // Search by invoice_number if 21 was the number
        $res3 = $conn->query("SELECT id, invoice_number, patient_id FROM invoices WHERE invoice_number = '21' OR invoice_number LIKE '%21%'");
        if ($res3->num_rows > 0) {
            $i = $res3->fetch_assoc();
            echo "Invoice found by NUMBER matching '21': ID=" . $i['id'] . ", Number=" . $i['invoice_number'] . ", Owner_ID=" . $i['patient_id'] . "\n";
        } else {
            echo "No invoices matching '21' as number found either.\n";
        }
    }
} else {
    echo "Patient 'Gaston@gmail.com' NOT FOUND.\n";
}

$conn->close();
echo "--- END CHECK ---\n";
