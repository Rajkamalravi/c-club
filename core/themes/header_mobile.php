<?php 
//include_once('head.php'); 

$current_app = TAOH_SITE_CURRENT_APP_SLUG;

$taoh_home_url = ( defined( 'TAOH_PAGE_URL' ) && TAOH_PAGE_URL ) ? TAOH_PAGE_URL:TAOH_SITE_URL_ROOT;


$app_name = @taoh_parse_url(0) ? taoh_parse_url(0) : '';

$conttokenvar = @taoh_parse_url(2) ? taoh_parse_url(2) : '';
$tokenname = '';
$detail_name = '';
if($conttokenvar != ''){
    @$conttoken = array_pop( explode( '-', $conttokenvar) );
   
    if($app_name == 'events'){
      $detail_name = '/next/'.$conttoken;
    }else{
      $detail_name = '/d/'.$conttoken;
    }
}


//echo $current_app;
$about_url = $taoh_home_url."/about";
if ( $current_app != TAOH_PLUGIN_PATH_NAME ) $about_url = $taoh_home_url."/".$current_app."/about";

include_once('head.php'); 

//echo $current_app;
$about_url = $taoh_home_url."/about";
if ( $current_app != TAOH_PLUGIN_PATH_NAME ) $about_url = $taoh_home_url."/".$current_app."/about";

$data = taoh_user_all_info();
$ptoken = ( isset( $data->ptoken ) && $data->ptoken ) ? $data->ptoken:'';


