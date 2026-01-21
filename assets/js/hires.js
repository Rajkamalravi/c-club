let Base64 = {
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZ" +
        "abcdefghijklmnopqrstuvwxyz0123456789+/=",
    encode: function (e) {
        let t = "";
        let n, r, i, s, o, u, a;
        let f = 0;
        e = Base64._utf8_encode(e);
        while (f < e.length) {
            n = e.charCodeAt(f++);
            r = e.charCodeAt(f++);
            i = e.charCodeAt(f++);
            s = n >> 2;
            o = (n & 3) << 4 | r >> 4;
            u = (r & 15) << 2 | i >> 6;
            a = i & 63;
            if (isNaN(r)) {
                u = a = 64
            } else if (isNaN(i)) {
                a = 64
            }
            t = t +
                this._keyStr.charAt(s) +
                this._keyStr.charAt(o) +
                this._keyStr.charAt(u) +
                this._keyStr.charAt(a)
        }
        return t
    },
    decode: function (e) {
        let t = "";
        let n, r, i;
        let s, o, u, a;
        let f = 0;
        e = e.toString().replace(/[^A-Za-z0-9\+\/\=]/g, "");
        while (f < e.length) {
            s = this._keyStr.indexOf(e.charAt(f++));
            o = this._keyStr.indexOf(e.charAt(f++));
            u = this._keyStr.indexOf(e.charAt(f++));
            a = this._keyStr.indexOf(e.charAt(f++));
            n = s << 2 | o >> 4;
            r = (o & 15) << 4 | u >> 2;
            i = (u & 3) << 6 | a;
            t = t + String.fromCharCode(n);
            if (u != 64) {
                t = t + String.fromCharCode(r)
            }
            if (a != 64) {
                t = t + String.fromCharCode(i)
            }
        }
        t = Base64._utf8_decode(t);
        return t
    },
    _utf8_encode: function (e) {
        e = e.toString().replace(/\r\n/g, "\n");
        let t = "";
        for (let n = 0; n < e.length; n++) {
            let r = e.charCodeAt(n);
            if (r < 128) {
                t += String.fromCharCode(r)
            } else if (r > 127 && r < 2048) {
                t +=
                    String.fromCharCode(r >> 6 | 192);
                t +=
                    String.fromCharCode(r & 63 | 128)
            } else {
                t +=
                    String.fromCharCode(r >> 12 | 224);
                t +=
                    String.fromCharCode(r >> 6 & 63 | 128);
                t +=
                    String.fromCharCode(r & 63 | 128)
            }
        }
        return t
    },
    _utf8_decode: function (e) {
        let t = "";
        let n = 0;
        let r = c1 = c2 = 0;
        while (n < e.length) {
            r = e.charCodeAt(n);
            if (r < 128) {
                t += String.fromCharCode(r);
                n++
            } else if (r > 191 && r < 224) {
                c2 = e.charCodeAt(n + 1);
                t += String.fromCharCode(
                    (r & 31) << 6 | c2 & 63);

                n += 2
            } else {
                c2 = e.charCodeAt(n + 1);
                c3 = e.charCodeAt(n + 2);
                t += String.fromCharCode(
                    (r & 15) << 12 | (c2 & 63)
                    << 6 | c3 & 63);
                n += 3
            }
        }
        return t
    }
}

function crc32(str) {
    var crcTable = window.crcTable || (window.crcTable = makeCRCTable());
    var crc = 0 ^ (-1);
    for (var i = 0; i < str.length; i++) {
        crc = (crc >>> 8) ^ crcTable[(crc ^ str.charCodeAt(i)) & 0xFF];
    }
    return (crc ^ (-1)) >>> 0;
}

function makeCRCTable() {
    var c;
    var crcTable = [];
    for (var n = 0; n < 256; n++) {
        c = n;
        for (var k = 0; k < 8; k++) {
            c = ((c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1));
        }
        crcTable[n] = c;
    }
    return crcTable;
}

function getExplodeNames(input) {
    let keys = [];

    if (typeof input === 'string') {
        try {
            input = JSON.parse(input); // Try parsing if it's a JSON string
        } catch (e) {
            return []; // Return an empty array if parsing fails
        }
    }

    if (Array.isArray(input)) {
        input.forEach(value => {
            if (typeof value === 'string') {
                let parts = value.split(':>');
                if (parts.length > 1) {
                    keys.push(parts[1]);
                }
            }
        });
    } else {
        return []; // Return empty array if not an array
    }

    return keys;
}

function displayTaohFormatted(string) {
    let country, company, title, skill;
    //alert(_taoh_get_company)
    if (_is_logged_in && _is_profile_complete) {
        // Assuming these functions or variables fetch the required data
        country = _taoh_get_country;
        company = getExplodeNames(_taoh_get_company);
        title = getExplodeNames(_taoh_get_title);
        skill = getExplodeNames(_taoh_get_skill);
        // Sort skills alphabetically and join them into a string
        skill = skill.sort((a, b) => a.localeCompare(b)).join(', ');
    } else {
        // Default values if the user is not logged in
        country = _taoh_get_country;
        company = _taoh_get_company;
        title = _taoh_get_title;
        skill = _taoh_get_skill;
    }

    // Site information, these should be available in the client-side
    let logo = _taoh_site_logo; // Replace with actual logo URL
    let sqLogo = _taoh_site_sq_logo; // Replace with actual square logo URL

    // Placeholders and their replacements
    let find = ['[sitenameslug]', '[sitelogosqurl]', '[sitelogourl]', '[sitemytitle]', '[sitemyskill]', '[sitemycompany]', '[sitemycountry]'];
    let replace = [_taoh_site_name, sqLogo, logo, title, skill, company, country];
    
    // Perform the replacement
    let text = string;
    for (let i = 0; i < find.length; i++) {
        text = text.split(find[i]).join(replace[i]);
    }
    
    // Return the modified string
    return text;
}

