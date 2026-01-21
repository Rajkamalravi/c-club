<?php
$ptoken = $ptoken;
$get_user_info = json_decode(taoh_get_user_info($ptoken,'info'),true);
$user_info = $get_user_info['output']['user'];
$user_info = $user_info['full'];
$chat_name = $user_info['chat_name'];
$fname = $user_info['fname'];
if(isset($user_info['avatar_image']) && $user_info['avatar_image'] != ''){
    $avatar_image = $user_info['avatar_image'];
}else{
    if(isset($user_info['avatar']) && $user_info['avatar'] != 'default'){
        $avatar_image = TAOH_OPS_PREFIX.'/avatar/PNG/128/'.$user_info['avatar'].'.png';
    }else{
        $avatar_image = TAOH_OPS_PREFIX.'/avatar/PNG/128/avatar_def.png';
    }
}
$full_location = isset($user_info['full_location'])?$user_info['full_location']:'';
$full_url = TAOH_SITE_URL_ROOT.'/profile/'.$ptoken;
?>
<div class="open_window">
    <div id="owner_profile">
        <div class="card card-item light-dark-card">
            <div class="card-body">
                <h3 class="fs-17">Profile</h3>
                <div class="divider"><span></span></div>
                <div class="no-gutters" id="profile_info">
                    <div class="row pt-3">
                        <div class="col-auto">
                            <div class='comment-avatar mr-0' style="background:#52514f;">
                                <img width="40" class="lazy" src="<?php echo $avatar_image; ?>" alt="avatar">
                            </div>
                        </div>
                        <div class="col-lg-8 px-0">
                            <p class="fs-16 text-capitalize" id="chat_name"><strong><?php echo $fname; // $chat_name; ?></strong></p>
                            <a id="viewProfileClick1" target="_blank" href="<?php echo $full_url; ?>" style="cursor:pointer;color: #007bff;">View full profile</a>
                        </div>
                    </div>
                    <div class="row pt-1">
                        <div class="col-lg-12"><p><i class="la la-map-marker"></i><?php echo $full_location; ?></p></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>