$my_status = '';
/*$taoh_vals = array(
  "ops" => 'lrange',
  'key'=> $ptoken.'_live_status',
 // 'debug'=>1
);
$stat = taoh_remote_cache( $taoh_vals );
//print_r($stat);
$out = json_decode($stat, true);
if($out['success']){
  $my_status = $out['output'][0];
}*/
//echo"=========".$my_status;
?>
<!-- Navbar -->
<!-- Navbar -->
<div class="wrapper">
<header class="page-header header-area bg-white border-bottom border-bottom-gray header1 mobile-app-header" id="myHeader">
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
      <div class="">
        <div class="logo-box" style="text-align: left; display: flex; align-items: center;">
          <a href="<?php echo $taoh_home_url . "/../"; ?>" class="logo">
            <img src="<?php echo (defined('TAOH_PAGE_LOGO')) ? TAOH_PAGE_LOGO : TAOH_SITE_LOGO; ?>" alt="logo" style="max-height: 45px; width: auto;">
          </a>
          <?php
          if (defined('TAOH_SITE_LOGO_2') && TAOH_SITE_LOGO_2) {
            $logo_2_arr = json_decode(TAOH_SITE_LOGO_2);
            echo '&nbsp;&nbsp;<a href="' . $logo_2_arr[1] . '" target="_blank"><img src="' . $logo_2_arr[0] . '" alt="logo" style="max-height: 40px; width: auto; margin-left: 10px;"></a>';
          }
          ?>
        </div>

        <div class="mobile-user-menu" style="display: none">
          <ul>
              <?php
                  if ( taoh_user_is_logged_in() ) {
                ?>
                  <li class="dropdown stay_open user-dropdown bor-right">
                      <a class="nav-link dropdown-toggle dropdown--toggle pl-2 text-center" href="#" id="userMenuDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <div class="media media-card media--card shadow-none mb-0 rounded-0 align-items-center bg-transparent">
                                      <div class="media-body p-0 border-left-0">
                                              <?php echo taoh_get_profile_image(); ?>
                                      </div>
                              </div>
                      </a>
                      <div class="dropdown-menu dropdown--menu dropdown-menu-right mt-3 keep-open" id="userMenuDropdownarea" aria-labelledby="userMenuDropdown">
                              <h6 class="dropdown-header">Hi, <?php echo taoh_user_full_name(); ?></h6>
                              <div class="dropdown-divider border-top-gray mb-0"></div>
                              <div class="dropdown-item-list">
                              <a class="dropdown-item" href="<?php echo $taoh_home_url."/profile/".$ptoken; ?>"><i class="la la-user-tie mr-2"></i>Profile</a>
                                <a class="dropdown-item" href="<?php echo TAOH_SETTINGS_URL; ?>"><i class="la la-gear mr-2"></i>Settings</a>
                                <?php /* Messages & Referral - hidden until fixed
                                <a class="dropdown-item" href="<?php echo $taoh_home_url.'/message'; ?>"><i class="la la-sms mr-2"></i>Messages</a>
                                <a class="dropdown-item" href="<?php echo $taoh_home_url."/referral"; ?>"><i class="la la-user-plus mr-2"></i>Referral</a>
                                */ ?>
                                <!--<a class="dropdown-item status-list" href="#">
                                <p style="position: relative"><input type="text" maxlength="140" value="<?php echo $my_status; ?>" name="my_status" id="my_status" placeholder="Update your status">
                                    
                                    <button class="btn btn-success" id="status_save" onclick="updateStatus(1)"><i class="la la-check mr-2"></i></button>
                                    
                                    <button class="btn btn-danger" id="status_remove" style="display: none" onclick="updateStatus(0)"><i class="la la-close mr-2"></i></button>
                                </p>
                                </a>-->
                                <a onclick="localStorage.removeItem('isCodeSent')" class="dropdown-item" href="<?php echo  $taoh_home_url."/logout";?>"><i class="la la-power-off mr-2"></i>Log out</a>
                              </div>
                      </div>
                  </li>
                <?php
                  } else if ( defined(  'TAOH_PAGE_AVATAR') && defined(  'TAOH_PAGE_AVATAR') && defined( 'TAOH_PAGE_CHAT_NAME' ) && defined( 'TAOH_PAGE_PTOKEN' ) ){
                    ?>
                    <li class="dropdown user-dropdown bor-right">
                        <a class="nav-link dropdown-toggle dropdown--toggle pl-2" href="#" id="userMenuDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="media media-card media--card shadow-none mb-0 rounded-0 align-items-center bg-transparent">
                                        <div class="media-body p-0 border-left-0">
                                                <?php echo taoh_get_profile_image(); ?>
                                        </div>
                                </div>
                        </a>
                        <div class="dropdown-menu dropdown--menu dropdown-menu-right mt-3 keep-open" aria-labelledby="userMenuDropdown">
                            <div class="dropdown-item-list">
                                    <a onclick="localStorage.removeItem('isCodeSent')" class="dropdown-item" href="<?php echo  $taoh_home_url."/profile/".TAOH_PAGE_PTOKEN;?>"><i class="la la-power-off mr-2"></i><?php echo TAOH_PAGE_CHAT_NAME; ?></a>
                            </div>
                        </div>
                    </li>
                  <?php
                    } else {
                  ?>
                  <li class="dropdown user-dropdown bor-right">
                      <a class="nav-link dropdown-toggle dropdown--toggle pl-2" href="#" id="userMenuDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <div class="media media-card media--card shadow-none mb-0 rounded-0 align-items-center bg-transparent">
                                      <div class="media-body p-0 border-left-0">
                                              <?php echo taoh_get_profile_image(); ?>
                                      </div>
                              </div>
                      </a>
                      <div class="dropdown-menu dropdown--menu dropdown-menu-right mt-3 keep-open" aria-labelledby="userMenuDropdown">
                          <div class="dropdown-item-list">
                                  <a onclick="localStorage.removeItem('isCodeSent'); openLoginPopup(); return false;" class="dropdown-item" href="<?php echo  $taoh_home_url."/login.php"; ?>"><i class="la la-power-off mr-2"></i>Log In</a>
                          </div>
                      </div>
                  </li>
                  <!-- openLoginPopup is defined globally in footer.php -->
                <?php
                  }
                ?>
            </ul>
          </div>
      </div>
      <div class="user-action">
        <div class="off-canvas-menu-toggle icon-element icon-element-xs shadow-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Main menu">
            <i class="la la-bars"></i>
        </div>
    </div>
      <?php 
      $class = '';
      if(stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/apps' )){
        $class = 'active';
      }
      if((taoh_parse_url(0) != 'createacc') && (taoh_parse_url(0) != 'createsettings')){ ?>
        <ul class="navbar-nav ms-auto mb-lg-0">
          <!--<li class="nav-item">
            <a class="nav-link d-flex flex-column text-center" aria-current="page" href="<?php echo $taoh_home_url; ?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor" class="mercado-match" width="26" height="26" focusable="false">
              <path d="M23 9v2h-2v7a3 3 0 01-3 3h-4v-6h-4v6H6a3 3 0 01-3-3v-7H1V9l11-7z"></path>
              </svg><span class="small">Home</span></a>
          </li>-->
          <li class="nav-item <?php echo (( 
           stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/stlo') || 
           stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/') || 
            stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/club' ) ||
            stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/directory' ) ||
            stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/networking' ) ||
            stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/room' ) 
            
            ) ? "active":"") ?>">
            <a class="nav-link d-flex flex-column text-center" aria-current="page" href="<?php echo $taoh_home_url."/club"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="27.712" height="27.555" viewBox="0 0 27.712 27.555">
              <g id="Layer_2" data-name="Layer 2" transform="translate(-0.032 0.003)">
                <g id="Layer_1" data-name="Layer 1" transform="translate(0.457 0.422)">
                  <path id="Path_351" data-name="Path 351" d="M13.3.459a2.6,2.6,0,0,0-1.063.631,2.631,2.631,0,0,0-.376.6,1.5,1.5,0,0,0-.173.872,1.5,1.5,0,0,0,.173.876,2.067,2.067,0,0,0,1,1.041,2.322,2.322,0,0,0,.451.188c.06,0,.071.162.06.9v.906l-.395.038a7.381,7.381,0,0,0-3.668,1.5l-.342.267-2.029-2-2-2,.105-.233A1.92,1.92,0,0,0,5.02,2.68a1.879,1.879,0,0,0-.834-.834,1.958,1.958,0,0,0-1.458,0,1.924,1.924,0,0,0-.834.849,1.958,1.958,0,0,0,0,1.458,1.924,1.924,0,0,0,.834.834,1.92,1.92,0,0,0,1.364.026L4.325,4.9l2,2.007,2,2-.271.346a7.414,7.414,0,0,0-1.5,3.668l-.038.376-.466.019H5.58l-.03-.188A2.8,2.8,0,0,0,4.829,11.9a2.54,2.54,0,0,0-3.641.056,2.544,2.544,0,0,0,.068,3.634,2.533,2.533,0,0,0,3.574,0,2.818,2.818,0,0,0,.718-1.2l.03-.188h.936l.038.376a7.546,7.546,0,0,0,1.417,3.559,8.538,8.538,0,0,0,2.21,1.939,7.659,7.659,0,0,0,2.777.917l.406.038v.9c0,.752,0,.906-.06.906a2.322,2.322,0,0,0-.451.188,2.067,2.067,0,0,0-1,1.041,1.5,1.5,0,0,0-.173.879,1.5,1.5,0,0,0,.2.921,2.074,2.074,0,0,0,1.063,1.079,1.5,1.5,0,0,0,.872.173,1.5,1.5,0,0,0,.876-.173,2.089,2.089,0,0,0,1.056-1.03,1.5,1.5,0,0,0,.2-.921,1.5,1.5,0,0,0-.173-.879,2.067,2.067,0,0,0-1-1.041,2.322,2.322,0,0,0-.451-.188c-.06,0-.071-.162-.06-.9v-.906l.376-.038a7.482,7.482,0,0,0,3.63-1.481l.376-.3.962.924.924.921-.105.233a1.706,1.706,0,0,0,.861,2.2,1.958,1.958,0,0,0,1.458,0,1.729,1.729,0,0,0,.853-2.292,1.905,1.905,0,0,0-.834-.834,1.943,1.943,0,0,0-1.42-.023l-.2.109-.924-.928-.921-.928.271-.338a7.478,7.478,0,0,0,1.5-3.671l.038-.376h.932l.034.188a2.818,2.818,0,0,0,.722,1.229,2.57,2.57,0,1,0,0-3.687,2.8,2.8,0,0,0-.7,1.18l-.034.188h-.466l-.466-.019-.038-.376a7.414,7.414,0,0,0-1.5-3.668L19.3,8.948l.9-.958.921-.928.233.109a1.5,1.5,0,0,0,.661.109,1.2,1.2,0,0,0,.782-.2,1.717,1.717,0,0,0-.068-3.063A1.357,1.357,0,0,0,22,3.886a1.357,1.357,0,0,0-.729.135,1.879,1.879,0,0,0-.834.834,1.95,1.95,0,0,0-.023,1.39l.109.207-.928.921-.928.921-.376-.3a7.448,7.448,0,0,0-3.63-1.481l-.376-.038V5.57c0-.752,0-.9.06-.9A2.322,2.322,0,0,0,14.8,4.48a2.067,2.067,0,0,0,1-1.041,1.5,1.5,0,0,0,.173-.879,1.5,1.5,0,0,0-.2-.921A2.089,2.089,0,0,0,13.927.425,3.232,3.232,0,0,0,13.3.459Zm.906.872A1.432,1.432,0,0,1,15.1,2.56a1.3,1.3,0,1,1-2.589,0,1.172,1.172,0,0,1,.406-.887A1.2,1.2,0,0,1,14.208,1.331ZM3.8,2.639a.849.849,0,1,1-1.184.6.868.868,0,0,1,1.184-.6ZM22.341,4.792A.838.838,0,0,1,22.6,6.175a.8.8,0,0,1-1.191,0,.853.853,0,0,1,.936-1.383ZM14.825,7.385a6.475,6.475,0,0,1,5.122,8.4,5.4,5.4,0,0,1-.725,1.481c-.034,0-.086-.086-.124-.21a7.114,7.114,0,0,0-.706-1.447,6.764,6.764,0,0,0-1.747-1.65c-.259-.158-.5-.293-.534-.3s0-.105.109-.237A3.006,3.006,0,0,0,15.133,8.9a2.912,2.912,0,0,0-2.631,0,3.006,3.006,0,0,0-1.109,4.513.778.778,0,0,1,.147.225l-.346.188a5.8,5.8,0,0,0-2.631,3.149c-.053.165-.124.3-.15.289a5.241,5.241,0,0,1-.725-1.481A6.475,6.475,0,0,1,14.817,7.4Zm-.132,2.274a2.074,2.074,0,0,1,1.045,1.026,1.526,1.526,0,0,1,.2.947,1.661,1.661,0,0,1-.116.8,2.484,2.484,0,0,1-.838.909.462.462,0,0,0-.312.451c0,.218.147.376.47.447a6.445,6.445,0,0,1,1.379.68,4.96,4.96,0,0,1,1.9,2.92l.083.376-.338.312a6.708,6.708,0,0,1-3.074,1.586,8.9,8.9,0,0,1-2.567,0,6.655,6.655,0,0,1-3.074-1.586l-.342-.312.079-.376a4.78,4.78,0,0,1,3.236-3.585c.376-.124.522-.252.522-.474a.462.462,0,0,0-.312-.436,2.484,2.484,0,0,1-.838-.909,1.646,1.646,0,0,1-.116-.793,1.533,1.533,0,0,1,.173-.9,2.082,2.082,0,0,1,.924-1,1.879,1.879,0,0,1,1.15-.259A1.8,1.8,0,0,1,14.693,9.658ZM3.754,12.217a1.879,1.879,0,0,1,.834.823,1.334,1.334,0,0,1,.135.752,1.266,1.266,0,0,1-.184.8,1.556,1.556,0,0,1-.782.752,1.251,1.251,0,0,1-.733.143,1.312,1.312,0,0,1-.725-.132,1.732,1.732,0,0,1,.188-3.2,1.879,1.879,0,0,1,1.266.064Zm21.563,0a1.905,1.905,0,0,1,.838.823,1.375,1.375,0,0,1,.132.752,1.289,1.289,0,0,1-.18.8,1.586,1.586,0,0,1-.782.752,1.266,1.266,0,0,1-.733.143,1.319,1.319,0,0,1-.729-.132,1.732,1.732,0,0,1,.188-3.2A1.879,1.879,0,0,1,25.317,12.217ZM22.341,21.18a.838.838,0,0,1,.256,1.383.8.8,0,0,1-1.191,0,.853.853,0,0,1,.936-1.383Zm-8.132,2.578a1.428,1.428,0,0,1,.894,1.229,1.3,1.3,0,0,1-2.589,0,1.349,1.349,0,0,1,1.695-1.229Z" transform="translate(-0.457 -0.422)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                  <path id="Path_352" data-name="Path 352" d="M62.936,5.541c-.473.477-.556.586-.556.729a.455.455,0,0,0,.443.443c.143,0,.252-.083.725-.556s.567-.582.567-.729A.462.462,0,0,0,63.661,5C63.519,5,63.41,5.068,62.936,5.541Z" transform="translate(-39.11 -3.28)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                  <path id="Path_353" data-name="Path 353" d="M17.076,51.426c-.474.473-.556.582-.556.729a.451.451,0,0,0,.44.44c.143,0,.252-.079.7-.515a3.431,3.431,0,0,0,.545-.658.458.458,0,0,0-.466-.552C17.655,50.869,17.418,51.065,17.076,51.426Z" transform="translate(-10.484 -31.912)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                  <path id="Path_354" data-name="Path 354" d="M7.3,55.546a11.174,11.174,0,0,0-1.037.981l-.928.932-.225-.109a1.917,1.917,0,0,0-1.364.026,1.92,1.92,0,0,0-.838.838,1.947,1.947,0,0,0,0,1.454,1.717,1.717,0,0,0,3.07.083,1.2,1.2,0,0,0,.192-.782,1.541,1.541,0,0,0-.109-.646L5.95,58.09l.958-.955A9.77,9.77,0,0,0,7.9,56.06a.485.485,0,0,0-.229-.492A.376.376,0,0,0,7.3,55.546ZM4.807,58.154a.849.849,0,0,1,.259,1.387.815.815,0,0,1-1.2,0,.853.853,0,0,1,.939-1.387Z" transform="translate(-1.898 -34.806)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                </g>
              </g>
            </svg><span class="small">Club</span></a>
          </li>
          <li class="nav-item <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/events' ) ) ? "active":"") ?>">
            <a class="nav-link d-flex flex-column text-center" aria-current="page" href="<?php echo $taoh_home_url."/events"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="25.27" height="26.22" viewBox="0 0 25.251 26.169">
                <g id="Layer_1" data-name="Layer 1" transform="translate(0.375 0.375)" >
                  <path id="Path_349" data-name="Path 349" d="M5.3,0H7.679A1.072,1.072,0,0,1,8.57,1.087V2.742h7.355V1.114A1.075,1.075,0,0,1,16.82,0H19.2c.054.019.1.046.157.061a1.079,1.079,0,0,1,.853,1.152V2.75h1.947a3.073,3.073,0,0,1,.883.169A2.231,2.231,0,0,1,24.494,5.1V22.926a2.525,2.525,0,0,1-2.039,2.47,2.281,2.281,0,0,1-.384.023H2.436a2.251,2.251,0,0,1-1.99-1.095A2.946,2.946,0,0,1,0,22.661Q0,13.881,0,5.1A2.262,2.262,0,0,1,2.321,2.758H4.3V1.171A1.037,1.037,0,0,1,5.063.108ZM1.23,9.809V22.956A1.218,1.218,0,0,0,2.544,24.2H22a1.079,1.079,0,0,0,1.152-.8,2.266,2.266,0,0,0,.108-.68V9.817Zm18.981-5.83v.469A1.252,1.252,0,0,1,18.84,5.815H17.1a1.129,1.129,0,0,1-1.179-1.206V3.991H8.578v.73A1.114,1.114,0,0,1,7.525,5.807c-.676,0-1.356.019-2.032,0A1.214,1.214,0,0,1,4.3,4.551V3.979h-2.1a1.014,1.014,0,0,0-.968,1.068q0,1.636,0,3.272v.242H23.261a.726.726,0,0,0,0-.111V4.993a1,1,0,0,0-.956-.987C21.629,3.96,20.945,3.979,20.211,3.979Zm-3.107.6h1.874V1.244H17.1Zm-9.771,0V1.248H5.62V4.582Z" transform="translate(0.004 0)"  stroke="currentColor" stroke-width="0.75" fill="currentColor" class="mercado-match" focusable="false"/>
                  <path id="Path_350" data-name="Path 350" d="M18.655,39.537c.134-.741.227-1.536.422-2.266a1.2,1.2,0,0,0-.453-1.425c-.553-.442-1.033-.976-1.536-1.463a.676.676,0,0,1-.242-.73.661.661,0,0,1,.615-.426c.891-.131,1.782-.273,2.689-.384a.438.438,0,0,0,.384-.284c.384-.814.8-1.617,1.191-2.427a.68.68,0,0,1,.615-.461.664.664,0,0,1,.611.461c.407.833.822,1.655,1.229,2.489a.334.334,0,0,0,.3.211c.914.131,1.832.269,2.746.407a.645.645,0,0,1,.6.442.664.664,0,0,1-.246.707c-.657.63-1.31,1.264-1.97,1.886a.384.384,0,0,0-.131.411c.165.876.33,1.751.469,2.635a1.064,1.064,0,0,1-.073.611c-.154.3-.507.353-.872.165-.8-.411-1.6-.822-2.389-1.256a.5.5,0,0,0-.545,0q-1.241.649-2.485,1.279a.618.618,0,0,1-.922-.58Zm3.653-7.877c-.342.68-.657,1.306-.968,1.92a.591.591,0,0,1-.465.361l-.768.108-1.337.192-.038.069a2.361,2.361,0,0,1,.2.142c.43.411.856.826,1.29,1.237a.7.7,0,0,1,.238.645c-.054.315-.111.634-.165.953-.065.384-.123.768-.2,1.183.63-.342,1.206-.645,1.774-.96a.807.807,0,0,1,.845,0c.519.288,1.045.565,1.567.849.061.031.127.054.211.088-.092-.553-.134-1.087-.273-1.594a1.291,1.291,0,0,1,.488-1.5A10.651,10.651,0,0,0,25.761,34.2c-.031.023-.127,0-.219,0-.549-.077-1.095-.173-1.648-.227a.787.787,0,0,1-.691-.507C22.93,32.869,22.627,32.3,22.308,31.66Z" transform="translate(-10.358 -18.274)"  stroke="currentColor" stroke-width="0.75" fill="currentColor" class="mercado-match" focusable="false"/>
                </g>
            </svg><span class="small">Events</span></a>
          </li>
          <li class="nav-item <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/jobs' ) ) ? "active":"") ?>">
            <a class="nav-link d-flex flex-column text-center" aria-current="page" href="<?php echo $taoh_home_url."/jobs"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="31.051" height="27.691" viewBox="0 0 31.051 27.691">
              <g id="Layer_2" data-name="Layer 2" transform="translate(0 -0.005)">
                <g id="Layer_1" data-name="Layer 1" transform="translate(0.426 0.43)">
                  <path id="Path_355" data-name="Path 355" d="M30.626,15.735c-.078.39-.14.779-.242,1.169a7.057,7.057,0,0,1-1.847,3.265.756.756,0,0,0-.222.581,8.223,8.223,0,0,1-.086,1.582,2.985,2.985,0,0,1-2.993,2.4c-2.291.027-4.578,0-6.87,0-.351,0-.542-.168-.538-.448s.187-.433.549-.433H25.1a2.17,2.17,0,0,0,2.241-1.6,8.806,8.806,0,0,0,.136-1.13,7.512,7.512,0,0,1-8.358,0c-.269.273-.553.557-.826.849a.351.351,0,0,0-.055.238,1.033,1.033,0,0,1-.316.826C16.75,24.2,15.55,25.418,14.35,26.6a1.828,1.828,0,0,1-2.583-2.587c1.169-1.2,2.377-2.381,3.565-3.569a1.017,1.017,0,0,1,.826-.308.421.421,0,0,0,.277-.1c.277-.253.534-.522.818-.8a7.427,7.427,0,0,1-1.294-4.165h-7.4v1.559a1.66,1.66,0,0,1-3.308.242c-.039-.436-.019-.881-.023-1.325v-.5a5.334,5.334,0,0,1-3.9-2.256c0,.132,0,.214,0,.3v8.467a2.178,2.178,0,0,0,2.291,2.3h6.1c.421,0,.635.156.627.448s-.218.436-.643.436H3.673A3.077,3.077,0,0,1,.427,21.471V8.11A3.047,3.047,0,0,1,2.836,5.032a4.742,4.742,0,0,1,.908-.094H8.867v-.39a11.928,11.928,0,0,1,.121-1.874A2.829,2.829,0,0,1,11.716.457Q14.327.406,16.953.449a2.95,2.95,0,0,1,2.922,3.035V4.95h5.249a3.045,3.045,0,0,1,3.117,2.412,11.689,11.689,0,0,1,.086,2.026.779.779,0,0,0,.23.569,7.158,7.158,0,0,1,2.015,4.255,2.124,2.124,0,0,0,.055.226ZM27.466,9a11.078,11.078,0,0,0-.113-1.5,2.174,2.174,0,0,0-2.3-1.664H3.436A2.186,2.186,0,0,0,1.312,8.075V9.785a4.236,4.236,0,0,0,1.711,3.507,4.1,4.1,0,0,0,2.213.861,1.917,1.917,0,0,1,.39-1.407,1.656,1.656,0,0,1,1.29-.6A1.621,1.621,0,0,1,8,12.532a1.824,1.824,0,0,1,.581,1.6H16A7.4,7.4,0,0,1,27.466,9ZM16.848,15.069a6.449,6.449,0,1,0,6.461-6.457A6.449,6.449,0,0,0,16.848,15.069ZM18.963,4.938c0-.623.047-1.216,0-1.8a1.991,1.991,0,0,0-1.948-1.808c-1.75-.027-3.507-.023-5.245,0A2,2,0,0,0,9.9,2.717,8.431,8.431,0,0,0,9.775,4.93h.947V3.415A1.111,1.111,0,0,1,11.8,2.273q2.591-.023,5.186,0a1.122,1.122,0,0,1,1.079,1.169V4.677a1.111,1.111,0,0,0,.019.253Zm-7.38,0h5.58V3.617c0-.39-.058-.464-.456-.464H11.95c-.191,0-.343.066-.347.277C11.56,3.925,11.583,4.424,11.583,4.938Zm3.3,17.211c-.865.865-1.7,1.687-2.525,2.529a.92.92,0,0,0,0,1.3.947.947,0,0,0,1.305.039c.156-.136.3-.288.44-.433l2.085-2.081a1.629,1.629,0,0,0-.1-.132Zm-8.74-6.893h1.52V13.7A.748.748,0,0,0,6.915,13a.725.725,0,0,0-.779.616C6.1,14.165,6.144,14.707,6.144,15.256Zm.019.923a.982.982,0,0,0,.3,1.1.779.779,0,0,0,.861,0c.409-.277.39-.686.339-1.114Zm11.191,6.145a.355.355,0,0,0-.043-.082l-.779-.779c-.5-.5-.5-.5-.97,0h0l1.325,1.321ZM18.359,20.6l-.526-.623-.779.7.6.627Z" transform="translate(-0.426 -0.43)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                  <path id="Path_356" data-name="Path 356" d="M45.54,29.668a5.3,5.3,0,0,1,10.447-1.251c.078.323,0,.546-.269.62s-.483-.062-.584-.417a4.36,4.36,0,0,0-3.62-3.328,4.43,4.43,0,1,0,3.581,5.545.832.832,0,0,1,.058-.23.444.444,0,1,1,.853.23,4.992,4.992,0,0,1-1.2,2.338,5.323,5.323,0,0,1-9.254-2.9.9.9,0,0,1,0-.144Z" transform="translate(-27.961 -15.028)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                </g>
              </g>
          </svg><span class="small">Jobs</span></a>
          </li>
          <li class="nav-item <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/asks' ) ) ? "active":"") ?>">
            <a class="nav-link d-flex flex-column text-center" aria-current="page" href="<?php echo $taoh_home_url."/asks"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="33.085" height="28.854" viewBox="0 0 32.085 27.854">
              <g id="Group_136" data-name="Group 136" transform="translate(0.425 0.425)">
                <path id="Path_309" data-name="Path 309" d="M27.673,184H13.842A3.842,3.842,0,0,0,10,187.842V197.7a3.842,3.842,0,0,0,3.842,3.842H18l-.058,0,2.812,2.812,0-2.817h6.913a3.842,3.842,0,0,0,3.842-3.842v-9.861A3.842,3.842,0,0,0,27.673,184Z" transform="translate(-9.36 -177.999)" fill="none"/>
                <path id="Path_310" data-name="Path 310" d="M11.4,195.643a.64.64,0,0,1-.453-.188l-2.63-2.63H4.482A4.487,4.487,0,0,1,0,188.343v-9.861A4.487,4.487,0,0,1,4.482,174h6.467a.64.64,0,0,1,0,1.281H4.482a3.205,3.205,0,0,0-3.2,3.2v9.861a3.205,3.205,0,0,0,3.2,3.2H8.643a.64.64,0,0,1,.587.385l1.528,1.528v-1.273a.64.64,0,0,1,.64-.64h6.913a3.205,3.205,0,0,0,3.2-3.2v-4.418a.64.64,0,1,1,1.281,0v4.418a4.487,4.487,0,0,1-4.482,4.482H12.039l0,2.178a.64.64,0,0,1-.64.64Z" transform="translate(0 -168.639)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round" />
                <path id="Path_312" data-name="Path 312" d="M212.136,20.891a.64.64,0,0,1-.453-.187l-2.394-2.393a9.324,9.324,0,0,1-2.362.3h-1.621a9.307,9.307,0,1,1,0-18.613h1.621a9.317,9.317,0,0,1,9.307,9.307,9.206,9.206,0,0,1-1.011,4.223,9.4,9.4,0,0,1-2.446,3.016v3.7a.64.64,0,0,1-.64.64Zm-2.66-3.939a.64.64,0,0,1,.453.187l1.567,1.566V16.234a.64.64,0,0,1,.255-.511,8.026,8.026,0,0,0-4.823-14.441h-1.621a8.026,8.026,0,1,0,0,16.052h1.621a8.028,8.028,0,0,0,2.361-.353A.64.64,0,0,1,209.476,16.952Z" transform="translate(-184.999)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round" />
                <path id="Path_313" data-name="Path 313" d="M317.173,77.536a.64.64,0,0,1-.64-.64V75.358a1.212,1.212,0,0,1,.955-1.188,1.431,1.431,0,1,0-1.745-1.4.64.64,0,1,1-1.281,0,2.721,2.721,0,0,1,.861-1.982,2.69,2.69,0,0,1,2.04-.722,2.711,2.711,0,0,1,.45,5.339V76.9A.64.64,0,0,1,317.173,77.536Z" transform="translate(-295.466 -65.577)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round"/>
                <path id="Path_314" data-name="Path 314" d="M347.44,207.611a.639.639,0,1,1,.453-.187A.644.644,0,0,1,347.44,207.611Z" transform="translate(-325.686 -195.543)" fill="currentColor"/>
                <path id="Path_315" data-name="Path 315" d="M68.151,311.281H54.64a.64.64,0,0,1,0-1.281H68.151a.64.64,0,0,1,0,1.281Z" transform="translate(-50.542 -293.792)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round" />
                <path id="Path_316" data-name="Path 316" d="M265.64,367.281a.641.641,0,1,1,.453-.188A.645.645,0,0,1,265.64,367.281Z" transform="translate(-248.866 -346.865)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round" />
                <path id="Path_317" data-name="Path 317" d="M65.632,367.281H54.64a.64.64,0,0,1,0-1.281H65.632a.64.64,0,0,1,0,1.281Z" transform="translate(-50.542 -346.865)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round"/>
                <path id="Path_318" data-name="Path 318" d="M63.989,255.281H54.64a.64.64,0,0,1,0-1.281h9.349a.64.64,0,1,1,0,1.281Z" transform="translate(-50.542 -240.72)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round" />
              </g>
            </svg><span class="small">Asks</span></a>
          </li>
          
          <?php
            if ( taoh_user_is_logged_in() && TAOH_NOTIFICATION_ENABLED ) {
          ?>
           <li class="dropdown notification-dropdown ">
             <a class="nav-link dropdown-toggle dropdown--toggle pl-2 text-center" href="#" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <div class="notifier new" 
                onclick="
                <?php  if ( taoh_user_is_logged_in() && TAOH_NOTIFICATION_ENABLED && TAOH_NOTIFICATION_STATUS == 1) {  ?>
                    taoh_notification_init(1);
                <?php } else { ?> 
                  taoh_notification_init(0); taoh_counter_init(0);
                <?php } ?>
                ">
                  <i style="font-size:30px;" class="fas fa-bell"></i>
                  
                  <div style="display:none" id="badge_count" class="badges"></div>

                </div>
            </a>
              <div class="stay_open1 dropdown-menu dropdown--menu dropdown-menu-right keep-open notificationDropdown" style="width: 370px;" aria-labelledby="notificationDropdown">
                  <h6 class="dropdown-header">Notifications Received

                    <img style="display:none;" id="loaderChat" width="20" src="<?php //echo TAOH_SITE_URL_ROOT.'/assets/images/taoh_loader.gif'; ?>"/>
                  </h6>
                  <div class="dropdown-divider border-top-gray mb-0"></div>
                  <div class="dropdown-item-list " >
                        <ul id="notifications-list" style="overflow-y:auto">
                          <li class="no-result">No Result Found</li>
                          
                        </ul>
                      
       
                    </div>
                    <div class="dropdown-divider border-top-gray mb-0"></div>
                    <h6 class="dropdown-footer" >
                      <a target="_blank" style="font-size:12px;padding-left:100px;display:none" href="<?php //echo TAOH_SITE_URL_ROOT.'/notifications/';?>" id="notification_load_more" style="display:none;"  >
                        See all Notifications
                      </a>
                    </h6>
                    
                </div>

         </li> 
         <?php } ?>
          <?php
            if ( taoh_user_is_logged_in() ) {
          ?>
            <li class="dropdown stay_open user-dropdown bor-right">
                <a class="nav-link dropdown-toggle dropdown--toggle pl-2 text-center" href="#" id="userMenuDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media media-card media--card shadow-none mb-0 rounded-0 align-items-center bg-transparent">
                                <div class="media-body p-0 border-left-0">
                                        <?php echo taoh_get_profile_image(); ?>
                                </div>
                        </div>
                </a>
                <div class="dropdown-menu dropdown--menu dropdown-menu-right mt-3 keep-open" id="userMenuDropdownarea" aria-labelledby="userMenuDropdown">
                        <h6 class="dropdown-header">Hi, <?php echo taoh_user_full_name(); ?></h6>
                        <div class="dropdown-divider border-top-gray mb-0"></div>
                        <div class="dropdown-item-list">
                                <a class="dropdown-item" href="<?php echo  $taoh_home_url."/referral"; ?>"><i class="la la-user-plus mr-2"></i>Referral</a>
                                <!--<a class="dropdown-item status-list" href="#">
                                <p style="position: relative"><input type="text" maxlength="140" value="<?php echo $my_status; ?>" name="my_status" id="my_status" placeholder="Update your status">
                                    
                                    <button class="btn btn-success" id="status_save" onclick="updateStatus(1)"><i class="la la-check mr-2"></i></button>
                                    
                                    <button class="btn btn-danger" id="status_remove" style="display: none" onclick="updateStatus(0)"><i class="la la-close mr-2"></i></button>
                                </p>
                                </a>-->
                                <a class="dropdown-item" href="<?php echo $taoh_home_url."/profile/".$ptoken; ?>"><i class="la la-user mr-2"></i>Public Profile</a>
                                <a class="dropdown-item" href="<?php echo $taoh_home_url."/settings"; ?>"><i class="la la-gear mr-2"></i>Settings</a>
                                <a onclick="localStorage.removeItem('isCodeSent')" class="dropdown-item" href="<?php echo  $taoh_home_url."/logout";?>"><i class="la la-power-off mr-2"></i>Log out</a>
                        </div>
                </div>
            </li>
          <?php
            } else if ( defined(  'TAOH_PAGE_AVATAR') && defined(  'TAOH_PAGE_AVATAR') && defined( 'TAOH_PAGE_CHAT_NAME' ) && defined( 'TAOH_PAGE_PTOKEN' ) ){
              ?>
              <li class="dropdown user-dropdown bor-right">
                  <a class="nav-link dropdown-toggle dropdown--toggle pl-2" href="#" id="userMenuDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <div class="media media-card media--card shadow-none mb-0 rounded-0 align-items-center bg-transparent">
                                  <div class="media-body p-0 border-left-0">
                                          <?php echo taoh_get_profile_image(); ?>
                                  </div>
                          </div>
                  </a>
                  <div class="dropdown-menu dropdown--menu dropdown-menu-right mt-3 keep-open" aria-labelledby="userMenuDropdown">
                      <div class="dropdown-item-list">
                              <a onclick="localStorage.removeItem('isCodeSent')" class="dropdown-item" href="<?php echo  $taoh_home_url."/profile/".TAOH_PAGE_PTOKEN;?>"><i class="la la-power-off mr-2"></i><?php echo TAOH_PAGE_CHAT_NAME; ?></a>
                      </div>
                  </div>
              </li>
            <?php
              } else {
            ?>
            <li class="dropdown user-dropdown bor-right">
                <a class="nav-link dropdown-toggle dropdown--toggle pl-2" href="#" id="userMenuDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media media-card media--card shadow-none mb-0 rounded-0 align-items-center bg-transparent">
                                <div class="media-body p-0 border-left-0">
                                        <?php echo taoh_get_profile_image(); ?>
                                </div>
                        </div>
                </a>
                <div class="dropdown-menu dropdown--menu dropdown-menu-right mt-3 keep-open" aria-labelledby="userMenuDropdown">
                    <div class="dropdown-item-list">
                            <a onclick="localStorage.removeItem('isCodeSent'); openLoginPopup(); return false;" class="dropdown-item" href="<?php echo  $taoh_home_url."/login.php"; ?>"><i class="la la-power-off mr-2"></i>Log In</a>
                    </div>
                </div>
            </li>
          <?php
            }
          ?>
         <!-- href="<?php //echo $taoh_home_url."/support"; ?>" -->
         <li class="nav-item <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/support' ) ) ? "active":"") ?>">
            <a class="nav-link d-flex flex-column text-center support-page cursor-pointer" aria-current="page" >
            <svg xmlns="http://www.w3.org/2000/svg" width="26.107" height="26.107" viewBox="0 0 48.107 48.107">
              <g id="bx_help_circle-1325051883812201654" data-name="bx+help+circle-1325051883812201654" transform="translate(-2 -2)">
                <path id="Path_364" data-name="Path 364" d="M17.529,6a9.474,9.474,0,0,0-9.463,9.463h4.811a4.652,4.652,0,0,1,9.3,0c0,1.438-1.157,2.482-2.925,3.911-.613.5-1.193.972-1.662,1.441a7.836,7.836,0,0,0-2.47,5.229v1.6h4.811l0-1.523a3.3,3.3,0,0,1,1.061-1.907,16.663,16.663,0,0,1,1.287-1.1c1.874-1.518,4.71-3.81,4.71-7.654A9.47,9.47,0,0,0,17.529,6ZM15.123,30.053h4.811v4.811H15.123Z" transform="translate(8.525 5.621)" fill="#7f7f7f"/>
                <path id="Path_365" data-name="Path 365" d="M26.053,2A24.053,24.053,0,1,0,50.107,26.053,24.081,24.081,0,0,0,26.053,2Zm0,43.3A19.243,19.243,0,1,1,45.3,26.053,19.265,19.265,0,0,1,26.053,45.3Z" fill="#7f7f7f"/>
              </g>
            </svg><span class="small">Support</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link d-flex flex-column text-center" data-toggle="modal" data-target="#learn-modal"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor" class="mercado-match" width="24" height="24" focusable="false">
              <path d="M3 3h4v4H3zm7 4h4V3h-4zm7-4v4h4V3zM3 14h4v-4H3zm7 0h4v-4h-4zm7 0h4v-4h-4zM3 21h4v-4H3zm7 0h4v-4h-4zm7 0h4v-4h-4z"></path>
              </svg><span class="small dropdown-toggle">Learning</span></a>
          </li>
          <?php if (! taoh_user_is_logged_in()){ ?>
          <li style="color:#2d86eb;font-weight:bold;" class="nav-item <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/employers' ) ) ? "active":"") ?>">
            <a class="nav-link d-flex flex-column text-center" aria-current="page" href="<?php echo TAOH_SITE_URL_ROOT."/employers"; ?>">
            <img width="30" height="" src="<?php echo TAOH_SITE_URL_ROOT."/assets/images/employer_menu_9d0f54.png"; ?>">
            <span class="small" style="color: #9d0f54;font-weight:bold;">Employers</span></a>
          </li>
          <?php } ?>
          <?php 
          if ( taoh_user_is_logged_in() && isset( $_SESSION[ TAOH_ROOT_PATH_HASH ][ 'USER_INFO' ]->type ) && $_SESSION[ TAOH_ROOT_PATH_HASH ][ 'USER_INFO' ]->type == 'employer' ){ ?>
          <li style="color:#2d86eb;font-weight:bold;" class="nav-item <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/employers' ) ) ? "active":"") ?>">
            <a class="nav-link d-flex flex-column text-center" aria-current="page" href="<?php echo TAOH_SITE_URL_ROOT.'/jobs/dash'; ?>">
            <img width="30" height="" src="<?php echo TAOH_SITE_URL_ROOT."/assets/images/employer_menu_9d0f54.png"; ?>">
            <span class="small" style="color: #9d0f54;font-weight:bold;">Employers</span></a>
          </li>
          <?php } ?>
        </ul>
      <?php } ?>
    </div>
  </nav>
  <div class="off-canvas-menu custom-scrollbar-styled">
        <div class="off-canvas-menu-close icon-element icon-element-sm" data-toggle="tooltip" data-placement="left" title="" data-original-title="Close menu">
            <i class="la la-times"></i>
        </div><!-- end off-canvas-menu-close -->
        <ul class="generic-list-item off-canvas-menu-list pt-90px">
            <!-- <li>
                <a href="#">Home<button class="sub-nav-toggler" type="button"><i class="la la-angle-down"></i></button></a>
                <ul class="sub-menu">
                    <li><a href="index.html">Home - landing</a></li>
                    <li><a href="home-2.html">Home - main</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Pages<button class="sub-nav-toggler" type="button"><i class="la la-angle-down"></i></button></a>
                <ul class="sub-menu">
                    <li><a href="user-profile.html">user profile</a></li>
                    <li><a href="notifications.html">Notifications</a></li>
                    <li><a href="referrals.html">Referrals</a></li>
                    <li><a href="setting.html">settings</a></li>
                    <li><a href="ask-question.html">ask question</a></li>
                </ul>
            </li> -->
            <li class="nav-item <?php echo (( 
           stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/stlo') || 
           stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/') || 
            stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/club' ) ||
            stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/directory' ) ||
            stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/networking' ) ||
            stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/room' ) 
            
            ) ? "active":"") ?>">
            <a class="nav-link text-center" aria-current="page" href="<?php echo $taoh_home_url."/club"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="27.712" height="27.555" viewBox="0 0 27.712 27.555">
              <g id="Layer_2" data-name="Layer 2" transform="translate(-0.032 0.003)">
                <g id="Layer_1" data-name="Layer 1" transform="translate(0.457 0.422)">
                  <path id="Path_351" data-name="Path 351" d="M13.3.459a2.6,2.6,0,0,0-1.063.631,2.631,2.631,0,0,0-.376.6,1.5,1.5,0,0,0-.173.872,1.5,1.5,0,0,0,.173.876,2.067,2.067,0,0,0,1,1.041,2.322,2.322,0,0,0,.451.188c.06,0,.071.162.06.9v.906l-.395.038a7.381,7.381,0,0,0-3.668,1.5l-.342.267-2.029-2-2-2,.105-.233A1.92,1.92,0,0,0,5.02,2.68a1.879,1.879,0,0,0-.834-.834,1.958,1.958,0,0,0-1.458,0,1.924,1.924,0,0,0-.834.849,1.958,1.958,0,0,0,0,1.458,1.924,1.924,0,0,0,.834.834,1.92,1.92,0,0,0,1.364.026L4.325,4.9l2,2.007,2,2-.271.346a7.414,7.414,0,0,0-1.5,3.668l-.038.376-.466.019H5.58l-.03-.188A2.8,2.8,0,0,0,4.829,11.9a2.54,2.54,0,0,0-3.641.056,2.544,2.544,0,0,0,.068,3.634,2.533,2.533,0,0,0,3.574,0,2.818,2.818,0,0,0,.718-1.2l.03-.188h.936l.038.376a7.546,7.546,0,0,0,1.417,3.559,8.538,8.538,0,0,0,2.21,1.939,7.659,7.659,0,0,0,2.777.917l.406.038v.9c0,.752,0,.906-.06.906a2.322,2.322,0,0,0-.451.188,2.067,2.067,0,0,0-1,1.041,1.5,1.5,0,0,0-.173.879,1.5,1.5,0,0,0,.2.921,2.074,2.074,0,0,0,1.063,1.079,1.5,1.5,0,0,0,.872.173,1.5,1.5,0,0,0,.876-.173,2.089,2.089,0,0,0,1.056-1.03,1.5,1.5,0,0,0,.2-.921,1.5,1.5,0,0,0-.173-.879,2.067,2.067,0,0,0-1-1.041,2.322,2.322,0,0,0-.451-.188c-.06,0-.071-.162-.06-.9v-.906l.376-.038a7.482,7.482,0,0,0,3.63-1.481l.376-.3.962.924.924.921-.105.233a1.706,1.706,0,0,0,.861,2.2,1.958,1.958,0,0,0,1.458,0,1.729,1.729,0,0,0,.853-2.292,1.905,1.905,0,0,0-.834-.834,1.943,1.943,0,0,0-1.42-.023l-.2.109-.924-.928-.921-.928.271-.338a7.478,7.478,0,0,0,1.5-3.671l.038-.376h.932l.034.188a2.818,2.818,0,0,0,.722,1.229,2.57,2.57,0,1,0,0-3.687,2.8,2.8,0,0,0-.7,1.18l-.034.188h-.466l-.466-.019-.038-.376a7.414,7.414,0,0,0-1.5-3.668L19.3,8.948l.9-.958.921-.928.233.109a1.5,1.5,0,0,0,.661.109,1.2,1.2,0,0,0,.782-.2,1.717,1.717,0,0,0-.068-3.063A1.357,1.357,0,0,0,22,3.886a1.357,1.357,0,0,0-.729.135,1.879,1.879,0,0,0-.834.834,1.95,1.95,0,0,0-.023,1.39l.109.207-.928.921-.928.921-.376-.3a7.448,7.448,0,0,0-3.63-1.481l-.376-.038V5.57c0-.752,0-.9.06-.9A2.322,2.322,0,0,0,14.8,4.48a2.067,2.067,0,0,0,1-1.041,1.5,1.5,0,0,0,.173-.879,1.5,1.5,0,0,0-.2-.921A2.089,2.089,0,0,0,13.927.425,3.232,3.232,0,0,0,13.3.459Zm.906.872A1.432,1.432,0,0,1,15.1,2.56a1.3,1.3,0,1,1-2.589,0,1.172,1.172,0,0,1,.406-.887A1.2,1.2,0,0,1,14.208,1.331ZM3.8,2.639a.849.849,0,1,1-1.184.6.868.868,0,0,1,1.184-.6ZM22.341,4.792A.838.838,0,0,1,22.6,6.175a.8.8,0,0,1-1.191,0,.853.853,0,0,1,.936-1.383ZM14.825,7.385a6.475,6.475,0,0,1,5.122,8.4,5.4,5.4,0,0,1-.725,1.481c-.034,0-.086-.086-.124-.21a7.114,7.114,0,0,0-.706-1.447,6.764,6.764,0,0,0-1.747-1.65c-.259-.158-.5-.293-.534-.3s0-.105.109-.237A3.006,3.006,0,0,0,15.133,8.9a2.912,2.912,0,0,0-2.631,0,3.006,3.006,0,0,0-1.109,4.513.778.778,0,0,1,.147.225l-.346.188a5.8,5.8,0,0,0-2.631,3.149c-.053.165-.124.3-.15.289a5.241,5.241,0,0,1-.725-1.481A6.475,6.475,0,0,1,14.817,7.4Zm-.132,2.274a2.074,2.074,0,0,1,1.045,1.026,1.526,1.526,0,0,1,.2.947,1.661,1.661,0,0,1-.116.8,2.484,2.484,0,0,1-.838.909.462.462,0,0,0-.312.451c0,.218.147.376.47.447a6.445,6.445,0,0,1,1.379.68,4.96,4.96,0,0,1,1.9,2.92l.083.376-.338.312a6.708,6.708,0,0,1-3.074,1.586,8.9,8.9,0,0,1-2.567,0,6.655,6.655,0,0,1-3.074-1.586l-.342-.312.079-.376a4.78,4.78,0,0,1,3.236-3.585c.376-.124.522-.252.522-.474a.462.462,0,0,0-.312-.436,2.484,2.484,0,0,1-.838-.909,1.646,1.646,0,0,1-.116-.793,1.533,1.533,0,0,1,.173-.9,2.082,2.082,0,0,1,.924-1,1.879,1.879,0,0,1,1.15-.259A1.8,1.8,0,0,1,14.693,9.658ZM3.754,12.217a1.879,1.879,0,0,1,.834.823,1.334,1.334,0,0,1,.135.752,1.266,1.266,0,0,1-.184.8,1.556,1.556,0,0,1-.782.752,1.251,1.251,0,0,1-.733.143,1.312,1.312,0,0,1-.725-.132,1.732,1.732,0,0,1,.188-3.2,1.879,1.879,0,0,1,1.266.064Zm21.563,0a1.905,1.905,0,0,1,.838.823,1.375,1.375,0,0,1,.132.752,1.289,1.289,0,0,1-.18.8,1.586,1.586,0,0,1-.782.752,1.266,1.266,0,0,1-.733.143,1.319,1.319,0,0,1-.729-.132,1.732,1.732,0,0,1,.188-3.2A1.879,1.879,0,0,1,25.317,12.217ZM22.341,21.18a.838.838,0,0,1,.256,1.383.8.8,0,0,1-1.191,0,.853.853,0,0,1,.936-1.383Zm-8.132,2.578a1.428,1.428,0,0,1,.894,1.229,1.3,1.3,0,0,1-2.589,0,1.349,1.349,0,0,1,1.695-1.229Z" transform="translate(-0.457 -0.422)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                  <path id="Path_352" data-name="Path 352" d="M62.936,5.541c-.473.477-.556.586-.556.729a.455.455,0,0,0,.443.443c.143,0,.252-.083.725-.556s.567-.582.567-.729A.462.462,0,0,0,63.661,5C63.519,5,63.41,5.068,62.936,5.541Z" transform="translate(-39.11 -3.28)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                  <path id="Path_353" data-name="Path 353" d="M17.076,51.426c-.474.473-.556.582-.556.729a.451.451,0,0,0,.44.44c.143,0,.252-.079.7-.515a3.431,3.431,0,0,0,.545-.658.458.458,0,0,0-.466-.552C17.655,50.869,17.418,51.065,17.076,51.426Z" transform="translate(-10.484 -31.912)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                  <path id="Path_354" data-name="Path 354" d="M7.3,55.546a11.174,11.174,0,0,0-1.037.981l-.928.932-.225-.109a1.917,1.917,0,0,0-1.364.026,1.92,1.92,0,0,0-.838.838,1.947,1.947,0,0,0,0,1.454,1.717,1.717,0,0,0,3.07.083,1.2,1.2,0,0,0,.192-.782,1.541,1.541,0,0,0-.109-.646L5.95,58.09l.958-.955A9.77,9.77,0,0,0,7.9,56.06a.485.485,0,0,0-.229-.492A.376.376,0,0,0,7.3,55.546ZM4.807,58.154a.849.849,0,0,1,.259,1.387.815.815,0,0,1-1.2,0,.853.853,0,0,1,.939-1.387Z" transform="translate(-1.898 -34.806)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                </g>
              </g>
            </svg><span class="small">Club</span></a>
          </li>
            <li class="nav-item <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/events' ) ) ? "active":"") ?>">
            <a class="nav-link text-center" aria-current="page" href="<?php echo $taoh_home_url."/events"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="25.27" height="26.22" viewBox="0 0 25.251 26.169">
                <g id="Layer_1" data-name="Layer 1" transform="translate(0.375 0.375)" >
                  <path id="Path_349" data-name="Path 349" d="M5.3,0H7.679A1.072,1.072,0,0,1,8.57,1.087V2.742h7.355V1.114A1.075,1.075,0,0,1,16.82,0H19.2c.054.019.1.046.157.061a1.079,1.079,0,0,1,.853,1.152V2.75h1.947a3.073,3.073,0,0,1,.883.169A2.231,2.231,0,0,1,24.494,5.1V22.926a2.525,2.525,0,0,1-2.039,2.47,2.281,2.281,0,0,1-.384.023H2.436a2.251,2.251,0,0,1-1.99-1.095A2.946,2.946,0,0,1,0,22.661Q0,13.881,0,5.1A2.262,2.262,0,0,1,2.321,2.758H4.3V1.171A1.037,1.037,0,0,1,5.063.108ZM1.23,9.809V22.956A1.218,1.218,0,0,0,2.544,24.2H22a1.079,1.079,0,0,0,1.152-.8,2.266,2.266,0,0,0,.108-.68V9.817Zm18.981-5.83v.469A1.252,1.252,0,0,1,18.84,5.815H17.1a1.129,1.129,0,0,1-1.179-1.206V3.991H8.578v.73A1.114,1.114,0,0,1,7.525,5.807c-.676,0-1.356.019-2.032,0A1.214,1.214,0,0,1,4.3,4.551V3.979h-2.1a1.014,1.014,0,0,0-.968,1.068q0,1.636,0,3.272v.242H23.261a.726.726,0,0,0,0-.111V4.993a1,1,0,0,0-.956-.987C21.629,3.96,20.945,3.979,20.211,3.979Zm-3.107.6h1.874V1.244H17.1Zm-9.771,0V1.248H5.62V4.582Z" transform="translate(0.004 0)"  stroke="currentColor" stroke-width="0.75" fill="currentColor" class="mercado-match" focusable="false"/>
                  <path id="Path_350" data-name="Path 350" d="M18.655,39.537c.134-.741.227-1.536.422-2.266a1.2,1.2,0,0,0-.453-1.425c-.553-.442-1.033-.976-1.536-1.463a.676.676,0,0,1-.242-.73.661.661,0,0,1,.615-.426c.891-.131,1.782-.273,2.689-.384a.438.438,0,0,0,.384-.284c.384-.814.8-1.617,1.191-2.427a.68.68,0,0,1,.615-.461.664.664,0,0,1,.611.461c.407.833.822,1.655,1.229,2.489a.334.334,0,0,0,.3.211c.914.131,1.832.269,2.746.407a.645.645,0,0,1,.6.442.664.664,0,0,1-.246.707c-.657.63-1.31,1.264-1.97,1.886a.384.384,0,0,0-.131.411c.165.876.33,1.751.469,2.635a1.064,1.064,0,0,1-.073.611c-.154.3-.507.353-.872.165-.8-.411-1.6-.822-2.389-1.256a.5.5,0,0,0-.545,0q-1.241.649-2.485,1.279a.618.618,0,0,1-.922-.58Zm3.653-7.877c-.342.68-.657,1.306-.968,1.92a.591.591,0,0,1-.465.361l-.768.108-1.337.192-.038.069a2.361,2.361,0,0,1,.2.142c.43.411.856.826,1.29,1.237a.7.7,0,0,1,.238.645c-.054.315-.111.634-.165.953-.065.384-.123.768-.2,1.183.63-.342,1.206-.645,1.774-.96a.807.807,0,0,1,.845,0c.519.288,1.045.565,1.567.849.061.031.127.054.211.088-.092-.553-.134-1.087-.273-1.594a1.291,1.291,0,0,1,.488-1.5A10.651,10.651,0,0,0,25.761,34.2c-.031.023-.127,0-.219,0-.549-.077-1.095-.173-1.648-.227a.787.787,0,0,1-.691-.507C22.93,32.869,22.627,32.3,22.308,31.66Z" transform="translate(-10.358 -18.274)"  stroke="currentColor" stroke-width="0.75" fill="currentColor" class="mercado-match" focusable="false"/>
                </g>
            </svg><span class="small">Events</span></a>
          </li>
          <li class="nav-item <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/jobs' ) ) ? "active":"") ?>">
            <a class="nav-link text-center" aria-current="page" href="<?php echo $taoh_home_url."/jobs"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="31.051" height="27.691" viewBox="0 0 31.051 27.691">
              <g id="Layer_2" data-name="Layer 2" transform="translate(0 -0.005)">
                <g id="Layer_1" data-name="Layer 1" transform="translate(0.426 0.43)">
                  <path id="Path_355" data-name="Path 355" d="M30.626,15.735c-.078.39-.14.779-.242,1.169a7.057,7.057,0,0,1-1.847,3.265.756.756,0,0,0-.222.581,8.223,8.223,0,0,1-.086,1.582,2.985,2.985,0,0,1-2.993,2.4c-2.291.027-4.578,0-6.87,0-.351,0-.542-.168-.538-.448s.187-.433.549-.433H25.1a2.17,2.17,0,0,0,2.241-1.6,8.806,8.806,0,0,0,.136-1.13,7.512,7.512,0,0,1-8.358,0c-.269.273-.553.557-.826.849a.351.351,0,0,0-.055.238,1.033,1.033,0,0,1-.316.826C16.75,24.2,15.55,25.418,14.35,26.6a1.828,1.828,0,0,1-2.583-2.587c1.169-1.2,2.377-2.381,3.565-3.569a1.017,1.017,0,0,1,.826-.308.421.421,0,0,0,.277-.1c.277-.253.534-.522.818-.8a7.427,7.427,0,0,1-1.294-4.165h-7.4v1.559a1.66,1.66,0,0,1-3.308.242c-.039-.436-.019-.881-.023-1.325v-.5a5.334,5.334,0,0,1-3.9-2.256c0,.132,0,.214,0,.3v8.467a2.178,2.178,0,0,0,2.291,2.3h6.1c.421,0,.635.156.627.448s-.218.436-.643.436H3.673A3.077,3.077,0,0,1,.427,21.471V8.11A3.047,3.047,0,0,1,2.836,5.032a4.742,4.742,0,0,1,.908-.094H8.867v-.39a11.928,11.928,0,0,1,.121-1.874A2.829,2.829,0,0,1,11.716.457Q14.327.406,16.953.449a2.95,2.95,0,0,1,2.922,3.035V4.95h5.249a3.045,3.045,0,0,1,3.117,2.412,11.689,11.689,0,0,1,.086,2.026.779.779,0,0,0,.23.569,7.158,7.158,0,0,1,2.015,4.255,2.124,2.124,0,0,0,.055.226ZM27.466,9a11.078,11.078,0,0,0-.113-1.5,2.174,2.174,0,0,0-2.3-1.664H3.436A2.186,2.186,0,0,0,1.312,8.075V9.785a4.236,4.236,0,0,0,1.711,3.507,4.1,4.1,0,0,0,2.213.861,1.917,1.917,0,0,1,.39-1.407,1.656,1.656,0,0,1,1.29-.6A1.621,1.621,0,0,1,8,12.532a1.824,1.824,0,0,1,.581,1.6H16A7.4,7.4,0,0,1,27.466,9ZM16.848,15.069a6.449,6.449,0,1,0,6.461-6.457A6.449,6.449,0,0,0,16.848,15.069ZM18.963,4.938c0-.623.047-1.216,0-1.8a1.991,1.991,0,0,0-1.948-1.808c-1.75-.027-3.507-.023-5.245,0A2,2,0,0,0,9.9,2.717,8.431,8.431,0,0,0,9.775,4.93h.947V3.415A1.111,1.111,0,0,1,11.8,2.273q2.591-.023,5.186,0a1.122,1.122,0,0,1,1.079,1.169V4.677a1.111,1.111,0,0,0,.019.253Zm-7.38,0h5.58V3.617c0-.39-.058-.464-.456-.464H11.95c-.191,0-.343.066-.347.277C11.56,3.925,11.583,4.424,11.583,4.938Zm3.3,17.211c-.865.865-1.7,1.687-2.525,2.529a.92.92,0,0,0,0,1.3.947.947,0,0,0,1.305.039c.156-.136.3-.288.44-.433l2.085-2.081a1.629,1.629,0,0,0-.1-.132Zm-8.74-6.893h1.52V13.7A.748.748,0,0,0,6.915,13a.725.725,0,0,0-.779.616C6.1,14.165,6.144,14.707,6.144,15.256Zm.019.923a.982.982,0,0,0,.3,1.1.779.779,0,0,0,.861,0c.409-.277.39-.686.339-1.114Zm11.191,6.145a.355.355,0,0,0-.043-.082l-.779-.779c-.5-.5-.5-.5-.97,0h0l1.325,1.321ZM18.359,20.6l-.526-.623-.779.7.6.627Z" transform="translate(-0.426 -0.43)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                  <path id="Path_356" data-name="Path 356" d="M45.54,29.668a5.3,5.3,0,0,1,10.447-1.251c.078.323,0,.546-.269.62s-.483-.062-.584-.417a4.36,4.36,0,0,0-3.62-3.328,4.43,4.43,0,1,0,3.581,5.545.832.832,0,0,1,.058-.23.444.444,0,1,1,.853.23,4.992,4.992,0,0,1-1.2,2.338,5.323,5.323,0,0,1-9.254-2.9.9.9,0,0,1,0-.144Z" transform="translate(-27.961 -15.028)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-miterlimit="10"/>
                </g>
              </g>
          </svg><span class="small">Jobs</span></a>
          </li>
          <li class="nav-item <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/asks' ) ) ? "active":"") ?>">
            <a class="nav-link text-center" aria-current="page" href="<?php echo $taoh_home_url."/asks"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="33.085" height="28.854" viewBox="0 0 32.085 27.854">
              <g id="Group_136" data-name="Group 136" transform="translate(0.425 0.425)">
                <path id="Path_309" data-name="Path 309" d="M27.673,184H13.842A3.842,3.842,0,0,0,10,187.842V197.7a3.842,3.842,0,0,0,3.842,3.842H18l-.058,0,2.812,2.812,0-2.817h6.913a3.842,3.842,0,0,0,3.842-3.842v-9.861A3.842,3.842,0,0,0,27.673,184Z" transform="translate(-9.36 -177.999)" fill="none"/>
                <path id="Path_310" data-name="Path 310" d="M11.4,195.643a.64.64,0,0,1-.453-.188l-2.63-2.63H4.482A4.487,4.487,0,0,1,0,188.343v-9.861A4.487,4.487,0,0,1,4.482,174h6.467a.64.64,0,0,1,0,1.281H4.482a3.205,3.205,0,0,0-3.2,3.2v9.861a3.205,3.205,0,0,0,3.2,3.2H8.643a.64.64,0,0,1,.587.385l1.528,1.528v-1.273a.64.64,0,0,1,.64-.64h6.913a3.205,3.205,0,0,0,3.2-3.2v-4.418a.64.64,0,1,1,1.281,0v4.418a4.487,4.487,0,0,1-4.482,4.482H12.039l0,2.178a.64.64,0,0,1-.64.64Z" transform="translate(0 -168.639)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round" />
                <path id="Path_312" data-name="Path 312" d="M212.136,20.891a.64.64,0,0,1-.453-.187l-2.394-2.393a9.324,9.324,0,0,1-2.362.3h-1.621a9.307,9.307,0,1,1,0-18.613h1.621a9.317,9.317,0,0,1,9.307,9.307,9.206,9.206,0,0,1-1.011,4.223,9.4,9.4,0,0,1-2.446,3.016v3.7a.64.64,0,0,1-.64.64Zm-2.66-3.939a.64.64,0,0,1,.453.187l1.567,1.566V16.234a.64.64,0,0,1,.255-.511,8.026,8.026,0,0,0-4.823-14.441h-1.621a8.026,8.026,0,1,0,0,16.052h1.621a8.028,8.028,0,0,0,2.361-.353A.64.64,0,0,1,209.476,16.952Z" transform="translate(-184.999)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round" />
                <path id="Path_313" data-name="Path 313" d="M317.173,77.536a.64.64,0,0,1-.64-.64V75.358a1.212,1.212,0,0,1,.955-1.188,1.431,1.431,0,1,0-1.745-1.4.64.64,0,1,1-1.281,0,2.721,2.721,0,0,1,.861-1.982,2.69,2.69,0,0,1,2.04-.722,2.711,2.711,0,0,1,.45,5.339V76.9A.64.64,0,0,1,317.173,77.536Z" transform="translate(-295.466 -65.577)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round"/>
                <path id="Path_314" data-name="Path 314" d="M347.44,207.611a.639.639,0,1,1,.453-.187A.644.644,0,0,1,347.44,207.611Z" transform="translate(-325.686 -195.543)" fill="currentColor"/>
                <path id="Path_315" data-name="Path 315" d="M68.151,311.281H54.64a.64.64,0,0,1,0-1.281H68.151a.64.64,0,0,1,0,1.281Z" transform="translate(-50.542 -293.792)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round" />
                <path id="Path_316" data-name="Path 316" d="M265.64,367.281a.641.641,0,1,1,.453-.188A.645.645,0,0,1,265.64,367.281Z" transform="translate(-248.866 -346.865)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round" />
                <path id="Path_317" data-name="Path 317" d="M65.632,367.281H54.64a.64.64,0,0,1,0-1.281H65.632a.64.64,0,0,1,0,1.281Z" transform="translate(-50.542 -346.865)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round"/>
                <path id="Path_318" data-name="Path 318" d="M63.989,255.281H54.64a.64.64,0,0,1,0-1.281h9.349a.64.64,0,1,1,0,1.281Z" transform="translate(-50.542 -240.72)" stroke="currentColor" stroke-width="0.85" fill="currentColor" class="mercado-match" focusable="false" stroke-linejoin="round" />
              </g>
            </svg><span class="small">Asks</span></a>
          </li>
          
          <?php
          if ( 0 && taoh_user_is_logged_in() )  {
          ?>
          <li class="nav-item <?php echo (( stristr( $_SERVER[ 'REQUEST_URI' ], TAOH_PLUGIN_PATH_NAME.'/message' ) ) ? "active":"") ?>">
            <a class="nav-link d-flex flex-column text-center" aria-current="page" href="<?php echo $taoh_home_url."/message"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="35.712" height="27.555" viewBox="0 0 35.712 27.555">
            <defs id="defs2"/>
              <g id="layer1" transform="translate(-396,-292)">

              <path d="m 420,309.01367 a 1,1 0 0 0 -1,1 1,1 0 0 0 1,1 1,1 0 0 0 1,-1 1,1 0 0 0 -1,-1 z" id="path453477" style="color:#4C4C4C;fill:#7F7F7F;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"/>

              <path d="m 417,309.01367 a 1,1 0 0 0 -1,1 1,1 0 0 0 1,1 1,1 0 0 0 1,-1 1,1 0 0 0 -1,-1 z" id="path453475" style="color:#4C4C4C;fill:#7F7F7F;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"/>

              <path d="m 414,309.01367 a 1,1 0 0 0 -1,1 1,1 0 0 0 1,1 1,1 0 0 0 1,-1 1,1 0 0 0 -1,-1 z" id="path453473" style="color:#4C4C4C;fill:#7F7F7F;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"/>

              <path d="m 410,300.01367 a 1,1 0 0 0 -1,1 1,1 0 0 0 1,1 1,1 0 0 0 1,-1 1,1 0 0 0 -1,-1 z" id="path453457" style="color:#4C4C4C;fill:#7F7F7F;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"/>

              <path d="m 407,300.01367 a 1,1 0 0 0 -1,1 1,1 0 0 0 1,1 1,1 0 0 0 1,-1 1,1 0 0 0 -1,-1 z" id="path453455" style="color:#4C4C4C;fill:#7F7F7F;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"/>

              <path d="m 404,300.01367 a 1,1 0 0 0 -1,1 1,1 0 0 0 1,1 1,1 0 0 0 1,-1 1,1 0 0 0 -1,-1 z" id="path453453" style="color:#4C4C4C;fill:#7F7F7F;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"/>

              <path d="m 400.05859,294.01367 c -1.09347,0 -2.05859,0.87774 -2.05859,2 v 10 c 0,1.12227 0.96512,2 2.05859,2 H 402 v 3 a 1.0001,1.0001 0 0 0 1.70703,0.70703 l 3.70703,-3.70703 H 408 v 7 c 0,1.122 0.96512,2 2.05859,2 h 4.88086 l 3.70703,3.70703 a 1.0001,1.0001 0 0 0 1.70704,-0.70703 v -3 h 3.58789 c 1.09347,0 2.05859,-0.87802 2.05859,-2 v -10 c 0,-1.12198 -0.96512,-2 -2.05859,-2 H 416 v -7 c 0,-1.12226 -0.96512,-2 -2.05859,-2 z m 0,2 h 13.88282 c 0.0399,0 0.0539,0.004 0.0586,0.006 v 9.98828 c -0.005,0.002 -0.0187,0.006 -0.0586,0.006 H 407 a 1.0001,1.0001 0 0 0 -0.70703,0.29297 L 404,308.59961 v -1.58594 a 1.0001,1.0001 0 0 0 -1,-1 h -2.94141 c -0.0399,0 -0.0539,-0.004 -0.0586,-0.006 v -9.98828 c 0.005,-0.002 0.0187,-0.006 0.0586,-0.006 z m 15.94141,9 h 7.94141 c 0.0399,0 0.0539,0.004 0.0586,0.006 v 9.98828 c -0.005,0.002 -0.0187,0.006 -0.0586,0.006 h -4.58789 a 1.0001,1.0001 0 0 0 -1,1 v 1.58594 l -2.29297,-2.29297 a 1.0001,1.0001 0 0 0 -0.70703,-0.29297 h -5.29493 c -0.0399,0 -0.0539,-0.004 -0.0586,-0.006 v -6.99414 h 3.94141 c 1.09347,0 2.05859,-0.87773 2.05859,-2 z" id="path453429" style="color:#4C4C4C;fill:#7F7F7F;fill-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4.1;-inkscape-stroke:none"/>

              </g>
            </svg><span class="small">Messages</span></a>
          </li>
          <?php
          }
          ?>

        </ul>
    </div>
    <div class="body-overlay"></div>