function toEntries(v) {
    return Array.isArray(v)
        ? v.map((val, idx) => [idx, val])
        : Object.entries(v || {});
}

function taoh_get_profile_image(){
    var logged_user = logged_user_data;
    var jsonObject = JSON.parse(logged_user);
    //console.log('==========jsonObject================',jsonObject);
    if(jsonObject.avatar_image){
        return '<img width="40" height="40" style="border-radius: 20px;" src="'+jsonObject.avatar_image+'" alt="Profile Image">';
    }else if(jsonObject.avatar){
        return '<img width="40" height="40" src="'+_taoh_ops_prefix+'/avatar/PNG/128/'+jsonObject.avatar+'.png" alt="Profile Image">';
    }else{
        return '<img width="40" height="40" src="'+_taoh_ops_prefix+'/avatar/PNG/128/avatar_def.png" alt="Profile Image">';
    }
}

async function taohPost(url, taohVals, debug = false) {
    const postData = new URLSearchParams(taohVals);

    if (debug) {
        console.log(`${url}?${postData.toString()}`);
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: postData
        });

        const result = await response.text();

        if (new URLSearchParams(window.location.search).get('debug') === '1') {
            const urlPrint = `URL: [${url}]; PostData: [${postData.toString()}];`;
            console.log(urlPrint);
            const date = new Date();
            const filename = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}_logs.cache`;
            const logEntry = `${Math.floor(Date.now() / 1000)}-${urlPrint}\n`;

            // Here we would need to use a server-side script to append the log entry to the file.
            // Since JavaScript running in the browser can't directly write to the file system,
            // an AJAX request would be needed to a server-side endpoint handling the log writing.
            await fetch('/path/to/logging/endpoint', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ filename, logEntry })
            });
        }

        return result;
    } catch (error) {
        console.error('Error making POST request:', error);
        return null;
    }
}

async function taohGet(url, taohVals) {
    const urlParams = new URLSearchParams(taohVals);
    const finalUrl = `${url}?${urlParams.toString().replace(/&amp;/g, '&')}`;

    try {
        const response = await fetch(finalUrl, {
            method: 'GET'
        });

        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.statusText}`);
        }

        const result = await response.text();
        return result;
    } catch (error) {
        console.error('Error making GET request:', error);
        return null;
    }
}

function taoh_set_error_message_old(message, time=2000) {
    $('#toast').show();
    $('#toast').addClass('toast_active');
    $('#toast_error').html('');                    
    $('#toast_error').html(`<div class='toasterror_class'><span><i class='las la-check-circle mr-2 info_icon'></i> 
        ${message} &nbsp;
    <span class='toast_dismiss' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</span>
    </span></div>`);
    setTimeout(function () {   
        $("#toast").removeClass("toast_active");
        $("#toast_container").removeClass("toast-middle-con");
    }, time);
}

function taoh_set_success_message_old(message, time=5000) {
    $('#toast').show();
    $('#toast').addClass('toast_active');
    $('#toast_error').html('');                    
    $('#toast_error').html(`<div class='success_class'><span><i class='las la-check-circle mr-2 info_icon'></i> 
        ${message} &nbsp;
    <span class='toast_dismiss' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</span>
    </span></div>`);
    setTimeout(function () {   
        $('#toast').removeClass('toast_active');
        $("#toast_container").removeClass("toast-middle-con");
    }, time);
}

function taoh_set_warning_message_old(message, time=1000) {
    $('#toast').show();
    $('#toast').addClass('toast_active');
    $('#toast_error').html('');
    $('#toast_error').html(`<div class='info_class'><span><i class='las la-check-circle mr-2 info_icon'></i> 
        ${message} &nbsp;
    <span class='toast_dismiss' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</span>
    </span></div>`);
    setTimeout(function () {
        $("#toast").removeClass("toast_active");
        $("#toast_container").removeClass("toast-middle-con");
    }, time);
}

let dojoToastQueue = [];
let dojoToastActive = false;

function taoh_show_queued_success(message, time = 5000) {
    dojoToastQueue.push({ message, time });
    runDojoToastQueue();
}

function runDojoToastQueue() {
    if (dojoToastActive || dojoToastQueue.length === 0) return;

    dojoToastActive = true;

    const { message, time } = dojoToastQueue.shift();
    taoh_set_success_message(message, time);

    setTimeout(() => {
        dojoToastActive = false;
        runDojoToastQueue();
    }, time + 300);
}


function taoh_set_info_message(message, time = 5000, position = 'toast-middle-right', buttons = [], options = {}) {
    taoh_dojo_toast_message(message, time, 'info', position, buttons, options);
}

function taoh_set_warning_message(message, time = 5000, position = 'toast-middle-right', buttons = [], options = {}) {
    taoh_dojo_toast_message(message, time, 'warning', position, buttons, options);
}

function taoh_set_error_message(message, time = 5000, position = 'toast-middle-right', buttons = [], options = {}) {
    taoh_dojo_toast_message(message, time, 'error', position, buttons, options);
}


