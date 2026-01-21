<?php

function constructDefaultChannelInfo($room_slug, $channels_input_data): array
{
    $channels = [];
    foreach ($channels_input_data as $value) {
        if(empty($value['name'])) {
            continue;
        }
        $channel_name = $value['name'];
        $channel_description = $value['description'] ?? '';
        $channel_slug_data = [$room_slug, $channel_name];
        asort($channel_slug_data);
        $channel_id = generateSecureSlug(implode('_', $channel_slug_data), 16);
        $channel_type = defined('TAOH_CHANNEL_DEFAULT') ? TAOH_CHANNEL_DEFAULT : 1;
        $channel_data = [
            'name' => $channel_name,
            'description' => $channel_description,
            'ptoken' => '',
            'source' => 'admin',
            'isDefault' => 1
        ];

        $channels[] = [
            'channel_id' => $channel_id,
            'channel_type' => $channel_type,
            'channel_data' => $channel_data,
            'channel_members' => [],
            'channel_passcode' => '',
            'channel_ticket_type' => '',
            'channel_visibility' => 'public',
            'show_default' => 1
        ];
    }

    return $channels;
}

function constructDefaultEventChannelInfo($room_slug, $eventtoken, $channels_input_data): array
{
    $channels = [];
    foreach ($channels_input_data as $value) {
        if(empty($value['channel_name'])) {
            continue;
        }
        $channel_name = $value['channel_name'];
        $channel_description = $value['channel_desc'] ?? '';
        $channel_slug_data = [$room_slug, $channel_name];
        asort($channel_slug_data);
        $channel_id = generateSecureSlug(implode('_', $channel_slug_data), 16);
        $channel_type = defined('TAOH_CHANNEL_DEFAULT') ? TAOH_CHANNEL_DEFAULT : 1;
        $channel_data = [
            'name' => $channel_name,
            'description' => $channel_description,
            'ptoken' => '',
            'source' => 'admin',
            'eventtoken' => $eventtoken
        ];

        $channels[] = [
            'channel_id' => $channel_id,
            'channel_type' => $channel_type,
            'channel_data' => $channel_data,
            'channel_members' => [],
            'channel_passcode' => '',
            'channel_ticket_type' => '',
            'channel_visibility' => 'public',
            'show_default' => 0
        ];
    }

    return $channels;
}

function constructEventSessionChannelInfo($eventtoken, $channels_input_data): array
{
    $channels = [];
    foreach ($channels_input_data as $value) {
        if(empty($value['spk_title'])) {
            continue;
        }
        $speakerId = $value['ID'];
        $channel_name = $value['spk_title'];
        $channel_description = $value['spk_desc'];
        $channel_slug_data = [$eventtoken, 'session', $speakerId];
        asort($channel_slug_data);
        $channel_id = generateSecureSlug(implode('_', $channel_slug_data), 16);
        $channel_type = defined('TAOH_CHANNEL_SESSION') ? TAOH_CHANNEL_SESSION : 7;
        $channel_data = [
            'name' => $channel_name,
            'description' => $channel_description,
            'ptoken' => '',
            'source' => 'admin',
            'speaker_hall' => $value['spk_hall'] ?? '',
            'speaker_logo' => $value['spk_logo_image'] ?? '',
            'speaker_id' => $value['ID'] ?? '',
            'eventtoken' => $eventtoken
        ];

        $channels[] = [
            'channel_id' => $channel_id,
            'channel_type' => $channel_type,
            'channel_data' => $channel_data,
            'channel_members' => [],
            'channel_passcode' => '',
            'channel_ticket_type' => '',
            'channel_visibility' => 'public',
            'show_default' => 1
        ];
    }

    return $channels;
}

