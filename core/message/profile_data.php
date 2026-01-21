<?php
if (!empty($user_data['ptoken'])):


    if (!isset($user_data['ptoken'])) {
        echo 'Invalid Profile!';
        die();
    }

    $about_type = '';
    $about_me = '';
    $fun_fact = '';

    // && stripos(@get_headers($user_data['avatar_image'])[0], "200 OK")
    $avatarSrc = !empty($user_data['avatar_image']) ? $user_data['avatar_image'] : (TAOH_OPS_PREFIX . '/avatar/PNG/128/' . (!empty($user_data['avatar']) ? $user_data['avatar'] : 'default') . '.png');

    $about_me = implode(' ', array_filter(explode(' ', $user_data['aboutme'])));
    $fun_fact = implode(' ', array_filter(explode(' ', $user_data['funfact'])));
    $user_ptoken = $user_data['ptoken'];
    if (isset($user_data['about_type']))
        $about_type = implode(' ', array_filter(explode(' ', $user_data['about_type'])));
    $get_skill = $user_data['skill'];

    if (!taoh_user_is_logged_in()) {
        $about_me = substr($about_me, 0, 100);
        $fun_fact = substr($fun_fact, 0, 100);
        $about_type = substr($about_type, 0, 100);
    }

    if (isset($data['output']['user']['full']['education']) && is_array($user_data['education'])) {
        $edu_encode = json_encode($user_data['education']);
        $edu_list = json_decode($edu_encode, true);
        $edu_tot_count = array_key_last($edu_list) + 1;
        $edu_last_key = array_key_last($edu_list);
    } else {
        $edu_tot_count = 0;
        $edu_last_key = 0;
        $edu_list = [];
    }
    if (isset($data['output']['user']['full']['employee']) && is_array($user_data['employee'])) {
        $emp_encode = json_encode($user_data['employee']);
        $emp_list = json_decode($emp_encode, true);
        $emp_tot_count = array_key_last($emp_list) + 1;
        $emp_last_key = array_key_last($emp_list);
    } else {
        $emp_tot_count = 0;
        $emp_last_key = 0;
        $emp_list = [];
    }

    ?>

    <div>
        <div class="media media-card">
            <div class="media-body">
                <div class="row">
                    <div class="col-lg-3 pr-0">
                        <img src="<?php echo $avatarSrc;?>" class="chat-user-avatar" alt="avatar">
                    </div>
                    <div class="col-lg-9 pl-2">
                        <span class="text-black mr-3"><?php echo $user_data['chat_name'];?></span><br>
                        <span class="prof-link"><?php echo $user_data['type'] ?? 'professional'; ?>
                        </span>
                    </div>
                </div>
                <div class="mt-1"><?php echo $user_data['full_location'];?></div>

                <div>
                    <div class="accordion collapsed" data-toggle="collapse" data-target="#profileMoreInfo" aria-expanded="false" aria-controls="profileMoreInfo" style="align-items:flex-end;display:flex;justify-content:flex-end;cursor:pointer">
                        <span>Show More &nbsp;<span class="accicon"><i class="fas fa-angle-down rotate-icon"></i></span></span>
                    </div>
                    <div id="profileMoreInfo" class="collapse">
                        <div>
                            <?php if(!empty($about_me)){ ?>
                                <div class="media media-card">
                                    <div class="media-body">
                                        <div class="mb-2"><h5 class="media-card-title">About</h5></div>
                                        <div class="mt-2 mb-2">
                                            <?php echo $about_me; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if(!empty($fun_fact)){ ?>
                                <div class="media media-card">
                                    <div class="media-body">
                                        <div class="mb-2"><h5 class="media-card-title">Fun Fact</h5></div>
                                        <div class="mt-2 mb-2">
                                            <?php echo $fun_fact; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if(!empty($get_skill)){ ?>
                                <div class="media media-card">
                                    <div class="media-body">
                                        <div class="mb-2"><h5 class="media-card-title">Skills</h5></div>
                                        <div class="mt-2 mb-2">
                                            <?php foreach($get_skill as $keys => $vals){
                                                if(!empty($vals['value'])){?>
                                                    <span class="skill-link"><?php echo $vals['value']; ?></span>
                                                <?php } } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if(!empty($about_type)){ ?>
                                <div class="media media-card">
                                    <div class="media-body">
                                        <div class="mb-2"><h5 class="media-card-title">About Profile Type</h5></div>
                                        <div class="mt-2 mb-2">
                                            <?php echo $about_type; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if(is_array($emp_list)){

                                if(is_array($emp_list[0]['title'])){ ?>
                                    <div class="media-card">
                                        <div class="mb-5">
                                            <h5 class="media-card-title float-left">Experience</h5>
                                        </div>
                                        <?php
                                        foreach($emp_list as $emp_keys => $emp_vals){

                                            $em_title = ($emp_vals['emp_title'])?$emp_vals['emp_title'] : $emp_vals['title'];
                                            foreach ( $em_title as $em_key => $em_value ){
                                                list ( $em_pre, $em_post ) = explode( ':>', $em_value );
                                            }
                                            $em_company = ($emp_vals['emp_company'])?$emp_vals['emp_company'] : $emp_vals['company'];
                                            foreach ( $em_company as $em_cmp_key => $em_cmp_value ){
                                                list ( $em_cmp_pre, $em_cmp_post ) = explode( ':>', $em_cmp_value );
                                            }
                                            $get_present_not = ($emp_vals['current_role'] == 'on')?' Present':get_month_from_number($emp_vals['emp_end_month']).' '.$emp_vals['emp_year_end'];

                                            $emp_placeType = $emp_vals['emp_placeType'];
                                            if ($emp_placeType == 'rem') {
                                                $emp_placeType = ' . ' . 'Remote';
                                            } else if ($emp_placeType == 'ons') {
                                                $emp_placeType = '. ' . 'Onsite';
                                            } else if ($emp_placeType == 'hyb') {
                                                $emp_placeType = '. ' . 'Hybrid';
                                            } else {
                                                $emp_placeType = '';
                                            }

                                            $skills = $emp_vals['skill'];
                                            $items = '';
                                            foreach ($skills as $s_keys => $s_vals){
                                                $items = explode(':>',$s_vals);
                                            }

                                            $roletype_arr = array("remo" => "Remote Work", "full" => "Full Time", "part" => "Part Time", "temp" => "Temporary", "free" => "Freelance", "cont" => "Contract", "pdin" => "Paid Internship", "unin" => "Unpaid Internship", "voln" => "Volunteer",);
                                            $roletype = $emp_vals['emp_roletype'];
                                            $role_items = '';
                                            foreach ($roletype as $key => $value){
                                                $role_items = ' . '.$roletype_arr[$value];
                                            }
                                            ?>

                                            <div class="mt-5 mb-2">
                                                <div class="d-flex mt-3">
                                                    <div style="height:45px;width:45px;" class="media-img d-block">
                                                        <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/work.png'; ?>" alt="company logo">
                                                    </div>
                                                    <div class="media-body border-left-0 emp_response">
                                                        <div>
                                                            <h5 class="mb-1 fs-16 fw-medium float-left"><a><?php echo $em_post; ?></a></h5>


                                                        </div><br>

                                                        <p class="lh-20 fs-13 font-weight-bold"><?php echo $em_cmp_post.$role_items; ?></p>
                                                        <p class="lh-20 fs-13"><?php echo get_month_from_number($emp_vals['emp_start_month']).' '.$emp_vals['emp_year_start'].' - '.$get_present_not ?> .
                                                            <span><?php echo get_diff_dates($emp_vals['emp_year_start'],$emp_vals['emp_start_month'],$emp_vals['emp_year_end'],$emp_vals['emp_end_month']); ?></span></p>
                                                        <p class="mb-2 lh-20 fs-13"><?php echo $emp_vals['emp_full_location'].$emp_placeType; ?></p>
                                                        <p class="lh-20 mb-3 fs-13"><?php echo (strlen($emp_vals['emp_responsibilities'])<=200)?$emp_vals['emp_responsibilities']:mb_substr($emp_vals['emp_responsibilities'], 0, 200).'......'; ?></p>
                                                        <?php if(is_array($emp_vals['skill'])){?>
                                                            <p class="lh-20 fs-13"><span class="lh-20 fs-13 font-weight-bold">Skills: </span><?php echo $items[1]; ?></p>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php } ?>
                                    </div>
                                <?php }
                            }?>

                            <?php if(is_array($edu_list)){
                                if(is_array($edu_list[0]['company'])){?>
                                    <div class="media-card">
                                        <div class="mb-5">
                                            <h5 class="media-card-title float-left">Education</h5>

                                        </div>
                                        <?php foreach($edu_list as $edu_keys => $edu_vals){
                                            $ed_name = $edu_vals['company'];
                                            foreach ( $ed_name as $ed_key => $ed_value ){
                                                list ( $ed_pre, $ed_post ) = explode( ':>', $ed_value );
                                            }

                                            $degeree_arr = array(
                                                "highschool" => "High School Diploma or GED",
                                                "vocational" => "Vocational/Technical Diploma",
                                                "associate" => "Associate Degree",
                                                "bachelor" => "Bachelor's Degree",
                                                "master" => "Master's Degree",
                                                "doctorate" => "Doctorate or Professional Degree",
                                                "other" => "Other (for degeree not listed above)"
                                            );
                                            $degree_get = $edu_vals['edu_degree'];
                                            $degree_items = '';
                                            foreach ($degree_get as $d_key => $d_value){
                                                $degree_items = $degeree_arr[$d_value];
                                            }

                                            $d_skills = $edu_vals['skill'];
                                            $d_items = '';
                                            foreach ($d_skills as $d_keys => $d_vals){
                                                $d_items = explode(':>',$d_vals);
                                            }
                                            ?>

                                            <div class="mt-5 mb-2">
                                                <div class="d-flex mt-3">
                                                  <span style="height:45px;width:45px;" class="media-img d-block">
                                                      <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/education.png'; ?>" alt="company logo">
                                                  </span>

                                                    <div class="media-body border-left-0">
                                                        <div>
                                                            <h5 class="mb-1 fs-16 fw-medium float-left"><a><?php echo $ed_post; ?></a></h5>
                                                        </div><br>
                                                        <p class="lh-20 fs-13 font-weight-bold"><?php echo $degree_items.', '.$edu_vals['edu_specalize']; ?></p>
                                                        <p class="lh-20 fs-13"><?php echo get_month_from_number($edu_vals['edu_start_month']).' '.$edu_vals['edu_start_year'].' - '.get_month_from_number($edu_vals['edu_end_month']).' '.$edu_vals['edu_complete_year']; ?></p>
                                                        <?php if($edu_vals['edu_grade'] != ''){?>
                                                            <p class="lh-20 fs-13 font-weight-bold"><span class="lh-20 fs-13">Grade: </span><?php echo $edu_vals['edu_grade']; ?></p>
                                                        <?php }?>
                                                        <?php if($edu_vals['edu_activities'] != ''){?>
                                                            <p class="mt-2 lh-20 fs-13"><span class="lh-20 fs-13 font-weight-bold">Activities and societies: </span><?php echo $edu_vals['edu_activities']; ?></p>
                                                        <?php }?>
                                                        <p class="mt-2 lh-20 fs-13"><?php echo (strlen($edu_vals['edu_description'])<=200)?$edu_vals['edu_description']:mb_substr($edu_vals['edu_description'], 0, 200).'......'; ?></p>
                                                        <?php if(is_array($edu_vals['skill'])){?>
                                                            <p class="mt-2 lh-20 fs-13"><span class="lh-20 fs-13 font-weight-bold">Skills: </span><?php echo $d_items[1]; ?></p>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php } ?>
                                    </div>
                                <?php }
                            }?>

                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>

<?php
endif;
?>