function taoh_set_success_message(message, time = 5000, position = 'toast-middle-right', buttons = [], options = {}) {
    taoh_dojo_toast_message(message, time, 'success', position, buttons, options);
}
function taoh_dojo_toast_message(message, time = 5000, type = 'success', position = 'toast-middle-right', buttons = [], options = {}) {
    $('#toast').show();
    $('#toast').addClass('toast_active');

    let typeClass;
    let iconSvg = '';
    if (type === 'error') {
        typeClass = 'toasterror_class';
        iconSvg = `<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#D13D13"/>
                        <path d="M23.5265 10.7325C24.1512 10.1078 24.1512 9.09324 23.5265 8.46853C22.9018 7.84382 21.8872 7.84382 21.2625 8.46853L16 13.7361L10.7325 8.47353C10.1078 7.84882 9.09324 7.84882 8.46853 8.47353C7.84382 9.09824 7.84382 10.1128 8.46853 10.7375L13.7361 16L8.47353 21.2675C7.84882 21.8922 7.84882 22.9068 8.47353 23.5315C9.09824 24.1562 10.1128 24.1562 10.7375 23.5315L16 18.2639L21.2675 23.5265C21.8922 24.1512 22.9068 24.1512 23.5315 23.5265C24.1562 22.9018 24.1562 21.8872 23.5315 21.2625L18.2639 16L23.5265 10.7325Z" fill="white"/>
                    </svg>`;
    } else if (type === 'info') {
        typeClass = 'info_class';
        iconSvg = `<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="15.5491" cy="15.1051" rx="9.28352" ry="9.3986" fill="white"/>
                        <path d="M16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32ZM13.5 21H15V17H13.5C12.6687 17 12 16.3312 12 15.5C12 14.6687 12.6687 14 13.5 14H16.5C17.3312 14 18 14.6687 18 15.5V21H18.5C19.3312 21 20 21.6688 20 22.5C20 23.3312 19.3312 24 18.5 24H13.5C12.6687 24 12 23.3312 12 22.5C12 21.6688 12.6687 21 13.5 21ZM16 8C16.5304 8 17.0391 8.21071 17.4142 8.58579C17.7893 8.96086 18 9.46957 18 10C18 10.5304 17.7893 11.0391 17.4142 11.4142C17.0391 11.7893 16.5304 12 16 12C15.4696 12 14.9609 11.7893 14.5858 11.4142C14.2107 11.0391 14 10.5304 14 10C14 9.46957 14.2107 8.96086 14.5858 8.58579C14.9609 8.21071 15.4696 8 16 8Z" fill="#0058D8"/>
                    </svg>`;
    } else if (type === 'warning') {
        typeClass = 'warning_class';
        iconSvg = `<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.9988 0C16.8864 0 17.7052 0.535714 18.1552 1.41429L31.6562 27.7C32.1125 28.5857 32.1125 29.6786 31.6687 30.5643C31.2249 31.45 30.3936 32 29.4998 32H2.49787C1.60406 32 0.772748 31.45 0.328967 30.5643C-0.114815 29.6786 -0.108565 28.5786 0.341468 27.7L13.8424 1.41429C14.2925 0.535714 15.1113 0 15.9988 0ZM15.9988 9.14286C15.1675 9.14286 14.4987 9.90714 14.4987 10.8571V18.8571C14.4987 19.8071 15.1675 20.5714 15.9988 20.5714C16.8301 20.5714 17.4989 19.8071 17.4989 18.8571V10.8571C17.4989 9.90714 16.8301 9.14286 15.9988 9.14286ZM17.999 25.1429C17.999 24.5366 17.7883 23.9553 17.4132 23.5266C17.0381 23.098 16.5293 22.8571 15.9988 22.8571C15.4684 22.8571 14.9596 23.098 14.5845 23.5266C14.2094 23.9553 13.9987 24.5366 13.9987 25.1429C13.9987 25.7491 14.2094 26.3304 14.5845 26.7591C14.9596 27.1878 15.4684 27.4286 15.9988 27.4286C16.5293 27.4286 17.0381 27.1878 17.4132 26.7591C17.7883 26.3304 17.999 25.7491 17.999 25.1429Z" fill="#FFBA37"/>
                    </svg>`;
    } else if (type === 'success') {
        typeClass = 'success_class';
        iconSvg = `<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32ZM23.0625 13.0625L15.0625 21.0625C14.475 21.65 13.525 21.65 12.9438 21.0625L8.94375 17.0625C8.35625 16.475 8.35625 15.525 8.94375 14.9438C9.53125 14.3625 10.4812 14.3563 11.0625 14.9438L14 17.8813L20.9375 10.9375C21.525 10.35 22.475 10.35 23.0562 10.9375C23.6375 11.525 23.6437 12.475 23.0562 13.0562L23.0625 13.0625Z" fill="#0F9D58"/>
                    </svg>`;
    } else {
        typeClass = 'success_class';
    }

    if (position) {
        $('#toast').removeClass('toast-top-right toast-middle-right toast-bottom-right toast-middle');
        $('#toast').addClass(position);

        if (position === 'toast-middle') {
            $('#toast_container').addClass('toast-middle-con');
        } else {
            $('#toast_container').removeClass('toast-middle-con');
        }
    }

    let buttonHTML = '';
    buttons.forEach(function(button, index) {
        buttonHTML += `<button class="toast-btn ${button.class || ''}" id="btn-${index}">${button.text}</button>`;
    });

    $("#toast_error").html(`
        <div>
            <div class="dojo toast-header ${typeClass} shadow-none pl-3">

                <h5 class="heading">
                    ${iconSvg}
                    Dojo Says !
                </h5>

                <!-- dojo svg -->
                <svg class="dojo-v1-svg" width="69" height="51" viewBox="0 0 69 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M49.5658 17C49.5658 17 44.022 11.0179 45.1519 0L58 2.08076C58 2.08076 57.4602 11.3992 49.5658 17Z" fill="#FF3B38"/>
                    <path d="M52 19.4342C52 19.4342 57.9821 24.978 69 23.8481L66.9192 11C66.9192 11 57.6008 11.5398 52 19.4342Z" fill="#FF3B38"/>
                    <path d="M66 41C66 36.6664 65.1464 32.3752 63.488 28.3714C61.8296 24.3677 59.3989 20.7298 56.3345 17.6655C53.2702 14.6011 49.6323 12.1704 45.6286 10.512C41.6248 8.85357 37.3336 8 33 8C28.6664 8 24.3752 8.85357 20.3714 10.512C16.3677 12.1704 12.7298 14.6011 9.66547 17.6655C6.60114 20.7298 4.17038 24.3677 2.51197 28.3714C0.853569 32.3752 -3.78857e-07 36.6664 0 41L33 41H66Z" fill="url(#paint0_linear_7568_2)"/>
                    <path d="M50 41C50 38.7675 49.5603 36.5569 48.706 34.4944C47.8516 32.4318 46.5994 30.5578 45.0208 28.9792C43.4422 27.4006 41.5682 26.1484 39.5056 25.294C37.4431 24.4397 35.2325 24 33 24C30.7675 24 28.5569 24.4397 26.4944 25.294C24.4318 26.1484 22.5578 27.4006 20.9792 28.9792C19.4006 30.5578 18.1484 32.4318 17.294 34.4944C16.4397 36.5569 16 38.7675 16 41L33 41H50Z" fill="url(#paint1_linear_7568_2)"/>
                    <path d="M32.6953 33.0198C32.7581 32.9963 32.8268 32.9995 32.9012 33.0011C32.9987 33.0044 33.0194 33.0919 33.0293 33.1681C33.045 33.2921 33.0698 33.4258 33.1054 33.5684C33.1153 33.6097 33.1211 33.6519 33.1244 33.6932C33.1351 33.8764 33.1483 34.0182 33.2004 34.177C33.2475 34.3237 33.2484 34.4785 33.2938 34.6179C33.3195 34.6957 33.3567 34.8188 33.3674 34.9226C33.3773 35.0158 33.4005 35.1009 33.436 35.1795C33.4865 35.2897 33.4832 35.4218 33.5294 35.5377C33.5824 35.6706 33.6055 35.8019 33.6568 35.9307C33.6882 36.0126 33.7105 36.0928 33.7345 36.1747C33.746 36.2119 33.7576 36.2443 33.7708 36.2711C33.8204 36.3748 33.8502 36.4818 33.9023 36.5879C33.9883 36.7614 34.0709 36.9753 34.1768 37.169C34.2594 37.3205 34.3685 37.5118 34.4769 37.6552C34.5645 37.7744 34.619 37.8797 34.7125 37.9883C34.8547 38.148 35.0539 38.4032 35.244 38.5953C35.4143 38.7671 35.5962 38.9025 35.8293 39.0913C35.926 39.1691 36.0286 39.2444 36.1368 39.3182C36.3766 39.4795 36.5866 39.6091 36.7626 39.7039C37.1471 39.9122 37.5802 40.1051 38.063 40.2801C38.0887 40.2899 38.1523 40.3101 38.2515 40.3417C38.3375 40.3677 38.4226 40.4041 38.5053 40.4268C38.6144 40.456 38.7012 40.4827 38.7657 40.5087C38.8294 40.5346 38.8996 40.554 38.9774 40.5686C39.1038 40.5913 39.2287 40.6562 39.3659 40.6756C39.4403 40.6853 39.5205 40.7064 39.6098 40.7412C39.7395 40.7907 39.8677 40.7891 40.0041 40.8247C40.0091 40.8255 40.0487 40.8377 40.1231 40.8604C40.2372 40.8936 40.3315 40.9058 40.4555 40.9236C40.5555 40.9382 40.687 40.9965 40.8093 41.016C40.8192 41.0168 40.873 41.0233 40.9722 41.0338C41.0019 41.037 41.0325 41.0419 41.0623 41.046C41.0871 41.0492 41.1152 41.0557 41.1507 41.0662C41.2722 41.1043 41.3963 41.1294 41.5219 41.1424C41.693 41.1594 41.7831 41.1748 41.8989 41.2729C42.0105 41.3669 42.0675 41.567 41.8658 41.6076C41.8162 41.6189 41.693 41.6448 41.4938 41.687C41.4128 41.704 41.3293 41.7162 41.24 41.721C41.0978 41.7299 41.0127 41.7356 40.9854 41.7372C40.9068 41.7437 40.8572 41.7486 40.8366 41.7518C40.6845 41.7688 40.4927 41.8328 40.3877 41.8361C40.2381 41.8401 40.1355 41.8491 40.0818 41.862C39.8768 41.9123 39.7925 41.9398 39.637 41.9568C39.5403 41.9674 39.4775 41.9771 39.4477 41.986C39.3088 42.0282 39.2204 42.0533 39.184 42.0581C39.0071 42.0897 38.8856 42.1181 38.8194 42.1424C38.7359 42.174 38.6698 42.1829 38.5681 42.2129C38.4135 42.2583 38.2317 42.3158 38.0225 42.3823C37.8844 42.4269 37.7604 42.4731 37.6488 42.5217C37.5959 42.5444 37.4736 42.5979 37.2818 42.6805C36.8445 42.8685 36.4113 43.1262 36.0442 43.4423C35.7731 43.6757 35.5151 43.9342 35.2679 44.2154C35.1464 44.3532 35.0191 44.5469 34.9216 44.6709C34.8455 44.7681 34.7777 44.8929 34.6959 45.0072C34.6587 45.0615 34.6289 45.1069 34.6066 45.1425C34.4669 45.3799 34.3842 45.5266 34.3586 45.5817C34.2966 45.7179 34.2189 45.8889 34.123 46.0939C33.9948 46.3686 33.9056 46.5826 33.8568 46.7358C33.827 46.8298 33.8055 46.893 33.7906 46.9238C33.7559 47.0008 33.7336 47.0624 33.7245 47.1102C33.7071 47.2017 33.6435 47.3217 33.6203 47.4351C33.6005 47.53 33.5889 47.5907 33.5476 47.692C33.5187 47.7617 33.5021 47.8112 33.4988 47.8412C33.4831 47.9514 33.4765 48.0259 33.4451 48.1216C33.4087 48.2253 33.3839 48.3355 33.3698 48.4522C33.3508 48.6086 33.293 48.7577 33.2706 48.8939C33.25 49.0227 33.25 49.2026 33.221 49.342C33.1979 49.4482 33.1756 49.5292 33.164 49.6338C33.1284 49.9344 33.1342 50.2197 33.1144 50.543C33.1061 50.6638 33.0598 51.0779 32.8375 50.9871C32.6944 50.9296 32.6647 50.791 32.6597 50.6573C32.6581 50.5925 32.6556 50.4822 32.6531 50.3299C32.6498 50.103 32.5522 49.8882 32.5398 49.6662C32.5365 49.6111 32.5299 49.5325 32.52 49.4296C32.5076 49.3031 32.4737 49.218 32.4415 49.0884C32.4109 48.9652 32.42 48.7764 32.3803 48.6548C32.3472 48.5543 32.3125 48.4441 32.3018 48.308C32.2951 48.2067 32.2629 48.0705 32.2067 47.8987C32.1951 47.8606 32.1827 47.7812 32.1728 47.6597C32.1695 47.6256 32.1505 47.5551 32.1141 47.4506C32.0926 47.3866 32.0761 47.325 32.0645 47.2634C32.0471 47.1661 32.0372 47.0908 32.0075 47.0146C31.9545 46.8785 31.938 46.7326 31.8909 46.5867C31.8347 46.4109 31.7884 46.2674 31.7528 46.1548C31.6991 45.983 31.647 45.8403 31.5982 45.7277C31.4808 45.4538 31.3858 45.2471 31.3163 45.111C31.1932 44.8719 31.0948 44.7058 31.022 44.6126C30.8699 44.4173 30.76 44.2366 30.612 44.0834C30.4268 43.8889 30.2516 43.7195 30.0308 43.5712C29.9101 43.4894 29.801 43.3937 29.6588 43.3087C29.4422 43.1798 29.2562 43.0761 29.1008 42.9967C28.8553 42.8719 28.6379 42.7932 28.3245 42.6757C28.265 42.6539 28.2088 42.6433 28.1542 42.619C28.0856 42.589 28.012 42.5639 27.9335 42.5436C27.8219 42.5145 27.7483 42.4942 27.7095 42.4796C27.5524 42.4245 27.4284 42.4197 27.2755 42.3645C27.1994 42.337 27.0828 42.3135 26.9249 42.294C26.8398 42.2843 26.6902 42.2252 26.5736 42.2065C26.4711 42.1903 26.3297 42.1814 26.2165 42.149C26.1437 42.1279 26.0817 42.1101 26.0329 42.0955C25.9949 42.0841 25.9023 42.076 25.7552 42.0696C25.7014 42.0671 25.6287 42.0534 25.5386 42.0274C25.3641 41.9756 25.2261 41.9658 25.0169 41.9512C24.9582 41.9472 24.9045 41.9383 24.8375 41.9188C24.6722 41.8694 24.5317 41.8556 24.3498 41.8394C24.2721 41.8313 24.2159 41.8216 24.1836 41.8078C24.1134 41.7778 24.0059 41.7033 24.0009 41.6222C23.991 41.4764 24.0605 41.3969 24.2109 41.384C24.3324 41.3743 24.4308 41.3629 24.5044 41.3483C24.6259 41.3256 24.7359 41.3013 24.8359 41.2754C24.8747 41.2657 24.9574 41.2576 25.0864 41.2527C25.1666 41.2495 25.2716 41.2243 25.4014 41.1789C25.5138 41.14 25.6609 41.1498 25.775 41.1238C25.7998 41.1182 25.856 41.1003 25.9453 41.0679C26.009 41.0452 26.0941 41.029 26.1999 41.0209C26.233 41.0193 26.3198 40.9917 26.462 40.9415C26.5389 40.9139 26.6769 40.9107 26.7588 40.8783C26.8133 40.8556 26.8894 40.8224 26.9613 40.807C27.0448 40.7891 27.1167 40.7673 27.1754 40.7438C27.306 40.6919 27.4119 40.6708 27.5358 40.6157C27.583 40.5955 27.6301 40.576 27.678 40.5574C28.1228 40.3848 28.5659 40.1805 28.9718 39.9309C29.0809 39.8637 29.1735 39.8175 29.2636 39.7551C29.4182 39.6489 29.6001 39.5355 29.7621 39.4082C29.9143 39.2883 30.1003 39.1692 30.221 39.0557C30.3739 38.9123 30.5483 38.7421 30.7435 38.5492C30.8972 38.3969 30.9964 38.2867 31.0411 38.2194C31.089 38.144 31.1452 38.0662 31.208 37.9836C31.2858 37.8847 31.3403 37.8053 31.3759 37.7486C31.5305 37.4901 31.6569 37.2494 31.7561 37.0241C31.7735 36.9868 31.7958 36.9325 31.8223 36.8644C31.8727 36.734 31.9157 36.6529 31.9455 36.533C31.9612 36.473 31.981 36.4146 32.005 36.3595C32.0562 36.2445 32.0604 36.1351 32.1042 36.0184C32.1472 35.9025 32.1786 35.7858 32.1959 35.6683C32.2001 35.6432 32.2158 35.5945 32.2431 35.5224C32.3009 35.366 32.2918 35.2088 32.3439 35.0524C32.3555 35.0135 32.3737 34.9576 32.3968 34.883C32.4084 34.8425 32.4183 34.7444 32.4266 34.5888C32.4282 34.5524 32.4514 34.4729 32.4944 34.353C32.5109 34.3076 32.5233 34.2217 32.5308 34.0937C32.5456 33.8603 32.5539 33.7371 32.5572 33.7242C32.5853 33.6285 32.6151 33.5199 32.6267 33.4113C32.6448 33.2574 32.6564 33.1407 32.6655 33.0588C32.668 33.0402 32.6779 33.0271 32.6953 33.0198Z" fill="white"/>
                    <defs>
                    <linearGradient id="paint0_linear_7568_2" x1="33" y1="8" x2="33" y2="74" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FDCC6E"/>
                    <stop offset="1" stop-color="#FF6600"/>
                    </linearGradient>
                    <linearGradient id="paint1_linear_7568_2" x1="33" y1="24" x2="33" y2="58" gradientUnits="userSpaceOnUse">
                    <stop offset="0.1" stop-color="#FD1D1D"/>
                    <stop offset="0.9" stop-color="#FF6600"/>
                    </linearGradient>
                    </defs>
                </svg>                
                ${!options?.hideDismissBtn? `<button type="button" class='btn toast_dismiss toast-v2-dismiss shadow-none' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</button>` : ''}
                
            </div>

            <div class="toast-body px-3 pt-3 pb-2">
                <p class="sm-text mb-2">${message}</p>
                
                <div class="toast-buttons">
                    ${buttonHTML}
                </div>
            </div>
        </div>`
    );

    buttons.forEach(function(button, index) {
        const btn = document.getElementById(`btn-${index}`);
        btn.addEventListener('click', function() {
            button.action(); // Execute the associated action
            $("#toast").removeClass("toast_active");
            $("#toast_container").removeClass("toast-middle-con");
        });
    });

     
    
        
     // Dismiss the toast after the specified time
    /*if (time && time!= null) {
        //alert();
        setTimeout(function () {
            $("#toast").removeClass("toast_active");
            $("#toast_container").removeClass("toast-middle-con");
        }, time);
    }*/

    startAutoClose(time);

    

}

    let toastTimer;
    let time = 5000; // 5 seconds


    function showToast() {
        $("#toast").addClass("toast_active");
        $("#toast_container").addClass("toast-middle-con");

        startAutoClose(time);
    }

    function startAutoClose(timing='') {
        if(timing && timing != '') {
            //alert(0);
            clearTimeout(toastTimer); // safety
            toastTimer = setTimeout(() => {
                closeToast();
            }, timing);
        }
    }

    function closeToast() {
        $("#toast").removeClass("toast_active");
        $("#toast_container").removeClass("toast-middle-con");
    }

    // 🔹 Pause on hover
    $("#toast_container").on("mouseenter", function () {
       // alert(1)
        clearTimeout(toastTimer);
    });

    // 🔹 Resume on leave
    $("#toast_container").on("mouseleave", function () {
       // alert(2)
        startAutoClose(time);
    });



