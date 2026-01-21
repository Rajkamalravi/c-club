
document.addEventListener('DOMContentLoaded', function () {
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

/* Get User Information From Server */
async function fetchUserInfoFromServer(formData, maxRetries = 3) {
    if (!formData.ptoken) return Promise.reject('Missing ptoken in formData');

    const user_info_key = 'user_info_list';
    const ops = formData.ops;
    const delay = 2000;

    return new Promise((resolve, reject) => {
        let retries = 0;

        function sendUserInfoRequest() {
            retries++;

            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'POST',
                headers: {"cfcache": 900},
                data: formData,
                success: function (response) {
                    try {
                        let srv_userInfoObj = typeof response === 'string' ? JSON.parse(response) : response;

                        if (srv_userInfoObj.success && srv_userInfoObj.user_data) {
                            let userInfoObj = srv_userInfoObj.user_data;
                            userInfoObj.last_fetch_time = Date.now();

                            userInfoList[ops] = userInfoList[ops] || {};
                            userInfoList[ops][userInfoObj.ptoken] = userInfoObj;

                            IntaoDB.getItem(objStores.common_store.name, user_info_key).then((intao_data) => {
                                let updatedResponse = intao_data?.values || {};
                                updatedResponse[ops] = updatedResponse[ops] || {};
                                updatedResponse[ops][userInfoObj.ptoken] = userInfoObj;

                                IntaoDB.setItem(objStores.common_store.name, { taoh_common: user_info_key, values: updatedResponse, timestamp: Date.now() });
                            });

                            resolve(userInfoObj);
                        } else {
                            reject('Invalid user info response from server!');
                        }
                    } catch (e) {
                        if (retries < maxRetries) {
                            setTimeout(sendUserInfoRequest, delay);
                        } else {
                            reject('Error parsing response from server after max retries!');
                        }
                    }
                },
                error: function () {
                    if (retries < maxRetries) {
                        setTimeout(sendUserInfoRequest, delay);
                    } else {
                        reject('Network error after max retries!');
                    }
                }
            });
        }

        sendUserInfoRequest();
    });
}

async function ft_getUserInfo(pToken_to, ops = 'public', serverFetch = false) {
    if (!pToken_to?.trim()) return null;

    let userInfo = {};
    let ft_userInfoList = {};

    // Initialize ops object if not exists
    ft_userInfoList[ops] = ft_userInfoList[ops] || {};

    if (!serverFetch) {
        // Try to get userInfo from local variable
        userInfo = ft_userInfoList[ops][pToken_to] || userInfo;

        // Try to get userInfo from global variable
        if (!userInfo.ptoken) {
            userInfoList[ops] = userInfoList[ops] || {};
            userInfo = userInfoList[ops][pToken_to] || userInfo;
        }

        // Try to get userInfo from IndexedDB
        if (!userInfo.ptoken) {
            const user_info_key = 'user_info_list';
            const intao_data = await IntaoDB.getItem(objStores.common_store.name, user_info_key);
            if (intao_data?.values && intao_data.values[ops] && intao_data.values[ops][pToken_to]) {
                let userInfoObj = intao_data.values[ops][pToken_to];
                // Check if data is expired (expires after 2 day)
                if (userInfoObj.last_fetch_time && (Date.now() - userInfoObj.last_fetch_time) <= 172800000) {
                    ft_userInfoList[ops][userInfoObj.ptoken] = userInfoObj;
                    userInfo = userInfoObj;
                }
            }
        }
    }

    // Fetch userInfo from server if not found locally
    if (!userInfo.ptoken) {
        const formData = {
            taoh_action: 'taoh_user_info',
            ops: ops,
            ptoken: pToken_to
        };

        try {
            const srv_userInfoObj = await fetchUserInfoFromServer(formData);
            srv_userInfoObj.last_fetch_time = Date.now();
            ft_userInfoList[ops][srv_userInfoObj.ptoken] = srv_userInfoObj;
            userInfo = srv_userInfoObj;
        } catch (e) {
            console.log('getUserInfo error:', e);
        }
    }

    // If userInfo not found, set default values
    if (!userInfo.ptoken) {
        userInfo = {
            ptoken: pToken_to,
            chat_name: 'Unknown Name',
            avatar: 'default',
            full_location: 'Unknown Location',
            type: 'Unknown Type',
            is_unknown: true,
            last_fetch_time: Date.now()
        };
        ft_userInfoList[ops][userInfo.ptoken] = userInfo;
    }

    return userInfo;
}

