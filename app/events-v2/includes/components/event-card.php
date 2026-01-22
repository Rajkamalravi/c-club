<?php
/**
 * Events V2 - Event Card Component
 *
 * Reusable event card for listing pages
 *
 * Usage:
 * include('components/event-card.php');
 * ev2_render_event_card($event_data);
 */

/**
 * Render an event card
 *
 * @param array $event Event data array
 * @param string $size Card size: 'default', 'small', 'large'
 * @return void
 */
function ev2_render_event_card($event, $size = 'default') {
    $status_badge = events_v2_get_status_badge($event);
    $type_badge = events_v2_get_type_badge($event['event_type'] ?? 'virtual');
    $event_url = events_v2_event_url($event['eventtoken'], $event['url_slug'] ?? '');
    $event_image = events_v2_get_image($event);
    $event_date = events_v2_format_date($event['utc_start_at'], $event['locality'] ?? 0);
    $event_time = events_v2_format_time($event['utc_start_at'], $event['locality'] ?? 0);

    $size_class = $size !== 'default' ? "ev2-event-card--{$size}" : '';
    ?>
    <article class="ev2-event-card <?php echo $size_class; ?>">
        <a href="<?php echo htmlspecialchars($event_url); ?>" class="ev2-event-card-link">
            <div class="ev2-event-image">
                <img
                    src="<?php echo htmlspecialchars($event_image); ?>"
                    alt="<?php echo htmlspecialchars($event['title'] ?? 'Event'); ?>"
                    loading="lazy"
                >
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
                <?php if ($size !== 'small'): ?>
                    <p class="ev2-event-description">
                        <?php echo events_v2_truncate($event['description'] ?? '', 100); ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($event['location'])): ?>
                    <div class="ev2-event-location">
                        <i class="bi bi-geo-alt me-1"></i>
                        <?php echo htmlspecialchars($event['location']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </a>
    </article>
    <?php
}

/**
 * Render a horizontal event card (for lists)
 *
 * @param array $event Event data array
 * @return void
 */
function ev2_render_event_card_horizontal($event) {
    $status_badge = events_v2_get_status_badge($event);
    $type_badge = events_v2_get_type_badge($event['event_type'] ?? 'virtual');
    $event_url = events_v2_event_url($event['eventtoken'], $event['url_slug'] ?? '');
    $event_image = events_v2_get_image($event);
    $event_date = events_v2_format_date($event['utc_start_at'], $event['locality'] ?? 0);
    $event_time = events_v2_format_time($event['utc_start_at'], $event['locality'] ?? 0);
    ?>
    <article class="ev2-event-card-horizontal">
        <a href="<?php echo htmlspecialchars($event_url); ?>" class="ev2-event-card-link">
            <div class="ev2-event-image">
                <img
                    src="<?php echo htmlspecialchars($event_image); ?>"
                    alt="<?php echo htmlspecialchars($event['title'] ?? 'Event'); ?>"
                    loading="lazy"
                >
            </div>
            <div class="ev2-event-content">
                <div class="ev2-event-meta">
                    <span class="badge <?php echo $status_badge['class']; ?> badge-sm"><?php echo $status_badge['label']; ?></span>
                    <span class="badge <?php echo $type_badge['class']; ?> badge-sm">
                        <i class="<?php echo $type_badge['icon']; ?>"></i>
                    </span>
                    <span class="ev2-event-date">
                        <i class="bi bi-calendar3 me-1"></i><?php echo $event_date; ?>
                    </span>
                </div>
                <h3 class="ev2-event-title"><?php echo htmlspecialchars($event['title'] ?? 'Untitled Event'); ?></h3>
                <p class="ev2-event-description">
                    <?php echo events_v2_truncate($event['description'] ?? '', 150); ?>
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
    <?php
}