$(document).on('click', '.toast_dismiss', function () {
    dojoToastActive = false;
    runDojoToastQueue();
});

function savetaodata() {
    var global_settings = $('#global_settings').val();
    getIntaoDb().then((db) => {
        const dataStoreName = objStores.data_store.name;
        const APIStoreName = objStores.api_store.name;
        if (db.objectStoreNames.contains(APIStoreName)) {
            //console.log('-------APIStoreName---if-----',APIStoreName)  
            const apirequest = db.transaction(APIStoreName).objectStore(APIStoreName).get(index_name); // get ttl data
            apirequest.onsuccess = () => {
                const apiqueue = apirequest.result;
                if (apiqueue) {
                   // console.log('-------apiqueue----if----')
                    const apiqueuedata = apirequest.result.value;
                    if (apiqueuedata == 1) {
                        const datarequest = db.transaction(dataStoreName).objectStore(dataStoreName).get(index_name); // get main data
                        datarequest.onsuccess = () => {
                            if (datarequest.result != undefined) {
                                const storedata = datarequest.result.values;
                                saveapidata(storedata);
                                let obj_api_data = {
                                    [objStores.api_store.name]: ''
                                };
                                Object.keys(obj_api_data).forEach(key => {
                                    IntaoDB.removeItem(key, index_name).catch((err) => console.log('Storage failed', err));
                                });
                            }
                        }
                    }
                }
                else{
                    //kalpana added
                    //console.log('-------apiqueue----else----',global_settings)
                    if(global_settings != '' && global_settings ==1){
                        
                        $('#global_settings').val(0);
                        taoh_set_success_message('Settings has been saved successfully');
                        setTimeout(function () {
                            window.location.reload();
                        }, 5000);
                    }
                }
            }
        }
        
    });
}

