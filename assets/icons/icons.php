<?php
/**
 * Global Icons Helper
 *
 * Provides a unified way to render icons across the entire site.
 * Uses FontAwesome where available, falls back to custom SVG sprite.
 *
 * USAGE:
 *   <?php include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php'; ?>
 *   <?= icon('calendar') ?>
 *   <?= icon('location', '#b4b4b4', 14) ?>
 *   <?= icon('ticket', 'text-primary') ?>
 */

// Base path for SVG sprite
define('ICON_SPRITE_PATH', '/raj/assets/icons/sprite.svg');

// FontAwesome icon mappings
$GLOBALS['FA_ICONS'] = [
    // Calendar & Time
    'calendar'          => 'fa-regular fa-calendar',
    'calendar-solid'    => 'fa-solid fa-calendar',
    'calendar-days'     => 'fa-solid fa-calendar-days',
    'calendar-plus'     => 'fa-solid fa-calendar-plus',
    'calendar-check'    => 'fa-regular fa-calendar-check',
    'clock'             => 'fa-regular fa-clock',
    'clock-solid'       => 'fa-solid fa-clock',

    // Location
    'location'          => 'fa-solid fa-location-dot',
    'location-pin'      => 'fa-solid fa-map-pin',
    'map'               => 'fa-solid fa-map',
    'map-marker'        => 'fa-solid fa-map-marker-alt',
    'directions'        => 'fa-solid fa-diamond-turn-right',
    'compass'           => 'fa-regular fa-compass',

    // Buildings & Places
    'building'          => 'fa-solid fa-building',
    'building-o'        => 'fa-regular fa-building',
    'home'              => 'fa-solid fa-house',
    'office'            => 'fa-solid fa-building-columns',
    'city'              => 'fa-solid fa-city',

    // Users & People
    'user'              => 'fa-solid fa-user',
    'user-o'            => 'fa-regular fa-user',
    'user-plus'         => 'fa-solid fa-user-plus',
    'user-check'        => 'fa-solid fa-user-check',
    'user-group'        => 'fa-solid fa-user-group',
    'users'             => 'fa-solid fa-users',
    'people'            => 'fa-solid fa-people-group',
    'person'            => 'fa-solid fa-person',

    // Communication
    'envelope'          => 'fa-solid fa-envelope',
    'envelope-o'        => 'fa-regular fa-envelope',
    'comment'           => 'fa-regular fa-comment',
    'comment-solid'     => 'fa-solid fa-comment',
    'comments'          => 'fa-regular fa-comments',
    'message'           => 'fa-regular fa-message',
    'phone'             => 'fa-solid fa-phone',
    'bell'              => 'fa-solid fa-bell',
    'bell-o'            => 'fa-regular fa-bell',

    // Social Media
    'linkedin'          => 'fa-brands fa-linkedin-in',
    'twitter'           => 'fa-brands fa-x-twitter',
    'facebook'          => 'fa-brands fa-facebook-f',
    'instagram'         => 'fa-brands fa-instagram',
    'github'            => 'fa-brands fa-github',
    'youtube'           => 'fa-brands fa-youtube',
    'whatsapp'          => 'fa-brands fa-whatsapp',
    'telegram'          => 'fa-brands fa-telegram',
    'tiktok'            => 'fa-brands fa-tiktok',

    // Actions
    'share'             => 'fa-solid fa-share-nodes',
    'share-alt'         => 'fa-solid fa-share-alt',
    'link'              => 'fa-solid fa-link',
    'copy'              => 'fa-regular fa-copy',
    'paste'             => 'fa-solid fa-paste',
    'edit'              => 'fa-solid fa-pen',
    'edit-o'            => 'fa-regular fa-pen-to-square',
    'trash'             => 'fa-solid fa-trash',
    'trash-o'           => 'fa-regular fa-trash-can',
    'download'          => 'fa-solid fa-download',
    'upload'            => 'fa-solid fa-upload',
    'save'              => 'fa-solid fa-floppy-disk',
    'print'             => 'fa-solid fa-print',

    // Navigation
    'chevron-up'        => 'fa-solid fa-chevron-up',
    'chevron-down'      => 'fa-solid fa-chevron-down',
    'chevron-left'      => 'fa-solid fa-chevron-left',
    'chevron-right'     => 'fa-solid fa-chevron-right',
    'arrow-up'          => 'fa-solid fa-arrow-up',
    'arrow-down'        => 'fa-solid fa-arrow-down',
    'arrow-left'        => 'fa-solid fa-arrow-left',
    'arrow-right'       => 'fa-solid fa-arrow-right',
    'angles-left'       => 'fa-solid fa-angles-left',
    'angles-right'      => 'fa-solid fa-angles-right',
    'external-link'     => 'fa-solid fa-arrow-up-right-from-square',

    // Status & Feedback
    'check'             => 'fa-solid fa-check',
    'check-circle'      => 'fa-solid fa-circle-check',
    'check-circle-o'    => 'fa-regular fa-circle-check',
    'times'             => 'fa-solid fa-xmark',
    'close'             => 'fa-solid fa-xmark',
    'times-circle'      => 'fa-solid fa-circle-xmark',
    'info'              => 'fa-solid fa-circle-info',
    'info-circle'       => 'fa-solid fa-circle-info',
    'warning'           => 'fa-solid fa-triangle-exclamation',
    'exclamation'       => 'fa-solid fa-circle-exclamation',
    'question'          => 'fa-solid fa-circle-question',

    // Favorites & Rating
    'heart'             => 'fa-regular fa-heart',
    'heart-solid'       => 'fa-solid fa-heart',
    'star'              => 'fa-regular fa-star',
    'star-solid'        => 'fa-solid fa-star',
    'star-half'         => 'fa-solid fa-star-half-stroke',
    'bookmark'          => 'fa-regular fa-bookmark',
    'bookmark-solid'    => 'fa-solid fa-bookmark',
    'thumbs-up'         => 'fa-solid fa-thumbs-up',
    'thumbs-up-o'       => 'fa-regular fa-thumbs-up',
    'thumbs-down'       => 'fa-solid fa-thumbs-down',

    // Media & Content
    'image'             => 'fa-regular fa-image',
    'images'            => 'fa-solid fa-images',
    'video'             => 'fa-solid fa-video',
    'camera'            => 'fa-solid fa-camera',
    'microphone'        => 'fa-solid fa-microphone',
    'microphone-lines'  => 'fa-solid fa-microphone-lines',
    'play'              => 'fa-solid fa-play',
    'pause'             => 'fa-solid fa-pause',
    'stop'              => 'fa-solid fa-stop',
    'volume-up'         => 'fa-solid fa-volume-high',
    'volume-mute'       => 'fa-solid fa-volume-xmark',

    // Files & Documents
    'file'              => 'fa-regular fa-file',
    'file-solid'        => 'fa-solid fa-file',
    'file-text'         => 'fa-regular fa-file-lines',
    'file-pdf'          => 'fa-solid fa-file-pdf',
    'file-word'         => 'fa-solid fa-file-word',
    'file-excel'        => 'fa-solid fa-file-excel',
    'file-image'        => 'fa-solid fa-file-image',
    'file-code'         => 'fa-solid fa-file-code',
    'folder'            => 'fa-solid fa-folder',
    'folder-open'       => 'fa-solid fa-folder-open',

    // UI Elements
    'search'            => 'fa-solid fa-magnifying-glass',
    'filter'            => 'fa-solid fa-filter',
    'sliders'           => 'fa-solid fa-sliders',
    'sort'              => 'fa-solid fa-sort',
    'sort-up'           => 'fa-solid fa-sort-up',
    'sort-down'         => 'fa-solid fa-sort-down',
    'plus'              => 'fa-solid fa-plus',
    'minus'             => 'fa-solid fa-minus',
    'bars'              => 'fa-solid fa-bars',
    'menu'              => 'fa-solid fa-bars',
    'ellipsis'          => 'fa-solid fa-ellipsis',
    'ellipsis-v'        => 'fa-solid fa-ellipsis-vertical',
    'grip'              => 'fa-solid fa-grip',
    'list'              => 'fa-solid fa-list',
    'table'             => 'fa-solid fa-table',
    'columns'           => 'fa-solid fa-table-columns',
    'eye'               => 'fa-solid fa-eye',
    'eye-slash'         => 'fa-solid fa-eye-slash',
    'eye-o'             => 'fa-regular fa-eye',

    // E-commerce & Business
    'cart'              => 'fa-solid fa-cart-shopping',
    'bag'               => 'fa-solid fa-bag-shopping',
    'credit-card'       => 'fa-solid fa-credit-card',
    'money'             => 'fa-solid fa-money-bill',
    'wallet'            => 'fa-solid fa-wallet',
    'receipt'           => 'fa-solid fa-receipt',
    'tag'               => 'fa-solid fa-tag',
    'tags'              => 'fa-solid fa-tags',
    'percent'           => 'fa-solid fa-percent',
    'barcode'           => 'fa-solid fa-barcode',
    'qrcode'            => 'fa-solid fa-qrcode',

    // Tickets & Events
    'ticket'            => 'fa-solid fa-ticket',
    'ticket-alt'        => 'fa-solid fa-ticket-simple',
    'calendar-event'    => 'fa-solid fa-calendar-day',
    'clock-rotate'      => 'fa-solid fa-clock-rotate-left',
    'hourglass'         => 'fa-solid fa-hourglass',

    // Work & Professional
    'briefcase'         => 'fa-solid fa-briefcase',
    'suitcase'          => 'fa-solid fa-suitcase',
    'id-card'           => 'fa-solid fa-id-card',
    'id-badge'          => 'fa-solid fa-id-badge',
    'graduation-cap'    => 'fa-solid fa-graduation-cap',
    'certificate'       => 'fa-solid fa-certificate',
    'award'             => 'fa-solid fa-award',
    'trophy'            => 'fa-solid fa-trophy',
    'medal'             => 'fa-solid fa-medal',
    'crown'             => 'fa-solid fa-crown',

    // Technology
    'laptop'            => 'fa-solid fa-laptop',
    'desktop'           => 'fa-solid fa-desktop',
    'mobile'            => 'fa-solid fa-mobile-screen-button',
    'tablet'            => 'fa-solid fa-tablet-screen-button',
    'wifi'              => 'fa-solid fa-wifi',
    'signal'            => 'fa-solid fa-signal',
    'globe'             => 'fa-solid fa-globe',
    'globe-americas'    => 'fa-solid fa-earth-americas',
    'code'              => 'fa-solid fa-code',
    'terminal'          => 'fa-solid fa-terminal',
    'database'          => 'fa-solid fa-database',
    'server'            => 'fa-solid fa-server',
    'cloud'             => 'fa-solid fa-cloud',
    'cloud-upload'      => 'fa-solid fa-cloud-arrow-up',
    'cloud-download'    => 'fa-solid fa-cloud-arrow-down',

    // Security
    'lock'              => 'fa-solid fa-lock',
    'unlock'            => 'fa-solid fa-unlock',
    'key'               => 'fa-solid fa-key',
    'shield'            => 'fa-solid fa-shield',
    'shield-check'      => 'fa-solid fa-shield-halved',
    'user-shield'       => 'fa-solid fa-user-shield',

    // Misc
    'fire'              => 'fa-solid fa-fire',
    'bolt'              => 'fa-solid fa-bolt',
    'zap'               => 'fa-solid fa-bolt',
    'sun'               => 'fa-solid fa-sun',
    'moon'              => 'fa-solid fa-moon',
    'gear'              => 'fa-solid fa-gear',
    'cog'               => 'fa-solid fa-gear',
    'gears'             => 'fa-solid fa-gears',
    'wrench'            => 'fa-solid fa-wrench',
    'tools'             => 'fa-solid fa-screwdriver-wrench',
    'magic'             => 'fa-solid fa-wand-magic-sparkles',
    'refresh'           => 'fa-solid fa-arrows-rotate',
    'sync'              => 'fa-solid fa-arrows-rotate',
    'redo'              => 'fa-solid fa-rotate-right',
    'undo'              => 'fa-solid fa-rotate-left',
    'spinner'           => 'fa-solid fa-spinner',
    'circle-notch'      => 'fa-solid fa-circle-notch',
    'gift'              => 'fa-solid fa-gift',
    'handshake'         => 'fa-regular fa-handshake',
    'lightbulb'         => 'fa-regular fa-lightbulb',
    'rocket'            => 'fa-solid fa-rocket',
    'paper-plane'       => 'fa-solid fa-paper-plane',
    'flag'              => 'fa-solid fa-flag',
    'flag-o'            => 'fa-regular fa-flag',
    'pin'               => 'fa-solid fa-thumbtack',
    'quote'             => 'fa-solid fa-quote-left',
    'at'                => 'fa-solid fa-at',
    'hashtag'           => 'fa-solid fa-hashtag',
];

