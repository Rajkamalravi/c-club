<?php

taoh_get_header();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '-1');
error_reporting(E_ALL);

$parse_url_1 = taoh_parse_url(1);

$user_info_obj = taoh_user_all_info();
$ptoken = $user_info_obj->ptoken;

$ntw_view = 1;
$timezone = $user_info_obj->local_timezone;

if ($parse_url_1 == 'room' || $parse_url_1 == 'forum' || $parse_url_1 == 'live') {
    $contslug = taoh_parse_url(2);

    if (empty($contslug)) {
//        taoh_redirect(TAOH_SITE_URL_ROOT);
        showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1002, 'networking');
        taoh_exit();
    }

    $contslug_arr = explode('-', $contslug);
    $keytoken = array_pop($contslug_arr);


} else if ($parse_url_1 == 'custom-room') {
    $custom_room_key = $keytoken = taoh_parse_url(3);
    if (empty($custom_room_key)) {
        taoh_redirect(TAOH_SITE_URL_ROOT);
        taoh_exit();
    }
}
else if ($parse_url_1 == 'dm') {
    $custom_room_key = $keytoken = taoh_parse_url(2);
    //echo "==custom_room_key====".$custom_room_key;die();
    if (empty($custom_room_key)) {
        taoh_redirect(TAOH_SITE_URL_ROOT);
        taoh_exit();
    }
    $ntw_view = 3;
} else {
    $contslug = ''; // :rk temp fix
    $geo_enable = 1;

    $date = new DateTime('now', new DateTimeZone($timezone));
    $abbreviation = $date->format('T');


    $abbreviation = strtoupper($abbreviation);
    $this_week = date('W');
    //echo "======".$this_week;die();


    if ($geo_enable) {
        $full_loc_expl = explode(', ', $user_info_obj->full_location);
        $country = array_pop($full_loc_expl);

        $keytoken = hash('crc32', TAOH_SITE_ROOT_HASH . $country.$this_week);
    } else {
        $keytoken = hash('crc32', TAOH_SITE_ROOT_HASH . $this_week);
    }
}

//echo $keytoken;

