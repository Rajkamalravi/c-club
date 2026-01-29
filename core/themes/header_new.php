<?php include_once('head.php');

$app_temp = @taoh_parse_url(0) ? taoh_parse_url(0):TAOH_PLUGIN_PATH_NAME;
$current_app = TAOH_SITE_CURRENT_APP_SLUG;


//echo $current_app;
$about_url = TAOH_SITE_URL_ROOT."/about";
if ( $current_app != TAOH_PLUGIN_PATH_NAME ) $about_url = TAOH_SITE_URL_ROOT."/".$current_app."/about";

?>
<header class="header-area bg-white border-bottom border-bottom-gray header1">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-4">
        <div class="logo-box">
          <a href="<?php echo $taoh_home_url . "/../"; ?>" class="logo">
            <img src="<?php echo (defined('TAOH_PAGE_LOGO')) ? TAOH_PAGE_LOGO : TAOH_SITE_LOGO; ?>" alt="logo" style="max-height: 45px; width: auto;">
          </a>
          <div class="user-action">
              <div class="off-canvas-menu-toggle icon-element icon-element-xs shadow-sm" data-toggle="tooltip" data-placement="top" title="Main menu">
                  <i class="la la-bars"></i>
              </div>
          </div>
        </div>
      </div>
      <div class="col-lg-8 float-right">
        <div class="menu-wrapper">
            <nav class="menu-bar ml-auto pr-2">
              <ul>
                <li><a href="<?php echo TAOH_SITE_URL_ROOT; ?>">Hires</a></li>
                  <li class="dropdown">
                      <a class="nav-link dropdown-toggle dropdown--toggle" href="#" id="appDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Growth Tools
                      </a>
                      <div class="dropdown-menu dropdown--menu mt-3 keep-open" aria-labelledby="appDropdown">
                          <div class="dropdown-item-list">
                            <?php foreach (taoh_available_apps() as $app) { ?>
                              <a href="<?php echo TAOH_SITE_URL_ROOT; ?>/<?php echo $app; ?>"><?php echo $app; ?></a>
                              <div class="dropdown-divider border-top-gray mb-0"></div>
                            <?php	} ?>
                            <a href="<?php echo TAOH_SITE_URL_ROOT."/learning/jusask"; ?>">#JusASK, The Career Coach</a>
                            <div class="dropdown-divider border-top-gray mb-0"></div>
                            <a href="<?php echo TAOH_TIPS_URL; ?>">Tips & Tricks</a>
                            <div class="dropdown-divider border-top-gray mb-0"></div>
                          </div>
                      </div>
                  </li>
                  <li><a class="nav-link dropdown-toggle dropdown--toggle" href="#" id="readsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">About</a>
                  <div class="dropdown-menu dropdown--menu mt-3 keep-open" aria-labelledby="readsDropdown">
                      <div class="dropdown-item-list">
                        <a href="<?php echo $about_url; ?>">About</a>
                        <div class="dropdown-divider border-top-gray mb-0"></div>
                        <a href="<?php echo 'https://tao.ai'; ?>">TAO.ai</a>
                      </div>
                  </div>
                </li>
                <li><a class="nav-link dropdown-toggle dropdown--toggle" href="#" id="readsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Resources</a>
                  <div class="dropdown-menu dropdown--menu mt-3 keep-open" aria-labelledby="readsDropdown">
                      <div class="dropdown-item-list">
                        <a href="<?php echo TAOH_READS_URL; ?>">Blogs</a>
                        <div class="dropdown-divider border-top-gray mb-0"></div>
                        <a href="<?php echo TAOH_FLASHCARD_URL; ?>">Flashcards</a>
                        <div class="dropdown-divider border-top-gray mb-0"></div>
                        <a href="<?php echo TAOH_OBVIOUS_URL; ?>">Obvious Baba</a>
                        <div class="dropdown-divider border-top-gray mb-0"></div>
                      </div>
                  </div>
                </li>
              </ul>
            </nav><!-- end main-menu -->

            <?php  if ( !taoh_user_is_logged_in() ) { ?>
              <div class="nav-right-button">
                  <a href="<?php echo TAOH_LOGIN_URL; ?>" class="btn theme-btn theme-btn-outline mr-2"><i class="la la-sign-in mr-1"></i> Login / Sign Up</a>
              </div><!-- end nav-right-button -->
              <?php } else { ?>
                <div class="nav-right-button">
                    <ul class="user-action-wrap d-flex align-items-center">
                        <li class="dropdown">
                            <span id="dotNotifier"></span>
                            <a class="nav-link dropdown-toggle dropdown--toggle" href="#" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-bell"></i>
                            </a>
                            <div class="dropdown-menu dropdown--menu dropdown-menu-right mt-3 keep-open" aria-labelledby="notificationDropdown">
                                <h6 class="dropdown-header"><i class="la la-bell pr-1 fs-16"></i> <span id="unreadCount"></span> Unread Notifications <span id="notificationMenuLoader"></span> <button onclick="taoh_notification_read_all()" style="display: none" id="readAllBtn" class="badge badge-primary float-right">Read All</button></h6>
                                <div class="dropdown-divider border-top-gray mb-0"></div>
                                <div id="notificationList" class="dropdown-item-list"></div>
                                <!--<a class="dropdown-item pb-1 border-bottom-0 text-center btn-text fw-regular" href="<?php //echo taoh_notifications_url(); ?>">View All Notifications <i class="la la-arrow-right icon ml-1"></i></a>-->
                            </div>
                        </li>
                        <li class="dropdown user-dropdown">
                            <a class="nav-link dropdown-toggle dropdown--toggle pl-2" href="#" id="userMenuDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="media media-card media--card shadow-none mb-0 rounded-0 align-items-center bg-transparent">
                                    <div class="media-body p-0 border-left-0">
                                        <h5 class="fs-14">
                                          <?php echo taoh_get_profile_image(); ?>
                                          <?php echo taoh_user_full_name(); ?></h5>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown--menu dropdown-menu-right mt-3 keep-open" aria-labelledby="userMenuDropdown">
                                <h6 class="dropdown-header"><?php echo taoh_user_full_name(); ?></h6>
                                <div class="dropdown-divider border-top-gray mb-0"></div>
                                <div class="dropdown-item-list">
                                <?php
                                //echo "<a class=\"dropdown-item\" href=\"". TAOH_NOTIFICATION_URL."\"><i class=\"la la-gear mr-2\"></i>Notifications</a>";
                                ?>
                                  <a class="dropdown-item" href="<?php echo TAOH_REFERRAL_URL; ?>"><i class="la la-gear mr-2"></i>Referral</a>
                                    <a class="dropdown-item" href="<?php echo TAOH_SETTINGS_URL; ?>"><i class="la la-gear mr-2"></i>Settings</a>

                                    <a onclick="localStorage.removeItem('isCodeSent')" class="dropdown-item" href="<?php echo TAOH_LOGOUT_URL;?>"><i class="la la-power-off mr-2"></i>Log out</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
              <?php } ?>
        </div><!-- end menu-wrapper -->
      </div><!-- end col-lg-6 -->
    </div>
  </div>
  <div class="off-canvas-menu custom-scrollbar-styled">
        <div class="off-canvas-menu-close icon-element icon-element-sm shadow-sm" data-toggle="tooltip" data-placement="left" title="Close menu">
            <i class="la la-times"></i>
        </div><!-- end off-canvas-menu-close -->
        <ul class="generic-list-item off-canvas-menu-list pt-90px">
            <?php  if ( taoh_user_is_logged_in() ) { ?>
              <li class="p-3"> <?php echo taoh_get_profile_image(); ?> <?php echo taoh_user_full_name(); ?></li>
            <?php } ?>
            <li> <a href="<?php echo TAOH_SITE_URL_ROOT; ?>">Hires</a></li>
            <li>
                <a href="#">Growth Tools</a>
                <ul class="sub-menu">
                    <?php foreach (taoh_available_apps() as $app) { ?>
                      <li><a href="<?php echo TAOH_SITE_URL_ROOT; ?>/<?php echo $app; ?>"><?php echo $app; ?></a></li>
                    <?php	} ?>
                </ul>
            </li>
            <li>
                <a href="<?php echo $about_url; ?>">About</a>
                <ul class="sub-menu">
                  <li><a href="<?php echo TAOH_READS_URL; ?>">Reads</a></li>
                  <li><a href="<?php echo TAOH_FLASHCARD_URL; ?>">Flashcard</a></li>
                  <li><a href="<?php echo TAOH_OBVIOUS_URL; ?>">Obvious Baba</a></li>
                  <li><a href="<?php echo TAOH_TIPS_URL; ?>">Tips & Tricks</a></li>
                </ul>
            </li>
            <li>
              <a href="#">Account</a>
              <ul class="sub-menu">
                <?php  if ( !taoh_user_is_logged_in() ) { ?>
                  <li><a href="<?php echo TAOH_LOGIN_URL; ?>">Login / Signup</a></li>
                <?php } else { ?>

                  <li><a href="<?php echo TAOH_REFERRAL_URL; ?>">Referral</a></li>
                  <li><a href="<?php echo TAOH_SETTINGS_URL; ?>">Settings</a></li>
                  <li><a onclick="localStorage.removeItem('isCodeSent')"  href="<?php echo TAOH_LOGOUT_URL; ?>">Log out</a></li>
                <?php } ?>
              </ul>
            </li>
        </ul>
    </div><!-- end off-canvas-menu -->
    <div class="body-overlay"></div>
</header>