function saveapidata(storedata) {
    // post data to api
    jQuery.post(_action_url+'/settings', storedata, function (response) {
        console.log("response is" + response);
        //alert(response);
        if (response == 0) {
            taoh_set_error_message('Sorry, Settings has not been saved. Please try again.');
        } else {
            taoh_set_success_message('Settings has been saved');
            
            window.location.reload();
            setTimeout(function () {
               
            }, 3000);
        }

        let obj_data = {
            [objStores.data_store.name]: '',
            [objStores.ttl_store.name]: '',
            [objStores.api_store.name]: ''
        };
        Object.keys(obj_data).forEach(key => {
            IntaoDB.removeItem(key, index_name).catch((err) => console.log('Storage failed', err));
        });
        // clearObjectStore(index_name);
    });
}

function decode(str) {
    try {
        if (!str || typeof str !== 'string') {
            return '';
        }        
        var decodedHtml = decodeURIComponent(str);
        var html_decode = (decodedHtml !== "") ? decodedHtml.substring(0, 245) + "...." : '';
        var textarea = document.createElement('textarea');
        textarea.innerHTML = html_decode;
        return textarea.value;
    } catch (error) {       
        return '';
    }
}

function intao_delete(mod){
    let cache_delete = mod;
    let dataStoreName = '';
    if(cache_delete == 'recipe' || cache_delete == 'tags' || cache_delete == 'read'){
      dataStoreName = READStore;
    }
    getIntaoDb(dbName).then((db) => {
      const transaction = db.transaction(dataStoreName, 'readwrite');
      const objectStore = transaction.objectStore(dataStoreName);
      const request = objectStore.openCursor();
      request.onsuccess = (event) => {
        const cursor = event.target.result;
        console.log(cursor);
        if (cursor) {
          const index_key = cursor.primaryKey;
          if(
            index_key.includes(cache_delete)
          ){
            objectStore.delete(index_key);
          }
          cursor.continue();
        }
      };
      
    });
    const newUrl = new URL(location.href);
    if(cache_delete == 'recipe' || cache_delete == 'read' || cache_delete == 'tags'){
      newUrl.searchParams.delete('intao_delete');
    }
    window.history.replaceState({}, document.title, newUrl.href);
}