</header>
<main class="page-body theme-bg">
<div class="modal left fade" id="learn-modal" tabindex="" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Learning Hub</h5>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">X</button>
      </div>
      <div class="modal-body">
        <div class="main-box">
                    <div class="row">
            <?php if ( TAOH_READS_ENABLE ) { ?>
            <div class="col-6">
              <div class="borders mr-2 mt-2">
                <div class="inline-box">
                  <a href="<?php echo $taoh_home_url."/learning/"; ?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="45pt" height="30pt" fill="currentColor">
                    <path d="M21 4H3a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1zM4 6h16v2H4V6zm0 4h10v2H4v-2zm0 4h16v2H4v-2z"/>
                  </svg></a>
                </div>
              </div>
              <div class="mod-title">Blog</div>
            </div>
            <?php } ?>
            <div class="col-6">
              <div class="borders mr-2 mt-2">
                <div class="inline-box">
                    <a href="<?php echo $taoh_home_url."/learning/flashcard/"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="45pt" height="30pt" viewBox="0 0 406.273 511.79">
                      <g id="Group_56" data-name="Group 56" transform="translate(-16213.347 -13784.855)">
                        <g id="Group_55" data-name="Group 55" transform="translate(16315.991 13950.23)">
                          <path id="Path_154" data-name="Path 154" d="M198.65-545.293v11.707H180.888v-5.652H160.7v3.23c0,3.068-.081,3.23-2.584,3.23-6.3,0-14.856,6.459-18.166,13.483-1.373,2.826-1.453,7.186-1.453,60.473V-401.5l2.422,4.764a22.245,22.245,0,0,0,5.894,7.428c7.024,5.329,3.31,5.167,93.9,4.925l81.869-.242,3.795-1.857a30,30,0,0,0,7.1-5.49c6.459-7.024,6.055-2.664,6.055-66.932,0-64.026.323-59.827-5.813-66.609-3.472-3.875-9.85-7.266-13.726-7.266-2.584,0-2.664-.161-2.664-3.23v-3.23H296.344v5.652H279.389V-557H198.65Zm49.493,1.776,17.52.484v9.446H212.376v-10.5H221.5C226.424-544.082,238.454-543.84,248.143-543.517Z" transform="translate(-138.5 557)"/>
                          <g id="Group_42" data-name="Group 42" transform="translate(4.253 210.797)">
                            <path id="Path_141" data-name="Path 141" d="M197.337-275.466c-11.959,5.943-21.79,31.7-18.562,49.01,1.467,8.217,4.109,11.886,10.638,14.894,3.3,1.541,4.769,1.687,13.94,1.687,8.364,0,11.005-.294,14.16-1.467,4.769-1.761,8.584-4.842,10.418-8.364,3.228-6.383,3.6-18.2,1.027-29.421-2.421-10.638-10.565-23.038-17.242-26.486C207.9-277.52,201.372-277.52,197.337-275.466Zm13.426,14.16c5.209,5.649,8.291,17.755,6.823,26.779-1.174,7.41-4.989,10.492-13.06,10.492-10.418,0-14.013-4.989-13.06-17.755,1.027-13.5,7.63-24.285,14.087-23.184C206.875-264.681,208.929-263.287,210.763-261.306Z" transform="translate(-141.937 277.026)"/>
                            <path id="Path_144" data-name="Path 144" d="M343.913-275.908a18.819,18.819,0,0,0-9.538,8.217c-1.174,2.2-1.541,4.182-1.541,8.8,0,5.209.22,6.236,2.054,9.024,2.495,3.815,6.677,6.456,15.407,9.978,7.777,3.155,10.418,4.842,11.886,7.777,1.027,1.834,1.027,2.421.073,4.622-1.761,4.255-7.043,6.09-13.573,4.769-5.5-1.1-10.272-5.8-11.886-11.666-.587-2.2-1.394-4.035-1.761-4.035-1.687,0-4.035,6.53-4.035,11.005.073,11.665,9.1,18.562,23.331,17.828,7.41-.44,11.886-2.128,15.7-5.943,7.263-7.263,7.63-20.543.734-27.073-3.6-3.448-6.456-4.989-15.04-8.291-8.878-3.375-11.079-5.062-11.079-8.658,0-3.155,1.834-5.356,5.136-6.31,3.375-.954,6.53.073,11.739,3.6a33.793,33.793,0,0,0,4.842,2.935c.807,0,4.329-5.723,4.989-8.144.807-2.861-.587-4.475-6.016-6.9-3.815-1.761-5.8-2.2-11.592-2.348C348.975-276.862,345.894-276.642,343.913-275.908Z" transform="translate(-182.643 276.954)"/>
                            <path id="Path_142" data-name="Path 142" d="M139.509-274.9c-.22.587.073,4.182.734,7.924,2.641,15.628,3.375,41.526,1.247,44.094-1.541,1.908-3.522,1.394-5.209-1.394-.88-1.394-1.981-2.568-2.495-2.568-1.247,0-4.916,8.144-4.989,10.932-.073,2.715.44,3.522,3.815,5.209,3.6,1.834,10.345,1.908,13.94.147,6.823-3.375,8.8-11.812,8.8-37.931,0-16.655-.88-25.9-2.421-26.926-.44-.294-3.6-.587-6.97-.587C141.27-276,139.8-275.78,139.509-274.9Z" transform="translate(-128.791 276.752)"/>
                            <path id="Path_143" data-name="Path 143" d="M264.506-274.679c-1.614,1.834-2.568,9.685-3.155,26.119-.514,13.573.514,33.162,1.908,36.758l.734,1.981,15.994-.293c13.353-.22,16.288-.44,18.269-1.541,5.429-2.935,8.291-10.418,6.823-17.975-.954-5.209-2.348-7.63-7.41-13.06-2.348-2.568-4.329-4.989-4.329-5.429s1.247-2.348,2.861-4.255c4.549-5.5,5.429-7.777,5.062-13.206-.293-4.182-.587-4.989-2.641-6.823-3.375-3.008-6.53-3.6-20.4-3.6C267-276,265.533-275.853,264.506-274.679Zm23.551,10.125c2.641,1.834,2.128,4.7-1.687,8.951-3.668,4.109-9.464,8.217-11.445,8.217-2.054,0-3.081-2.715-2.568-6.97.66-5.723,1.834-8.437,4.4-9.978C279.62-266.1,285.709-266.169,288.057-264.554Zm.514,26.046c7.19,8,6.016,15.627-2.715,17.462-7.63,1.687-11.812-1.027-12.693-8.291a19.223,19.223,0,0,1-.073-5.429c.587-1.761,8.8-6.676,11.152-6.75C285.2-241.517,286.957-240.27,288.571-238.509Z" transform="translate(-164.058 276.752)"/>
                          </g>
                        </g>
                        <g id="SVGRepo_iconCarrier" transform="translate(16216.991 13788.5)">
                          <path id="Path_163" data-name="Path 163" d="M537.551,94h-138.2v29.7h138.2a4.922,4.922,0,0,1,4.859,4.94V563.859a4.922,4.922,0,0,1-4.859,4.94H206.773a4.922,4.922,0,0,1-4.859-4.94V128.641a4.922,4.922,0,0,1,4.859-4.94h55.585l5.4-29.7h-60.99c-18.785,0-34.073,15.543-34.073,34.641V563.859c0,19.1,15.289,34.641,34.073,34.641H537.61c18.785,0,34.073-15.543,34.073-34.641V128.641C571.624,109.543,556.335,94,537.551,94Z" transform="translate(-172.7 -94)" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="7.29"/>
                        </g>
                        <path id="Path_164" data-name="Path 164" d="M16486,13130v111l39.713-34.587,39.365,34.587v-111Z" transform="translate(-180.149 666.5)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="23"/>
                      </g>
                      </svg></a>
                </div>
              </div>
              <div class="mod-title">Flashcard</div>
            </div>
          </div>
          <div class="row mt-1">
            <div class="col-6">
              <div class="borders mr-2 mt-2">
                <div class="inline-box">
                  <a href="<?php echo $taoh_home_url."/learning/askobviousbaba"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="45pt" height="30pt" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                  </svg></a>
                </div>
              </div>
              <div class="mod-title">Obvious Baba</div>
            </div>
            <div class="col-6">
              <div class="borders mr-2 mt-2">
                <div class="inline-box">
                  <a href="<?php echo $taoh_home_url."/learning/jusask"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="45pt" height="30pt" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/>
                  </svg></a>
                </div>
              </div>
              <div class="mod-title">Sidekick</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Navbar -->
