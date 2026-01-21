<?php

final class NtwAdapterEvents
{
    public function generateChannelId($channel_slug_data): string
    {
        asort($channel_slug_data);
        return generateSecureSlug(implode('_', $channel_slug_data), 16);
    }

    public function constructDefaultChannelInfo($room_slug, $channels_input_data): array
    {
        $channels = [];
        foreach ($channels_input_data as $value) {
            if (empty($value['name'])) {
                continue;
            }
            $channel_name = $value['name'];
            $channel_description = taoh_title_desc_encode($value['description'] ?? '');
            $channel_slug_data = [$room_slug, $channel_name];
            $channel_id = $this->generateChannelId($channel_slug_data);
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

    public function constructDefaultEventChannelInfo($room_slug, $eventtoken, $channels_input_data): array
    {
        $channels = [];
        foreach ($channels_input_data as $value) {
            if (empty($value['channel_name'])) {
                continue;
            }
            $channel_name = $value['channel_name'];
            $channel_description = taoh_title_desc_encode($value['channel_desc'] ?? '');
            $channel_slug_data = [$room_slug, $channel_name];
            $channel_id = $this->generateChannelId($channel_slug_data);
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
                'show_default' => 1
            ];
        }

        return $channels;
    }

    public function constructEventSessionChannelInfo($eventtoken, $channels_input_data): array
    {
        $channels = [];
        foreach ($channels_input_data as $value) {
            if (empty($value['spk_title'])) {
                continue;
            }
            $speakerId = $value['ID'];
            $channel_name = $value['spk_title'];
            $channel_description = taoh_title_desc_encode($value['spk_desc']);
            $spk_logo_image = $value['spk_logo_image'] ?? '';
            if (empty($spk_logo_image)) {
                //$spk_logo_image = TAOH_SITE_URL_ROOT . '/assets/images/no-room.png';
                $spk_logo_image = TAOH_CDN_PREFIX . "/images/igcache/" . $value['spk_title'] . "/630_630/blog.jpg";
            }

            $channel_slug_data = [$eventtoken, 'session', $speakerId];
            $channel_id = $this->generateChannelId($channel_slug_data);
            $channel_type = defined('TAOH_CHANNEL_SESSION') ? TAOH_CHANNEL_SESSION : 7;
            $channel_data = [
                'name' => $channel_name,
                'description' => $channel_description,
                'ptoken' => '',
                'source' => 'admin',
                'speaker_hall' => $value['spk_hall'] ?? '',
                'speaker_logo' => $spk_logo_image,
                'streaming_link' => $value['spk_streaming_link'] ?? '',
                'speaker_id' => $value['ID'] ?? '',
                'eventtoken' => $eventtoken,
                'spk_datefrom' => $value['spk_datefrom'] ?? '',
                'spk_dateto' => $value['spk_dateto'] ?? '',
                'spk_timezoneSelect' => $value['spk_timezoneSelect'] ?? '',
                'spk_state' => $value['spk_state'] ?? '',
            ];

            $channels[] = [
                'global_slug' => $eventtoken,
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

    public function constructEventExhibitorChannelInfo($eventtoken, $channels_input_data): array
    {
        $channels = [];
        foreach ($channels_input_data as $value) {
            if (empty($value['exh_session_title'])) {
                continue;
            }
            $exhibitorId = $value['ID'];
            $channel_name = $value['exh_session_title'];
            $channel_description = taoh_title_desc_encode($value['exh_description']);
            $exh_logo_image = $value['exh_logo'] ?? '';
            if (empty($exh_logo_image)) {
                $exh_logo_image = TAOH_CDN_PREFIX . "/images/ig/" . urlencode($channel_name) . "/uncategorized/1.png";
            }

            $channel_slug_data = [$eventtoken, 'exhibitor', $exhibitorId];
            $channel_id = $this->generateChannelId($channel_slug_data);
            $channel_type = defined('TAOH_CHANNEL_EXHIBITOR') ? TAOH_CHANNEL_EXHIBITOR : 2;
            $channel_data = [
                'name' => $channel_name,
                'description' => $channel_description,
                'ptoken' => '',
                'source' => 'admin',
                'exhibitor_hall' => $value['exh_hall'] ?? '',
                'exhibitor_raffles' => $value['exh_raffles'] ?? '',
                'exhibitor_logo' => $exh_logo_image,
                'streaming_link' => $value['exh_streaming_link'] ?? '',
                'exhibitor_id' => $value['ID'] ?? '',
                'eventtoken' => $eventtoken,
                'exh_state' => $value['exh_state'] ?? '',
            ];

            $channels[] = [
                'global_slug' => $eventtoken,
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


    public function createBulkRoomInfoChannels($room_info, $my_ptoken)
    {
        $room_data = $room_info['room'] ?? [];

        $roomslug = $room_data['keyslug'] ?? null;
        $room_type = $room_data['room_type'] ?? '';
        $event_token = $room_data['eventtoken'] ?? null;
        $keyword = ($room_type === 'event' && !empty($event_token)) ? $event_token : 'club';
        $room_info_channels = $room_data['channels'] ?? [];

        if (!$roomslug || empty($room_info_channels)) {
            return json_encode(['success' => false, 'error' => 'missing_required_fields']);
        }

        $channels_to_create = [];
        foreach ($room_info_channels as $channelConfig) {
            $channel_data = $channelConfig['channel_data'] ?? [];
            $channel_passcode = $channelConfig['channel_passcode'] ?? '';

            $channels_create_info_arr = [
                'roomSlug' => $roomslug,
                'keyword' => $keyword,
                'channelId' => $channelConfig['channel_id'] ?? '',
                'channelType' => $channelConfig['channel_type'] ?? '',
                'channelData' => $channel_data,
                'channelMembers' => $channelConfig['channel_members'] ?? [],
                'channelTicketType' => $channelConfig['channel_ticket_type'] ?? '',
                'visibility' => $channelConfig['channel_visibility'] ?? 'public',
                'showDefault' => $channelConfig['show_default'] ?? 0,
            ];

            if (!empty($channel_passcode)) {
                $channels_create_info_arr['channelEncryptPasscode'] = openEncrypt($channel_passcode);
            }

            if (isset($channelConfig['global_slug'])) {
                $channels_create_info_arr['globalSlug'] = $channelConfig['global_slug'];
            }

            $channels_to_create[] = $channels_create_info_arr;
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

    function updateChannelInfo($roomslug, $keyword, $key, $channel_info)
    {
        $channel_id = $channel_info['channel_id'] ?? '';
        $channel_type = $channel_info['channel_type'] ?? 1;
        $channel_data = $channel_info['channel_data'] ?? '';
        $channel_passcode = $channel_info['channel_passcode'] ?? '';
        $channel_ticket_type = $channel_info['channel_ticket_type'] ?? '';

        if (!$roomslug || !$keyword || empty($channel_id) || empty($channel_data)) {
            return json_encode(['success' => false, 'error' => 'missing_required_fields']);
        }

        $taoh_vals = array(
            "ops" => 'channel',
            'action' => 'update_channel',
            'roomSlug' => $roomslug,
            'keyword' => $keyword,
            'channelId' => $channel_id,
            'channelType' => $channel_type,
            'channelData' => $channel_data,
            'channelMembers' => $channel_info['channel_members'] ?? [],
            'visibility' => $channel_info['channel_visibility'] ?? 'public',
            'showDefault' => $channel_info['show_default'] ?? 0,
            'code' => TAOH_OPS_CODE,
            'key' => $key
        );

        if (!empty($channel_passcode)) {
            $taoh_vals['channelEncryptPasscode'] = openEncrypt($channel_passcode);
        }

        if (!empty($channel_ticket_type)) {
            $taoh_vals['channelTicketType'] = $channel_ticket_type;
        }

        if (isset($channel_info['global_slug'])) {
            $taoh_vals['globalSlug'] = $channel_info['global_slug'];
        }

        //$taoh_vals['debug'] = 1;
        //echo taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);exit();

        return taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    }

    function deleteChannel($roomslug, $keyword, $key, $channel_info)
    {
        $channel_id = $channel_info['channel_id'] ?? '';
        $channel_type = $channel_info['channel_type'] ?? 1;

        if (!$roomslug || !$keyword || empty($channel_id)) {
            return json_encode(['success' => false, 'error' => 'missing_required_fields']);
        }

        $taoh_vals = array(
            "ops" => 'channel',
            'action' => 'delete_channel',
            'roomSlug' => $roomslug,
            'keyword' => $keyword,
            'channelId' => $channel_id,
            'channelType' => $channel_type,
            'code' => TAOH_OPS_CODE,
            'key' => $key
        );

        if (isset($channel_info['global_slug'])) {
            $taoh_vals['globalSlug'] = $channel_info['global_slug'];
        }

    //$taoh_vals['debug'] = 1;
    //echo taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);exit();

        return taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    }

    public function generateRoomSlug($input_data = []): array
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

        $eventtoken = $input_data['eventtoken'] ?? '';

        if (empty($eventtoken)) {
            $response['error'] = 'missing_event_token';
            return $response;
        }

        if (isset($input_data['country_locked'])) {
            if ($input_data['country_locked'] == 1) {
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

        if ($geo_enable) {
            $key_key .= '_' . $country_code;
        }

        if ($attendee_count > 500) {
            $key_key .= '_' . $tzAbbreviation;
        }

        return [
            'success' => true,
            'roomslug' => hash('crc32', $key_key),
            'geo_enabled' => $geo_enable
        ];
    }

    public function constructAndCreateRoomInfo($sess_user_info, $input_data = [])
    {
        $ptoken = $sess_user_info->ptoken ?? '';

        $geo_enable = 0;
        $breadcrumbs = [['label' => 'Home', 'url' => TAOH_SITE_URL_ROOT]];
        $room_type = 'event';

        $eventtoken = $input_data['eventtoken'] ?? '';

        // Fetch RSVP details
//        $taoh_vals = array(
//            'token' => taoh_get_api_token(1, 1),
//            'ops' => 'rsvp',
//            'mod' => 'events',
//            'eventtoken' => $eventtoken,
//            'cache_required' => 0,
//        );
//        $rsvp_response_json = taoh_apicall_get('events.rsvp.get', $taoh_vals);
//        $rsvp_arr = taoh_get_array($rsvp_response_json);
//        if ((!isset($rsvp_arr['success']) || (isset($rsvp_arr['success']) && !$rsvp_arr['success']))) {
////            taoh_redirect(TAOH_SITE_URL_ROOT . '/' . TAOH_SITE_CURRENT_APP_SLUG . '/d/' . $contslug);
////            taoh_exit();
//        }

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
            return ['success' => false, 'error' => 'event_not_found'];
        }
        $event_arr = $event_detail_response['output'];
        $events_data = $event_arr['conttoken'] ?? [];

        //echo '===<pre>';print_r($events_data);echo '</pre>';die();

        if (($events_data['country_locked'] ?? 0) == 1) {
            $geo_enable = 1;
        }
        $title = $events_data['title'] ?? '';
        $subtitle = $events_data['subtitle'] ?? '';
        $org_video_message_link = $events_data['org_video_message_link'] ?? '';
        $description = taoh_title_desc_encode($events_data['description'] ?? '');
        $tags = array_filter(array_map('trim', explode(',', $events_data['event_tags'] ?? '')));
        $breadcrumbs = array_merge($breadcrumbs, [
            ['label' => 'Events', 'url' => TAOH_SITE_URL_ROOT . '/events'],
            ['label' => $title, 'url' => TAOH_SITE_URL_ROOT . '/events/chat/id/events/' . $eventtoken],
            ["label" => "Networking", "url" => null],
        ]);

        $home_link = TAOH_SITE_URL_ROOT . '/events/chat/id/events/' . $eventtoken;
        $event_default_channels_data = $events_data['event_channels'] ?? [];
        $contact_email = $events_data['contact_email'] ?? '';
        $show_video_conv_btn = ($events_data['disable_video_conversation'] ?? '') != '1' ? 1 : 0;
        $allow_auto_manage = ($events_data['auto_manage'] ?? '') == '1' ? 1 : 0;

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

        if (empty($input_data['roomslug'])) {
            return ['success' => false, 'error' => 'missing_room_slug'];
        }
        $keyslug = $input_data['roomslug'];

        $browse_channel_tabs = [
            ['slug' => 'allchannels', 'name' => 'All Channels', 'channel_types_to_show' => [TAOH_CHANNEL_DEFAULT, TAOH_CHANNEL_EXHIBITOR, TAOH_CHANNEL_SESSION], 'eventtoken' => $eventtoken]
        ];

        $welcome_messages = [];

        if (!empty($events_data['welcome_messages'] ?? '')) {
            $welcome_messages = $events_data['welcome_messages'];
        } else {
            $default_welcome_messages = defined('TAOH_NTW_WELCOME_MESSAGES') ? TAOH_NTW_WELCOME_MESSAGES : [];
            if (!empty($default_welcome_messages) && is_array($default_welcome_messages)) {
                $welcome_messages = $default_welcome_messages;
            }
        }

        $channels = [];

        if (!empty($event_default_channels_data)) {
            $channels = array_merge($channels, $this->constructDefaultEventChannelInfo($keyslug, $eventtoken, $event_default_channels_data));
        } else {
            $default_channels_data = defined('TAOH_NTW_DEFAULT_CHANNELS') ? TAOH_NTW_DEFAULT_CHANNELS : [];
            $channels = array_merge($channels, $this->constructDefaultChannelInfo($keyslug, $default_channels_data));
        }

        if (isset($speakers) && !empty($speakers)) {
            $channels = array_merge($channels, $this->constructEventSessionChannelInfo($eventtoken, $speakers));
        }

        if (isset($exhibitors) && !empty($exhibitors)) {
            $channels = array_merge($channels, $this->constructEventExhibitorChannelInfo($eventtoken, $exhibitors));
        }

        $browse_channel_tabs[] = ['slug' => 'sessions', 'name' => 'Sessions', 'channel_types_to_show' => [TAOH_CHANNEL_SESSION], 'eventtoken' => $eventtoken];
        $browse_channel_tabs[] = ['slug' => 'exhibitors', 'name' => 'Exhibitors', 'channel_types_to_show' => [TAOH_CHANNEL_EXHIBITOR], 'eventtoken' => $eventtoken];

        $final_room_title = $title ?? '';
        $final_room_subtitle = $subtitle ?? '';
        $final_room_description = $description ?? '';
        $final_room_type = $room_type;
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
            "keyword" => $eventtoken,
            "global_slug" => $eventtoken ?? '',
            "title" => $final_room_title,
            "subtitle" => $final_room_subtitle,
            "org_video_message_link" => $org_video_message_link,
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
            "home_link" => $home_link,
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
            "channels" => $final_channels ?? [],
            "browse_channel_tabs" => $browse_channel_tabs,
            "disable_video_conversation" => $show_video_conv_btn,
            "auto_manage" => $allow_auto_manage,
            "msg_from_owner" => taoh_title_desc_encode($events_data['msg_from_owner'] ?? ''),
            "ticket_types" => $events_data['ticket_types'] ?? [],
            //"exhibitors" => $exhibitors ?? [],
            //"sponsors" => $sponsors ?? [],
            "eventtoken" => $eventtoken ?? '',
            "ptoken" => $events_data['ptoken'] ?? '',
            "organizer_ptokens" => $events_data['event_organizer_ptokens'] ?? '',
            "streaming_link" => $events_data['live_link'] ?? '',
            "welcome_messages" => $welcome_messages ?? '',
        ];

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

        $create_room_response = createRoomInfo($room_info_arr, $eventtoken, $ptoken);

        return $create_room_response;
    }

    public function updateRoomInfo($sess_user_info, $input_data = [])
    {
        $ptoken = $sess_user_info->ptoken ?? '';

        $geo_enable = 0;
        $breadcrumbs = [['label' => 'Home', 'url' => TAOH_SITE_URL_ROOT]];
        $room_type = 'event';

        $update_types = $input_data['update_types'] ?? [];
        $eventtoken = $input_data['eventtoken'] ?? '';

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
            return ['success' => false, 'error' => 'event_not_found'];
        }
        $event_arr = $event_detail_response['output'];
        $events_data = $event_arr['conttoken'] ?? [];

        

        if (!empty($events_data['country_locked'])) {
            $geo_enable = 1;
        }
        $title = $events_data['title'] ?? '';
        $subtitle = $events_data['subtitle'] ?? '';
        $description = taoh_title_desc_encode($events_data['description'] ?? '');
        $tags = array_filter(array_map('trim', explode(',', $events_data['event_tags'] ?? '')));
        $breadcrumbs = array_merge($breadcrumbs, [
            ['label' => 'Events', 'url' => TAOH_SITE_URL_ROOT . '/events'],
            ['label' => $title, 'url' => TAOH_SITE_URL_ROOT . '/events/chat/id/events/' . $eventtoken],
            ["label" => "Networking", "url" => null],
        ]);
        $home_link = TAOH_SITE_URL_ROOT . '/events/chat/id/events/' . $eventtoken;
        $event_default_channels_data = $events_data['event_channels'] ?? [];
        $contact_email = $events_data['contact_email'] ?? '';
        $show_video_conv_btn = ($events_data['disable_video_conversation'] ?? '') != '1' ? 1 : 0;
        $allow_auto_manage = ($events_data['auto_manage'] ?? '') == '1' ? 1 : 0;

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

        if (empty($input_data['roomslug'])) {
            return ['success' => false, 'error' => 'missing_room_slug'];
        }
        $keyslug = $input_data['roomslug'];

        $browse_channel_tabs = [
            ['slug' => 'allchannels', 'name' => 'All Channels', 'channel_types_to_show' => [TAOH_CHANNEL_DEFAULT, TAOH_CHANNEL_EXHIBITOR, TAOH_CHANNEL_SESSION], 'eventtoken' => $eventtoken]
        ];

        $channels = [];

        if (!empty($event_default_channels_data)) {
            $channels = array_merge($channels, $this->constructDefaultEventChannelInfo($keyslug, $eventtoken, $event_default_channels_data));
        } else {
            $default_channels_data = defined('TAOH_NTW_DEFAULT_CHANNELS') ? TAOH_NTW_DEFAULT_CHANNELS : [];
            $channels = array_merge($channels, $this->constructDefaultChannelInfo($keyslug, $default_channels_data));
        }

        if (isset($speakers) && !empty($speakers)) {
            $channels = array_merge($channels, $this->constructEventSessionChannelInfo($eventtoken, $speakers));
            $browse_channel_tabs[] = ['slug' => 'sessions', 'name' => 'Sessions', 'channel_types_to_show' => [TAOH_CHANNEL_SESSION], 'eventtoken' => $eventtoken];
        }

        if (isset($exhibitors) && !empty($exhibitors)) {
            $channels = array_merge($channels, $this->constructEventExhibitorChannelInfo($eventtoken, $exhibitors));
            $browse_channel_tabs[] = ['slug' => 'exhibitors', 'name' => 'Exhibitors', 'channel_types_to_show' => [TAOH_CHANNEL_EXHIBITOR], 'eventtoken' => $eventtoken];
        }

        $final_room_title = $title ?? '';
        $final_room_subtitle = $subtitle ?? '';
        $final_room_description = $description ?? '';
        $final_room_type = $room_type;
        $final_room_tags = $tags ?? [];
        $owners_info = [
            [
                'ptoken' => $ptoken ?? ''
            ]
        ];
        $final_contact_email = $contact_email ?? '';
        $final_channels = $channels ?? [];


        // echo '<pre>-------pn update-------'; print_r($final_channels); die;

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
            "home_link" => $home_link,
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
            "channels" => $final_channels ?? [],
            "browse_channel_tabs" => $browse_channel_tabs,
            "disable_video_conversation" => $show_video_conv_btn,
            "auto_manage" => $allow_auto_manage,
            "msg_from_owner" => $events_data['msg_from_owner'] ?? '',
            "ticket_types" => $events_data['ticket_types'] ?? [],
            //"exhibitors" => $exhibitors ?? [],
            //"sponsors" => $sponsors ?? [],
            "eventtoken" => $eventtoken ?? '',
            "ptoken" => $events_data['ptoken'] ?? '',
            "organizer_ptokens" => $events_data['event_organizer_ptokens'] ?? '',
            "streaming_link" => $events_data['live_link'] ?? '',
            "speednetworking" => 0,
        ];

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

        $update_room_response = updateRoomInfo($room_info_arr, $ptoken);
        if (in_array($update_room_response['success'], [true, 'true']) && !empty($update_room_response['output'])) {
            $created_room_info = $update_room_response['output'];

            $create_bulk_channels_response = $this->createBulkRoomInfoChannels($created_room_info, $ptoken);
        }

        return $update_room_response;
    }
}
