<?php
// Start session
session_start();

// Include database configuration and functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('index.php');
}

// Get settings
$settings = getSettings($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Members Debug - Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <h1>Team Members Debug</h1>
        
        <div class="card">
            <div class="card-body">
                <h5>Debug Information:</h5>
                <div id="debug-info" class="mt-3"></div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-body">
                <h5>Test Form Submission</h5>
                <form id="testForm" action="debug_submit.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="test_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="test_name" name="name" value="Test Member">
                    </div>
                    <div class="mb-3">
                        <label for="test_position" class="form-label">Position</label>
                        <input type="text" class="form-control" id="test_position" name="position" value="Test Position">
                    </div>
                    <div class="mb-3">
                        <label for="test_bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="test_bio" name="bio" rows="3">Test Bio</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="test_order" class="form-label">Order</label>
                        <input type="number" class="form-control" id="test_order" name="order_number" value="99">
                    </div>
                    <button type="submit" class="btn btn-primary">Test Submit</button>
                </form>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="team.php" class="btn btn-secondary">Back to Team Members</a>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Check if the modal opens correctly
            const debugInfo = $('#debug-info');
            
            // Add info about jQuery and Bootstrap versions
            debugInfo.append('<p>jQuery version: ' + $.fn.jquery + '</p>');
            debugInfo.append('<p>Bootstrap version: ' + (typeof bootstrap !== 'undefined' ? bootstrap.Tooltip.VERSION : 'Not detected') + '</p>');
            
            // Check for Console Errors
            debugInfo.append('<p>Check browser console for JavaScript errors when using the team form.</p>');
            
            // Add a test modal button
            debugInfo.append('<button id="testModalBtn" class="btn btn-info">Test Modal</button>');
            
            // Add test modal
            $('body').append(`
                <div class="modal fade" id="testModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Test Modal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>If you can see this, modals are working properly.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            // Test modal button
            $('#testModalBtn').click(function() {
                var testModal = new bootstrap.Modal(document.getElementById('testModal'));
                testModal.show();
            });
        });
    </script>
</body>
</html>
