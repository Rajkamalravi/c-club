<?php

$curr_page = taoh_parse_url(0);
if(TAOH_JUSASK_ENABLE){
    include_once('chatbot.php');
}
if(taoh_user_is_logged_in()){
    require_once(TAOH_CORE_PATH . '/club/networking_footer.php');
}
?>

</main>

<?php
if (!taoh_user_is_logged_in()) {
    echo '<div class="col footer-prompt" id="login-prompt" style="display: none;">';
    echo '<h5 class="pb-2">You need to log in to see the full content!</h5>';
    echo '<a href="' . (TAOH_LOGIN_URL ?? '') . '" class="btn theme-btn" id="login-btn"><i class="la la-sign-in mr-1"></i>Login / Signup</a>';
    echo '</div>';
}
?>




<footer class="page-footer">
    <input type="hidden" name="global_settings" id="global_settings" />
    <input type="hidden" name="avt_img_delete" id="avt_img_delete" />
    <section class="footer-area pt-30px bg-dark position-relative font-light">
      <div class="container">
        <div class="row align-items-center pb-4 copyright-wrap">
          <div class="col-lg-12">
          <?php if(!TAOH_CUSTOM_FOOTER)  { ?>
            <ul class="nav justify-content-center" style="margin-bottom: -10px;">
              <li class="nav-item"><a class="nav-link " href="https://tao.ai" target="_blank" style="color: #999999;">TAO.ai</a></li>
              <li class="nav-item"><a class="nav-link " href="https://theworktimes.com" target="_blank" style="color: #999999;">TheWorkTimes</a></li>
              <li class="nav-item"><a class="nav-link " href="https://NoWorkerLeftBehind.org" target="_blank" style="color: #999999;">#NoWorkerLeftBehind</a></li>
            </ul>
            <ul class="nav justify-content-center ">
              <li class="nav-item"><a class="nav-link " href="<?php echo TAOH_SITE_URL_ROOT."/employers";?>" target="_blank" style="color: #999999;">Employers</a></li>
              <li class="nav-item"><a class="nav-link " href="<?php echo TAOH_SITE_URL_ROOT."/partners";?>" target="_blank" style="color: #999999;">Partners</a></li>
              <li class="nav-item"><a class="nav-link " href="<?php echo TAOH_SITE_URL_ROOT."/professionals";?>" target="_blank" style="color: #999999;">Professionals</a></li>
            </ul>
            <?php } else {

                echo TAOH_CUSTOM_FOOTER;
            } ?>
           
            <p class="text-center text-muted" style="color: #999999;">
              <strong style="color: #6C757D;">&copy; <?php echo date('Y'); ?>
                <a href="https://jushires.com" style="color: #6C757D;">#Hires</a>
                  [ by
                  <a href="https://tao.ai" style="color: #6C757D;">TAO.ai</a>
                  ] | All Rights Reserved |
                  <a class="flex-column text-center support-page" target="_blank" style="cursor:pointer;color: #31A4FB;" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16.107" height="16.107" viewBox="0 0 48.107 48.107">
                      <g id="bx_help_circle-1325051883812201654" data-name="bx+help+circle-1325051883812201654" transform="translate(-2 -2)">
                        <path id="Path_364" data-name="Path 364" d="M17.529,6a9.474,9.474,0,0,0-9.463,9.463h4.811a4.652,4.652,0,0,1,9.3,0c0,1.438-1.157,2.482-2.925,3.911-.613.5-1.193.972-1.662,1.441a7.836,7.836,0,0,0-2.47,5.229v1.6h4.811l0-1.523a3.3,3.3,0,0,1,1.061-1.907,16.663,16.663,0,0,1,1.287-1.1c1.874-1.518,4.71-3.81,4.71-7.654A9.47,9.47,0,0,0,17.529,6ZM15.123,30.053h4.811v4.811H15.123Z" transform="translate(8.525 5.621)" fill="#7f7f7f"/>
                        <path id="Path_365" data-name="Path 365" d="M26.053,2A24.053,24.053,0,1,0,50.107,26.053,24.081,24.081,0,0,0,26.053,2Zm0,43.3A19.243,19.243,0,1,1,45.3,26.053,19.265,19.265,0,0,1,26.053,45.3Z" fill="#7f7f7f"/>
                      </g>
                    </svg>  <span  style="color: #31A4FB;">Support</span>
                  </a> <?php if(taoh_user_is_logged_in()){ ?> |
                  <a class="support-page" target="_blank" style="color: #31A4FB;">Question?</a> / 
                      <a class="feedback-page" target="_blank" style="cursor:pointer;color: #31A4FB;">Feedback</a>
                  <?php } ?>

              </strong><br>
              <a href="https://tao.ai/privacy.php" target="_BLANK" style="color: #6C757D;">Privacy Policy</a> |
              <a href="https://tao.ai/terms.php" target="_BLANK" style="color: #6C757D;">Terms &amp; Conditions</a> |
              <a href="https://tao.ai/conduct.php" target="_BLANK" style="color: #6C757D;">Code of Conduct</a></p>
          </div><!-- end col-lg-12 -->
        </div><!-- end row -->
      </div><!-- end container -->
    </section>
    <section class="extraSpace"></section>
  </footer>

