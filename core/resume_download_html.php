<?php
$ptoken = taoh_parse_url(1);

$indx_db_settings = 1;
$user_is_logged_in = taoh_user_is_logged_in() ?? false;
$data = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
if($ptoken == '' || $ptoken == 'stlo'){
    $ptoken = $data->ptoken;
}
// echo "<br> ptoken : ".$ptoken;
$return = taoh_get_user_info($ptoken,'full',1);
$pfdata = json_decode($return, true);

// echo "<pre style='color:blue'>"; print_r($data); echo "</pre>";
// echo "<pre style='color:red'>"; print_r($pfdata['output']); echo "</pre>";
$sub_domain_value = $pfdata['output']['user']['site']['sub_domain_value'] ?? '';
$source = json_decode($sub_domain_value, true)[0]['source'] ?? '';

$is_same_source = defined('TAOH_SITE_URL_ROOT') && $source === TAOH_SITE_URL_ROOT;

$about_me = implode(' ', array_filter(explode(' ', $pfdata['output']['user']['aboutme'])));
$fun_fact = implode(' ', array_filter(explode(' ', $pfdata['output']['user']['funfact'])));
$hobbies = json_decode(implode(' ', array_filter(explode(' ', $pfdata['output']['user']['hobbies']))));

$get_skill = $pfdata['output']['user']['skill'];
$data_keywords = (array)($pfdata['output']['user']['keywords'] ?? []);


if (isset($pfdata['output']['user']['avatar_image']) && $pfdata['output']['user']['avatar_image'] != '') {
    $avatar_image = $pfdata['output']['user']['avatar_image'];
} else {
    if (isset($pfdata['output']['user']['avatar']) && $pfdata['output']['user']['avatar'] != 'default') {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $pfdata['output']['user']['avatar'] . '.png';
    } else {
        $avatar_image = TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png';
    }
}
$localImagePath =  $avatar_image;

// $localImagePath = 'profile_temp.jpg';
// file_put_contents($localImagePath, file_get_contents($remoteImageUrl));
// echo "<pre>"; print_r($data_keywords); echo "</pre>";

$tag_category = TAOH_TAG_CATEGORY;
$tag_category_form = TAOH_TAG_CATEGORY_FORM;

if (isset($pfdata['output']['user']['education']) && is_array($pfdata['output']['user']['education'])) {
    $edu_encode = json_encode($pfdata['output']['user']['education']);
    $edu_list = json_decode($edu_encode, true);
    $edu_tot_count = array_key_last($edu_list) + 1;
    $edu_last_key = array_key_last($edu_list);
} else {
    $edu_tot_count = 0;
    $edu_last_key = 0;
    $edu_list = '';
}
if (isset($pfdata['output']['user']['employee']) && is_array($pfdata['output']['user']['employee'])) {
    $emp_encode = json_encode($pfdata['output']['user']['employee']);
    $emp_list = json_decode($emp_encode, true);
    $emp_tot_count = array_key_last($emp_list) + 1;
    $emp_last_key = array_key_last($emp_list);
} else {
    $emp_tot_count = 0;
    $emp_last_key = 0;
    $emp_list = '';
}

if($edu_list != ''){
    foreach($edu_list as $edu_keys => $edu_vals){
        $edu_year[$edu_keys] = $edu_vals['edu_complete_year'];
        $edu_list[$edu_keys]['keys'] = $edu_keys;
    }
    array_multisort($edu_year, SORT_DESC, $edu_list);
}

if($emp_list != ''){
    foreach($emp_list as $ekeys => $evals){
        $emp_year[$ekeys] = $evals['emp_year_end'];
        $emp_list[$ekeys]['keys'] = $ekeys;
    }
    array_multisort($emp_year, SORT_DESC, $emp_list);
}
?>


<?php

$date = date('m/d/Y');

global $htmldata;
$company = '';
$user_type = !empty($pfdata['output']['user']['type']) ? ucfirst($pfdata['output']['user']['type']) : 'Professional';
foreach($pfdata['output']['user']['company'] as $ckey=>$companyDet){
    $company = $companyDet['value'] ?? '';
}

