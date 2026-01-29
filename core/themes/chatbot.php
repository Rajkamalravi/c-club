<?php
$share_link  = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$taoh_home_url = ( defined( 'TAOH_PAGE_URL' ) && TAOH_PAGE_URL ) ? TAOH_PAGE_URL:TAOH_SITE_URL_ROOT;
$app_url =  taoh_parse_url(0);
$app_action = taoh_parse_url(1);

$current_page = $app_url != 'stlo' ? $app_url : 'club';
$current_page = $app_action == 'room' ? 'networking' : $current_page;

/* $data = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
$ptoken = $data->ptoken; */
// echo $app_url.'===='.$app_action.'===='.$taoh_home_url;
?>
<style>
.supportChatbot, .sideChatbot, .obviousChatbot {
  max-height: 210px;
  overflow-y: auto; /* or scroll */
  scrollbar-width: thin;
}
</style>
<div class="chatbot-acc" id="dojo_bot_show" style="right:0;display: none;">
    <div style="" class="accordion" id="accordionExample">
        <div class="card" style="overflow: unset; border-radius: 8px;">
            <!-- side-kick-svg.svg -->
            <div class="card-header align-items-center" id="headingOne">
                <div class="title-con">
                    <img class="bot-img bot-logo" src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/Group 194.svg';?>" alt="">
                    <div>
                        <h6 class="bot-title mb-0"><?php echo TAOH_JUSASK_SUPPORT_BOT_TITLE; ?></h6>
                        <!-- <p class="bot-description mb-0">AI-Powered Career Coach assists you with everything around career !</p> -->
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end" style="flex: 1; gap: 2px;">
                    <div class="dropdown text-right">
                        <button class="btn dropdown-toggle text-white py-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" data-toggle="collapse" data-target="#dojo_support_Form" aria-expanded="true" aria-controls="collapseOne" id="dojo_support_form_link">Support Dojo</a>
                            <a class="dropdown-item" href="#" data-toggle="collapse" data-target="#sidekick_Form" aria-expanded="true" aria-controls="collapseOne" id="sidekick_form_link">Sidekick</a>
                            <a class="dropdown-item" href="#" data-toggle="collapse" data-target="#obvious_baba_Form" aria-expanded="true" aria-controls="collapseOne" id="obvious_form_link">Obvious Baba</a>
                        </div>
                    </div>
                    <button class="btn p-0" type="button" onclick="side_closeForm()"> <!--  data-toggle="collapse" data-target="#dojo_support_Form" aria-expanded="true" aria-controls="collapseOne" -->
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.95898 9.26562H12.5723" stroke="white" stroke-width="1.24" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9.26667 17.5333C13.8133 17.5333 17.5333 13.8133 17.5333 9.26667C17.5333 4.72 13.8133 1 9.26667 1C4.72 1 1 4.72 1 9.26667C1 13.8133 4.72 17.5333 9.26667 17.5333Z" stroke="white" stroke-width="1.24" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                    </button>
                </div>
            </div>

            <div id="dojo_support_Form" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class="card-body px-2">
                    <div class="user-chatarea-messages" style="max-height: 210px; scrollbar-width: thin;">
                        <!-- <div class="acc-user-chat">
                            <img class="user-img" src="<?php #echo TAOH_SITE_URL_ROOT.'/assets/images/profile_room_1.png';?>" alt="">
                            <p class="chat-message">What is a super perfect resume !</p>

                            <span class="time-stamp">7:20</span>
                        </div> -->
                        <div class ="supportChatbot pr-2">
                        <div class="bot-chat">
                            <!-- <img class="bot-img" src="<?php #echo TAOH_SITE_URL_ROOT.'/assets/images/Group 194.svg';?>" alt=""> -->
                            <p class="chat-message"><?php echo TAOH_JUSASK_SUPPORT_BOT_MSG1; ?></p>

                            <!-- <span class="time-stamp">7:20</span> -->
                        </div>
                        </div>
                    </div>
                    <div class="chat-form py-2 align-items-center">
                        <input type="text" class="mb-0 form-control support_ask" placeholder="Type your message here">
                        <button type="submit" class="btn support_btn d-flex align-items-center" style="gap: 6px;" id="sendSupport" >
                            <i></i>
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.04768 2.03133L13.1239 5.56946C16.2983 7.15666 16.2983 9.75239 13.1239 11.3396L6.04768 14.8777C1.28608 17.2585 -0.656589 15.3076 1.72421 10.5543L2.44341 9.12413C2.62528 8.76039 2.62528 8.15692 2.44341 7.79319L1.72421 6.35479C-0.656589 1.60146 1.29434 -0.349475 6.04768 2.03133Z" stroke="#4361EE" stroke-width="1.24" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="sidekick_Form" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample" style="background-color: transparent;">
                <div class="card-body px-2">
                    <div class="user-chatarea-messages" style="max-height: 210px; scrollbar-width: thin;">
                        <!-- <div class="acc-user-chat">
                            <img class="user-img" src="<?php #echo TAOH_SITE_URL_ROOT.'/assets/images/profile_room_1.png';?>" alt="">
                            <p class="chat-message">What is a super perfect resume !</p>

                            <span class="time-stamp">7:20</span>
                        </div> -->
                        <div class ="sideChatbot pr-2">
                        <div class="bot-chat">
                            <!-- <img class="bot-img" src="<?php #echo TAOH_SITE_URL_ROOT.'/assets/images/side-kick-svg.svg';?>" alt=""> -->
                            <p class="chat-message"><?php echo TAOH_JUSASK_BOT_1_MSG1; ?></p>

                            <!-- <span class="time-stamp">7:20</span> -->
                        </div>
                        </div>
                    </div>
                    <div class="chat-form py-2 align-items-center">
                        <input type="text" class="mb-0 form-control side_ask" placeholder="<?php echo TAOH_JUSASK_SUPPORT_BOT_NAME; ?>">
                        <button type="submit" class="btn side_btn d-flex align-items-center" style="gap: 6px;" id="sendSupport" >
                            <i></i>
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.04768 2.03133L13.1239 5.56946C16.2983 7.15666 16.2983 9.75239 13.1239 11.3396L6.04768 14.8777C1.28608 17.2585 -0.656589 15.3076 1.72421 10.5543L2.44341 9.12413C2.62528 8.76039 2.62528 8.15692 2.44341 7.79319L1.72421 6.35479C-0.656589 1.60146 1.29434 -0.349475 6.04768 2.03133Z" stroke="#4361EE" stroke-width="1.24" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="obvious_baba_Form" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class="card-body px-2">
                    <div class="user-chatarea-messages" style="max-height: 210px; scrollbar-width: thin;">
                        <!-- <div class="acc-user-chat">
                            <img class="user-img" src="<?php #echo TAOH_SITE_URL_ROOT.'/assets/images/profile_room_1.png';?>" alt="">
                            <p class="chat-message">What is a super perfect resume !</p>

                            <span class="time-stamp">7:20</span>
                        </div> -->
                        <div class ="obviousChatbot pr-2">
                        <div class="bot-chat">
                            <!-- <img class="bot-img" src="<?php #echo TAOH_SITE_URL_ROOT.'/assets/images/Obviousbaba.svg';?>" alt=""> -->
                            <p class="chat-message"><?php echo TAOH_JUSASK_BOT_2_MSG1; ?></p>

                            <!-- <span class="time-stamp">7:20</span> -->
                        </div>
                        </div>
                    </div>
                    <div class="chat-form py-2 align-items-center">
                        <input type="text" class="mb-0 form-control obvious_ask" placeholder="<?php echo TAOH_JUSASK_SUPPORT_BOT_NAME; ?>">
                        <button type="submit" class="btn obvious_btn d-flex align-items-center" style="gap: 6px;" id="sendSupport" >
                            <i></i>
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.04768 2.03133L13.1239 5.56946C16.2983 7.15666 16.2983 9.75239 13.1239 11.3396L6.04768 14.8777C1.28608 17.2585 -0.656589 15.3076 1.72421 10.5543L2.44341 9.12413C2.62528 8.76039 2.62528 8.15692 2.44341 7.79319L1.72421 6.35479C-0.656589 1.60146 1.29434 -0.349475 6.04768 2.03133Z" stroke="#4361EE" stroke-width="1.24" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php if((taoh_user_is_logged_in() && (TAOH_ENABLE_OBVIOUSBABA || TAOH_ENABLE_SIDEKICK || TAOH_ENABLE_JUSASK))) { // ($app_action != 'room' && $app_url != 'message' ) &&  ?>
<!-- animated menu -->
<div class="d-none d-sm-block animated-menu menu-container">
    <!--<input type="checkbox" id="toggle1" class="menu-toggle1" />-->
    <label for="toggle1" class="menu-button">
        <img onclick="side_openForm(event)" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/Group 194.svg" alt="Menu" />
    </label>
