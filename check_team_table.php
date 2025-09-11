<?php
// Database connection parameters
$host = 'localhost';
$user = 'root';
$pass = 'Denis55522';
$db = 'futurehope_db';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get table structure
$sql = "DESCRIBE team";
$result = $conn->query($sql);

if ($result) {
    echo "Team Table Structure:\n";
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] == 'NO' ? 'Required' : 'Optional') . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}

// Check if the form submission works
echo "\n\nChecking form submission functionality...\n";
$sql = "SHOW TABLES LIKE 'team'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "The team table exists.\n";
    
    // Check permissions
    $sql = "SELECT * FROM team LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result === false) {
        echo "Error querying team table: " . $conn->error . "\n";
    } else {
        echo "Can successfully query the team table.\n";
        
        // Try a test update (don't actually update anything)
        $sql = "UPDATE team SET name = name WHERE 1=0";
        if ($conn->query($sql) === TRUE) {
            echo "Write permissions to team table are OK.\n";
        } else {
            echo "Error updating team table: " . $conn->error . "\n";
        }
    }
} else {
    echo "The team table does not exist!\n";
}

// Close connection
$conn->close();
?>