foreach($pfdata['output']['user']['title'] as $ckey=>$titleDet){
    $title = $titleDet['value'] ?? '';
}

$htmldata = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>resume</title>
</head>
<style>
    @import url("https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap");
    body {
        font-family: "Ubuntu", sans-serif;
        font-size: 12px;
        color: #000;
        margin: 0;
        padding: 0;
    }
    h1, h2, h3, h4, h5, h6, p {
        margin: 0;
        padding: 0;
    }
    table {
        margin: 0;
    }
    ul, ol {
        margin: 0;
        padding: 0;
        list-style: none;
    }
</style>
<body>
    <div>
        <table cellpadding="0" cellspacing="0" border="0" style="border-bottom: 1px solid #d3d3d3; width: 100%;">

            <tr>
                <td style="width: 100px; text-align: center; padding-bottom: 5px;">';
                    $htmldata .= '<img style="width: 80px; height: 80px; border-radius: 100%; object-fit: cover;" src="'.$localImagePath.'" alt="Profileimage">';
            if($pfdata['output']['user']['mylink'] != ''){
                $htmldata .= '
                    <div style="">
                        <img style="width: 12px; height: 12px; margin-top: 5px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/linkedin.jpg" alt="">
                    </div>';
            }
                $htmldata .= '</td>
                <td style="width: 100%; padding-bottom: 5px;">
                    <div>
                        <p style="font-size: 18px; font-weight: 500;">'.ucfirst($pfdata['output']['user']['fname']).' <span style=" background-color: #2557A7; color: #ffffff; font-weight: 500; border-radius: 12px; font-size: 14px;">&nbsp;&nbsp; '.$user_type.' &nbsp;&nbsp;</span></p>
                        <span style="font-size: 18px; font-weight: 500;">'.$company.'</span>
                    </div>
                </td>
            </tr>
        </table>

        <table style="padding-top: 15px;" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="width: 50%;">
                    <img style="width: 12px; height: 12px; margin-right: 3px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/job.jpg" >
                    <span style="font-size: 16px; font-weight: 400;">'.$title.'</span>
                </td>
                <td style="width: 40%;">
                    <img style="width: 12px; height: 14px; margin-right: 3px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/loc.jpg" >
                    <span style="font-size: 16px; font-weight: 400;">'.$pfdata['output']['user']['full_location'].'</span>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    <img style="width: 12px; height: 10px; margin-right: 3px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/mail.jpg" >
                    <span style="font-size: 16px; font-weight: 400;">'.$pfdata['output']['user']['email'].'</span>
                </td>
            </tr>
        </table>

        <!-- about me start  -->
        <table style="padding-top: 15px; width: 100%;" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td  style="width: 100%;">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td style="width: 16px; border-bottom: 1px solid #d3d3d3; padding-bottom: 6px; padding-right: 8px;">
                                <img style="width: 12px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/abt-prs.jpg" alt="">
                            </td>
                            <td style="border-bottom: 1px solid #d3d3d3; padding-bottom: 6px; width: 100%;"><span style="font-size: 16px; font-weight: 500; color: #000000;">About me</span>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" style="padding-top: 6px;">
                                <p style="font-size: 16px; color: #000000; text-align: left; line-height: 13px;">'.taoh_title_desc_decode($about_me).'</p>
                            </td>
                        </tr>
                    </table>
                    <!-- skills -->
                    <table style="padding-top: 6px;" cellpadding="0" cellspacing="0">
                        <tr>

                            <td style="width: 40px; padding-right: 6px;">
                                <span style="font-size: 16px; font-weight: 500; color: #000000; line-height: 17px;">Skills:</span>
                            </td>
                            <td style="width: 100%;">
                            ';
                                                foreach($get_skill as $keys => $vals){
                                                    if(!empty($vals['value'])){
                                                        $htmldata .= ' <span style="font-size: 13px; line-height: 21px; font-weight: 400; color: #000000; background-color: #008799; border-radius: 12px;">&nbsp; &nbsp; '.$vals['value'].' &nbsp; &nbsp;</span>&nbsp;';
                                                }
                                            }
                                    $htmldata .= '

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- about me end  -->


        <!-- intersts start  -->
        <!-- <table cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 16px; border-bottom: 1px solid #d3d3d3; padding-bottom: 10px; padding-right: 12px;">
                    <img style="width: 10px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/intersts.jpg" alt="">
                    <div style="line-height: 2px;">&nbsp;</div>
                </td>
                <td style="border-bottom: 1px solid #d3d3d3; padding-bottom: 10px; width: 100%;"><span style="font-size: 12px; font-weight: 500; color: #000000;">&nbsp; Interests</span>
                <div style="line-height: 2px;">&nbsp;</div></td>
            </tr>

            <tr>
                <td style="width: 20px;">&nbsp;</td>
                <td style="width: 100%;">
                    <div style="line-height: 9px;">&nbsp;</div>
                    <span style="font-size: 10px; line-height: 30px; font-weight: 400; color: #ffffff; background-color: #B55B55; border-radius: 12px;">&nbsp; &nbsp; Open to Work &nbsp; &nbsp;</span>

                    &nbsp;

                    <span style="font-size: 10px; line-height: 30px; font-weight: 400; color: #ffffff; background-color: #4F4F4F; border-radius: 12px;">&nbsp; &nbsp; Bartering &nbsp; &nbsp;</span>

                     &nbsp;

                    <span style="font-size: 10px; line-height: 30px; font-weight: 400; color: #ffffff; background-color: #4F4F4F; border-radius: 12px;">&nbsp; &nbsp; Volunteering &nbsp; &nbsp;</span>

                     &nbsp;

                    <span style="font-size: 10px; line-height: 30px; font-weight: 400; color: #ffffff; background-color: #4F4F4F; border-radius: 12px;">&nbsp; &nbsp; Career Switch &nbsp; &nbsp;</span>

                     &nbsp;

                    <span style="font-size: 10px; line-height: 30px; font-weight: 400; color: #ffffff; background-color: #4F4F4F; border-radius: 12px;">&nbsp; &nbsp; Interview Preparation &nbsp; &nbsp;</span>
                </td>
            </tr>
        </table> -->
        <!-- intersts end  -->';

    if($emp_list != '' && count($emp_list) > 0){
        $htmldata .=  '<!-- <br><br> -->
        <!-- Educational Qualifications start  -->
        <table style="padding-top: 15px; width: 100%;" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td>

                    <table style="width: 100%;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 16px; border-bottom: 1px solid #d3d3d3; padding-bottom: 6px; padding-right: 6px;">
                                <img style="width: 14px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/edu.jpg" alt="">

                            </td>
                            <td style="border-bottom: 1px solid #d3d3d3; padding-bottom: 6px; width: 100%;"><span style="font-size: 16px; font-weight: 500; color: #000000;">Educational Qualifications</span>
                            </td>
                        </tr>
                    </table>';

                        foreach($edu_list as $edu_keys => $edu_vals){
                            $ed_name = $edu_vals['company'];
                            foreach ( $ed_name as $ed_key => $ed_value ){
                                if(!is_array($ed_value)){
                                    list ( $ed_pre, $ed_post ) = explode( ':>', $ed_value );
                                }else{
                                    foreach ( $ed_value as $edu_key => $edu_value ){
                                        list ( $ed_pre, $ed_post ) = explode( ':>', $edu_value );
                                    }
                                }
                            }

                    $htmldata .=  '

                    <!-- list 1 -->
                    <table style="padding-top: 10px; width: 100%;">
                        <tr>
                            <td style="width: 100%; ">
                                <table style="width: 100%;" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="width: 50%;">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="width: 35px; padding-right: 10px;">

                                                        <img style="width: 30px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/grd.jpg" alt="">

                                                    </td>
                                                    <td style="width: 100%;">
                                                        <div>
                                                            <span style="font-size: 16px; font-weight: 500; color: #000000; line-height: 12px">'.$edu_vals['edu_specalize'].'</span>
                                                            <br style="line-height: 1;">
                                                            <span style="font-size: 12px; font-weight: 400; color: #4F4F4F; line-height: 10px">'.get_month_from_number($edu_vals['edu_start_month']).' '.$edu_vals['edu_start_year'].' to '.get_month_from_number($edu_vals['edu_end_month']).' '.$edu_vals['edu_complete_year'].'</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="width: 50%;">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="width: 30px; padding-right: 10px;">
                                                        <img style="width: 20px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/univ.jpg" alt="">
                                                    </td>
                                                    <td style="width: 100%;">
                                                        <div>
                                                            <span style="font-size: 16px; font-weight: 500; color: #000000; line-height: 12px">'.$ed_post.'</span>
                                                            <br style="line-height: 1;">
                                                            <span style="font-size: 12px; font-weight: 400; color: #4F4F4F; line-height: 10px"></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>';

            if($edu_vals['edu_grade'] != ''){
                $htmldata .= '
                    <table style="padding-top: 6px;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <span style="font-size: 16px; font-weight: 500; color: #000000;">Grade: '.$edu_vals['edu_grade'].'</span>
                            </td>
                        </tr>
                    </table>';
            }

            if($edu_vals['skill'] != ''){
                $htmldata .= '
                    <!-- skills -->
                    <table style="padding-top: 6px;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 40px;">
                                <span style="font-size: 16px; font-weight: 500; color: #000000;">Skills:</span>
                            </td>
                            <td style="width: 100%;">';
                                        $d_skills = $edu_vals['skill'];
                                        $d_items = '';
                                        foreach ($d_skills as $d_keys => $d_vals){
                                            if(!is_array($ed_value)){
                                                list ( $skill_pre, $skill_name ) = explode(':>',$d_vals);
                                                     $d_items = explode(':>',$d_vals);
                                $htmldata .= ' <span style="font-size: 14px; line-height: 18px; font-weight: 400; color: #000000; background-color: #EFEFEF; border-radius: 12px;">&nbsp; &nbsp; '.$skill_name.' &nbsp; &nbsp;</span> &nbsp;';
                                }  else{
                                            foreach ($d_vals as $ed_keys => $ed_vals){
                                                list ( $skill_pre, $skill_name ) = explode(':>',$ed_vals);
                                                $htmldata .= ' <span style="font-size: 14px; line-height: 18px; font-weight: 400; color: #000000; background-color: #EFEFEF; border-radius: 12px;">&nbsp; &nbsp; '.$skill_name.' &nbsp; &nbsp;</span> &nbsp;';

                                }
                                    }
                                }
                            $htmldata .= '</td>
                        </tr>
                    </table>';
            }
                    if(trim($edu_vals['edu_activities']) != ''){
                    $htmldata .= '
                    <table style="padding-top: 6px;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 100%;">
                                <span style="font-size: 16px; font-weight: 500; color: #000000;">Activities: '.$edu_vals['edu_activities'].'</span>
                            </td>
                        </tr>
                    </table>';
                    }


                    if(trim($edu_vals['edu_activities']) != ''){
                    $htmldata .= '
                    <table style="padding-top: 6px; width: 100%;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <span style="font-size: 16px; font-weight: 500; color: #000000;">Description:</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%; padding-top: 6px;">
                                <span style="font-size: 14px; color: #000000; text-align: left; line-height: 13px;">'.$edu_vals['edu_description'].'</span>
                            </td>
                        </tr>
                    </table>';
                    }
                }
                $htmldata .= '
                    <!-- list 1 end -->

                    </td>
            </tr>
        </table>
        <!-- Educational Qualifications end  -->';
    }

    if($emp_list != '' && count($emp_list) > 0){
        $htmldata .= '
        <!-- Experience Details start  -->
        <table style="padding-top: 15px; width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <table style="width: 100%;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 16px; border-bottom: 1px solid #d3d3d3; padding-bottom: 6px; padding-right: 6px;">
                                <img style="width: 14px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/exp.jpg" alt="">
                            </td>
                            <td style="border-bottom: 1px solid #d3d3d3; padding-bottom: 6px; width: 100%;"><span style="font-size: 16px; font-weight: 500; color: #000000;">Experience Details</span>
                            </td>
                        </tr>
                    </table>';


                    foreach($emp_list as $emp_keys => $emp_vals){
                        // echo "<pre>"; print_r($emp_vals); echo "</br>";
                        $em_title = (isset($emp_vals['emp_title']))? $emp_vals['emp_title'] : $emp_vals['title'];
                        foreach ( $em_title as $em_key => $em_value ){
                            if(!is_array($em_value)){
                                list ( $em_pre, $em_post ) = explode( ':>', $em_value );
                            }else{
                                // echo"<pre>";print_r($em_value); //die();
                                foreach ( $em_value as $emp_key => $emp_value ){
                                    // echo "<br> Emp value :".$emp_value;
                                    list ( $em_pre, $em_post ) = explode( ':>', $emp_value );
                                }
                            }
                        }
                        $em_company = (isset($emp_vals['emp_company'])) ?$emp_vals['emp_company'] : $emp_vals['company'];
                        foreach ( $em_company as $em_cmp_key => $em_cmp_value ){
                            if(!is_array($em_cmp_value)){
                                list ( $em_cmp_pre, $em_cmp_post ) = explode( ':>', $em_cmp_value );
                            }else{
                                // echo"<pre>";print_r($em_value); //die();
                                foreach ( $em_cmp_value as $emp_cmp_key => $emp_cmp_value ){
                                    // echo "<br> Emp value :".$emp_value;
                                    list ( $em_cmp_pre, $em_cmp_post ) = explode( ':>', $emp_cmp_value );
                                }
                            }

                        }

                        $get_present_not = ($emp_vals['current_role'] == 'on')?' Present':get_month_from_number($emp_vals['emp_end_month']).' '.$emp_vals['emp_year_end'];
                               $current_year = date('Y');   // Outputs: 2025 (Current Year)
                               $current_month = date('n');  // Outputs: 3 (Current Month - Without leading zero)

                               $end_month = !empty($emp_vals['emp_end_month']) ?$emp_vals['emp_end_month'] : $current_month;
                               $end_year = !empty($emp_vals['emp_year_end']) ? $emp_vals['emp_year_end'] : $current_year;

                               $emp_placeType = $emp_vals['emp_placeType'];
                               if($emp_placeType == 'rem'){
                                   $emp_placeType = 'Remote';
                               }else if($emp_placeType == 'ons'){
                                   $emp_placeType = 'Onsite';
                               }else if($emp_placeType == 'hyb'){
                                   $emp_placeType = 'Hybrid';
                               }else{
                                   $emp_placeType = '';
                               }

                               $roletype_arr = array(
                                   "remo" => "Remote Work",
                                   "full" => "Full Time",
                                   "part" => "Part Time",
                                   "temp" => "Temporary",
                                   "free" => "Freelance",
                                   "cont" => "Contract",
                                   "pdin" => "Paid Internship",
                                   "unin" => "Unpaid Internship",
                                   "voln" => "Volunteer",
                               );
                               $roletype = $emp_vals['emp_roletype'];
                               $role_items = '';
                               foreach ($roletype as $key => $value){
                                   $role_items = $roletype_arr[$value];
                               }

                               $industry_arr = array(
                                   "agri" => "Agriculture & Forestry",
                                   "arts" => "Arts & Entertainment",
                                   "auto" => "Automotive",
                                   "aero" => "Aviation & Aerospace",
                                   "bank" => "Banking & Finance",
                                   "bio" => "Biotechnology",
                                   "che" => "Chemicals",
                                   "cons" => "Construction",
                                   "good" => "Consumer Goods & Services",
                                   "space" => "Defense & Space",
                                   "edu" => "Education",
                                   "ene" => "Energy & Utilities",
                                   "engi" => "Engineering",
                                   "envi" => "Environmental Services",
                                   "fash" => "Fashion & Apparel",
                                   "food" => "Food & Beverages",
                                   "govt" => "Government & Public Sector",
                                   "heal" => "Healthcare & Pharmaceuticals",
                                   "tour" => "Hospitality & Tourism",
                                   "tech" => "Information Technology",
                                   "ins" => "Insurance",
                                   "legal" => "Legal Services",
                                   "manu" => "Manufacturing",
                                   "mari" => "Maritime",
                                   "mark" => "Marketing & Advertising",
                                   "media" => "Media & Communications",
                                   "mini" => "Mining & Metals",
                                   "non" => "Non-Profit & NGO",
                                   "prof" => "Professional Services",
                                   "real" => "Real Estate",
                                   "ret" => "Retail",
                                   "sports" => "Sports & Recreation",
                                   "tele" => "Telecommunications",
                                   "logi" => "Transport & Logistics",
                                   "other" => "Others (for industries not listed above)"
                                 );
                    $htmldata .= '<!-- list 1 -->
                    <table style="padding-top: 15px; width: 100%;" cellpadding="0" cellspacing="0">
                        <tr>

                            <td style="width: 100%;">
                                <table style="width: 100%;" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="width: 50%;">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="width: 30px;">
                                                        <img style="width: 20px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/suitcase.jpg" alt="">
                                                    </td>
                                                    <td style="width: 100%;">
                                                        <div>
                                                            <span style="font-size: 16px; font-weight: 500; color: #000000; line-height: 12px">'.$em_cmp_post.'</span>
                                                            <br style="line-height: 1;">
                                                            <span style="font-size: 12px; font-weight: 400; color: #4F4F4F; line-height: 10px">'.$emp_vals['emp_full_location'].'</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="width: 50%;">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="width: 30px;">
                                                        <img style="width: 20px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/suit-tie.jpg" alt="">
                                                    </td>
                                                    <td style="width: 100%;">
                                                        <div>
                                                            <span style="font-size: 16px; font-weight: 500; color: #000000; line-height: 12px">'.$em_post.'</span>
                                                            <br style="line-height: 1;">
                                                            <span style="font-size: 12px; font-weight: 400; color: #4F4F4F; line-height: 12px">'.get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' to '.$get_present_not.'</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>';

                if($emp_placeType != ''){
                    $htmldata .= '
                    <table style="padding-top: 6px; width: 100%;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <span style="font-size: 16px; font-weight: 500; color: #000000;">Location: '.$emp_placeType.', '.$role_items.'</span>
                            </td>
                        </tr>
                    </table>';
                }

                if($emp_vals['emp_industry'] != ''){
                    $htmldata .= '
                    <table style="padding-top: 6px; width: 100%;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 100%;">
                                <span style="font-size: 16px; font-weight: 500; color: #000000;">Industry: '.$industry_arr[$emp_vals['emp_industry']].'</span>
                            </td>
                        </tr>
                    </table>';
                }

                if($emp_vals['skill'] != ''){
                    $htmldata .= '
                    <!-- skills -->
                    <table style="padding-top: 6px; width: 100%;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 40px; padding-right: 6px;">
                                <span style="font-size: 16px; font-weight: 500; color: #000000; line-height: 17px;">Skills:</span>
                            </td>
                            <td style="width: 100%;">';
                            $skills = $emp_vals['skill'];
                            $items = '';
                            foreach ($skills as $s_keys => $s_vals){
                                if(!is_array($s_vals)){
                                    $items = explode(':>',$s_vals);
                                    $htmldata .= ' <span style="font-size: 14px; line-height: 18px; font-weight: 400; color: #000000; background-color: #EFEFEF; border-radius: 12px;">&nbsp; &nbsp; '.$items[1].' &nbsp; &nbsp;</span>&nbsp;';
                                } else{
                                    foreach ($s_vals as $es_keys => $es_vals){
                                        $items = explode(':>',$es_vals);
                                        $htmldata .= ' <span style="font-size: 14px; line-height: 18px; font-weight: 400; color: #000000; background-color: #EFEFEF; border-radius: 12px;">&nbsp; &nbsp; '.$items[1].' &nbsp; &nbsp;</span>&nbsp;';
                                    }
                                }
                            }

                        $htmldata .= '
                            </td>
                        </tr>
                    </table>';
                }

                if(trim($emp_vals['emp_responsibilities']) != ''){
                    $htmldata .= '
                    <table style="padding-top: 6px; width: 100%;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <span style="font-size: 16px; font-weight: 500; color: #000000;">Responsibilities:</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%;">
                                <p style="font-size: 14px; color: #000000; text-align: left; line-height: 17px;">'.$emp_vals['emp_responsibilities'].'</p>
                            </td>
                        </tr>
                    </table>';
                }
                    $htmldata .= '
                    <!-- list 1 end -->';
                }

                $htmldata .= '
                </td>
            </tr>
        </table>
        <!-- Experience Details end  -->';
    }

    if($fun_fact != ''){
        $htmldata .= '
        <table style="padding-top: 15px; width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="border-bottom: 1px solid #d3d3d3;">
                    <table style="width: 100%; padding-bottom: 6px;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 16px; padding-right: 6px;">
                                <img style="width: 14px; height: 12px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/gear.jpg" alt="">
                            </td>
                            <td style="width: 100%;">
                                <span style="font-size: 16px; font-weight: 500;">Fun Fact</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td  style="padding-top: 6px;">
                    <div>';
                    $htmldata .= '  <span style="font-size: 16px; line-height: 18px; font-weight: 400; color: #000000; border-radius: 12px;"> '.taoh_title_desc_decode($fun_fact).' </span>';
                    $htmldata .= '
                        <!-- <br><br> -->
                    </div>
                </td>
            </tr>
        </table>';
    }


    if($hobbies != '' && count($hobbies) > 0){
        $htmldata .= '
        <table style="padding-top: 15px; width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="border-bottom: 1px solid #d3d3d3;">
                    <table>
                        <tr>
                            <td style="width: 16px; padding-right: 6px;">
                                <img style="width: 14px; height: 14px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/gear.jpg" alt="">
                            </td>
                            <td style="width: 100%;">
                                <span style="font-size: 16px; font-weight: 500;">Hobbies & Interests</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td style="padding-top: 6px;">
                    <div>';
                    foreach ($hobbies as $f_keys => $f_vals){
                        $htmldata .= '  <span style="font-size: 14px; line-height: 18px; font-weight: 400; color: #000000; background-color: #AFE7EF; border-radius: 12px;">&nbsp; &nbsp; '.(PROFESSIONAL_HOBBIES[$f_vals] ?? "").' &nbsp; &nbsp;</span>&nbsp;';
                    }
                    $htmldata .= '
                        <!-- <br><br> -->
                    </div>
                </td>
            </tr>
        </table>';
    }

    if($data_keywords != '' && count($data_keywords) > 0){
        $htmldata .= '
        <table style="padding-top: 15px; width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="border-bottom: 1px solid #d3d3d3;">
                    <table>
                        <tr>
                            <td style="width: 16px; padding-right: 6px;">
                                <img style="width: 14px; height: 14px;" src="'.TAOH_SITE_URL_ROOT.'/assets/images/club-peo.jpg" alt="">
                            </td>
                            <td style="width: 100%;">
                                <span style="font-size: 16px; font-weight: 500;">Club Information</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td style="padding-top: 6px;">
                    <div>';

                    foreach ($data_keywords as $k => $keyword) {
                        if(!empty($keyword)){
                            $htmldata .= ' <span style="font-size: 14px; line-height: 30px; font-weight: 400; color: #ffffff; background-color: #000000; border-radius: 12px;">&nbsp; &nbsp; '.$keyword.' &nbsp; &nbsp;</span> &nbsp;';
                        }
                    }
             $htmldata .= '
                        <br><br>
                    </div>
                </td>
            </tr>
        </table>';
    }

$htmldata .= '
    </div>
</body>
</html>';