</div>
<?php } ?>
<script>
    var chat_ask = '';
    var bot_name = '';
    var bot_desc = '';
    var ask_bot = '';
    var chatarea = '';

    $(document).ready(function(){
        $('.support_ask').on('keypress', function (e) {
            if (e.which === 13) { // 13 is the Enter key
                e.preventDefault();
                $('.support_btn').click();
            }
        });
        $('.side_ask').on('keypress', function (e) {
            if (e.which === 13) { // 13 is the Enter key
                e.preventDefault();
                $('.side_btn').click();
            }
        });
        $('.obvious_ask').on('keypress', function (e) {
            if (e.which === 13) { // 13 is the Enter key
                e.preventDefault();
                $('.obvious_btn').click();
            }
        });
        $(".support_btn").click(function(){
            var open_ticket = $('#open_ticket').val();
            $('.support_ask').css('border','1px solid #F1F1F1');
            if($('.support_ask').val() == ''){
                $('.support_ask').css('border','1px solid red !important');
                return false;
            }
            chat_ask = $('.support_ask').val();
            bot_name = '<?php echo TAOH_JUSASK_SUPPORT_BOT_ASK; ?>';
            bot_desc = '<?php echo TAOH_JUSASK_SUPPORT_BOT_DESCRIPTION; ?>';
            ask_bot = '<?php echo TAOH_JUSASK_SUPPORT_BOT_ASK; ?>';
            chatarea = $('.supportChatbot');
            chaticon = '<?php echo TAOH_JUSASK_SUPPORT_BOT_IMG; ?>';
            bot_btn = 'support_btn';
            taoh_jusask_chat_init(chat_ask,bot_name,bot_desc,ask_bot,chatarea,chaticon,open_ticket,bot_btn);
        });

        $(".side_btn").click(function(){
            if($('.side_ask').val() == ''){
                return false;
            }
            chat_ask = $('.side_ask').val();
            bot_name = '<?php echo TAOH_JUSASK_BOT_1_NAME; ?>';
            bot_desc = '<?php echo TAOH_JUSASK_BOT_1_DESCRIPTION; ?>';
            ask_bot = '<?php echo TAOH_JUSASK_BOT_1_ASK; ?>';
            chatarea = $('.sideChatbot');
            chaticon = '<?php echo TAOH_JUSASK_BOT_1_IMG; ?>';
            bot_btn = 'side_btn';
            taoh_jusask_chat_init(chat_ask,bot_name,bot_desc,ask_bot,chatarea,chaticon,'',bot_btn);
        });

        $(".obvious_btn").click(function(){
            if($('.obvious_ask').val() == ''){
                return false;
            }
            chat_ask = $('.obvious_ask').val();
            bot_name = '<?php echo TAOH_JUSASK_BOT_2_NAME; ?>';
            bot_desc = '<?php echo TAOH_JUSASK_BOT_2_DESCRIPTION; ?>';
            ask_bot = '<?php echo TAOH_JUSASK_BOT_2_ASK; ?>';
            chatarea = $('.obviousChatbot');
            chaticon = '<?php echo TAOH_JUSASK_BOT_2_IMG; ?>';
            bot_btn = 'obvious_btn';
            taoh_jusask_chat_init(chat_ask,bot_name,bot_desc,ask_bot,chatarea,chaticon,'',bot_btn);
        });

        $('.dropdown-item').click(function(){
            const botTitleMap = {
                'dojo_support_form_link': '<?php echo TAOH_JUSASK_SUPPORT_BOT_TITLE; ?>',
                'sidekick_form_link': '<?php echo TAOH_JUSASK_BOT_1_TITLE; ?>',
                'obvious_form_link': '<?php echo TAOH_JUSASK_BOT_2_TITLE; ?>'
            };
            const botLogoMap = {
                'dojo_support_form_link': 'Group 194.svg',
                'sidekick_form_link': 'side-kick-svg.svg',
                'obvious_form_link': 'Obviousbaba.svg'
            };
            const selectedText = $(this).attr('id');
            const newTitle = botTitleMap[selectedText];
            const newLogo = botLogoMap[selectedText];
            $('.bot-title').text(newTitle);
            $('.bot-logo').attr('src','<?php echo TAOH_SITE_URL_ROOT.'/assets/images/';?>'+newLogo);
        });
    });

    function side_openForm(event) {
       //$('.input_block').show();
       event.stopPropagation();
        $("#toggle").prop('checked', false);
        $(".menu-button").addClass('d-none');
        document.getElementById("dojo_bot_show").style.display = "block";
        var targetTab = $('#dojo_support_Form');
        if (!targetTab.hasClass('show')) {
            $("#dojo_support_form_link").click();
        }
    }

    function side_closeForm() {
        $(".menu-button").removeClass('d-none');
        document.getElementById("dojo_bot_show").style.display = "none";
    }


