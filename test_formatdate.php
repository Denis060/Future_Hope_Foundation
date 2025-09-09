<?php
// Include required files
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Test the formatDate function
$current_date = date('Y-m-d H:i:s');
$formatted_date = formatDate($current_date);

echo "<h1>Function Test: formatDate()</h1>";
echo "<p>Current Date: $current_date</p>";
echo "<p>Formatted Date: $formatted_date</p>";

// Test with donation data
echo "<h2>Testing with Donations Data</h2>";

$donation_query = "SELECT * FROM donations LIMIT 5";
$result = $conn->query($donation_query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Name</th><th>Amount</th><th>Campaign</th><th>Created Date</th><th>Formatted Date</th></tr>";
    
    while ($donation = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . ($donation['is_anonymous'] ? 'Anonymous' : $donation['donor_name']) . "</td>";
        echo "<td>$" . number_format($donation['amount'], 2) . "</td>";
        echo "<td>" . ($donation['campaign'] ?: 'General') . "</td>";
        echo "<td>" . $donation['created_at'] . "</td>";
        echo "<td>" . formatDate($donation['created_at']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No donations found.</p>";
}

echo "<p><a href='admin/'>Go to Admin Login</a></p>";
?>
