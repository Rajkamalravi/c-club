<?php
if (!taoh_user_is_logged_in()) {
    return taoh_redirect(TAOH_LOGIN_URL);
}

function process_keywords_post(){
    $taoh_user_keywords = defined('TAOH_USER_KEYWORDS') ? getJsonDecodedData(TAOH_USER_KEYWORDS) : [];
    $taoh_user_keywords_keys = array_keys($taoh_user_keywords);

    $keywords = [];
    foreach ($taoh_user_keywords_keys as $key) {
        if (isset($_POST[$key])) {
            $keywords[$key] = $_POST[$key];
            unset($_POST[$key]);
        }
    }

    $_POST['keywords'] = $keywords;
}

function set_profile_completion_flag($exist_data)
{
    $profile_stage = $exist_data['profile_stage'] ?? 0;
    $completed_profile_stages = $exist_data['completed_profile_stages'] ?? [];

    // Profile Stage 1: Settings Basic
    $fname = $_POST['fname'] ?? ($exist_data['fname'] ?? '');
    $lname = $_POST['lname'] ?? ($exist_data['lname'] ?? '');
    $coordinates = $_POST['coordinates'] ?? ($exist_data['coordinates'] ?? '');

    if(!empty($fname) && !empty($lname) && !empty($coordinates)){
        $profile_stage = 1;
        $completed_profile_stages[] = 1;
    }

    // Profile Stage 2: Settings General
    $company = $_POST['company:company'] ?? ($exist_data['company'] ?? []);
    $skill = $_POST['skill:skill'] ?? ($exist_data['skill'] ?? []);
    $role = $_POST['title:title'] ?? ($exist_data['title'] ?? []);

    if (!empty($company) && !empty($skill) && !empty($role)) {
        if ($profile_stage >= 1) {
            $profile_stage = 2;
        }
        $completed_profile_stages[] = 2;
    }

    // Profile Stage 3: Settings Profile Flags
    $tags_data = $_POST['tags_data'] ?? ($exist_data['tags_data'] ?? []);

    if (!empty($tags_data)) {
        if ($profile_stage >= 2) {
            $profile_stage = 3;
        }
        $completed_profile_stages[] = 3;
    }

    $_POST['profile_stage'] = $profile_stage;
    $_POST['completed_profile_stages'] = array_values(array_unique($completed_profile_stages));
}

function update_profile_info($data)
{
    $taoh_ptoken = $data['taoh_ptoken'] ?? '';

    // Update the user's basic profile information
    $remove = array("profile_detail_" . $taoh_ptoken,
        "profile_short_" . taoh_get_api_token(1),
        "profile_info_" . $taoh_ptoken,
        "profile_cell_" . $taoh_ptoken,
        "profile_full_" . $taoh_ptoken,
        "profile_public_" . $taoh_ptoken,
        "users_users_public_" . $taoh_ptoken,
        "*_networking_cell_" . $taoh_ptoken,
        "users_*",
    );

    $taoh_call = 'users.tao.add';
    $taoh_vals = array(
        'token' => taoh_get_api_token(1),
        'mod' => 'tao_tao',
        'toenter' => $data,
        'cache' => [
            "action" => 'profile_update',
            "intao_store" => 'taoh_intaodb_common',
            "remove" => $remove
        ]
    );

//    $taoh_vals['debug'] = 1;
//    echo taoh_apicall_post($taoh_call, $taoh_vals);die;

    return taoh_apicall_post($taoh_call, $taoh_vals);
}

