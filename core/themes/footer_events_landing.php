<?php
$curr_page = taoh_parse_url(0);
$opt = taoh_parse_url(1);
//echo "====ccccccc===".$opt;

if (TAOH_JUSASK_ENABLE) {
    include_once('chatbot.php');
}
?>

</main>

 <!-- Footer Banner  -->
<?php if(defined('TAOH_FOOTER_BANNER_AD') && TAOH_FOOTER_BANNER_AD) {
    // file_get_contents(TAOH_OPS_PREFIX.'/images/calendar', false, stream_context_create(array( "ssl"=>array( "verify_peer"=>false, "verify_peer_name"=>false,),)))
    if(!isset($_SESSION['footer_banner'])){
        $get_banner = file_get_contents(TAOH_CDN_ADS);
        $banner = json_decode($get_banner,1);
        $footer_banner = $banner['footer'];
        $_SESSION['footer_banner'] = $footer_banner;
    }
    if(isset($_SESSION['footer_banner']) && count($_SESSION['footer_banner']) > 0){
    foreach($_SESSION['footer_banner'] as $key=>$val){
        $link = str_ireplace('[TAOH_HOME_URL]',TAOH_SITE_URL_ROOT,$val['link']);

    ?>
    <div class="cover-workcongress-image">
    <div class="bg-image" style="background-image: url('<?php echo $val['image'];?>');"></div>
    <div class="glass-overlay"></div>
    <a href="<?php echo $link;?>" class="workcongress-main-image">
        <img src="<?php echo $val['image'];?>" alt="">
    </a>
</div>
<?php } } } ?>


