<?php
/**
 * Events V2 - Event Detail Page
 *
 * Modern event detail with tabs, speakers, agenda, etc.
 */

// Get event token from URL
$url_slug = $goto ?? '';
$eventtoken = '';

// Parse event token from URL slug (format: slug-TOKEN or just TOKEN)
if (!empty($url_slug)) {
    $parts = explode('-', $url_slug);
    $eventtoken = end($parts);
}

// Fetch event details
$event = events_v2_get_detail($eventtoken);

if (!$event) {
    // Redirect to 404 or show error
    include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/error.php');
    die();
}

// Fetch event meta (speakers, exhibitors, sponsors)
$event_meta = events_v2_get_meta($eventtoken);
$speakers = $event_meta['event_speaker'] ?? [];
$exhibitors = $event_meta['event_exhibitor'] ?? [];
$sponsors = $event_meta['event_sponsor'] ?? [];

// Event details
$status_badge = events_v2_get_status_badge($event);
$type_badge = events_v2_get_type_badge($event['event_type'] ?? 'virtual');
$event_image = events_v2_get_image($event);
$event_date = events_v2_format_date($event['utc_start_at'], $event['locality'] ?? 0);
$event_time = events_v2_format_time($event['utc_start_at'], $event['locality'] ?? 0);
$event_end_date = events_v2_format_date($event['utc_end_at'], $event['locality'] ?? 0);
$event_end_time = events_v2_format_time($event['utc_end_at'], $event['locality'] ?? 0);
$has_rsvp = events_v2_has_rsvp($event);
$rsvp_url = events_v2_rsvp_url($eventtoken);

// Page meta
$page_title = $event['title'] ?? 'Event Details';
$page_description = events_v2_truncate($event['description'] ?? '', 160);
$page_css = 'detail';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/head.php'); ?>
    <title><?php echo htmlspecialchars($page_title); ?> | <?php echo TAOH_SITE_TITLE; ?></title>

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($event_image); ?>">
    <meta property="og:type" content="event">