if (isset($_POST['taoh_action']) && $_POST['taoh_action'] === 'basic_profile_update') {
    $response['status'] = false;

    $upload_status = true;
    $upload_error = 'upload_error';
    $file_upload_url = TAOH_CDN_PREFIX . '/cache/upload/now';
    if (!empty($_FILES['profile_picture']['name'])) {
        $profile_picture_upload_res = taoh_remote_file_upload($_FILES['profile_picture'], $file_upload_url);
        if ($profile_picture_upload_res['success']) {
            $_POST['avatar_image'] = $profile_picture_upload_res['output'];
        } else {
            $upload_status = false;
            $upload_error = 'upload_failed';
        }
    }

    if ($upload_status && (!empty($_POST['avatar_image']) || (!empty($_POST['avatar']) && ($_POST['avatar'] !== 'default')))) {
        $data_encode = json_encode(taoh_user_all_info());
        $data = json_decode($data_encode, true);

        $taoh_ptoken = $_POST['taoh_ptoken'] ?? '';

        $profile_complete_fields = ['fname', 'lname', 'email', 'type', 'country_code', 'coordinates', 'company:company', 'title:title'];
        $is_valid_profile = !in_array('', array_map(function ($field) {
            $val = $_POST[$field] ?? null;

            if (is_array($val)) {
                $nonEmpty = array_filter(array_map('trim', $val), static fn($v) => $v !== '');
                // return a non-empty marker if ok, else '' so your in_array check works
                return !empty($nonEmpty) ? 'ok' : '';
            }

            return trim((string)$val); // '' if missing/empty
        }, $profile_complete_fields), true);

        if ($is_valid_profile) {
            $_POST['profile_complete'] = 1;
        }

        $chatName = strtolower(trim($data['chat_name'] ?? ''));
        if ((empty($chatName) || $chatName === 'anonymous' || empty($data->profile_complete ?? '')) && !empty($_POST['fname'])) {
            $_POST['chat_name'] = $chatName = $_POST['fname'];
        }

        set_profile_completion_flag($data, $_POST);

        if (!empty($taoh_ptoken)) {
            // Update existing profile
            $api_response = update_profile_info($_POST);
            $result = json_decode($api_response);

            if (!empty($result)) {
                taoh_session_save(TAOH_ROOT_PATH_HASH, ['USER_INFO' => $result]);
                unset($_SESSION['user_timezone']);
                $response['status'] = true;
                $response['data'] = $result;
            } else {
                $response['status'] = false;
                $response['error'] = 'update_failed';
            }
        } else {
            // Create a new profile

        }
    } else {
        $response['status'] = false;
        $response['error'] = $upload_error === 'upload_failed' ? 'upload_failed' : 'profile_picture_not_provided';
    }

    echo json_encode($response);
    exit();
}

