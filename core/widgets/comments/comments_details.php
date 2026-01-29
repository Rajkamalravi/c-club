<?php
$conttoken = $data['conttoken'];
// if ( taoh_user_is_logged_in() ){
  //$api = TAOH_SITE_CONTENT_GET."?mod=core&token=".taoh_get_dummy_token(1)."&conttoken=".$conttoken."&conttype=blog&type=comment&ops=get&cacheo=0&cache_remove=1";
  //$req = file_get_contents($api);
  $taoh_call = 'core.content.get';
  $taoh_vals = array(
    'mod' => 'core',
    'token'=>taoh_get_dummy_token(1),
    'conttoken' => $conttoken,
    'type' => 'comment',
    'conttype' => $data['conttype'],
    'ops'=>'get',
    //'cfcc5h'=> 1, //cfcache newly added
  );


 $cache_name = $data['conttype'] . "_comments_" . $data['conttoken'];

 $taoh_vals[ 'cache_name' ] = $cache_name;

  ksort($taoh_vals);

  function isJsonString($input) {
		// Must be a string
		if (!is_string($input)) {
			return false;
		}

		// Try to decode
		json_decode($input,true);

		// Check for JSON errors
		return json_last_error() === JSON_ERROR_NONE;
	}

  //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
  $comments = json_decode(taoh_apicall_get($taoh_call, $taoh_vals,'',1),true);
  $total = ( isset( $comments['output']['total'] ) )? $comments['output']['total']:0;
  if ( $total ){
    foreach($comments['output']['comment'] as $comments){
      if($comments['parentid'] == 0 || $comments['parentid'] == ''){
        $comment_array[$comments['commentid']] = $comments;
      } else {
        $comment_array[$comments['parentid']]['reply'][] = $comments;
      }
    }
  //echo '<pre>';print_r($comment_array);die();
  ?>
    <div class="card card-item adddiv light-dark">
        <div class="">
          <div id="accordion" class="generic-accordion">
            <div class="">
              <div class="card-header" id="headingOne">
                <button class="btn btn-link fs-15 collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                  <h4 class="py-2 fs-20"><?php echo $total; ?> <?php echo $data['label'].'(s)'; ?></h4>
                  <i class="la la-angle-down collapse-icon"></i>
                </button>
              </div>
              <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion" style="">
                <div class="card-body">
                    <?php
                    if (isset($comment_array) && is_array($comment_array)) {
                        foreach ($comment_array as $comment) {
                            $comment_avatar = !empty($comment['avatar_image']) ? $comment['avatar_image'] : (TAOH_OPS_PREFIX . '/avatar/PNG/128/' . (!empty($comment['avatar']) ? $comment['avatar'] : 'default') . '.png');
                            ?>
                                <div class="comment-section">
                                    <!-- Main Comment -->
                                    <div class="comment">
                                      <div class="comment-header">
                                        <img class="comment-avatar" src="<?= $comment_avatar; ?>" alt="avatar">
                                        <div class="comment-info">
                                          <span class="comment-name"><?php echo $comment['fname'] != '' ? htmlspecialchars($comment['fname']) : htmlspecialchars($comment['chat_name']); // chat_name ?></span>
                                          <span class="comment-date text-gray">
                                            <?php echo taoh_fullyear_convert($comment['date'], $convert = true); ?>
                                          </span>
                                        </div>
                                      </div>
                                      <p class="comment-text">
                                        <?php if(!isJsonString($comment['comment'])){
                                          echo taoh_title_desc_decode($comment['comment']);
                                        }else{
                                          $commentArr = json_decode($comment['comment'], true);
                                          $ask_comment = '';
                                          foreach($commentArr as $ckey => $comments){
                                            $ask_comment .= "<b>".$ckey ."</b>: ";
                                            if(is_array($comments)){
                                             $result_comments = array_map(function($subarray) {
                                                      return (is_array($subarray)) ? implode(', ', $subarray) : $subarray;
                                                  }, $comments);
                                              $ask_comment .= implode("<br>",$result_comments)."<br>";
                                            }else{
                                              $ask_comment .= $comments."<br>";
                                            }
                                          }
                                          echo taoh_title_desc_decode($ask_comment);
                                        }
                                      ?>
                                        <?php // echo taoh_title_desc_decode($comment['comment']); ?>
                                      </p>
                                      <?php if ( taoh_user_is_logged_in() ){ ?>
                                      <div class="comment-interactions">
                                          <span class="comment-reply text-primary" onclick="scrollToSection()" data-commentid="<?php echo htmlspecialchars($comment['commentid']); ?>" style="text-decoration: underline;" title="Reply?">Reply?</span>
                                      </div>
                                      <?php } ?>
                                      <!-- Reply to Main Comment -->
                                      <?php if (isset($comment['reply']) && is_array($comment['reply'])) {
                                        foreach ($comment['reply'] as $reply) {
                                      ?>
                                        <div class="comment-reply-section">
                                          <div class="comment">
                                              <div class="comment-header">
                                              <img class="comment-avatar" src="https://opslogy.com/avatar/PNG/128/<?php echo htmlspecialchars($reply['avatar']); ?>.png" alt="avatar">
                                                <div class="comment-info">
                                                  <span class="comment-name"><?php echo $reply['user_fname'] ? htmlspecialchars($reply['user_fname']) : htmlspecialchars($reply['chat_name']); // chat_name ?></span>
                                                  <span class="comment-date text-gray">
                                                    <?php
                                                      echo taoh_fullyear_convert($reply['date'],$convert=true);
                                                    ?>
                                                  </span>
                                                </div>
                                              </div>
                                              <p class="comment-text">
                                                <?php echo taoh_title_desc_decode($reply['comment']); ?>
                                              </p>
                                          </div>
                                        </div>
                                      <?php } }?>
                                    </div>
                                  </div>
                            <?php
                        }
                    }

                    ?>
                </div>
              </div>
            </div><!-- end card -->
          </div>
        </div><!-- end card-body -->
    </div>
  <?php
  }
// }
?>
<script>
  function showLoading(event){
    //alert();
    event.preventDefault();
    var textareaValue = document.getElementById("comment_value").value;
    //alert(textareaValue)
    if(textareaValue == null || textareaValue == ""){
      document.getElementById("commentresponseMessage").style.color = "red";
      document.getElementById("commentresponseMessage").innerHTML = "Please enter comment!";
      return false;
    }
    document.getElementById("commentresponseMessage").innerHTML = "";
    // Get the submit button that was clicked

    const submitButton = document.getElementById("comment_submit");
   // const submitButton = event.submitter;
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

    // Create a hidden input to hold the button name and value
    const hiddenInput = document.createElement("input");
    hiddenInput.type = "hidden";
    hiddenInput.name = submitButton.name;
    document.getElementById("command_form").appendChild(hiddenInput);

    // Submit the form
    setTimeout(function() {
        document.getElementById("command_form").submit();
    }, 1000); // Delay submission for demonstration purposes
  }

  function scrollToSection() {
    var commentid = event.target.getAttribute("data-commentid");
    document.querySelector(".parentid").value = commentid;
    document.getElementById("command_form").scrollIntoView({ behavior: "smooth" });
  }
</script>