// Custom SVG icons (in sprite.svg)
$GLOBALS['SVG_ICONS'] = [
    'location-pin',
    'location-dot',
    'building',
    'building-simple',
    'add-user',
    'user',
    'users',
    'followers',
    'ticket',
    'event',
    'calendar-check',
    'skills',
    'star',
    'badge',
    'chat',
    'message',
    'smiley',
    'check',
    'info',
    'globe',
    'video',
    'hybrid',
    'venue',
    'chevron-right',
    'chevron-left',
    'chevron-down',
    'chevron-up',
    'arrow-left',
    'share',
    'heart',
    'bookmark',
    'plus',
    'close',
    'edit',
    'trash',
    'search',
    'filter',
    'briefcase',
    'link',
    'external',
    'clock',
    'bell',
    'copy',
    'download',
    'upload',
    'eye',
    'fire',
    'bolt',
    'shield',
    'verified',
    'directions',
    'menu',
    'more',
];

/**
 * Render an icon
 *
 * @param string $name   Icon name
 * @param string $color  CSS class or hex color (e.g., 'text-primary' or '#b4b4b4')
 * @param int    $size   Size in pixels (default: 16)
 * @param string $class  Additional CSS classes
 * @return string HTML
 */
function icon($name, $color = '', $size = 16, $class = '') {
    global $FA_ICONS, $SVG_ICONS;

    // Determine if color is a hex value or CSS class
    $isHex = strpos($color, '#') === 0;
    $style = $isHex ? ' style="color:' . htmlspecialchars($color) . '"' : '';
    $colorClass = !$isHex && $color ? ' ' . $color : '';
    $extraClass = $class ? ' ' . $class : '';

    // Check FontAwesome first
    if (isset($GLOBALS['FA_ICONS'][$name])) {
        return '<i class="' . $GLOBALS['FA_ICONS'][$name] . $colorClass . $extraClass . '"' . $style . '></i>';
    }

    // Check custom SVG sprite
    if (in_array($name, $GLOBALS['SVG_ICONS'])) {
        $fill = $isHex ? htmlspecialchars($color) : 'currentColor';
        return '<svg class="icon icon-' . $name . $colorClass . $extraClass . '" ' .
               'width="' . intval($size) . '" height="' . intval($size) . '" fill="' . $fill . '"' . $style . '>' .
               '<use href="' . ICON_SPRITE_PATH . '#icon-' . $name . '"></use>' .
               '</svg>';
    }

    // Fallback
    return '<i class="fa-solid fa-question' . $colorClass . '"' . $style . ' title="Unknown: ' . htmlspecialchars($name) . '"></i>';
}