function intao_delete1(mod){
    let cache_delete = mod;
    let dataStoreName = '';
    if(cache_delete == 'tags'){
      dataStoreName = READStore;
    }
    getIntaoDb(dbName).then((db) => {
      const transaction = db.transaction(dataStoreName, 'readwrite');
      const objectStore = transaction.objectStore(dataStoreName);
      const request = objectStore.openCursor();
      request.onsuccess = (event) => {
        const cursor = event.target.result;
        console.log(cursor);
        if (cursor) {
          const index_key = cursor.primaryKey;
          if(
            index_key.includes(cache_delete)
          ){
            objectStore.delete(index_key);
          }
          cursor.continue();
        }
      };
      
    });
    const newUrl = new URL(location.href);
    if(cache_delete == 'tags'){
      newUrl.searchParams.delete('intao_delete1');
    }
    window.history.replaceState({}, document.title, newUrl.href);
}

async function taoh_get_room(room_hash, ptoken, data = {}) {
    let type = data['type'] || 'detail';

    let formdata = new FormData();
    formdata.append('ops', 'status');
    formdata.append('status', 'getroom');
    formdata.append('code', _taoh_ops_code);
    formdata.append('key', ptoken);
    formdata.append('keyslug', room_hash);
    formdata.append('type', type);

    if(data.hasOwnProperty('app')){
        formdata.append('app', data['app']);
    }

    try {
        let response = await fetch(_taoh_cache_chat_proc_url, {
            method: 'POST',
            body: formdata
        });
        return await response.json();
    } catch (error) {
        console.error(error);
        return { success: false, output: false };
    }
}

