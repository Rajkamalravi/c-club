<footer class="page-footer">
    <section class="footer-area position-relative font-light">
      <div class="container">
        <div class="bg-dark align-items-center pb-4 copyright-wrap">
          <div class="col-lg-12">
            <ul class="nav justify-content-center" style="margin-bottom: -10px;margin-top:10px;">
              <li class="nav-item"><a class="nav-link " href="https://tao.ai" target="_blank" style="color: #999999;">TAO.ai</a></li>
              <li class="nav-item"><a class="nav-link " href="https://theworktimes.com" target="_blank" style="color: #999999;">TheWorkTimes</a></li>
              <li class="nav-item"><a class="nav-link " href="https://NoWorkerLeftBehind.org" target="_blank" style="color: #999999;">#NoWorkerLeftBehind</a></li>
            </ul>
            <ul class="nav justify-content-center border-bottom pb-3 mb-3">
              <li class="nav-item"><a class="nav-link " href="<?php echo TAOH_TEMP_SITE_URL."/employers";?>" target="_blank" style="color: #999999;">Employers</a></li>
              <li class="nav-item"><a class="nav-link " href="<?php echo TAOH_TEMP_SITE_URL."/partners";?>" target="_blank" style="color: #999999;">Partners</a></li>
              <li class="nav-item"><a class="nav-link " href="<?php echo TAOH_TEMP_SITE_URL."/professionals";?>" target="_blank" style="color: #999999;">Professionals</a></li>
            </ul>
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
                  <!--<a class="support-page" target="_blank" style="color: #31A4FB;">Question?</a> /  -->
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

<script>
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
</script>
</body>
</html>