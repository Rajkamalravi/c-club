<?php
//echo"<br>=====clubKey=====>".TAOH_CLUBKEY;
//if(taoh_user_is_logged_in()) {
if(1) {
  //$api = TAOH_SITE_WEMET_GET."?mod=tao_tao&token=".TAOH_API_TOKEN;
  //$wemet_url = taoh_url_get_content($api);
  //commented below since we need to make this video widget without login
 
  $roomkey = 'b91267c5';
  //$roomkey = TAOH_CLUBKEY;
  ?>

  <div class="card card-item">
      <div class="card-body">
          <h3 class="fs-17 pb-3">Your Video Chat Room</h3>
          <div class="divider"><span></span></div><p>&nbsp;</p>
          <!--<h3 class="fs-15 pb-3 text-success"><span class="dropdown-toggle">via Google Meet</span></h3>-->
          <ul class="menu-video-main">
            <li class="">
              <h3 class="fs-15 pb-3 text-success"><span class="dropdown-toggle" id="chat_type">via Google Meet</span></h3>
              <ul class="sub-menu">
              <li><a class="options_sections active" onclick="loadChatType('google',this);" href="javascript:void(0);">via Google Meet</a></li>
              <li><a class="options_sections" onclick="loadChatType('zoom',this);" href="javascript:void(0);">via Zoom</a></li>
              <li><a class="options_sections" onclick="loadChatType('teams',this);" href="javascript:void(0);">via Teams</a></li>
              </ul>
            </li>
          </ul>
          
          <div class="chat_section" id="google_section">
            <div class="fs-11 text-dark" >
              
              1. Click Chat now button to open your Google meet in the new tab.<br>
              2. Share your google meet link with other member.<br>
              3. Engage via Google meet chat room.<br>
            <a class="badge bg-success text-white fs-14" 
            target=_BLANK type="button" href="<?php echo "https://meet.google.com/new"; ?>"><?php echo "Chat Now &gt;"; 
            ?></a>
           
          </div></div>

          <div class="chat_section" id="zoom_section" style="display:none" >
            <div class="fs-11 text-dark" >
              
              1. Click Chat now button to open your zoom account in the new tab. <br>
              2. Click Host option from the top menu to launch the meeting.<br>
              3. Click Participants from the bottom menu to copy your meeting link and share with other member to engage.<br>
            
              <a class="badge bg-success text-white fs-14" 
            target=_BLANK type="button" href="<?php echo "https://zoom.us/join"; ?>"><?php echo "Chat Now &gt;"; 
            ?></a>
          </div></div>

          <div class="chat_section" id="teams_section" style="display:none">
            <div class="fs-11 text-dark" >
              
              1. Click Chat now button to open your Teams account in the new tab.<br>
              2. Create the meeting in Teams by clicking calendar option in menu.<br>
              3. Go to Calendar. on the left side of Teams and select the scheduled meeting.<br>
              4. The meeting link will appear as a URL. Copy it and share it with the other member to engage.<br>
            
            <a class="badge bg-success text-white fs-14" 
            target=_BLANK type="button" href="<?php echo "https://www.microsoft.com/en-in/microsoft-teams/join-a-meeting"; ?>"><?php echo "Chat Now &gt;"; 
            ?></a>
          </div></div>

          <span style="font-size:10px"><font >*You need to be signed into Google meet, Zoom or Teams to use these options.</font></span>

          <!-- </div> -->
      </div>
  </div><!-- end card -->

  <script>
  function loadChatType(type,param){
    //alert($(param).text());
      var sec = type+'_section';
      
      $('.options_sections').removeClass('active');
      $('.chat_section').hide();
      $('#'+sec).show();
      $(param).addClass('active');
      $('#chat_type').html($(param).text())


  }
  function CopyToClipboard(id){
    var r = document.createRange();
    r.selectNode(document.getElementById(id));
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(r);
    document.execCommand('copy');
    window.getSelection().removeAllRanges();
  }
  </script>
<?php 
} 
?>