<!-- Navbar -->


<style>
.notifier {
  position: relative;
  display: inline-block;
}

.bell {
  font-size: 26px;
  color: #FFF;
  transition: 0.3s;
}

.bell:hover {
  color: #EF476F;
}
.stay_open{
  display:block !important;
}
.notification-dropdown .dropdown--menu::before {
  right:18%;
}
.no-result{
  color :red;
  font-size:10px;
  margin-left:25px;
}

.badges {
  position: absolute;
  top: -5px;
  left: 24px;
  padding: 0 5px;
  font-size: 16px;
  line-height: 22px;
  height: 22px;
  background: #EF476F;
  color: #FFF;
  border-radius: 11px;
  white-space: nowrap;
}

.notifier.green .badges {
  background: #06D6A0;
}
.notifier.green .bell:hover {
  color: #06D6A0;
}

.notifier.new .badges {
  animation: pulse 2s ease-out;
  animation-iteration-count: infinite;
}

.notify_time{
  font-size: 11px;
 /* position: relative;
  left: 145px;*/
}
.bold{
  color: #000;
  font-weight: bold;
  background: #ccc;
}
.bold .bgimage{
  color: #000;
  font-weight: bold;
  background: #ccc;
}
.bgimage{
  background:#fff;vertical-align:middle;
  /*margin-right:1em;*/
}
.cursor-pointer{
  cursor:pointer;
}
@keyframes pulse {
  40% {
    transform: scale3d(1, 1, 1);
  }


  50% {
    transform: scale3d(1.3, 1.3, 1.3);
  }

  55% {
    transform: scale3d(1, 1, 1);
  }
  
  60% {
    transform: scale3d(1.3, 1.3, 1.3);
  }

  65% {
    transform: scale3d(1, 1, 1);
  }
}
</style>

