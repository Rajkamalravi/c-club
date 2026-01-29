<?php
//taoh_get_header();
taoh_get_header_iframe();
//$api = 'https://preapi.tao.ai/users.user.get?mod=taoai&token=hT93oaWC&ops=info&ptoken=oyeuyy9vnx1u';
$ptoken = taoh_parse_url(1);

$mod = 'users';
$ops = 'info';
$taoh_call = 'users.user.get';
//$cache_name = $mod.'_'.$ops.'_' . $ptoken . '_' . taoh_scope_key_encode( $ptoken, 'global' );
$cache_name = $mod.'_'.$ops.'_' . $ptoken;
$taoh_vals = array(
    'token' => taoh_get_dummy_token(1),
    'ops' => $ops,
    'mod' => $mod,
    'cache_name' => $cache_name,
    'cache_time' => 7200,
    //'cache' => array ( "name" => $cache_name,  "ttl" => 7200),
    'ptoken' => $ptoken,
    //'cfcc5h' => 1 //cfcache newly added

);
//$taoh_vals[ 'cfcache' ] = $cache_name;
ksort($taoh_vals);
//echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );
$return = taoh_apicall_get($taoh_call, $taoh_vals);
$data = json_decode($return,true);
//echo'<pre>';print_r($data);echo'</pre>';
$about_me = implode(' ',array_filter(explode(' ',$data['output']['user']['about_me'])));
$fun_fact = implode(' ',array_filter(explode(' ',$data['output']['user']['fun_fact'])));
$about_type = implode(' ',array_filter(explode(' ',$data['output']['user']['about_type'])));
$get_skill = $data['output']['user']['skill'][0]['name'];
$skill = explode(",",$get_skill);
?>
<style>
body{
    font-size:10px;
}
.media-card .media-body h5{
    font-size:10px;
}
.skill-link {
    color: #6c727c;
    background-color: powderblue;
    margin-right: 5px;
    margin-bottom: 7px;
    text-align: center;
    display: inline-block;
    font-size: 10px;
    line-height: 16px;
    padding: 7px 15px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 6px;
    -webkit-transition: all 0.2s;
    -moz-transition: all 0.2s;
    -ms-transition: all 0.2s;
    -o-transition: all 0.2s;
    transition: all 0.2s;
    /* border: 1px solid rgba(121, 127, 135, 0.05);*/
}
.prof-link {
    color: #fff;
    background-color: #131a4c;
    /* margin-right: 5px; */
    margin-bottom: 7px;
    text-align: center;
    /* display: inline-block; */
    font-size: 10px;
    line-height: 15px;
    padding: 7px 15px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 20px;
    -webkit-transition: all 0.2s;
    -moz-transition: all 0.2s;
    -ms-transition: all 0.2s;
    -o-transition: all 0.2s;
    transition: all 0.2s;
    /* border: 1px solid rgba(121, 127, 135, 0.05); */
}
.colored{
    height:150px;
    background-color:black;
    border-radius: 8px;
}
.profile{
    position:absolute;
    margin-top:-45px;
}
@media (min-width: 1280px){
  .container {
      max-width: 1021px;
  }
  }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<section class="blog-area pt-50px pb-80px">
    <div class="container">
        <div id="profile-loader">
            <div class="card card-item">
                <div class="card-body">
                <img id="loaderEmail" width="100" src="<?php echo TAOH_LOADER_GIF; ?>"/>

                </div>
            </div>
        </div>
        <div id="profile-content" style="display:none" class="media media-card p-0 mb-1">
                <div class="media-body">
                    <div class="media-card p-2 mb-0">

                        <div class="d-flex justify-content-between">
                        <div class="profile">
                            <img width="48" height="48" src="<?php echo TAOH_OPS_PREFIX.'/avatar/PNG/128/'.$data['output']['user']['avatar'].'.png';?>" alt="">
                        </div>
                            <div>
                                <span class="text-black mr-3"><?php echo $data['output']['user']['chat_name'];?></span>
                                <span class="prof-link"><?php echo $data['output']['user']['type'];?></span>
                            </div>

                        </div>
                        <div>
                            <div class="mt-1"><?php echo $data['output']['user']['full_location'];?></div>
                         </div>
                    </div>
                </div>
            </div>
            <?php if(!empty($about_me)){ ?>
            <div class="media media-card p-2 mb-1">
                <div class="media-body">
                    <div class="mb-2"><h5>About</h5></div>
                    <?php echo $about_me;?>
                </div>
            </div>
            <?php } ?>
            <?php if(!empty($fun_fact)){ ?>
            <div class="media media-card p-2 mb-1">
                <div class="media-body">
                    <div class="mb-2"><h5>Fun Fact</h5></div>
                    <?php echo $fun_fact;?>
                </div>
            </div>
            <?php } ?>
            <div class="media media-card p-2  mb-1">
                <div class="media-body">
                    <div class="mb-2"><h5>Skills</h5></div>
                        <?php foreach($skill as $keys => $vals){ ?>
                            <span class="skill-link"><?php echo $vals; ?></span>
                        <?php } ?>
                </div>
            </div>
            <?php if(!empty($about_type)){ ?>
            <div class="media media-card p-2  mb-1">
                <div class="media-body">
                    <div class="mb-2"><h5>About Profile Type</h5></div>
                    <?php echo $about_type;?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
<?php ///taoh_get_footer(); ?>
<script>
window.addEventListener("load", afterLoaded,false);
function afterLoaded(){
	$('#profile-content').show();
    $('#profile-loader').hide();
}


</script>