async function ft_getRoomInfo(room_hash, ptoken, data = {}, serverFetch = false) {
    if (room_hash.trim() === '') {
        return {};
    }

    let roominfo = {};
    let app = data['app'] || 'custom';
    let type = data['type'] || 'detail';

    if (!serverFetch) {
        // Try to get roominfo from local variable
        if (typeof ft_roomInfoList[room_hash] !== 'undefined') {
            roominfo = ft_roomInfoList[room_hash];
        }

        // Try to get roominfo from global variable
        if (!roominfo.keyslug && typeof roomInfoList[room_hash] !== 'undefined') {
            roominfo = roomInfoList[room_hash];
        }

        // Try to get roominfo from IndexedDB
        if (!roominfo.keyslug) {
            const room_info_key = 'room_info_list';
            const intao_data = await IntaoDB.getItem(objStores.common_store.name, room_info_key);
            if (intao_data && typeof intao_data.values[room_hash] !== 'undefined') {
                let roominfoObj = intao_data.values[room_hash];

                // Check if data is expired (expires after 5 min ((5 * 60) * 1000))
                if (roominfoObj.last_fetch_time && (Date.now() - roominfoObj.last_fetch_time) <= 300000) {
                    ft_roomInfoList[room_hash] = roominfoObj;
                    roominfo = roominfoObj;
                }
            }
        }
    }

    // Fetch roominfo from server if it does not exist locally
    if (!roominfo.keyslug) {
        const formData = {
            "ops": "status",
            "status": "getroom",
            "code": _taoh_ops_code,
            "key": ptoken,
            "keyslug": room_hash,
            "app": app,
            "type": type
        };

        try {
            let srv_roominfoObj = await fetchRoomInfoFromServer(formData);
            if (srv_roominfoObj?.hasOwnProperty('keyslug')) {
                srv_roominfoObj.last_fetch_time = Date.now();
                ft_roomInfoList[srv_roominfoObj.keyslug] = srv_roominfoObj;
                roominfo = srv_roominfoObj;
            }
        } catch (err) {
            if (err.error === 'room_not_exist' && _ntw_ft_ptoken?.trim()) {
                let create_room_info = {
                    keyslug: room_hash,
                    app: 'global',
                    club: {
                        title: 'Networking Chat',
                        description: 'Welcome to the Networking Chat Room',
                        short: 'Connect with your friends.',
                        image: _taoh_curr_app_image || '',
                        square_image: _taoh_curr_app_image_square || '',
                        links: {
                            club: '/club'
                        }
                    }
                };
                // let create_room_data = await taoh_create_room(create_room_info, _ntw_ft_ptoken, data);
                // if (create_room_data.success && create_room_data.output) {
                roominfo = create_room_info;
                ft_roomInfoList[room_hash] = roominfo;
                // }
            } else {
                console.log('getRoomInfo error:', err.message);
            }
        }
    }

    // If roominfo not found, set default values
    if (!roominfo.keyslug) {
        roominfo = {
            keyslug: room_hash,
            club: {
                title: 'Networking Chat',
                links: {
                    club: '/club'
                }
            },
            is_unknown: true,
            last_fetch_time: Date.now()
        };
        ft_roomInfoList[room_hash] = roominfo;
    }

    return roominfo;
}

if (typeof getProfileFollowup !== 'function') {
    function getProfileFollowup(requestData, serverFetch = false) {
        return new Promise((resolve, reject) => {
            if (!requestData.ptoken) {
                reject("profile_token_not_provided");
                return;
            }

            const followUpKey = `${requestData.follow_type}_list_${requestData.ptoken}`;

            const handleResponse = (response, saveToDB = true) => {
                if (response.success) {
                    if (saveToDB) {
                        IntaoDB.setItem(objStores.common_store.name, {
                            taoh_data: followUpKey,
                            values: response,
                            timestamp: Date.now()
                        });
                    }
                    resolve({ requestData, response });
                } else {
                    reject("Failed to fetch followup details! Try Again");
                }
            };

            if (serverFetch) {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'POST',
                    data: {
                        action: 'taoh_followup_users_list',
                        taoh_action: 'taoh_followup_users_list',
                        token: _taoh_ajax_token,
                        ptoken: requestData.ptoken,
                        follow_type: requestData.follow_type
                    },
                    dataType: 'json',
                    success: (response) => handleResponse(response, true),
                    error: (xhr) => {
                        reject(`Error: ${xhr.status}`)
                    }
                });
            } else {
                IntaoDB.getItem(objStores.common_store.name, followUpKey)
                    .then((data) => {
                        if (data?.values) {
                            handleResponse(data.values, false);
                        } else {
                            getProfileFollowup(requestData, true).then(resolve).catch(reject);
                        }
                    })
                    .catch(reject);
            }
        });
    }
}

