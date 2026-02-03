<?php
//$club_header_user_info_obj = taoh_user_all_info();
//$club_header_ptoken = $club_header_user_info_obj ? ($club_header_user_info_obj->ptoken ?? '') : '';

?>
<div class="mobile-app py-4 d-flex flex-wrap align-items-center justify-content-center" style="gap:6px;">
    <!-- original tabs -->
    <ul class="nav nav-tabs justify-content-center border-0 d-none d-lg-flex" id="clubTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?php if (isset($pagename) && $pagename === 'main') {
                echo 'active';
            } ?>" id="main-tab" href="<?= TAOH_SITE_URL_ROOT . '/club/main' ?>">Main</a>
        </li>
        <?php if(TAOH_ANNOUNCEMENT_ENABLE) { ?>
            <li class="nav-item">
                <a class="nav-link <?php if (isset($pagename) && $pagename === 'announcements') {
                    echo 'active';
                } ?>" id="announcements-tab" href="<?= TAOH_SITE_URL_ROOT . '/club/announcements' ?>">Announcements</a>
            </li>
        <?php } ?>
        <?php if(TAOH_NEWS_ENABLE) { ?>
            <li class="nav-item">
                <a class="nav-link <?php if (isset($pagename) && $pagename === 'news_feed') {
                    echo 'active';
                } ?>" id="news-feed-tab" href="<?= TAOH_SITE_URL_ROOT . '/club/news_feed' ?>">News Feed</a>
            </li>
        <?php } ?>
        <li class="nav-item">
            <a class="nav-link <?php if (isset($pagename) && $pagename === 'directory') {
                echo 'active';
            } ?>" id="directory-tab" href="<?= TAOH_SITE_URL_ROOT . '/directory/club' ?>">Directory</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if (isset($pagename) && $pagename === 'groups') {
                echo 'active';
            } ?>" id="rooms-tab" href="<?= TAOH_SITE_URL_ROOT . '/club/groups' ?>">Groups</a>
        </li>

        <?php
//        if (!empty($roomslug)) {
//            $networking_title = $room_info['room']['title'] ?? 'Networking';
//            $networking_url = TAOH_SITE_URL_ROOT . '/club/room/' . taoh_slugify($networking_title) . '-' . $roomslug;
            ?>
            <li class="nav-item">
                <a class="nav-link <?php if (isset($pagename) && $pagename === 'networking') {
                    echo 'active';
                } ?>" id="networking-tab" href="<?= TAOH_SITE_URL_ROOT . '/club/networking'; ?>">Networking</a>
            </li>
            <?php
//        }
        ?>

        <?php /*
        if ($club_header_ptoken) {
            ?>
            <li class="nav-item">
                <a class="nav-link <?php if (isset($pagename) && $pagename === 'profile') {
                    echo 'active';
                } ?>" id="profile-tab" href="<?= TAOH_SITE_URL_ROOT . '/club/profile/' . $club_header_ptoken ?>">Profile</a>
            </li>
            <?php
        }
        
        if ( taoh_user_is_logged_in() && isset( $_SESSION[ TAOH_ROOT_PATH_HASH ][ 'USER_INFO' ]->type ) && $_SESSION[ TAOH_ROOT_PATH_HASH ][ 'USER_INFO' ]->type == 'recruiter' ){
            ?>
            <li class="nav-item">
                <a class="nav-link d-flex flex-column text-center" aria-current="page" href="<?php echo TAOH_SITE_URL_ROOT.'/jobs/dash'; ?>">
                <span style="color: #9d0f54;font-weight:bold;">Employers</span></a>
            </li>
            <?php
        }*/
        ?>

    </ul>

    <!-- small screen tabs -->
    <?php
        if ($pagename === 'main') {
            $active_label = 'Main';
        } elseif ($pagename === 'announcements') {
            $active_label = 'Announcements';
        } elseif ($pagename === 'news_feed') {
            $active_label = 'News Feed';
        } elseif ($pagename === 'directory') {
            $active_label = 'Directory';
        } elseif ($pagename === 'groups') {
            $active_label = 'Groups';
        } elseif ($pagename === 'networking') {
            $active_label = 'Networking';
        } else {
            $active_label = '';
        }
    ?>

    <div class="d-block d-lg-none text-center flex-grow-1">
        <div class="dropdown mx-auto club-DD">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="mobileTabDropdown" data-toggle="dropdown" aria-expanded="false">
                <?php echo $active_label ?> <i class="fas fa-ellipsis-v ms-2"></i>
            </button>
            <ul class="dropdown-menu dropdown-mobile-full" aria-labelledby="mobileTabDropdown">
                <li><a class="dropdown-item" href="<?= TAOH_SITE_URL_ROOT . '/club/main' ?>">Main</a></li>
                <?php if(TAOH_ANNOUNCEMENT_ENABLE) { ?>
                    <li><a class="dropdown-item" href="<?= TAOH_SITE_URL_ROOT . '/club/announcements' ?>">Announcements</a></li>
                <?php } ?>
                <?php if(TAOH_NEWS_ENABLE) { ?>
                    <li><a class="dropdown-item" href="<?= TAOH_SITE_URL_ROOT . '/club/news_feed' ?>">News Feed</a></li>
                <?php } ?>
                <li><a class="dropdown-item" href="<?= TAOH_SITE_URL_ROOT . '/directory/club' ?>">Directory</a></li>
                <li><a class="dropdown-item" href="<?= TAOH_SITE_URL_ROOT . '/club/groups' ?>">Groups</a></li>
                <li><a class="dropdown-item" href="<?= TAOH_SITE_URL_ROOT . '/club/networking' ?>">Networking</a></li>
            </ul>
        </div>
    </div>

    <?php if (! taoh_user_is_logged_in()){ ?>
        <div class="nav-right-button">
            <a onclick="localStorage.removeItem('isCodeSent')" href="javascript:void(0);"
            class="btn theme-btn theme-btn login-button px-2 px-sm-3" aria-pressed="true" data-toggle="modal" data-target="#config-modal" style="width: fit-content"><i class="la la-sign-in mr-1"></i> Login / Sign Up</a>
            <!-- And Grow Together! -->
        </div>
    <?php } ?>
</div>