/**
 * Render an icon as inline SVG (for emails or when sprite doesn't work)
 *
 * @param string $name   Icon name from SVG_ICONS
 * @param string $color  Fill color (default: currentColor)
 * @param int    $size   Size in pixels
 * @return string Inline SVG HTML
 */
function icon_inline($name, $color = 'currentColor', $size = 16) {
    $sprites = [
        'location-pin' => '<path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079Z"/>',
        'building' => '<path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1Z"/>',
        'ticket' => '<path d="M2.11111 0C0.946701 0 0 0.946701 0 2.11111V4.22222C0 4.5125 0.244097 4.7401 0.517882 4.83576C1.13802 5.05017 1.58333 5.64062 1.58333 6.33333C1.58333 7.02604 1.13802 7.61649 0.517882 7.8309C0.244097 7.92656 0 8.15417 0 8.44444V10.5556C0 11.72 0.946701 12.6667 2.11111 12.6667H16.8889C18.0533 12.6667 19 11.72 19 10.5556V8.44444C19 8.15417 18.7559 7.92656 18.4821 7.8309C17.862 7.61649 17.4167 7.02604 17.4167 6.33333C17.4167 5.64062 17.862 5.05017 18.4821 4.83576C18.7559 4.7401 19 4.5125 19 4.22222V2.11111C19 0.946701 18.0533 0 16.8889 0H2.11111Z"/>',
        'skills' => '<path d="M6.77617 0.333008C6.65371 0.126758 6.42812 0 6.1875 0C5.94688 0 5.72129 0.126758 5.59883 0.333008L3.53633 3.77051C3.40957 3.9832 3.40527 4.24746 3.52773 4.4623C3.6502 4.67715 3.87793 4.81035 4.125 4.81035H8.25C8.49707 4.81035 8.72695 4.67715 8.84727 4.4623C8.96758 4.24746 8.96543 3.9832 8.83867 3.77051L6.77617 0.333008ZM5.5 5.5C2.46289 5.5 0 7.68652 0 10.3906C0 10.7275 0.280273 11 0.625 11H10.375C10.7197 11 11 10.7275 11 10.3906C11 7.68652 8.53711 5.5 5.5 5.5Z"/>',
        'smiley' => '<path d="M9 0C4.02943 0 0 4.02943 0 9C0 13.9706 4.02943 18 9 18C13.9706 18 18 13.9706 18 9C18 4.02943 13.9706 0 9 0ZM5.4 7.2C5.4 6.53726 5.93726 6 6.6 6C7.26274 6 7.8 6.53726 7.8 7.2C7.8 7.86274 7.26274 8.4 6.6 8.4C5.93726 8.4 5.4 7.86274 5.4 7.2ZM12.6 12.6C11.5941 13.3941 10.3412 13.8 9 13.8C7.65882 13.8 6.40588 13.3941 5.4 12.6C5.04 12.3176 4.97647 11.7882 5.25882 11.4282C5.54118 11.0682 6.07059 11.0047 6.43059 11.2871C7.12941 11.8306 8.02353 12.15 9 12.15C9.97647 12.15 10.8706 11.8306 11.5694 11.2871C11.9294 11.0047 12.4588 11.0682 12.7412 11.4282C13.0235 11.7882 12.96 12.3176 12.6 12.6ZM11.4 8.4C10.7373 8.4 10.2 7.86274 10.2 7.2C10.2 6.53726 10.7373 6 11.4 6C12.0627 6 12.6 6.53726 12.6 7.2C12.6 7.86274 12.0627 8.4 11.4 8.4Z"/>',
        'add-user' => '<path d="M6.3 5.88C7.78985 5.88 9 4.56875 9 2.94C9 1.31125 7.78985 0 6.3 0C4.81015 0 3.6 1.31125 3.6 2.94C3.6 4.56875 4.81015 5.88 6.3 5.88ZM8.82 6.72H8.46825C7.8183 7.04063 7.0812 7.245 6.3 7.245C5.5188 7.245 4.78485 7.04063 4.13175 6.72H3.78C1.6929 6.72 0 8.51719 0 10.7344V11.5781C0 12.3637 0.604125 13 1.35 13H11.25C11.9959 13 12.6 12.3637 12.6 11.5781V10.7344C12.6 8.51719 10.9071 6.72 8.82 6.72ZM13.5 5.6875V4.0625C13.5 3.77656 13.2984 3.5625 13.0275 3.5625H12.2725C12.0016 3.5625 11.8 3.77656 11.8 4.0625V5.6875H10.2725C10.0016 5.6875 9.8 5.90156 9.8 6.1875V6.9375C9.8 7.22344 10.0016 7.4375 10.2725 7.4375H11.8V9.0625C11.8 9.34844 12.0016 9.5625 12.2725 9.5625H13.0275C13.2984 9.5625 13.5 9.34844 13.5 9.0625V7.4375H15.1275C15.3984 7.4375 15.6 7.22344 15.6 6.9375V6.1875C15.6 5.90156 15.3984 5.6875 15.1275 5.6875H13.5Z"/>',
    ];

    $viewBoxes = [
        'location-pin' => '0 0 10 13',
        'building' => '0 0 8 11',
        'ticket' => '0 0 19 13',
        'skills' => '0 0 11 11',
        'smiley' => '0 0 18 18',
        'add-user' => '0 0 18 15',
    ];

    if (isset($sprites[$name])) {
        return '<svg width="' . intval($size) . '" height="' . intval($size) . '" viewBox="' . $viewBoxes[$name] . '" fill="' . htmlspecialchars($color) . '">' . $sprites[$name] . '</svg>';
    }

    return '';
}

