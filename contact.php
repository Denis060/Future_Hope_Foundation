<?php
// Set page title
$page_title = "Contact Us";

// Include header
include_once 'includes/header.php';

// Process contact form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? sanitize($conn, $_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize($conn, $_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize($conn, $_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? sanitize($conn, $_POST['subject']) : '';
    $msg = isset($_POST['message']) ? sanitize($conn, $_POST['message']) : '';
    
    // Validate fields
    if (empty($name) || empty($email) || empty($msg)) {
        $message = displayError("Please fill in all required fields.");
    } else {
        // Insert the message into the database
        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) 
                VALUES ('$name', '$email', '$phone', '$subject', '$msg')";
        
        if ($conn->query($sql)) {
            $message = displaySuccess("Thank you for your message. We'll get back to you soon!");
            
            // Clear form fields after successful submission
            $_POST = array();
        } else {
            $message = displayError("Sorry, there was an error sending your message. Please try again later.");
        }
    }
}
?>

<!-- Page Header -->
<div class="page-header" style="background-image: url('assets/images/page-header.jpg');">
    <div class="overlay" style="background-color: rgba(44, 62, 80, 0.85);">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="text-white text-center">Contact Us</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Contact Section -->
<section class="contact-section section-padding">
    <div class="container">
        <div class="section-title text-center">
            <h2>Get in Touch</h2>
            <p>Have questions or want to know more about our work? Reach out to us!</p>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="contact-info">
                    <div class="contact-info-box">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Visit Us</h4>
                        <p><?php echo $settings['site_address'] ?? '123 Charity Street, City, Country'; ?></p>
                    </div>
                    <div class="contact-info-box">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h4>Call Us</h4>
                        <p><?php echo $settings['site_phone'] ?? '+1 (123) 456-7890'; ?></p>
                    </div>
                    <div class="contact-info-box">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Email Us</h4>
                        <p><?php echo $settings['site_email'] ?? 'info@futurehope.org'; ?></p>
                    </div>
                    <div class="contact-info-box">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4>Office Hours</h4>
                        <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
                        <p>Saturday: 9:00 AM - 12:00 PM</p>
                        <p>Sunday: Closed</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="contact-form">
                    <?php echo $message; ?>
                    <form method="post" action="">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <input type="text" name="name" class="form-control" placeholder="Your Name *" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-4">
                                <input type="email" name="email" class="form-control" placeholder="Your Email *" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-4">
                                <input type="tel" name="phone" class="form-control" placeholder="Your Phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-4">
                                <input type="text" name="subject" class="form-control" placeholder="Subject" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                            </div>
                            <div class="col-md-12 mb-4">
                                <textarea name="message" class="form-control" rows="5" placeholder="Your Message *" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Map -->
<div class="map-section">
    <div class="container-fluid p-0">
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215151353841!2d-73.98731768459596!3d40.757977979326544!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c258555555555%3A0x5555555555555555!2sCharity%20Organization!5e0!3m2!1sen!2sus!4v1631234567890!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