<footer class="page-footer">
    <input type="hidden" name="global_settings" id="global_settings" />
    <input type="hidden" name="avt_img_delete" id="avt_img_delete" />
    <section class="footer-area pt-30px position-relative font-light" style="background: #1E1C1C;">
      <div class="container">
        <div class="row align-items-center pb-4 copyright-wrap">
          <div class="col-lg-12">
            <?php if(TAOH_FOOTER_MENU_ARRAY !='')  { $footer_array = json_decode(TAOH_FOOTER_MENU_ARRAY,1); ?>
                <div class="col-xl-5 mx-auto px-0">
                    <ul class="nav justify-content-center" style="margin-bottom: -10px;">
                        <?php foreach($footer_array as $key=>$val){ ?>
                            <li class="nav-item text-center footer-link-text"><a class="nav-link " title="<?php echo $val[2];?>" href="<?php echo $val[0];?>" target="_blank" style="color: #ffffff;"><?php echo $val[1];?></a></li>
                        <?php } ?>
                    </ul>
                </div>

            <?php } else { ?>

                <div class="col-xl-5 mx-auto px-0">

                </div>
           <?php } ?>


            <div class="footer-menu-item-container my-3 px-3">
                <a href="<?php echo TAOH_SITE_URL_ROOT;?>" target="_blank" class="footer-menu-item mx-lg-5">

                    <div class="svg-container">
                        <svg width="16" height="16" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.99738 4.99023C9.99738 5.3418 9.73694 5.61719 9.44178 5.61719H8.88617L8.89833 8.74609C8.89833 8.79883 8.89485 8.85156 8.88964 8.9043V9.21875C8.88964 9.65039 8.57885 10 8.19514 10H7.91734C7.89824 10 7.87914 10 7.86004 9.99805C7.83574 10 7.81143 10 7.78712 10H7.22284H6.80613C6.42242 10 6.11163 9.65039 6.11163 9.21875V8.75V7.5C6.11163 7.1543 5.86335 6.875 5.55603 6.875H4.44482C4.1375 6.875 3.88922 7.1543 3.88922 7.5V8.75V9.21875C3.88922 9.65039 3.57843 10 3.19472 10H2.77801H2.22415C2.1981 10 2.17206 9.99805 2.14602 9.99609C2.12518 9.99805 2.10435 10 2.08351 10H1.80571C1.422 10 1.11121 9.65039 1.11121 9.21875V7.03125C1.11121 7.01367 1.11121 6.99414 1.11294 6.97656V5.61719H0.555603C0.243076 5.61719 0 5.34375 0 4.99023C0 4.81445 0.0520878 4.6582 0.173626 4.52148L4.62539 0.15625C4.74693 0.0195312 4.88583 0 5.00737 0C5.12891 0 5.26781 0.0390625 5.37198 0.136719L9.80639 4.52148C9.94529 4.6582 10.0147 4.81445 9.99738 4.99023Z" fill="#1E1C1C"/>
                        </svg>
                    </div>
                    <span>Home</span>
                </a>
                <!--<div class="terms-menu" style="position: relative;">
                    <a class="footer-menu-item mx-lg-5" id="termsLink" style="cursor: pointer;">
                        <div class="svg-container">
                            <svg width="16" height="16" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 1.11111C0 0.498264 0.498264 0 1.11111 0H3.88889V2.22222C3.88889 2.52951 4.13715 2.77778 4.44444 2.77778H6.66667V3.44792C5.38368 3.81076 4.44444 4.98958 4.44444 6.38889C4.44444 7.41493 4.94965 8.32118 5.72396 8.87674C5.6684 8.88542 5.61285 8.88889 5.55556 8.88889H1.11111C0.498264 8.88889 0 8.39062 0 7.77778V1.11111ZM6.66667 2.22222H4.44444V0L6.66667 2.22222ZM7.5 3.88889C8.16304 3.88889 8.79893 4.15228 9.26777 4.62112C9.73661 5.08996 10 5.72585 10 6.38889C10 7.05193 9.73661 7.68782 9.26777 8.15666C8.79893 8.6255 8.16304 8.88889 7.5 8.88889C6.83696 8.88889 6.20107 8.6255 5.73223 8.15666C5.26339 7.68782 5 7.05193 5 6.38889C5 5.72585 5.26339 5.08996 5.73223 4.62112C6.20107 4.15228 6.83696 3.88889 7.5 3.88889ZM7.5 8.05556C7.61051 8.05556 7.71649 8.01166 7.79463 7.93352C7.87277 7.85538 7.91667 7.7494 7.91667 7.63889C7.91667 7.52838 7.87277 7.4224 7.79463 7.34426C7.71649 7.26612 7.61051 7.22222 7.5 7.22222C7.38949 7.22222 7.28351 7.26612 7.20537 7.34426C7.12723 7.4224 7.08333 7.52838 7.08333 7.63889C7.08333 7.7494 7.12723 7.85538 7.20537 7.93352C7.28351 8.01166 7.38949 8.05556 7.5 8.05556ZM6.38889 5.58333V5.69444C6.38889 5.84722 6.51389 5.97222 6.66667 5.97222C6.81944 5.97222 6.94444 5.84722 6.94444 5.69444V5.58333C6.94444 5.49132 7.0191 5.41667 7.11111 5.41667H7.81424C7.94792 5.41667 8.05556 5.52431 8.05556 5.65799C8.05556 5.74826 8.00521 5.82986 7.92708 5.87153L7.37153 6.16319C7.27951 6.21181 7.22222 6.30556 7.22222 6.40972V6.66667C7.22222 6.81944 7.34722 6.94444 7.5 6.94444C7.65278 6.94444 7.77778 6.81944 7.77778 6.66667V6.57812L8.18576 6.36458C8.44792 6.22743 8.61111 5.95486 8.61111 5.65972C8.61111 5.21875 8.25347 4.86285 7.81424 4.86285H7.11111C6.71181 4.86285 6.38889 5.18576 6.38889 5.58507V5.58333Z" fill="#1E1C1C"/>
                            </svg>
                        </div>
                        <span>Terms and Policies</span>
                    </a>
                    <ul id="termsList" style="display: none;">
                        <li><a href="https://tao.ai/privacy.php" target="_BLANK" class="term-item">Privacy Policy</a></li>
                        <li><a href="https://tao.ai/terms.php" target="_BLANK" class="term-item">Terms & Conditions</a></li>
                        <li><a href="https://tao.ai/conduct.php" target="_BLANK" class="term-item">Code of Conduct</a></li>
                    </ul>
                </div>-->

                <div class="dropdown terms-menu">

                    <a class="footer-menu-item mx-lg-5 dropdown-toggle removecaret text-wrap" id="termsLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" classss="" id="termsLink" style="cursor: pointer;">
                        <div class="svg-container">
                            <svg width="16" height="16" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 1.11111C0 0.498264 0.498264 0 1.11111 0H3.88889V2.22222C3.88889 2.52951 4.13715 2.77778 4.44444 2.77778H6.66667V3.44792C5.38368 3.81076 4.44444 4.98958 4.44444 6.38889C4.44444 7.41493 4.94965 8.32118 5.72396 8.87674C5.6684 8.88542 5.61285 8.88889 5.55556 8.88889H1.11111C0.498264 8.88889 0 8.39062 0 7.77778V1.11111ZM6.66667 2.22222H4.44444V0L6.66667 2.22222ZM7.5 3.88889C8.16304 3.88889 8.79893 4.15228 9.26777 4.62112C9.73661 5.08996 10 5.72585 10 6.38889C10 7.05193 9.73661 7.68782 9.26777 8.15666C8.79893 8.6255 8.16304 8.88889 7.5 8.88889C6.83696 8.88889 6.20107 8.6255 5.73223 8.15666C5.26339 7.68782 5 7.05193 5 6.38889C5 5.72585 5.26339 5.08996 5.73223 4.62112C6.20107 4.15228 6.83696 3.88889 7.5 3.88889ZM7.5 8.05556C7.61051 8.05556 7.71649 8.01166 7.79463 7.93352C7.87277 7.85538 7.91667 7.7494 7.91667 7.63889C7.91667 7.52838 7.87277 7.4224 7.79463 7.34426C7.71649 7.26612 7.61051 7.22222 7.5 7.22222C7.38949 7.22222 7.28351 7.26612 7.20537 7.34426C7.12723 7.4224 7.08333 7.52838 7.08333 7.63889C7.08333 7.7494 7.12723 7.85538 7.20537 7.93352C7.28351 8.01166 7.38949 8.05556 7.5 8.05556ZM6.38889 5.58333V5.69444C6.38889 5.84722 6.51389 5.97222 6.66667 5.97222C6.81944 5.97222 6.94444 5.84722 6.94444 5.69444V5.58333C6.94444 5.49132 7.0191 5.41667 7.11111 5.41667H7.81424C7.94792 5.41667 8.05556 5.52431 8.05556 5.65799C8.05556 5.74826 8.00521 5.82986 7.92708 5.87153L7.37153 6.16319C7.27951 6.21181 7.22222 6.30556 7.22222 6.40972V6.66667C7.22222 6.81944 7.34722 6.94444 7.5 6.94444C7.65278 6.94444 7.77778 6.81944 7.77778 6.66667V6.57812L8.18576 6.36458C8.44792 6.22743 8.61111 5.95486 8.61111 5.65972C8.61111 5.21875 8.25347 4.86285 7.81424 4.86285H7.11111C6.71181 4.86285 6.38889 5.18576 6.38889 5.58507V5.58333Z" fill="#1E1C1C"/>
                            </svg>
                        </div>
                        <span>Terms</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-item"><a href="https://tao.ai/privacy.php" target="_BLANK" class="term-item">Privacy Policy</a></li>
                        <li class="dropdown-item"><a href="https://tao.ai/terms.php" target="_BLANK" class="term-item">Terms & Conditions</a></li>
                        <li class="dropdown-item"><a href="https://tao.ai/conduct.php" target="_BLANK" class="term-item">Code of Conduct</a></li>
                    </ul>
                </div>

                <a  href="https://tao.ai" target="_blank" class="footer-menu-item mx-lg-5">
                    <svg width="30" height="30" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5341 4.42451L8.4495 4.39261C8.24982 5.34678 7.3313 10.9602 8.343 11.5608C9.20029 12.0685 9.89517 11.1648 10.4383 11.1303C11.1944 13.3868 6.82811 14.7556 6.5033 11.7788C6.30362 9.93955 7.55228 5.18199 7.34461 4.32882C5.92823 4.29161 4.07788 4.02849 4.06723 5.7242C4.0619 6.44447 4.46392 6.69697 3.72644 6.76873C2.77331 5.31489 3.48949 3.36934 4.76743 2.82448C5.75784 2.40454 9.26152 2.68627 10.7258 2.68627C11.2157 2.68627 12.2274 2.51882 12.5496 2.65438C13.1805 4.26769 11.7748 4.42451 10.5341 4.42451ZM16 8.02855C16 6.8777 15.6645 5.62851 15.3051 4.79129C14.9271 3.90888 14.3067 3.01319 13.6757 2.38859C11.4473 0.174598 7.80254 -0.73173 4.81269 0.668958C3.48949 1.28824 2.31272 2.17862 1.5007 3.40123C0.896336 4.31022 0.47568 5.04113 0.249378 6.17869C-0.0567951 7.71759 -0.0967307 8.04716 0.21743 9.62061C0.459706 10.8352 0.773867 11.4492 1.40485 12.4645C2.69344 14.535 5.45699 15.9436 7.94897 15.9995C8.31372 16.0101 9.29081 15.8613 9.65023 15.7815C12.1369 15.2446 14.1789 13.6021 15.2465 11.3057C15.4276 10.9176 15.6486 10.317 15.7684 9.81198C15.8616 9.40267 16 8.44052 16 8.02855Z" fill="white"/>
                    </svg>
                    <span>By Tao.ai</span>
                </a>
                <?php if(taoh_user_is_logged_in()){ ?>
                <a class="feedback-page footer-menu-item mx-lg-5" target="_blank" style="cursor:pointer;">
                    <div class="svg-container">
                        <svg width="16" height="16" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.25 0C0.560547 0 0 0.560547 0 1.25V8.75C0 9.43945 0.560547 10 1.25 10H6.25C6.93945 10 7.5 9.43945 7.5 8.75V8.37305C7.44727 8.39453 7.39453 8.41211 7.33984 8.42578L6.16602 8.71875C6.10742 8.73242 6.04883 8.74219 5.99023 8.74609C5.97266 8.74805 5.95508 8.75 5.9375 8.75H4.6875C4.56836 8.75 4.46094 8.68359 4.4082 8.57812L4.23633 8.23242C4.20312 8.16602 4.13672 8.125 4.06445 8.125C3.99219 8.125 3.92383 8.16602 3.89258 8.23242L3.7207 8.57812C3.66406 8.69336 3.54102 8.76172 3.41406 8.75C3.28711 8.73828 3.17773 8.65039 3.14258 8.5293L2.8125 7.44141L2.62109 8.08203C2.50195 8.47852 2.13672 8.75 1.72266 8.75H1.5625C1.39062 8.75 1.25 8.60938 1.25 8.4375C1.25 8.26562 1.39062 8.125 1.5625 8.125H1.72266C1.86133 8.125 1.98242 8.03516 2.02148 7.90234L2.3125 6.93555C2.37891 6.71484 2.58203 6.5625 2.8125 6.5625C3.04297 6.5625 3.24609 6.71484 3.3125 6.93555L3.53906 7.68945C3.68359 7.56836 3.86719 7.5 4.0625 7.5C4.37305 7.5 4.65625 7.67578 4.79492 7.95312L4.88086 8.125H5.05469C4.99414 7.95312 4.98242 7.76562 5.02734 7.58203L5.32031 6.4082C5.375 6.1875 5.48828 5.98828 5.64844 5.82812L7.5 3.97656V3.125H5C4.6543 3.125 4.375 2.8457 4.375 2.5V0H1.25ZM5 0V2.5H7.5L5 0ZM10.7383 2.72852C10.4336 2.42383 9.93945 2.42383 9.63281 2.72852L9.05859 3.30273L10.4453 4.68945L11.0195 4.11523C11.3242 3.81055 11.3242 3.31641 11.0195 3.00977L10.7383 2.72852ZM6.0918 6.26953C6.01172 6.34961 5.95508 6.44922 5.92773 6.56055L5.63477 7.73438C5.60742 7.8418 5.63867 7.95312 5.7168 8.03125C5.79492 8.10938 5.90625 8.14062 6.01367 8.11328L7.1875 7.82031C7.29688 7.79297 7.39844 7.73633 7.47852 7.65625L10.002 5.13086L8.61523 3.74414L6.0918 6.26953Z" fill="#1E1C1C"/>
                        </svg>
                    </div>
                    <span>Feedback</span>
                </a>
                <?php } ?>
                <?php if ( taoh_user_is_logged_in() && defined( 'TAOH_SITE_DONATE_ENABLE' ) && TAOH_SITE_DONATE_ENABLE ) { ?>
                <a href="<?php echo TAOH_SITE_URL_ROOT."/donate"; ?>" class="footer-menu-item mx-lg-5">
                    <div class="svg-container">
                        <svg width="16" height="16" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.68724 6.83475C2.65604 6.58123 3.45611 6.5761 4.86577 7.44936L6.74895 7.06267C7.03741 7.00377 7.20341 7.08316 7.30682 7.20864C7.37214 7.29059 7.39663 7.38534 7.37214 7.48522C7.34764 7.58509 7.28505 7.66192 7.18708 7.70801L5.1624 8.67346C4.98007 8.78358 5.10525 8.96797 5.2522 8.90394L7.57079 7.81045C7.66604 7.76435 7.7368 7.69777 7.78306 7.60558C7.83476 7.50314 7.91096 7.42119 8.01437 7.35205L9.89211 6.11515C10.3683 5.80016 10.7412 6.23807 10.5915 6.39684L8.29739 8.58383L5.8509 9.76952C5.52706 9.92573 5.20594 9.98207 4.844 9.9411L1.72534 9.61075C1.6056 9.59794 1.51579 9.50319 1.51579 9.39051V7.04987C1.51579 6.94743 1.58383 6.86292 1.68724 6.83475ZM5.85907 1.29557C5.86451 1.30581 5.87811 1.31349 5.89172 1.31349C5.90533 1.31349 5.91621 1.30581 5.92438 1.29557C6.45232 0.419748 7.26056 -0.256324 8.36815 0.0945161C9.21993 0.363408 9.83768 1.15472 9.83768 2.08944C9.83768 3.41598 8.3872 4.49411 7.4565 5.34944L6.14481 6.48647C6.00058 6.6094 5.78015 6.6094 5.63591 6.48647L4.32695 5.34944C3.39624 4.49411 1.94576 3.41598 1.94576 2.08944C1.94576 1.12911 2.59617 0.322434 3.48333 0.074029C4.56642 -0.228155 5.34745 0.447918 5.85907 1.29557ZM0.236757 6.85268H0.990571C1.1212 6.85268 1.22733 6.95255 1.22733 7.07291V9.55697C1.22733 9.80025 1.01506 10 0.756535 10H0.236757C0.106133 10 0 9.90013 0 9.7772V7.07291C0 6.95255 0.106133 6.85268 0.236757 6.85268Z" fill="black"/>
                        </svg>
                    </div>
                    <span>Donate</span>
                </a>
                <?php } ?>
            </div>

            <p class="text-center text-muted" style="color: #999999;">
              <strong style="color: #6C757D;">&copy; <?php echo date('Y'). "</strong> | <strong>".TAOH_SITE_NAME_SLUG."</strong> | "."<a href='https://theworkcompany.com' target='_blank' class='twc-logo' style='color: #fff;'>The<b>W</b><img src='https://theworkcompany.com/assets/images/theworkcompany_sq.png' alt='O' height='14'><b style='color: #fff;'>RK</b>Company</a>"; ?> | <strong style="color: #6C757D;">All Rights Reserved</strong>
              <br>
            </p>
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
            <p>Trouble Viewing the Page? Please refresh or try a different browser for a better experience. We apologize for the inconvenience!</p>
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

