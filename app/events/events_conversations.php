<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/conversations_style.css?v=<?php echo TAOH_CSS_JS_VERSION;?>">
<?php $groupchat_base = rtrim(TAOH_FIREBASE_SCRIPT_URL, '/'); $groupchat_base = str_replace('/api', '', $groupchat_base); ?>
<link rel="stylesheet" href="<?php echo $groupchat_base; ?>/css/style.css">
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"></script>
<script src="<?php echo $groupchat_base; ?>/js/app.js"></script>
<script src="<?php echo $groupchat_base; ?>/js/ui.js"></script>

<div class="conversations-container">
    <!-- Suggested Action Card -->
    <div class="suggested-action-card" id="suggested-action" role="region" aria-label="Suggested activity">
        <div class="suggested-action-content">
            <span class="suggested-action-icon" aria-hidden="true"></span>
            <div class="suggested-action-text">
                <span class="suggested-action-label">Suggested for you</span>
                <span class="suggested-action-message" id="suggested-action-message">Loading suggestions...</span>
                <span class="suggested-action-detail" id="suggested-action-detail"></span>
            </div>
        </div>
        <div class="suggested-action-actions">
            <button class="conv-btn conv-btn-primary conv-btn-sm" id="suggested-action-cta">Join</button>
            <button class="conv-btn conv-btn-ghost conv-btn-sm suggested-action-dismiss" id="suggested-action-dismiss" aria-label="Dismiss suggestion">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <!-- Sub Tabs -->
    <div class="conversations-tabs">
        <button class="conversations-tab active" data-conv-tab="groups">Group Chats</button>
        <button class="conversations-tab" data-conv-tab="direct">Direct Messages</button>
        <button class="conversations-tab" data-conv-tab="speed">Speed Networking</button>
    </div>

    <!-- Group Chats Panel -->
    <div class="conversations-panel active" id="conv-groups">
        <!-- Create Channel Button -->
        <div style="display:flex;justify-content:flex-end;margin-bottom:8px;">
            <?php if (!empty($ptoken)) { ?>
                <button class="conv-btn conv-btn-primary conv-btn-sm" id="btn-create-channel" style="font-size:13px;">+ Create Channel</button>
            <?php } else { ?>
                <button class="conv-btn conv-btn-primary conv-btn-sm" id="btn-create-channel-login" style="font-size:13px;" data-toggle="modal" data-target="#config-modal">Login to Create Channel</button>
            <?php } ?>
        </div>
        <!-- All sections will be dynamically rendered here -->
        <div id="groups-sections-container">
            <!-- Loading skeleton -->
            <div class="conversation-list-loading" id="groups-loading">
                <div class="conversation-skeleton">
                    <div class="skeleton-icon"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                    </div>
                </div>
                <div class="conversation-skeleton">
                    <div class="skeleton-icon"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                    </div>
                </div>
                <div class="conversation-skeleton">
                    <div class="skeleton-icon"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wehbae / Direct Messages Panel -->
    <div class="conversations-panel" id="conv-direct">
        <div class="conversations-section">
            <h3 class="conversations-section-title">People in this Room</h3>
            <div class="conversation-list" id="dm-list">
                <!-- Loading skeleton -->
                <div class="conversation-list-loading">
                    <div class="conversation-skeleton">
                        <div class="skeleton-icon" style="border-radius: 50%;"></div>
                        <div class="skeleton-content">
                            <div class="skeleton-line"></div>
                            <div class="skeleton-line"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="conv-empty-state" id="dm-empty" style="display: none;">
            <p class="conv-text-secondary">No users found in this room yet.</p>
        </div>
    </div>

    <!-- Speed Networking Panel -->
    <div class="conversations-panel" id="conv-speed">
        <div class="speed-networking" id="speed-networking-container">
            <div class="speed-networking-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
            </div>
            <h3>Speed Networking</h3>
            <p>Meet someone new in a 5-minute video chat.<br>You can leave anytime. No pressure.</p>

            <button class="conv-btn-accent" id="btn-join-queue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span id="queue-btn-text">Join Queue</span>
            </button>

            <div class="speed-networking-stats">
                <span id="queue-count">-- people waiting</span>
                <span id="avg-wait">Avg wait: --</span>
            </div>
        </div>
    </div>
</div>

<!-- Chat Coming Soon Modal -->
<div class="conv-chat-modal-overlay" id="chat-coming-soon-modal">
    <div class="conv-chat-modal">
        <div class="conv-chat-modal-header">
            <div class="conv-chat-modal-header-info">
                <h3 class="conv-chat-modal-title" id="chat-modal-booth-name">Booth Chat</h3>
                <div class="conv-chat-modal-subtitle" id="chat-modal-subtitle" style="display:none;">
                    <span class="conv-chat-modal-timeslot" id="chat-modal-timeslot"></span>
                    <span class="conv-chat-modal-live-status" id="chat-modal-live-status"></span>
                </div>
            </div>
            <button class="conv-chat-modal-close" id="chat-modal-close" aria-label="Close modal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="conv-chat-modal-body" style="padding:0;display:flex;flex-direction:column;flex:1;overflow:hidden;">
            <div id="tao-groupchat" style="flex:1;overflow:hidden;"></div>
        </div>
    </div>
</div>