/**
 * Get just the FontAwesome class for an icon name
 *
 * @param string $name Icon name
 * @return string FA class or empty
 */
function icon_class($name) {
    return isset($GLOBALS['FA_ICONS'][$name]) ? $GLOBALS['FA_ICONS'][$name] : '';
}

/**
 * Check if an icon exists
 *
 * @param string $name Icon name
 * @return bool
 */
function icon_exists($name) {
    return isset($GLOBALS['FA_ICONS'][$name]) || in_array($name, $GLOBALS['SVG_ICONS']);
}

/**
 * Render an icon as an <img> tag (for maximum caching)
 * Note: Cannot be styled with CSS colors - use icon() for dynamic colors
 *
 * @param string $name   Icon name (must exist as separate SVG file)
 * @param int    $size   Size in pixels
 * @param string $class  Additional CSS classes
 * @param string $alt    Alt text
 * @return string HTML img tag
 */
function icon_img($name, $size = 16, $class = '', $alt = '') {
    $path = '/raj/assets/icons/svg/' . $name . '.svg';
    $altText = $alt ?: $name;
    $extraClass = $class ? ' class="' . htmlspecialchars($class) . '"' : '';

    return '<img src="' . $path . '" width="' . intval($size) . '" height="' . intval($size) . '"' .
           $extraClass . ' alt="' . htmlspecialchars($altText) . '" loading="lazy">';
}
?>
