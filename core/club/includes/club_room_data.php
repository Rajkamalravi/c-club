<?php
if (!defined('TAOH_CURR_APP_IMAGE_SQUARE')) {
    define('TAOH_CURR_APP_IMAGE_SQUARE', TAOH_SITE_URL_ROOT . '/assets/images/nerwork_app_sq.png');
}
if (!defined('TAOH_CURR_APP_IMAGE')) {
    define('TAOH_CURR_APP_IMAGE', TAOH_SITE_URL_ROOT . '/assets/images/nerwork_app.png');
}

function get_networking_keyword_room_data($data)
{
    $keyslug = $data['keyslug'];
    $title = $data['title'];
    $description = $data['description'];
    $keyword_value = $data['keyword_value'];
    $current_app_slug = $data['current_app_slug'] ?? TAOH_SITE_CURRENT_APP_SLUG;
    $url_slug = taoh_slugify($title . '-' . $keyword_value) . '-' . $keyslug;

    $room_data_arr = array(
        'keyslug' => $keyslug,
        'app' => 'keyword',
        'club' => array(
            'title' => $title . ' - ' . $keyword_value,
            'description' => $description,
            'short' => 'Connect with ' . TAOH_SITE_NAME_SLUG . ' professionals from all over the ' . TAOH_SITE_NAME_SLUG . ' to advance your career. We meet 4pm - 6pm to connect and network.',
            'image' => TAOH_SITE_LOGO,
            'square_image' => TAOH_SITE_FAVICON,
            'links' => array(
                'club' => '/' . $current_app_slug . '/room/' . $url_slug,
                'networking' => '/' . $current_app_slug . '/club/local',
                'detail' => '/' . $current_app_slug . '/club/local',
            ),
            'profile_types' => array(
                array(
                    'slug' => 'employer',
                    'title' => 'Employer',
                ),
                array(
                    'slug' => 'professional',
                    'title' => 'Professional',
                ),
                array(
                    'slug' => 'provider',
                    'title' => 'Provider',
                ),
            ),
            'skill' => '',
            'company' => '',
            'roles' => '',
            'sponsors' => array(
                array(
                    'title' => 'NoWorkerLeftBehind',
                    'sub_title' => 'Let us all work together and help each other succeed.',
                    'image' => 'https://noworkerleftbehind.org/wp-content/uploads/2022/09/cropped-cropped-nwlb_sq-270x270.png',
                    'link' => 'https://noworkerleftbehind.org/',
                ),
                array(
                    'title' => 'TAO.ai',
                    'sub_title' => 'TAO: Through technology, make professional connectios and career growth universally accessible.',
                    'image' => 'https://tao.ai/tao/innovative/img/TAO_AI_Logo_icon_orng.png',
                    'link' => 'https://tao.ai',
                ),
            ),
            'breadcrumbs' => array(
                array(
                    'title' => 'Home',
                    'link' => '',
                ),
                array(
                    'title' => ucfirst($current_app_slug),
                    'link' => '/' . $current_app_slug,
                ),
                array(
                    'title' => $title . ' - ' . $keyword_value,
                    'link' => '/' . $current_app_slug . '/room/' . $url_slug,
                ),
            ),
            'live' => '',
            'geo_enable' => $data['geo_enable'],
            'owner_enable' => false,
            'owner' => '',
            'full_location' => '',
            'coordinates' => '',
            'geohash' => '',
            'longitude' => '',
            'latitude' => '',
            'faq' => array(
                array(
                    'title' => 'What is ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour?',
                    'description' => 'Introducing ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour—a special window from 4 p.m. to 6 p.m. local time, designed to supercharge your professional networking. Use this time to connect with other professionals, engage in meaningful conversations, and unlock opportunities for collaboration. Do not miss this chance to expand your network and accelerate your career! This networking room is available 24x7 for anyone to join and network with other professionals.',
                ),
                array(
                    'title' => 'Is it free to join ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour?',
                    'description' => 'Yes, it is free to join ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour. You can join the networking room anytime between 4 p.m. to 6 p.m. in your local time zone.',
                ),
                array(
                    'title' => 'What is I am not visiting this page 4 p.m. to 6 p.m. in my local time zone?',
                    'description' => 'You can still join the networking room anytime as long as you could find other people to connect with. You could always expand your search radius to see other people in your region.',
                ),
                array(
                    'title' => 'What is this community for?',
                    'description' => 'This community is for professionals from all over the world who are looking for new jobs or to advance their careers.',
                ),
                array(
                    'title' => 'Who is this community for?',
                    'description' => 'This community is for anyone who is serious about their job search. We welcome people of all experience levels, industries, and backgrounds.',
                ),
                array(
                    'title' => 'What are the benefits of joining this community?',
                    'description' => 'The benefits of joining this community include:<br />
                            Access to a network of professionals who can offer support and advice<br />
                            Information about new job opportunities<br />
                            Tips and advice on resume writing, cover letter writing, interviewing, and salary negotiation<br />
                            A sense of community and support',
                ),
                array(
                    'title' => 'How do I join this community?',
                    'description' => 'To join this community, simply create an account and join the group.',
                ),
                array(
                    'title' => 'What are the rules of this community?',
                    'description' => 'The rules of this community are simple: be respectful of others and share helpful information.',
                ),
            ),
        )
    );

    return $room_data_arr;
}