<div class="modal" id="indexedDBWarningModal" tabindex="-1" role="dialog" aria-labelledby="indexedDBWarningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="indexedDBWarningModalLabel">Warning</h5>
        </div>
        <div class="modal-body">
            <p>This browser is not supported to load this site. Please upgrade your browser version to the latest.
                If the issue persists, try a different browser.</p>
        </div>
    </div>
    </div>
</div>

</div><!-- end class="wrapper" -->

<style>
    @keyframes highlight-green {
        0% {
            background: #3ee632;
        }
        100% {
            background: #ffff99;
        }
    }

    .highlight-green {
        animation: highlight-green 10s;
    }
</style>
<?php
 $session_data = taoh_session_get(TAOH_ROOT_PATH_HASH);
if (defined('TAOH_CUSTOM_FOOT')) {
    echo TAOH_CUSTOM_FOOT;

    var_dump(get_defined_vars());
}

if (defined('TAOH_SITE_GA_ENABLE') && TAOH_SITE_GA_ENABLE) {
    if (defined('TAOH_GA_CODE')) {
        echo TAOH_GA_CODE;
    } else {
        ?>
        <!-- Google tag (gtag.js) -->
        <script async
                src="https://www.googletagmanager.com/gtag/js?id=<?php echo (defined('TAOH_PAGE_GA') && TAOH_PAGE_GA) ? TAOH_PAGE_GA : TAOH_SITE_GA; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());

            gtag('config', '<?php  echo (defined('TAOH_PAGE_GA') && TAOH_PAGE_GA) ? TAOH_PAGE_GA : TAOH_SITE_GA; ?>');
        </script>
    <?php }
}

if (!@$_COOKIE['client_time_zone']) { ?>
    <script>
        var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        document.cookie = "client_time_zone=" + timezone;
    </script>
<?php } ?>
<?php echo @$hook;
// <script src="https://unmeta.net/script/js/play3/main.js"></script>
?>

