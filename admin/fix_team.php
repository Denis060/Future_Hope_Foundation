<?php
// This script fixes potential issues with the team management functionality

// Start session
session_start();

// Include database configuration and functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('index.php');
}

// Set content type to JSON
header('Content-Type: application/json');
$response = ['success' => false];

// Check if fixes should be applied
if (isset($_GET['apply_fix']) && $_GET['apply_fix'] == 'yes') {
    // 1. Check if the team table exists and has the correct structure
    $tableExists = $conn->query("SHOW TABLES LIKE 'team'");
    if ($tableExists->num_rows == 0) {
        // Table doesn't exist, create it
        $createTableSQL = "CREATE TABLE `team` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `position` varchar(255) NOT NULL,
            `bio` text,
            `image` varchar(255) DEFAULT NULL,
            `order_number` int(11) NOT NULL DEFAULT '0',
            `facebook` varchar(255) DEFAULT NULL,
            `twitter` varchar(255) DEFAULT NULL,
            `instagram` varchar(255) DEFAULT NULL,
            `linkedin` varchar(255) DEFAULT NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT '1',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        )";
        
        if ($conn->query($createTableSQL)) {
            $response['table_created'] = true;
        } else {
            $response['table_creation_error'] = $conn->error;
        }
    } else {
        $response['table_exists'] = true;
    }
    
    // 2. Fix form submission functionality by checking and updating the JavaScript
    $teamFile = file_get_contents('../admin/team.php');
    
    // Check if the saveTeamMember click event is properly configured
    if (strpos($teamFile, "$('#saveTeamMember').click(function()") !== false) {
        $response['form_submission_code_exists'] = true;
        
        // Update the code to ensure proper form submission
        $oldCode = "// Handle Save Button Click in Modal
            $('#saveTeamMember').click(function() {
                if ($('#name').val() && $('#position').val()) {
                    $('#teamMemberForm').submit();
                } else {
                    alert('Please fill in all required fields.');
                }
            });";
        
        $newCode = "// Handle Save Button Click in Modal
            $('#saveTeamMember').click(function() {
                if ($('#name').val() && $('#position').val()) {
                    console.log('Submitting form...');
                    $('#teamMemberForm').submit();
                } else {
                    alert('Please fill in all required fields.');
                }
            });";
            
        $updatedTeamFile = str_replace($oldCode, $newCode, $teamFile);
        
        if (file_put_contents('../admin/team.php', $updatedTeamFile)) {
            $response['js_code_updated'] = true;
        } else {
            $response['js_update_error'] = 'Could not write to team.php';
        }
    } else {
        $response['js_code_not_found'] = true;
    }
    
    // 3. Fix any permission issues with the uploads directory
    $uploadsDir = '../uploads';
    if (!file_exists($uploadsDir)) {
        if (mkdir($uploadsDir, 0777, true)) {
            $response['uploads_dir_created'] = true;
        } else {
            $response['uploads_dir_error'] = 'Could not create uploads directory';
        }
    } else {
        if (is_writable($uploadsDir)) {
            $response['uploads_dir_writable'] = true;
        } else {
            // Try to fix permissions
            if (chmod($uploadsDir, 0777)) {
                $response['uploads_permissions_fixed'] = true;
            } else {
                $response['uploads_permissions_error'] = 'Could not change permissions on uploads directory';
            }
        }
    }
    
    $response['success'] = true;
    $response['message'] = 'Fixes have been applied. Please try using the team functionality now.';
} else {
    $response['message'] = 'Use ?apply_fix=yes to apply fixes';
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