if (isset($_POST['taoh_action']) && in_array($_POST['taoh_action'], ['general_profile_update', 'profile_tags_update', 'profile_update'])) {
    $taoh_ptoken = $_POST['taoh_ptoken'] ?? '';

    if ($_POST['taoh_action'] === 'general_profile_update') {
        process_keywords_post();

        $data_encode = json_encode(taoh_user_all_info());
        $data = json_decode($data_encode, true);

        $chatName = strtolower(trim($_POST['chat_name'] ?? ''));
        if ((empty($chatName) || $chatName === 'anonymous') && !empty($data['fname'])) {
            $_POST['chat_name'] = $chatName = $data['fname'];
        }

        set_profile_completion_flag($data);
    }

    if ($_POST['taoh_action'] === 'profile_tags_update') {
        $data_encode = json_encode(taoh_user_all_info(1));
        $data = json_decode($data_encode, true);

        $_POST['tags'] = is_array($_POST['tags'] ?? null) ? $_POST['tags'] : [];
        $new_tags_data = $_POST['tags_data'] ?? [];

        $exist_tags_data = is_array($data['tags_data'] ?? null) ? $data['tags_data'] : [];
        $final_tags_data = array_merge($exist_tags_data, $new_tags_data);

        $category_key_ids = array_map(function ($category_key) {
            return str_replace(' ', '_', strtolower($category_key));
        }, array_values($_POST['tags']));

        // Keep only tags_data key that exist in tags array
        $_POST['tags_data'] = array_filter($final_tags_data, function ($key) use ($category_key_ids) {
            return in_array($key, $category_key_ids);
        }, ARRAY_FILTER_USE_KEY);

        array_walk($_POST['tags_data'], function (&$tag_data) {
            $tag_data = is_array($tag_data) ? json_encode($tag_data, JSON_UNESCAPED_SLASHES) : $tag_data;
        });

        $checkEmptyArrayFields = ['tags', 'tags_data'];
        array_map(function($field) {
            if (isset($_POST[$field]) && $_POST[$field] === []) {
                $_POST[$field] = '';
            }
        }, $checkEmptyArrayFields);

        set_profile_completion_flag($data);
    }

    if (isset($_POST['form_id']) && ($_POST['form_id'] === 'profile_experience_form' || $_POST['form_id'] === 'profile_education_form')) {
        if (isset($_POST['emp_btnSave']) || isset($_POST['edu_btnSave'])) {
            $data_encode = json_encode(taoh_user_all_info(1));
            $data = json_decode($data_encode, true);

            if ((isset($_POST['emp_add']) || isset($_POST['emp_edit'])) && isset($_POST['emp_btnSave'])) {
                $emp_key = $_POST['emp_edit'] ?? $_POST['emp_add'];
                $_POST['employee'] = is_array($data['employee'] ?? null) ? $data['employee'] : [];

                $employee_fields = [
                    'title:title'             => "emp_title_$emp_key",
                    'current_role'            => "is_current_role_check_$emp_key",
                    'emp_roletype'            => "emp_roletype_$emp_key",
                    'company:company'         => "emp_company_$emp_key",
                    'emp_coordinates'         => "emp_coordinates_$emp_key",
                    'emp_full_location'       => "emp_full_location_$emp_key",
                    'emp_geohash'             => "emp_geohash_$emp_key",
                    'emp_placeType'           => "emp_placeType_$emp_key",
                    'skill:skill'             => "emp_skill_$emp_key",
                    'emp_start_month'         => "emp_start_month_$emp_key",
                    'emp_year_start'          => "emp_year_start_$emp_key",
                    'emp_end_month'           => "emp_end_month_$emp_key",
                    'emp_year_end'            => "emp_year_end_$emp_key",
                    'emp_responsibilities'    => "emp_responsibilities_$emp_key",
                    'emp_profile_headline'    => "emp_profile_headline_$emp_key",
                    'emp_industry'            => "emp_industry_$emp_key"
                ];

                foreach ($employee_fields as $key => $postField) {
                    if (isset($_POST[$postField])) {
                        $empNewData[$key] = $_POST[$postField];
                    }
                }

                if (!empty($empNewData)) {
                    // Encode specific fields if present
                    $empEncodeFields = ['emp_responsibilities', 'emp_profile_headline'];
                    foreach ($empEncodeFields as $field) {
                        if (!empty(trim($empNewData[$field] ?? ''))) {
                            $empNewData[$field] = taoh_title_desc_encode($empNewData[$field]);
                        }
                    }

                    $_POST['employee'][$emp_key] = $empNewData;
                }
            }

            if ((isset($_POST['edu_add']) || isset($_POST['edu_edit'])) && isset($_POST['edu_btnSave'])) {
                $edu_key = $_POST['edu_edit'] ?? $_POST['edu_add'];
                $_POST['education'] = is_array($data['education'] ?? null) ? $data['education'] : [];

                $education_fields = [
                    'company:company' => "edu_name_$edu_key",
                    'edu_degree' => "edu_degree_$edu_key",
                    'edu_specalize' => "edu_specalize_$edu_key",
                    'edu_grade' => "edu_grade_$edu_key",
                    'edu_start_month' => "edu_start_month_$edu_key",
                    'edu_start_year' => "edu_start_year_$edu_key",
                    'edu_end_month' => "edu_end_month_$edu_key",
                    'edu_complete_year' => "edu_complete_year_$edu_key",
                    'edu_activities' => "edu_activities_$edu_key",
                    'edu_description' => "edu_description_$edu_key",
                    'skill:skill' => "edu_skill_$edu_key"
                ];

                foreach ($education_fields as $key => $postField) {
                    if (isset($_POST[$postField])) {
                        $eduNewData[$key] = $_POST[$postField];
                    }
                }

                if (!empty($eduNewData)) {
                    // Encode specific fields if present
                    $eduEncodeFields = ['edu_specalize', 'edu_activities', 'edu_description'];
                    foreach ($eduEncodeFields as $field) {
                        if (!empty(trim($eduNewData[$field] ?? ''))) {
                            $eduNewData[$field] = taoh_title_desc_encode($eduNewData[$field]);
                        }
                    }

                    $_POST['education'][$edu_key] = $eduNewData;
                }
            }
        }

        if (isset($_POST['emp_btnDelete']) || isset($_POST['edu_btnDelete'])) {
            $data_encode = json_encode(taoh_user_all_info(1));
            $data = json_decode($data_encode, true);

            if (isset($_POST['emp_delete']) && isset($_POST['emp_btnDelete'])) {
                $emp_key = $_POST['emp_delete'];
                $_POST['employee'] = is_array($data['employee'] ?? null) ? $data['employee'] : [];

                unset($_POST['employee'][$emp_key]);
                if (count($_POST['employee']) == 0) {
                    $_POST['employee'] = 'deleted';
                }
            }

            if (isset($_POST['edu_delete']) && isset($_POST['edu_btnDelete'])) {
                $edu_key = $_POST['edu_delete'];
                $_POST['education'] = is_array($data['education'] ?? null) ? $data['education'] : [];

                unset($_POST['education'][$edu_key]);
                if (count($_POST['education']) == 0) {
                    $_POST['education'] = 'deleted';
                }
            }
        }
    }


    if (isset($_POST['form_id']) && $_POST['form_id'] === 'profile_form_5') {
        $encodeFields = ['aboutme', 'funfact'];
        foreach ($encodeFields as $field) {
            if (!empty($_POST[$field])) {
                $_POST[$field] = taoh_title_desc_encode($_POST[$field]);
            }
        }
        $_POST['hobbies'] = json_encode($_POST['hobbies'] ?? '');
        $_POST['mylink'] = trim($_POST['mylink'] ?? '');
    }


    if (isset($_POST['form_id']) && $_POST['form_id'] === 'profile_form_6') {
        $_POST['tao_unsubscribe_emails'] = (int)!empty($_POST['tao_unsubscribe_emails']);
        $_POST['unlist_me_dir'] = ($_POST['unlist_me_dir'] ?? '') === 'yes' ? 'yes' : 'no';
    }

    unset($_POST['form_id']);

    $response['status'] = false;

    if (!empty($taoh_ptoken)) {
        // Update existing profile
        $api_response = update_profile_info($_POST);
        $result = json_decode($api_response);

        if (!empty($result)) {
            taoh_session_save(TAOH_ROOT_PATH_HASH, ['USER_INFO' => $result]);
            $response['status'] = true;
            $response['data'] = $result;
        } else {
            $response['status'] = false;
            $response['error'] = 'update_failed';
        }
    } else {
        // Create a new profile

    }

    echo json_encode($response);
    exit();
}