<script type="text/javascript">
    var loopTime = '<?php echo TAOH_NOTIFICATION_LOOP_TIME_INTERVAL;?>';

    $(document).ready(function () {
        // Set a threshold for maximum load time (e.g., 15000 milliseconds = 15 seconds)
        //const loadTimeThreshold = <?php //echo TAOH_HEALTH_TIMEOUT; ?>// * 1000;

        // Set a timeout function that redirects the user if the page takes too long to load
        /*const timeoutHandle = setTimeout(function() {   // Redirect the user to a different page if the load time exceeds the threshold
            window.location.href = '<?php //echo TAOH_SITE_URL_ROOT."/down.html";  ?>';
        }, loadTimeThreshold);

        // Listen for the window's 'load' event
        window.addEventListener('load', function() {  // If the page loads within the threshold, clear the timeout to prevent redirection
            clearTimeout(timeoutHandle);
        });*/

        // var loadTime = window.performance.timing.domContentLoadedEventEnd - window.performance.timing.navigationStart;
        // console.log('Load Time ------------', loadTime);
        // console.log(loadTime + " <= " + loadTimeThreshold);
        //if(loadTime >= loadTimeThreshold){
        //window.location.href = '<?php //echo TAOH_SITE_URL_ROOT."/down.php";  ?>';
        //}
        
        <?php if($curr_page != 'login' && $curr_page != 'createacc'){ ?>
            //checkReferralStatus();
            setInterval(function () {
                checkReferralStatus();
            }, 60000);
        <?php } ?>


        <?php
        if(taoh_user_is_logged_in() ) {
            if(TAOH_NOTIFICATION_ENABLED && TAOH_NOTIFICATION_STATUS == 2){
                ?>
                setTimeout(function () {
                    taoh_notification_init(1);
                }, 3000);

                setInterval(function () {
                    taoh_notification_init(0);

                }, loopTime);
                <?php
            }

            ?>
            checksitemap();

           <?php if($curr_page != 'settings'){ ?>
                setInterval(function () {
                    checkProfileCompletion();
                  
                }, 60000);

                
            <?php } ?>
            checksuperadminInit();
            setInterval(function () {
                    checksuperadminInit();
            }, 60000);

            savetaodata();
            setInterval(function () {
               // console.log('----savetaodata------------')
                if(typeof index_name !== 'undefined') checkTTL(index_name);
                savetaodata();
            }, 10000);

            <?php
        }
        ?>

        if(localStorage.getItem('indexedDBFailed') === 'true'){
            showIndexedDBWarning();
        }
    });

    async function getUserLiveStatus(pToken) {
        if (!navigator.onLine) return {success: false};

        let data = {
            'ops': 'live',
            'status': 'get',
            'code': _taoh_ops_code,
            'key': pToken,
            'ptoken': pToken,
        };

        return await new Promise((resolve, reject) => {
            $.post(_taoh_cache_chat_proc_url, data, function (response) {
                let res = JSON.parse(response);
                resolve(res);
            });
        });
    }

    function checksitemap() {
        <?php
        $currentDate = date("Ymd");
        $filename = "sitemap_" . $currentDate . ".sitemap";
        if(!file_exists($filename)){
        //$myfile = fopen($filename, "a") or die("Unable to open file!");
        ?>
        jQuery.get("<?php echo TAOH_SITE_URL_ROOT . '/sitemap'; ?>", {
            'taoh_action': 'taoh_sitemap_call',
        }, function (response) {
            res = response;
            //render_events_template(res, eventArea);
        }).fail(function () {
            console.log("Network issue!");
        })
        <?php
        }  ?>
    }

    function checkProfileCompletion(){
        <?php if(isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->profile_complete)  && 
        taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->profile_complete == 0 && 
        isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->fname) && 
        taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->fname == 'anonymous'){ ?>
        if (typeof showBasicSettingsModal === 'function') {
            showBasicSettingsModal();
        }
             // $('#toast').toast('show');
             //    $('#toast').show();
             //    $("#toast").addClass("toast_active");
             //    var msg = "complete your settings to fully use the platform.";
             //    $("#toast_error").html("<div class='toasterror_class'><span><i class='las la-exclamation-circle info_icon'></i> "+msg+"&nbsp;<span class='toast_dismiss' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</span></span></div>");
             //    setTimeout(function () {
             //        $("#toast").removeClass("toast_active");
             //    }, 8000);
        <?php } ?>       
    }

    function checksuperadminInit(){
        <?php if(isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->is_super_admin) && 
        taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->is_super_admin == 1 && 
        taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->site_status == 'init'){ ?>
            var msg = 'Please complete your site settings. Click Manage Button on the header menu and proceed to fill the site data.';
            taoh_set_error_message(msg,8000);
        <?php } ?>       
    }

    function checkReferralStatus(){
        var data = {
            'taoh_action': 'taoh_check_referral_status',
        };

        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
            data = response;//JSON.parse(response);
            //alert(data);
            if (data == 0) {

                $('#toast').toast('show');
                $('#toast').show();
                $("#toast").addClass("toast_active");
                var msg = "Sorry, You haven't logged in the site. You will be redirecting in few secs.";
                $("#toast_error").html("<div class='toasterror_class'><span><i class='las la-exclamation-circle info_icon'></i> "+msg+"&nbsp;<span class='toast_dismiss' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</span></span></div>");
                //$("#loader").show();
                //$("#error_textmsg").html(msg);
                    
                setTimeout(function () {

                    $("#toast").removeClass("toast_active");
                    $("#loader").hide();
                    
                    //window.location.href = '<?php //echo TAOH_SITE_URL_ROOT.'/login' ?>';
                }, 8000);
                

            } else {

            }


        });
    }

    function updateStatus(process) {


        <?php  if(taoh_user_is_logged_in() ) { ?>

        $('#userMenuDropdownarea').addClass('stay_open');

        var my_status = $('#my_status').val();

        if (my_status == '') {
            return false;
        }
        if (process == 0) {
            $('#my_status').val('');
            my_status = '';
        }
        if (my_status != '') { //add
            $('#status_save').hide();
            $('#status_remove').show();
        } else { //remove
            $('#status_save').show();
            $('#status_remove').hide();
        }
        var data = {
            'taoh_action': 'taoh_update_status',
            "process": process,
            "my_status": my_status,
            "ptoken": "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>",
        };

        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
            data = response;//JSON.parse(response);
            if (process) {


            } else {

            }


        });

        setTimeout(function () {
            $('#userMenuDropdownarea').removeClass('stay_open');
        }, 5000);


        <?php } ?>
    }

    function taoh_counter_init(call_at) {

        <?php  if(taoh_user_is_logged_in() && TAOH_NOTIFICATION_ENABLED ) { ?>
        //alert('----2------');
        $('#badge_count').hide();
        $('#badge_count').html('');
        $('.notification_row').removeClass('bold');
        <?php  if ( taoh_user_is_logged_in() ) {  ?>
        var data = {
            'taoh_action': 'taoh_get_notification_counter',
            'mod': 'core',
            'ops': "get",
            "type": "notify",
            "token": "<?php echo TAOH_API_TOKEN; ?>",
            "ptoken": "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>",
            "call_at": call_at

        };
        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
            data = response;//JSON.parse(response);
            if (data.status) {
                if (data.total_num > 0) {
                    $('#badge_count').show();
                    $('#badge_count').html(data.total_num);
                } else {
                    $('#badge_count').hide();
                    $('#badge_count').html('');
                }

            } else {
                $('#badge_count').hide();
                $('#badge_count').html('');
            }
        });
        <?php } ?>
        <?php } ?>
    }

    <?php  if ( taoh_user_is_logged_in() && TAOH_NOTIFICATION_ENABLED ) {  ?>
    function taoh_notification_init(call_from = 0) {
        // $('#loaderChat').show();
        /*var data = {
         'taoh_action': 'taoh_get_notification',
         'mod': 'core',
         'ops': "get",
         "type" : "notify",
         "token" : "<?php //echo TAOH_API_TOKEN; ?>",
     "ptoken" :  "<?php //echo $_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']->ptoken; ?>",
     "call_from" : call_from
   };*/
        var data = {
            'taoh_action': 'taoh_get_notification',
            'mod': 'notify',
            'ops': "webnotify",
            "type": "notify",
            "token": "<?php echo TAOH_API_TOKEN; ?>",
            "ptoken": "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>",
            "call_from": 0, //call_from
        };
        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
            data = response; //JSON.parse(response);
            // console.log(data);
            if (data.status) {
                if (data.total_num > 0) {
                    $('#notifications-list').css('height', '250px');
                    render_notification_list_template(data.output, call_from);
                    if (!call_from) {
                        $('#badge_count').show();
                        //$('#badge_count').html(data.total_num);
                        var old = $('#badge_count').html();
                        if (old == '') old = 0;
                        var total = data.output.length + parseInt(old);
                        $('#badge_count').html(total);
                    }
                } else {
                    if (call_from) {
                        $('#notifications-list').html('<li class="no-result">No Result Found</li>');
                    }
                }
                if (data.total_num > 10) {
                    $('#notification_load_more').show();
                }
            } else {
                if (call_from) {
                    $('#badge_count').hide();
                    $('#notifications-list').html('<li class="no-result">No Result Found</li>');
                    $('#notification_load_more').hide();
                }
            }
            $('#loaderChat').hide();
            if (call_from) {
                taoh_counter_init(1);
            }

        });

    }

    function render_notification_list_template(data, call_from) {
        var notification_data = '';
        // console.log('------call_from------', call_from);
        var class_add = '';
        if (call_from == 0) {
            class_add = 'bold';
        }
        $.each(data, function (i, v) {

            notification_data += `<li class="notification_row ${class_add}">
            <div class="row m-2" style="font-size:12px;">
              <div class="col-lg-2" style="padding-left:2px;padding-right:2px;">
                <div class='bgimage ' style="">
                 <img width="50" class="lazy" src="https://opslogy.com/avatar/PNG/128/${v.avatar ? v.avatar : 'default'}.png" alt="avatar" style=""></div>

                </div>

                <div class="col-lg-8 fs-12" style="padding-left: 5px;">
                  <p><span>${v.title}</span><p>
                  <span>${v.message}</span>
               </div>
               <div class="col-lg-2 fs-12" style="padding:0px;margin:0px;">
                 <span class="notify_time">${v.timestamp}</span>
               </div>
            </div>
            <div class="dropdown-divider"></div>
          </li>`;


        });
        //<span><h3>${v.title}</h3></span>
        if (call_from == 1) {
            $('#notifications-list').html(notification_data);
        } else {
            $('#notifications-list').prepend(notification_data);
        }
    }
    <?php  }  ?>

    var mertricsLoad = function () {
        $('.dash_metrics').each(function (f) {
            var conttoken = $(this).attr("conttoken");
            var metrics = $(this).attr("data-metrics");
            var type = $(this).attr("data-type");
            if (type == '') {
                //type = '<?php //echo TAO_PAGE_TYPE ?>';
                type = 'view';
            }
            var data = {
                'taoh_action': 'toah_metrics_push',
                'conttoken': conttoken,
                'ptoken': '<?php echo isset($session_data['USER_INFO']) ? $session_data['USER_INFO']->ptoken : '' ; ?>',
                'met_action': metrics,
                'met_type': type,
            };
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
                //success
            }).fail(function () {
                console.log("Network issue!");
            })
        });
    }
    window.onload = function () {
        setTimeout(mertricsLoad, 8000);
    }

    function checkTTL(index_name, store_name = dataStore) {
        const TTLStoreName = objStores.ttl_store.name;
        getIntaoDb(dbName).then((db) => {
            if (db.objectStoreNames.contains(TTLStoreName)) {
                const request = db.transaction(TTLStoreName).objectStore(TTLStoreName).get(index_name);
                request.onsuccess = () => {
                    const TTLdata = request.result;
                    if (TTLdata) {
                        let current_time = new Date().getTime();

                        // Check ttl exists or not for(5 minutes)
                        if (current_time > TTLdata.time) {
                            let obj_data = {
                                [store_name]: '',
                                [objStores.ttl_store.name]: '',
                                [objStores.api_store.name]: ''
                            };
                            Object.keys(obj_data).forEach(key => {
                                IntaoDB.removeItem(key, index_name).catch((err) => console.log('Storage failed', err));
                            });
                        }else{
                            console.log('TTL is not expired');
                        }
                    }
                }
            }
        });
    }

    function triggerNextRequest(callback, ttl = 3000) {
        setTimeout(callback, ttl);
    }

    $(document).on("click", '.toast_dismiss', function () {
        // $("#toast").toggle();
        $("#toast").removeClass("toast_active");
    });

    function showIndexedDBWarning() {
        const modal = document.getElementById("indexedDBWarningModal");
        modal.style.display = "block";
    }


    <?php if(isset($_GET['clear']) && $_GET['clear'] == 'config') { ?>
        
        const newUrl = new URL(location.href);
        newUrl.searchParams.delete('clear');
        window.history.replaceState({}, document.title, newUrl.href);
    <?php } ?>

</script>

</body>
</html>
<?php

if (isset($_GET['secret_delete']) && $_GET['secret_delete']) {
    $url = TAOH_PLUGIN_PATH . "/cache/general/subsecret_" .  $_GET['secret_delete'] . ".cache";
    unlink($url);
    taoh_set_error_message('Error in Operation. Please try again.',8000,1);

}

if (isset($_GET['secret_delete_force']) &&  $_GET['secret_delete_force'] == 1) {

    $url = TAOH_PLUGIN_PATH . "/cache/general/subsecret_" .  TAOH_SITE_ROOT_HASH . ".cache";
    unlink($url);
   // taoh_set_error_message('Error in Operation. Please try again.',8000,1);

}

if ( ! isset( $_COOKIE[ 'client_time_zone' ] ) && stristr( $_SERVER[ 'REQUEST_URI' ], '/events/' ) ){
    header("Location: ".$_SERVER[ 'REQUEST_URI' ]); taoh_exit();
}

taoh_cacheops('logpush');
taoh_exit();

?>