function get_networking_directory_room_data($data)
{
    $keyslug = $data['keyslug'];
    $title = $data['title'];
    $description = $data['description'];
    $current_app_slug = $data['current_app_slug'] ?? TAOH_SITE_CURRENT_APP_SLUG;
    $url_slug = taoh_slugify($title) . '-' . $keyslug;

    $room_data_arr = array(
        'keyslug' => $keyslug,
        'app' => 'directory',
        'club' => array(
            'title' => $title,
            'description' => $description,
            'short' => 'Connect with ' . TAOH_SITE_NAME_SLUG . ' professionals from all over the ' . TAOH_SITE_NAME_SLUG . ' to advance your career. We meet 4pm - 6pm to connect and network.',
            'image' => TAOH_SITE_LOGO,
            'square_image' => TAOH_SITE_FAVICON,
            'links' => array(
                'club' => '/' . $current_app_slug . '/room/' . $url_slug,
                'networking' => '/' . $current_app_slug . '/club/local',
                'detail' => '/' . $current_app_slug . '/club/local',
            ),
            'profile_types' => array(
                array(
                    'slug' => 'employer',
                    'title' => 'Employer',
                ),
                array(
                    'slug' => 'professional',
                    'title' => 'Professional',
                ),
                array(
                    'slug' => 'provider',
                    'title' => 'Provider',
                ),
            ),
            'skill' => '',
            'company' => '',
            'roles' => '',
            'sponsors' => array(
                array(
                    'title' => 'NoWorkerLeftBehind',
                    'sub_title' => 'Let us all work together and help each other succeed.',
                    'image' => 'https://noworkerleftbehind.org/wp-content/uploads/2022/09/cropped-cropped-nwlb_sq-270x270.png',
                    'link' => 'https://noworkerleftbehind.org/',
                ),
                array(
                    'title' => 'TAO.ai',
                    'sub_title' => 'TAO: Through technology, make professional connectios and career growth universally accessible.',
                    'image' => 'https://tao.ai/tao/innovative/img/TAO_AI_Logo_icon_orng.png',
                    'link' => 'https://tao.ai',
                ),
            ),
            'breadcrumbs' => array(
                array(
                    'title' => 'Home',
                    'link' => '',
                ),
                array(
                    'title' => ucfirst($current_app_slug),
                    'link' => '/' . $current_app_slug,
                ),
                array(
                    'title' => $title,
                    'link' => '/' . $current_app_slug . '/room/' . $url_slug,
                ),
            ),
            'live' => '',
            'geo_enable' => $data['geo_enable'],
            'owner_enable' => false,
            'owner' => '',
            'full_location' => '',
            'coordinates' => '',
            'geohash' => '',
            'longitude' => '',
            'latitude' => '',
            'faq' => array(
                array(
                    'title' => 'What is ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour?',
                    'description' => 'Introducing ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour—a special window from 4 p.m. to 6 p.m. local time, designed to supercharge your professional networking. Use this time to connect with other professionals, engage in meaningful conversations, and unlock opportunities for collaboration. Do not miss this chance to expand your network and accelerate your career! This networking room is available 24x7 for anyone to join and network with other professionals.',
                ),
                array(
                    'title' => 'Is it free to join ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour?',
                    'description' => 'Yes, it is free to join ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour. You can join the networking room anytime between 4 p.m. to 6 p.m. in your local time zone.',
                ),
                array(
                    'title' => 'What is I am not visiting this page 4 p.m. to 6 p.m. in my local time zone?',
                    'description' => 'You can still join the networking room anytime as long as you could find other people to connect with. You could always expand your search radius to see other people in your region.',
                ),
                array(
                    'title' => 'What is this community for?',
                    'description' => 'This community is for professionals from all over the world who are looking for new jobs or to advance their careers.',
                ),
                array(
                    'title' => 'Who is this community for?',
                    'description' => 'This community is for anyone who is serious about their job search. We welcome people of all experience levels, industries, and backgrounds.',
                ),
                array(
                    'title' => 'What are the benefits of joining this community?',
                    'description' => 'The benefits of joining this community include:<br />
                            Access to a network of professionals who can offer support and advice<br />
                            Information about new job opportunities<br />
                            Tips and advice on resume writing, cover letter writing, interviewing, and salary negotiation<br />
                            A sense of community and support',
                ),
                array(
                    'title' => 'How do I join this community?',
                    'description' => 'To join this community, simply create an account and join the group.',
                ),
                array(
                    'title' => 'What are the rules of this community?',
                    'description' => 'The rules of this community are simple: be respectful of others and share helpful information.',
                ),
            ),
        )
    );

    return $room_data_arr;
}