/**------------------------------------------ Above Code New -----------------------------------------------------------*/


function profile_completion($post)
{
    $status_data = array(
        '0' => array('code' => 0, 'status' => 'done'),
        '1' => array('code' => 1, 'status' => 'General Settings Incomplete'),
        '2' => array('code' => 2, 'status' => 'Public Info Incomplete')
    );

    //Only Mandatory fields to consider
    $general_tab_valid_fields = array(
        'fname', 'lname', 'type', 'email', 'title:title', 'company:company', 'full_location', 'local_timezone'
    );
    $public_tab_valid_fields = array(
        'chat_name', 'skill:skill', 'aboutme'
    );


    $general_filled = 0;
    foreach ($general_tab_valid_fields as $value) {
        if (array_key_exists($value, $post)) {
            if ($post[$value]) {
                $general_filled++;
            }
        }
    }

    $public_filled = 0;
    foreach ($public_tab_valid_fields as $value) {
        if (array_key_exists($value, $post)) {
            if ($post[$value]) {
                $public_filled++;
            }
        }
    }

    if ($general_filled == count($general_tab_valid_fields) && $public_filled == count($public_tab_valid_fields)) {
        $status = $status_data[0]; //done
    } else {
        if ($general_filled == count($general_tab_valid_fields)) {
            if ($public_filled != count($public_tab_valid_fields)) {
                $status = $status_data[2]; //public complete
            }
        } else {
            $status = $status_data[1]; //incomplete
        }
    }
    // print_r($status);
    // echo $general_filled;
    // echo $public_filled;
    // print_r($_POST);
    // die();
    return $status;

}

