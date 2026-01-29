const current_time = Date.now(); // in milliseconds
/* let last_message_time = 0; // 20 minutes ago
//let live_users_count = 10;
let one_on_one_started = false;
let group_one_started = false;
let other_profiles_available = 6;
let user_type = "Employer";
let job_posted = true;
let job_posted_date = Date.now() - (10 * 24 * 60 * 60 * 1000); // 10 days ago
let job_shared = false; */
async function getDynamicContexts(_ntw_ft_room_key) {
    return new Promise((resolve, reject) => {
        console.log(objStores.ntw_store.name +'---ntw_dojo_msg_key-----'+ntw_dojo_msg_key)
        IntaoDB.getItem(objStores.ntw_store.name, ntw_dojo_msg_key).then((intao_data) => {
            frm_last_msg_sent_time = 0;
            let last_message_time = 0;
            let one_on_one_started = false;
            let group_one_started = false;
            let job_posted = false;
            let job_shared = false;

            console.log('-------------inta data-');
            console.log(intao_data);
            if (intao_data !== undefined) {
                let cur_time = Date.now();

                if (intao_data.values?.frm_last_msg_sent_time != undefined) {
                    last_message_time = intao_data.values.frm_last_msg_sent_time;
                    group_one_started = true;
                }

                if (intao_data.values?.cm_last_msg_sent_time != undefined) {
                    last_message_time = intao_data.values.cm_last_msg_sent_time;
                    one_on_one_started = true;
                }

                if (_taoh_last_job_post_date != '') {
                    job_posted = true;
                }

                if (jobUrlData[_ntw_ft_room_key]) {
                    job_shared = true;
                }

                let dynamicContexts = [
                    {
                        last_message_time: last_message_time,
                        //live_users_count: _taoh_live_user_count
                    },
                    {
                        one_on_one_started: one_on_one_started,
                        other_profiles_available: 6,
                        live_users_count: _taoh_live_user_count
                    },
                    {
                        user_type: my_profileType,
                        job_posted: job_posted,
                        live_users_count: _taoh_live_user_count
                    },
                    {
                        user_type: my_profileType,
                        job_posted_date: _taoh_last_job_post_date,
                        job_shared: job_shared,
                        live_users_count: _taoh_live_user_count
                    }
                ];
                console.log('-------resolve--------');
                console.log(dynamicContexts);
                resolve(dynamicContexts); // 🔑 now we resolve it
            } else {
                resolve([]); // or reject, based on your logic
            }
        }).catch(reject);
    });
}
async function taoh_load_dojo_suggestion(_ntw_ft_room_key){


    console.log(objStores.ntw_store);
    ntw_dojo_msg_key = 'dojo_data_'+_ntw_ft_room_key;

    let contst =  await getDynamicContexts(_ntw_ft_room_key);

    let output = [];
    if(contst != undefined){
        for (let i = 0; i < dojorules.length; i++) {
            const scenario = dojorules[i];
            const name = scenario.name;
            const context = contst[i];
            const checks = scenario.expectations;

            let allMatch = false;
            /* console.log('====context====>');
            console.log(context); */
            for (const [key, expected] of Object.entries(checks)) {
                if(context != undefined && context != 'undefined'){
                    const result = evaluateCondition(key, expected, context);
                    console.log(key+'result===>'+result);
                    if (!result) {
                        console.log('----casematch---'+result);
                        allMatch = true;
                        break; // ❌ One failed, skip this scenario
                    }
                }else{
                    allMatch = true;
                    break;
                }
            }
            console.log(name+'------allMatch-------'+allMatch);
            if (allMatch) {
                const msg = `💡 ${name}`;
                taoh_dojo_suggestion_toast(msg, false, 'toast-bottom-right', []);
                break; // ✅ Show only one message and stop here
            }
        }
    }
}
function checkCondition(key, expected) {
    switch (key) {
        case 'message_posted':
            //console.log(((current_time - last_message_time) <= 15 * 60 * 1000)+'-------'+expected);
            var result_time = (current_time - last_message_time) <= 15 * 60 * 1000 === expected;
            console.log('====resulttime======>'+result_time);
            return result_time;
        case 'live_users_gte':
            return _taoh_live_user_count >= expected;
        case 'one_on_one_started':
            return one_on_one_started === expected;
        case 'other_profiles_gt':
            return other_profiles_available > expected;
        case 'user_type':
            return user_type === expected;
        case 'job_posted':
            return job_posted === expected;
        case 'job_posted_within_days':
            const days = (current_time - job_posted_date) / (1000 * 60 * 60 * 24);
            return days <= expected;
        case 'job_shared':
            return job_shared === expected;
        default:
            return false;
    }
}

function evaluateCondition(key, expected, context) {
    const current_time = Date.now();

    switch (key) {
    case 'message_posted_within_15min':
        //console.log(((current_time - context.last_message_time) <= 15 * 60 * 1000))
        return ((current_time - context.last_message_time) <= 15 * 60 * 1000) === expected;

    case 'live_users_gte':
        return context.live_users_count >= expected;

    case 'one_on_one_started':
        return context.one_on_one_started === expected;

    case 'other_profiles_gt':
        return context.other_profiles_available > expected;

    case 'user_type':
        return context.user_type === expected;

    case 'job_posted':
        return context.job_posted === expected;

    case 'job_posted_within_days':
        const days = (current_time - context.job_posted_date) / (1000 * 60 * 60 * 24);
        return days <= expected;

    case 'job_shared':
        return context.job_shared === expected;

    default:
        return false;
    }
}

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
            let res = JSON.parse(response);
            resolve(res);
        });
    });
}