?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<style>
    .sidebar { background:#2c2f33; color:white; padding:10px; height: 750px; }
    .channel { padding:5px; cursor:pointer; }
    .channel.active { background:#5ea4d8; }
    .main { flex:1; display:flex; flex-direction:column; height: 750px; }
    .chat-header { background:#7289da; padding:10px; color:white; font-weight:bold; }
    .messages { flex:1; padding:10px; overflow-y:auto; background:#f4f4f4; height: 300px }
    .message { padding:5px; background:white; margin-bottom:5px; border-radius:4px; }
    .chat-input { display:flex; padding:10px; background:#ddd; }
    .chat-input textarea { flex:1; resize:none; padding:5px; }
    button { margin:3px; font-size:12px; }
    .msg-actions button { margin: 2px; font-size: 12px; }
    .pinned { border:2px solid gold; }

    .reply-panel {
        position: fixed;
        right: -400px;
        top: 0;
        width: 400px;
        height: 100%;
        background: #fff;
        border-left: 1px solid #ddd;
        box-shadow: -2px 0 5px rgba(0,0,0,0.1);
        transition: right 0.3s ease;
        overflow-y: auto;
        z-index: 1050;
    }

    .reply-panel.open {
        right: 0;
    }

    .reply-header {
        padding: 10px;
        background: #f8f8f8;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

</style>
</head>
<body>

<table width="100%" height="100%"><tr><td valign="top" width="20%">

<!-- Sidebar -->
<div class="sidebar">
    <h2>Channels</h2>
    <button id="btnCreateChannel">+ Create Channel</button>
    <button id="refreshBtn">🔄 Refresh All</button>
    <hr>
    <div id="channelList"></div>
</div>

</td><td width="80%" valign="top">

<!-- Main Chat -->
<div class="main">
    <div class="chat-header" id="activeChannelName">Select a channel</div>
    <div class="messages" id="messages"></div>
    <div class="chat-input">
        <textarea id="messageText" placeholder="Type a message..."></textarea>
        <button id="sendMessageBtn">Send</button>
    </div>
</div>

</td></tr></table>

<div id="replyPanel" class="reply-panel">
    <div class="reply-header">
        <span id="replyToText">Replies</span>
        <button id="closeReplyPanel" class="btn btn-sm btn-danger">✖</button>
    </div>
    <div id="replyMessages"></div>
</div>

<!-- Create Channel Modal -->
<div class="modal fade" id="createChannelModal" tabindex="-1" aria-labelledby="createChannelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="createChannelLabel">Create New Channel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="createChannelForm">
          <input type="hidden" class="form-control" id="channelType">
          <div class="mb-3">
            <label for="channelName" class="form-label">Channel Name</label>
            <input type="text" class="form-control" id="channelName" required>
          </div>
          <div class="mb-3">
            <label for="channelDescription" class="form-label">Description</label>
            <textarea class="form-control" id="channelDescription"></textarea>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="saveChannelBtn" class="btn btn-primary">Create</button>
      </div>

    </div>
  </div>
</div>


<script>

const roomslug = "<?php echo $keytoken ?>";
let my_pToken = '<?php echo $ptoken ?? ''; ?>';

async function saveChannelToDB1(channel_id, messages, metadata) {
    const channelKey = `channel_${channel_id}`;
    let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);

    let updatedResponse = channelData?.values || {};
    // Ensure existing messages array
    updatedResponse.messages = updatedResponse.messages || [];
    // Append new messages, avoiding duplicates by message_id
    const existingIds = new Set(updatedResponse.messages.map(m => m.message_id));
    messages.forEach(msg => {
        if (!existingIds.has(msg.message_id)) {
            updatedResponse.messages.push(msg);
        }
    });
    // Always update metadata (latest info wins)
    updatedResponse.metadata = metadata;
    await IntaoDB.setItem(objStores.ntw_store.name, {
        taoh_ntw: channelKey,
        values: updatedResponse,
        timestamp: Date.now()
    });
}

async function saveChannelToDB(channel_id, messages, metadata) {
    const channelKey = `channel_${channel_id}`;
    let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);

    let updatedResponse = channelData?.values || {};
    // Ensure existing messages array
    updatedResponse.messages = updatedResponse.messages || [];

    // Index existing messages by message_id for quick lookup
    const existingMap = new Map(updatedResponse.messages.map(m => [m.message_id, m]));

    for (const msg of messages) {
        const existing = existingMap.get(msg.message_id);

        if (!existing) {
            // Message not in cache → push it
            updatedResponse.messages.push(msg);
        } else {
            // Compare updated_timestamp (or fallback to timestamp if missing)
            const existingUpdated = existing.updated_timestamp || existing.timestamp;
            const incomingUpdated = msg.updated_timestamp || msg.timestamp;

            if (incomingUpdated > existingUpdated) {
                // Replace with newer version
                const index = updatedResponse.messages.findIndex(m => m.message_id === msg.message_id);
                if (index !== -1) {
                    updatedResponse.messages[index] = msg;
                }
            }
        }
    }

    // Always update metadata (latest info wins)
    updatedResponse.metadata = metadata;

    await IntaoDB.setItem(objStores.ntw_store.name, {
        taoh_ntw: channelKey,
        values: updatedResponse,
        timestamp: Date.now()
    });
}

$(document).on("click", ".view-replies", function () {
    let parentId = $(this).data("id");
    // Load replies from DB or memory
    loadReplies(parentId);
});

$(document).on("click", "#closeReplyPanel", function () {
    $("#replyPanel").removeClass("open");
    $("#replyMessages").html("");
});

async function loadReplies(parentId) {
    $("#replyMessages").html("Loading...");

    // Get channel_id from global (or pass it)
    let channel_id = currentChannelId;
    let channelData = await IntaoDB.getItem(objStores.ntw_store.name, `channel_${channel_id}`);

    let replies = [];
    if (channelData?.values?.messages) {
        replies = channelData.values.messages.filter(m => m.parent_message_id == parentId);
    }

    let parentMsg = channelData.values.messages.find(m => m.message_id == parentId);
    $("#replyToText").text(`Replies to: ${parentMsg?.text || "Message"}`);

    let replyHtml = replies.map(r => `
        <div class="reply" data-id="${r.message_id}">
            <strong>${r.ptoken == my_pToken ? "Me" : r.ptoken}</strong>: ${r.text}
            <small>${new Date(r.timestamp * 1000).toLocaleTimeString()}</small>
        </div>
    `).join("");

    $("#replyMessages").html(replyHtml || "<p>No replies yet</p>");
    $("#replyPanel").addClass("open");
}


async function loadChannelFromDB(channel_id) {
    const channelKey = `channel_${channel_id}`;
    const channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);
    return channelData?.values || null;
}

async function saveChannelTimestamp(channel_id, timestamp) {
    const channelKey = `channel_${channel_id}`;
    // Fetch existing record
    let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);
    let updatedResponse = channelData?.values || {};
    // Update just the timestamp
    updatedResponse.timestamp = timestamp;
    // Save back
    await IntaoDB.setItem(objStores.ntw_store.name, {
        taoh_ntw: channelKey,
        values: updatedResponse,
        timestamp: Date.now()
    });
}

async function getChannelTimestamp(channel_id) {
    const channelKey = `channel_${channel_id}`;
    let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);
    return channelData?.values?.timestamp || 0;
}


// =======================================
// Adaptive Polling
// =======================================
let channelTimestamps = {};
let pollingInterval = 10000;
let idleTime = 0;

// Open modal on button click
$("#btnCreateChannel").on("click", function() {
    $('#channelType').val("public");
    const modal = new bootstrap.Modal(document.getElementById('createChannelModal'));
    modal.show();
});

$(document).on("click", ".btnDeleteMsg", function () {
    const messageId = $(this).data("id");
    const channelId = currentChannelId;

    if (!confirm("Are you sure you want to delete this message?")) return;

    $.ajax({
        url: _taoh_site_ajax_url,
        type: "post",
        data: {
            roomslug: roomslug,
            taoh_action: "taoh_ntw_delete_message",
            channel_id: channelId,
            message_id: messageId,
            key: my_pToken
        },
        dataType: "json",
        success: function (res) {
            if (res.success) {
                // Update UI immediately
                $(`.message[data-id='${messageId}']`).html(
                    `<em>This message was deleted</em>`
                );
            }
        },
        error: function (xhr, status, error) {
            console.error("Delete failed:", error);
        }
    });
});


// Save channel when user clicks Create
$("#saveChannelBtn").on("click", function() {
    const channelName = $("#channelName").val().trim();
    const channelDescription = $("#channelDescription").val().trim() || "";
    const channelType = $('#channelType').val();

    if (!channelName) {
        alert("Please enter a channel name.");
        return;
    }

    const channelData = JSON.stringify({
        name: channelName,
        description: channelDescription,
        type: channelType,
        ptoken: my_pToken
    });

    const channelId = Math.floor(100 + Math.random() * 900).toString() + Date.now().toString();

    $.ajax({
        url: _taoh_site_ajax_url,
        type: 'post',
        data: {
            'roomslug': roomslug,
            'taoh_action': 'taoh_ntw_create_channel',
            'channel_id': channelId,
            'channel_data': channelData,
            'key': my_pToken
        },
        dataType: 'json',
        success: function(res) {
            if (res) {
                console.log("Channel created:", res);
                fetchChannels(); // reload channel list

                const modalEl = document.getElementById('createChannelModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modalInstance.hide();


                $("#createChannelForm")[0].reset();     // reset form
            } else {
                alert("Error creating channel: " + (res.error || "Unknown error"));
            }
        },
        error: function(xhr, status, error) {
            console.error("Create channel failed:", error);
            alert("Failed to create channel.");
        }
    });
});


async function fetchTimestamps1() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                'roomslug': roomslug,
                'taoh_action': 'taoh_ntw_get_timestamps',
                'key': my_pToken,
            },
            dataType: 'json',
            success: async function (data) {
                let updatesFound = false;
                for (let channel in data) {
                    if (!channelTimestamps[channel] || channelTimestamps[channel] != data[channel]) {
                        updatesFound = true;
                        highlightChannel(channel);
                    }
                    channelTimestamps[channel] = data[channel];

                    await saveChannelTimestamp(channel, data[channel]);
                }

                if (updatesFound) {
                    idleTime = 0;
                    pollingInterval = 10000;
                } else {
                    idleTime += pollingInterval / 1000;
                    adjustPollingInterval();
                }

                resolve({
                    status: 200,
                    success: true
                });
            },
            error: function (xhr, status, error) {
                resolve({
                    status: 201,
                    success: false,
                    error: error
                });
            },
            complete: function () {
                setTimeout(fetchTimestamps, pollingInterval);
            }
        });
    });
}

