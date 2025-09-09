<?php
// Include database configuration
require_once 'includes/config.php';

// SQL to create the team table
$sql = "CREATE TABLE IF NOT EXISTS team (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NOT NULL,
    bio TEXT,
    image VARCHAR(255),
    order_number INT(11) NOT NULL DEFAULT 0,
    facebook VARCHAR(255),
    twitter VARCHAR(255),
    instagram VARCHAR(255),
    linkedin VARCHAR(255),
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Team table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

// Close connection
$conn->close();
?>