function constructEventExhibitorChannelInfo($eventtoken, $channels_input_data): array
{
    $channels = [];
    foreach ($channels_input_data as $value) {
        if(empty($value['exh_session_title'])) {
            continue;
        }
        $exhibitorId = $value['ID'];
        $channel_name = $value['exh_session_title'];
        $channel_description = $value['exh_description'];
        $channel_slug_data = [$eventtoken, 'exhibitor', $exhibitorId];
        asort($channel_slug_data);
        $channel_id = generateSecureSlug(implode('_', $channel_slug_data), 16);
        $channel_type = defined('TAOH_CHANNEL_EXHIBITOR') ? TAOH_CHANNEL_EXHIBITOR : 2;
        $channel_data = [
            'name' => $channel_name,
            'description' => $channel_description,
            'ptoken' => '',
            'source' => 'admin',
            'exhibitor_hall' => $value['exh_hall'] ?? '',
            'exhibitor_raffles' => $value['exh_raffles'] ?? '',
            'exhibitor_logo' => $value['exh_logo'] ?? '',
            'streaming_link' => $value['exh_streaming_link'] ?? '',
            'exhibitor_id' => $value['ID'] ?? '',
            'eventtoken' => $eventtoken
        ];

        $channels[] = [
            'channel_id' => $channel_id,
            'channel_type' => $channel_type,
            'channel_data' => $channel_data,
            'channel_members' => [],
            'channel_passcode' => '',
            'channel_ticket_type' => '',
            'channel_visibility' => 'public',
            'show_default' => 1
        ];
    }

    return $channels;
}


function createBulkRoomInfoChannels($room_info, $my_ptoken)
{
    $room_data = $room_info['room'] ?? [];

    $roomslug = $room_data['keyslug'] ?? null;
    $room_type = $room_data['room_type'] ?? '';
    $event_token = $room_data['eventtoken'] ?? null;
    $keyword = ($room_type === 'event' && !empty($event_token)) ? $event_token : 'club';
    $room_info_channels = $room_data['channels'] ?? [];

    if (!$roomslug || empty($room_info_channels)) {
        return json_encode(['success' => false, 'error' => 'Missing required fields']);
    }

    $roomslugChangeTypes = array_values(array_filter([
        defined('TAOH_CHANNEL_EXHIBITOR') ? TAOH_CHANNEL_EXHIBITOR : null,
        defined('TAOH_CHANNEL_SPONSOR') ? TAOH_CHANNEL_SPONSOR : null,
        defined('TAOH_CHANNEL_SESSION') ? TAOH_CHANNEL_SESSION : null,
    ], static fn($v) => $v !== null));

    $channels_to_create = [];
    foreach ($room_info_channels as $channelConfig) {
        $channel_roomslug = $roomslug;
        $channel_type = $channelConfig['channel_type'] ?? '';
        $channel_data = $channelConfig['channel_data'] ?? [];

        if (in_array($channel_type, $roomslugChangeTypes)) {
            $channel_roomslug = $eventToken ?? $keyword;
        }

        $channels_to_create[] = [
            'roomSlug' => $channel_roomslug,
            'keyword' => $keyword,
            'channelId' => $channelConfig['channel_id'] ?? '',
            'channelType' => $channelConfig['channel_type'] ?? '',
            'channelData' => $channel_data,
            'channelMembers' => $channelConfig['channel_members'] ?? [],
            'channelEncryptPasscode' => $channelConfig['channel_passcode'] ?? '',
            'channelTicketType' => $channelConfig['channel_ticket_type'] ?? '',
            'visibility' => $channelConfig['channel_visibility'] ?? 'public',
            'showDefault' => $channelConfig['show_default'] ?? 0,
        ];
    }

    $taoh_vals = array(
        "ops" => 'channel',
        'action' => 'create_channels_bulk',
        'roomSlug' => $roomslug,
        'channels' => $channels_to_create,
        'code' => TAOH_OPS_CODE,
        'key' => $my_ptoken
    );

//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);exit();

    return taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
}


