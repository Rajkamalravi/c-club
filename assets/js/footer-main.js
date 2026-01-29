// Job Post Modal - lazy inject only when needed
(function(){
    if(localStorage.getItem('show_jobPostModal') != 1) return;
    var c = document.getElementById('jobPostModalContainer');
    if(!c) return;
    var sp = _taoh_site_url_root + '/assets/icons/sprite.svg';
    var svgUse = function(name, w, cls) {
        return '<svg class="icon icon-' + name + (cls ? ' ' + cls : '') + '" width="' + w + '" height="' + w + '" fill="currentColor"><use href="' + sp + '#icon-' + name + '"></use></svg>';
    };
    c.innerHTML = '<div class="modal fade post-option" id="jobPostModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
        '<div class="modal-dialog bg-white mx-auto" role="document">' +
            '<div class="modal-content">' +
                '<div class="modal-header bg-white justify-content-end">' +
                    '<button type="button" class="btn" data-dismiss="modal" aria-label="Close">' + svgUse('close', 9) + '</button>' +
                '</div>' +
                '<div class="modal-body d-flex flex-wrap justify-content-center align-items-start">' +
                    '<div class="d-flex justify-content-between w-100">' +
                        '<div>' + svgUse('jp-confetti-left', 202, 'job-post-svg-lg') + '</div>' +
                        '<div>' + svgUse('jp-badge', 149, 'job-post-svg-md') + '</div>' +
                        '<div>' + svgUse('jp-confetti-right', 202, 'job-post-svg-lg') + '</div>' +
                    '</div>' +
                    '<div class="d-flex flex-column align-items-center mb-4 text-center">' +
                        '<h6 class="setting-sm-text mb-4">Thanks! Your Profile Settings is now complete!</h6>' +
                        '<h3 class="setting-lg-text mb-2">Actively Hiring? Find Top Talent Here!</h3>' +
                        '<h5 class="setting-md-text mb-4">Post a free Job and get a Hiring badge </h5>' +
                        '<button type="button" class="btn s-btn setting-post-btn" id="postJobButton">Post a Free Job</button>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
    $('#jobPostModal').modal('show');
})();

if (document.getElementById('termsLink')) {
    const termsLink = document.getElementById('termsLink');
    const termsList = document.getElementById('termsList');
    const termItems = document.querySelectorAll('.term-item');

    termsLink.addEventListener('click', (event) => {
        event.preventDefault();
        if (termsList) {
            termsList.style.display = (termsList.style.display === 'none' || termsList.style.display === '') ? 'block' : 'none';
        }
    });

    if (termItems) {
        termItems.forEach(item => {
            item.addEventListener('click', () => {
                if (termsList) termsList.style.display = 'none';
            });
        });
    }
}

var loopTime = window._ft_loopTime || 30000;
let profileRSLiveStatusInterval;