<script>
function openNav() {
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
}

$(document).on('click', '.support-page', function () {
  var url = '<?php echo $taoh_home_url."/support/".$app_name.$detail_name; ?>';
  window.location = url;
   
});
$(document).on('click', '.feedback-page', function () {
  var url = '<?php echo $taoh_home_url."/feedback/".$app_name.$detail_name; ?>';
  window.location = url;
   
});
window.onscroll = function() {myFunction()};

var header = document.getElementById("myHeader");
var sticky = header.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("fixed-header");
  } else {
    header.classList.remove("fixed-header");
  }
}
var $window = $(window);

$window.on('load', function () {
/*=========== Main menu open ============*/
var mainMenuToggle = $('.off-canvas-menu-toggle');
  mainMenuToggle.on('click', function () {
      $('.off-canvas-menu, .body-overlay').addClass('active');
      bodyEl.css({'overflow': 'hidden'});
  });

  /*=========== Main menu close ============*/
  var mainMenuClose = $('.off-canvas-menu-close, .body-overlay');
  mainMenuClose.on('click', function () {
      $('.off-canvas-menu, .body-overlay').removeClass('active');
      bodyEl.css({'overflow': 'inherit'});
  });

  /*=========== User menu open ============*/
  var userMenuToggle = $('.user-off-canvas-menu-toggle');
  userMenuToggle.on('click', function () {
      $('.user-off-canvas-menu, .body-overlay').addClass('active');
      bodyEl.css({'overflow': 'hidden'});
  });

  /*=========== User menu close ============*/
  var userMenuClose = $('.user-off-canvas-menu-close, .body-overlay');
  userMenuClose.on('click', function () {
      $('.user-off-canvas-menu, .body-overlay').removeClass('active');
      bodyEl.css({'overflow': 'inherit'});
  });

  /*=========== Sub menu ============*/
  var dropdowmMenu = $('.off-canvas-menu-list .sub-menu');
  dropdowmMenu.parent('li').children('a').append(function() {
      return '<button class="sub-nav-toggler" type="button"><i class="la la-angle-down"></i></button>';
  });

   /*=========== Sub menu ============*/
   var dropdowmMenu = $('.off-canvas-menu-list .sub-menu');
    dropdowmMenu.parent('li').children('a').append(function() {
        return '<button class="sub-nav-toggler" type="button"><i class="la la-angle-down"></i></button>';
    });

});

</script>
