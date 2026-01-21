

async function getDynamicEvents() {
    return new Promise((resolve, reject) => {        

                let cur_time = Date.now();               
                let dynamicContexts = [
                    {
                        user_logged_in: isLoggedIn,
                        //live_users_count: _taoh_live_user_count
                    },
                    {
                        user_logged_in: isLoggedIn,
                        profile_complete : isValidUser
                    },
                    {
                        user_logged_in: isLoggedIn,
                        is_rsvp : is_rsvp
                    },
                    {
                         user_logged_in: isLoggedIn,
                        is_rsvp : is_rsvp
                    }
                ];
                console.log('-------resolve--------');
                console.log(dynamicContexts);
                resolve(dynamicContexts); // 🔑 now we resolve it
            } 
        );
    
}
       
async function getDynamicLobby() {
    return new Promise((resolve, reject) => {        

                let cur_time = Date.now();               
                let dynamicContexts = [
                    {
                        user_logged_in: isLoggedIn,
                        //live_users_count: _taoh_live_user_count
                    },
                    {
                        user_logged_in: isLoggedIn,
                        profile_complete : isValidUser
                    },
                    {
                        user_logged_in: isLoggedIn,
                        is_rsvp : 0,
                        is_event_live : 1
                    },
                    {
                        user_logged_in: isLoggedIn,
                        is_rsvp : 0,
                        is_event_live : 1,
                        is_sponsor_enabled : 1
                    },
                    {
                        user_logged_in: isLoggedIn,
                        is_rsvp : 0,
                        is_event_live : 1,
                        is_exhibitor_enabled : 1
                    },
                    {
                        user_logged_in: isLoggedIn,
                        is_rsvp : 0,
                        is_event_live : 1,
                        is_speaker_enabled : 1
                    }
                ];
                console.log('-------resolve--------');
                console.log(dynamicContexts);
                resolve(dynamicContexts); // 🔑 now we resolve it
            } 
        );
    
}
     

function evaluateEventCondition(key, expected, context) {
    const current_time = Date.now();

    switch (key) {
   
    case 'user_logged_in':
        return context.user_logged_in === expected;

    case 'profile_complete':
        return context.profile_complete === expected;

    case 'is_rsvp':
        return context.is_rsvp === expected;
    
    case 'is_event_live':
         return context.is_event_live === expected;
    
    case 'is_sponsor_enabled':
         return context.is_sponsor_enabled === expected;

    case 'is_exhibitor_enabled':
         return context.is_exhibitor_enabled === expected;

    case 'is_speaker_enabled':
         return context.is_speaker_enabled === expected;
    
    default:
        return false;
    }
}

let dojoContextCache = [];
let currentScenarioIndex = 0;

// Trigger full context reload every 5 mins
function refreshDojoEventContexts() {
    console.log("Refreshing dynamic contexts...");
    getDynamicEvents().then((contexts) => {
        console.log(contexts);
        dojoContextCache = contexts;
        console.log("Updated context cache:", contexts);
    });
}


// Trigger single scenario check every 1 minute
function checkNextDojoEventScenario() {
    if (!document.hidden && dojoContextCache.length > 0) {
        const scenario = dojoeventrules[currentScenarioIndex];
        const context = dojoContextCache[currentScenarioIndex];
        const name = scenario.name;
        const checks = scenario.expectations;

        let allMatch = false;
        console.log(context)
        for (const [key, expected] of Object.entries(checks)) {
            if (context && typeof context !== 'undefined') {
                console.log('key----->'+key+'<---expected------>'+expected)
                const result = evaluateEventCondition(key, expected, context);
                console.log('result----->'+result);
                if (result) {
                    console.log('result---key-->'+key);
                    allMatch = true;
                    break;
                }
            } else {
                allMatch = true;
                break;
            }
        }
        console.log(name+'-----'+allMatch);
        if (allMatch) {
            const msg = `💡 ${name}`;
            taoh_dojo_suggestion_toast(msg, false, 'toast-bottom-right', []);
        }

        // Rotate to next scenario
        currentScenarioIndex = (currentScenarioIndex + 1) % dojoeventrules.length;
    }
}


// Trigger full context reload every 5 mins
function refreshDojoLobbyContexts() {
    console.log("Refreshing dynamic contexts...");
    getDynamicLobby().then((contexts) => {
        console.log(contexts);
        dojoContextCache = contexts;
        console.log("Updated context cache:", contexts);
    });
}