function taoh_jusask_chat_init(chat_ask,bot_name,bot_desc,ask_bot,chatarea,chaticon,contact_support=0,bot_btn='') {
    $('.chatbot_load').show();
    $("."+bot_btn).addClass('disabled');
    let submit_btn_icon = $("."+bot_btn).find('i');
    submit_btn_icon.addClass('fa fa-spinner fa-spin');

      var data = {
        'taoh_action': 'taoh_all_chatbot_get',
        'ask' : chat_ask,
        'ops' : ask_bot,
        'bot' : bot_name,
        'bot_desc' : bot_desc,
        'send_to_support' : contact_support,
        'current_page' : '<?php echo $current_page;  // $share_link ;?>',
      };
      jQuery.post('<?php echo taoh_site_ajax_url(); ?>', data, function(response) {
        console.log(response)
        $('.chatbot_load').hide();
        render_jusask_chat_template(response, chatarea, bot_name,chaticon);
        $("."+bot_btn).removeClass('disabled');
        let submit_btn_icon = $("."+bot_btn).find('i');
        submit_btn_icon.removeClass('fa fa-spinner fa-spin');

      }).fail(function() {
        $('.chatbot_load').hide();
        console.log( "Network issue!" );
    })
}

function render_jusask_chat_template(data, slot, bot_name,chaticon) {
    //slot.empty();
    $('#send_ticket_block').hide();
    if(data.length === 0) {
        slot.append("<p>No data found!</p>");
    } else {
        $.each(data, function(i, v){
        var bot_pad = '';
        if(v.bot != 'LifeCoach Chatbot'){
            bot_pad = 'padding-right: 5px;';
        }
        var open_ticket = '';
        $('#support_default_text').removeClass('d-flex');
        $('#support_default_text').hide();

        if(v.open_ticket_flag != null && v.open_ticket_flag != undefined){
            if(v.open_ticket_flag == 1){
              $('#send_ticket_block').show();
            }
            else if(v.open_ticket_flag == 2){
              $('#send_ticket_block').hide();
              $('.input_block').hide();


            }
            $('.support_btn').css('margin-top','35px');
        }
        // alert(bot_name);
        if(v.bot != 'user'){
            if(bot_name == '<?php echo TAOH_JUSASK_SUPPORT_BOT_ASK; ?>'){
                img_name = 'Group 194.svg';
            }else if(bot_name == '<?php echo TAOH_JUSASK_BOT_1_NAME; ?>'){
                img_name = 'side-kick-svg.svg';
            }else if(bot_name == '<?php echo TAOH_JUSASK_BOT_2_NAME; ?>'){
                img_name = 'Obviousbaba.svg';
            }

        //if(v.bot == bot_name){
            slot.append(`
                  <div class="bot-chat">
                      <!-- <img class="bot-img" src="<?php #echo TAOH_SITE_URL_ROOT.'/assets/images/';?>${img_name}" alt="${bot_name}"> -->
                      <p class="chat-message">${v.message}</p>
                  </div>
                `);
        }else{
            slot.append(`
              <div class="acc-user-chat">
                  <?php // echo taoh_get_profile_image(); ?>
                  <p class="chat-message mt-2">${v.message}</p>
              </div>
        `);
        }
        })
        //$("#card-body").animate({ scrollTop: 20000000 }, "slow");
        $('.obvious_ask').val('');
        $('.side_ask').val('');
        $('.support_ask').val('');
        console.log(bot_name)
        if(bot_name == '<?php echo TAOH_JUSASK_BOT_2_NAME; ?>'){
            var element_bot = $('.obviousChatbot');
        }else if(bot_name == '<?php echo TAOH_JUSASK_BOT_1_NAME; ?>'){
            var element_bot = $('.sideChatbot');
        }else if(bot_name == '<?php echo TAOH_JUSASK_SUPPORT_BOT_ASK; ?>'){
            var element_bot = $('.supportChatbot');
        }
        element_bot.animate({
            scrollTop: element_bot[0].scrollHeight
        }, 500);
    }
}
</script>