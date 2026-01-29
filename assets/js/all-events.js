var _ale = window._ale_cfg || {};
        const isLoggedIn = _ale.isLoggedIn;
        const isValidUser = _ale.isValidUser;

        let loaderArea = $('#listloaderArea');
        let detailloaderArea = $('#detailloaderArea');
        let searchQuery = $('#searchQuery');
        let eventlistArea = $('#eventlistArea');
        let locationSelectInput = $('#locationSelect');
        let geohashInput = $('#geohash');
        let currentMod = _ale.appSlug;
        let geohash = "";
        let search = "";
        let locationClear = $('#locationClear');
        let searchClear = $('#searchClear');
        let postDate = $('#postdate').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        let activeListloaderArea = $('#activeListloaderArea');
        let listUpdatedAt = 0;
        let activeChatList = $('#activeChatList');
        let eventCount = $('#eventCount');
        let totalItems = 0; //this will be rewriiten on response of events on line 363
        let itemsPerPage = 12;
        let currentPage = 1;
        let like_min = _ale.likeMin;
        let comment_min = _ale.commentMin;
        let share_min = _ale.shareMin;
        let app_slug = 'events';
        let arr_cont = [];
        let liked_arr = _ale.likedArr;
        //var event_list_name = "events_list";
        var already_rendered = false;
        var get_slug = false;
        var event_list_name = "";
        var store_name = EVENTStore;
        var det_slot = $('.detail_tab');
        var event_type = '';
        var user_ptoken = _ale.userPtoken;
        const rsvped_data = _ale.rsvpedData;
        const rsvp_find = new Array();

        let _taoh_static_ajax_token = _ale.staticAjaxToken;

        loader(true, loaderArea);
        $('#dateRangeInputs').hide();
        $('.no_result_div').hide();
        //Initial run

        $(document).ready(function () {

            $('[data-toggle="tooltip"]').tooltip();

            rsvped_get();
            $('.ts-control').css('height', '37px');

            if (_ale.intaoDbEnable) {
            console.log('list ajax start time:', new Date().getTime());
            geteventlistdata();
            } else {
            taoh_events_init();
            }
        })

        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });

        function delete_events_into() {
            getIntaoDb(dbName).then((db) => {
                let dataStoreName = EVENTStore;
                const transaction = db.transaction(dataStoreName, 'readwrite');
                const objectStore = transaction.objectStore(dataStoreName);
                const request = objectStore.openCursor();
                request.onsuccess = (event) => {
                    const cursor = event.target.result;
                    if (cursor) {
                        const index_key = cursor.primaryKey;
                        if (index_key.includes('event')) {
                            objectStore.delete(index_key);
                        }
                        cursor.continue();
                    }
                };
            }).catch((err) => {
                console.log('Error in deleting data store');
            });
        }

        function get_event_type(event_type_get = '') {

            if (event_type_get == 'rsvp_list') {
                event_type = event_type_get;
            } else if (event_type_get == 'saved') {
                event_type = event_type_get;
            } else {
                event_type = '';
            }
            /*for clearing search and paging data */
            currentPage = 1;
            $('#postdate').val('');
            $('#from_date').val('');
            $('#to_date').val('');
            $('#query').val('');

            $('#locationClear').hide();
            $('#coordinateLocation').val("");
            $('#geohash').val("");
            geohash = "";
            $('.ts-control div.item').html('');
            $('.ts-wrapper').removeClass('full has-items input-hidden');
            /*for clearing search and paging data */

            if (_ale.intaoDbEnable) {
            geteventlistdata('', event_type);
            } else {
            taoh_events_init('', event_type);
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const fromDateInput = document.getElementById('from_date');
            const toDateInput = document.getElementById('to_date');

            if (fromDateInput && toDateInput) {
                fromDateInput.addEventListener('change', function () {
                    // Get the selected from date
                    const fromDate = new Date(fromDateInput.value);
                    // Set the minimum selectable date for the 'To' date
                    toDateInput.min = fromDate.toISOString().split("T")[0];
                });

                toDateInput.addEventListener('change', function () {
                    // Get the selected to date
                    const toDate = new Date(toDateInput.value);
                    // Set the maximum selectable date for the 'From' date
                    fromDateInput.max = toDate.toISOString().split("T")[0];
                });
            }
        });


        const postdateSelect = document.getElementById('postdate');
        if (postdateSelect) {
            postdateSelect.addEventListener('change', function () {
                if (this.value === 'date_range') {
                    $('#dateRangeInputs').css('display', 'flex');
                } else {
                    $('#dateRangeInputs').hide();
                    $('#from_date').val('');
                    $('#to_date').val('');
                }
            });
        }


        function searchFilter() {
            currentPage = 1;
            var queryString = $('#searchFilter').serialize();
            console.log(queryString);
            geohash = geohashInput.val();
            search = $('#query').val();
            console.log('----search------', search);
            if (search) {
                searchClear.show();
            }
            if (geohash) {
                locationClear.show();
            }
            already_rendered = false;
            event_type = event_type;
            if (_ale.intaoDbEnable) {
            geteventlistdata(queryString, event_type);
            } else {
            taoh_events_init('', event_type);
            }
        }

        function geteventlistdata(queryString, event_type_get = '') {
            // Open or create a database
            getIntaoDb(dbName).then((db) => {
                var currpage = currentPage - 1;
                event_type = event_type_get;

                var event_list_hash = 'events_+'+search + geohash + queryString + currpage + itemsPerPage + postDate + from_date + to_date + event_type;
                event_list_name = 'events_tao_all_' + crc32(event_list_hash);
                console.log(event_list_name);
                const datareventequest = db.transaction(store_name).objectStore(store_name).get(event_list_name); // get main data
                datareventequest.onsuccess = () => {
                    console.log(datareventequest);
                    const eventstoredatares = datareventequest.result;
                    if (eventstoredatares !== undefined && eventstoredatares !== null && eventstoredatares !== "" && eventstoredatares !== "undefined" && eventstoredatares !== "null") {
                        console.log('list ajax intaodb call start time:', new Date().getTime());
                        const eventstoredata = datareventequest.result.values;
                        get_slug = true;
                        already_rendered = true;
                        loader(false, loaderArea);
                        render_events_template(eventstoredata, eventlistArea, event_type);
                        //taoh_events_init(queryString);
                        console.log('ifff');
                    } else {
                        get_slug = false;
                        already_rendered = false;
                        loader(true, loaderArea);
                        event_type = event_type;
                        taoh_events_init(queryString, event_type);
                        console.log('else');
                    }
                }

            }).catch((error) => {
                console.log('Geteventlistdata Error:', error);
            });
        }

        function show_pagination(holder) {
            return $(holder).pagination({
                items: totalItems,
                itemsOnPage: itemsPerPage,
                currentPage: currentPage,
                displayedPages: 3,
                onInit: function () {
                    $("#pagination ul").addClass('pagination justify-content-center');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                },
                onPageClick: function (pageNumber, event) {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                    currentPage = pageNumber;
                    console.log('--show_pagination----------' + currentPage);
                    //taoh_events_init();
                    already_rendered = false;
                    console.log(already_rendered);
                    if (_ale.intaoDbEnable) {
                    geteventlistdata('', event_type);
                    } else {
                    taoh_events_init('', event_type);
                    }
                }
            });
        }

        function clearBtn(type) {
            loader(true, loaderArea);
            if (type == "search") {
                $('#searchClear').hide();
                search = "";
                $('#query').val("");
            }
            if (type == "geohash") {
                $('#locationClear').hide();
                $('#coordinateLocation').val("");
                $('#geohash').val("");
                geohash = "";
                $('.ts-control div.item').html('');
                $('.ts-wrapper').removeClass('full has-items input-hidden');
            }
            already_rendered = false;
            event_type = event_type;
            if (_ale.intaoDbEnable) {
            geteventlistdata('', event_type);
            } else {
            taoh_events_init('', event_type);
            }
        }

        function taoh_events_init(queryString = "", event_type_get = "") {
            search = $('#query').val();
            geohash = geohashInput.val();
            if (search) {
                searchClear.show();
            } else {
                searchClear.hide();
            }
            if (geohash) {
                locationClear.show();
            } else {
                locationClear.hide();
            }
            geohash = geohash
            /* if($('#locationSelect-ts-control').val() == ''){
                $('#coordinateLocation').val('');
                geohashInput.val('');
            } */
            postDate = $('#postdate').val();
            from_date = $('#from_date').val();
            to_date = $('#to_date').val();
            event_type = event_type_get;

            var data = {
                'taoh_action': 'events_get_tao',
                'ops': 'list',
                'search': search,
                'geohash': geohash,
                'offset': currentPage - 1,
                'limit': itemsPerPage,
                'postDate': postDate,
                'from_date': from_date,
                'to_date': to_date,
                'filter_type': event_type,
                'filters': queryString,
                'event_type' : _ale.eventType
            };

            jQuery.get(_taoh_site_ajax_url, data, function (listresponse) {
                listresponse = parseJSONSafely(listresponse);
                if (_ale.intaoDbEnable) {
                if (!get_slug) {
                    indx_events_list(listresponse);
                }
                if (!already_rendered) {
                    render_events_template(listresponse, eventlistArea, event_type);
                }
                } else {
                render_events_template(listresponse, eventlistArea, event_type);
                }
                loader(false, loaderArea);
            }).fail(function () {
                loader(false, loaderArea);
                console.log("Network issue!");
            })
        }

        function rsvped_get() {
            getIntaoDb(dbName).then((db) => {
                const dataStoreName = store_name;
                const transaction = db.transaction(dataStoreName, 'readwrite');
                const objectStore = transaction.objectStore(dataStoreName);
                const request = objectStore.openCursor();
                request.onsuccess = (event) => {
                    const cursor = event.target.result;
                    if (cursor) {
                        var index_key = cursor.primaryKey;
                        if (index_key.includes('rsvp_status_' + user_ptoken + '_')) {
                            rsvp_find.push(index_key);
                        }
                        cursor.continue();
                    } else {
                        console.log('Finished iterating');
                    }
                };
            });
        }

        function date_read(date, locality, timezone) {
            let options = {
                weekday: 'short',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: "numeric"
            };

            if (locality === 0) {

            } else {
                // Use a fallback mechanism for timezone
                timezone = timezone || getCookie('client_time_zone') || Intl.DateTimeFormat().resolvedOptions().timeZone;
            }

            if (!isValidTimezone(timezone)) timezone = 'UTC';

            options.timeZone = timezone;

            let output = new Date(date);
            return output.toLocaleDateString('en-US', options).toUpperCase();
        }

        function convertToSlug(Text) {
            return Text.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
        }

        function isEventTokenPresent(eventToken) {
            return rsvped_data.some(item => item.eventtoken === eventToken);
        }

        function render_events_template(data, slot, event_type_get) {
            slot.empty();
            console.log('---eventdata---------',data)
            console.log('---event_type_get---------',event_type_get)
            $('.no_result_div').hide();

            if (data.output === false || data.success === false) {
                slot.append("");
                $('#pagination').hide();
                event_detail_execute('0');

                var noresult = `<h1>We couldn't find exactly what you were looking for</h1>
                <p>It seems your search didn't yield any results.<br> Don't worry, we can help you find what you need.</p>
                <ul class="options">
                    <li>1. Adjust your Search Terms</li>
                    <li>2. Explore Other Events that might suit you</li>
                    <li>3. Refine Your Search Criteria</li>
                </ul>`;
                //alert(noresult)

                $('.noresult_html').html(noresult);
                $('.no_result_div').show();
                return false;
            }


                totalItems = data.output.total
                if (!get_slug) {
                    var result = format_object(data);
                } else {
                    var result = data;
                }

            $.each(result.output.list, function (i, v) {
                var additive = '';
                if (v.canonical_url && v.canonical_url != '' && v.canonical_url != undefined) {
                    additive = v.canonical_url;
                } else {
                    additive = v.source;
                }

                let user_timezone;
                if (isLoggedIn) {
                    user_timezone = '' + _ale.userTimezone + '';
                }
                if (!isLoggedIn || !user_timezone?.trim()) {
                    let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
                    user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
                }
                if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

                arr_cont.push(v.eventtoken.toString());

                v.title = ucfirst(v.title);

                // var company_name_get = v.company.length && v.company[0] ? v.company[0].name : '';

                var liked_check = get_liked_check(v.eventtoken, v.conttoken);

                let is_rsvp_done = false;
                let event_live_state;

                let btn_text = 'Register Now';
                let btn_class = 'btn-primary';
                let btn_icon = '<i class="fa fa-ticket mr-2" aria-hidden="true"></i>';
                let event_url = `${v.source}/" + _ale.appSlug + "/d/${convertToSlug(taoh_title_desc_decode(v.title))}-${v.eventtoken}?con=${v.conttoken}`;

                let rsvped_token = 'rsvp_status_' + user_ptoken + '_' + v.eventtoken;

                if (isLoggedIn) {
                    if (jQuery.inArray(rsvped_token, rsvp_find) !== -1 || isEventTokenPresent(v.eventtoken)) {
                        is_rsvp_done = true;
                        event_live_state = eventLiveState(v.utc_start_at || '', v.utc_end_at || '', v.locality, user_timezone);
                        if (event_live_state == 'live') {
                            if (isValidUser) {
                                btn_text = 'Event Live, Join Now';
                                btn_class = 'btn-success';
                                btn_icon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px"><circle cx="40" cy="40" r="28" fill="#fff"></circle>

                                                                    <polygon points="34,28 34,52 54,40" fill="#28A745"></polygon>

                                                                    <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>

                                                                    <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                            </svg>`;
                                event_url = `${v.source}/" + _ale.appSlug + "/chat/id/events/${v.eventtoken}`; // lobby url
                            } else {
                                btn_text = 'Complete Settings to Join';
                                btn_class = 'btn-success';
                                btn_icon = '<i class="fa fa-cogs mr-2" aria-hidden="true"></i>';
                                event_url = '<?_ale.siteUrlRoot + '/settings''; // settings url
                            }
                        } else {
                            btn_text = 'You have registered';
                            btn_class = 'btn-warning';
                            btn_icon = '<i class="fa fa-ticket mr-2" aria-hidden="true"></i>';
                            event_url = `${v.source}/" + _ale.appSlug + "/chat/id/events/${v.eventtoken}`; // lobby url
                        }
                    }
                } else {
                    btn_text = 'Register Now';
                    btn_class = 'btn-primary';
                    btn_icon = '<i class="fa fa-ticket mr-2" aria-hidden="true"></i>';
                }

                let rsvp_link;
                /*if (isLoggedIn) {
                    rsvp_link = `<a href="${event_url}" data-metrics="rsvp" class="btn d-flex align-items-center click_metrics ${btn_class}"
                                style="width: fit-content;">${btn_icon} <span>${btn_text}</span></a>`;
                } else {
                    rsvp_link = `<button type="button" class="btn create_referral ${btn_class}" id="register_ticket" data-location="${event_url}" data-title="${btoa(unescape(encodeURIComponent(v.title)))}" data-toggle="modal" data-target="#config-modal">${btn_icon} ${btn_text}</button>`;
                }*/

                rsvp_link = `<a href="${event_url}" data-metrics="rsvp" class="btn d-flex align-items-center click_metrics ${btn_class}"
                style="width: fit-content;">${btn_icon} <span>${btn_text}</span></a>`;

                var no_image = '' + _ale.siteUrlRoot + '/assets/images/event.jpg' + '';
                var img = newavatardisplay(v.user_avatar, v.avatar_image, '<?' + _ale.opsPrefix + '');

                let event_type = v.event_type ? (v.event_type).toLowerCase() : 'virtual';

                const costArray = v.ticket_types.map(ti => ti.price === 'paid' ? ti.cost : 0);
                const minCost = Math.min(...costArray);

                let event_timestamp_start_data = {
                    utc_datetime: v.utc_start_at,
                    local_datetime: v.local_start_at,
                    timezone: v.local_timezone,
                    locality: v.locality
                };
                let event_start_at = format_event_timestamp(event_timestamp_start_data, user_timezone);

                if (data.output.total == 1 && search =='') {
                    window.location.href = event_url;
                } else {
                var single_event = '';
                if(data.output.total == 1 && search !='')
                single_event = 'single_event';
                slot.append(`
                    <div class="${single_event} event-container dash_metrics px-0 pt-0 event-listing-block-row ${i == 0 ? 'active' : ''} ${data.output.total == 2 ? 'event-list-2' : ''}" style="cursor: pointer;"
                    data-conttoken="${v.eventtoken}" data-canonical = "${additive}" event-url="${event_url}"
                    data-metrics="view" conttoken="${v.eventtoken}" data-type="events">

                        <div class="flex-column justify-content-between" style="height: 100%;">
                            <div>
                                <div class="event-image">
                                    <div class="events-bg" style="background-image: url(${v.event_image != '' ? v.event_image : no_image})"></div>
                                    <div class="glass-overlay"></div>
                                    <img class="card-img shadow-sm" src="${v.event_image != '' ? v.event_image : no_image}" data-src="${v.event_image}" alt="${taoh_title_desc_decode(v.title)}">
                                </div>
                                <div class="px-3 pt-3">
                                    <div class="d-flex justify-content-between mb-3" style="gap: 6px;">
                                        <div class="event-date">
                                            <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19 6.00001H1M6 3.00001V1.00001M14 3V1M3 2.00001H2.9C1.85066 2.00001 1 2.85067 1 3.90001V16C1 17.1046 1.89543 18 3 18H17C18.1046 18 19 17.1046 19 16V3.90001C19 2.85067 18.1493 2.00001 17.1 2.00001H17M9 2.00001H11M13.253 10.1109L9.8802 14.3122C9.5075 14.7765 8.8143 14.8143 8.3933 14.3933L7 13" stroke="#ffffff" stroke-width="1.25" stroke-linecap="round"/>
                                            </svg>
                                            <span>${event_start_at}</span>
                                        </div>

                                    </div>

                                    <h4 class="event-title"><a href="${event_url}" data-metrics="view" class="click_metrics">${taoh_title_desc_decode(v.title)}</a></h4>

                                    <p class="event-location py-2">${event_type != 'virtual' && v.full_location ? newgenerateLocationHTML(v.full_location) : 'Attend Online'}</p>

                                    <div class="event-price mb-2">
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16.0268 0.500011L16.0268 0.500014H16.0249H9.87747C9.30941 0.500014 8.76623 0.72384 8.36704 1.12303L1.12507 8.365C0.291641 9.19842 0.291643 10.5483 1.12507 11.3817L6.61827 16.8749C7.4517 17.7084 8.80159 17.7084 9.63502 16.8749L16.877 9.63297C17.2762 9.23379 17.5 8.6906 17.5 8.12254V1.97098C17.5 1.15635 16.8392 0.496945 16.0268 0.500011ZM12.1069 3.31981C12.4476 2.97911 12.9096 2.7877 13.3915 2.7877C13.8733 2.7877 14.3354 2.97911 14.6761 3.31981C15.0168 3.66051 15.2082 4.1226 15.2082 4.60443C15.2082 5.08625 15.0168 5.54834 14.6761 5.88904C14.3354 6.22974 13.8733 6.42115 13.3915 6.42115C12.9096 6.42115 12.4476 6.22974 12.1069 5.88904C11.7662 5.54834 11.5748 5.08625 11.5748 4.60443C11.5748 4.1226 11.7662 3.66051 12.1069 3.31981Z" stroke="#212121"/>
                                        </svg>
                                        <span>From ${minCost == 0 ? '$0 (free)' : '$' + minCost}</span>
                                    </div>

                                    <div class="type_display event-type mb-2">
                                        ${(event_type == 'in-person') ? `<svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198ZM11.6579 7.43934C11.6952 7.4245 11.7289 7.40966 11.7662 7.39482L11.1347 9.75091C10.9255 10.5338 11.131 11.3686 11.684 11.966L14.3259 14.8267L15.148 18.0956C15.3087 18.7301 15.9589 19.1197 16.5978 18.9601C17.2368 18.8006 17.6292 18.155 17.4685 17.5205L16.6091 14.1032C16.5381 13.8138 16.3923 13.5504 16.1905 13.3314L14.3408 11.3278L15.062 8.89752L15.4208 9.75091C15.5852 10.1442 15.8879 10.467 16.2727 10.66L17.2705 11.1534C17.8609 11.4466 18.5783 11.2091 18.8735 10.6229C19.1687 10.0366 18.9296 9.32422 18.3392 9.0311L17.5358 8.63409L16.964 7.26866C16.3213 5.74369 14.8192 4.7493 13.1526 4.7493C12.3006 4.7493 11.4598 4.9274 10.6826 5.26876L10.3836 5.39862C9.15423 5.94034 8.21257 6.97183 7.79032 8.23707L7.69316 8.52649C7.48391 9.14983 7.82395 9.82141 8.44799 10.0292C9.07202 10.237 9.75211 9.89933 9.96137 9.2797L10.0585 8.99028C10.2715 8.35581 10.7424 7.84377 11.3552 7.57291L11.6541 7.44305L11.6579 7.43934ZM10.5368 12.4521L9.60264 14.7674L7.38301 16.9713C6.91592 17.4351 6.91592 18.1884 7.38301 18.6522C7.85011 19.1159 8.60867 19.1159 9.07576 18.6522L11.3813 16.3628C11.5532 16.1922 11.6878 15.9881 11.7774 15.7655L12.3193 14.4223L10.7984 12.7749C10.705 12.6747 10.619 12.5671 10.5368 12.4558V12.4521ZM8.23126 10.1702C7.94353 10.0069 7.58106 10.1034 7.41291 10.3891L6.21715 12.4447L4.18435 11.2796C3.61263 10.9531 2.88023 11.146 2.55139 11.7137L0.159872 15.8285C-0.168962 16.3962 0.025349 17.1235 0.597072 17.45L2.66723 18.6373C3.23896 18.9638 3.97136 18.7709 4.30019 18.2032L6.69171 14.0884C6.74777 13.9919 6.78887 13.8954 6.81503 13.7915L8.45172 10.9828C8.61614 10.6971 8.51899 10.3372 8.23126 10.1702Z" fill="#2557A7"/>
                                        </svg>` : (event_type == 'hybrid') ? `<svg width="25" height="25" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.48083 0.251601C6.84598 -0.0838669 7.40707 -0.0838669 7.76926 0.251601L13.9442 5.95158C14.1431 6.13564 14.25 6.39095 14.25 6.64923H9.97503C9.408 6.64923 8.89738 6.89861 8.55004 7.29345V6.17424C8.55004 5.91299 8.33629 5.69924 8.07504 5.69924H6.17505C5.9138 5.69924 5.70005 5.91299 5.70005 6.17424V8.07423C5.70005 8.33548 5.9138 8.54923 6.17505 8.54923H8.07504V12.3492H3.32506C2.53834 12.3492 1.90006 11.7109 1.90006 10.9242V7.59923H0.950065C0.558192 7.59923 0.20788 7.35876 0.0653809 6.99658C-0.0771186 6.63439 0.0178811 6.21877 0.305849 5.95158L6.48083 0.251601ZM10.45 9.02422V13.2992H16.15V9.02422H10.45ZM9.02504 8.54923C9.02504 8.02376 9.44957 7.59923 9.97503 7.59923H16.625C17.1505 7.59923 17.575 8.02376 17.575 8.54923V13.2992H18.525C18.7863 13.2992 19 13.513 19 13.7742C19 14.5609 18.3617 15.1992 17.575 15.1992H16.15H10.45H9.02504C8.23832 15.1992 7.60004 14.5609 7.60004 13.7742C7.60004 13.513 7.81379 13.2992 8.07504 13.2992H9.02504V8.54923Z" fill="#406CB2"/>
                                        </svg>` : `<svg width="20" height="13" viewBox="0 0 20 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0 2.16667C0 0.971615 0.996528 0 2.22222 0H11.1111C12.3368 0 13.3333 0.971615 13.3333 2.16667V10.8333C13.3333 12.0284 12.3368 13 11.1111 13H2.22222C0.996528 13 0 12.0284 0 10.8333V2.16667ZM19.4132 1.21198C19.7743 1.40156 20 1.76719 20 2.16667V10.8333C20 11.2328 19.7743 11.5984 19.4132 11.788C19.0521 11.9776 18.6146 11.9573 18.2708 11.7339L14.9375 9.56719L14.4444 9.24557V8.66667V4.33333V3.75443L14.9375 3.43281L18.2708 1.26615C18.6111 1.04609 19.0486 1.0224 19.4132 1.21198Z" fill="#2557A7"></path>
                                        </svg>`}
                                        <span>${event_type}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="px-3">
                            ${rsvp_link}
                            </div>
                        </div>
                </div>
                `);
                }

            });

            show_pagination('#pagination');
            if (data.output.total > itemsPerPage) {
                $('#pagination').show();
                show_pagination('#pagination');
            } else {
                $('#pagination').hide();
            }

            if (search) {
                taoh_metrix_ajax('events', arr_cont);
            }
            $("html, body").animate({scrollTop: 0}, "slow");
        }

        function get_liked_check(eventtoken, contstoken = '') {
            if (jQuery.inArray(eventtoken, liked_arr) !== -1) {
                var get_liked = 1;
            } else {
                var get_liked = 0;
            }
            let is_local = localStorage.getItem(app_slug + '_' + eventtoken + '_' + contstoken + '_liked');
            if ((get_liked) || (is_local)) {
                var liked_checks = `<a class="fs-25 mr-2 ml-2 already-saved" style="vertical-align: text-bottom;">

				<img src="<?_ale.siteUrlRoot/assets/images/bookmark-fill.svg" alt="bookmark-saved" class="bookmark-saved" style="width: 18px">
			</a>`;
            } else {
                var liked_checks = `<a class="fs-25 events_like" style="cursor: pointer;">
			<img src="<?_ale.siteUrlRoot/assets/images/bookmark.svg" alt="bookmark" data-event="${(contstoken)}" data-cont="${(eventtoken)}" class="event_save" title="Save Event" style="width: 18px">
			</a>`;
            }
            //<img src="<?_ale.siteUrlRoot/assets/images/bookmark.svg" alt="bookark" style="width: 18px">
            //<i style="cursor:pointer;" data-cont="${(conttoken)}" title="Save Event" class="las la-bookmark event_save"></i>
           // return liked_checks;
        }

        function updateCanonical(newCanonicalUrl, event_url) {
            //let homeurl = "/* _ale.siteUrlRoot + "/" + _ale.appSlug + "/" */";
            let homeurl = "<?_ale.siteUrlRoot + '/' + _ale.appSlug + '/'";
            //alert(homeurl)
            let url = homeurl + 'd/' + event_url + '/?q=main';
            window.history.pushState("", "", url);
        }

        function event_detail_execute(eventtoken, contstoken = '') {
            det_slot.empty();
            if (eventtoken == '0') {
                det_slot.append("");
                return false;
            }
            loader(true, loaderArea);
            var data = {
                'taoh_action': 'events_get_detail',
                'ops': 'detail',
                'eventtoken': eventtoken,
                'ptoken': _ale.userPtoken,
            };
            jQuery.post(_taoh_site_ajax_url, data, function (response) {
                loader(false, loaderArea);
                det_slot.html(response);
                var detail_like = get_liked_check(eventtoken, contstoken);
                $('.like_render').html(detail_like);

            }).fail(function () {
                console.log("Network issue on response!");

            });

        }

        function indx_events_list(eventlistdata) {
            var event_taoh_data = {taoh_data: event_list_name, values: eventlistdata};
            let event_setting_time = new Date();
            event_setting_time = event_setting_time.setMinutes(event_setting_time.getMinutes() + 30);
            var event_setting_timedata = {taoh_ttl: event_list_name, time: event_setting_time};
            obj_data = {[store_name]: event_taoh_data, [TTLStore]: event_setting_timedata};
            Object.keys(obj_data).forEach(key => {
                // console.log(key, obj_data[key]);
                IntaoDB.setItem(key, obj_data[key]).catch((err) => console.log('Storage failed', err));
            });
            return false;
        }


        $(document).on("click", ".event-listing-block-row", function () {
            var eventtoken = $(this).attr("data-conttoken");
            // var event_url = $(this).attr("event-url");
            // var newCanonicalUrl = $(this).attr("data-canonical");

            // $(this).addClass('active').siblings().removeClass('active');

            save_metrics('events', 'click', eventtoken);
            // window.location.href = event_url;
            //event_detail_execute(eventtoken);
            //updateCanonical(newCanonicalUrl,event_url);
        });

        $(document).on("click", ".event_save", function (event) {
            event.stopPropagation();
            if(!isLoggedIn){
                 taoh_set_error_message('Login to perform the action.');
                return false;
            }
            var savetoken = $(this).attr('data-cont');
            var contttoken = $(this).attr('data-event');
            $('.events_like').find(`[data-cont='${savetoken}']`).attr('src', "<?_ale.siteUrlRoot/assets/images/bookmark-fill.svg");
            $('.events_like').find(`[data-cont='${savetoken}']`).removeClass('event_save').addClass("already-saved").removeAttr("style");
            $('.events_like').find(`[data-cont='${savetoken}']`).parent().addClass("already-saved").removeAttr("style");
            localStorage.setItem(app_slug + '_' + savetoken + '_' + contttoken + '_liked', 1);
            delete_events_into();
            save_metrics('events', 'like', contttoken);
            var data = {
                'taoh_action': 'event_like_put',
                'eventtoken': savetoken,
                'contttoken': contttoken,
                'ptoken': _ale.userPtoken,
            };
            jQuery.post("<?_ale.siteAjaxUrl, data, function (response) {
                if (response.success) {
                    taoh_set_success_message('Event Saved Successfully.');
                } else {
                    taoh_set_error_message('Event Save Failed.');
                    console.log("Like Failed!");
                }
            }).fail(function () {
                console.log("Network issue!");
            })
        });

        $(document).on("click", ".event_save", function (event) {
            event.stopPropagation(); // Stop the event from propagating to the parent
            if(!isLoggedIn){
                 taoh_set_error_message('Login to perform the action.');
                return false;
            }
            var savetoken = $(this).attr('data-cont');
            var contttoken = $(this).attr('data-event');
            $('.events_like').find(`[data-cont='${savetoken}']`).attr('src', "<?_ale.siteUrlRoot/assets/images/bookmark-fill.svg");
            $('.events_like').find(`[data-cont='${savetoken}']`).removeClass('event_save').addClass("already-saved").removeAttr("style");
            $('.events_like').find(`[data-cont='${savetoken}']`).parent().addClass("already-saved").removeAttr("style");
            localStorage.setItem(app_slug + '_' + savetoken + '_' + contttoken + '_liked', 1);
            delete_events_into();

            save_metrics('events', 'like', contttoken);

            var data = {
                'taoh_action': 'event_like_put',
                'eventtoken': savetoken,
                'contttoken': contttoken,
                'ptoken': _ale.userPtoken,
            };
            jQuery.post("<?_ale.siteAjaxUrl, data, function (response) {
                if (response.success) {
                    taoh_set_success_message('Event Saved Successfully.');
                } else {
                    taoh_set_error_message('Event Save Failed.');
                    console.log("Like Failed!");
                }
            }).fail(function () {
                console.log("Network issue!");
            })
        });

        $(document).on("click", ".toggle-link", function (event) {
            event.preventDefault();
            var $this = $(this);
            var $content = $(".full-text");
            if ($this.text() === "Show More") {
                $content.text($content.data("full-text"));
                $this.text("Show Less");
            } else {
                $content.text($content.data("short-text"));
                $this.text("Show More");
            }
        });

        $(document).on('click', '.share_box', function (event) {
            var datatitle = $(this).attr("data-title");
            console.log(datatitle);
            var dataptoken = $(this).attr("data-ptoken");
            var datashare = $(this).attr("data-share");
            var dataconttoken = $(this).attr("data-conttoken");
            var share_link = $(this).attr("data-share");
            var dat_ajax = '' + _ale.siteUrlRoot + '/ajax' + '';
            var image = '' + _ale.pageImage + '';
            var desc = '' + _ale.pageDesc + '';
            var title = '' + _ale.pageTitle + '';
            var fb_share = "http://www.facebook.com/sharer.php?s=100&p[url]=" + share_link + "&p[images][0]=" + image + "&p[title]=" + title + "&p[summary]=" + desc;
            var tw_share = "https://twitter.com/intent/tweet?text=" + title + "&url=" + share_link;
            var link_share = "https://www.linkedin.com/shareArticle?mini=true&url=" + share_link + "&title=" + title + "&summary=" + desc + "&images=" + image;
            var email_share = "mailto:?subject=I wanted you to see this site&amp;body=Check out this site " + title + share_link;

            $("#share_icon").html(`


						<div class="social-icon-box d-flex text-center" data-ajax="${(dat_ajax)}" data-conttype="' + _ale.appSlug + '">
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(fb_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y media_share share_count" data-click="facebook" style="background-color:#365899; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Facebook">
								<svg focusable="false" class="svg-inline--fa fa-facebook-f fa-w-10" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg>
							</a>
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(tw_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y media_share share_count" data-click="twitter" style="background-color:#00acee; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Twitter">
								<svg focusable="false" class="svg-inline--fa fa-twitter fa-w-16" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>
							</a>
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(link_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y media_share share_count" data-click="linkedin" style="background-color:#0A66C2; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share on Linkedin">
								<svg focusable="false" class="svg-inline--fa fa-linkedin fa-w-14" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"></path></svg>
							</a>
							<a data-gtitle="${(datatitle)}" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" data-social="${(email_share)}" class="ml-3 icon-element icon-element-sm shadow-sm text-gray hover-y media_share share_count" data-click="email" style="background-color:#B23121; margin-bottom:10px;cursor:pointer;" target="_blank" title="Share vai Email">
								<svg focusable="false" class="svg-inline--fa fa-envelope fa-w-16" style="color: white;" width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>
							</a>
						</div>
						<div class="text-center mt-2 mb-2"> or </div>
							<span class="text-success-message">Link Copied!</span>
								<input type="text" style="display:none;" class="form-control form--control form--control-bg-gray copys-input" id="copys-input" value="${(share_link)}">
								<div class="copys-btn text-center media_share" data-gptoken="${(dataptoken)}" data-gshare="${(datashare)}" data-gconntoken="${(dataconttoken)}" style="cursor: pointer;"> <i class="fas fa-copy"></i> Copy URL</div>
					`);

            $("#exampleModal1").modal('show');
        });

        setInterval(function () {
            if (_ale.intaoDbEnable) {
            checkTTL(event_list_name, store_name);
            }
        }, 30000);