if ($_POST['taoh_action'] == 'new_profile' || $_POST['taoh_action'] == 'old_profile') {

    $data_encode = json_encode(taoh_user_all_info(1));
    $data = json_decode($data_encode, true);
    $sendArray = array();
    $tabname = '';
    if ($_POST['taoh_action'] == 'old_profile') {
        $sendArray['login_type'] = 'update';
    }

    if ((isset($_POST['emp_add']) || isset($_POST['emp_edit'])) && isset($_POST['emp_btnSave'])) {
        if (isset($_POST['emp_edit'])) {
            $emp_key = $_POST['emp_edit'];
        } else {
            $emp_key = $_POST['emp_add'];
        }
        if (is_array($data['employee'])) {
            $sendArray['employee'] = $data['employee'];
        } else {
            $sendArray['employee'] = [];
        }
        if (isset($_POST['emp_title_' . $emp_key])) $empArray['title:title'] = $_POST['emp_title_' . $emp_key];
        if (isset($_POST['is_current_role_check_' . $emp_key])) $empArray['current_role'] = $_POST['is_current_role_check_' . $emp_key];
        if (isset($_POST['emp_roletype_' . $emp_key])) $empArray['emp_roletype'] = $_POST['emp_roletype_' . $emp_key];
        if (isset($_POST['emp_company_' . $emp_key])) $empArray['company:company'] = $_POST['emp_company_' . $emp_key];
        if (isset($_POST['emp_coordinates_' . $emp_key])) $empArray['emp_coordinates'] = $_POST['emp_coordinates_' . $emp_key];
        if (isset($_POST['emp_full_location_' . $emp_key])) $empArray['emp_full_location'] = $_POST['emp_full_location_' . $emp_key];
        if (isset($_POST['emp_geohash_' . $emp_key])) $empArray['emp_geohash'] = $_POST['emp_geohash_' . $emp_key];
        if (isset($_POST['emp_placeType_' . $emp_key])) $empArray['emp_placeType'] = $_POST['emp_placeType_' . $emp_key];
        if (isset($_POST['emp_skill_' . $emp_key])) $empArray['skill:skill'] = $_POST['emp_skill_' . $emp_key];
        // if (isset($_POST['emp_skill_' . $emp_key])) $empArray['skill'] = $_POST['emp_skill_' . $emp_key];
        if (isset($_POST['emp_start_month_' . $emp_key])) $empArray['emp_start_month'] = $_POST['emp_start_month_' . $emp_key];
        if (isset($_POST['emp_year_start_' . $emp_key])) $empArray['emp_year_start'] = $_POST['emp_year_start_' . $emp_key];
        if (isset($_POST['emp_end_month_' . $emp_key])) $empArray['emp_end_month'] = $_POST['emp_end_month_' . $emp_key];
        if (isset($_POST['emp_year_end_' . $emp_key])) $empArray['emp_year_end'] = $_POST['emp_year_end_' . $emp_key];
        if (isset($_POST['emp_responsibilities_' . $emp_key])) $empArray['emp_responsibilities'] = taoh_title_desc_encode($_POST['emp_responsibilities_' . $emp_key]);
        if (isset($_POST['emp_industry_' . $emp_key])) $empArray['emp_industry'] = $_POST['emp_industry_' . $emp_key];
        if (isset($_POST['emp_profile_headline_' . $emp_key])) $empArray['emp_profile_headline'] = taoh_title_desc_encode($_POST['emp_profile_headline_' . $emp_key]);
        $sendArray['employee'][$emp_key] = $empArray;
    }

    if (isset($_POST['emp_delete']) && isset($_POST['emp_btnDelete'])) {
        $emp_key = $_POST['emp_delete'];
        if (is_array($data['employee'])) {
            $sendArray['employee'] = $data['employee'];
            unset($sendArray['employee'][$emp_key]);
            if (count($sendArray['employee']) == 0) {
                $sendArray['employee'] = 'deleted';
            }
        }
    }
    //echo "<pre>";print_r($sendArray);die;
    if ((isset($_POST['edu_add']) || isset($_POST['edu_edit'])) && isset($_POST['edu_btnSave'])) {
        if (isset($_POST['edu_edit'])) {
            $edu_key = $_POST['edu_edit'];
        } else {
            $edu_key = $_POST['edu_add'];
        }
        if (is_array($data['education'])) {
            $sendArray['education'] = $data['education'];
        } else {
            $sendArray['education'] = [];
        }
        if (isset($_POST['edu_name_' . $edu_key])) $eduArray['company:company'] = $_POST['edu_name_' . $edu_key];
        if (isset($_POST['edu_degree_' . $edu_key])) $eduArray['edu_degree'] = $_POST['edu_degree_' . $edu_key];
        if (isset($_POST['edu_specalize_' . $edu_key])) $eduArray['edu_specalize'] = taoh_title_desc_encode($_POST['edu_specalize_' . $edu_key]);
        if (isset($_POST['edu_grade_' . $edu_key])) $eduArray['edu_grade'] = $_POST['edu_grade_' . $edu_key];
        if (isset($_POST['edu_start_month_' . $edu_key])) $eduArray['edu_start_month'] = $_POST['edu_start_month_' . $edu_key];
        if (isset($_POST['edu_start_year_' . $edu_key])) $eduArray['edu_start_year'] = $_POST['edu_start_year_' . $edu_key];
        if (isset($_POST['edu_end_month_' . $edu_key])) $eduArray['edu_end_month'] = $_POST['edu_end_month_' . $edu_key];
        if (isset($_POST['edu_complete_year_' . $edu_key])) $eduArray['edu_complete_year'] = $_POST['edu_complete_year_' . $edu_key];
        if (isset($_POST['edu_activities_' . $edu_key])) $eduArray['edu_activities'] = taoh_title_desc_encode($_POST['edu_activities_' . $edu_key]);
        if (isset($_POST['edu_description_' . $edu_key])) $eduArray['edu_description'] = taoh_title_desc_encode($_POST['edu_description_' . $edu_key]);
        if (isset($_POST['edu_skill_' . $edu_key])) $eduArray['skill:skill'] = $_POST['edu_skill_' . $edu_key];
        // if (isset($_POST['edu_skill_' . $edu_key])) $eduArray['skill'] = $_POST['edu_skill_' . $edu_key];
        $sendArray['education'][$edu_key] = $eduArray;
    }
    if (isset($_POST['edu_delete']) && isset($_POST['edu_btnDelete'])) {
        $edu_key = $_POST['edu_delete'];
        if (is_array($data['education'])) {
            $sendArray['education'] = $data['education'];
            unset($sendArray['education'][$edu_key]);
            if (count($sendArray['education']) == 0) {
                $sendArray['education'] = 'deleted';
            }
        }
    }
    if (isset($_POST['about_btnSave'])) {
        $sendArray['aboutme'] = taoh_title_desc_encode(trim($_POST['aboutme']));
    }
    if (isset($_POST['funfact_btnSave'])) {
        // $sendArray['funfact'] = trim($_POST['funfact_old']).'##$##'.trim($_POST['funfact']);
        // echo "<pre>"; print_r($_POST); echo "</pre>"; die;
        $sendArray['funfact'] = taoh_title_desc_encode($_POST['funfact']);
    }

    if (isset($_POST['hobbies_btnSave'])) {
        // $sendArray['funfact'] = trim($_POST['funfact_old']).'##$##'.trim($_POST['funfact']);
        // echo "<pre>"; print_r($_POST); echo "</pre>"; die;
        $sendArray['hobbies'] = json_encode($_POST['hobbies']);
    }

    if (isset($_POST['mylink'])) {
        $sendArray['mylink'] = trim($_POST['mylink']);
    }
    if (isset($_POST['tao_unsubscribe_emails'])) {
        $sendArray['tao_unsubscribe_emails'] = ($_POST['tao_unsubscribe_emails'] == true || $_POST['tao_unsubscribe_emails'] == 1) ? 1 : 0;
    }
    if (isset($_POST['unlist_me_dir'])) {
        $sendArray['unlist_me_dir'] = ($_POST['unlist_me_dir'] == true || $_POST['unlist_me_dir'] == 1) ? 1 : 0;
    }

    if (isset($_POST['step5'])) {
        // echo "<br> Submit form 5";
        // echo "<pre>"; print_r($_POST); echo "</pre>"; die;
        $sendArray['aboutme'] = taoh_title_desc_encode(trim($_POST['aboutme']));
        $sendArray['funfact'] = taoh_title_desc_encode($_POST['funfact']);
        $sendArray['hobbies'] = json_encode($_POST['hobbies']);
        $sendArray['mylink'] = trim($_POST['mylink']);
    }
    $remove = array("profile_detail_" . $_POST['taoh_ptoken'],
        "profile_short_" . taoh_get_api_token(),
        "profile_info_" . $_POST['taoh_ptoken'],
        "profile_cell_" . $_POST['taoh_ptoken'],
        "profile_full_" . $_POST['taoh_ptoken'],
        "users_*",
    );
    $taoh_call = 'users.tao.add';
    $taoh_call_type = 'POST';
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(),
        'mod' => 'tao_tao',
        'toenter' => $sendArray,
        'redis_action' => 'profile_update',
        'redis_store' => 'taoh_intaodb_common',
        'cache' => array('remove' => $remove),
    );
    // echo '<pre>';print_r($sendArray);
    // echo "<pre style='color:red'>";print_r($taoh_vals); die;
    // echo taoh_apicall_post_debug($taoh_call, $taoh_vals); die;
    $result = taoh_apicall_post($taoh_call, $taoh_vals);

    taoh_delete_local_cache('tao_tao',$remove);

    //$result = taoh_apicall( $taoh_call, $taoh_call_type, $taoh_vals );
    unset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']);
    unset($_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']);
    //die();
    taoh_set_success_message('Profile Saved');
    // taoh_redirect(TAOH_SITE_URL_ROOT . '/profile/' . $_POST['taoh_ptoken']);
    /* if (isset($_POST['step5'])) { // commented to stop save and continue process
        $tabname = "#form-block-6";
    }
    if ((isset($_POST['emp_add']) || isset($_POST['emp_edit'])) && isset($_POST['emp_btnSave'])) {
        $tabname = "#form-block-3";
    }
    if ((isset($_POST['edu_add']) || isset($_POST['edu_edit'])) && isset($_POST['edu_btnSave'])) {
        $tabname = "#form-block-4";
    } */
    taoh_redirect(TAOH_SITE_URL_ROOT . '/settings/' . $_POST['taoh_ptoken'].$tabname);

    die();
}


