<?php

$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//$share_link = "https://dev.unmeta.net/hires/jobs";
$fb_share = "http://www.facebook.com/sharer.php?
s=100
&p[url]= ".$share_link."
&p[images][0]=".( defined( 'TAO_PAGE_IMAGE' )? TAO_PAGE_IMAGE : '' )."
&p[title]=".( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : 'Hires' )."
&p[summary]=".(urlencode( defined( 'TAO_PAGE_DESCRIPTION' )? substr(TAO_PAGE_DESCRIPTION, 0, 240) : '' ))."";

$tw_share = "https://twitter.com/intent/tweet?text=".( urlencode( ( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' ) ) )."&url=$share_link";

//$link_share = "http://www.linkedin.com/shareArticle?mini=true&url=".$share_link."&title=".(defined( 'TAO_PAGE_TITLE')?TAO_PAGE_TITLE : '' ) ."&summary=".( defined( 'TAO_PAGE_DESCRIPTION' ) ? substr(TAO_PAGE_DESCRIPTION, 0, 240) : '' )."";

$link_share = "http://www.linkedin.com/shareArticle?mini=true&url=".$share_link."&title=".(defined( 'TAO_PAGE_TITLE') ? TAO_PAGE_TITLE : '' )."&summary=".( defined( 'TAO_PAGE_DESCRIPTION' ) ? substr(TAO_PAGE_DESCRIPTION, 0, 240) : '' );

$email_share = "mailto:?subject=I wanted you to see this site&amp;body=Check out this site ".( defined( 'TAO_PAGE_TITLE' )? TAO_PAGE_TITLE : '' )." $share_link.";
?>
<style>
    .your-tooltip {
    opacity: 0;
    position: absolute;
    bottom: 50px;
    left: 80px;
    transition: all .3s;
    }

    .your-tooltip.show {
    opacity: 1;
    }
</style>

                <div class="card card-item p-4">
                    <div class="social-icon-box">
                        <div class="embed-responsive shadow-4-strong">
                            <h5 class="mb-4 pb-4 border-bottom">Copy &amp; Share Link</h5>
                            <form action="#" class="copy-to-clipboard d-flex flex-wrap align-items-center">
                            <div class="your-tooltip">Copied!</div>
                                <div class="input-group">
                                    <input type="text" id="copy-text" class="form-control form--control form--control-bg-gray copy-input" value="<?php echo $share_link; ?>">
                                    <div class="input-group-append">
                                        <button type="button" onclick="copyText()" class="btn theme-btn copy-btn"><i class="la la-copy mr-1"></i> Copy</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="embed-responsive shadow-4-strong">
                            <br />
                            <h5 class="mb-4 pb-4 border-bottom">Share on Social Media</h5>
                            <div class="social-icon-box">
                                <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="<?php echo $fb_share; ?>" target="_blank" title="Share on Facebook">
                                <svg focusable="false" class="svg-inline--fa fa-facebook-f fa-w-10" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg>
                                </a>
                                <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="<?php echo $tw_share; ?>" target="_blank" title="Share on Twitter">
                                <svg focusable="false" class="svg-inline--fa fa-twitter fa-w-16" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>
                                </a>
                                <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="<?php echo $link_share; ?>" target="_blank" title="Share on Linkedin">
                                <svg focusable="false" class="svg-inline--fa fa-linkedin fa-w-14" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"></path></svg>
                                </a>
                                <a class="mr-1 icon-element icon-element-sm shadow-sm text-gray hover-y d-inline-block" href="<?php echo $email_share; ?>" target="_blank" title="Share vai Email">
                                <svg focusable="false" class="svg-inline--fa fa-envelope fa-w-16" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
<script>
function copyText() {
    var yourToolTip = document.querySelector('.your-tooltip');
    /* Select text area by id*/
    var Text = document.getElementById("copy-text");
    /* Select the text inside text area. */
    Text.select();
    /* Copy selected text into clipboard */
    var copy_text = navigator.clipboard.writeText(Text.value);
    if(copy_text){
    $('.your-tooltip').addClass('show');
    setTimeout(function() {
        $('.your-tooltip').removeClass('show');
    }, 2000)
    }
}
</script>
