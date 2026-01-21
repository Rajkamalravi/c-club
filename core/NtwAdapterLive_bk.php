<?php

final class NtwAdapterLive {
    public function constructDefaultChannelInfo($room_slug, $channels_input_data): array
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

        $geo_enable = (int)($input_data['country_locked'] ?? 1);
        $attendee_count = 0;

        $dateHour = gmdate("YmdH");
        $title = $input_data['title'] ?? 'LiveNow';

        $key_key = $title . $dateHour;

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

    public function constructAndCreateRoomInfo($sess_user_info, $input_data = [])
    {
        $ptoken = $sess_user_info->ptoken ?? '';

        $geo_enable = (int)($input_data['country_locked'] ?? 0);
        $breadcrumbs = [
            ['label' => 'Home', 'url' => TAOH_SITE_URL_ROOT],
            ["label" => "Networking", "url" => null]
        ];
        $room_type = 'live';

        $live_now_data = $input_data['live_now_data'] ?? '';

        $title = $live_now_data['title'] ?? TAOH_SITE_NAME_SLUG . ' Live Now';
        $subtitle = '';
        $description = $live_now_data['description'] ?? 'Join ' . TAOH_SITE_NAME_SLUG . ' community for Live Now';
        $tags = [TAOH_SITE_NAME_SLUG];
        $breadcrumbs = array_merge($breadcrumbs, [
            ["label" => "Live Now", "url" => null],
        ]);
        $contact_email = '';
        $show_video_conv_btn = 0;
        $allow_auto_manage = 0;

        if (empty($input_data['roomslug'])) {
            return ['success' => false, 'error' => 'missing_room_slug'];
        }
        $keyslug = $input_data['roomslug'];

        $welcome_messages = [];
        $default_welcome_messages = defined('TAOH_NTW_WELCOME_MESSAGES') ? TAOH_NTW_WELCOME_MESSAGES : [];
        if (!empty($default_welcome_messages) && is_array($default_welcome_messages)) {
            $welcome_messages = $default_welcome_messages;
        }

        $channels = [];

        if (isset($live_now_data) && !empty($live_now_data['channels'])) {
            $live_now_channels_data = (array)$live_now_data['channels'];
            $channels = array_merge($channels, $this->constructDefaultChannelInfo($keyslug, $live_now_channels_data));
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
            "channels" => $final_channels ?? [],
            "browse_channel_tabs" => [
                ['slug' => 'allchannels', 'name' => 'All Channels', 'channel_types_to_show' => [TAOH_CHANNEL_DEFAULT]]
            ],
            "disable_video_conversation" => $show_video_conv_btn,
            "auto_manage" => $allow_auto_manage,
            "msg_from_owner" => '',
            "organizer_ptokens" => [],
            "streaming_link" => '',
            "welcome_messages" => $welcome_messages,
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

        $create_room_response = createRoomInfo($room_info_arr, $ptoken);
        if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
            $created_room_info = $create_room_response['output'];

            $create_bulk_channels_response = $this->createBulkRoomInfoChannels($created_room_info, $ptoken);
        }

        return $create_room_response;
    }
}