async function fetchTimestamps() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                'roomslug': roomslug,
                'taoh_action': 'taoh_ntw_get_timestamps',
                'key': my_pToken,
            },
            dataType: 'json',
            success: async function (data) {
                let updatesFound = false;

                for (let channel in data) {
                    const newTimestamp = data[channel];
                    const oldTimestamp = channelTimestamps[channel] || 0;

                    // Only proceed if timestamp changed
                    if (!oldTimestamp || oldTimestamp != newTimestamp) {
                        updatesFound = true;
                        highlightChannel(channel);

                        console.log("old timestamp found", oldTimestamp+" ~ "+newTimestamp);

                        // Fetch fresh messages for this channel
                        try {
                            const resp = await $.ajax({
                                url: _taoh_site_ajax_url,
                                type: 'post',
                                data: {
                                    'roomslug': roomslug,
                                    'taoh_action': 'taoh_ntw_get_messages',
                                    'channel_id': channel,
                                    'last_message_id': 0,
                                    'last_timestamp': oldTimestamp || 0,
                                    'key': my_pToken
                                },
                                dataType: 'json'
                            });

                            if (Array.isArray(resp) && resp.length > 0) {
                                // Save messages in IntaoDB
                                await saveChannelToDB(channel, resp, {});
                                renderMessages(resp, "", 1);

                                // Update timestamp in IntaoDB
                                await saveChannelTimestamp(channel, newTimestamp);
                            }
                        } catch (err) {
                            console.error("Error fetching new messages for channel", channel, err);
                        }
                    }

                    // Update local memory always
                    channelTimestamps[channel] = newTimestamp;
                }

                if (updatesFound) {
                    idleTime = 0;
                    pollingInterval = 10000;
                } else {
                    idleTime += pollingInterval / 1000;
                    adjustPollingInterval();
                }

                resolve({
                    status: 200,
                    success: true
                });
            },
            error: function (xhr, status, error) {
                resolve({
                    status: 201,
                    success: false,
                    error: error
                });
            },
            complete: function () {
                setTimeout(fetchTimestamps, pollingInterval);
            }
        });
    });
}