/* use specific modal class if needed
    .modal-lg {
    max-width: 80% !important;
    display: table-cell;
    vertical-align: middle;
}*/

    .vertical-alignment-helper {
    display:table;
    height: 100%;
    pointer-events:none;
    margin: auto;
}

    .vertical-align-center {
    /* To center vertically */
    display: table-cell;
    vertical-align: middle;
    pointer-events:none;
}

    .modal-content {
    /* Bootstrap sets the size of the modal in the modal-dialog class, we need to inherit it */
    width:inherit;
    max-width:inherit; /* For Bootstrap 4 - to avoid the modal window stretching
    full width */
    height:inherit;
    /* To center horizontally */
    margin: 0 auto;
    pointer-events:all;}
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
    // Get the elements
    const termsLink = document.getElementById('termsLink');
    const termsList = document.getElementById('termsList');
    const termItems = document.querySelectorAll('.term-item');

    // Toggle visibility of the terms list when the anchor is clicked
    termsLink.addEventListener('click', (event) => {
        event.preventDefault(); // Prevent default anchor behavior
        termsList.style.display = (termsList.style.display === 'none' || termsList.style.display === '') ? 'block' : 'none';
    });

    // Hide the terms list when any of the list items is clicked
    termItems.forEach(item => {
        item.addEventListener('click', () => {
            termsList.style.display = 'none'; // Hide the list
        });
    });


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
        if (localStorage.getItem('indexedDBFailed') === 'true') {
            if (parseInt(localStorage.getItem('indexedDBFailRetry')) < 3) {
                let indexedDBFailRetry = parseInt(localStorage.getItem('indexedDBFailRetry')) + 1;
                localStorage.setItem('indexedDBFailRetry', indexedDBFailRetry);
                location.reload();
            } else {
                localStorage.setItem('indexedDBFailRetry', 0);
                showIndexedDBWarning();
            }
        }
    });


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

    function checksuperadminInit(){
        <?php if(isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->is_super_admin) &&
        taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->is_super_admin == 1 &&
        taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->site_status == 'init'){ ?>
            var msg = 'Please complete your site settings. Click Manage Button on the header menu and proceed to fill the site data.';
            taoh_set_error_message(msg,8000);
        <?php } ?>
    }


    function taoh_metrix_ajax(app,arr_cont) {
        $.each(arr_cont, function(i, v){
            save_metrics(app,'view',v);
        });
    }

    function save_metrics(app,metrics_type,conttoken){
        var store_name = METIRCSStore;

        var metrics = {
            "conttoken": conttoken,
            "met_type" : app,
            "ptoken": '<?php echo isset($session_data['USER_INFO']) ? $session_data['USER_INFO']->ptoken : '' ; ?>',
            'met_action': metrics_type,
            'time': Date.now(),
            'secret':'<?php echo TAOH_API_SECRET; ?>',
            'type': 'metrics',
        }

        let metrics_setting_time = Date.now()+'_'+conttoken;
        //metrics_setting_time = metrics_setting_time.setMinutes(metrics_setting_time.getMilliseconds());

        let name = app+'_'+metrics_setting_time;
        var metrics_data = { taoh_data:name,values : metrics };

        obj_data = { [store_name]:metrics_data };
       // console.log('--1---store_name---------',store_name);
       // console.log('--1---metrics_data---------',metrics_data);
        Object.keys(obj_data).forEach(key => {
            console.log(key, obj_data[key]);
            IntaoDB.setItem(key,obj_data[key]).catch((err) => console.log('Storage failed', err));
        });
        return false;
    }

    var mertricsLoad = function () {
        $('.dash_metrics').each(function (f) {
            var conttoken = $(this).attr("conttoken");
            var metrics = $(this).attr("data-metrics");
            var app = $(this).attr("data-type");
            if (metrics == '') {
                metrics = 'view';
            }

            save_metrics(app, metrics, conttoken);


        });
    }

    function moveMetricstoRedis() {
        var store_name = METIRCSStore;

        const MetricsStoreName = METIRCSStore;
        let metricsPush = [];
        getIntaoDb(dbName).then((db) => {
            if (db.objectStoreNames.contains(MetricsStoreName)) {
                const request = db.transaction(MetricsStoreName).objectStore(MetricsStoreName).getAll();

                request.onsuccess = () => {
                    const metricsData = request.result;

                    //console.log('----metricsData-----',metricsData);
                    if (metricsData && metricsData.length > 0) {
                        metricsData.forEach((data) => {
                            let metrics_data = data.values;
                            let metrics_key = data.taoh_data;
                            //console.log('----metrics_key-----',metrics_key);

                            metricsPush.push(data.values);
                            IntaoDB.removeItem(store_name, data.taoh_data).then(() => console.log('Item Removed')).catch((err) => console.log('Storage failed', err));


                        });

                        //console.log('----metricsPush-----',metricsPush);

                        var data = {
                            'taoh_action': 'toah_metrics_push',
                            'metrics_data': JSON.stringify(metricsPush),

                        };
                        jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function (response) {
                            //success
                        }).fail(function () {
                            console.log("Network issue!");
                        })

                    }
                }

                //console.log('----metricsPush-----',metricsPush);
            }
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
        $("#toast_container").removeClass("toast-middle-con");
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


if ( ! isset( $_COOKIE[ 'client_time_zone' ] ) && stristr( $_SERVER[ 'REQUEST_URI' ], '/events/' ) ){
    header("Location: ".$_SERVER[ 'REQUEST_URI' ]); taoh_exit();
}

taoh_cacheops('logpush');
taoh_exit();

?>