$(document).ready(function () {

    if (window._ft_enableReferralCheck) {
        if (!ft_isLoggedIn) {
            setInterval(function () {
                checkReferralStatus();
            }, 60000);
        }
    }

    if (ft_isLoggedIn) {
        setInterval(function () {
            moveMetricstoRedis();
        }, 10000);
    }

    if (ft_isLoggedIn && window._ft_notificationEnabled && window._ft_notificationStatus == 2) {
        setTimeout(function () {
            taoh_notification_init(1);
        }, 3000);

        setInterval(function () {
            taoh_notification_init(0);
        }, loopTime);
    }

    if (ft_isLoggedIn) {
        if (!window._ft_isSettingsPage) {
            setInterval(function () {
                checkProfileCompletion();
            }, 60000);
        }

        checksuperadminInit();
        setInterval(function () {
            checksuperadminInit();
        }, 60000);

        savetaodata();
        setInterval(function () {
            if (typeof index_name !== 'undefined') checkTTL(index_name);
            savetaodata();
        }, 10000);
    }

    var currentUrl = window.location.href;
    var visitedUrls = JSON.parse(localStorage.getItem('visitedUrls')) || [];
    visitedUrls.push(currentUrl);
    if (visitedUrls.length > 5) {
        visitedUrls.shift();
    }
    localStorage.setItem('visitedUrls', JSON.stringify(visitedUrls));

    $(document).on('click', '#bugsubmit', function (e) {
        e.preventDefault();
        if ($("#bugForm").valid()) {
            const formData = new FormData($("#bugForm")[0]);
            formData.append('taoh_action', 'taoh_report_bug');
            var currentUrl = window.location.href;
            var visitedUrls = JSON.parse(localStorage.getItem('visitedUrls')) || [];
            formData.append('visited_url', visitedUrls);
            formData.append('current_url', currentUrl);

            let submit_btn = $(this);
            submit_btn.prop('disabled', true);

            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function (response) {
                    if (response.success) {
                        $("#reportBugModal").modal("hide");
                        $("#reportBugModal").hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        document.getElementById("bugForm").reset();
                        submit_btn.prop('disabled', false);
                        taoh_set_success_message('<h5>Thanks!</h5>Issue report submitted successfully.', false);
                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    });

    $('#contactusModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var email = button.data('email');
        $('#contact_email').val(email);
        var addtitle = button.data('addtitle') || '';
        $('#contact_addtitle').val(addtitle);
    });

    $(document).on('click', '#contactusSubmit', async function (e) {
        e.preventDefault();
        let to_email = $('#contact_email').val();
        if (to_email == '') {
            taoh_set_error_message('Error on sending email. Please check after sometime', false);
            return false;
        } else {
            if ($("#contactusForm").valid()) {
                const formData = new FormData($("#contactusForm")[0]);
                formData.append('taoh_action', 'taoh_contact_us');
                formData.append('eventtoken', eventToken);
                formData.append('to_email', to_email);

                let submit_btn = $(this);
                submit_btn.prop('disabled', true);

                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (response) {
                        if (response.success) {
                            $("#contactusModal").modal("hide");
                            document.getElementById("contactusForm").reset();
                            submit_btn.prop('disabled', false);
                            taoh_set_success_message('Thanks! Mail sent successfully.', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }
        }
    });
});


function checksitemap() {
    if (window._ft_sitemapNeeded) {
        jQuery.get(_taoh_site_url_root + '/sitemap', {
            'taoh_action': 'taoh_sitemap_call',
        }, function (response) {
            // sitemap generated
        }).fail(function () {
            console.log("Network issue!");
        });
    }
}

function checkProfileCompletion() {
    if (window._ft_profileIncomplete) {
        if (typeof showBasicSettingsModal === 'function') {
            const $completeModal = $('#completeSettingsModal');
            if ($completeModal.length === 0 || !$completeModal.hasClass('show')) {
                showBasicSettingsModal();
            }
        }
    }
}

function checksuperadminInit() {
    if (window._ft_isSuperAdminInit) {
        var msg = 'Please complete your site settings. Click Manage Button on the header menu and proceed to fill the site data.';
        taoh_set_error_message(msg, 8000);
    }
}

function checkReferralStatus() {
    if (window._ft_hasReferralCookie) {
        $('#toast').toast('show');
        $('#toast').show();
        $("#toast").addClass("toast_active");
        var msg = "Sorry, You haven't logged in the site. You will be redirecting in few secs.";
        $("#toast_error").html("<div class='toasterror_class'><span><i class='las la-exclamation-circle info_icon'></i> " + msg + "&nbsp;<span class='toast_dismiss' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</span></span></div>");

        setTimeout(function () {
            $("#toast").removeClass("toast_active");
            $("#toast_container").removeClass("toast-middle-con");
            $("#loader").hide();
            localStorage.setItem('email', '');
            $('#config-modal').modal({show: true});
        }, 8000);
    }
}

function updateStatus(process) {
    if (!ft_isLoggedIn) return;

    $('#userMenuDropdownarea').addClass('stay_open');

    var my_status = $('#my_status').val();

    if (my_status == '') {
        return false;
    }
    if (process == 0) {
        $('#my_status').val('');
        my_status = '';
    }
    if (my_status != '') {
        $('#status_save').hide();
        $('#status_remove').show();
    } else {
        $('#status_save').show();
        $('#status_remove').hide();
    }
    var data = {
        'taoh_action': 'taoh_update_status',
        "process": process,
        "my_status": my_status,
        "ptoken": window._ft_ptoken || '',
    };

    jQuery.post(_taoh_site_ajax_url, data, function (response) {
        // status updated
    });

    setTimeout(function () {
        $('#userMenuDropdownarea').removeClass('stay_open');
    }, 5000);
}

function taoh_counter_init(call_at) {
    if (!ft_isLoggedIn || !window._ft_notificationEnabled) return;

    $('#badge_count').hide();
    $('#badge_count').html('');
    $('.notification_row').removeClass('bold');

    if (!ft_isLoggedIn) return;

    var data = {
        'taoh_action': 'taoh_get_notification_counter',
        'mod': 'core',
        'ops': "get",
        "type": "notify",
        "token": window._ft_apiToken || '',
        "ptoken": window._ft_ptoken || '',
        "call_at": call_at
    };
    jQuery.post(_taoh_site_ajax_url, data, function (response) {
        data = response;
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
}

if (ft_isLoggedIn && window._ft_notificationEnabled) {
    function taoh_notification_init(call_from) {
        call_from = call_from || 0;
        var data = {
            'taoh_action': 'taoh_get_notification',
            'mod': 'notify',
            'ops': "webnotify",
            "type": "notify",
            "token": window._ft_apiToken || '',
            "ptoken": window._ft_ptoken || '',
            "call_from": 0,
        };
        jQuery.post(_taoh_site_ajax_url, data, function (response) {
            data = response;
            if (data.status) {
                if (data.total_num > 0) {
                    $('#notifications-list').css('height', '250px');
                    render_notification_list_template(data.output, call_from);
                    if (!call_from) {
                        $('#badge_count').show();
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
        if (call_from == 1) {
            $('#notifications-list').html(notification_data);
        } else {
            $('#notifications-list').prepend(notification_data);
        }
    }
}

$(document).on('click', '.media_share', function (event) {
    var click = $(this).attr("data-click");
    var dataconttoken = $(this).attr("data-gconntoken");
    save_metrics('jobs', 'share', dataconttoken);
});

function taoh_metrix_ajax(app, arr_cont) {
    $.each(arr_cont, function (i, v) {
        save_metrics(app, 'view', v);
    });
}

function save_metrics(app, metrics_type, conttoken) {
    var store_name = METIRCSStore;

    var metrics = {
        "conttoken": conttoken,
        "met_type": app,
        "ptoken": window._ft_ptoken || '',
        'met_action': metrics_type,
        'time': Date.now(),
        'secret': window._ft_apiSecret || '',
        'type': 'metrics',
    };

    let metrics_setting_time = Date.now() + '_' + conttoken;
    let name = app + '_' + metrics_setting_time;
    var metrics_data = {taoh_data: name, values: metrics};

    obj_data = {[store_name]: metrics_data};
    Object.keys(obj_data).forEach(key => {
        IntaoDB.setItem(key, obj_data[key]).catch((err) => console.log('Storage failed', err));
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
};

function moveMetricstoRedis() {
    var store_name = METIRCSStore;
    const MetricsStoreName = METIRCSStore;
    let metricsPush = [];
    getIntaoDb(dbName).then((db) => {
        if (db.objectStoreNames.contains(MetricsStoreName)) {
            const request = db.transaction(MetricsStoreName).objectStore(MetricsStoreName).getAll();

            request.onsuccess = () => {
                const metricsData = request.result;
                if (metricsData && metricsData.length > 0) {
                    metricsData.forEach((data) => {
                        metricsPush.push(data.values);
                        IntaoDB.removeItem(store_name, data.taoh_data).catch((err) => console.log('Storage failed', err));
                    });

                    var data = {
                        'taoh_action': 'toah_metrics_push',
                        'metrics_data': JSON.stringify(metricsPush),
                    };
                    jQuery.post(_taoh_site_ajax_url, data, function (response) {
                        //success
                    }).fail(function () {
                        console.log("Network issue!");
                    });
                }
            };
        }
    });
}

window.onload = function () {
    setTimeout(mertricsLoad, 8000);
};

function triggerNextRequest(callback, ttl) {
    ttl = ttl || 3000;
    setTimeout(callback, ttl);
}

$(document).on("click", '.toast_dismiss', function () {
    $("#toast").removeClass("toast_active");
    $("#toast_container").removeClass("toast-middle-con");
});

if (window._ft_clearConfig) {
    const newUrl = new URL(location.href);
    newUrl.searchParams.delete('clear');
    window.history.replaceState({}, document.title, newUrl.href);
}

$('.li-modal').on('click', function (e) {
    e.preventDefault();
    $('#theModal').modal('show').find('.modal-content').load($(this).attr('href'));
});

$('.login-button').click(function (e) {
    var locc = $(location).attr('href');
    var days = 1;
    var name = _taoh_root_path_hash + '_referral_back_url';
    var value = locc;
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
    localStorage.setItem('email', '');
    $('#config-modal').modal({show: true});
});

$(document).on('click', '.login-button1', function (e) {
    // login button1 handler
});

if (localStorage.getItem('show_jobPostModal') == 1) {
    $('#jobPostModal').modal('show');
    localStorage.setItem('show_jobPostModal', 0);
}

$('#postJobButton').click(function () {
    window.location.href = _taoh_site_url_root + '/jobs/post';
});

$(document).on('show.bs.modal', '#config-modal', function (e) {
    $('#isCodeSent').hide();
    $('#isCodeNotSent').show();
    localStorage.setItem('isCodeSent', 'false');
    if (!e.relatedTarget) return;

    const key = _taoh_root_path_hash + '_referral_back_url';
    let value = $(e.relatedTarget).data('location');
    if (key && value) {
        const expires = new Date(Date.now() + 86400000).toUTCString();
        document.cookie = `${key}=${value}; expires=${expires}; path=/`;
    }
});

const formatObjectAndReunOnlyValue = (obj) => {
    if (typeof obj !== "undefined" && typeof obj === "object") {
        return Object.entries(obj).map(([key, value]) => {
            if (typeof value === "string" && value.includes(":>")) {
                const [slug, name] = value.split(":>");
                return name;
            } else {
                if (value['id'] != undefined)
                    return value['name'];
                else
                    return value['value'];
            }
        });
    }
    return {};
};

function get_to_date() {
    var currentDate = new Date();
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    return month + "-" + day + "-" + year;
}

var date_lat_intao_delete = localStorage.getItem('date_lat_intao_delete');
var current_date = get_to_date();
if (date_lat_intao_delete == '' || date_lat_intao_delete == null) {
    deleteIntaoData();
    localStorage.setItem('date_lat_intao_delete', current_date);
} else if (date_lat_intao_delete != current_date) {
    deleteIntaoData();
    localStorage.setItem('date_lat_intao_delete', current_date);
}

function deleteIntaoData() {
    var db_version = parseInt(_intao_db_version) || 1;
    var db_name = 'intaodb_' + _taoh_plugin_name;
    notifyRecreateIntaoDb(db_name, db_version);
    recreateIntaoDb(db_name, db_version);
}

function checkReportHumanCheckbox() {
    if ($('#human_report').is(':checked')) {
        $('#human_report').val(1);
        $('#bugsubmit').animate({
            width: '200px'
        }, 2000, function () {
            $('#bugsubmit').attr({'disabled': false});
        });
    } else {
        $('#human_report').val(0);
    }
}

function checkdojotracker() {
    // placeholder for dojo tracker check
}

function isGoalStale(timestamp) {
    let reaffirm_interval_days = 7;
    if (!timestamp) return true;
    const daysElapsed = (Date.now() - new Date(timestamp).getTime()) / (1000 * 60 * 60 * 24);
    return daysElapsed > reaffirm_interval_days;
}

async function shouldPromptGoal() {
    try {
        var existing = await IntaoDB.getItem(objStores.dojo_store.name, objStores.dojo_store.name);
        if (!existing) {
            jQuery.get(_taoh_site_ajax_url, {
                'taoh_action': 'check_dojo_goal',
            }, function (response) {
                res = response;
                if (res.dojo_goal != undefined) {
                    var payload = res.dojo_goal;
                    IntaoDB.setItem(objStores.dojo_store.name, payload);
                    existing = payload;
                }
            }).fail(function () {
                console.log("Network issue!");
            });
        }
        return !existing || isGoalStale(existing.timestamp);
    } catch (e) {
        return true;
    }
}

function showGoalModal() {
    document.getElementById("goalModal").style.display = "block";
}

document.getElementById("closeGoalModal").addEventListener("click", function () {
    document.getElementById("goalModal").style.display = "none";
});

function hideGoalModal() {
    document.getElementById("goalModal").style.display = "none";
}

document.getElementById("submitGoalBtn").addEventListener("click", async () => {
    const selected = document.querySelector('input[name="user_goal"]:checked');
    if (!selected) {
        alert("Please select a goal");
        return;
    }
    let savetime = Date.now();
    const goal = selected.value;
    const payload = {
        taoh_dojo_goal: objStores.dojo_store.name,
        values: {
            goal,
            success: true,
            timestamp: savetime,
            output: "Goal saved"
        },
        timestamp: savetime
    };

    try {
        IntaoDB.setItem(objStores.dojo_store.name, payload);
        hideGoalModal();
        var data = {
            'taoh_action': 'update_dojo_tracker_status',
            'mod': 'core',
            "token": window._ft_apiToken || '',
            "ptoken": window._ft_ptoken || '',
            "data": payload
        };

        jQuery.post(_taoh_site_ajax_url, data, function (response) {
            data = response;
        });
    } catch (err) {
        console.error("Failed to save goal", err);
    }
});

if (window._ft_dojoTrackerEnabled) {
    window.addEventListener("load", async () => {
        if (await shouldPromptGoal()) {
            showGoalModal();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const maxSizeEl = document.getElementById('max_upload_size');
    if (!maxSizeEl) return;
    const maxSize = parseInt(maxSizeEl.value);

    document.querySelectorAll('input[type="file"]:not(.file_my_validation)').forEach(function (input) {
        input.addEventListener('change', function () {
            for (let file of input.files) {
                if (file.size > maxSize) {
                    taoh_set_error_message(`"${file.name}" is too large. Max allowed is ${formatBytes(maxSize)}.`, false);
                    input.value = '';
                    break;
                }
            }
        });
    });

    function formatBytes(bytes) {
        const sizes = ['B', 'KB', 'MB', 'GB'];
        if (bytes === 0) return '0 B';
        const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }
});

function updateRSProfileLiveStatus() {
    const ptoken = $('#profileModalContent').getSyncedData('ptoken');
    if (!ptoken || typeof getUserLiveStatus !== 'function') return;

    const $statusContainer = $('#profileModal').find('.rs_profile_live_status');
    if (!$statusContainer.length) return false;

    getUserLiveStatus(ptoken).then((userLiveStatus) => {
        const isOnline = userLiveStatus.success && Boolean(userLiveStatus.output);
        $statusContainer.find('.status-con').toggleClass('active', isOnline);
        $statusContainer.find('.status-text').text(isOnline ? 'Online' : 'Away');
        $statusContainer.show();
    }).catch(console.error);

    return true;
}

$(document).on('click', '.openProfileModal', function () {
    if (ft_isLoggedIn) {
        const currentElem = $(this);
        let currentFullPath = (window.location.href).replace(_taoh_site_url_root, '');

        var pagename = currentElem.attr('data-pagename');
        var profile_token = currentElem.attr('data-profile_token');
        var view_more = currentElem.hasClass('view_more');
        var height_pop = window.innerHeight;

        $('.profileModalBody').css('height', height_pop + 'px');
        $('#profileModalContent').css('height', height_pop - 100 + 'px');
        $('#profileModalContent').css('overflow', 'auto');

        $('#profileModalContent').html('<div class="d-flex align-items-center justify-content-center h-100"><img class="loader-gif" src="https://cdn.tao.ai/assets/wertual/images/taoh_loader.gif"></div>');
        $('#profileModalContent').setSyncedData('ptoken', profile_token);

        $('#profileModal').modal('show');

        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'GET',
            data: {
                taoh_action: 'get_profile_data',
                profile_token: profile_token,
                pagename,
                view_more,
                path: encodeURIComponent(btoa(currentFullPath)),
            },
            success: function (response) {
                $('#profileModalContent').html(response);
            }
        });
    }
});

$(document).on('shown.bs.modal', '#profileModal', function () {
    let waitForStatus = setInterval(() => {
        if (updateRSProfileLiveStatus()) {
            clearInterval(waitForStatus);
            profileRSLiveStatusInterval = setInterval(updateRSProfileLiveStatus, 300000);
        }
    }, 500);
});

$(document).on('hide.bs.modal', '#profileModal', function () {
    if (profileRSLiveStatusInterval) {
        clearInterval(profileRSLiveStatusInterval);
        profileRSLiveStatusInterval = null;
    }
});

$(document).on('shown.bs.collapse', '#profile_rs_view_more', function () {
    this.scrollIntoView({behavior: 'smooth', block: 'nearest'});
    $('[data-target="#profile_rs_view_more"]').text('View Less');
}).on('hidden.bs.collapse', function () {
    $('[data-target="#profile_rs_view_more"]').text('View More');
});

$(document).on('click', '.remaining-skills', function () {
    let currentElem = $(this);
    let remainingSkillsCount = currentElem.data('count');
    if (remainingSkillsCount > 0) {
        let remainingSkillsContainer = currentElem.siblings('.remaining-skills-container');
        remainingSkillsContainer.toggle();
        currentElem.text(remainingSkillsContainer.is(':visible') ? `- ${remainingSkillsCount}` : `+${remainingSkillsCount}`);
    }
});