function get_networking_local_room_data($data)
{
    $keyslug = $data['keyslug'];
    $title = $data['title'];
    $current_app_slug = $data['current_app_slug'] ?? TAOH_SITE_CURRENT_APP_SLUG;
    $url_slug = taoh_slugify($title) . '-' . $keyslug;

    $room_data_arr = array(
        'keyslug' => $keyslug,
        'app' => 'global',
        'club' => array(
            'title' => $title,
            'description' => 'Welcome to the Global ' . TAOH_SITE_NAME_SLUG . ' Connex, a social group for professionals from all over the world! This is a place where you can connect with other professionals to advance your career, learn about new opportunities, and share your own insights and expertise.<br /><br />
                Whether you are just starting out in your career or you are a seasoned veteran, you are welcome here. We are a friendly and inclusive community of professionals from all areas and backgrounds.<br /><br />
                <strong>In our group, you can</strong>:<br /><br />
                Ask questions about career development, job search strategies, and networking.<br />
                Share your own career journey, advice, and insights.<br />
                Connect with other professionals who are interested in the same industries and roles as you.<br />
                Find mentors and sponsors who can help you achieve your career goals.<br />
                Make new friends and connections from all over the world!<br /><br />
                So what are you waiting for? Join the Global ' . TAOH_SITE_NAME_SLUG . ' Connex today and start building your global professional network!',
            'short' => 'Connect with ' . TAOH_SITE_NAME_SLUG . ' professionals from all over the ' . TAOH_SITE_NAME_SLUG . ' to advance your career. We meet 4pm - 6pm to connect and network.',
            'image' => TAOH_SITE_LOGO,
            'square_image' => TAOH_SITE_FAVICON,
            'links' => array(
                'club' => '/' . $current_app_slug . '/room/' . $url_slug,
                'networking' => '/' . $current_app_slug . '/local',
                'detail' => '/' . $current_app_slug . '/local',
            ),
            'profile_types' => array(
                array(
                    'slug' => 'employer',
                    'title' => 'Employer',
                ),
                array(
                    'slug' => 'professional',
                    'title' => 'Professional',
                ),
                array(
                    'slug' => 'provider',
                    'title' => 'Provider',
                ),
            ),
            'skill' => '',
            'company' => '',
            'roles' => '',
            'sponsors' => array(
                array(
                    'title' => 'NoWorkerLeftBehind',
                    'sub_title' => 'Let us all work together and help each other succeed.',
                    'image' => 'https://noworkerleftbehind.org/wp-content/uploads/2022/09/cropped-cropped-nwlb_sq-270x270.png',
                    'link' => 'https://noworkerleftbehind.org/',
                ),
                array(
                    'title' => 'TAO.ai',
                    'sub_title' => 'TAO: Through technology, make professional connectios and career growth universally accessible.',
                    'image' => 'https://tao.ai/tao/innovative/img/TAO_AI_Logo_icon_orng.png',
                    'link' => 'https://tao.ai',
                ),
            ),
            'breadcrumbs' => array(
                array(
                    'title' => 'Home',
                    'link' => '',
                ),
                array(
                    'title' => ucfirst($current_app_slug),
                    'link' => '/' . $current_app_slug,
                ),
                array(
                    'title' => $title,
                    'link' => '/' . $current_app_slug . '/room/' . $url_slug,
                ),
            ),
            'live' => '',
            'geo_enable' => $data['geo_enable'],
            'owner_enable' => false,
            'owner' => '',
            'full_location' => '',
            'coordinates' => '',
            'geohash' => '',
            'longitude' => '',
            'latitude' => '',
            'faq' => array(
                array(
                    'title' => 'What is ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour?',
                    'description' => 'Introducing ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour—a special window from 4 p.m. to 6 p.m. local time, designed to supercharge your professional networking. Use this time to connect with other professionals, engage in meaningful conversations, and unlock opportunities for collaboration. Do not miss this chance to expand your network and accelerate your career! This networking room is available 24x7 for anyone to join and network with other professionals.',
                ),
                array(
                    'title' => 'Is it free to join ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour?',
                    'description' => 'Yes, it is free to join ' . TAOH_SITE_NAME_SLUG . ' Community Networking Hour. You can join the networking room anytime between 4 p.m. to 6 p.m. in your local time zone.',
                ),
                array(
                    'title' => 'What is I am not visiting this page 4 p.m. to 6 p.m. in my local time zone?',
                    'description' => 'You can still join the networking room anytime as long as you could find other people to connect with. You could always expand your search radius to see other people in your region.',
                ),
                array(
                    'title' => 'What is this community for?',
                    'description' => 'This community is for professionals from all over the world who are looking for new jobs or to advance their careers.',
                ),
                array(
                    'title' => 'Who is this community for?',
                    'description' => 'This community is for anyone who is serious about their job search. We welcome people of all experience levels, industries, and backgrounds.',
                ),
                array(
                    'title' => 'What are the benefits of joining this community?',
                    'description' => 'The benefits of joining this community include:<br />
                            Access to a network of professionals who can offer support and advice<br />
                            Information about new job opportunities<br />
                            Tips and advice on resume writing, cover letter writing, interviewing, and salary negotiation<br />
                            A sense of community and support',
                ),
                array(
                    'title' => 'How do I join this community?',
                    'description' => 'To join this community, simply create an account and join the group.',
                ),
                array(
                    'title' => 'What are the rules of this community?',
                    'description' => 'The rules of this community are simple: be respectful of others and share helpful information.',
                ),
            ),
        ),
    );

    return $room_data_arr;
}