<!-- Create Channel Modal -->
<div class="conv-chat-modal-overlay" id="create-channel-modal">
    <div class="conv-chat-modal" style="min-width:360px;min-height:380px;max-width:480px;width:90%;">
        <div class="conv-chat-modal-header">
            <div class="conv-chat-modal-header-info">
                <h3 class="conv-chat-modal-title">Create Channel</h3>
            </div>
            <button class="conv-chat-modal-close" id="create-channel-modal-close" aria-label="Close modal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="conv-chat-modal-body" style="text-align:left;">
            <form id="create-channel-form">
                <div style="margin-bottom:12px;">
                    <label style="font-weight:600;font-size:13px;">Channel Name</label>
                    <input type="text" id="channel-name" name="channel_name" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;" placeholder="e.g. AI Discussion">
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-weight:600;font-size:13px;">Description</label>
                    <textarea id="channel-description" name="description" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;min-height:60px;" placeholder="What's this channel about?"></textarea>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-weight:600;font-size:13px;">Access Type</label>
                    <select id="channel-access-type" name="access_type" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </select>
                </div>
                <button type="submit" class="conv-btn conv-btn-primary" style="width:100%;padding:10px;">Create Channel</button>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    // Event token from PHP
    var eventtoken = '<?php echo $eventtoken ?? ''; ?>';
    var cdn_prefix = '<?php echo TAOH_CDN_PREFIX; ?>';
    var creatorPtoken = '<?php echo $ptoken ?? ''; ?>';
    var isLoggedIn = <?php echo !empty($ptoken) ? 'true' : 'false'; ?>;
    var userProfileInfo = <?php
        $uinfo = taoh_user_all_info();
        echo json_encode($uinfo);
    ?>;
    var GROUPCHAT_API_BASE = '<?php echo rtrim(TAOH_FIREBASE_SCRIPT_URL, "/"); ?>';

    // Dojo notification tracking for live sessions
    var dojoSessionScheduled = new Set();
    var dojoSessionSent = [];
    var dojoSessionTiming = 0;

    // State
    var state = {
        currentTab: 'groups',
        groupChats: [],
        sessions: [],
        directMessages: [],
        speedNetworking: {
            inQueue: false,
            queuePosition: null,
            queueCount: 0,
            avgWaitTime: 0
        },
        suggestedActionDismissed: false,
        currentRoomSlug: null,
        defaultChannels: [],
        userChannels: []
    };

    // Icons for group chats
    var chatIcons = {
        'message-circle': '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>',
        'code': '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>',
        'briefcase': '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>',
        'users': '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'info': '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4M12 8h.01"></path></svg>'
    };

    // Color palette for icons
    var iconColors = ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14'];

    // Initialize
    $(document).ready(function() {
        initTabs();
        initSuggestedAction();
        initSpeedNetworking();
        initChatModal();
    });

    // Tab Switching
    function initTabs() {
        $('.conversations-tab').on('click', function() {
            var tab = $(this).data('conv-tab');
            switchConversationTab(tab);
        });

        // Meet People button
        $('#btn-meet-people').on('click', function() {
            // Switch to attendees tab in parent if available
            if (typeof window.switchToTab === 'function') {
                window.switchToTab('attendees');
            }
        });
    }

    function switchConversationTab(tab) {
        state.currentTab = tab;

        // Update tab buttons
        $('.conversations-tab').removeClass('active');
        $('.conversations-tab[data-conv-tab="' + tab + '"]').addClass('active');

        // Update panels
        $('.conversations-panel').removeClass('active');
        $('#conv-' + tab).addClass('active');

        // Load data for the tab
        if (tab === 'groups') {
            initConversationRoom();
        } else if (tab === 'direct') {
            loadDirectMessages();
        } else if (tab === 'speed') {
            loadSpeedNetworking();
        }
    }

    // Initialize conversation room via AJAX (called on tab click only)
    function initConversationRoom() {
        console.log('initConversationRoom called');
        var $container = $('#groups-sections-container');
        $('#groups-loading').show();

        var token = getEventToken();
        if (!token) {
            console.log('No eventtoken found');
            renderNoResults($container);
            return;
        }

        var ajaxUrl = typeof _taoh_site_ajax_url !== 'undefined' ? _taoh_site_ajax_url : '/club/app/events/ajax.php';
        var ajaxToken = typeof _taoh_ajax_token !== 'undefined' ? _taoh_ajax_token : '';

        $.ajax({
            url: ajaxUrl,
            method: 'POST',
            data: {
                action: 'get_event_conversation_room',
                taoh_action: 'get_event_conversation_room',
                token: ajaxToken,
                eventtoken: token
            },
            dataType: 'json',
            success: function(response) {
                console.log('get_event_conversation_room response:', response);
                if (response && response.success && response.roomslug) {
                    state.currentRoomSlug = response.roomslug;
                    checkOrCreateRoom(response.roomslug, response.room_info);
                } else {
                    console.error('Failed to get room slug:', response);
                    renderNoResults($container);
                }
            },
            error: function(xhr, status, err) {
                console.error('AJAX error:', status, err);
                renderNoResults($container);
            }
        });
    }

    // Check if room exists, create if not, then load channel stats
    function checkOrCreateRoom(roomSlug, roomInfo) {
        console.log('checkOrCreateRoom:', roomSlug);

        var urlParams = new URLSearchParams(window.location.search);
        var shouldDelete = urlParams.get('delete') === '1';

        var deletePromise = shouldDelete
            ? fetch(GROUPCHAT_API_BASE + '/rooms.php?room_slug=' + encodeURIComponent(roomSlug), {
                method: 'DELETE'
            }).then(function(res) {
                return res.json();
            }).then(function(data) {
                console.log('Room DELETE response:', data);
            }).catch(function(err) {
                console.warn('Error deleting room (may not exist):', err);
            })
            : Promise.resolve();

        deletePromise.then(function() {
            // After delete attempt (if any), check and create room
            fetch(GROUPCHAT_API_BASE + '/rooms.php?room_slug=' + encodeURIComponent(roomSlug))
        .then(function(res) {
            return res.json();
        }).then(function(data) {
            console.log('Room GET response:', data);
            if (data && data.room && data.room.room_slug) {
                console.log('Room exists, loading channel stats');
                loadChannelStats(roomSlug);
            } else {
                console.log('Room does not exist, creating...');
                createRoom(roomSlug, roomInfo);
            }
        }).catch(function(err) {
            console.error('Error checking room:', err);
            renderNoResults($('#groups-sections-container'));
        });
        }); // end deletePromise.then
    }

    // Create room via POST to rooms.php
    function createRoom(roomSlug, roomInfo) {
        var postData = {
            room_slug: roomSlug,
            eventtoken: eventtoken,
            profile_info: userProfileInfo
        };
        if (roomInfo) {
            postData.room_info = roomInfo;
        }
        console.log('Creating room with data:', postData);

        fetch(GROUPCHAT_API_BASE + '/rooms.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(postData)
        }).then(function(res) {
            return res.json();
        }).then(function(data) {
            console.log('Room created:', data);
            loadChannelStats(roomSlug);
        }).catch(function(err) {
            console.error('Error creating room:', err);
            renderNoResults($('#groups-sections-container'));
        });
    }

    // Load channel stats from channel-stats.php
    function loadChannelStats(roomSlug) {
        console.log('Loading channel stats for:', roomSlug);
        fetch(GROUPCHAT_API_BASE + '/channel-stats.php?room_slug=' + encodeURIComponent(roomSlug))
        .then(function(res) { return res.json(); })
        .then(function(data) {
            console.log('Channel stats response:', data);
            renderFromChannelStats(data);
        }).catch(function(err) {
            console.error('Error loading channel stats:', err);
            renderNoResults($('#groups-sections-container'));
        });
    }

    // Convert object to array (channel-stats returns objects keyed by channel_id)
    function toArray(obj) {
        if (Array.isArray(obj)) return obj;
        if (obj && typeof obj === 'object') return Object.values(obj);
        return [];
    }

    // Render from channel-stats.php response
    function renderFromChannelStats(data) {
        var grouped = data.grouped || {};
        var defaultChannels = toArray(grouped['default'] || grouped['1'] || {});
        var exhibitorChannels = toArray(grouped['exhibitor'] || grouped['2'] || {});
        var sessionChannels = toArray(grouped['session'] || grouped['7'] || {});
        var userCreatedChannels = toArray(grouped['user_created'] || {});

        console.log('Channels - default:', defaultChannels.length, 'exhibitor:', exhibitorChannels.length, 'session:', sessionChannels.length, 'user_created:', userCreatedChannels.length);

        // Map default channels to state
        state.defaultChannels = defaultChannels.map(function(ch) {
            return {
                id: ch.channel_id,
                name: ch.channel_name || 'General',
                icon: 'message-circle',
                logo: '',
                description: ch.description || '',
                activeMembers: ch.activeMembers || 0,
                unreadCount: ch.unreadCount || 0,
                isPinned: true,
                ptoken: '',
                sponsorType: '',
                groupType: 'general',
                members: ch.members || [],
                channelData: ch
            };
        });

        // Map exhibitor channels to state
        state.groupChats = exhibitorChannels.map(function(ch) {
            return {
                id: ch.channel_id,
                name: ch.channel_name || 'Exhibitor',
                icon: 'briefcase',
                logo: '',
                description: ch.description || '',
                activeMembers: ch.activeMembers || 0,
                unreadCount: ch.unreadCount || 0,
                isPinned: false,
                ptoken: '',
                sponsorType: '',
                members: ch.members || [],
                channelData: ch
            };
        });

        state.sessions = sessionChannels.map(function(ch) {
            return {
                id: ch.channel_id,
                name: ch.channel_name || 'Session',
                icon: 'users',
                logo: '',
                speakers: [],
                dateFrom: '',
                dateTo: '',
                timezone: '',
                state: '',
                isLive: false,
                activeMembers: ch.activeMembers || 0,
                unreadCount: ch.unreadCount || 0,
                isPinned: false,
                ptoken: '',
                members: ch.members || [],
                channelData: ch
            };
        });

        state.userChannels = userCreatedChannels.map(function(ch) {
            return {
                id: ch.channel_id,
                name: ch.channel_name || 'Channel',
                description: ch.description || '',
                access_type: ch.access_type || 'public',
                activeMembers: ch.activeMembers || 0,
                unreadCount: ch.unreadCount || 0,
                members: ch.members || [],
                channelData: ch
            };
        });

        renderAllGroupSections();
        updateSuggestedAction();
    }

    // Render no results
    function renderNoResults($container) {
        $('#groups-loading').hide();
        $container.html('<div class="conv-empty-state"><p class="conv-text-secondary">No groups available at this time.</p></div>');
    }

    // Render a single channel item (for user_created channels)
    function renderChannelItem(channel, index) {
        var isPrivate = channel.access_type === 'private';
        var iconHtml = isPrivate
            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>'
            : '<span style="font-size:20px;font-weight:700;">#</span>';

        var isMember = false;
        if (creatorPtoken && channel.members && Array.isArray(channel.members)) {
            isMember = channel.members.indexOf(creatorPtoken) !== -1;
        }

        // Check if current user is the channel creator (first member)
        var isOwner = false;
        if (creatorPtoken && channel.members && Array.isArray(channel.members) && channel.members[0] === creatorPtoken) {
            isOwner = true;
        }

        var html = '';
        html += '<div class="conversation-item" tabindex="0" role="button" data-channel-id="' + channel.id + '">';
        html += '  <div class="conversation-icon" style="background:#f0f0f0;display:flex;align-items:center;justify-content:center;">' + iconHtml + '</div>';
        html += '  <div class="conversation-info">';
        html += '    <h4 class="conversation-name">' + escapeHtml(channel.name) + '</h4>';
        html += '    <p class="conversation-preview">' + escapeHtml(channel.description || '') + '</p>';
        html += '  </div>';
        if (isOwner) {
            html += '  <div class="channel-owner-actions" style="display:flex;align-items:center;gap:6px;margin-right:8px;">';
            html += '    <button class="conv-btn conv-btn-sm btn-star-channel" data-channel-id="' + channel.id + '" title="Star" style="background:none;border:none;cursor:pointer;padding:4px;font-size:16px;color:#ccc;">&#9734;</button>';
            html += '    <button class="conv-btn conv-btn-sm btn-delete-channel" data-channel-id="' + channel.id + '" title="Delete" style="background:none;border:none;cursor:pointer;padding:4px;font-size:14px;color:#dc3545;">';
            html += '      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>';
            html += '    </button>';
            html += '  </div>';
        } else if (isMember && isLoggedIn) {
            html += '  <div class="conversation-join" style="display:flex;align-items:center;margin-right:8px;">';
            html += '    <button class="conv-btn conv-btn-sm btn-leave-channel" data-channel-id="' + channel.id + '" style="font-size:11px;color:#dc3545;border:1px solid #dc3545;background:none;">Leave</button>';
            html += '  </div>';
        } else if (!isMember && isLoggedIn) {
            html += '  <div class="conversation-join" style="display:flex;align-items:center;margin-right:8px;">';
            html += '    <button class="conv-btn conv-btn-primary conv-btn-sm btn-join-channel" data-channel-id="' + channel.id + '" style="font-size:11px;">Join</button>';
            html += '  </div>';
        } else if (!isMember && !isLoggedIn) {
            html += '  <div class="conversation-join" style="display:flex;align-items:center;margin-right:8px;">';
            html += '    <button class="conv-btn conv-btn-primary conv-btn-sm btn-join-channel-login" data-toggle="modal" data-target="#config-modal" style="font-size:11px;">Login to Join</button>';
            html += '  </div>';
        }
        html += '  <div class="conversation-meta">';
        html += '    <span class="conversation-active">' + (channel.activeMembers || 0) + ' active</span>';
        if (channel.unreadCount > 0) {
            html += '    <span class="conversation-unread">' + channel.unreadCount + '</span>';
        }
        html += '  </div>';
        html += '</div>';

        return html;
    }

    // Chat scripts loaded on page load, just call callback
    function loadChatScripts(callback) {
        callback();
    }

    // Initialize TaoGroupChat inside the modal
    async function initChat(channelId) {
        try {
            TaoGroupChatUI.init('tao-groupchat');
            await TaoGroupChat.init({
                apiBase: GROUPCHAT_API_BASE,
                room_slug: state.currentRoomSlug,
                channel_id: channelId,
                ptoken: creatorPtoken,
                debug: true
            });
            TaoGroupChat.setPresence(true);
        } catch (e) {
            console.error('Chat init failed:', e);
            document.getElementById('tao-groupchat').innerHTML =
                '<div style="padding:20px;color:red;">Error: ' + e.message + '</div>';
        }
    }

    // Check if current user is a member of a channel
    function isChannelMember(channel) {
        if (!creatorPtoken || !channel || !channel.members || !Array.isArray(channel.members)) return false;
        return channel.members.indexOf(creatorPtoken) !== -1;
    }

    // Find channel across all types
    function findChannelById(channelId) {
        return state.userChannels.find(function(c) { return c.id == channelId; })
            || state.groupChats.find(function(c) { return c.id == channelId; })
            || state.sessions.find(function(c) { return c.id == channelId; })
            || state.defaultChannels.find(function(c) { return c.id == channelId; });
    }

    // Show loading spinner in the groupchat container
    function showChatLoading() {
        document.getElementById('tao-groupchat').innerHTML =
            '<div style="display:flex;align-items:center;justify-content:center;height:100%;"><div style="width:2rem;height:2rem;border:3px solid #e0e0e0;border-top-color:#007bff;border-radius:50%;animation:spin 0.8s linear infinite;"></div></div>' +
            '<style>@keyframes spin{to{transform:rotate(360deg)}}</style>';
    }

    // Open a channel
    function openChannel(channelId) {
        console.log('Opening channel:', channelId);
        var ch = state.userChannels.find(function(c) { return c.id == channelId; });
        if (!isChannelMember(ch)) { console.log('Not a member, join first'); return; }

        $('#chat-modal-booth-name').text(ch.name || 'Channel');
        $('#chat-modal-subtitle').hide();
        showChatLoading();
        $('#chat-coming-soon-modal').addClass('active');
        loadChatScripts(function() { initChat(channelId); });
    }

    // Join a channel
    function joinChannel(channelId, $btn) {
        console.log('Joining channel:', channelId);
        console.log('Profile info from session:', userProfileInfo);
        // Search across all channel types
        var ch = state.userChannels.find(function(c) { return c.id == channelId; })
              || state.groupChats.find(function(c) { return c.id == channelId; })
              || state.sessions.find(function(c) { return c.id == channelId; })
              || state.defaultChannels.find(function(c) { return c.id == channelId; });
        if (!ch) { console.error('Channel not found:', channelId); return; }

        var postData = {
            channel_id: channelId,
            ptoken: creatorPtoken,
            profile_info: userProfileInfo,
            display_name: (userProfileInfo && userProfileInfo.chat_name) ? userProfileInfo.chat_name : ''
        };

        console.log('Join channel payload:', postData);

        fetch(GROUPCHAT_API_BASE + '/channel-stats.php?action=join', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(postData)
        }).then(function(res) { return res.json(); })
        .then(function(data) {
            console.log('Join channel response:', data);
            if (data.success) {
                // Reload channel stats
                loadChannelStats(state.currentRoomSlug);
            }
        }).catch(function(err) {
            console.error('Error joining channel:', err);
            if ($btn) $btn.prop('disabled', false).html('Join');
        });
    }

    // Notify for live sessions using dojo
    function notifyLiveSessions() {
        if (typeof taoh_show_queued_success !== 'function') {
            return; // Dojo notification function not available
        }

        state.sessions.forEach(function(session) {
            if (session.isLive) {
                var sessionId = session.id;

                // Check if already scheduled
                if (dojoSessionScheduled.has(sessionId)) {
                    return;
                }

                dojoSessionScheduled.add(sessionId);
                dojoSessionTiming += 5000;
                var delay = dojoSessionTiming;
                var sessionName = session.name;

                setTimeout(function() {
                    taoh_show_queued_success(sessionName + " is live now!");
                    dojoSessionSent.push(sessionId);
                }, delay);
            }
        });
    }

    // Arrow icon SVG
    // Arrow icon SVG - chevron down
    var arrowIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';

    // Render all group sections with collapsible headers
    function renderAllGroupSections() {
        var $container = $('#groups-sections-container');

        // Hide loading
        $('#groups-loading').hide();

        // Categorize groups
        var sponsors = [];
        var exhibitors = [];
        var general = [];
        var custom = [];

        state.groupChats.forEach(function(chat) {
            if (chat.groupType === 'general') {
                general.push(chat);
            } else if (chat.groupType === 'custom') {
                custom.push(chat);
            } else if (chat.sponsorType && chat.sponsorType.trim() !== '') {
                sponsors.push(chat);
            } else {
                exhibitors.push(chat);
            }
        });

        var html = '';

        // Render sections in order: Default, Sponsors, Exhibitors, Sessions, General, Custom, Channels
        if (state.defaultChannels.length > 0) {
            html += renderCollapsibleSection('default', 'Default', state.defaultChannels, 'group');
        }
        if (sponsors.length > 0) {
            html += renderCollapsibleSection('sponsors', 'Sponsors', sponsors, 'group');
        }
        if (exhibitors.length > 0) {
            html += renderCollapsibleSection('exhibitors', 'Exhibitors', exhibitors, 'group');
        }
        if (state.sessions.length > 0) {
            html += renderCollapsibleSection('sessions', 'Sessions', state.sessions, 'session');
        }
        if (general.length > 0) {
            html += renderCollapsibleSection('general', 'General', general, 'group');
        }
        if (custom.length > 0) {
            html += renderCollapsibleSection('custom', 'Custom Groups', custom, 'group');
        }
        if (state.userChannels.length > 0) {
            html += renderCollapsibleSection('channels', 'Channels', state.userChannels, 'channel');
        }

        if (html === '') {
            html = '<div class="conv-empty-state"><p class="conv-text-secondary">No groups available at this time.</p></div>';
        }

        $container.html(html);

        // Bind section toggle handlers using event delegation
        $container.off('click', '.conversations-section-header').on('click', '.conversations-section-header', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $section = $(this).closest('.conversations-section');
            $section.toggleClass('collapsed');
            console.log('Section toggled:', $section.attr('id'), 'collapsed:', $section.hasClass('collapsed'));
        });

        // Bind group chat click handlers
        $container.off('click', '.conversation-item[data-chat-id]').on('click', '.conversation-item[data-chat-id]', function(e) {
            if ($(e.target).hasClass('btn-join-channel') || $(e.target).hasClass('btn-join-channel-login') || $(e.target).hasClass('btn-leave-channel') || $(e.target).closest('.btn-star-channel').length) return;
            e.stopPropagation();
            var chatId = $(this).data('chat-id');
            var ptoken = $(this).data('ptoken');
            openGroupChat(chatId, ptoken);
        });

        // Bind session click handlers
        $container.off('click', '.conversation-item[data-session-id]').on('click', '.conversation-item[data-session-id]', function(e) {
            if ($(e.target).hasClass('btn-join-channel') || $(e.target).hasClass('btn-join-channel-login') || $(e.target).hasClass('btn-leave-channel') || $(e.target).closest('.btn-star-channel').length) return;
            e.stopPropagation();
            var sessionId = $(this).data('session-id');
            var ptoken = $(this).data('ptoken');
            openSession(sessionId, ptoken);
        });

        // Bind channel click handlers
        $container.off('click', '.conversation-item[data-channel-id]').on('click', '.conversation-item[data-channel-id]', function(e) {
            if ($(e.target).hasClass('btn-join-channel') || $(e.target).hasClass('btn-join-channel-login') || $(e.target).hasClass('btn-leave-channel') || $(e.target).closest('.btn-star-channel').length || $(e.target).closest('.btn-delete-channel').length) return;
            e.stopPropagation();
            var channelId = $(this).data('channel-id');
            openChannel(channelId);
        });

        // Bind join channel button
        $container.off('click', '.btn-join-channel').on('click', '.btn-join-channel', function(e) {
            e.stopPropagation();
            var $btn = $(this);
            var channelId = $btn.data('channel-id');
            $btn.prop('disabled', true).html('<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.8s linear infinite;vertical-align:middle;"></span>');
            joinChannel(channelId, $btn);
        });

        // Bind star channel button
        $container.off('click', '.btn-star-channel').on('click', '.btn-star-channel', function(e) {
            e.stopPropagation();
            var $btn = $(this);
            var channelId = $btn.data('channel-id');
            var isStarred = $btn.hasClass('starred');

            if (isStarred) {
                $btn.removeClass('starred').html('&#9734;').css('color', '#ccc');
            } else {
                $btn.addClass('starred').html('&#9733;').css('color', '#f5a623');
            }

            // Call API to star/unstar
            fetch(GROUPCHAT_API_BASE + '/channels.php?action=star', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    room_slug: state.currentRoomSlug,
                    channel_id: channelId,
                    ptoken: creatorPtoken,
                    starred: !isStarred
                })
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                console.log('Star channel response:', data);
            }).catch(function(err) {
                console.error('Star channel error:', err);
            });
        });

        // Bind delete channel button
        $container.off('click', '.btn-delete-channel').on('click', '.btn-delete-channel', function(e) {
            e.stopPropagation();
            var $btn = $(this);
            var channelId = $btn.data('channel-id');

            if (!confirm('Are you sure you want to delete this channel?')) return;

            $btn.prop('disabled', true);

            fetch(GROUPCHAT_API_BASE + '/channels.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    channel_id: channelId
                })
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                console.log('Delete channel response:', data);
                if (data.success) {
                    // Remove from DOM and state
                    $btn.closest('.conversation-item').fadeOut(300, function() { $(this).remove(); });
                    state.userChannels = state.userChannels.filter(function(c) { return c.id !== channelId; });
                } else {
                    alert(data.message || 'Failed to delete channel');
                    $btn.prop('disabled', false);
                }
            }).catch(function(err) {
                console.error('Delete channel error:', err);
                alert('Failed to delete channel');
                $btn.prop('disabled', false);
            });
        });

        // Bind leave channel button
        $container.off('click', '.btn-leave-channel').on('click', '.btn-leave-channel', function(e) {
            e.stopPropagation();
            var $btn = $(this);
            var channelId = $btn.data('channel-id');

            if (!confirm('Are you sure you want to leave this channel?')) return;

            $btn.prop('disabled', true).text('Leaving...');

            fetch(GROUPCHAT_API_BASE + '/channels.php?action=leave', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    channel_id: channelId,
                    ptoken: creatorPtoken
                })
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                console.log('Leave channel response:', data);
                if (data.success) {
                    // Reload channel stats to refresh the list
                    loadChannelStats(state.currentRoomSlug);
                } else {
                    alert(data.message || 'Failed to leave channel');
                    $btn.prop('disabled', false).text('Leave');
                }
            }).catch(function(err) {
                console.error('Leave channel error:', err);
                alert('Failed to leave channel');
                $btn.prop('disabled', false).text('Leave');
            });
        });
    }

    // Render a collapsible section
    function renderCollapsibleSection(id, title, items, type) {
        var html = '';
        html += '<div class="conversations-section" id="section-' + id + '">';
        html += '  <div class="conversations-section-header">';
        html += '    <div class="conversations-section-header-left">';
        html += '      <h3 class="conversations-section-title">' + escapeHtml(title) + '</h3>';
        html += '      <span class="conversations-section-count">' + items.length + '</span>';
        html += '    </div>';
        html += '    <div class="conversations-section-arrow">' + arrowIcon + '</div>';
        html += '  </div>';
        html += '  <div class="conversations-section-content">';
        html += '    <div class="conversation-list">';

        if (type === 'session') {
            items.forEach(function(session, index) {
                html += renderSessionItem(session, index);
            });
        } else if (type === 'channel') {
            items.forEach(function(channel, index) {
                html += renderChannelItem(channel, index);
            });
        } else {
            items.forEach(function(chat, index) {
                html += renderGroupItem(chat, index);
            });
        }

        html += '    </div>';
        html += '  </div>';
        html += '</div>';

        return html;
    }

    // Render a single group item
    function renderGroupItem(chat, index) {
        var iconHtml;
        var iconColor = iconColors[index % iconColors.length];
        var iconStyle = '';

        if (chat.icon === 'custom' && chat.logo) {
            iconHtml = '<img src="' + escapeHtml(chat.logo) + '" alt="' + escapeHtml(chat.name) + '" style="width:100%;height:100%;object-fit:contain;border-radius:6px;">';
            iconStyle = 'background: #fff; padding: 4px;';
        } else {
            iconHtml = chatIcons[chat.icon] || chatIcons['briefcase'];
            iconStyle = chat.isPinned ? 'background: ' + iconColor + '; color: white;' : '';
        }

        var isMember = false;
        if (creatorPtoken && chat.members && Array.isArray(chat.members)) {
            isMember = chat.members.indexOf(creatorPtoken) !== -1;
        }

        var html = '';
        html += '<div class="conversation-item" tabindex="0" role="button" data-chat-id="' + chat.id + '" data-ptoken="' + escapeHtml(chat.ptoken || '') + '">';
        html += '  <div class="conversation-icon" style="' + iconStyle + '">' + iconHtml + '</div>';
        html += '  <div class="conversation-info">';
        html += '    <h4 class="conversation-name">' + escapeHtml(chat.name) + '</h4>';
        html += '    <p class="conversation-preview">' + escapeHtml(chat.lastMessage || chat.description || '') + '</p>';
        html += '  </div>';
        if (isLoggedIn) {
            html += '  <div class="channel-owner-actions" style="display:flex;align-items:center;gap:6px;margin-right:4px;">';
            html += '    <button class="conv-btn conv-btn-sm btn-star-channel" data-channel-id="' + chat.id + '" title="Star" style="background:none;border:none;cursor:pointer;padding:4px;font-size:16px;color:#ccc;">&#9734;</button>';
            html += '  </div>';
        }
        if (isMember && isLoggedIn) {
            html += '  <div class="conversation-join" style="display:flex;align-items:center;margin-right:8px;">';
            html += '    <button class="conv-btn conv-btn-sm btn-leave-channel" data-channel-id="' + chat.id + '" style="font-size:11px;color:#dc3545;border:1px solid #dc3545;background:none;">Leave</button>';
            html += '  </div>';
        } else if (!isMember && isLoggedIn) {
            html += '  <div class="conversation-join" style="display:flex;align-items:center;margin-right:8px;">';
            html += '    <button class="conv-btn conv-btn-primary conv-btn-sm btn-join-channel" data-channel-id="' + chat.id + '" style="font-size:11px;">Join</button>';
            html += '  </div>';
        } else if (!isMember && !isLoggedIn) {
            html += '  <div class="conversation-join" style="display:flex;align-items:center;margin-right:8px;">';
            html += '    <button class="conv-btn conv-btn-primary conv-btn-sm btn-join-channel-login" data-toggle="modal" data-target="#config-modal" style="font-size:11px;">Login to Join</button>';
            html += '  </div>';
        }
        html += '  <div class="conversation-meta">';
        html += '    <span class="conversation-active">' + (chat.activeMembers || 0) + ' active</span>';
        if (chat.unreadCount > 0) {
            html += '    <span class="conversation-unread">' + chat.unreadCount + '</span>';
        }
        html += '  </div>';
        html += '</div>';

        return html;
    }

    // Render a single session item
    function renderSessionItem(session, index) {
        var iconHtml;
        var iconColor = iconColors[index % iconColors.length];
        var iconStyle = '';

        if (session.icon === 'custom' && session.logo) {
            iconHtml = '<img src="' + escapeHtml(session.logo) + '" alt="' + escapeHtml(session.name) + '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
            iconStyle = 'background: #fff; border-radius: 50%; overflow: hidden;';
        } else {
            iconHtml = chatIcons[session.icon] || chatIcons['users'];
            iconStyle = 'background: ' + iconColor + '; color: white;';
        }

        var timeSlot = formatSessionTime(session);

        var speakersHtml = '';
        if (session.speakers && session.speakers.length > 0) {
            speakersHtml = '<div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:4px;">';
            session.speakers.forEach(function(s) {
                speakersHtml += '<div style="display:flex;align-items:center;gap:4px;">';
                if (s.profileImg) {
                    speakersHtml += '<img src="' + escapeHtml(s.profileImg) + '" alt="" style="width:20px;height:20px;border-radius:50%;object-fit:cover;">';
                }
                speakersHtml += '<span style="font-size:11px;color:#666;">' + escapeHtml(s.name);
                if (s.designation) {
                    speakersHtml += ', <span style="color:#999;">' + escapeHtml(s.designation);
                    if (s.company) speakersHtml += ', ' + escapeHtml(s.company);
                    speakersHtml += '</span>';
                }
                speakersHtml += '</span></div>';
            });
            speakersHtml += '</div>';
        }

        var isMember = false;
        if (creatorPtoken && session.members && Array.isArray(session.members)) {
            isMember = session.members.indexOf(creatorPtoken) !== -1;
        }

        var liveDotHtml = '';
        if (session.isLive) {
            liveDotHtml = '<span style="position:absolute;top:2px;left:2px;width:12px;height:12px;background:#28a745;border-radius:50%;border:2px solid #fff;z-index:1;" title="Live"></span>';
        }

        var html = '';
        html += '<div class="conversation-item session-item" tabindex="0" role="button" data-session-id="' + session.id + '" data-ptoken="' + escapeHtml(session.ptoken || '') + '">';
        html += '  <div style="position:relative;">';
        html += liveDotHtml;
        html += '    <div class="conversation-icon" style="' + iconStyle + '">' + iconHtml + '</div>';
        html += '  </div>';
        html += '  <div class="conversation-info">';
        html += '    <h4 class="conversation-name">' + escapeHtml(ucfirst(session.name)) + '</h4>';
        if (timeSlot) {
            html += '    <p class="conversation-preview" style="color:#3563ae;font-size:12px;margin-bottom:2px;">' + timeSlot + '</p>';
        }
        if (speakersHtml) {
            html += speakersHtml;
        }
        html += '  </div>';
        if (isLoggedIn) {
            html += '  <div class="channel-owner-actions" style="display:flex;align-items:center;gap:6px;margin-right:4px;">';
            html += '    <button class="conv-btn conv-btn-sm btn-star-channel" data-channel-id="' + session.id + '" title="Star" style="background:none;border:none;cursor:pointer;padding:4px;font-size:16px;color:#ccc;">&#9734;</button>';
            html += '  </div>';
        }
        if (isMember && isLoggedIn) {
            html += '  <div class="conversation-join" style="display:flex;align-items:center;margin-right:8px;">';
            html += '    <button class="conv-btn conv-btn-sm btn-leave-channel" data-channel-id="' + session.id + '" style="font-size:11px;color:#dc3545;border:1px solid #dc3545;background:none;">Leave</button>';
            html += '  </div>';
        } else if (!isMember && isLoggedIn) {
            html += '  <div class="conversation-join" style="display:flex;align-items:center;margin-right:8px;">';
            html += '    <button class="conv-btn conv-btn-primary conv-btn-sm btn-join-channel" data-channel-id="' + session.id + '" style="font-size:11px;">Join</button>';
            html += '  </div>';
        } else if (!isMember && !isLoggedIn) {
            html += '  <div class="conversation-join" style="display:flex;align-items:center;margin-right:8px;">';
            html += '    <button class="conv-btn conv-btn-primary conv-btn-sm btn-join-channel-login" data-toggle="modal" data-target="#config-modal" style="font-size:11px;">Login to Join</button>';
            html += '  </div>';
        }
        html += '  <div class="conversation-meta" style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">';
        if (session.isLive) {
            html += '    <span style="color:#28a745;font-weight:600;font-size:12px;">● Live</span>';
        } else {
            html += '    <span style="color:#999;font-size:12px;">Not Live</span>';
        }
        html += '    <span class="conversation-active">' + (session.activeMembers || 0) + ' active</span>';
        if (session.unreadCount > 0) {
            html += '    <span class="conversation-unread">' + session.unreadCount + '</span>';
        }
        html += '  </div>';
        html += '</div>';

        return html;
    }

    // Format session time using format_event_timestamp if available
    function formatSessionTime(session) {
        if (!session.dateFrom || !session.dateTo) return '';

        // Check if format_event_timestamp is available (from taoh.js)
        if (typeof format_event_timestamp !== 'function') {
            // Fallback: just return raw dates
            return session.dateFrom + ' - ' + session.dateTo;
        }

        try {
            // Get user timezone
            var user_timezone = 'UTC';
            if (typeof taoh_user_timezone === 'function') {
                user_timezone = taoh_user_timezone() || 'UTC';
            } else if (typeof getCookie === 'function') {
                var clientTz = getCookie('client_time_zone');
                user_timezone = clientTz || Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
            } else {
                user_timezone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
            }

            var event_timestamp_start_data = {
                utc_datetime: session.dateFrom.replace(/[T:-]/g, '') + '00',
                local_datetime: session.dateFrom.replace(/[T:-]/g, '') + '00',
                timezone: session.timezone,
                locality: ''
            };

            var event_timestamp_end_data = {
                utc_datetime: session.dateTo.replace(/[T:-]/g, '') + '00',
                local_datetime: session.dateTo.replace(/[T:-]/g, '') + '00',
                timezone: session.timezone,
                locality: ''
            };

            var startdate = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'dd MMM yyyy', 0);
            var starttime = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'hh:mm A', 1);
            var enddate = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'dd MMM yyyy', 0);
            var endtime = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'hh:mm A', 1);

            if (startdate === enddate) {
                return startdate + ', ' + starttime + ' - ' + endtime;
            } else {
                return startdate + ' ' + starttime + ' - ' + enddate + ' ' + endtime;
            }
        } catch (e) {
            console.error('Error formatting session time:', e);
            return '';
        }
    }

    // Open Session chat
    function openSession(sessionId, ptoken) {
        console.log('Opening session chat:', sessionId, ptoken);

        // Find the session from state
        var session = state.sessions.find(function(s) {
            return s.id == sessionId;
        });
        if (!isChannelMember(session)) { console.log('Not a member, join first'); return; }

        var sessionName = (session && session.name) || 'Session Chat';
        var timeSlot = session ? formatSessionTime(session) : '';
        var isLive = session ? session.isLive : false;

        // Update modal content
        $('#chat-modal-booth-name').text(sessionName);

        // Show subtitle with timeslot and live status
        var $subtitle = $('#chat-modal-subtitle');
        var $timeslot = $('#chat-modal-timeslot');
        var $liveStatus = $('#chat-modal-live-status');

        if (timeSlot || session) {
            $timeslot.text(timeSlot);

            if (isLive) {
                $liveStatus.removeClass('not-live').addClass('is-live');
                $liveStatus.html('<span class="live-dot"></span> Live');
            } else {
                $liveStatus.removeClass('is-live').addClass('not-live');
                $liveStatus.html('<span class="live-dot"></span> Not Live');
            }

            $subtitle.show();
        } else {
            $subtitle.hide();
        }

        showChatLoading();
        $('#chat-coming-soon-modal').addClass('active');
        loadChatScripts(function() { initChat(sessionId); });
    }

    // Load Wehbae Users (Direct Messages)
    function loadDirectMessages() {
        var $list = $('#dm-list');
        var $empty = $('#dm-empty');

        // Show loading state
        $list.html(getLoadingHTML(true));
        $empty.hide();

        if (!state.currentRoomSlug) {
            console.log('Wehbae: No room slug yet, waiting...');
            $list.html('');
            $empty.show();
            return;
        }

        // Fetch room users from rooms.php?action=users
        fetch(GROUPCHAT_API_BASE + '/rooms.php?room_slug=' + encodeURIComponent(state.currentRoomSlug) + '&action=users')
        .then(function(res) { return res.json(); })
        .then(function(response) {
            if (response.success && response.users && Object.keys(response.users).length > 0) {
                // Convert users object to array
                state.directMessages = Object.keys(response.users).map(function(key) {
                    var u = response.users[key];

                    // title and company can be objects like {"id": "slug:>Display Name"}
                    var titleStr = '';
                    if (u.title && typeof u.title === 'object') {
                        var tVal = Object.values(u.title)[0] || '';
                        titleStr = tVal.indexOf(':>') !== -1 ? tVal.split(':>')[1] : tVal;
                    } else if (typeof u.title === 'string') {
                        titleStr = u.title;
                    }

                    var companyStr = '';
                    if (u.company && typeof u.company === 'object') {
                        var cVal = Object.values(u.company)[0] || '';
                        companyStr = cVal.indexOf(':>') !== -1 ? cVal.split(':>')[1] : cVal;
                    } else if (typeof u.company === 'string') {
                        companyStr = u.company;
                    }

                    // avatar priority: avatar_image URL > avatar PNG > default
                    var avatarUrl = '/club/assets/images/avatar/PNG/128/avatar_def.png';
                    if (u.avatar_image && u.avatar_image.length > 0) {
                        avatarUrl = u.avatar_image;
                    } else if (u.avatar && u.avatar.length > 0) {
                        avatarUrl = '/club/assets/images/avatar/PNG/128/' + u.avatar + '.png';
                    }

                    return {
                        id: key,
                        ptoken: u.ptoken || '',
                        name: (u.chat_name || ((u.fname || '') + ' ' + (u.lname || '')).trim()) || 'Unknown',
                        fullName: ((u.fname || '') + ' ' + (u.lname || '')).trim(),
                        avatar: avatarUrl,
                        title: titleStr,
                        company: companyStr,
                        type: u.type || ''
                    };
                });
                renderDirectMessages();
            } else {
                state.directMessages = [];
                $list.html('');
                $empty.show();
            }
        })
        .catch(function(err) {
            console.error('Error loading wehbae users:', err);
            state.directMessages = [];
            $list.html('');
            $empty.show();
        });
    }

    function renderDirectMessages() {
        var $list = $('#dm-list');
        var $empty = $('#dm-empty');
        var html = '';

        if (state.directMessages.length === 0) {
            $list.html('');
            $empty.show();
            return;
        }

        $empty.hide();

        state.directMessages.forEach(function(user) {
            var fullName = user.fullName || user.name || 'Unknown';
            var avatarHtml = '<img src="' + escapeHtml(user.avatar) + '" alt="' + escapeHtml(fullName) + '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';

            var subtitle = '';
            if (user.title && user.company) {
                subtitle = user.title + ' at ' + user.company;
            } else if (user.title) {
                subtitle = user.title;
            } else if (user.company) {
                subtitle = user.company;
            }

            html += '<div class="conversation-item" tabindex="0" role="button" data-user-id="' + escapeHtml(user.id) + '" data-ptoken="' + escapeHtml(user.ptoken) + '">';
            html += '  <div class="conv-avatar-wrapper">';
            html += '    <div class="conv-avatar">' + avatarHtml + '</div>';
            html += '  </div>';
            html += '  <div class="conversation-info">';
            html += '    <h4 class="conversation-name">' + escapeHtml(fullName) + '</h4>';
            if (subtitle) {
                html += '    <p class="conversation-preview">' + escapeHtml(subtitle) + '</p>';
            }
            if (user.type) {
                html += '    <p class="conversation-preview" style="font-size:11px;color:#888;">' + escapeHtml(user.type) + '</p>';
            }
            html += '  </div>';
            html += '  <div class="conversation-meta">';
            html += '    <button class="conv-btn conv-btn-primary" style="padding:4px 12px;font-size:12px;" data-dm-user="' + escapeHtml(user.id) + '">Message</button>';
            html += '  </div>';
            html += '</div>';
        });

        $list.html(html);

        // Bind click on Message button
        $list.find('.conv-btn[data-dm-user]').on('click', function(e) {
            e.stopPropagation();
            var userId = $(this).data('dm-user');
            var ptoken = $(this).closest('.conversation-item').data('ptoken');
            openDirectMessage(userId, ptoken);
        });

        // Bind click on item row
        $list.find('.conversation-item').on('click', function() {
            var userId = $(this).data('user-id');
            var ptoken = $(this).data('ptoken');
            openDirectMessage(userId, ptoken);
        });
    }

    // Speed Networking
    function initSpeedNetworking() {
        $('#btn-join-queue').on('click', function() {
            if (state.speedNetworking.inQueue) {
                leaveQueue();
            } else {
                joinQueue();
            }
        });
    }

    function loadSpeedNetworking() {
        $.ajax({
            url: '/club/core/club/api/get_speed_networking.php',
            method: 'GET',
            data: {
                event_id: getEventId()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    state.speedNetworking = $.extend(state.speedNetworking, response.data);
                    renderSpeedNetworking();
                } else {
                    // Use default/sample data
                    state.speedNetworking.queueCount = 8;
                    state.speedNetworking.avgWaitTime = 45;
                    renderSpeedNetworking();
                }
            },
            error: function() {
                // Fallback
                state.speedNetworking.queueCount = 8;
                state.speedNetworking.avgWaitTime = 45;
                renderSpeedNetworking();
            }
        });
    }

    function renderSpeedNetworking() {
        var $container = $('#speed-networking-container');
        var $btnText = $('#queue-btn-text');
        var $queueCount = $('#queue-count');
        var $avgWait = $('#avg-wait');

        if (state.speedNetworking.inQueue) {
            $container.addClass('in-queue');
            $btnText.text('Leave Queue');

            if (state.speedNetworking.queuePosition) {
                // Show queue position
                var posHtml = '<div class="queue-position">#' + state.speedNetworking.queuePosition + '</div>';
                posHtml += '<div class="queue-position-label">Your position in queue</div>';
                $container.find('p').html(posHtml);
            }
        } else {
            $container.removeClass('in-queue');
            $btnText.text('Join Queue');
            $container.find('p').html('Meet someone new in a 5-minute video chat.<br>You can leave anytime. No pressure.');
        }

        $queueCount.text(state.speedNetworking.queueCount + ' people waiting');
        $avgWait.text('Avg wait: ' + state.speedNetworking.avgWaitTime + ' seconds');
    }

    function joinQueue() {
        $.ajax({
            url: '/club/core/club/api/speed_networking_queue.php',
            method: 'POST',
            data: {
                action: 'join',
                event_id: getEventId()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    state.speedNetworking.inQueue = true;
                    state.speedNetworking.queuePosition = response.position || 1;
                    renderSpeedNetworking();

                    // Start polling for match
                    startMatchPolling();
                } else {
                    alert(response.message || 'Failed to join queue');
                }
            },
            error: function() {
                // Demo mode - simulate joining
                state.speedNetworking.inQueue = true;
                state.speedNetworking.queuePosition = Math.floor(Math.random() * 5) + 1;
                state.speedNetworking.queueCount++;
                renderSpeedNetworking();
            }
        });
    }

    function leaveQueue() {
        $.ajax({
            url: '/club/core/club/api/speed_networking_queue.php',
            method: 'POST',
            data: {
                action: 'leave',
                event_id: getEventId()
            },
            dataType: 'json',
            complete: function() {
                state.speedNetworking.inQueue = false;
                state.speedNetworking.queuePosition = null;
                state.speedNetworking.queueCount = Math.max(0, state.speedNetworking.queueCount - 1);
                renderSpeedNetworking();
                stopMatchPolling();
            }
        });
    }

    var matchPollInterval = null;
    function startMatchPolling() {
        if (matchPollInterval) return;

        matchPollInterval = setInterval(function() {
            $.ajax({
                url: '/club/core/club/api/speed_networking_queue.php',
                method: 'GET',
                data: {
                    action: 'check_match',
                    event_id: getEventId()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.matched) {
                        stopMatchPolling();
                        openSpeedNetworkingCall(response.matchedUser);
                    } else if (response.position) {
                        state.speedNetworking.queuePosition = response.position;
                        renderSpeedNetworking();
                    }
                }
            });
        }, 3000);
    }

    function stopMatchPolling() {
        if (matchPollInterval) {
            clearInterval(matchPollInterval);
            matchPollInterval = null;
        }
    }

    function openSpeedNetworkingCall(matchedUser) {
        // Implement video call opening logic
        console.log('Matched with:', matchedUser);
        // Could open a modal or redirect to video call
        if (typeof window.openVideoCall === 'function') {
            window.openVideoCall(matchedUser);
        }
    }

    // Suggested Action Card
    function initSuggestedAction() {
        $('#suggested-action-dismiss').on('click', function() {
            state.suggestedActionDismissed = true;
            $('#suggested-action').removeClass('suggested-action-card--visible');
        });

        $('#suggested-action-cta').on('click', function() {
            var action = $(this).data('action');
            var id = $(this).data('id');
            var ptoken = $(this).data('ptoken') || '';

            if (action === 'join-chat') {
                openGroupChat(id, ptoken);
            } else if (action === 'join-speed') {
                switchConversationTab('speed');
                joinQueue();
            }
        });
    }

    function updateSuggestedAction() {
        if (state.suggestedActionDismissed) return;

        var $card = $('#suggested-action');
        var $message = $('#suggested-action-message');
        var $detail = $('#suggested-action-detail');
        var $cta = $('#suggested-action-cta');

        // Get all available groups with names
        var availableGroups = state.groupChats.filter(function(chat) {
            return chat.name && chat.name.trim() !== '';
        });

        // Filter sponsors only (those with sponsorType)
        var sponsorGroups = availableGroups.filter(function(chat) {
            return chat.sponsorType && chat.sponsorType.trim() !== '';
        });

        // Find most active chat (from all groups - sponsors + exhibitors)
        var mostActiveChat = null;
        var maxActive = 0;
        availableGroups.forEach(function(chat) {
            if (chat.activeMembers > maxActive) {
                maxActive = chat.activeMembers;
                mostActiveChat = chat;
            }
        });

        // Priority: 1) Live sponsor only, 2) Speed networking queue, 3) Active groups > 5 members (sponsors + exhibitors)
        if (sponsorGroups.length > 0) {
            // 1st Priority: Pick a random sponsor (not exhibitor)
            var randomIndex = Math.floor(Math.random() * sponsorGroups.length);
            var randomSponsor = sponsorGroups[randomIndex];

            $message.text('"' + randomSponsor.name + '" booth is live now');
            $detail.text('Join the conversation with ' + (randomSponsor.activeMembers || 0) + ' attendees');
            $cta.text('Visit Booth').data('action', 'join-chat').data('id', randomSponsor.id).data('ptoken', randomSponsor.ptoken || '');
            $card.addClass('suggested-action-card--visible');
        } else if (state.speedNetworking.queueCount > 3) {
            // 2nd Priority: Speed networking queue
            $message.text('Speed Networking is busy!');
            $detail.text(state.speedNetworking.queueCount + ' people looking to connect');
            $cta.text('Join Queue').data('action', 'join-speed').data('id', null);
            $card.addClass('suggested-action-card--visible');
        } else if (mostActiveChat && maxActive > 5) {
            // 3rd Priority: Most active chat with > 5 members (includes all groups)
            $message.text('"' + mostActiveChat.name + '" booth is buzzing!');
            $detail.text(maxActive + ' people chatting now');
            $cta.text('Join Chat').data('action', 'join-chat').data('id', mostActiveChat.id).data('ptoken', mostActiveChat.ptoken || '');
            $card.addClass('suggested-action-card--visible');
        }
    }

    // Chat Opening Functions
    function openGroupChat(chatId, ptoken) {
        console.log('Opening exhibitor chat:', chatId, ptoken);

        var chat = state.groupChats.find(function(c) { return c.id == chatId; })
              || state.defaultChannels.find(function(c) { return c.id == chatId; });
        if (!isChannelMember(chat)) { console.log('Not a member, join first'); return; }

        var chatName = (chat && chat.name) || 'Booth Chat';

        $('#chat-modal-booth-name').text(chatName);
        $('#chat-modal-subtitle').hide();
        showChatLoading();
        $('#chat-coming-soon-modal').addClass('active');
        loadChatScripts(function() { initChat(chatId); });
    }

    // Cleanup chat on modal close
    function closeChatModal() {
        $('#chat-coming-soon-modal').removeClass('active');
        try {
            if (typeof TaoGroupChat !== 'undefined' && TaoGroupChat.setPresence) {
                TaoGroupChat.setPresence(false);
            }
        } catch (e) { console.warn('Error setting presence off:', e); }
        var el = document.getElementById('tao-groupchat');
        if (el) el.innerHTML = '';
    }

    // Initialize chat modal
    function initChatModal() {
        // Close modal on close button click
        $('#chat-modal-close').on('click', function() {
            closeChatModal();
        });

        // Close modal on overlay click (outside the modal content)
        $('#chat-coming-soon-modal').on('click', function(e) {
            if (e.target === this) {
                closeChatModal();
            }
        });

        // Close modal on Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                if ($('#chat-coming-soon-modal').hasClass('active')) {
                    closeChatModal();
                }
                if ($('#create-channel-modal').hasClass('active')) {
                    $('#create-channel-modal').removeClass('active');
                }
            }
        });

        // Create Channel modal
        $('#btn-create-channel').on('click', function() {
            $('#create-channel-modal').addClass('active');
        });

        $('#create-channel-modal-close').on('click', function() {
            $('#create-channel-modal').removeClass('active');
        });

        $('#create-channel-modal').on('click', function(e) {
            if (e.target === this) {
                $(this).removeClass('active');
            }
        });

        // Create Channel form submission
        $('#create-channel-form').on('submit', function(e) {
            e.preventDefault();
            var channelName = $('#channel-name').val().trim();
            var description = $('#channel-description').val().trim();
            var accessType = $('#channel-access-type').val();

            if (!channelName) { alert('Channel name is required'); return; }
            if (!state.currentRoomSlug) { alert('Room not ready. Please try again.'); return; }

            var postData = {
                room_slug: state.currentRoomSlug,
                channel_name: channelName,
                description: description,
                access_type: accessType,
                channel_type: 'user_created',
                ptoken: creatorPtoken,
                profile_info: userProfileInfo,
                display_name: (userProfileInfo && userProfileInfo.chat_name) ? userProfileInfo.chat_name : ''
            };

            console.log('Creating channel:', postData);

            fetch(GROUPCHAT_API_BASE + '/channels.php?action=create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(postData)
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                console.log('Create channel response:', data);
                if (data.success) {
                    $('#create-channel-modal').removeClass('active');
                    $('#create-channel-form')[0].reset();
                    loadChannelStats(state.currentRoomSlug);
                } else {
                    alert(data.error || 'Failed to create channel');
                }
            }).catch(function(err) {
                console.error('Error creating channel:', err);
                alert('Failed to create channel');
            });
        });
    }

    function openDirectMessage(dmId, userId) {
        console.log('Opening DM:', dmId, userId);
        // Implement your DM opening logic here
        if (typeof window.openChat === 'function') {
            window.openChat('dm', dmId, userId);
        } else if (typeof window.parent !== 'undefined' && typeof window.parent.openChat === 'function') {
            window.parent.openChat('dm', dmId, userId);
        }
    }

    function getSampleDirectMessages() {
        return [];
    }

    // Utility Functions
    function getEventId() {
        // Try to get event ID from various sources
        if (typeof window.eventId !== 'undefined') return window.eventId;
        if (typeof window.EVENT_ID !== 'undefined') return window.EVENT_ID;

        // Try URL params
        var urlParams = new URLSearchParams(window.location.search);
        var eventParam = urlParams.get('event_id') || urlParams.get('eventId') || urlParams.get('id');
        if (eventParam) return eventParam;

        // Try data attribute
        var container = document.querySelector('.conversations-container');
        if (container && container.dataset.eventId) return container.dataset.eventId;

        return null;
    }

    function getEventToken() {
        // Use the local eventtoken variable injected from PHP
        if (eventtoken && eventtoken !== '') return eventtoken;

        // Fallback: Try to get event token from various sources
        if (typeof window.eventtoken !== 'undefined' && window.eventtoken) return window.eventtoken;
        if (typeof window.eventToken !== 'undefined' && window.eventToken) return window.eventToken;
        if (typeof window.EVENT_TOKEN !== 'undefined' && window.EVENT_TOKEN) return window.EVENT_TOKEN;

        // Try URL params
        var urlParams = new URLSearchParams(window.location.search);
        var tokenParam = urlParams.get('eventtoken') || urlParams.get('eventToken') || urlParams.get('event_token');
        if (tokenParam) return tokenParam;

        // Try URL path (e.g., /club/events/{eventtoken}/...)
        var pathMatch = window.location.pathname.match(/\/events\/([a-zA-Z0-9_-]+)/);
        if (pathMatch && pathMatch[1]) return pathMatch[1];

        // Try data attribute
        var container = document.querySelector('.conversations-container');
        if (container && container.dataset.eventtoken) return container.dataset.eventtoken;

        // Try hidden input
        var hiddenInput = document.querySelector('input[name="eventtoken"]');
        if (hiddenInput && hiddenInput.value) return hiddenInput.value;

        return null;
    }

    function getLoadingHTML(isAvatar) {
        var iconStyle = isAvatar ? 'border-radius: 50%;' : '';
        return '<div class="conversation-list-loading">' +
            '<div class="conversation-skeleton"><div class="skeleton-icon" style="' + iconStyle + '"></div><div class="skeleton-content"><div class="skeleton-line"></div><div class="skeleton-line"></div></div></div>' +
            '<div class="conversation-skeleton"><div class="skeleton-icon" style="' + iconStyle + '"></div><div class="skeleton-content"><div class="skeleton-line"></div><div class="skeleton-line"></div></div></div>' +
            '<div class="conversation-skeleton"><div class="skeleton-icon" style="' + iconStyle + '"></div><div class="skeleton-content"><div class="skeleton-line"></div><div class="skeleton-line"></div></div></div>' +
            '</div>';
    }

    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function ucfirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function getInitials(name) {
        if (!name) return '?';
        var parts = name.split(' ');
        if (parts.length >= 2) {
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }
        return name.substring(0, 2).toUpperCase();
    }

    function formatTime(timestamp) {
        if (!timestamp) return '';

        var date = new Date(timestamp);
        var now = new Date();
        var diff = now - date;

        var minutes = Math.floor(diff / 60000);
        var hours = Math.floor(diff / 3600000);
        var days = Math.floor(diff / 86400000);

        if (minutes < 1) return 'Just now';
        if (minutes < 60) return minutes + 'm ago';
        if (hours < 24) return hours + 'h ago';
        if (days < 7) return days + 'd ago';

        return date.toLocaleDateString();
    }

    // Expose functions globally for external use
    window.conversationsTab = {
        switchTab: switchConversationTab,
        initRoom: initConversationRoom,
        refresh: function() {
            initConversationRoom();
            loadDirectMessages();
            loadSpeedNetworking();
        },
        openGroupChat: openGroupChat,
        openSession: openSession,
        openDirectMessage: openDirectMessage
    };

})();
</script>