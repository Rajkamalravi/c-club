
<?php if($page_sel != "jobs") { ?>
  <li class="nav-item">
    <a class="nav-link <?php if($page_sel == "jobs") echo 'active';?> " id="jobs_tab"
    href="<?php echo TAOH_SITE_URL_ROOT."/jobs/"; ?>" role="tab" aria-controls="jobs"
      aria-selected="true">All Jobs</a>
  </li>
  <?php if(defined('TAOH_SCOUT_ENABLE') && TAOH_SCOUT_ENABLE) { ?>
    <li class="nav-item">
      <a class="nav-link <?php if($page_sel == "scout") echo 'active';?> " id="scout_tab"
      style='color: #2479D8; font-weight: bold;'
      href="<?php echo TAOH_SITE_URL_ROOT."/jobs/scout"; ?>" role="tab" aria-controls="scout"
      aria-selected="true">Scout</a>
    </li>
  <?php } ?>
<?php } else { ?>
  <?php if(defined('TAOH_SCOUT_ENABLE') && TAOH_SCOUT_ENABLE) { ?>
    <?php if(taoh_user_is_logged_in()) { ?>
              <?php if(isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->status_scout_employer) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->status_scout_employer >=1){ ?>
          <li class="nav-item">
            <a class="nav-link " id="emp_tab" href="<?php echo TAOH_SITE_URL_ROOT."/jobs/scouts-signup-employer"; ?>" role="tab" aria-controls="emp" aria-selected="true">#EmployerScouts</a>
          </li>
              <?php } ?>

            <?php if(isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->status_scout_professional) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->status_scout_professional == 2){ ?>
            <li class="nav-item">
              <a class="nav-link " id="prof_tab" href="<?php echo TAOH_SITE_URL_ROOT."/jobs/scouts-signup-professional"; ?>" role="tab" aria-controls="prof" aria-selected="true">ScoutMembers</a>
              </li>
          <?php } ?>

        <?php if(isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->status_scout) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->status_scout == 2){ ?>
            <li class="nav-item">
              <a class="nav-link " id="scout_tab" href="<?php echo TAOH_SITE_URL_ROOT."/jobs/scouts-signup"; ?>" role="tab" aria-controls="scout" aria-selected="true">Scouts</a>
              </li>
        <?php } ?>

    <?php } ?>
    <li class="nav-item">
      <a class="nav-link <?php if($page_sel == "scout") echo 'active';?>" id="scoutt_tab" href="<?php echo TAOH_SITE_URL_ROOT."/jobs/scout/"; ?>" role="tab" aria-controls="scout" aria-selected="true" style='color: #2479D8; font-weight: bold;'>About Scout</a>
    </li>
  <?php } ?>
<?php } ?>
