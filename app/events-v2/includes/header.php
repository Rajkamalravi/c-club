<?php
/**
 * Events V2 - Page Header
 *
 * Navigation and branding
 */

$user_info = taoh_user_is_logged_in() ? taoh_user_all_info() : null;
?>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg ev2-navbar sticky-top">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="<?php echo TAOH_SITE_URL_ROOT; ?>">
            <?php if (defined('TAOH_SITE_LOGO') && TAOH_SITE_LOGO): ?>
                <img src="<?php echo TAOH_SITE_LOGO; ?>" alt="<?php echo TAOH_SITE_NAME_SLUG; ?>" height="40">
            <?php else: ?>
                <span class="fw-bold"><?php echo TAOH_SITE_NAME_SLUG; ?></span>
            <?php endif; ?>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#ev2Nav" aria-controls="ev2Nav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-4"></i>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="ev2Nav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo TAOH_EVENTS_V2_URL; ?>">
                        <i class="bi bi-calendar-event me-1"></i> Events
                    </a>
                </li>
                <?php if (taoh_user_is_logged_in()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo TAOH_EVENTS_V2_URL; ?>?filter=my-events">
                        <i class="bi bi-bookmark me-1"></i> My Events
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <!-- Search Form -->
            <form class="d-flex me-3 ev2-search-form" action="<?php echo TAOH_EVENTS_V2_URL; ?>" method="get">
                <div class="input-group">
                    <input type="search" class="form-control" name="search" placeholder="Search events..." aria-label="Search events" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- User Actions -->
            <div class="d-flex align-items-center gap-2">
                <?php if (taoh_user_is_logged_in()): ?>
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (!empty($user_info->avatar)): ?>
                                <img src="<?php echo $user_info->avatar; ?>" alt="Profile" class="rounded-circle" width="32" height="32">
                            <?php else: ?>
                                <div class="ev2-avatar-placeholder">
                                    <i class="bi bi-person"></i>
                                </div>
                            <?php endif; ?>
                            <span class="d-none d-lg-inline"><?php echo htmlspecialchars($user_info->first_name ?? 'User'); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo TAOH_SITE_URL_ROOT; ?>/profile"><i class="bi bi-person me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo TAOH_EVENTS_V2_URL; ?>?filter=my-events"><i class="bi bi-calendar-check me-2"></i> My RSVPs</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo TAOH_SITE_URL_ROOT; ?>/logout"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo TAOH_LOGIN_URL; ?>" class="btn btn-outline-primary">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                    <a href="<?php echo TAOH_SITE_URL_ROOT; ?>/register" class="btn btn-primary">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
