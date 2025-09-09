<?php
// Set page title
$page_title = "Donate";
include 'includes/header.php';

// Initialize variables
$success = false;
$error = '';
$donor_name = '';
$donor_email = '';
$amount = '';
$campaign = '';
$payment_method = '';
$notes = '';
$is_anonymous = 0;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $donor_name = sanitizeInput($_POST['donor_name'] ?? '');
    $donor_email = sanitizeInput($_POST['donor_email'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $campaign = sanitizeInput($_POST['campaign'] ?? '');
    $payment_method = sanitizeInput($_POST['payment_method'] ?? 'credit_card');
    $notes = sanitizeInput($_POST['notes'] ?? '');
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    
    // Validate inputs
    if (empty($donor_email) && empty($donor_name) && $is_anonymous == 0) {
        $error = "Please provide either your name or email, or choose to donate anonymously.";
    } elseif ($amount <= 0) {
        $error = "Please enter a valid donation amount.";
    } elseif (!empty($donor_email) && !filter_var($donor_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Generate a transaction ID
        $transaction_id = 'FHF-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        
        // Insert donation into database
        $status = 'pending'; // In a real application, this would be updated after payment processing
        
        $stmt = $conn->prepare("INSERT INTO donations (donor_name, donor_email, amount, campaign, payment_method, transaction_id, status, notes, is_anonymous) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssdsssssi", $donor_name, $donor_email, $amount, $campaign, $payment_method, $transaction_id, $status, $notes, $is_anonymous);
        
        if ($stmt->execute()) {
            $success = true;
            
            // Clear form variables
            $donor_name = '';
            $donor_email = '';
            $amount = '';
            $campaign = '';
            $payment_method = '';
            $notes = '';
            $is_anonymous = 0;
        } else {
            $error = "Error processing your donation. Please try again.";
        }
        
        $stmt->close();
    }
}

// Get ongoing projects for the campaign dropdown
$projects = getProjects(0, 'ongoing', true);
?>

<!-- Page Title -->
<section class="page-title-section bg-primary">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white">Make a Donation</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">Donate</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Donation Impact Section -->
<section class="donation-impact-section py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Your Support Makes a Difference</h2>
                <p class="lead">When you donate to Future Hope Foundation, you're helping orphans and less privileged children access education, healthcare, and essential support.</p>
            </div>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="impact-card text-center p-4 h-100 border rounded shadow-sm">
                    <div class="impact-icon mb-3">
                        <i class="fas fa-book fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4">Education</h3>
                    <p>$25 provides school supplies for a child for an entire term.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="impact-card text-center p-4 h-100 border rounded shadow-sm">
                    <div class="impact-icon mb-3">
                        <i class="fas fa-heartbeat fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4">Healthcare</h3>
                    <p>$50 ensures medical checkups and basic medications for five children.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="impact-card text-center p-4 h-100 border rounded shadow-sm">
                    <div class="impact-icon mb-3">
                        <i class="fas fa-home fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4">Shelter & Support</h3>
                    <p>$100 provides a month of housing, meals, and care for a child in need.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Donation Form Section -->
<section class="donation-form-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white py-3">
                        <h3 class="mb-0">Make Your Donation</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($success): ?>
                        <div class="alert alert-success mb-4">
                            <h4 class="alert-heading">Thank You for Your Donation!</h4>
                            <p>Your generous contribution will help us make a difference in the lives of orphans and less privileged children.</p>
                            <p class="mb-0">A confirmation email will be sent to you shortly with the details of your donation.</p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="donationForm">
                            <div class="row mb-4">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Donation Amount*</label>
                                    <div class="btn-group amount-options w-100" role="group">
                                        <input type="radio" class="btn-check" name="amount_btn" id="amount25" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="amount25">$25</label>
                                        
                                        <input type="radio" class="btn-check" name="amount_btn" id="amount50" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="amount50">$50</label>
                                        
                                        <input type="radio" class="btn-check" name="amount_btn" id="amount100" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="amount100">$100</label>
                                        
                                        <input type="radio" class="btn-check" name="amount_btn" id="amount250" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="amount250">$250</label>
                                        
                                        <input type="radio" class="btn-check" name="amount_btn" id="amountCustom" autocomplete="off" checked>
                                        <label class="btn btn-outline-primary" for="amountCustom">Custom</label>
                                    </div>
                                    <div class="mt-3 input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="amount" name="amount" value="<?php echo $amount; ?>" min="1" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="donor_name" class="form-label">Your Name</label>
                                    <input type="text" class="form-control" id="donor_name" name="donor_name" value="<?php echo $donor_name; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="donor_email" class="form-label">Your Email</label>
                                    <input type="email" class="form-control" id="donor_email" name="donor_email" value="<?php echo $donor_email; ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" <?php echo $is_anonymous ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_anonymous">Make this donation anonymous</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="campaign" class="form-label">Donation Purpose (Optional)</label>
                                    <select class="form-select" id="campaign" name="campaign">
                                        <option value="">General Fund</option>
                                        <option value="education" <?php echo $campaign == 'education' ? 'selected' : ''; ?>>Education Support</option>
                                        <option value="healthcare" <?php echo $campaign == 'healthcare' ? 'selected' : ''; ?>>Healthcare Initiatives</option>
                                        <option value="nutrition" <?php echo $campaign == 'nutrition' ? 'selected' : ''; ?>>Nutrition Program</option>
                                        <option value="shelter" <?php echo $campaign == 'shelter' ? 'selected' : ''; ?>>Shelter & Housing</option>
                                        <?php if (!empty($projects)): foreach ($projects as $project): ?>
                                        <option value="project-<?php echo $project['id']; ?>" <?php echo $campaign == 'project-'.$project['id'] ? 'selected' : ''; ?>><?php echo $project['title']; ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="payment_method" class="form-label">Payment Method*</label>
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value="credit_card" <?php echo $payment_method == 'credit_card' ? 'selected' : ''; ?>>Credit Card</option>
                                        <option value="paypal" <?php echo $payment_method == 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                                        <option value="bank_transfer" <?php echo $payment_method == 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Comments (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $notes; ?></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Donate Now</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="mb-0"><i class="fas fa-lock me-1"></i> Your donation is secure and encrypted. All payment information is processed securely.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Other Ways to Help Section -->
<section class="other-ways-section py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Other Ways to Help</h2>
                <p class="lead">Beyond financial donations, there are many ways you can support our mission.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 mb-4">
                <div class="help-card h-100 border rounded shadow-sm p-4 text-center">
                    <div class="help-icon mb-3">
                        <i class="fas fa-hands-helping fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4">Volunteer</h3>
                    <p>Share your skills and time with our organization to help with various programs and events.</p>
                    <a href="contact.php" class="btn btn-outline-primary mt-2">Contact Us</a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="help-card h-100 border rounded shadow-sm p-4 text-center">
                    <div class="help-icon mb-3">
                        <i class="fas fa-gift fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4">In-Kind Gifts</h3>
                    <p>Donate supplies, equipment, services, or other resources needed by our programs.</p>
                    <a href="contact.php" class="btn btn-outline-primary mt-2">Learn More</a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="help-card h-100 border rounded shadow-sm p-4 text-center">
                    <div class="help-icon mb-3">
                        <i class="fas fa-bullhorn fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4">Spread the Word</h3>
                    <p>Follow us on social media and help raise awareness about our cause and mission.</p>
                    <div class="social-links mt-2">
                        <?php if (!empty($settings['facebook_url'])): ?>
                        <a href="<?php echo $settings['facebook_url']; ?>" class="btn btn-outline-primary me-2"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['twitter_url'])): ?>
                        <a href="<?php echo $settings['twitter_url']; ?>" class="btn btn-outline-primary me-2"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['instagram_url'])): ?>
                        <a href="<?php echo $settings['instagram_url']; ?>" class="btn btn-outline-primary"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript for the donation form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle the amount selection buttons
    const amountBtns = document.querySelectorAll('input[name="amount_btn"]');
    const amountInput = document.getElementById('amount');
    
    amountBtns.forEach(function(btn) {
        btn.addEventListener('change', function() {
            if (this.id === 'amount25') {
                amountInput.value = '25';
            } else if (this.id === 'amount50') {
                amountInput.value = '50';
            } else if (this.id === 'amount100') {
                amountInput.value = '100';
            } else if (this.id === 'amount250') {
                amountInput.value = '250';
            } else {
                amountInput.value = '';
                amountInput.focus();
            }
        });
    });
    
    // Toggle anonymous donation fields
    const anonymousCheck = document.getElementById('is_anonymous');
    const nameField = document.getElementById('donor_name');
    const emailField = document.getElementById('donor_email');
    
    anonymousCheck.addEventListener('change', function() {
        if (this.checked) {
            nameField.required = false;
            emailField.required = false;
        } else {
            // In a real app, you might want to require at least one contact method
        }
    });
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