function generateRoomSlug($input_data = [], $room_type = 'club'): array
{
    $response = ['success' => false];

    $country_code = $input_data['country_code'] ?? 'US';
    $country_name = $input_data['country_name'] ?? 'United States';
    $timezone = $input_data['local_timezone'] ?? '';

    $tz = in_array($timezone, DateTimeZone::listIdentifiers(), true)
        ? new DateTimeZone($timezone)
        : new DateTimeZone('UTC');

    $date = new DateTime('now', $tz);
    $tzAbbreviation = $date->format('T') ?: 'UTC';

    $geo_enable = 0;
    $attendee_count = 0;

    if ($room_type === 'event') {
        $eventtoken = $input_data['eventtoken'] ?? '';

        if (!empty($eventtoken)) {
            if (isset($input_data['country_locked'])) {
                if (!empty($input_data['country_locked'])) {
                    $geo_enable = 1;
                }
            } else {
                // Fetch event details
                $event_detail_cache_name = 'event_detail_' . $eventtoken;
                $taoh_vals = array(
                    'token' => taoh_get_api_token(1, 1),
                    'ops' => 'baseinfo',
                    'mod' => 'events',
                    'eventtoken' => $eventtoken,
                    'cache_name' => $event_detail_cache_name,
                    'ttl' => 2 * 60 * 60,
                    //'cfcc2h' => 1
                );
                $event_detail_response_json = taoh_apicall_get('events.event.get', $taoh_vals);
                $event_detail_response = taoh_get_array($event_detail_response_json);
                if ((!isset($event_detail_response['success']) || (isset($event_detail_response['success']) && !$event_detail_response['success']))) {
                    $response['error'] = 'event_not_found';
                    return $response;
                }
                $event_arr = $event_detail_response['output'];
                $events_data = $event_arr['conttoken'] ?? [];
                if (!empty($events_data['country_locked'])) {
                    $geo_enable = 1;
                }
            }

            if (isset($input_data['attendee_count'])) {
                $attendee_count = (int)$input_data['attendee_count'];
            } else {
                // Fetch attendee count
                $taoh_vals = array(
                    'token' => taoh_get_api_token(1, 1),
                    'mod' => 'events',
                    'eventtoken' => $eventtoken ?? '',
                    'country_name' => $country_name,
                    'cache_required' => 0
                );
                $response_count = taoh_apicall_get('events.rsvp.users.count', $taoh_vals);
                $att_response_arr = taoh_get_array($response_count, true);
                if ($att_response_arr['success'] && $att_response_arr['output'] != '') {
                    $attendee_count = $att_response_arr['output'];
                }
            }

            $key_key = $eventtoken;
        } else {
            $response['error'] = 'missing_event_token';
            return $response;
        }
    } elseif ($room_type === 'live') {
        $geo_enable = (int)($input_data['country_locked'] ?? 1);
        $dateHour = gmdate("YmdH");
        $title = $input_data['title'] ?? 'LiveNow';

        $key_key = $title . $dateHour;
    } else {
        $geo_enable = (int)($input_data['country_locked'] ?? 1);
        $this_week = date('W');

        $key_key = TAOH_SITE_ROOT_HASH . $this_week;
    }


    if ($geo_enable) {
        $key_key .= '_' . $country_code;
    }

    if ($attendee_count > 250) {
        $key_key .= '_' . $tzAbbreviation;
    }

    return [
        'success' => true,
        'roomslug' => hash('crc32', $key_key),
        'geo_enabled' => $geo_enable
    ];
}