function get_forum_room_data($data)
{
    $keyslug = $data['keyslug'];
    $title = $data['title'];
    $current_app_slug = $data['current_app_slug'] ?? TAOH_SITE_CURRENT_APP_SLUG;
    $url_slug = taoh_slugify($title) . '-' . $keyslug;

    $room_data_arr = array(
        'keyslug' => $keyslug,
        'app' => 'forum',
        'club' => array(
            'title' => $title,
            'description' => 'Welcome to the ' . $title . ' forum! This is a place where you can connect with your peers and industry experts to discuss the latest trends, share insights, and ask questions. Whether you are looking for advice on your job search, career development, or industry-specific topics, this forum is the place to be.',
            'short' => 'Connect with your peers and industry experts to discuss the latest trends, share insights, and ask questions.',
            'image' => TAOH_SITE_LOGO,
            'square_image' => TAOH_SITE_FAVICON,
            'links' => array(
                'club' => '/' . $current_app_slug . '/forum/' . $url_slug. '?t=' . base64url_encode($title)
            ),
            'profile_types' => array(),
            'skill' => '',
            'company' => '',
            'roles' => '',
            'sponsors' => array(),
            'breadcrumbs' => array(
                array(
                    'title' => 'Home',
                    'link' => '',
                ),
                array(
                    'title' => ucfirst($current_app_slug),
                    'link' => '/' . $current_app_slug,
                ),
                array(
                    'title' => $title,
                    'link' => '/' . $current_app_slug . '/forum/' . $url_slug. '?t=' . base64url_encode($title),
                ),
            ),
            'live' => '',
            'geo_enable' => $data['geo_enable'],
            'owner_enable' => false,
            'owner' => '',
            'full_location' => '',
            'coordinates' => '',
            'geohash' => '',
            'longitude' => '',
            'latitude' => '',
            'faq' => array(),
        )
    );

    return $room_data_arr;
}