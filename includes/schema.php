<?php
/**
 * Generate Schema.org structured data for better SEO
 * Include this file in the footer to improve search engine visibility
 */
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "NGO",
    "name": "<?php echo htmlspecialchars($settings['site_name']); ?>",
    "url": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>",
    <?php if (!empty($settings['site_logo'])): ?>
    "logo": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . $settings['site_logo']; ?>",
    <?php endif; ?>
    <?php if (!empty($settings['about_content'])): ?>
    "description": "<?php echo htmlspecialchars(strip_tags(truncateText($settings['about_content'], 150))); ?>",
    <?php endif; ?>
    "sameAs": [
        <?php
        $social_links = [];
        if (!empty($settings['facebook_url'])) $social_links[] = '"' . htmlspecialchars($settings['facebook_url']) . '"';
        if (!empty($settings['twitter_url'])) $social_links[] = '"' . htmlspecialchars($settings['twitter_url']) . '"';
        if (!empty($settings['instagram_url'])) $social_links[] = '"' . htmlspecialchars($settings['instagram_url']) . '"';
        if (!empty($settings['youtube_url'])) $social_links[] = '"' . htmlspecialchars($settings['youtube_url']) . '"';
        echo implode(', ', $social_links);
        ?>
    ],
    <?php if (!empty($settings['site_address'])): ?>
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "<?php echo htmlspecialchars($settings['site_address']); ?>"
    },
    <?php endif; ?>
    <?php if (!empty($settings['site_email'])): ?>
    "email": "<?php echo htmlspecialchars($settings['site_email']); ?>",
    <?php endif; ?>
    <?php if (!empty($settings['site_phone'])): ?>
    "telephone": "<?php echo htmlspecialchars($settings['site_phone']); ?>",
    <?php endif; ?>
    "foundingDate": "2014",
    "nonprofitStatus": "Nonprofit",
    "serviceType": "Humanitarian Aid",
    "slogan": "Building hope for a better future",
    "memberOf": [
        {
            "@type": "Organization",
            "name": "Humanitarian Organizations Network"
        }
    ]
}
</script>

<?php
// Add organization schema if we're on the homepage
if (basename($_SERVER['PHP_SELF']) == 'index.php'):
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "url": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>",
    "name": "<?php echo htmlspecialchars($settings['site_name']); ?>",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/search.php?q={search_term_string}"; ?>",
        "query-input": "required name=search_term_string"
    }
}
</script>
<?php endif; ?>

<?php
// Add breadcrumbs schema if we're not on the homepage
if (basename($_SERVER['PHP_SELF']) != 'index.php'):
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; ?>"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "<?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Page'; ?>",
            "item": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>"
        }
    ]
}
</script>
<?php endif; ?>

<?php
// Add events schema if we're on the events page
if (basename($_SERVER['PHP_SELF']) == 'events.php' && !empty($upcoming_events)):
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ItemList",
    "itemListElement": [
    <?php
    $event_items = [];
    foreach ($upcoming_events as $index => $event) {
        $event_items[] = '{
            "@type": "ListItem",
            "position": ' . ($index + 1) . ',
            "item": {
                "@type": "Event",
                "name": "' . htmlspecialchars($event['title']) . '",
                "startDate": "' . $event['event_date'] . 'T' . $event['event_time'] . '",
                "location": {
                    "@type": "Place",
                    "name": "' . htmlspecialchars($event['location']) . '"
                },
                "organizer": {
                    "@type": "Organization",
                    "name": "' . htmlspecialchars($settings['site_name']) . '"
                },
                "image": "' . (!empty($event['image']) ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . getImageUrl($event['image'])) : '') . '",
                "description": "' . htmlspecialchars(strip_tags(truncateText($event['description'], 150))) . '"
            }
        }';
    }
    echo implode(', ', $event_items);
    ?>
    ]
}
</script>
<?php endif; ?>