function constructAndCreateRoomInfo($sess_user_info, $input_data = [], $room_type = 'club')
{
//    $sess_user_info = (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null);
//    if (empty($sess_user_info)) {
//        header('Location: ' . TAOH_SITE_URL_ROOT);
//        exit();
//    }
    $ptoken = $sess_user_info->ptoken ?? '';

    $geo_enable = 0;
    $breadcrumbs = [['label' => 'Home', 'url' => TAOH_SITE_URL_ROOT]];

    if ($room_type === 'event') {
        $eventtoken = $input_data['eventtoken'] ?? '';

        // Fetch RSVP details
        $taoh_vals = array(
            'token' => taoh_get_api_token(1, 1),
            'ops' => 'rsvp',
            'mod' => 'events',
            'eventtoken' => $eventtoken,
            'cache_required' => 0,
        );
        $rsvp_response_json = taoh_apicall_get('events.rsvp.get', $taoh_vals);
        $rsvp_arr = taoh_get_array($rsvp_response_json);
        if ((!isset($rsvp_arr['success']) || (isset($rsvp_arr['success']) && !$rsvp_arr['success']))) {
//            taoh_redirect(TAOH_SITE_URL_ROOT . '/' . TAOH_SITE_CURRENT_APP_SLUG . '/d/' . $contslug);
//            taoh_exit();
        }

        // Fetch event details
        $event_detail_cache_name = 'event_detail_' . $eventtoken;
        $taoh_vals = array(
            'token' => taoh_get_api_token(1, 1),
            'ops' => 'baseinfo',
            'mod' => 'events',
            'eventtoken' => $eventtoken,
            'cache_name' => $event_detail_cache_name,
            'ttl' => 2 * 60 * 60,
            //'cfcc2h' => 1
        );
        $event_detail_response_json = taoh_apicall_get('events.event.get', $taoh_vals);
        $event_detail_response = taoh_get_array($event_detail_response_json);
        if ((!isset($event_detail_response['success']) || (isset($event_detail_response['success']) && !$event_detail_response['success']))) {
//            taoh_redirect(TAOH_SITE_URL_ROOT . '/' . TAOH_SITE_CURRENT_APP_SLUG . '/d/' . $contslug);
//            taoh_exit();
        }
        $event_arr = $event_detail_response['output'];
        $events_data = $event_arr['conttoken'] ?? [];
        if (!empty($events_data['country_locked'])) {
            $geo_enable = 1;
        }
        $title = $events_data['title'] ?? '';
        $subtitle = $events_data['subtitle'] ?? '';
        $description = urldecode($events_data['description'] ?? '');
        $tags = array_filter(array_map('trim', explode(',', $events_data['event_tags'] ?? '')));
        $breadcrumbs = array_merge($breadcrumbs, [
            ['label' => 'Events', 'url' => TAOH_SITE_URL_ROOT . '/events'],
            ['label' => $title, 'url' => TAOH_SITE_URL_ROOT . '/events/chat/id/events/' . $eventtoken],
            ["label" => "Networking", "url" => null],
        ]);

        $event_default_channels_data = $events_data['event_channels'] ?? [];
        $contact_email = $events_data['contact_email'] ?? '';

        $search = $type = ''; // wrongly handled type nd search in get_event_MetaInfo fn cache_name so here used
        $cache_name = 'event_MetaInfo_' . $eventtoken . '_' . $type . '_' . $search;
        $taoh_vals = array(
            'mod' => 'events',
            'token' => taoh_get_api_token(1, 1),
            'eventtoken' => $eventtoken,
            'cfcc5h' => 1,
            'cache_name' => $cache_name,
        );
        $get_event_meta_info_response = taoh_apicall_get('events.content.get', $taoh_vals);
        $get_event_meta_info_arr = json_decode($get_event_meta_info_response, true);
        if (in_array($get_event_meta_info_arr['success'], [true, 'true']) && !empty($get_event_meta_info_arr['output'])) {
            $event_meta_info = $get_event_meta_info_arr['output'];

            $speakers = $event_meta_info['event_speaker'] ?? [];
            $exhibitors = $event_meta_info['event_exhibitor'] ?? [];
            $sponsors = $event_meta_info['event_sponsor'] ?? [];
        }

    } elseif ($room_type === 'live') {
        if (!empty($input_data['country_locked'])) {
            $geo_enable = 1;
        }
        $live_now_data = $input_data['live_now_data'] ?? '';

        $title = $live_now_data['title'] ?? TAOH_SITE_NAME_SLUG . ' Live Now';
        $subtitle = '';
        $description = $live_now_data['description'] ?? 'Join ' . TAOH_SITE_NAME_SLUG . ' community for Live Now';
        $tags = [TAOH_SITE_NAME_SLUG];
        $breadcrumbs = array_merge($breadcrumbs, [
            ["label" => "Live Now", "url" => null],
        ]);
        $contact_email = '';

    } else {
        $geo_enable = 1;

        $title = TAOH_SITE_NAME_SLUG . ' Community Club Networking Hour';
        $subtitle = '';
        $description = 'Join ' . TAOH_SITE_NAME_SLUG . ' community for Career Networking';
        $room_type = 'club';
        $tags = [TAOH_SITE_NAME_SLUG];
        $breadcrumbs = array_merge($breadcrumbs, [
            ["label" => "Networking", "url" => null],
        ]);
        $contact_email = '';
    }

    if (empty($input_data['roomslug'])) {
        return ['success' => false, 'error' => 'missing_room_slug'];
    }
    $keyslug = $input_data['roomslug'];

    $channels = [];

    if ($room_type === 'live') {
        if (isset($live_now_data) && !empty($live_now_data['channels'])) {
            $live_now_channels_data = (array)$live_now_data['channels'];
            $channels = array_merge($channels, constructDefaultChannelInfo($keyslug, $live_now_channels_data));
        }
    } else {
        $default_channels_data = [
            ['name' => 'general', 'description' => '📢 Announcements, updates, and all-community chatter.'],
            ['name' => 'intros', 'description' => '🎤 Name, role, and what you\'re exploring.'],
            ['name' => 'coffee-chats', 'description' => '💬 Spark a convo. Ask, share, or riff.'],
            ['name' => 'help-wanted', 'description' => '🙋 Need a job, collab, or support? Post it here.'],
            ['name' => 'industry-room-tech', 'description' => '💻 Tech talks, trends, and connections.'],
        ];
        $channels = array_merge($channels, constructDefaultChannelInfo($keyslug, $default_channels_data));
    }

    if ($room_type === 'event' && !empty($eventtoken)) {
        if (!empty($event_default_channels_data)) {
            $channels = array_merge($channels, constructDefaultEventChannelInfo($keyslug, $eventtoken, $event_default_channels_data));
        }

        if (isset($speakers) && !empty($speakers)) {
            $channels = array_merge($channels, constructEventSessionChannelInfo($eventtoken, $speakers));
        }

        if (isset($exhibitors) && !empty($exhibitors)) {
            $channels = array_merge($channels, constructEventExhibitorChannelInfo($eventtoken, $exhibitors));
        }
    }

    $final_room_title = $title ?? '';
    $final_room_subtitle = $subtitle ?? '';
    $final_room_description = $description ?? '';
    $final_room_type = $room_type ?? 'club';
    $final_room_tags = $tags ?? [];
    $owners_info = [
        [
            'ptoken' => $ptoken ?? ''
        ]
    ];
    $final_contact_email = $contact_email ?? '';
    $final_channels = $channels ?? [];

    $room_info_arr = array();
    $room_info_arr['room'] = [
        "keyslug" => $keyslug,
        "title" => $final_room_title,
        "subtitle" => $final_room_subtitle,
        "description" => $final_room_description,
        "room_type" => $final_room_type,
        "visibility" => "public",   // NI
        "timezone" => "America/New_York",
        "locale" => "en-US",    // NI
        "geo_enable" => $geo_enable ?? 0,
        "country_lock" => [],   // NI
        "start_at" => null,
        "end_at" => null,
        "max_concurrent_users" => 0,
        "tags" => $final_room_tags,
        "breadcrumbs" => $breadcrumbs,
        "logo_url" => null,
        "banner_url" => null,
        "square_image_url" => null,
        "theme" => ["brand_color" => null, "accent_color" => null], // NI
        "sso" => ["enabled" => false, "domain_restrictions" => []], // NI
        "dm_permissions" => "open", // NI
        "rate_limits" => [
            "create_channel_per_user_per_day" => 2,
            "post_per_minute" => 20,
            "dm_new_threads_per_hour" => 10,
        ],  // NI
        "analytics" => ["enabled" => false, "level" => "basic"],
        "announcements" => [
            "pre" => [
                "repeat" => "multiple",
                "remind_start_minutes" => 60,
                "remind_duration" => 15,
                "remind_duration_type" => "minutes",
                "message" => "Event starts in {15} {minutes}.",
            ],
            "during" => [
                "repeat" => "once",
                "remind_start_minutes" => 0,
                "remind_duration" => 5,
                "remind_duration_type" => "minutes",
                "message" => "Event started.",
            ],
            "post" => [
                "repeat" => "once",
                "remind_start_minutes" => 0,
                "remind_duration" => 5,
                "remind_duration_type" => "minutes",
                "message" =>
                    "Thanks for joining! Highlights & replays here: <link>",
            ],
        ],
        "owners" => $owners_info,
        "managers" => [],
        "contact_email" => $final_contact_email,
        'channels' => $final_channels ?? []
    ];

    if (($room_info_arr['room']['room_type'] ?? '') === 'event') {
        $room_info_arr['room']['event'] = [
            "eventtoken" => $eventtoken ?? '',
            "ticket_types" => $events_data['ticket_types'] ?? [],
            "exhibitors" => $exhibitors ?? [],
            "sponsors" => $sponsors ?? [],
            "ptoken" => $events_data['ptoken'] ?? '',
            "event_organizer_ptokens" => $events_data['event_organizer_ptokens'] ?? '',
            "streaming_link" => $events_data['live_link'] ?? '',
            "msg_from_owner" => $events_data['msg_from_owner'] ?? '',
        ];
    }

    $room_info_arr['profiles'] = [
        "attendee" => [
            "label" => "Attendee",
            "eligibility" => [
                "require_ticket" => true,
                "whitelist_domains" => [],
                "invite_only" => false,
            ],
            "can" => [
                "enter_room" => true,
                "speak_in_voice" => true,
                "post_in_public" => true,
                "dm" => "mutual_opt_in",
                "create_channel" => false,
                "host_video_meet" => false,
                "pin_messages" => false,
                "join_speed_networking" => true,
                "join_watch_party" => true,
                "share_screen" => false,
                "upload_files" => true,
                "react_with_emojis" => true,
                "delete_messages" => true,
            ],
        ],
        "speaker" => [
            "label" => "Speaker",
            "eligibility" => [
                "require_ticket" => true,
                "whitelist_domains" => [],
                "invite_only" => false,
            ],
            "can" => [
                "enter_room" => true,
                "speak_in_voice" => true,
                "post_in_public" => true,
                "dm" => "mutual_opt_in",
                "create_channel" => false,
                "host_video_meet" => false,
                "pin_messages" => false,
                "join_speed_networking" => true,
                "join_watch_party" => true,
                "share_screen" => false,
                "upload_files" => true,
                "react_with_emojis" => true,
                "delete_messages" => true,
            ],
        ],
        "sponsor" => [
            "label" => "Sponsor",
            "packages" => [
                [
                    "tier" => "gold",
                    "eligibility" => [
                        "require_ticket" => true,
                        "whitelist_domains" => [],
                        "invite_only" => false,
                    ],
                    "can" => [
                        "enter_room" => true,
                        "speak_in_voice" => true,
                        "post_in_public" => true,
                        "dm" => "mutual_opt_in",
                        "create_channel" => false,
                        "host_video_meet" => false,
                        "pin_messages" => false,
                        "join_speed_networking" => true,
                        "join_watch_party" => true,
                        "share_screen" => false,
                        "upload_files" => true,
                        "react_with_emojis" => true,
                        "delete_messages" => true,
                    ],
                ],
                [
                    "tier" => "silver",
                    "eligibility" => [
                        "require_ticket" => true,
                        "whitelist_domains" => [],
                        "invite_only" => false,
                    ],
                    "can" => [
                        "enter_room" => true,
                        "speak_in_voice" => true,
                        "post_in_public" => true,
                        "dm" => "mutual_opt_in",
                        "create_channel" => false,
                        "host_video_meet" => false,
                        "pin_messages" => false,
                        "join_speed_networking" => true,
                        "join_watch_party" => true,
                        "share_screen" => false,
                        "upload_files" => true,
                        "react_with_emojis" => true,
                        "delete_messages" => true,
                        "add_channel_banner" => true,
                    ],
                ],
            ],
        ],
        "recruiter" => [
            "label" => "Recruiter",
            "eligibility" => [
                "require_ticket" => true,
                "whitelist_domains" => [],
                "invite_only" => false,
            ],
            "can" => [
                "enter_room" => true,
                "speak_in_voice" => true,
                "post_in_public" => true,
                "dm" => "mutual_opt_in",
                "create_channel" => false,
                "host_video_meet" => false,
                "pin_messages" => false,
                "join_speed_networking" => true,
                "join_watch_party" => true,
                "share_screen" => false,
                "upload_files" => true,
                "react_with_emojis" => true,
                "delete_messages" => true,
                "open_job_threads" => true,
                "request_cv" => true,
            ],
        ],
        "moderator" => [
            "label" => "Moderator",
            "eligibility" => [
                "require_ticket" => true,
                "whitelist_domains" => [],
                "invite_only" => false,
            ],
            "can" => [
                "enter_room" => true,
                "speak_in_voice" => true,
                "post_in_public" => true,
                "dm" => "mutual_opt_in",
                "create_channel" => false,
                "host_video_meet" => false,
                "pin_messages" => false,
                "join_speed_networking" => true,
                "join_watch_party" => true,
                "share_screen" => false,
                "upload_files" => true,
                "react_with_emojis" => true,
                "delete_messages" => true,
            ],
        ],
        "organizer" => [
            "label" => "Organizer",
            "eligibility" => ["full_access" => true],
            "can" => "full_access",
        ],
    ];

    $create_room_response = createRoomInfo($room_info_arr, $ptoken);
    if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
        $created_room_info = $create_room_response['output'];

        $create_bulk_channels_response = createBulkRoomInfoChannels($created_room_info, $ptoken);
    }

    return $create_room_response;
}


//$sess_user_info = (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null);
//constructAndCreateRoomInfo($sess_user_info, ['eventtoken' => '30wby3cyid0f1o3']);

//header('Content-Type: application/json');
//echo json_encode($create_room_response);
//echo $create_bulk_channels_response ?? 'create_bulk_channels_response failed';
//exit();
