var _etb = window._etb_cfg || {};

    var my_ptoken = _etb.myPtoken;
    var table_url_index = _etb.tableUrlIndex;
    var cdn_prefix = _etb.cdnPrefix;
    var current_app_page = _etb.currentAppPage;
var banner_table_buttons = '';
if (!_etb.isLoggedIn) {
    banner_table_buttons = '<button type="button" class="mt-3 mb-2 btn btn-primary" style="width:250px;" data-location="" data-toggle="modal" data-target="#config-modal"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Login to create table</button>';
} else if (_etb.isEventsLobby) {
    banner_table_buttons = '<a target="_blank" class="btn banner-v2-btn mt-0 mr-1" href="' + _etb.tablesCreateUrl + '">Create table</a>' +
        '<a target="_blank" class="btn banner-v2-btn mt-0 mr-1" href="' + _etb.tablesUrl + '">More Info</a>';
} else {
    banner_table_buttons = '<span class="info" style="color:#007bff;">Register to create table</span>';
}
var no_banner_table_buttons = '';
if (_etb.isEventsLobby) {
    no_banner_table_buttons = '<a target="_blank" class="btn banner-v2-btn mt-0 mr-1" href="' + _etb.tablesCreateUrl + '">Create table</a>' +
        '<a target="_blank" class="btn banner-v2-btn mt-0 mr-1" href="' + _etb.tablesUrl + '">More Info</a>';
} else {
    no_banner_table_buttons = '<span class="info" style="color:#007bff;">Register to create table</span>';
}
    var banner_table =`<div class="v2-banner flex-column flex-md-row p-3 px-lg-5">
                        <div class="v2-svg-con" style="background-color:transparent;">
                           <img src="${_etb.tablesImgUrl}" width="50" alt="Tables™ Logo">
                        </div>
                        <div>
                            <h6 class="mb-2" >Start a discussion.</h6>
                            <p class="mb-2"> Spark the community with your thoughts.</p>
                            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
${banner_table_buttons}
                            </div>
                        </div>
                    </div>`;
        var no_banner_table =`<div class="v2-banner flex-column flex-md-row p-3 px-lg-5">
                        <div class="v2-svg-con" style="background-color:transparent;">
                           <img src="${_etb.tablesImgUrl}" width="50" alt="Tables™ Logo">
                        </div>
                        <div>
                            <h6 class="mb-2" >Be the first to start a discussion.</h6>
                            <p class="mb-2"> Spark the community with your thoughts.</p>
                            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
${no_banner_table_buttons}
                            </div>
                        </div>
                    </div>`;


    async function getEventTables(eventtoken,response,hallColorArray,search='',tab_name='') {
    var eventTableKey = `event_tables_${eventtoken}`;
    // alert(eventTableKey);

       IntaoDB.getItem(objStores.event_store.name, eventTableKey).then((res) => {

            console.log('-----response 1-------',res);
             if (res && res.success && res.output && res.output.total_result > 0) {
                var tables = res.output.data;
                getEventLobbyTables(eventtoken, tables);
            } else {
                const handleTableResponse = (response) => {
                    // console.log('handleResponse : '+response);
                    console.log('-----response 2--------',response);

                    if (response.success && response.output && response.output.total_result > 0) {

                        IntaoDB.setItem(objStores.event_store.name, {
                            taoh_data: eventTableKey,
                            values: response,
                            timestamp: Date.now()
                        });
                       var tables = response.output.data;
                        getEventLobbyTables(eventtoken, tables);
                    } else {
                        //var content = 'No Result Found';

                      //  $('#tables_top').remove();
                        $('#tables_list').html(no_banner_table);


                    }
                };

                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'POST',  // changed to post to avoid caching issues in flexible domain
                    data: {
                        action: 'get_event_tables',
                        taoh_action: 'get_event_tables',
                        token: _taoh_ajax_token,
                        eventtoken: eventtoken,
                 },
                    dataType: 'json',
                    success: handleTableResponse,
                    error: (xhr) => console.error('Error:', xhr.status)
                });
            }
        }).catch((error) => {
            console.error('Error fetching event tables:', error);
        });


    }

    function getEventLobbyTables(eventtoken, tables){
                 var colorArray = ['#708090','#A7C7E7','#5F9EA0','#B3A398','#A8BBA2','#C3D6B8',
                        '#EED9C4','#D1C2E0','#F5F5F5','#748CAB','#D6D1CD','#E4C9AF',
                        '#B8E0D2','#E6C0C0','#C8A2C8'];

                var tables_html = '';
                var maxVisible = 15;
                //const table_view_all_link = table_url + "k/" + eventtoken + "/tables";
                const table_view_all_link = table_url_index + "tables";

                tables.slice(0, maxVisible).forEach((table, index) => {
                    var table_color = colorArray[index % colorArray.length];

                    tables_html += generateTableCardHTML(eventtoken, table, table_color);
                     if(index == 0)
                        tables_html +=banner_table;

                });

                if (tables.length > maxVisible) {
                    tables_html += `
                        <div class="d-flex justify-content-end mt-3">
                            <a href="${table_view_all_link}" target="_blank" id="viewAllTables" class="btn btn-link"> View All Tables (${tables.length})&nbsp;&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i></a>
                        </div>
                    `;
                }


                 $('#tables_list').html(tables_html);

                // Show or hide the tables tab based on the presence of tables
                if(tables.length > 0){
                     $('#tables_list').show();
                } else {
                    $('#tables_top').hide();
                    $('#tables_list').hide();
                }
    }


    function generateTableCardHTML(eventtoken, table, color) {
        let user_timezone;
        if (isLoggedIn) {
            user_timezone = _etb.userTimezone;
        }
        if (!isLoggedIn || !user_timezone?.trim()) {
            let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
            user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
        }
        if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

        const skillBadge = table.skills
            ? `<p class="n-info-badge mr-2" style="background-color:#F9A386">Skill: ${table.skills}</p>`
            : '';

        //const table_link = table_url + "/k/" + eventtoken + "/" + table.key+'/tables';
        const table_link = table_url_index + "tables/"+ table.key;


        const fullDescription = table.description ? table.description.trim() : 'No description available';
        var location_type = table.location_type || 'Location';

        const locationMap = {
            'zoom': 'Zoom Meeting',
            'google_meet': 'Google Meet',
            'jitsi': 'Jitsi Meet',
            'tao_team': 'Tao Team',
            'physical': 'Physical Location',
            'tbd': 'TBD'
        };

        const durationMap = {
            '15 min': 15,
            '30 min': 30,
            '45 min': 45,
            '1 hour': 60,
            '1.5 hours': 90,
            '2 hours': 120
        };

        var duration = 0;
        if(durationMap[table.span]) {
            duration = durationMap[table.span];
        }

        var startTime = table.date + table.time;
        var inputStartTime = startTime.replace(/[T:-]/g, '') + '00';

        let year = inputStartTime.substring(0, 4);
        let month = inputStartTime.substring(4, 6) - 1;
        let day = inputStartTime.substring(6, 8);
        let hour = inputStartTime.substring(8, 10);
        let minute = inputStartTime.substring(10, 12);
        let second = inputStartTime.substring(12, 14);
        let date = new Date(year, month, day, hour, minute, second);
        date.setMinutes(date.getMinutes() + parseInt(duration));

        console.log("added date", inputStartTime, date);

        let y = date.getFullYear();
        let m = String(date.getMonth() + 1).padStart(2, '0');
        let d = String(date.getDate()).padStart(2, '0');
        let h = String(date.getHours()).padStart(2, '0');
        let min = String(date.getMinutes()).padStart(2, '0');
        let s = String(date.getSeconds()).padStart(2, '0');

        let endTime = `${y}${m}${d}${h}${min}${s}`;

        if (locationMap[location_type]) {
            location_type = locationMap[location_type];
        } else {
            // Capitalize the first letter for unexpected types
            location_type = location_type.charAt(0).toUpperCase() + location_type.slice(1);
        }

        const shortDescription = fullDescription.length > 200
            ? fullDescription.substring(0, 200) + '...'
            : fullDescription;

        const descId = `desc_${table.key}`;


        var locality = "";

        let event_timestamp_start_data = {
            utc_datetime: startTime.replace(/[T:-]/g, '') + '00',
            local_datetime: startTime.replace(/[T:-]/g, '') + '00',
            timezone: table.timezone,
            locality: locality
        };

        let event_timestamp_end_data = {
            utc_datetime: endTime,
            local_datetime: endTime,
            timezone: table.timezone,
            locality: locality
        };

        console.log("D1 ", table);
        console.log("D2 ", event_timestamp_start_data);
        console.log("D3 ", event_timestamp_end_data);

        let startdate = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'EEE, MMM d, yyyy', 0);
        let starttime = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'hh:mm A', 1);

        let enddate = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'EEE, MMM d, yyyy', 0);
        let endtime = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'hh:mm A', 1);

        console.log("start end", startdate, enddate);


        let display_datetime;
        if(startdate === enddate) {
            display_datetime = startdate +", "+ starttime +" - "+ endtime;
        } else {
            display_datetime = startdate +", "+ starttime +" - "+ enddate +", "+ endtime;
        }

        const image_src = `${cdn_prefix}/images/igcache/${encodeURIComponent(table.title)}____${encodeURIComponent(startdate +", "+ starttime)}____${encodeURIComponent(table.location)}/480_480/event.jpg`;

        return `
            <div class="tables_div new-exh-list mb-3 discussion-tab-inner">
                <div class="p-2 d-flex" style="gap: 12px; flex: 1;">

                        <div class="d-flex flex-column flex-md-row align-items-md-center" style="gap: 12px;flex: 1;">

                            <div class="g-overlay-con">
                                <div class="n-hall-list-bg d-md-none" style="background-image: url(${image_src})"></div>
                                <div class="glass-overlay d-md-none"></div>
                                <img class="n-hall-list-pic" src="${image_src}" alt="">
                            </div>
                            <div style="flex: 1;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap" style="flex: 1; gap: 12px;">

                                    <div class="d-flex align-items-center justify-content-between flex-wrap flex-xl-nowrap" style="flex: 1; gap: 12px;">
                                        <div class="d-flex flex-column" style="gap:3px;">
                                                <div class="d-flex align-items-center flex-wrap">
                                                    <p class="n-info-badge mr-2" style="background: #5170ff; color: #fff;">${display_datetime}</p>
                                                    ${skillBadge}
                                                    <p class="n-info-badge mr-2" style="background-color:#9d0854; color: #fff">Type: ${location_type}</p>

                                                </div>
                                                <h6 class="n-exh-name text-capitalize mb-0">${table.title}</h6>
                                                <div class="d-flex align-items-center justify-content-between mb-1" style="gap: 10px;">
                                                    <p id="${descId}" class="desc-text text-capitalize">${shortDescription}</p>
                                                </div>
                                                ${fullDescription.length > 200
                                                    ? `<a href="javascript:void(0);" class="toggle-desc" data-id="${descId}" data-full="${encodeURIComponent(fullDescription)}" data-short="${encodeURIComponent(shortDescription)}">Show more</a>`
                                                    : ''
                                                }
                                        </div>
                                        ${current_app_page == 'events_lobby' ? `
                                        <div class="flex-shrink-lg-0 d-flex align-items-center" style="gap: 6px;">
                                                    <a target="_blank" data-metrics="view_exhibitor" href="${table_link}" class="btn bor-btn metrics_action">Visit Us</a>
                                        </div>` :
                                        `<div class="flex-shrink-lg-0 d-flex align-items-center " style="gap: 6px;">
                                                    <a target="_blank" disabled data-metrics="view_exhibitor" href="javascript:void(0)"
                                                    class="btn bor-btn joinus-btn join_networking disabled"
                                                    style="border:1px solid #d3d3d3;background-color:#d3d3d3;color:#656565 !important">Register to visit</a>
                                        </div>`}
                                        <a href="#" style="padding-top: 5px;margin-left: 10px;margin-right: 10px;color: #040404 !important; "><i class="fa fa-chevron-right" style="font-size: 25px;"></i></a>
                                    </div>

                                </div>

                            </div>

                        </div>

                </div>
            </div>
        `;
    }

    $(document).on('click', '.toggle-desc', function () {
        const descId = $(this).data('id');
        const full = decodeURIComponent($(this).data('full'));
        const short = decodeURIComponent($(this).data('short'));

        const descElem = $('#' + descId);
        const isExpanded = $(this).text() === 'Show less';

        if (isExpanded) {
            descElem.text(short);
            $(this).text('Show more');
        } else {
            descElem.text(full);
            $(this).text('Show less');
        }
    });

