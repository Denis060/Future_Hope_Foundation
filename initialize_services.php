<?php
// Include database configuration
require_once 'includes/config.php';

// Array of foundation objectives as services
$services = [
    [
        'title' => 'Educational Programs for Orphans',
        'description' => '<p>We promote educational programs for orphans and vulnerable children in our communities. Our focus is on ensuring these children have access to quality education regardless of their circumstances.</p><p>Through our educational support programs, we provide scholarships, learning materials, and mentorship to help these children succeed academically and build a brighter future for themselves.</p>',
        'icon' => 'fas fa-graduation-cap',
        'order_number' => 1,
        'is_active' => 1
    ],
    [
        'title' => 'Child Rights Advocacy',
        'description' => '<p>We seek for the rights of children mainly through sensitization and advocacy with community leaders by conducting seminars, workshops and radio programmes.</p><p>Our advocacy efforts aim to create awareness about children\'s rights, address issues affecting children, and ensure their voices are heard in decision-making processes that affect them.</p>',
        'icon' => 'fas fa-bullhorn',
        'order_number' => 2,
        'is_active' => 1
    ],
    [
        'title' => 'Relief Aid & Rehabilitation',
        'description' => '<p>We initiate and promote programs concerned with provision of relief aid, rehabilitation and development of children.</p><p>Our relief programs address immediate needs during emergencies, while our rehabilitation initiatives help children recover from traumatic experiences and rebuild their lives. We provide food, shelter, clothing, and psychosocial support to children in need.</p>',
        'icon' => 'fas fa-hands-helping',
        'order_number' => 3,
        'is_active' => 1
    ],
    [
        'title' => 'Moral & Academic Development',
        'description' => '<p>We build an academically sound, moral upright and self-contained society through comprehensive development programs for children and youth.</p><p>Our approach focuses on holistic development, equipping children with not only academic knowledge but also strong moral values, life skills, and a sense of responsibility towards their communities.</p>',
        'icon' => 'fas fa-book-reader',
        'order_number' => 4,
        'is_active' => 1
    ],
    [
        'title' => 'Education Sponsorship',
        'description' => '<p>We encourage good and qualitative education through sponsorship of deserving students who might otherwise not be able to continue their education.</p><p>Our sponsorship program covers school fees, uniforms, books, and other educational needs, ensuring that financial constraints do not prevent children from accessing education.</p>',
        'icon' => 'fas fa-hand-holding-usd',
        'order_number' => 5,
        'is_active' => 1
    ],
    [
        'title' => 'Girl Child Education Support',
        'description' => '<p>We support girl child education by linking them to organisations that give scholarships in any part of the world.</p><p>Our special focus on girl child education aims to bridge the gender gap in education, empower girls, and provide them with opportunities for personal and professional development.</p>',
        'icon' => 'fas fa-female',
        'order_number' => 6,
        'is_active' => 1
    ],
    [
        'title' => 'Gender Awareness Programs',
        'description' => '<p>We undertake programs centred on children\'s right and gender awareness through rural community sensitisations and intimate training on gender issues so as to promote gender equality.</p><p>Our gender awareness initiatives aim to challenge harmful gender norms, promote equal opportunities for boys and girls, and create a more inclusive society where every child can thrive.</p>',
        'icon' => 'fas fa-balance-scale',
        'order_number' => 7,
        'is_active' => 1
    ],
    [
        'title' => 'Coordination with Other NGOs',
        'description' => '<p>We coordinate and work amicably with other organisations with similar intersections, aims and objectives to maximize our impact and reach more children in need.</p><p>By collaborating with other organizations, we can share resources, expertise, and best practices, creating a stronger network of support for vulnerable children and their families.</p>',
        'icon' => 'fas fa-handshake',
        'order_number' => 8,
        'is_active' => 1
    ]
];

// Check if services table is empty
$check_query = "SELECT COUNT(*) as count FROM services";
$result = $conn->query($check_query);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Table is empty, insert the services
    $stmt = $conn->prepare("INSERT INTO services (title, description, icon, order_number, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssii", $title, $description, $icon, $order_number, $is_active);
    
    $success = true;
    
    foreach ($services as $service) {
        $title = $service['title'];
        $description = $service['description'];
        $icon = $service['icon'];
        $order_number = $service['order_number'];
        $is_active = $service['is_active'];
        
        if (!$stmt->execute()) {
            $success = false;
            echo "Error inserting service: " . $conn->error;
            break;
        }
    }
    
    if ($success) {
        echo "Successfully inserted foundation objectives as services!";
    }
    
    $stmt->close();
} else {
    echo "Services table already has entries. No new services were added.";
}

$conn->close();
?>
