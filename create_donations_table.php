<?php
// Include database configuration
require_once 'includes/config.php';

// SQL to create donations table
$create_table_sql = "
CREATE TABLE IF NOT EXISTS `donations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `donor_name` varchar(100) DEFAULT NULL,
  `donor_email` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `campaign` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `status` enum('completed','pending','failed') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute the SQL
if ($conn->query($create_table_sql)) {
    echo "<h1>Success!</h1>";
    echo "<p>Donations table created successfully.</p>";
} else {
    echo "<h1>Error</h1>";
    echo "<p>Error creating donations table: " . $conn->error . "</p>";
}

// Add some sample donations for testing
$sample_data = "
INSERT INTO `donations` (`donor_name`, `donor_email`, `amount`, `campaign`, `payment_method`, `transaction_id`, `status`, `notes`, `is_anonymous`, `created_at`) VALUES
('John Doe', 'john@example.com', 100.00, 'Education Fund', 'credit_card', 'FHF-A1B2C3D4', 'completed', 'Keep up the good work!', 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
('Jane Smith', 'jane@example.com', 250.00, 'Healthcare Initiative', 'paypal', 'FHF-E5F6G7H8', 'completed', 'I support your mission', 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
('Mike Johnson', 'mike@example.com', 50.00, 'Food Program', 'credit_card', 'FHF-I9J0K1L2', 'completed', NULL, 0, DATE_SUB(NOW(), INTERVAL 1 WEEK)),
('Sarah Williams', 'sarah@example.com', 1000.00, 'Education Fund', 'bank_transfer', 'FHF-M3N4O5P6', 'completed', 'Donation for children\'s education program', 0, CURRENT_TIMESTAMP),
(NULL, NULL, 75.00, 'General Support', 'paypal', 'FHF-Q7R8S9T0', 'pending', 'Monthly donation', 1, DATE_SUB(NOW(), INTERVAL 1 DAY));
";

// Add sample data
if ($conn->multi_query($sample_data)) {
    echo "<p>Sample donation data added successfully.</p>";
    echo "<p><a href='admin/donations.php'>Go to Donations Admin</a></p>";
} else {
    echo "<p>Error adding sample data: " . $conn->error . "</p>";
}
?>