const buildSkillContent = (skillData, pToken,returnSkill='') => {
    if (!skillData) return '';

    let skill = '';
    const sm = [];
    const skillArray = [];
    let skill_length = Object.keys(skillData).length;

    $.each(skillData, function (k, m) {
        if (typeof m === "string" && m.includes(":>")) {
            const [id, name] = m.split(":>");
            const skillItem = `<span class="btn skill-list skill_directory" data-skillid="${k}" data-skillslug="${id}">${name}</span>`;
            skill += skillItem;
            sm.push(skillItem);
            skillArray.push(name)
        } else {

            const skillItem = `<span class="btn skill-list skill_directory" data-skillid="${k}" data-skillslug="${m.slug}">${m.value}</span>`;
            skill += skillItem;
            sm.push(skillItem);
            skillArray.push(m.value)
        }
    });

    if(returnSkill == 1){
        return skillArray;
    }

    let showtext = '';
    if (skill_length > 3) {
        let use_dnone = (typeof skillShowMore !== 'undefined' && Array.isArray(skillShowMore)) ? skillShowMore.includes(pToken) : false;
        showtext = `<p class="less-content-${pToken} ${(use_dnone ? 'd-none' : '')}">${sm[0]}${sm[1]}${sm[2]}... <span class="show-more fs-12 text-primary" data-id="${pToken}">show more</span></p>
                <p class="more-content-${pToken} ${(use_dnone ? '' : 'd-none')}">${skill} <span class="show-less fs-12 text-primary" data-id="${pToken}">show less</span></p>`;
    } else {
        showtext = `<p class="less-content-${pToken}">${skill}</p>`;
    }
    return showtext;
};

function getTimeGap(v) {
    const { DateTime } = luxon;

    const tz = v.spk_timezoneSelect || 'UTC';

    // Parse event window as LOCAL times in the event’s timezone, then compare as instants
    const startUtc = DateTime.fromISO(v.spk_datefrom, { zone: tz }).toUTC();
    const endUtc   = DateTime.fromISO(v.spk_dateto,   { zone: tz }).toUTC();
    if (!startUtc.isValid || !endUtc.isValid) return false;

    const nowUtc = DateTime.utc();

    return startUtc-nowUtc;
}

const buildSkillContentText = (skillData, pToken,returnSkill='') => {
    if (!skillData) return '';

    let skill = '';
    const sm = [];
    const skillArray = [];
    let skill_length = Object.keys(skillData).length;

    $.each(skillData, function (k, m) {
        if (typeof m === "string" && m.includes(":>")) {
            const [id, name] = m.split(":>");
            const skillItem = name+",";
            skill += skillItem;
            sm.push(skillItem);
            skillArray.push(name)
        } else {
            const skillItem = m.value+",";
            skill += skillItem;
            sm.push(skillItem);
            skillArray.push(m.value)
        }
    });

    return skill;
};

const buildUserMoodStatus = (status) => {
    let [statusText, emoji] = status.split('###');
    let userMoodStatus = '';

    if (emoji) {
        userMoodStatus = `<img width="20" class="emoji-place" src="${_taoh_site_url_root + '/assets/images/emojis/' + emoji + '.svg'}"  alt="moodStatus"/>`;
    }
    userMoodStatus += ` ${statusText}`;
    return userMoodStatus;
};

const formatObject = (obj) => {
    if (typeof obj !== "undefined" && typeof obj === "object") {
        return Object.entries(obj).map(([key, value]) => {
            if (typeof value === "string" && value.includes(":>")) {
                const [slug, name] = value.split(":>");
                return {id: key, slug, name};
            } else {
                return (value['id'] !== undefined)
                    ? { id: value['id'], slug: value['slug'], name: value['name'] }
                    : { id: key, slug: value['slug'], name: value['value'] };
            }
        });
    }
    return {};
};

async function updateUserLiveStatus(pToken) {
    if (!pToken || !navigator.onLine) return {success: false};

    let data = {
        'ops': 'live',
        'status': 'post',
        'code': _taoh_ops_code,
        'key': pToken,
        'ptoken': pToken
    };

    return await new Promise((resolve, reject) => {
        $.post(_taoh_cache_chat_url, data, function (response) {
            let res = {output: false, success: false};
            if (typeof response === 'string') {
                try {
                    res = JSON.parse(response);
                } catch (error) {
                    console.error('Failed to parse updateUserLiveStatus response as JSON:', error);
                }
            } else if (typeof response === 'object') {
                res = response;
            }
            resolve(res);
        });
    });
}

async function getUserLiveStatus(pToken) {
    if (!pToken || !navigator.onLine) return {success: false};

    let data = {
        'ops': 'live',
        'status': 'get',
        'code': _taoh_ops_code,
        'key': pToken,
        'ptoken': pToken,
        'cfcc10': 1,
    };

    return await new Promise((resolve, reject) => {
        $.get(_taoh_cache_chat_url, data, function (response) {
            let res = {output: false, success: false};
            if (typeof response === 'string') {
                try {
                    res = JSON.parse(response);
                } catch (error) {
                    console.error('Failed to parse getUserLiveStatus response as JSON:', error);
                }
            } else if (typeof response === 'object') {
                res = response;
            }
            resolve(res);
        });
    });
}

function showIndexedDBWarning() {
    const modal = document.getElementById("indexedDBWarningModal");
    modal.style.display = "block";
}