/*if ( isset( $_POST['lname'] ) && isset( $_POST[ 'fname' ] ) && isset( $_POST[ 'email' ] ) && isset( $_POST[ 'chat_name' ] ) ){
*/
if(isset($_POST['login_type']) && $_POST['login_type'] == 'create'){
		//$profile_status = profile_completion($_POST);
		//$_POST['profile_status'] = $profile_status;
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		die;*/
		$remove = array("profile_detail_".$_POST['taoh_ptoken'],
			"profile_short_".taoh_get_api_token(),
			"profile_info_".$_POST['taoh_ptoken'],
			"profile_cell_".$_POST['taoh_ptoken'],
			"profile_full_".$_POST['taoh_ptoken'],
			"profile_public_".$_POST['taoh_ptoken'],
            "users_*",
		);
    $taoh_call = 'users.tao.add';
    $taoh_call_type = 'POST';
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(),
        'mod' => 'tao_tao',
        'toenter' => $_POST,
        'redis_action' => 'profile_update',
        'redis_store' => 'taoh_intaodb_common',
        'cache' => array('remove' => $remove),

    );
		//echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die;
		$result = taoh_apicall_post($taoh_call, $taoh_vals);
		taoh_delete_local_cache('tao_tao',$remove);

		//$result = taoh_apicall( $taoh_call, $taoh_call_type, $taoh_vals );
		unset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']);
    	unset($_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']);

		if(isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'refer_token']!=''){

            $taoh_call = "core.refer.update";
            $taoh_vals = array(
                "mod" => "invite",
                'refer_token' => $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'],
                'secret' => TAOH_API_SECRET,
                'token' => taoh_get_dummy_token(),
                'refer_key' => 'profile',

            );
			//echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );
			taoh_apicall_post($taoh_call, $taoh_vals);

			if(isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url'])
			 && $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url'] != ''){
            		$url = $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url'];

				/*setcookie(TAOH_ROOT_PATH_HASH.'_'.'refer_token',$refer_token, strtotime( '-2 days' ), '/');
				setcookie(TAOH_ROOT_PATH_HASH.'_'.'referral_back_url',$link, strtotime( '-2 days' ), '/');
				setcookie(TAOH_ROOT_PATH_HASH.'_'.'referral_data',$data, strtotime( '-2 days' ), '/');
				setcookie(TAOH_ROOT_PATH_HASH.'_'.'from_referral',1, strtotime( '-2 days' ), '/');*/

				taoh_set_success_message('Profile completed');
				//echo "====".$url;die();
				taoh_redirect($url); exit();
			}
			/*else{
				setcookie(TAOH_ROOT_PATH_HASH.'_'.'refer_token',$refer_token, strtotime( '-2 days' ), '/');
				setcookie(TAOH_ROOT_PATH_HASH.'_'.'referral_back_url',$link, strtotime( '-2 days' ), '/');
				setcookie(TAOH_ROOT_PATH_HASH.'_'.'referral_data',$data, strtotime( '-2 days' ), '/');
				setcookie(TAOH_ROOT_PATH_HASH.'_'.'from_referral',1, strtotime( '-2 days' ), '/');
			}*/
		}


		if(isset($_POST['login_type']) && $_POST['login_type'] == 'create'){
			//echo "=1===";die();
			taoh_set_success_message('Your account was successfully created!');
			taoh_redirect(TAOH_SITE_URL_ROOT.'/apps?success=true');
		}else{
			taoh_set_success_message('Settings Saved');
			//echo "=2===";die();
			taoh_redirect(TAOH_SITE_URL_ROOT.'/settings');
		}
}