</head>
<body class="ev2-body">
    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/header.php'); ?>

    <!-- Hero Section -->
    <section class="ev2-detail-hero" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('<?php echo htmlspecialchars($event_image); ?>');">
        <div class="container">
            <nav aria-label="breadcrumb" class="ev2-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo TAOH_EVENTS_V2_URL; ?>">Events</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars(events_v2_truncate($page_title, 40)); ?></li>
                </ol>
            </nav>

            <div class="ev2-hero-badges">
                <span class="badge <?php echo $status_badge['class']; ?> ev2-status-badge"><?php echo $status_badge['label']; ?></span>
                <span class="badge <?php echo $type_badge['class']; ?>">
                    <i class="<?php echo $type_badge['icon']; ?> me-1"></i><?php echo $type_badge['label']; ?>
                </span>
            </div>

            <h1 class="ev2-detail-title"><?php echo htmlspecialchars($page_title); ?></h1>

            <div class="ev2-hero-meta">
                <div class="ev2-meta-item">
                    <i class="bi bi-calendar3"></i>
                    <span><?php echo $event_date; ?></span>
                </div>
                <div class="ev2-meta-item">
                    <i class="bi bi-clock"></i>
                    <span><?php echo $event_time; ?> - <?php echo $event_end_time; ?></span>
                </div>
                <?php if (!empty($event['location'])): ?>
                    <div class="ev2-meta-item">
                        <i class="bi bi-geo-alt"></i>
                        <span><?php echo htmlspecialchars($event['location']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!$has_rsvp && $status_badge['status'] !== 'ended'): ?>
                <a href="<?php echo $rsvp_url; ?>" class="btn btn-primary btn-lg ev2-hero-cta">
                    <i class="bi bi-ticket-perforated me-2"></i>Register Now
                </a>
            <?php elseif ($has_rsvp): ?>
                <span class="btn btn-success btn-lg ev2-hero-cta" disabled>
                    <i class="bi bi-check-circle me-2"></i>Already Registered
                </span>
            <?php endif; ?>
        </div>
    </section>

    <!-- Sticky CTA Bar -->
    <div class="ev2-sticky-cta" id="ev2-sticky-cta">
        <div class="container">
            <div class="ev2-sticky-cta-content">
                <div class="ev2-sticky-info">
                    <h4><?php echo htmlspecialchars(events_v2_truncate($page_title, 50)); ?></h4>
                    <span><?php echo $event_date; ?> at <?php echo $event_time; ?></span>
                </div>
                <?php if (!$has_rsvp && $status_badge['status'] !== 'ended'): ?>
                    <a href="<?php echo $rsvp_url; ?>" class="btn btn-primary">Register Now</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="ev2-detail-main">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs ev2-detail-tabs" id="ev2-detail-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="about-tab" data-bs-toggle="tab" data-bs-target="#about-panel" type="button" role="tab">
                                <i class="bi bi-info-circle me-1"></i>About
                            </button>
                        </li>
                        <?php if (!empty($event['agenda'])): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="agenda-tab" data-bs-toggle="tab" data-bs-target="#agenda-panel" type="button" role="tab">
                                    <i class="bi bi-calendar-week me-1"></i>Agenda
                                </button>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($speakers)): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="speakers-tab" data-bs-toggle="tab" data-bs-target="#speakers-panel" type="button" role="tab">
                                    <i class="bi bi-people me-1"></i>Speakers
                                    <span class="badge bg-secondary ms-1"><?php echo count($speakers); ?></span>
                                </button>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($exhibitors)): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="exhibitors-tab" data-bs-toggle="tab" data-bs-target="#exhibitors-panel" type="button" role="tab">
                                    <i class="bi bi-shop me-1"></i>Exhibitors
                                    <span class="badge bg-secondary ms-1"><?php echo count($exhibitors); ?></span>
                                </button>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($sponsors)): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sponsors-tab" data-bs-toggle="tab" data-bs-target="#sponsors-panel" type="button" role="tab">
                                    <i class="bi bi-award me-1"></i>Sponsors
                                </button>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content ev2-tab-content" id="ev2-tab-content">
                        <!-- About Panel -->
                        <div class="tab-pane fade show active" id="about-panel" role="tabpanel">
                            <div class="ev2-about-content">
                                <?php echo $event['description'] ?? '<p>No description available.</p>'; ?>
                            </div>

                            <!-- Event Highlights -->
                            <?php if (!empty($event['highlights'])): ?>
                                <div class="ev2-highlights">
                                    <h3>Event Highlights</h3>
                                    <ul>
                                        <?php foreach ($event['highlights'] as $highlight): ?>
                                            <li><?php echo htmlspecialchars($highlight); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Agenda Panel -->
                        <?php if (!empty($event['agenda'])): ?>
                            <div class="tab-pane fade" id="agenda-panel" role="tabpanel">
                                <div class="ev2-agenda">
                                    <?php echo $event['agenda']; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Speakers Panel -->
                        <?php if (!empty($speakers)): ?>
                            <div class="tab-pane fade" id="speakers-panel" role="tabpanel">
                                <div class="ev2-speakers-grid">
                                    <?php foreach ($speakers as $speaker): ?>
                                        <div class="ev2-speaker-card">
                                            <div class="ev2-speaker-image">
                                                <img src="<?php echo htmlspecialchars($speaker['image'] ?? TAOH_SITE_URL_ROOT . '/assets/images/user.png'); ?>" alt="<?php echo htmlspecialchars($speaker['name'] ?? 'Speaker'); ?>">
                                            </div>
                                            <div class="ev2-speaker-info">
                                                <h4><?php echo htmlspecialchars($speaker['name'] ?? 'Speaker'); ?></h4>
                                                <p class="ev2-speaker-title"><?php echo htmlspecialchars($speaker['title'] ?? ''); ?></p>
                                                <p class="ev2-speaker-company"><?php echo htmlspecialchars($speaker['company'] ?? ''); ?></p>
                                                <?php if (!empty($speaker['linkedin'])): ?>
                                                    <a href="<?php echo htmlspecialchars($speaker['linkedin']); ?>" target="_blank" class="ev2-speaker-social">
                                                        <i class="bi bi-linkedin"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Exhibitors Panel -->
                        <?php if (!empty($exhibitors)): ?>
                            <div class="tab-pane fade" id="exhibitors-panel" role="tabpanel">
                                <div class="ev2-exhibitors-grid">
                                    <?php foreach ($exhibitors as $exhibitor): ?>
                                        <div class="ev2-exhibitor-card">
                                            <div class="ev2-exhibitor-logo">
                                                <img src="<?php echo htmlspecialchars($exhibitor['logo'] ?? TAOH_SITE_URL_ROOT . '/assets/images/company.png'); ?>" alt="<?php echo htmlspecialchars($exhibitor['name'] ?? 'Exhibitor'); ?>">
                                            </div>
                                            <h4><?php echo htmlspecialchars($exhibitor['name'] ?? 'Exhibitor'); ?></h4>
                                            <p><?php echo htmlspecialchars(events_v2_truncate($exhibitor['description'] ?? '', 80)); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Sponsors Panel -->
                        <?php if (!empty($sponsors)): ?>
                            <div class="tab-pane fade" id="sponsors-panel" role="tabpanel">
                                <div class="ev2-sponsors-list">
                                    <?php
                                    $sponsor_tiers = ['platinum' => [], 'gold' => [], 'silver' => [], 'bronze' => [], 'other' => []];
                                    foreach ($sponsors as $sponsor) {
                                        $tier = strtolower($sponsor['tier'] ?? 'other');
                                        if (!isset($sponsor_tiers[$tier])) $tier = 'other';
                                        $sponsor_tiers[$tier][] = $sponsor;
                                    }
                                    ?>
                                    <?php foreach ($sponsor_tiers as $tier => $tier_sponsors): ?>
                                        <?php if (!empty($tier_sponsors)): ?>
                                            <div class="ev2-sponsor-tier">
                                                <h4 class="ev2-tier-title"><?php echo ucfirst($tier); ?> Sponsors</h4>
                                                <div class="ev2-sponsor-logos ev2-sponsor-<?php echo $tier; ?>">
                                                    <?php foreach ($tier_sponsors as $sponsor): ?>
                                                        <div class="ev2-sponsor-logo">
                                                            <img src="<?php echo htmlspecialchars($sponsor['logo'] ?? ''); ?>" alt="<?php echo htmlspecialchars($sponsor['name'] ?? 'Sponsor'); ?>">
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="col-lg-4">
                    <div class="ev2-sidebar">
                        <!-- Event Info Card -->
                        <div class="ev2-sidebar-card">
                            <h3>Event Details</h3>
                            <ul class="ev2-sidebar-list">
                                <li>
                                    <i class="bi bi-calendar3"></i>
                                    <div>
                                        <strong>Date</strong>
                                        <span><?php echo $event_date; ?></span>
                                    </div>
                                </li>
                                <li>
                                    <i class="bi bi-clock"></i>
                                    <div>
                                        <strong>Time</strong>
                                        <span><?php echo $event_time; ?> - <?php echo $event_end_time; ?></span>
                                    </div>
                                </li>
                                <li>
                                    <i class="bi bi-globe"></i>
                                    <div>
                                        <strong>Timezone</strong>
                                        <span><?php echo taoh_user_timezone(); ?></span>
                                    </div>
                                </li>
                                <?php if (!empty($event['location'])): ?>
                                    <li>
                                        <i class="bi bi-geo-alt"></i>
                                        <div>
                                            <strong>Location</strong>
                                            <span><?php echo htmlspecialchars($event['location']); ?></span>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <i class="<?php echo $type_badge['icon']; ?>"></i>
                                    <div>
                                        <strong>Event Type</strong>
                                        <span><?php echo $type_badge['label']; ?></span>
                                    </div>
                                </li>
                            </ul>

                            <?php if (!$has_rsvp && $status_badge['status'] !== 'ended'): ?>
                                <a href="<?php echo $rsvp_url; ?>" class="btn btn-primary w-100 mt-3">
                                    <i class="bi bi-ticket-perforated me-2"></i>Register for this Event
                                </a>
                            <?php elseif ($has_rsvp): ?>
                                <button class="btn btn-success w-100 mt-3" disabled>
                                    <i class="bi bi-check-circle me-2"></i>You're Registered
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100 mt-3" disabled>
                                    <i class="bi bi-clock-history me-2"></i>Event Has Ended
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- Share Card -->
                        <div class="ev2-sidebar-card">
                            <h3>Share Event</h3>
                            <div class="ev2-share-buttons">
                                <a href="#" class="ev2-share-btn ev2-share-twitter" data-share="twitter" title="Share on Twitter">
                                    <i class="bi bi-twitter-x"></i>
                                </a>
                                <a href="#" class="ev2-share-btn ev2-share-linkedin" data-share="linkedin" title="Share on LinkedIn">
                                    <i class="bi bi-linkedin"></i>
                                </a>
                                <a href="#" class="ev2-share-btn ev2-share-facebook" data-share="facebook" title="Share on Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="#" class="ev2-share-btn ev2-share-email" data-share="email" title="Share via Email">
                                    <i class="bi bi-envelope"></i>
                                </a>
                                <button class="ev2-share-btn ev2-share-copy" data-share="copy" title="Copy Link">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Add to Calendar -->
                        <div class="ev2-sidebar-card">
                            <h3>Add to Calendar</h3>
                            <div class="ev2-calendar-buttons">
                                <a href="#" class="ev2-calendar-btn" data-calendar="google">
                                    <i class="bi bi-google"></i> Google Calendar
                                </a>
                                <a href="#" class="ev2-calendar-btn" data-calendar="outlook">
                                    <i class="bi bi-microsoft"></i> Outlook
                                </a>
                                <a href="#" class="ev2-calendar-btn" data-calendar="ical">
                                    <i class="bi bi-calendar-plus"></i> iCal / Apple
                                </a>
                            </div>
                        </div>

                        <!-- Organizer Card -->
                        <?php if (!empty($event['organizer'])): ?>
                            <div class="ev2-sidebar-card">
                                <h3>Organizer</h3>
                                <div class="ev2-organizer">
                                    <div class="ev2-organizer-logo">
                                        <img src="<?php echo htmlspecialchars($event['organizer']['logo'] ?? TAOH_SITE_URL_ROOT . '/assets/images/company.png'); ?>" alt="Organizer">
                                    </div>
                                    <div class="ev2-organizer-info">
                                        <h4><?php echo htmlspecialchars($event['organizer']['name'] ?? ''); ?></h4>
                                        <?php if (!empty($event['organizer']['website'])): ?>
                                            <a href="<?php echo htmlspecialchars($event['organizer']['website']); ?>" target="_blank">
                                                <i class="bi bi-globe me-1"></i>Visit Website
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/footer.php'); ?>
    <script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-v2/detail.js"></script>

    <script>
        // Pass event data to JS
        window.EVENTS_V2_URL = '<?php echo TAOH_EVENTS_V2_URL; ?>';
        window.EV2_EVENT_DATA = {
            eventtoken: '<?php echo htmlspecialchars($eventtoken); ?>',
            title: '<?php echo addslashes($page_title); ?>',
            description: '<?php echo addslashes($page_description); ?>',
            start_date: '<?php echo $event['utc_start_at'] ?? ''; ?>',
            end_date: '<?php echo $event['utc_end_at'] ?? ''; ?>',
            location: '<?php echo addslashes($event['location'] ?? ''); ?>',
            url: '<?php echo TAOH_EVENTS_V2_URL . '/d/' . ($event['url_slug'] ?? '') . '-' . $eventtoken; ?>'
        };
    </script>
</body>
</html>