function fetchChannels() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                'roomslug': roomslug,
                'taoh_action': 'taoh_ntw_get_channels',
                'key': my_pToken,
            },
            dataType: 'json',
            success: function (data) {
                console.log("ch_data", data);
                renderChannelList(data); // pass server response directly
                resolve({
                    status: 200,
                    success: true
                });
            },
            error: function (xhr, status, error) {
                resolve({
                    status: 201,
                    success: false,
                    error: error
                });
            },
            complete: function () {
                setTimeout(fetchTimestamps, pollingInterval);
            }
        });
    });
}

function join_channel(channel_id) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                'roomslug': roomslug,
                'taoh_action': 'taoh_ntw_join_channel',
                'channel_id': channel_id,
                'key': my_pToken,
            },
            dataType: 'json',
            success: function (data) {
                console.log("ch_data", data);
                resolve({
                    status: 200,
                    success: true
                });
            },
            error: function (xhr, status, error) {
                resolve({
                    status: 201,
                    success: false,
                    error: error
                });
            },
            complete: function () {
                setTimeout(fetchTimestamps, pollingInterval);
            }
        });
    });
}

function send_message(channel_id, message_text) {

    var taohAction = "taoh_ntw_send_message";
    if(window.replyToMessageId) {
        taohAction = "taoh_ntw_add_reply_message";
    }

    return new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                'roomslug': roomslug,
                'taoh_action': taohAction, // backend action for sending messages
                'channel_id': channel_id,
                'message': message_text,
                'parent_message_id': window.replyToMessageId || null,
                'key': my_pToken,
            },
            dataType: 'json',
            success: function (data) {
                console.log("message_sent", data);
                renderMessages(data.message, "", 1);
                resolve({
                    status: 200,
                    success: true,
                    response: data
                });
            },
            error: function (xhr, status, error) {
                resolve({
                    status: 201,
                    success: false,
                    error: error
                });
            },
            complete: function () {
                setTimeout(fetchTimestamps, pollingInterval);
            }
        });
    });
}