//old settig data api
if (isset($_POST['taoh_session'])) {
    process_keywords_post();

    if ($_POST['taoh_session'] == "old") {
        if (isset($_POST['lname']) && isset($_POST['fname']) && isset($_POST['email']) && isset($_POST['chat_name'])) {

            //$profile_status = profile_completion($_POST);
            //$_POST['profile_status'] = $profile_status;
            //print_r($_POST);die;
            $remove = array("profile_detail_" . $_POST['taoh_ptoken'],
                "profile_short_" . taoh_get_api_token(),
                "profile_info_" . $_POST['taoh_ptoken'],
                "profile_cell_" . $_POST['taoh_ptoken'],
                "profile_full_" . $_POST['taoh_ptoken'],
                "profile_public_" . $_POST['taoh_ptoken'],
                "*_networking_cell_" . $_POST['taoh_ptoken'],
                "users_*",
            );

            $taoh_call = 'users.tao.add';
            $taoh_call_type = 'POST';
            $taoh_vals = array(
                'token' => taoh_get_dummy_token(),
                'mod' => 'tao_tao',
                'toenter' => $_POST,
                'redis_action' => 'profile_update',
                'redis_store' => 'taoh_intaodb_common',
                'cache' => array('remove' => $remove),
            );
            //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die;
            $result = taoh_apicall_post($taoh_call, $taoh_vals);
            taoh_delete_local_cache('tao_tao',$remove);
            //echo $result;exit();

            //$result = taoh_apicall( $taoh_call, $taoh_call_type, $taoh_vals );
            unset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']);
            unset($_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']);
            if (isset($_POST['login_type']) && $_POST['login_type'] == 'create') {
                taoh_redirect(TAOH_SITE_URL_ROOT . '/apps?success=true');
            } else {
				if(isset($_POST['login_type'] ) &&  $_POST['login_type'] == 'first_update'){
					update_refer_for_profile_complete();
				}
                taoh_set_success_message('Settings Saved');
                taoh_redirect(TAOH_SITE_URL_ROOT . '/settings');
            }
        }
    } else {
        $remove = array("profile_detail_" . $_POST['taoh_ptoken'],
            "profile_short_" . taoh_get_api_token(),
            "profile_info_" . $_POST['taoh_ptoken'],
            "profile_cell_" . $_POST['taoh_ptoken'],
            "profile_full_" . $_POST['taoh_ptoken'],
            "profile_public_" . $_POST['taoh_ptoken'],
            "*_networking_cell_" . $_POST['taoh_ptoken'],
            "users_*",
        );

        $taoh_call = 'users.tao.add';
        $taoh_call_type = 'POST';
        $taoh_vals = array(
            'token' => taoh_get_dummy_token(),
            'mod' => 'tao_tao',
            'toenter' => $_POST,
            'redis_action' => 'profile_update',
            'redis_store' => 'taoh_intaodb_common',
            'cache' => array('remove' => $remove),
        );
        //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die;
        $result = json_decode(taoh_apicall_post($taoh_call, $taoh_vals));
        taoh_delete_local_cache('tao_tao',$remove);
        if ($result == "") {
            echo 0;
        } else {

			if(isset($_POST['login_type'] ) &&  $_POST['login_type'] == 'first_update'){
				update_refer_for_profile_complete();
			}

            taoh_session_save(TAOH_ROOT_PATH_HASH, ['USER_INFO' => $result]);
            echo 1;
        }
        exit;
    }
}

if(isset($_POST['step2'])){
    //taoh_indb_session_step2

    $remove = array("profile_detail_" . $_POST['taoh_ptoken'],
        "profile_short_" . taoh_get_api_token(),
        "profile_info_" . $_POST['taoh_ptoken'],
        "profile_cell_" . $_POST['taoh_ptoken'],
        "profile_full_" . $_POST['taoh_ptoken'],
        "users_*",
    );
    $taoh_call = 'users.tao.add';
    $taoh_call_type = 'POST';
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(),
        'mod' => 'tao_tao',
        'toenter' => $_POST,
        'redis_action' => 'profile_update',
        'redis_store' => 'taoh_intaodb_common',
        'cache' => array('remove' => $remove),
    );
    // echo '<pre>';print_r($taoh_vals); die;
    //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);
    $result = taoh_apicall_post($taoh_call, $taoh_vals);
    taoh_delete_local_cache('tao_tao',$remove);

    //$result = taoh_apicall( $taoh_call, $taoh_call_type, $taoh_vals );
    unset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']);
    unset($_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']);

    echo 1;

}


taoh_exit();