async function taoh_create_room(room_info, ptoken, data = {}) {
    let type = data['type'] || 'detail';

    let formdata = new FormData();
    formdata.append('ops', 'status');
    formdata.append('status', 'postroom');
    formdata.append('code', _taoh_ops_code);
    formdata.append('key', ptoken);
    formdata.append('keyslug', room_info.keyslug);
    formdata.append('type', type);
    formdata.append('value', JSON.stringify(room_info));

    if(data.hasOwnProperty('app')){
        formdata.append('app', data['app']);
    }

    try {
        let response = await fetch(_taoh_cache_chat_proc_url, {
            method: 'POST',
            body: formdata
        });
        return await response.json();
    } catch (error) {
        console.error(error);
        return { success: false, output: false };
    }
}

function timetotimestamp(time) {
    let date = new Date(time);
    var year = date.getFullYear();
    var month = ('0' + (date.getMonth() + 1)).slice(-2); // Months are zero-based
    var day = ('0' + date.getDate()).slice(-2);
    var hours = ('0' + date.getHours()).slice(-2);
    var minutes = ('0' + date.getMinutes()).slice(-2);
    var seconds = ('0' + date.getSeconds()).slice(-2);
    // Combine into the desired format
    var formattedDateTime = year + month + day + hours + minutes + seconds;
    return formattedDateTime;
}

function taohFullyearConvert(timestamp, convert = false) {
    if (convert) {
      const currentYear = new Date().getFullYear();
      // Get the first two digits of the current year (e.g., "20")
      const firstTwoDigits = currentYear.toString().substring(0, 2);
      timestamp = firstTwoDigits + timestamp;
    }
  
    // Ensure the timestamp length is correct (14 characters)
    if (timestamp.length !== 14) {
      throw new Error("Invalid timestamp length.");
    }
  
    // Extract year, month, day, hour, minute, and second from the timestamp
    const year = parseInt(timestamp.substring(0, 4), 10);
    const month = parseInt(timestamp.substring(4, 6), 10) - 1; // Month is 0-based in JS
    const day = parseInt(timestamp.substring(6, 8), 10);
    const hour = parseInt(timestamp.substring(8, 10), 10);
    const minute = parseInt(timestamp.substring(10, 12), 10);
    const second = parseInt(timestamp.substring(12, 14), 10);
  
    // Create a Date object from the parsed components
    const date = new Date(year, month, day, hour, minute, second);
  
    // Get the current date and time
    const now = new Date();
  
    // Calculate the difference in time
    const diffInSeconds = Math.floor((now - date) / 1000);
  
    // Calculate time units
    const years = Math.floor(diffInSeconds / (3600 * 24 * 365));
    const months = Math.floor(diffInSeconds / (3600 * 24 * 30));
    const days = Math.floor(diffInSeconds / (3600 * 24));
    const hours = Math.floor(diffInSeconds / 3600);
    const minutes = Math.floor(diffInSeconds / 60);
    const seconds = diffInSeconds;
  
    // Determine the appropriate time unit to return
    if (years > 0) {
      return `${years}year ago`;
    } else if (months > 0) {
      return `${months}month ago`;
    } else if (days > 0) {
      return `${days}days ago`;
    } else if (hours > 0) {
      return `${hours}hour ago`;
    } else if (minutes > 0) {
      return `${minutes}minutes ago`;
    } else {
      return `${seconds}seconds ago`;
    }
  }

  function formatDate(dateString) {
    const date = new Date(dateString);
    console.log('date',date)
    // Options to format the date
    const options = {
      year: 'numeric',
      month: 'long', // Use 'short' for abbreviated month (e.g., "Aug")
      day: 'numeric'
    };
  
    return date.toLocaleDateString('en-US', options);
  }

function customUrlDecode(text) {
    if (!text || text.length < 1) return text;

    try {
        return decodeURIComponent(text.replace(/\+/g, ' '));
    } catch (e) {
        console.warn('Malformed URI, returning original text:', text);
        return text.replace(/\+/g, ' ');
    }
}

  function renderJobType(placeType) {
    // Define the mapping for place types
    const placeTypeMap = {
    "ons": "Onsite",
    "rem": "Remote",
    "hyb": "Hybrid"
    };

    // Get the job type based on the placeType key
    return placeTypeMap[placeType];
  }

  function renderRoleType(roletype) {
    // Define the mapping for role types
    const roleTypeMap = {
    "remo": "Remote Work",
    "full": "Full Time",
    "part": "Part Time",
    "temp": "Temporary",
    "free": "Freelance",
    "cont": "Contract",
    "pdin": "Paid Internship",
    "unin": "Unpaid Internship",
    "voln": "Volunteer"
    };

    // Get the role types from the response
    const roleItems = roletype.map(type => roleTypeMap[type] || type);

    // Join role items with <br> tags
    const roleTypeHtml = roleItems.join(',');
    return roleTypeHtml;
  }