function renderChannelList(channelArray) {
    $("#channelList").empty();

    if (!Array.isArray(channelArray)) {
        console.error("Invalid channels data received:", channelArray);
        return;
    }

    channelArray.forEach(item => {
        let channelData;
        try {
            channelData = JSON.parse(item.data);
        } catch (e) {
            console.error("Invalid channel data JSON:", item.data);
            return; // skip this channel
        }

        const channelName = channelData.name || "Unnamed";
        const topic = channelData.topic || "";

        $("#channelList").append(`
            <div class="channel" data-id="${item.id}" data-topic="${topic}">
                # ${channelName}
                <button class="join-btn" data-channel="${item.id}">Join</button>
                <button class="leave-btn" data-channel="${item.id}">Leave</button>
                <button class="pin-btn" data-channel="${item.id}" data-pinned="0">📌</button>
            </div>
        `);
    });
}


function renderChannelList(channelArray) {
    $("#channelList").empty();

    if (!Array.isArray(channelArray)) {
        console.error("Invalid channels data received:", channelArray);
        return;
    }

    channelArray.forEach(item => {
        let channelData;
        try {
            channelData = JSON.parse(item.data);
        } catch (e) {
            console.error("Invalid channel data JSON:", item.data);
            return; // skip this channel
        }

        const channelName = channelData.name || "Unnamed";
        const topic = channelData.topic || "";

        $("#channelList").append(`
            <div class="channel" data-id="${item.id}" data-channel_name="${channelName}" data-topic="${topic}">
                # ${channelName}
                <button class="join-btn d-none" data-channel="${item.id}">Join</button>
                <button class="leave-btn d-none" data-channel="${item.id}">Leave</button>
                <button class="pin-btn" data-channel="${item.id}" data-pinned="0">📌</button>
            </div>
        `);
    });
}


function adjustPollingInterval() {
    if (idleTime >= 120 && idleTime < 420) pollingInterval = 30000;
    else if (idleTime >= 420) pollingInterval = 60000;
}

function highlightChannel(channel) {
    $(`.channel[data-id="${channel}"]`).css("background", "#444");
}

// =======================================
// Load Channel Messages with Cache
// =======================================
async function loadChannelMessages(channel_id, firstCall = 0) {

    const cachedData = await loadChannelFromDB(channel_id);
    if (firstCall === 1 && cachedData) {
        renderMessages(cachedData.messages, cachedData.metadata);
    }

    if(firstCall == 1) {
        lastTimestamp = 0;
    } else {
        lastTimestamp = await getChannelTimestamp(channel_id);
    }

    // Fetch fresh messages from backend
    return new Promise((resolve, reject) => {
        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                'roomslug': roomslug,
                'taoh_action': 'taoh_ntw_get_messages',
                'channel_id': channel_id,
                'last_message_id': 0,
                'last_timestamp': lastTimestamp,
                'key': my_pToken
            },
            dataType: 'json',
            success: async function (res) {
                console.log("GET messages", res);
                try {
                    if(Array.isArray(res) && res.length > 0) {
                        await saveChannelToDB(channel_id, res, "");
                        if (!cachedData) {
                            renderMessages(res, "", 0);
                        }
                    }
                    resolve({
                        status: 200,
                        success: true,
                        response: res
                    });
                } catch (err) {
                    console.error("Error getting messages:", err);
                    reject(err);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching messages:", error);
                reject({
                    status: 201,
                    success: false,
                    error: error
                });
            }
        });
    });
}


