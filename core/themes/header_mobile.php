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
                                <a class="dropdown-item" href="<?php echo $taoh_home_url.'/message'; ?>"><i class="la la-sms mr-2"></i>Messages</a>
                                <a class="dropdown-item" href="<?php echo  $taoh_home_url."/referral"; ?>"><i class="la la-user-plus mr-2"></i>Referral</a>
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
                                  <a onclick="localStorage.removeItem('isCodeSent')" class="dropdown-item" href="<?php echo  $taoh_home_url."/fwd/login"; ?>"><i class="la la-power-off mr-2"></i>Log In</a>
                          </div>
                      </div>
                  </li>
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
                            <a onclick="localStorage.removeItem('isCodeSent')" class="dropdown-item" href="<?php echo  $taoh_home_url."/fwd/login"; ?>"><i class="la la-power-off mr-2"></i>Log In</a>
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
      </div>
      <div class="modal-body">
        <div class="main-box">
          <div class="learn-title"><h3>Jobs</h3></div>  
          <div class="row">
            <div class="col-6">
              <div class="borders mr-2 mt-2">  
                <div class="inline-box">
                  <a href="<?php echo  $taoh_home_url."/learning/job"; ?>"><svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="45pt" height="30pt" viewBox="0 0 512.000000 561.000000" preserveAspectRatio="xMidYMid meet">

                  <g transform="translate(0.000000,561.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                  <path d="M920 5595 c-336 -68 -587 -329 -639 -665 -9 -56 -11 -550 -9 -1925 3 -2021 -1 -1859 58 -2017 66 -177 261 -372 438 -438 149 -56 136 -55 713 -55 l535 0 41 27 c62 41 88 90 88 168 0 78 -26 127 -88 168 l-40 27 -521 5 -521 5 -67 32 c-93 44 -151 100 -196 191 l-37 76 0 1855 0 1856 32 67 c44 93 100 151 191 196 l76 37 1325 0 1326 0 67 -32 c76 -36 155 -108 187 -171 52 -102 51 -82 51 -1151 l0 -995 21 -44 c41 -84 143 -129 232 -102 59 17 94 47 122 104 l25 50 0 1000 c0 653 -4 1024 -11 1066 -26 164 -96 305 -213 428 -120 127 -261 204 -434 238 -105 20 -2652 19 -2752 -1z"/>
                  <path d="M1179 4387 c-135 -71 -145 -254 -20 -342 l43 -30 1071 -3 c790 -2 1083 0 1114 9 84 23 152 124 140 210 -8 58 -54 128 -102 153 -40 21 -46 21 -1121 24 l-1080 2 -45 -23z"/>
                  <path d="M1179 3587 c-135 -71 -145 -254 -20 -342 l43 -30 1071 -3 c790 -2 1083 0 1114 9 84 23 152 124 140 210 -8 58 -54 128 -102 153 -40 21 -46 21 -1121 24 l-1080 2 -45 -23z"/>
                  <path d="M1180 2787 c-136 -71 -147 -253 -21 -342 l43 -30 591 0 592 0 38 24 c21 13 50 42 65 64 23 34 27 52 27 107 0 55 -4 73 -27 107 -15 22 -44 51 -65 64 l-38 24 -580 3 -581 3 -44 -24z"/>
                  <path d="M3260 2275 l0 -125 -94 0 -95 0 -3 28 -3 27 -107 3 -108 3 0 -36 c0 -31 -3 -35 -25 -35 -40 0 -97 -28 -138 -68 -77 -74 -72 -26 -75 -719 l-3 -622 25 -48 c29 -58 68 -96 121 -120 37 -17 98 -18 938 -21 l898 -3 43 25 c59 34 113 97 126 145 7 28 10 236 8 659 l-3 618 -28 43 c-33 49 -106 99 -159 107 -33 6 -37 10 -40 38 l-3 31 -112 3 -113 3 0 -31 0 -30 -90 0 -90 0 0 125 0 125 -435 0 -435 0 0 -125z m548 -22 l172 -6 0 -49 0 -48 -285 0 -285 0 0 55 0 55 113 0 c61 0 190 -3 285 -7z"/>
                  </g>
                  </svg></a>
                </div>
              </div>
              <div class="mod-title">Reads</div>
            </div>
            <div class="col-6">
              <div class="borders mr-2 mt-2">  
                <div class="inline-box">
                    <a href="<?php echo  $taoh_home_url."/learning/flashcard/networking/"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="45pt" height="30pt" viewBox="0 0 406.273 511.79">
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
                      </svg>
                    </a>
                </div>
              </div>
              <div class="mod-title">Cards</div>
            </div>
          </div>
          <div class="learn-title"><h3>Work</h3></div>
          <div class="row">
            <div class="col-6">
              <div class="borders mr-2 mt-2">  
                <div class="inline-box">
                  <a href="<?php echo  $taoh_home_url."/learning/work"; ?>"><svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="45pt" height="30pt" viewBox="0 0 517.000000 561.000000" preserveAspectRatio="xMidYMid meet">

                  <g transform="translate(0.000000,561.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                  <path d="M920 5595 c-336 -68 -587 -329 -639 -665 -9 -56 -11 -550 -9 -1925 3 -2021 -1 -1859 58 -2017 66 -177 261 -372 438 -438 149 -56 136 -55 713 -55 l535 0 41 27 c62 41 88 90 88 168 0 78 -26 127 -88 168 l-40 27 -521 5 -521 5 -67 32 c-93 44 -151 100 -196 191 l-37 76 0 1855 0 1856 32 67 c44 93 100 151 191 196 l76 37 1325 0 1326 0 67 -32 c76 -36 155 -108 187 -171 52 -102 51 -82 51 -1151 l0 -995 21 -44 c41 -84 143 -129 232 -102 59 17 94 47 122 104 l25 50 0 1000 c0 653 -4 1024 -11 1066 -26 164 -96 305 -213 428 -120 127 -261 204 -434 238 -105 20 -2652 19 -2752 -1z"/>
                  <path d="M1179 4387 c-135 -71 -145 -254 -20 -342 l43 -30 1071 -3 c790 -2 1083 0 1114 9 84 23 152 124 140 210 -8 58 -54 128 -102 153 -40 21 -46 21 -1121 24 l-1080 2 -45 -23z"/>
                  <path d="M1179 3587 c-135 -71 -145 -254 -20 -342 l43 -30 1071 -3 c790 -2 1083 0 1114 9 84 23 152 124 140 210 -8 58 -54 128 -102 153 -40 21 -46 21 -1121 24 l-1080 2 -45 -23z"/>
                  <path d="M1180 2787 c-136 -71 -147 -253 -21 -342 l43 -30 591 0 592 0 38 24 c21 13 50 42 65 64 23 34 27 52 27 107 0 55 -4 73 -27 107 -15 22 -44 51 -65 64 l-38 24 -580 3 -581 3 -44 -24z"/>
                  <path d="M3282 2355 c-73 -21 -108 -40 -165 -93 -83 -78 -117 -165 -126 -321 -6 -103 -6 -105 -36 -121 -29 -16 -30 -20 -29 -81 1 -79 33 -172 74 -214 16 -17 30 -39 30 -49 0 -43 34 -146 66 -198 28 -46 34 -66 34 -110 0 -84 -24 -149 -76 -207 -60 -67 -131 -99 -269 -123 -60 -10 -130 -25 -154 -33 -102 -34 -178 -101 -235 -204 -24 -46 -26 -52 -11 -58 21 -8 2764 3 2772 11 10 10 -60 117 -109 166 -62 63 -120 90 -236 107 -251 38 -356 105 -398 251 -22 76 -15 139 25 200 19 30 40 85 53 138 16 65 30 96 50 117 17 17 37 58 53 108 37 119 32 179 -16 179 -27 0 -30 12 -39 140 -6 101 -11 127 -38 183 -70 151 -209 229 -401 226 -61 -1 -57 -2 47 -14 126 -15 188 -39 259 -100 76 -65 123 -193 123 -337 0 -73 13 -106 42 -110 22 -3 23 -7 22 -73 0 -77 -37 -176 -75 -206 -14 -10 -26 -39 -35 -84 -17 -80 -42 -141 -80 -190 -25 -34 -26 -40 -20 -108 8 -89 34 -144 96 -207 61 -60 117 -87 222 -107 209 -39 226 -44 285 -82 44 -28 72 -57 106 -109 26 -39 47 -74 47 -77 0 -3 -68 -5 -150 -5 l-150 0 -43 68 c-85 132 -166 181 -339 206 -163 24 -235 54 -305 127 -50 53 -73 113 -73 195 0 60 4 77 28 115 37 58 49 91 66 172 11 50 21 73 40 88 27 21 57 93 75 180 14 65 3 109 -28 109 -31 0 -38 20 -45 140 -5 103 -9 121 -40 185 -38 78 -72 119 -134 160 -46 30 -51 41 -24 48 9 3 -94 5 -229 6 -135 0 -293 3 -350 6 -75 4 -119 1 -157 -10z m271 -14 c4 -4 -17 -27 -48 -50 -101 -77 -140 -161 -158 -349 -11 -110 -13 -117 -35 -120 -36 -5 -45 -35 -32 -109 15 -91 47 -171 76 -189 15 -10 26 -29 29 -52 12 -74 45 -168 72 -207 24 -35 27 -49 26 -111 -2 -122 -56 -209 -164 -263 -59 -29 -82 -36 -249 -66 -151 -28 -240 -88 -309 -208 l-32 -57 -82 0 c-45 0 -119 3 -164 7 l-82 6 14 27 c8 15 34 52 59 83 64 78 135 111 296 137 175 29 252 67 323 162 53 71 75 222 38 269 -34 44 -61 107 -79 186 -13 54 -26 86 -44 104 -50 50 -97 231 -67 256 8 6 25 13 39 15 22 3 25 7 23 43 -4 69 17 213 38 270 40 108 152 198 281 225 48 10 219 3 231 -9z"/>
                  </g>
                  </svg></a>
                </div>
              </div>
              <div class="mod-title">Reads</div>
            </div>
            <div class="col-6">
              <div class="borders mr-2 mt-2">  
                <div class="inline-box">
                  <a href="<?php echo $taoh_home_url."/learning/flashcard/career-development/"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="45pt" height="30pt" viewBox="0 0 406.273 511.79">
                    <g id="Group_59" data-name="Group 59" transform="translate(-16379.347 -14880.605)">
                      <path id="Path_165" data-name="Path 165" d="M210.308-542.285a33.555,33.555,0,0,0-16.526,11.657c-7.282,9.067-10.83,22.373-10.83,40.153,0,9.773-.093,10.009-2.241,10.009a4.6,4.6,0,0,0-3.361,1.766c-3.081,4.71,2.521,31.439,7,33.559,1.681.824,2.147,2.237,2.708,7.536.747,8.125,3.361,17.192,6.536,22.49,2.054,3.415,2.427,5.181,2.427,12.011,0,20.135-11.951,34.148-31.37,36.974-19.7,2.708-29.13,10.127-36.692,28.967L126-332.1H254.376c70.584,0,128.376-.353,128.376-.824,0-2.355-7.842-17.074-11.11-20.724-6.909-7.889-12.7-10.48-26.982-12.482-19.607-2.708-31.931-16.956-31.931-36.974,0-6.594.373-8.714,2.241-11.775,3.641-6.241,5.229-11.186,6.722-20.489.747-4.828,1.307-8.949,1.307-9.185a5.19,5.19,0,0,1,1.774-.942c3.361-1.06,9.15-22.49,8.029-29.791-.56-3.768-1.961-5.181-5.135-5.181-1.494,0-1.681-1.531-2.054-14.483-.28-13.07-.653-15.072-3.268-22.137-7.283-19.547-20.634-28.26-40.427-26.376a90.847,90.847,0,0,1-13.071.471c-5.042-.353-19.793-.353-27.542.118-3.081.118-10.177,0-15.872-.471C217.777-543.815,213.949-543.58,210.308-542.285Zm24.648,1.531c.84.706-.187,2.12-3.641,5.063-10.083,8.6-14.752,21.2-15.5,41.919-.373,12.835-.467,13.306-2.521,13.306a4.574,4.574,0,0,0-3.268,1.766c-3.081,4.71,2.521,31.439,7,33.559,1.681.824,2.147,2.237,2.708,7.536.747,8.125,3.361,17.192,6.536,22.49,2.054,3.415,5.572-8.711,5.572-1.882,0,20.135-15.1,48.04-34.515,50.866-19.42,2.708-29.13,10.127-36.225,27.789l-1.587,3.886H144.206c-8.5,0-15.405-.471-15.405-.942,0-1.884,7.936-15.308,10.737-18.134,6.255-6.594,11.671-8.831,28.383-11.422a34.245,34.245,0,0,0,21.38-12.717c5.322-6.712,7.656-13.777,8.309-24.61.467-7.772.373-9.067-1.4-11.422-3.454-4.592-6.722-14.483-7.843-23.55-.934-7.536-1.4-9.067-2.988-9.3-2.334-.471-6.909-14.366-7.749-23.668-.654-6.594.84-9.067,5.135-9.067,2.147,0,2.241-.236,1.587-5.652a98.432,98.432,0,0,1,.373-15.308c2.334-23.786,12.044-37.209,29.97-41.448C219.271-542.873,232.9-542.167,234.956-540.754Zm65.449.824c6.069,2.355,12.884,8.243,16.619,14.366,5.135,8.478,7.1,16.838,7.656,32.381.467,13.306.56,13.895,2.428,13.895a4.443,4.443,0,0,1,3.174,1.413c3.455,4.357-2.334,30.262-7.1,31.793-1.4.471-1.867,2.12-2.427,7.3-.747,8.714-3.548,17.663-7.189,23.432-2.894,4.475-2.988,4.946-2.427,13.07.654,10.6,2.427,15.9,7.936,23.315,6.442,8.714,13.164,12.482,25.675,14.6,12.418,2,15.592,3.179,21.287,7.654,5.135,4.121,7.1,6.712,11.484,15.425l3.455,6.83H352.409l-3.268-7.183c-5.228-11.422-12.791-19.076-21.941-22.019a98.646,98.646,0,0,0-12.417-2.473c-19.607-2.708-31.931-16.956-31.931-36.974,0-6.594.373-8.714,2.241-11.775,3.641-6.241,5.228-11.186,6.722-20.489.747-4.828,1.307-8.949,1.307-9.185a5.191,5.191,0,0,1,1.774-.942c3.361-1.06,9.15-22.49,8.029-29.791-.56-3.768-1.961-5.181-5.135-5.181-1.494,0-1.681-1.531-2.054-14.483-.28-13.07-.654-15.072-3.174-22.019-3.455-9.3-7.656-15.543-13.071-19.547-2.241-1.648-4.108-3.532-4.108-4C275.383-543.227,292.656-542.756,300.4-539.93Z" transform="translate(16327.991 15573.763)"/>
                      <g id="Group_58" data-name="Group 58" transform="translate(16489.992 15269.036)">
                        <path id="Path_138" data-name="Path 138" d="M232.095-273.737c-4.023,1.823-8.549,7.637-11.494,14.754-3.879,9.286-5.1,15.882-5.1,27.425,0,9.026.144,10.328,1.8,13.886a14.442,14.442,0,0,0,10.632,8.766c4.67,1.128,14.08.694,18.606-.955,7.328-2.69,10.991-10.241,10.991-22.565C257.526-258.376,244.667-279.292,232.095-273.737ZM241-259.591c6.9,8.418,8.549,28.64,2.8,34.455-4.095,4.166-12.213,3.732-15.733-.868-1.509-1.822-1.8-3.3-2.083-8.766-.431-11.456,2.8-23,7.471-26.47C236.549-263.5,237.986-263.236,241-259.591Z" transform="translate(-159.145 274.638)"/>
                        <path id="Path_139" data-name="Path 139" d="M286.961-272.215c-2.011,3.841-2.227,59.877-.287,62.67.5.786,2.586,1.135,6.394,1.135,6.466,0,6.25.436,4.6-8.728-1.509-8.292-1.365-10.823.79-13.878,1.293-1.92,2.514-2.706,3.951-2.706,2.874,0,5.747,5.586,7.759,15.1,2.155,10.125,1.94,9.776,7.687,9.776h4.957l-.072-4.8c-.144-6.546-2.443-13.267-6.968-20.512l-3.879-6.2,3.879-5.586c4.167-6.11,5.6-10.649,5.029-15.973-.5-4.19-2.083-7.245-5.029-9.339C313.685-272.826,312.1-273,300.394-273,293.211-273,287.176-272.651,286.961-272.215Zm20.618,9.776c2.083,1.4,2.658,2.444,2.658,5.5,0,3.229-3.879,7.856-9.555,11.434-5.172,3.317-5.819,2.531-5.172-5.935.575-9.252,2.227-11.871,7.4-11.958A11.017,11.017,0,0,1,307.578-262.439Z" transform="translate(-179.858 274.177)"/>
                        <path id="Path_140" data-name="Path 140" d="M348.227-269.77C347.293-265.58,346-243.057,346-231.1c.072,11.436,1.006,20.952,2.3,21.912,1.149.96,9.267.96,10.417.087.718-.524.718-2.008-.144-6.809-1.94-11.087-.934-16.412,3.664-20.079,1.868-1.484,1.94-1.484,3.233.349,2.155,3.23,4.382,11.611,4.741,17.634.575,10.3.144,9.69,6.537,9.428l5.532-.262-.144-5.5c-.215-9.428-3.017-17.983-9.052-27.848-3.161-5.151-3.3-4.19,2.155-14.4,3.233-6.024,6.178-14.491,5.532-15.714-.216-.436-2.371-.7-4.885-.7h-4.526l-.862,3.666c-2.011,8.293-9.914,23.483-12.5,24.094-1.006.262-1.221-.436-1.221-4.365a97.567,97.567,0,0,1,1.437-12.833c.79-4.54,1.437-8.73,1.437-9.428,0-.873-1.365-1.135-5.388-1.135h-5.316Z" transform="translate(-197.861 274.177)"/>
                        <path id="Path_137" data-name="Path 137" d="M136.151-272.129c-.934,1.916-1.221,15.848-.431,25.514.79,9.84,3.448,22.031,6.394,28.91,2.227,5.4,5.244,10.014,6.609,10.014,1.437,0,4.957-6.008,8.549-14.629,1.293-3.048,2.586-5.225,3.017-4.876a19.844,19.844,0,0,1,2.371,5.138c2.73,7.315,6.681,14.368,8.19,14.368,3.951,0,12.5-22.205,14.943-38.663,1.006-7.14,1.365-22.728.575-25.253-.359-1.132-1.509-1.393-5.46-1.393-4.382,0-4.957.174-4.6,1.48,2.227,9.23,2.155,21.6-.216,32.045s-4.957,12.626-7.256,6.27c-2.73-7.4-3.376-21.334-1.724-36.138l.431-3.657h-10.7l.359,11.581c.287,9.84.144,12.539-1.221,18.809-1.724,8.272-3.52,12.539-5.388,12.975-2.3.61-3.736-3.919-4.741-15.239-.575-6.618,0-20.551,1.149-25.95L147.43-273h-5.388C139.025-273,136.367-272.652,136.151-272.129Z" transform="translate(-135.262 274.177)"/>
                      </g>
                      <g id="Group_57" data-name="Group 57" transform="translate(165.991 1095.75)">
                        <g id="SVGRepo_iconCarrier" transform="translate(16217 13788.5)">
                          <path id="Path_163" data-name="Path 163" d="M537.551,94h-138.2v29.7h138.2a4.922,4.922,0,0,1,4.859,4.94V563.859a4.922,4.922,0,0,1-4.859,4.94H206.773a4.922,4.922,0,0,1-4.859-4.94V128.641a4.922,4.922,0,0,1,4.859-4.94h49.585l11.4-29.7h-60.99c-18.785,0-34.073,15.543-34.073,34.641V563.859c0,19.1,15.289,34.641,34.073,34.641H537.61c18.785,0,34.073-15.543,34.073-34.641V128.641C571.624,109.543,556.335,94,537.551,94Z" transform="translate(-172.7 -94)" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="7.29"/>
                        </g>
                        <path id="Path_164" data-name="Path 164" d="M16486,13130v109.917l39.713-34.251,39.365,34.251V13130Z" transform="translate(-180.149 666.5)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="23"/>
                      </g>
                    </g>
                  </svg></a>
                </div>
              </div>
              <div class="mod-title">Cards</div>
            </div>
          </div>
          <div class="learn-title"><h3>Wellness</h3></div>
          <div class="row">
            <div class="col-6">
              <div class="borders mr-2 mt-2">  
                <div class="inline-box">
                  <a href="<?php echo $taoh_home_url."/learning/wellness"; ?>"><svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="45pt" height="30pt" viewBox="0 0 512.000000 533.000000" preserveAspectRatio="xMidYMid meet">

                  <g transform="translate(0.000000,533.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                  <path d="M920 5315 c-336 -68 -587 -329 -639 -665 -9 -56 -11 -550 -9 -1925 3 -2021 -1 -1859 58 -2017 66 -177 261 -372 438 -438 149 -56 136 -55 713 -55 l535 0 41 27 c62 41 88 90 88 168 0 78 -26 127 -88 168 l-40 27 -521 5 -521 5 -67 32 c-93 44 -151 100 -196 191 l-37 76 0 1855 0 1856 32 67 c44 93 100 151 191 196 l76 37 1325 0 1326 0 67 -32 c76 -36 155 -108 187 -171 52 -102 51 -82 51 -1151 l0 -995 21 -44 c41 -84 143 -129 232 -102 59 17 94 47 122 104 l25 50 0 1000 c0 653 -4 1024 -11 1066 -26 164 -96 305 -213 428 -120 127 -261 204 -434 238 -105 20 -2652 19 -2752 -1z"/>
                  <path d="M1179 4107 c-135 -71 -145 -254 -20 -342 l43 -30 1071 -3 c790 -2 1083 0 1114 9 84 23 152 124 140 210 -8 58 -54 128 -102 153 -40 21 -46 21 -1121 24 l-1080 2 -45 -23z"/>
                  <path d="M1179 3307 c-135 -71 -145 -254 -20 -342 l43 -30 1071 -3 c790 -2 1083 0 1114 9 84 23 152 124 140 210 -8 58 -54 128 -102 153 -40 21 -46 21 -1121 24 l-1080 2 -45 -23z"/>
                  <path d="M1180 2507 c-136 -71 -147 -253 -21 -342 l43 -30 591 0 592 0 38 24 c21 13 50 42 65 64 23 34 27 52 27 107 0 55 -4 73 -27 107 -15 22 -44 51 -65 64 l-38 24 -580 3 -581 3 -44 -24z"/>
                  <path d="M3485 2215 c-170 -21 -271 -52 -419 -128 -280 -143 -483 -376 -590 -675 -25 -70 -59 -232 -51 -241 2 -2 25 9 50 23 88 52 151 68 290 73 126 5 133 4 224 -26 52 -18 125 -48 162 -67 l68 -36 48 33 c69 48 199 99 253 99 l45 0 -3 -342 c-1 -189 -6 -392 -11 -452 -8 -102 -10 -111 -35 -133 -18 -15 -40 -23 -66 -23 -75 0 -100 25 -114 111 -8 47 -37 73 -70 65 -32 -8 -41 -39 -32 -109 7 -64 46 -123 101 -151 54 -28 164 -29 219 -2 47 24 93 92 102 151 3 23 9 229 12 458 4 228 11 419 15 423 22 23 204 -44 290 -106 l43 -31 85 40 c235 110 485 107 666 -7 38 -24 42 -31 45 -72 l3 -45 18 38 c38 79 31 163 -19 223 l-24 29 6 -35 c2 -19 -7 3 -21 50 -69 225 -199 431 -363 573 -262 229 -593 333 -927 292z"/>
                  </g>
                  </svg></a>
                </div>
              </div> 
              <div class="mod-title">Reads</div>
            </div>
            <div class="col-6">
              <div class="borders mr-2 mt-2">  
                <div class="inline-box">
                  <a href="<?php echo $taoh_home_url."/learning/flashcard/mindfulness/"; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="45pt" height="30pt" viewBox="0 0 406.273 511.789">
                    <g id="Group_63" data-name="Group 63" transform="translate(-16085.354 -16644.105)">
                      <path id="Path_166" data-name="Path 166" d="M292.345-533.939c-45.62,10.585-80.208,41.595-95.414,85.724-4.473,13.12-6.411,24.9-3.727,23.555,26.388-13.269,48.005-13.418,73.648-.447l9.84,4.92,5.814-3.876c6.56-4.622,22.81-10.734,28.028-10.734h3.727v27.134c0,15.058-.447,36.973-1.044,48.751-1.044,24.6-2.087,26.984-12.076,26.984-7.752,0-10.734-2.684-11.778-10.138q-1.342-10.51-7.6-8.5c-3.876,1.193-4.771,5.218-2.982,12.97,3.28,14.76,19.679,21.916,33.693,14.76,11.032-5.665,11.33-6.709,12.374-61.721.6-26.835,1.342-49.347,1.938-49.794,2.534-2.684,28.028,7.454,33.1,13.12,2.385,2.684,2.684,2.534,8.647-.895,21.766-12.523,48.154-13.865,69.921-3.28,5.516,2.684,9.989,4.323,9.989,3.727,0-5.516-5.516-25.792-9.691-35.482-15.8-37.719-46.663-64.256-86.469-74.543C336.623-535.877,305.464-536.92,292.345-533.939Z" transform="translate(15970.257 17329.705)"/>
                      <g id="Group_62" data-name="Group 62" transform="translate(16148 17039.563)">
                        <path id="Path_146" data-name="Path 146" d="M149-328.4c-.776,1.314-1.015,10.867-.358,17.494.657,6.747,2.866,15.106,5.314,19.823,1.851,3.7,4.359,6.866,5.493,6.866,1.194,0,4.12-4.12,7.105-10.031,1.075-2.09,2.149-3.582,2.508-3.344a12.744,12.744,0,0,1,1.97,3.523c2.269,5.015,5.553,9.852,6.807,9.852,3.284,0,10.389-15.226,12.419-26.51.836-4.9,1.134-15.584.478-17.315-.3-.776-1.254-.955-4.538-.955-3.642,0-4.12.119-3.821,1.015,1.851,6.329,1.791,14.808-.179,21.972s-4.12,8.658-6.03,4.3c-2.269-5.075-2.806-14.628-1.433-24.779l.358-2.508h-8.9l.3,7.941c.239,6.747.119,8.6-1.015,12.9-1.433,5.672-2.926,8.6-4.478,8.9-1.911.418-3.1-2.687-3.941-10.449-.478-4.538,0-14.091.955-17.793l.358-1.493H153.9C151.389-329,149.18-328.761,149-328.4Z" transform="translate(-148.262 330.405)"/>
                        <path id="Path_147" data-name="Path 147" d="M233.247-327.925a39.406,39.406,0,0,0-1.612,6.448c-.836,4.18-1.075,8.478-1.134,19.942,0,8.061.179,15.106.418,15.643.418.955,1.612,1.075,13.136,1.075,6.986,0,13.076-.239,13.554-.537.716-.478.478-1.373-1.194-5.135a34.021,34.021,0,0,0-2.329-4.777c-.239-.119-1.911.537-3.881,1.552-4.9,2.448-7.643,2.388-10.21-.418-1.732-1.851-2.03-2.508-2.03-5.135s.239-3.224,2.09-5.075c2.03-2.03,2.269-2.09,5.732-1.911l3.642.239,1.493-4.06c.776-2.269,1.314-4.3,1.075-4.478-.776-.776-5.971-.179-9.314.955-3.881,1.373-4.717,1.015-4.717-2.15,0-6.03,6.329-8.956,12.6-5.851a8.914,8.914,0,0,0,2.627.955c.6,0,3.881-6.687,3.881-7.941,0-.239-5.254-.418-11.643-.418C234.859-329,233.784-328.881,233.247-327.925Z" transform="translate(-181.398 330.405)"/>
                        <path id="Path_148" data-name="Path 148" d="M289.876-327.985a75.146,75.146,0,0,0-1.851,9.135c-1.075,6.329-1.373,10.628-1.433,20-.06,6.568.119,12.419.358,12.957.418.955,1.552,1.075,12.419,1.075,9.792,0,12.061-.179,12.3-.836.3-.776-1.791-9.374-2.567-10.688-.119-.239-1.194.358-2.329,1.314-2.508,2.269-6.329,3.463-8.419,2.687a7.906,7.906,0,0,1-2.806-2.209c-2.567-3.463-1.254-17.733,2.687-28.9.955-2.687,1.732-5.015,1.732-5.195S297.817-329,295.19-329C291.309-329,290.354-328.821,289.876-327.985Z" transform="translate(-203.994 330.405)"/>
                        <path id="Path_149" data-name="Path 149" d="M341.581-327.806a57.16,57.16,0,0,0-1.851,8.18c-1.552,8.6-2.329,31.347-1.194,33.5.657,1.254,1.015,1.314,12.6,1.314,7.225,0,12-.239,12.24-.6.3-.478-1.134-6.747-2.388-10.628-.179-.537-1.134-.119-3.045,1.314-4.777,3.642-9.374,3.4-11.225-.537-2.388-4.956-.119-20.42,4.717-32.72.358-.9-.06-1.015-4.418-1.015C342.894-329,342.118-328.821,341.581-327.806Z" transform="translate(-224.711 330.405)"/>
                        <path id="Path_152" data-name="Path 152" d="M281.2-211.057c-6.21,1.015-9.971,5.374-9.971,11.643,0,6.389,2.269,8.956,11.464,12.9,8.717,3.762,10.688,6.508,7.105,10.091-1.552,1.493-2.209,1.732-5.135,1.732a13.466,13.466,0,0,1-5.015-.9,11.847,11.847,0,0,1-5.553-7.225c-.836-3.045-1.552-3.1-2.985-.179a13.813,13.813,0,0,0-.418,9.314c1.612,4.3,6.568,7.4,12.6,7.822,10.031.716,16.9-4.956,16.778-13.912-.06-4-.657-5.613-3.1-8.24-1.911-2.09-3.582-2.985-10.449-5.792-6.448-2.567-8.12-4.3-6.926-7.165,1.672-4.12,6.329-4.06,12.061.179a3.533,3.533,0,0,0,2.508.836c1.254-.478,3.642-5.433,3.165-6.628C296.122-209.624,287.345-212.072,281.2-211.057Z" transform="translate(-51.332 211.386)"/>
                        <path id="Path_153" data-name="Path 153" d="M341.866-211.182c-5.851,1.194-9.076,4.418-10.031,9.911-1.015,6.448,2.747,11.285,11.344,14.628,6.448,2.508,9.553,5.075,9.016,7.523-.716,2.866-2.926,4.359-6.747,4.359-5.672,0-9.016-2.508-10.628-7.941-.955-3.165-1.672-3.344-2.985-.717a14.986,14.986,0,0,0,0,10.688c3.762,7.941,18.868,9.314,25.5,2.329,5.015-5.254,4.538-14.808-.955-18.987a44.434,44.434,0,0,0-8-4,38.8,38.8,0,0,1-7.105-3.582c-3.045-2.806-.6-7.046,4-7.046,1.612,0,3.165.6,5.374,2.09a14.864,14.864,0,0,0,3.941,2.09c1.075,0,3.941-5.613,3.463-6.807C356.912-209.689,347.479-212.316,341.866-211.182Z" transform="translate(-75.82 211.451)"/>
                        <path id="Path_150" data-name="Path 150" d="M149.826-208.507c-.179.836-.478,2.209-.657,3.1-.6,2.985-.358,36.243.3,37.974.6,1.552.836,1.612,4.478,1.612,2.149,0,4-.179,4.12-.478a42.237,42.237,0,0,0-.836-6.687c-1.373-7.881-2.09-21.435-1.134-22.689,1.732-2.448,8.777,10.031,12.9,22.808l2.209,7.046h3.762c3.284,0,3.881-.179,4.538-1.373.6-1.015.776-5.971.776-20.42,0-10.509-.179-19.823-.478-20.778-.418-1.552-.6-1.612-4.359-1.612H171.5l.358,1.911c1.97,9.076,2.866,20.778,1.612,20.778-.3,0-1.672-2.09-3.045-4.657-3.344-6.03-8.658-14.33-10.628-16.42-1.373-1.433-1.911-1.612-5.613-1.612C150.363-210,150.125-209.94,149.826-208.507Z" transform="translate(-2.479 210.866)"/>
                        <path id="Path_151" data-name="Path 151" d="M220.048-208.806a31.628,31.628,0,0,0-1.552,5.553c-1.134,5.493-1.851,33.377-.9,35.765l.657,1.672h12.658c6.986,0,13.076-.239,13.554-.537.716-.478.537-1.314-1.134-4.956a43.1,43.1,0,0,0-2.388-4.836c-.239-.239-1.97.358-3.821,1.373-4.777,2.508-7.583,2.448-10.27-.239-1.732-1.732-2.03-2.448-2.03-4.836,0-5.374,2.926-7.941,8.3-7.225l2.926.358,1.194-3.1c2.09-5.732,2.09-5.672.478-6.09s-5.075.239-9.076,1.672c-2.985,1.015-3.821.478-3.821-2.448,0-6.15,6.329-9.076,12.6-5.851a8.413,8.413,0,0,0,2.269.9c.657,0,4.239-6.628,4.239-7.762,0-.358-4.06-.6-11.643-.6C221.242-210,220.645-209.94,220.048-208.806Z" transform="translate(-30.01 210.866)"/>
                      </g>
                      <g id="Group_61" data-name="Group 61" transform="translate(-294 1763.5)">
                        <g id="Group_57" data-name="Group 57" transform="translate(166 1095.75)">
                          <g id="SVGRepo_iconCarrier" transform="translate(16217 13788.5)">
                            <path id="Path_163" data-name="Path 163" d="M537.551,94h-138.2v29.7h138.2a4.922,4.922,0,0,1,4.859,4.94V563.859a4.922,4.922,0,0,1-4.859,4.94H206.773a4.922,4.922,0,0,1-4.859-4.94V128.641a4.922,4.922,0,0,1,4.859-4.94H262.7L267.763,94h-60.99c-18.785,0-34.073,15.543-34.073,34.641V563.859c0,19.1,15.289,34.641,34.073,34.641H537.61c18.785,0,34.073-15.543,34.073-34.641V128.641C571.624,109.543,556.335,94,537.551,94Z" transform="translate(-172.7 -94)" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="7.29"/>
                          </g>
                          <path id="Path_164" data-name="Path 164" d="M16486,13130v107.25l39.713-33.419,39.365,33.419V13130Z" transform="translate(-180.149 666.5)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="23"/>
                        </g>
                      </g>
                    </g>
                  </svg></a>
                </div>
              </div>
              <div class="mod-title">Cards</div>
            </div>
          </div>
          <div class="learn-title"><h3>Coach</h3></div>
          <div class="row">
            <div class="col-6">
              <div class="borders mr-2 mt-2">  
                <div class="inline-box">
                  <a href="<?php echo $taoh_home_url."/learning/askobviousbaba"; ?>"><svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                  width="45pt" height="30pt" viewBox="0 0 300.000000 300.000000"
                    preserveAspectRatio="xMidYMid meet">

                    <g transform="translate(0.000000,300.000000) scale(0.100000,-0.100000)"
                      fill="#000000" stroke="none">
                      <path d="M1434 2624 c-97 -14 -200 -48 -274 -91 -55 -32 -256 -231 -270 -268
                      -15 -39 -12 -131 5 -173 9 -20 29 -57 44 -82 16 -25 31 -57 35 -72 3 -15 20
                      -43 36 -62 17 -18 30 -43 30 -55 0 -22 -14 -28 -25 -11 -8 13 -38 13 -69 -1
                      -37 -17 -60 -68 -54 -122 5 -42 2 -50 -33 -92 -41 -50 -79 -138 -79 -185 0
                      -17 8 -34 19 -42 10 -7 25 -38 34 -68 15 -53 56 -108 70 -94 3 3 15 -14 27
                      -38 38 -74 69 -110 95 -106 15 2 31 -8 50 -30 32 -39 78 -64 99 -56 8 3 21 2
                      27 -2 9 -5 1 -12 -22 -19 -18 -6 -39 -22 -46 -35 -6 -13 -24 -30 -40 -38 -47
                      -24 -74 -54 -105 -119 -26 -55 -32 -62 -48 -53 -29 16 -46 12 -132 -25 l-80
                      -36 -45 27 c-44 25 -47 26 -105 13 -35 -8 -63 -20 -66 -29 -3 -8 -16 -25 -29
                      -37 -20 -19 -23 -29 -17 -60 8 -44 20 -63 41 -63 11 0 8 -9 -11 -30 -44 -53
                      -11 -84 73 -71 28 4 53 10 54 12 2 2 14 20 27 39 39 57 119 130 143 130 16 0
                      18 -3 9 -12 -7 -7 -12 -40 -12 -78 0 -56 4 -70 22 -87 68 -62 177 -88 348 -83
                      124 3 213 2 330 -3 38 -2 45 1 44 16 -2 16 4 17 42 11 23 -3 128 -7 231 -7
                      196 -2 267 9 353 51 51 25 71 85 49 147 -14 38 -14 45 -2 45 16 0 91 -73 140
                      -136 29 -36 39 -42 85 -47 40 -5 56 -3 70 10 25 22 23 40 -7 67 l-25 24 24 6
                      c41 10 49 84 12 120 -12 13 -25 30 -28 38 -3 7 -33 19 -66 27 -58 13 -61 12
                      -105 -13 l-45 -27 -76 36 c-75 35 -119 44 -131 25 -12 -19 -22 -9 -52 53 -32
                      65 -59 95 -106 119 -16 8 -34 25 -40 38 -7 14 -27 28 -45 34 -44 12 -40 26 7
                      26 32 0 44 7 78 42 26 29 45 42 60 40 24 -4 65 41 96 105 13 27 28 43 39 43
                      20 0 53 58 63 112 5 24 14 38 26 41 46 12 10 154 -61 238 -23 27 -36 50 -30
                      54 15 9 12 82 -5 115 -8 16 -24 34 -34 40 -25 13 -68 13 -76 0 -11 -18 -23
                      -10 -30 21 -5 23 -2 36 14 53 20 21 56 106 45 106 -3 0 4 12 15 26 43 55 71
                      210 51 284 -15 55 -107 147 -187 187 -34 17 -77 45 -95 62 -18 17 -59 44 -90
                      60 -66 34 -115 36 -264 15z m236 -564 c109 -28 167 -81 232 -217 24 -48 44
                      -90 45 -91 1 -2 15 6 31 18 37 28 65 16 82 -35 16 -49 7 -69 -26 -61 -15 4
                      -62 9 -105 12 -59 4 -86 1 -109 -11 -16 -8 -30 -17 -30 -18 0 -2 36 -3 80 -2
                      68 0 82 -3 100 -21 l21 -21 -52 -22 c-49 -20 -53 -20 -71 -5 -13 13 -39 17
                      -96 18 -66 1 -81 5 -100 24 -12 12 -28 22 -34 22 -20 0 -81 101 -80 131 0 15
                      8 42 17 59 13 26 21 31 38 25 12 -4 54 -13 92 -21 39 -8 80 -18 93 -24 13 -6
                      28 -7 34 -3 14 8 -11 18 -122 49 -116 33 -134 32 -160 -11 -12 -19 -24 -50
                      -27 -68 -4 -30 1 -42 35 -86 22 -28 44 -60 47 -72 4 -13 12 -18 22 -15 20 8
                      43 -11 43 -36 0 -15 -5 -18 -21 -13 -16 5 -31 -2 -63 -33 -78 -76 -102 -76
                      -177 -6 -34 31 -52 41 -68 37 -14 -3 -23 0 -27 11 -9 23 16 48 42 43 17 -3 24
                      2 28 18 3 12 24 42 46 67 22 26 40 56 40 69 0 35 -39 107 -62 115 -11 3 -70
                      -7 -132 -22 -121 -31 -122 -31 -114 -40 4 -3 57 6 120 20 l113 26 17 -23 c17
                      -22 23 -44 19 -81 -2 -27 -61 -115 -81 -121 -9 -3 -27 -16 -40 -30 -19 -20
                      -26 -22 -48 -14 -29 11 -106 4 -161 -16 -31 -10 -40 -9 -72 8 -38 20 -42 39
                      -11 52 38 16 96 22 129 13 46 -13 53 -4 13 17 -23 12 -46 15 -79 11 -160 -20
                      -172 -16 -151 49 17 50 45 63 81 36 35 -26 36 -26 43 3 3 14 12 31 19 38 7 7
                      21 33 32 58 47 110 82 141 196 174 117 34 273 41 369 16z m-670 -395 c0 -3
                      -11 -28 -25 -55 -31 -61 -31 -64 1 -44 18 12 28 14 36 6 6 -6 16 -8 23 -6 25
                      10 25 -15 0 -34 -21 -17 -22 -21 -9 -26 9 -3 31 -6 50 -6 29 0 33 -3 31 -22
                      -2 -19 4 -24 31 -26 23 -2 32 -8 32 -21 0 -25 19 -39 60 -43 26 -2 34 -7 32
                      -19 -4 -24 23 -41 65 -41 21 0 48 -8 61 -19 22 -17 26 -17 63 -3 35 15 43 15
                      78 0 37 -14 41 -14 64 4 15 12 41 20 64 20 42 0 63 14 63 42 0 14 8 18 32 18
                      35 0 69 25 62 45 -4 9 5 15 28 17 25 2 34 8 36 26 3 18 10 22 42 22 45 0 59
                      14 30 30 -25 13 -28 45 -3 36 9 -4 20 -1 24 5 5 9 14 8 33 -5 31 -20 33 -8 6
                      44 -24 47 -24 50 -5 50 45 0 165 -171 165 -236 0 -28 -15 -31 -29 -6 -8 13
                      -11 4 -11 -38 -2 -60 -20 -122 -38 -128 -7 -2 -12 6 -12 17 0 36 -17 23 -30
                      -23 -14 -52 -66 -146 -80 -146 -6 0 -10 12 -10 27 0 27 -1 27 -26 -12 -57 -85
                      -124 -140 -124 -100 0 8 -2 15 -6 15 -3 0 -33 -29 -67 -65 -34 -36 -70 -65
                      -80 -65 -10 0 -15 5 -12 10 13 21 -8 8 -66 -40 -79 -66 -100 -66 -174 2 -41
                      37 -59 48 -65 39 -13 -22 -48 -2 -102 60 -54 61 -68 70 -68 44 0 -8 -4 -15 -9
                      -15 -18 0 -71 52 -103 102 l-33 51 -5 -29 -5 -29 -26 35 c-14 19 -32 53 -41
                      75 -9 22 -21 51 -26 65 l-11 25 -7 -22 c-3 -13 -10 -23 -14 -23 -14 0 -32 57
                      -38 120 -4 48 -7 59 -13 42 -14 -36 -32 -27 -26 13 9 53 51 135 90 176 50 52
                      97 83 97 64z m305 -106 c12 -10 36 -24 55 -31 19 -6 51 -27 70 -45 20 -18 46
                      -33 59 -33 24 0 91 38 91 52 0 4 21 16 47 28 26 11 55 28 65 37 36 32 239 -3
                      242 -42 1 -5 -24 -12 -54 -15 -36 -3 -49 -8 -37 -12 9 -4 17 -15 17 -24 0 -14
                      -7 -15 -45 -10 -42 7 -60 -3 -35 -19 6 -3 10 -13 10 -21 0 -10 -12 -14 -44
                      -14 -24 0 -52 -3 -61 -6 -14 -6 -14 -8 1 -25 10 -11 14 -22 11 -25 -9 -10 -57
                      -9 -89 0 -24 7 -28 5 -28 -13 0 -26 -30 -28 -65 -5 -22 14 -26 14 -47 0 -33
                      -23 -68 -21 -68 5 0 15 -5 19 -17 15 -55 -16 -74 -17 -88 -6 -14 12 -14 15 0
                      30 14 16 12 18 -36 23 -66 8 -79 20 -53 48 l19 22 -47 -7 c-51 -8 -67 3 -42
                      28 14 15 11 16 -35 16 -61 0 -69 19 -17 44 81 38 182 41 221 5z m528 -654 c6
                      -13 34 -38 62 -57 27 -18 56 -46 64 -63 8 -16 25 -47 38 -68 23 -37 25 -38 67
                      -32 36 6 56 1 114 -24 39 -17 76 -31 83 -31 7 0 31 11 53 24 33 19 50 23 89
                      19 26 -3 47 -9 47 -13 0 -10 -29 -23 -55 -24 -15 -1 -21 -10 -26 -39 -5 -32
                      -3 -40 15 -49 33 -17 106 -94 106 -111 0 -8 -6 -17 -14 -20 -14 -6 -36 10 -36
                      26 0 13 -54 87 -63 87 -5 0 1 -13 12 -28 11 -16 23 -41 27 -56 6 -24 4 -27
                      -12 -24 -10 2 -24 14 -30 27 -7 13 -32 44 -57 70 -25 25 -48 56 -52 69 -6 20
                      -13 22 -73 22 -37 0 -82 -4 -99 -9 -43 -13 -72 -3 -79 25 -7 27 -33 46 -89 64
                      -50 16 -135 101 -135 134 0 13 -7 43 -15 66 -8 23 -15 46 -15 51 0 16 61 -14
                      73 -36z m-642 7 c-6 -11 -14 -46 -17 -78 -9 -79 -38 -116 -111 -144 -66 -24
                      -96 -48 -89 -67 10 -25 -36 -36 -93 -24 -95 20 -114 16 -169 -38 -28 -28 -63
                      -68 -77 -89 -24 -37 -65 -62 -65 -41 0 12 21 48 43 73 20 23 23 41 5 30 -16
                      -9 -37 -38 -59 -79 -20 -37 -49 -46 -49 -15 0 13 19 39 47 66 45 41 50 61 7
                      29 -33 -25 -58 -18 -72 18 -12 30 -11 34 18 60 31 27 50 28 50 2 0 -7 -9 -16
                      -19 -20 -13 -4 -17 -12 -14 -25 4 -15 11 -17 41 -13 35 6 72 32 72 51 0 14
                      -30 37 -49 38 -45 3 -52 5 -47 13 3 4 24 11 46 15 34 5 50 2 90 -21 l49 -27
                      80 33 c63 26 88 32 117 27 45 -9 57 1 95 76 23 46 41 66 84 94 30 19 55 41 55
                      49 0 13 31 38 38 30 2 -2 -1 -12 -7 -23z m619 -156 c0 -24 62 -75 113 -93 48
                      -16 49 -18 23 -21 -16 -2 -39 2 -52 8 -13 8 -37 10 -61 6 -44 -8 -94 -9 -100
                      -2 -3 3 4 17 15 33 11 15 23 42 26 60 5 22 12 30 22 26 8 -3 14 -11 14 -17z
                      m-300 -16 c0 -14 7 -20 23 -20 31 0 47 -18 47 -55 0 -46 -16 -55 -92 -55 -64
                      0 -69 2 -80 25 -8 18 -9 35 -2 53 8 22 16 28 47 30 29 3 37 7 37 23 0 10 5 19
                      10 19 6 0 10 -9 10 -20z m-310 -32 c7 -18 15 -36 18 -40 2 -5 -6 -8 -19 -8
                      -12 0 -30 -7 -40 -16 -21 -19 -152 -29 -147 -12 3 7 30 20 61 31 36 12 65 29
                      79 47 13 16 25 29 28 29 3 1 12 -14 20 -31z m40 -88 c11 -11 20 -23 20 -27 0
                      -5 15 -8 34 -9 18 0 46 -10 61 -22 18 -14 42 -22 66 -22 35 0 59 -18 59 -44 0
                      -12 -48 -5 -59 8 -7 8 -32 19 -57 25 -24 6 -65 16 -90 22 -32 8 -48 17 -54 34
                      -12 30 -25 38 -39 21 -9 -10 -8 -16 4 -26 8 -7 12 -16 9 -21 -8 -14 -24 -10
                      -24 6 0 8 -6 13 -12 11 -7 -1 -10 -1 -5 1 4 3 7 15 7 28 0 38 48 47 80 15z
                      m651 4 c9 -11 10 -20 2 -32 -12 -19 -25 -20 -32 -1 -2 8 2 10 13 6 10 -4 15
                      -3 11 3 -3 6 -13 10 -22 10 -11 0 -14 -7 -11 -25 4 -16 0 -25 -11 -28 -9 -3
                      -1 -5 18 -6 l33 -1 -23 -24 c-22 -22 -25 -23 -51 -10 -15 8 -36 14 -48 14 -27
                      0 -86 -31 -128 -68 -18 -15 -56 -37 -84 -47 l-51 -18 -24 27 c-12 15 -23 30
                      -23 34 0 5 20 13 45 20 42 11 45 14 45 46 0 40 15 49 69 40 34 -5 40 -3 59 24
                      24 33 63 47 91 33 13 -7 24 -6 37 4 25 19 68 19 85 -1z m598 -13 c15 -15 17
                      -27 12 -52 -8 -42 -39 -50 -77 -20 -19 15 -22 21 -11 21 9 0 19 -4 22 -10 14
                      -23 35 -10 35 22 0 17 -3 28 -7 25 -7 -8 -33 11 -33 24 0 16 40 9 59 -10z
                      m-1881 -3 c-6 -16 -36 -34 -44 -25 -9 9 15 37 33 37 8 0 13 -5 11 -12z m1813
                      -8 c23 -19 23 -19 4 -20 -21 0 -45 17 -45 32 0 14 17 9 41 -12z m-895 -56 c-3
                      -9 -6 -20 -6 -25 0 -19 -27 -8 -38 16 -11 24 -10 25 19 25 24 0 30 -4 25 -16z"/>
                      <path d="M1600 1772 c0 -13 11 -34 25 -47 35 -36 113 -35 152 1 29 27 29 27
                      -40 2 -45 -16 -95 -2 -117 35 -18 29 -20 30 -20 9z"/>
                      <path d="M1358 1748 c-31 -32 -81 -37 -128 -13 -34 18 -40 7 -10 -20 42 -38
                      180 0 168 46 -2 10 -11 6 -30 -13z"/>
                      <path d="M1443 693 c-7 -3 -13 -15 -13 -28 0 -24 16 -35 38 -28 6 3 12 15 12
                      28 0 24 -16 35 -37 28z"/>
                      <path d="M1522 688 c-16 -16 -15 -45 2 -51 15 -6 36 11 36 29 0 21 -25 35 -38
                      22z"/>
                    </g>
                  </svg></a>
                </div>
              </div> 
              <div class="mod-title">Mindfullness Coach</div>
            </div>
            <div class="col-6">
              <div class="borders mr-2 mt-2">  
                <div class="inline-box">
                  <a href="<?php echo $taoh_home_url."/learning/jusask"; ?>"><svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                  width="45pt" height="30pt" viewBox="0 0 256.000000 256.000000"
                    preserveAspectRatio="xMidYMid meet">

                    <g transform="translate(0.000000,256.000000) scale(0.100000,-0.100000)"
                    fill="#000000" stroke="none">
                      <path d="M1197 2219 c-162 -24 -263 -127 -298 -306 -11 -60 -21 -75 -34 -53
                      -4 6 -24 19 -45 30 -48 24 -122 26 -160 4 -34 -21 -80 -89 -81 -124 -1 -14 -2
                      -35 -3 -48 -1 -18 -13 -28 -60 -47 -73 -30 -146 -99 -178 -169 -23 -50 -23
                      -57 -23 -401 0 -401 2 -410 84 -498 66 -71 128 -99 227 -105 l82 -4 -30 -82
                      c-53 -141 -60 -140 122 -21 l163 105 508 0 c484 0 511 1 562 20 62 23 128 77
                      163 131 48 76 54 129 54 449 0 319 -6 373 -54 448 -42 67 -145 141 -197 142
                      -13 0 -17 10 -17 49 0 56 -24 104 -73 146 -46 38 -128 36 -189 -5 -51 -34 -54
                      -31 -65 45 -25 172 -126 273 -297 295 -83 11 -82 11 -161 -1z m278 -195 c50
                      -24 75 -71 75 -140 0 -36 -6 -59 -19 -75 -18 -22 -27 -24 -128 -27 -103 -2
                      -106 -2 -75 13 46 20 32 36 -18 19 -40 -14 -54 -11 -122 24 -5 2 -8 -3 -8 -11
                      0 -8 15 -22 33 -31 32 -15 31 -16 -60 -16 -82 0 -95 2 -119 24 -24 20 -28 31
                      -27 73 1 52 32 126 56 136 11 4 9 -5 -6 -32 -49 -89 52 -182 138 -125 26 17
                      47 66 39 89 -5 12 2 15 30 15 35 0 36 -1 36 -36 0 -50 45 -94 95 -94 51 0 95
                      44 95 95 0 27 -8 44 -29 66 -35 35 -75 38 -128 9 -49 -26 -79 -25 -128 5 -22
                      13 -53 25 -70 26 l-30 2 35 8 c19 4 94 6 165 5 105 -2 139 -6 170 -22z m-291
                      -35 c33 -26 35 -73 4 -106 -36 -39 -92 -29 -113 20 -31 75 45 136 109 86z
                      m261 -14 c26 -25 32 -59 15 -85 -8 -12 -10 -10 -10 11 0 31 -17 49 -45 49 -46
                      0 -62 -57 -23 -84 19 -14 20 -16 5 -16 -27 0 -67 45 -67 75 0 14 11 37 25 50
                      13 14 36 25 50 25 14 0 37 -11 50 -25z m-565 -891 c0 -152 -16 -183 -97 -184
                      -54 0 -91 31 -100 83 l-6 38 34 -3 c26 -2 34 -8 39 -28 4 -18 12 -25 30 -25
                      25 0 25 0 28 118 l3 117 34 0 35 0 0 -116z m686 44 c15 -40 39 -106 55 -145
                      l27 -73 -29 0 c-24 0 -31 6 -44 36 -14 35 -15 35 -67 32 -49 -3 -54 -5 -67
                      -35 -11 -26 -21 -33 -42 -33 l-28 0 25 68 c86 227 83 222 114 222 26 0 30 -6
                      56 -72z m384 -10 l0 -82 38 37 c26 25 47 37 67 37 l29 0 -32 -33 c-18 -18 -32
                      -36 -32 -41 0 -4 18 -34 40 -67 l41 -59 -33 0 c-28 1 -37 7 -61 46 -23 38 -30
                      44 -42 34 -8 -7 -15 -28 -15 -46 0 -30 -3 -34 -25 -34 l-25 0 0 145 0 145 25
                      0 c25 0 25 -1 25 -82z m-1393 32 c-5 -37 -3 -40 18 -40 19 0 24 7 30 40 4 26
                      12 40 22 40 13 0 14 -8 9 -40 -5 -32 -3 -40 9 -40 22 0 18 -27 -5 -33 -24 -6
                      -29 -57 -5 -57 8 0 15 -7 15 -15 0 -8 -9 -15 -19 -15 -16 0 -20 -8 -23 -37 -2
                      -27 -8 -39 -21 -41 -15 -3 -17 2 -11 38 7 40 6 42 -17 38 -20 -2 -25 -10 -27
                      -40 -2 -29 -8 -38 -22 -38 -15 0 -17 6 -13 40 5 33 3 40 -11 40 -9 0 -16 7
                      -16 15 0 8 9 15 20 15 16 0 20 7 20 30 0 20 -5 30 -15 30 -8 0 -15 7 -15 15 0
                      8 9 15 19 15 15 0 21 10 26 40 4 26 12 40 22 40 12 0 15 -8 10 -40z m771 -46
                      c31 -20 29 -54 -3 -54 -14 0 -28 7 -31 15 -8 20 -64 20 -64 1 0 -9 22 -21 55
                      -31 65 -18 86 -47 67 -90 -15 -33 -56 -48 -112 -42 -44 5 -80 33 -80 63 0 22
                      53 17 68 -6 14 -23 44 -26 61 -6 15 18 -1 31 -56 45 -54 15 -75 42 -61 82 16
                      47 98 59 156 23z m-338 -53 c0 -85 6 -101 36 -101 32 0 44 27 44 104 l0 66 30
                      0 30 0 0 -105 0 -106 -42 1 c-24 0 -59 -2 -80 -6 -29 -5 -40 -2 -57 15 -18 18
                      -21 33 -21 111 l0 90 30 0 30 0 0 -69z m838 42 l27 -28 -23 -3 c-13 -2 -32 4
                      -42 13 -22 20 -64 16 -68 -7 -2 -13 10 -21 49 -32 28 -9 58 -21 65 -27 22 -18
                      17 -66 -8 -83 -46 -33 -158 -11 -158 30 0 19 34 18 50 -2 19 -22 75 -15 75 11
                      0 15 -14 24 -60 40 -52 17 -60 24 -63 47 -8 67 102 96 156 41z"/>
                      <path d="M1116 1954 c-22 -21 -20 -61 3 -74 31 -16 49 -12 67 15 15 23 15 27
                      0 50 -19 29 -47 32 -70 9z"/>
                      <path d="M1486 1073 c-9 -26 -16 -48 -16 -50 0 -2 18 -3 39 -3 l39 0 -16 50
                      c-9 28 -19 50 -23 50 -4 0 -14 -21 -23 -47z"/>
                      <path d="M546 1064 c-12 -31 -6 -44 19 -44 21 0 25 5 25 30 0 23 -5 30 -19 30
                      -10 0 -22 -7 -25 -16z"/>
                    </g>
                  </svg></a>
                </div>
              </div>
              <div class="mod-title">Career Coach</div>
            </div>
            <?php if( taoh_user_is_logged_in() && isset( $_GET[ 'postblog' ] ) && $_GET[ 'postblog' ] ) { ?>
            <div class="col-6 mt-3 ml-2">
              <a href="<?php echo TAOH_DASH_URL."?app=reads&from=dash&to=reads/post"; ?>" target="_blank" class="btn btn-outline-primary" style="border-radius: 15px;">Post Blog</a>
            </div>
            <?php }?>
          </div>    
        </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
