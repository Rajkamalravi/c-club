<?php
/**
 * Events V2 - Event Listing Page
 *
 * Modern event discovery with filters and search
 */

// Get filter parameters
$search = $_GET['search'] ?? '';
$event_types = $_GET['type'] ?? []; // Now an array
if (is_string($event_types)) {
    $event_types = !empty($event_types) ? [$event_types] : [];
}
$from_date = $_GET['from'] ?? '';
$to_date = $_GET['to'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

// Fetch events
$filters = [
    'search' => $search,
    'event_type' => $event_types, // Pass as array
    'from_date' => $from_date,
    'to_date' => $to_date,
];

$events_response = events_v2_get_list($filters, $limit, $offset);
$events = $events_response['output']['events'] ?? [];
$total_count = $events_response['output']['total_count'] ?? 0;
$total_pages = ceil($total_count / $limit);

// Page meta
$page_title = 'Discover Events';
$page_description = 'Find and join exciting virtual, in-person, and hybrid events.';
$page_css = 'listing';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/head.php'); ?>
    <title><?php echo htmlspecialchars($page_title); ?> | <?php echo TAOH_SITE_TITLE; ?></title>
</head>
<body class="ev2-body">
    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/header.php'); ?>

    <!-- Hero Section -->
    <section class="ev2-listing-hero">
        <div class="container">
            <div class="ev2-hero-content">
                <h1>Discover Events</h1>
                <p>Find virtual, in-person, and hybrid events that match your interests</p>

                <!-- Search Bar -->
                <form class="ev2-search-form" action="" method="GET">
                    <div class="ev2-search-wrapper">
                        <i class="bi bi-search ev2-search-icon"></i>
                        <input
                            type="text"
                            name="search"
                            class="ev2-search-input"
                            placeholder="Search events by name, topic, or keyword..."
                            value="<?php echo htmlspecialchars($search); ?>"
                            id="ev2-search-input"
                        >
                        <button type="submit" class="btn btn-primary ev2-search-btn">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="ev2-listing-main">
        <div class="container">
            <div class="row">
                <!-- Filters Sidebar (Desktop) -->
                <aside class="col-lg-3 ev2-filters-sidebar d-none d-lg-block">
                    <div class="ev2-filters-card">
                        <div class="ev2-filters-header">
                            <h3><i class="bi bi-funnel me-2"></i>Filters</h3>
                            <a href="<?php echo TAOH_EVENTS_V2_URL; ?>" class="ev2-clear-filters">Clear All</a>
                        </div>

                        <form id="ev2-filter-form" action="" method="GET">
                            <!-- Event Type -->
                            <div class="ev2-filter-section">
                                <h4>Event Type</h4>
                                <div class="ev2-filter-options">
                                    <label class="ev2-filter-checkbox">
                                        <input type="checkbox" name="type[]" value="virtual" <?php echo in_array('virtual', $event_types) ? 'checked' : ''; ?>>
                                        <span><i class="bi bi-camera-video me-1"></i>Virtual</span>
                                    </label>
                                    <label class="ev2-filter-checkbox">
                                        <input type="checkbox" name="type[]" value="in-person" <?php echo in_array('in-person', $event_types) ? 'checked' : ''; ?>>
                                        <span><i class="bi bi-geo-alt me-1"></i>In-Person</span>
                                    </label>
                                    <label class="ev2-filter-checkbox">
                                        <input type="checkbox" name="type[]" value="hybrid" <?php echo in_array('hybrid', $event_types) ? 'checked' : ''; ?>>
                                        <span><i class="bi bi-broadcast me-1"></i>Hybrid</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Date Range -->
                            <div class="ev2-filter-section">
                                <h4>Date Range</h4>
                                <div class="ev2-date-inputs">
                                    <div class="mb-3">
                                        <label class="form-label small">From</label>
                                        <input type="date" name="from" class="form-control" value="<?php echo htmlspecialchars($from_date); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small">To</label>
                                        <input type="date" name="to" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden search field to preserve search term -->
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">

                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </form>
                    </div>
                </aside>

                <!-- Events Grid -->
                <div class="col-lg-9">
                    <!-- Filter Bar (Mobile + Results Info) -->
                    <div class="ev2-filter-bar">
                        <div class="ev2-results-info">
                            <span><?php echo number_format($total_count); ?> events found</span>
                            <?php if (!empty($search)): ?>
                                <span class="ev2-search-tag">
                                    "<?php echo htmlspecialchars($search); ?>"
                                    <a href="<?php echo TAOH_EVENTS_V2_URL; ?>?type=<?php echo urlencode($event_type); ?>&from=<?php echo urlencode($from_date); ?>&to=<?php echo urlencode($to_date); ?>"><i class="bi bi-x"></i></a>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="ev2-filter-bar-actions">
                            <!-- Mobile Filter Button -->
                            <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#ev2-filters-offcanvas">
                                <i class="bi bi-funnel me-1"></i>Filters
                            </button>

                            <!-- View Toggle -->
                            <div class="ev2-view-toggle d-none d-md-flex">
                                <button class="ev2-view-toggle-btn active" data-view="grid" title="Grid View">
                                    <i class="bi bi-grid-3x3-gap"></i>
                                </button>
                                <button class="ev2-view-toggle-btn" data-view="list" title="List View">
                                    <i class="bi bi-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Events Container -->
                    <div class="ev2-events-container ev2-events-grid" id="ev2-events-container">
                        <?php if (empty($events)): ?>
                            <!-- Empty State -->
                            <div class="ev2-empty-state">
                                <div class="ev2-empty-icon">
                                    <i class="bi bi-calendar-x"></i>
                                </div>
                                <h3>No Events Found</h3>
                                <p>We couldn't find any events matching your criteria. Try adjusting your filters or search terms.</p>
                                <a href="<?php echo TAOH_EVENTS_V2_URL; ?>" class="btn btn-primary">View All Events</a>
                            </div>
                        <?php else: ?>
                                <?php foreach ($events as $event):
                                    $status_badge = events_v2_get_status_badge($event);
                                    $type_badge = events_v2_get_type_badge($event['event_type'] ?? 'virtual');
                                    $event_url = events_v2_event_url($event['eventtoken'], $event['url_slug'] ?? '');
                                    $event_image = events_v2_get_image($event);
                                    $event_date = events_v2_format_date($event['utc_start_at'], $event['locality'] ?? 0);
                                    $event_time = events_v2_format_time($event['utc_start_at'], $event['locality'] ?? 0);
                                ?>
                                    <article class="ev2-event-card">
                                        <a href="<?php echo htmlspecialchars($event_url); ?>" class="ev2-event-card-link">
                                            <div class="ev2-event-image">
                                                <img src="<?php echo htmlspecialchars($event_image); ?>" alt="<?php echo htmlspecialchars($event['title'] ?? 'Event'); ?>" loading="lazy">
                                                <div class="ev2-event-badges">
                                                    <span class="badge <?php echo $status_badge['class']; ?>"><?php echo $status_badge['label']; ?></span>
                                                    <span class="badge <?php echo $type_badge['class']; ?>">
                                                        <i class="<?php echo $type_badge['icon']; ?> me-1"></i><?php echo $type_badge['label']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ev2-event-content">
                                                <div class="ev2-event-date">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?php echo $event_date; ?> at <?php echo $event_time; ?>
                                                </div>
                                                <h3 class="ev2-event-title"><?php echo htmlspecialchars($event['title'] ?? 'Untitled Event'); ?></h3>
                                                <p class="ev2-event-description">
                                                    <?php echo events_v2_truncate($event['description'] ?? '', 100); ?>
                                                </p>
                                                <?php if (!empty($event['location'])): ?>
                                                    <div class="ev2-event-location">
                                                        <i class="bi bi-geo-alt me-1"></i>
                                                        <?php echo htmlspecialchars($event['location']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    </article>
                                <?php endforeach; ?>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav class="ev2-pagination" aria-label="Event pagination">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($event_type); ?>&from=<?php echo urlencode($from_date); ?>&to=<?php echo urlencode($to_date); ?>">
                                                    <i class="bi bi-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php
                                        $start_page = max(1, $page - 2);
                                        $end_page = min($total_pages, $page + 2);

                                        if ($start_page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=1&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($event_type); ?>&from=<?php echo urlencode($from_date); ?>&to=<?php echo urlencode($to_date); ?>">1</a>
                                            </li>
                                            <?php if ($start_page > 2): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($event_type); ?>&from=<?php echo urlencode($from_date); ?>&to=<?php echo urlencode($to_date); ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($end_page < $total_pages): ?>
                                            <?php if ($end_page < $total_pages - 1): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($event_type); ?>&from=<?php echo urlencode($from_date); ?>&to=<?php echo urlencode($to_date); ?>"><?php echo $total_pages; ?></a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($event_type); ?>&from=<?php echo urlencode($from_date); ?>&to=<?php echo urlencode($to_date); ?>">
                                                    <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Filters Offcanvas -->
    <div class="offcanvas offcanvas-start ev2-filters-offcanvas" tabindex="-1" id="ev2-filters-offcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="bi bi-funnel me-2"></i>Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="GET">
                <!-- Event Type -->
                <div class="ev2-filter-section">
                    <h4>Event Type</h4>
                    <div class="ev2-filter-options">
                        <label class="ev2-filter-checkbox">
                            <input type="checkbox" name="type[]" value="virtual" <?php echo in_array('virtual', $event_types) ? 'checked' : ''; ?>>
                            <span><i class="bi bi-camera-video me-1"></i>Virtual</span>
                        </label>
                        <label class="ev2-filter-checkbox">
                            <input type="checkbox" name="type[]" value="in-person" <?php echo in_array('in-person', $event_types) ? 'checked' : ''; ?>>
                            <span><i class="bi bi-geo-alt me-1"></i>In-Person</span>
                        </label>
                        <label class="ev2-filter-checkbox">
                            <input type="checkbox" name="type[]" value="hybrid" <?php echo in_array('hybrid', $event_types) ? 'checked' : ''; ?>>
                            <span><i class="bi bi-broadcast me-1"></i>Hybrid</span>
                        </label>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="ev2-filter-section">
                    <h4>Date Range</h4>
                    <div class="ev2-date-inputs">
                        <div class="mb-3">
                            <label class="form-label small">From</label>
                            <input type="date" name="from" class="form-control" value="<?php echo htmlspecialchars($from_date); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">To</label>
                            <input type="date" name="to" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="<?php echo TAOH_EVENTS_V2_URL; ?>" class="btn btn-outline-secondary">Clear All</a>
                </div>
            </form>
        </div>
    </div>

    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/footer.php'); ?>
    <script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-v2/listing.js"></script>

    <script>
        // Pass data to JS
        window.EVENTS_V2_URL = '<?php echo TAOH_EVENTS_V2_URL; ?>';
        window.EV2_EVENTS_DATA = {
            total_count: <?php echo $total_count; ?>,
            current_page: <?php echo $page; ?>,
            total_pages: <?php echo $total_pages; ?>
        };
    </script>
</body>
</html>