function cleanFontstyles(text) {
    if (!text) return '';

    let str = String(text);

    str = str.replace(/font-size/gi, 'font-size-clean')
        .replace(/font-family/gi, 'font-family-clean');

    // Replace opening and closing h1–h6 with span
    str = str.replace(/<h[1-6]\b([^>]*)>/gi, '<span$1>')
        .replace(/<\/h[1-6]>/gi, '</span>');

    // If you still want to strip all tags at the end, keep this line:
    str = str.replace(/<[^>]*>/g, '');

    return str;
}


function format_Timestamp(timestamp) {
    // Extract year, month, day, hour, minute, second
    var year = parseInt(timestamp.substring(0, 4), 10);
    var month = parseInt(timestamp.substring(4, 6), 10) - 1; // Months are 0-based
    var day = parseInt(timestamp.substring(6, 8), 10);
    var hour = parseInt(timestamp.substring(8, 10), 10);
    var minute = parseInt(timestamp.substring(10, 12), 10);
    var second = parseInt(timestamp.substring(12, 14), 10);

    // Create a date object
    var date = new Date(year, month, day, hour, minute, second);

    // Format the date to "MMM d, yyyy"
    var options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);

}

function taoh_title_desc_decode(text){
    if (!text || text.length < 1) return text;

    let newstring = text.replace(/'/, '');
    //return displayTaohFormatted(cleanFontstyles(htmlDecode(customUrlDecode(escape(newstring)))));
    return displayTaohFormatted(cleanFontstyles(htmlDecode(customUrlDecode(newstring))));
}

function taoh_desc_decode(text){
    if (!text || text.length < 1) return text;

    let strr = cleanFontstyles(text);
    return htmlDecode(customUrlDecode(strr));
}

function taoh_desc_decode_new(text) {
    if (!text || text.length < 1) return text;

    let strr = cleanFontstyles(text);
    return htmlDecode(customUrlDecode(strr)); // temp revert back

    // let decoded = decodeURIComponent(text.replace(/\+/g, " "));
    // let strr = cleanFontstyles(decoded);
    // return htmlDecode(customUrlDecode(strr));
}

function taoh_text_decode(encoded) {
    const urlDecoded = decodeURIComponent(encoded);

    const txt = document.createElement("textarea");
    txt.innerHTML = urlDecoded;
    return txt.value;
}

async function generateSecureSlug(slugString, short = 0, algorithm = 'SHA-256') {
    const encoder = new TextEncoder();
    const encoded = encoder.encode(slugString);
    const hashBuffer = await crypto.subtle.digest(algorithm, encoded);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');

    if ([16, 32].includes(short)) {
        return hashHex.substring(0, short);
    }

    return hashHex;
}


function taoh_dojo_suggestion_toast(message, time = 5000, type = 'success', position = 'toast-middle-right', buttons = []) {
    //alert(message);
  //  const icon = document.querySelector(".animated-menu .menu-button");
    const popup = document.getElementById("dojo-popup");
   // icon.classList.add("head-shake");
    $('.dojo-sugg-msg').html(message);

      // Show the popup
      $('#dojo-popup').show();
      $('#dojo-popup').removeClass('dojo-slide-out');
      $('#dojo-popup').removeClass('d-none');
      $('#dojo-popup').addClass('dojo-slide-in');
      $('#dojo-popup').addClass(type);
     /*  popup.style.display = "block";
      popup.classList.remove("dojo-slide-out");
      popup.classList.add("dojo-slide-in"); */

      // Hide after 3 seconds
      setTimeout(() => {
      
        $('#dojo-popup').removeClass('dojo-slide-in')
        $('#dojo-popup').addClass('dojo-slide-out')
        // $('#dojo-popup').addClass('d-none');
        $('#dojo-popup').removeClass('error', 'success', 'warning', 'info');
        // icon.classList.remove("head-shake");

        setTimeout(() => {
            $('#dojo-popup').hide();          
        }, 600); // match the slideOut duration
      }, 3000);

    let typeClass;
    let iconSvg = '';
    if (type === 'error') {
        typeClass = 'toasterror_class';
        iconSvg = `<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#D13D13"/>
                        <path d="M23.5265 10.7325C24.1512 10.1078 24.1512 9.09324 23.5265 8.46853C22.9018 7.84382 21.8872 7.84382 21.2625 8.46853L16 13.7361L10.7325 8.47353C10.1078 7.84882 9.09324 7.84882 8.46853 8.47353C7.84382 9.09824 7.84382 10.1128 8.46853 10.7375L13.7361 16L8.47353 21.2675C7.84882 21.8922 7.84882 22.9068 8.47353 23.5315C9.09824 24.1562 10.1128 24.1562 10.7375 23.5315L16 18.2639L21.2675 23.5265C21.8922 24.1512 22.9068 24.1512 23.5315 23.5265C24.1562 22.9018 24.1562 21.8872 23.5315 21.2625L18.2639 16L23.5265 10.7325Z" fill="white"/>
                    </svg>`;
    } else if (type === 'info') {
        typeClass = 'info_class';
        
    } else if (type === 'warning') {
        typeClass = 'warning_class';
       
    } else if (type === 'success') {
        typeClass = 'success_class';
        
    } else {
        typeClass = 'success_class';
    }

    if (position) {
        $('#toast').removeClass('toast-top-right toast-middle-right toast-bottom-right toast-middle');
        $('#toast').addClass(position);

        if (position === 'toast-middle') {
            $('#toast_container').addClass('toast-middle-con');
        } else {
            $('#toast_container').removeClass('toast-middle-con');
        }
    }
   
}
  