async function renderMessages(messages, metadata, append = 1) {

    if(append == 0) {
        $("#messages").html("");
    }

    console.log("renderMessages messages", messages);

    if (!Array.isArray(messages)) {
        messages = [messages];
    }

    for (const msg of messages) {

        let date = new Date(msg.timestamp * 1000);
        let timeString = date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        var chatInfo = await getUserInfo(msg.ptoken, 'public');
        var style = "";
        if(msg.ptoken == my_pToken) {
            style = "style='margin-left: 500px;'";
        }

        var chat_name = chatInfo.chat_name;
        chat_name = chat_name.charAt(0).toUpperCase() + chat_name.slice(1);
        if ($(`#messages .message[data-id="${msg.message_id}"]`).length === 0) {
            if (msg.deleted) {
                $("#messages").append(`
                    <div class="message deleted" data-id="${msg.message_id}">
                        <em>${msg.text}</em>
                        <small>${timeString}</small>
                    </div>
                `);
            } else {

                let replyCount = messages.filter(m => m.parent_message_id == msg.message_id).length;

                if(!msg.parent_message_id) {
                    $("#messages").append(`
                    <div class="message" data-id="${msg.message_id}" ${style}>
                        <strong>${chat_name}</strong>: ${msg.text}
                        <div class="dropdown msg-actions" style="display:inline-block;">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ⋮
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item btnReply" data-id="${msg.message_id}" href="#">↩️ Reply</a></li>
                            <li><a class="dropdown-item btnPinMsg" data-id="${msg.message_id}" href="#">📌 Pin</a></li>
                            <li><a class="dropdown-item btnDeleteMsg" data-id="${msg.message_id}" href="#">🗑 Delete</a></li>
                        </ul>
                    </div>
                    <div class="msg-react">
                        <button class="btnReact btn btn-sm btn-outline-secondary" data-id="${msg.message_id}">😊</button>
                    </div>
                    ${replyCount > 0 ? `<div class="view-replies" style="text-decoration: underline; color: blue;" data-id="${msg.message_id}">${replyCount} ${replyCount > 1 ? "replies" : "reply"}</div>` : ""}
                    <small>${timeString}</small>
                    </div>`);
                }


            }
        } else {
            if (msg.deleted && $(`#messages .message[data-id="${msg.message_id}"]`).length) {
                $(`#messages .message[data-id="${msg.message_id}"]`).html(`<em>${msg.text}</em><small>${timeString}</small>`);
            }
        }
    }
    const messagesContainer = document.getElementById("messages");
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// =======================================
// Force Refresh All Channels
// =======================================
const channelsList = ['nhwAyPq2DY', 'VLEAjnBl69', 'bABD1ClrgI'];
async function forceRefreshAllChannels() {
    await Promise.all(
        channelsList.map(async (channel) => {
            try {
                const res = await fetch(`get_messages.php?channel=${encodeURIComponent(channel)}&lastMessageId=0`);
                const data = await res.json();
                await saveChannelToDB(channel, data.messages, data.metadata);
                renderMessagesIfActive(channel, data.messages, data.metadata);
            } catch (err) {
                console.error(`Error refreshing channel "${channel}":`, err);
            }
        })
    );
}
function renderMessagesIfActive(channel, messages, metadata) {
    if (currentChannelId === channel) renderMessages(messages, metadata, 0);
}

// =======================================
// Channel Join/Leave/Pin/Unpin
// =======================================

function leave_channel(channel) {
    fetch('leave_channel.php', { method: 'POST', body: new URLSearchParams({ channel }) });
}
function pin_channel(channel, pin) {
    fetch('pin_channel.php', { method: 'POST', body: new URLSearchParams({ channel, pin }) });
}

// =======================================
// UI + Events
// =======================================
let currentChannelId = null;

$(document).on("click", ".btnReply", function (e) {
    e.preventDefault();
    let parentId = $(this).attr("data-id");

    // Store the parent message ID globally
    window.replyToMessageId = parentId;

    // Option 1: Show a small "replying to" label above input
    let parentText = $(`.message[data-id="${parentId}"] strong`).text() + ": " +
                     $(`.message[data-id="${parentId}"]`).contents().filter(function() {
                         return this.nodeType === 3;
                     }).text().trim();

    $("#replyingTo").remove(); // clear old
    $(".chat-input").before(`
        <div id="replyingTo" class="alert alert-info p-1 mb-1">
            Replying to: ${parentText}
            <button id="cancelReply" class="btn btn-sm btn-outline-danger">✖</button>
        </div>
    `);

    $(".chat-input").focus();
});

// Cancel reply
$(document).on("click", "#cancelReply", function () {
    window.replyToMessageId = null;
    $("#replyingTo").remove();
});


$(document).on("click", ".channel", function() {
    $(".channel").removeClass("active");
    $('#messages').html("");
    $(this).addClass("active");
    currentChannelId = $(this).attr("data-id");
    var channel_name = $(this).attr('data-channel_name')
    $("#activeChannelName").text("# " + channel_name);
    $("#activeChannelName").attr("data-channel_id", currentChannelId);
    loadChannelMessages(currentChannelId, 1);
});

$("#sendMessageBtn").on("click", function() {
    let text = $("#messageText").val().trim();
    if (!text || !currentChannelId) return;
    //console.log("Send message:", text);
    $("#messageText").val("");

    let channelId = $("#activeChannelName").attr("data-channel_id");

    send_message(channelId, text);
});

$(document).on("click", ".join-btn", function(e) {
    e.stopPropagation();
    join_channel($(this).data("channel"));
});
$(document).on("click", ".leave-btn", function(e) {
    e.stopPropagation();
    leave_channel($(this).data("channel"));
});
$(document).on("click", ".pin-btn", function(e) {
    e.stopPropagation();
    let btn = $(this);
    let pinned = btn.data("pinned") === 1 ? 0 : 1;
    btn.data("pinned", pinned);
    pin_channel(btn.data("channel"), pinned);
});

$("#refreshBtn").on("click", forceRefreshAllChannels);

// =======================================
// Init
// =======================================
$(document).ready(function() {
    fetchChannels();
    fetchTimestamps();

    // setInterval(() => {
    //     let channelId = $("#activeChannelName").attr("data-channel_id");
    //     if(channelId) {
    //         loadChannelMessages(channelId);
    //     }
    // }, 10000);

});

async function getUserInfo(pToken_to, ops = 'public', serverFetch = false) {
    if (!pToken_to?.trim()) return null;

    let userInfo = {};

    if (!serverFetch) {
        // Try to get userInfo from IndexedDB
        if (!userInfo.ptoken) {
            const user_info_key = 'user_info_list';
            const intao_data = await IntaoDB.getItem(objStores.common_store.name, user_info_key);
            if (intao_data?.values && intao_data.values[ops] && intao_data.values[ops][pToken_to]) {
                let userInfoObj = intao_data.values[ops][pToken_to];
                // Check if data is expired (expires after 2 day)
                if (userInfoObj.last_fetch_time && (Date.now() - userInfoObj.last_fetch_time) <= 172800000) {
                    userInfo = userInfoObj;
                    $("#user_profile_type").val(userInfo.type);
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
    }

    return userInfo;
}
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
</script>

</body>
</html>
