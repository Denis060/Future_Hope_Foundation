<?php
// Include database configuration and functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>Donations Debug</h1>";

// Check if donations table exists
$tables_query = "SHOW TABLES LIKE 'donations'";
$tables_result = $conn->query($tables_query);
if ($tables_result->num_rows == 0) {
    echo "<p>❌ The donations table doesn't exist!</p>";
    exit;
} else {
    echo "<p>✅ The donations table exists</p>";
}

// Get the structure of the donations table
echo "<h2>Donations Table Structure</h2>";
$structure_query = "DESCRIBE donations";
$structure_result = $conn->query($structure_query);
if ($structure_result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Error describing donations table: " . $conn->error . "</p>";
}

// Check the donation data
echo "<h2>Donation Data</h2>";
$data_query = "SELECT * FROM donations";
$data_result = $conn->query($data_query);
if ($data_result) {
    if ($data_result->num_rows > 0) {
        echo "<p>✅ Found " . $data_result->num_rows . " donation records</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>";
        $first_row = $data_result->fetch_assoc();
        $data_result->data_seek(0);
        foreach (array_keys($first_row) as $column) {
            echo "<th>{$column}</th>";
        }
        echo "</tr>";
        while ($row = $data_result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . (is_null($value) ? "NULL" : $value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ No donation records found in the table</p>";
    }
} else {
    echo "<p>❌ Error querying donations table: " . $conn->error . "</p>";
}

// Test the getTotalDonations function
echo "<h2>getTotalDonations() Function Test</h2>";
$total = getTotalDonations();
echo "<p>Total donations returned by function: " . var_export($total, true) . " (type: " . gettype($total) . ")</p>";

// Run the SQL directly
$direct_query = "SELECT SUM(amount) as total FROM donations WHERE status = 'completed'";
$direct_result = $conn->query($direct_query);
if ($direct_result) {
    $row = $direct_result->fetch_assoc();
    echo "<p>Direct SQL result: " . var_export($row['total'], true) . " (type: " . gettype($row['total']) . ")</p>";
} else {
    echo "<p>❌ Error running direct SQL: " . $conn->error . "</p>";
}
?>
