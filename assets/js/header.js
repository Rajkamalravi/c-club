var livenowbuttonInterval = null;
var activeuserInterval = null;

document.addEventListener('DOMContentLoaded', function () {
    /* Toggle the visibility of the my network dropdown */
    let toggleLink = document.getElementById('toggleNetworkDropdown');
    let submenu = document.getElementById('myNetworkMenu');


    if(toggleLink){
        toggleLink.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          if(submenu)
          submenu.classList.toggle('d-none');

          let icon = toggleLink.querySelector('.la-angle-down');

          if(icon){
            icon.classList.toggle('la-angle-up');
            icon.classList.toggle('la-angle-down');
          }

      });
    }

    if(submenu){
         // prevent submenu clicks from closing the main dropdown
        submenu.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }


    /* /Toggle the visibility of the my network dropdown */

});

$(document).ready(function () {
    addActiveUserPtoken();
    checkLiveNowButtonVisibility();
    livenowbuttonInterval = setInterval(() => checkLiveNowButtonVisibility(), 60000);
    activeuserInterval = setInterval(() => addActiveUserPtoken(), 240000);
});

const logged_user_data = window._hdr_cfg.dataApi;

function getCookieData(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

if (window._hdr_cfg.hasTempApiToken) {
    var myCustomData = '2';
    var event = new CustomEvent('myEvent', {detail: myCustomData})
    window.parent.document.dispatchEvent(event);
}

window.document.addEventListener('myEvent', handleEvent, false)

function handleEvent(e) {
    if (e.detail == 1) {
        $('#loaderIframe').hide();
    }
    if (e.detail == 2) {
        $('#loaderIframe').hide();
        let back_url = getCookieData(window._hdr_cfg.rootPathHash + '_referral_back_url');
        window.location.href = (back_url != undefined && back_url != '') ? back_url : window._hdr_cfg.siteUrlRoot;
    }
}

function openNav() {
    document.getElementById("mySidebar").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
    document.getElementById("mySidebar").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
}

$(document).on('click', '.support-page', function () {
    window.location = window._hdr_cfg.supportPageUrl;
});

$(document).on('click', '#live_join_now_btn', function () {
    $('#liveNowModal').modal('hide');
});

$(document).on('click', '.live_now_btn', function () {
    $('#liveNowModalContent').awloader('show');
    $('#liveNowModal').modal('show');

    $.ajax({
        url: window._hdr_cfg.siteUrlRoot + '/livenow/live_now_data',
        type: 'GET',
        dataType: 'json',
        data: {},
        beforeSend: function() {
        },
        success: function (response) {
            if (response.success) {
                const liveNowData = response.live_now_data;
                const joinNowUrl = response.join_now_url;

                $('#liveNowModal .modal-header').text(liveNowData?.title);
                $('#live_join_now_btn').attr('href', joinNowUrl);
                $('#liveNowModalContent').awloader('hide');
            } else {
                $('#liveNowModal').modal('hide');
                taoh_set_error_message('No Live Now data found');
            }
        },
        error: function (xhr, status, err) {
            $('#liveNowModal').modal('hide');
            taoh_set_error_message('Error Fetching Live Now data');
            console.error('Error Fetching Live Now data : ' + err);
        }
    });

});

$(document).on('click', '.feedback-page', function () {
    var usert = window._hdr_cfg.userPtoken;
    if (usert) {
        window.location = window._hdr_cfg.feedbackUrl;
    } else {
        window.location = window._hdr_cfg.supportPageUrl;
    }
});


function chk_validations(id) {
    var req = 0;
    $(id + ' .validate').each(function (index) {
        $(this).removeClass('error_class');
        if ($(this).val() == '' || ($(this).is('select') && $(this).val().trim() == '')) {
            $(this).addClass('error_class');
            req = 1;
            $(this).keyup(function () {
                $(this).removeClass('error_class');
            });
        }
    });

    return req != 1;
}

function validateEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function configSubmit(formid) {
    if (chk_validations(formid)) {
        var email = $(formid + ' #iemail').val();
        var weCode = $(formid + ' #weCode').val();
        var weWord = $(formid + ' #captcha').val();
        let errorMessage = $(".errorMessage");
        let loadText = $("#loadingText");
        loadText.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`);
        if (!email) {
            $(formid + ' #emailerror_msg').html("Email Required");
            $(formid + ' #iemail').addClass('error_class');
            loadText.html(`Submit`);
            return false;
        } else if (!captcha) {
            errorMessage.append(`
      <span class="text-danger" id="errorCaptcha">Captcha Required</span>
    `);
            loadText.html(`Submit`);
            return false;
        }
        if (!validateEmail(email)) {
            $(formid + ' #emailerror_msg').html("Please Enter Valid Email");
            $(formid + ' #iemail').addClass('error_class');
            $("#loader").hide();
            loadText.html(`Submit`);
            return false;
        } else {
            $(formid + ' #emailerror_msg').html("");
        }
        var data = {
            'taoh_action': 'check_captcha',
            'we_code': weCode,
            'we_word': weWord,
            'email': email,
            'slug': '',
            'app': '',
            'site_title': window._hdr_cfg.siteTitle,
        };

        if (email && captcha) {
            jQuery.post(window._hdr_cfg.ajaxUrl, data, function (response) {
                console.log('response', response);
                if (response != 1) {
                    errorMessage.html(`
           <span class="text-danger" id="errorCaptcha">Captcha Invalid</span>
         `);
                    loadText.html(`Submit`);
                    return false;
                } else {
                    $('#config-modal').modal('hide');
                    $("#loader").hide();
                    $(formid + ' #iemail').val('');
                    $(formid + ' #captcha').val('');
                    window.open(window._hdr_cfg.loginUrl, '_self');
                    var data = {
                        'taoh_action': 'taoh_invite_friend',
                        'first_name': '',
                        'last_name': '',
                        'email': email,
                        'comment': '',
                        'from_link': window.location.href,
                        'network_title': window._hdr_cfg.networkTitle,
                        'app_name': '',
                        'referral_link': window._hdr_cfg.loginUrl,
                    };
                    jQuery.post(window._hdr_cfg.ajaxUrl, data, function (response) {
                        $("#loader").hide();
                        if (response == 1) {
                        } else {
                            $("#error_msg").html("Sorry, Something went wrong. Please try again.");
                        }
                    });

                }
            });
        }
    }
}

if(document.getElementById("myHeader")){


   var header = document.getElementById("myHeader");
   var sticky = header.offsetTop;

    window.onload = function() {
        myFunction();
    };

     window.onscroll = function () {
      myFunction();
  };
}


function myFunction() {
    if (window.pageYOffset > sticky) {
        header.classList.add("fixed-header");
        header.classList.add("fixed-header");
    } else {
        header.classList.remove("fixed-header");
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
    dropdowmMenu.parent('li').children('a').append(function () {
        return '<button class="sub-nav-toggler" type="button"><i class="la la-angle-down"></i></button>';
    });

    /*=========== Sub menu ============*/
    var dropdowmMenu = $('.off-canvas-menu-list .sub-menu');
    dropdowmMenu.parent('li').children('a').append(function () {
        return '<button class="sub-nav-toggler" type="button"><i class="la la-angle-down"></i></button>';
    });

});

// Time display
function updateTime(timezone) {
    const options = {timeZone: timezone, hour: '2-digit', minute: '2-digit'};
    const timeString = new Date().toLocaleTimeString('en-US', options);
    const dateString = new Date().toLocaleString('en-CA', { timeZone: timezone, hour12: false });

    const timeSpan = document.getElementById("time_span");
    if (timeSpan) {
        timeSpan.innerHTML = '<i class="fas fa-clock small" date-time="' + dateString + '"></i><a title="Based on ' + timezone + ' Timezone, visit settings page to change your timezone" href="' + window._hdr_cfg.siteUrlRoot + '/settings" style="color: #212121;"> ' + timeString + ' </a>';
    }
}

// Use session timezone or fallback to browser timezone
const h_userTimezone = window._hdr_cfg.userTimezone || Intl.DateTimeFormat().resolvedOptions().timeZone;

// Update time initially and every second
updateTime(h_userTimezone);
setInterval(() => updateTime(h_userTimezone), 1000);

function deleteJobsCache() {
}

function deleteAsksCache() {
}

function deleteEventsCache() {
}

function addActiveUserPtoken() {
    let data = {
        ops: 'livenow',
        action: 'add_user',
        code: _taoh_ops_code,
        key: window._hdr_cfg.sessionPtoken,
    };

    $.ajax({
        url: _taoh_cache_chat_url,
        type: 'GET',
        dataType: 'json',
        data: data,
        success: function (response, textStatus, jqXHR) {
            if (response.success) {
                console.log("User add response", response);
            }
        },
        error: function (xhr, status, err) {
            console.error('Error Fetching ChannelList : ' + err);
        }
    });
}

function checkLiveNowButtonVisibility() {
    let data = {
        ops: 'livenow',
        action: 'show_live_button',
        code: _taoh_ops_code,
        key: window._hdr_cfg.sessionPtoken,
    };

    $.ajax({
        url: _taoh_cache_chat_url,
        type: 'GET',
        dataType: 'json',
        data: data,
        success: function (response, textStatus, jqXHR) {
            if (response.success) {
                if(response.show_live_button) {
                  $('.live_now_btn').parent('li').show();
                } else {
                  $('.live_now_btn').parent('li').hide();
                }
            }
        },
        error: function (xhr, status, err) {
            console.error('Error : ' + err);
        }
    });
}

/*
const newUrl = new URL(location.href);
newUrl.searchParams.delete('action_jobs');
newUrl.searchParams.delete('action_asks');
newUrl.searchParams.delete('action_events');
newUrl.searchParams.delete('cs');
window.history.replaceState({}, document.title, newUrl.href);
*/


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
