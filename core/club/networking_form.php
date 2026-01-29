<?php
taoh_get_header();
$today = date("Y-m-d");
?>
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.error{
    color:red;
}

#network-apply .skill_select .ts-wrapper .ts-control .item {
    position: initial;
    width: auto;
    height: 21px;
    line-height: 19px;
    font-size: 14px;
    margin: 0 3px 1px 0 !important;
    padding-bottom: 0;
}
</style>
<section class="networks-area pt-40px pb-40px">
    <div class="container">
        <div class="row">
			<div class="col-lg-8 network-details-panel mt-30px mb-30px network_apply_form" id="network-apply">
				<form id="networking_form" method="POST" enctype="multipart/form-data">
					<div class="hidden">
                        <input type="hidden" value="tc2asi3iida2" name="opscode">
					</div>
					<div class="mb-40px">
						<h5 class="fs-20 text-uppercase mb-3 text-black">Create Room</h5>
                        <div class ="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Room Title <span style="color:red"> * </span></label>
                                    <input type="text" class="form-control form--control fs-14" id="room_title" name="room_title" placeholder="Room Title">
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium">Room Keyword <span style="color:red"> * </span></label>
                                    <input type="text" class="form-control form--control fs-14" id="room_keyword" name="room_keyword" placeholder="Room Keyword">
                                </div><!-- end form-group -->
                            </div>
                        </div>
                        <div class="form-group">
							<label class="fs-14 text-black fw-medium">Live Cast Link</label>
							<input type="text" class="form-control form--control fs-14" id="live_cast_link" name="live_cast_link" placeholder="Live Cast Link">
						</div><!-- end form-group -->
                        <div class="form-group">
                            <label class="fs-14 text-black fw-medium">Chat room</label>

                            <input class="ml-2" name="chat_room_status" id="v_options_internal" type="radio" value="1" checked="checked">
                            <label for="Internal">Internal</label>

                            <input class="ml-2" name="chat_room_status" id="v_options_external" type="radio" value="2">
                            <label for="External url">External url</label>

                            <input class="ml-2" name="chat_room_status" id="v_options_disable" type="radio" value="0">
                            <label for="Disable">Disable</label>
                            <input name="external_link" type="text" id="external_link" class="form-control" value="" ""="" style="display: none;">
						</div>
                        <div class="form-group external_div" style="display:none;">
							<label class="fs-14 text-black fw-medium">External Meeting Link</label>
							<input type="text" class="form-control form--control fs-14" id="external_link" name="external_link" placeholder="External Meeting Link">
						</div><!-- end form-group -->
                        <div class="form-group">
                            <label class="fs-14 text-black fw-medium mb-0">Geo Based</label>
                            <div class="pb-2"><small>(The logged-in user's country will be used when generating the room, and users will be filtered based on the radius range.)</small></div>
                            <label class="switch">
                                <input type="checkbox" id="geo_based" name="geo_based" title="Geo Based is required!">
                                <span class="slider round"></span>
                            </label>
						</div><!-- end form-group -->

                        <div class="form-group">
							<label class="fs-14 text-black fw-medium">Description <span style="color:red"> * </span></label>
							<textarea class="form-control summernote" id="description" name="description" rows="5" cols="80"></textarea>
                            <label id="description-error"  class="error" for="description"></label>
						</div><!-- end form-group -->
                        <div class="form-group">
							<label class="fs-14 text-black fw-medium">Lobby HTML Content</label>
							<textarea class="form-control summernote" id="html_description" name="html_description" rows="5" cols="80"></textarea>
						</div><!-- end form-group -->
                        <div class="form-group">
							<label class="fs-14 text-black fw-medium">Message from organizer</label>
							<textarea class="form-control summernote" id="msg_from_owner" name="msg_from_owner" rows="5" cols="80"></textarea>
						</div><!-- end form-group -->
						<div class="form-group">
							<label class="fs-14 text-black fw-medium">Square Image <span style="color:red"> * </span></label>
							<input type="file" class="form-control form--control fs-14" id="short_img" name="fileToUpload" accept="image/*" placeholder="Short Image">
						</div><!-- end form-group -->
						<div class="form-group">
							<label class="fs-14 text-black fw-medium">Banner Image <span style="color:red"> * </span></label>
							<input type="file" class="form-control form--control fs-14" id="sq_img" name="sq_img" accept="image/*" placeholder="Square Image">
						</div><!-- end form-group -->
                        <div class="form-group">
                            <label class="fs-14 text-black fw-medium mb-0">Lock Required</label>
                            <div class="pb-2"><small>(User entered lock code should match with specified lock code to access this room.)</small></div>
                            <label class="switch">
                                <input type="checkbox" onchange="enable_lock();" id="lock_req" name="lock_req">
                                <span class="slider round"></span>
                            </label>
						</div><!-- end form-group -->
                        <div class="form-group" style="display:none;">
							<label class="fs-14 text-black fw-medium">Lock Code <span style="color:red"> * </span></label>
							<input type="text" class="form-control form--control fs-14" id="lock_code" name="lock_code" placeholder="Lock Code">
                            <label id="lock_code-error" class="error" for="lock_code"></label>
						</div><!-- end form-group -->
                        <div class="form-group">
                            <label class="fs-14 text-black fw-medium mb-0">Date/Time Lock</label>
                            <div class="pb-2"><small>(Currently not in use.)</small></div>
                            <label class="switch">
                                <input type="checkbox" onchange="enable_datetime_lock();" id="datetime_lock_req" name="datetime_lock_req">
                                <span class="slider round"></span>
                            </label>
						</div><!-- end form-group -->
                        <div class="form-group datetimeshow" style="display:none;">
							<div class="row">
                                <div class="col-md-6">
                                    <label class="fs-14 text-black fw-medium">Start Date <span style="color:red"> * </span></label>
                                    <input type="datetime-local" class="form-control form--control fs-14" id="start_datetime_lock" name="start_datetime_lock" placeholder="Start Date">
                                </div>
                                <div class="col-md-6">
                                    <label class="fs-14 text-black fw-medium">End Date <span style="color:red"> * </span></label>
                                    <input type="datetime-local" class="form-control form--control fs-14" id="end_datetime_lock" name="end_datetime_lock" placeholder="End Date">
                                </div>
                            </div>
						</div><!-- end form-group -->
                        <div class="form-group">
                            <label class="fs-14 text-black fw-medium mb-0">Visiblity</label>
                            <div class="pb-2"><small>(Show or hide the room in the room listings.)</small></div>
                            <label class="switch">
                                <input type="checkbox" id="room_visiblity" name="room_visiblity" checked>
                                <span class="slider round"></span>
                            </label>
                        </div><!-- end form-group -->
                        <div class ="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Enable Normal Networking </label>
                                    <div class="pb-2"><small>(Currently not in use.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" id="taoh_networking_normal" name="taoh_networking_normal">
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Enable Speed Networking </label>
                                    <div class="pb-2"><small>(Currently not in use.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" id="taoh_networking_speed" name="taoh_networking_speed" checked>
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                        </div>
                        <div class ="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Make the room as country specific</label>
                                    <div class="pb-2"><small>(The logged-in user's country must match the specified country. Otherwise, the user will be blocked from accessing this room.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" onchange="show_country_field();" id="room_make_country_specific" name="room_make_country_specific">
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Make the room as country split</label>
                                    <div class="pb-2"><small>(The logged-in user's country will be used while generating room.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" id="room_make_country_split" name="room_make_country_split">
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="display:none;">
                                    <label class="fs-14 text-black fw-medium">Location <span style="color:red"> * </span></label>
                                    <?php echo field_location(); ?>
                                </div><!-- end form-group -->
                            </div>
                        </div>
                        <div class ="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Make the room as company specific</label>
                                    <div class="pb-2"><small>(The logged-in user's company must match the specified company. Otherwise, the user will be blocked from accessing this room.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" onchange="show_company_field();" id="room_make_cmp_specific" name="room_make_cmp_specific">
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Make the room as company split</label>
                                    <div class="pb-2"><small>(The logged-in user's company will be used while generating room.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" id="room_make_cmp_split" name="room_make_cmp_split">
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="display:none;">
                                    <label class="fs-14 text-black fw-medium">Company <span style="color:red"> * </span></label>
                                    <?php echo field_company(); ?>
                                </div><!-- end form-group -->
                            </div>
                        </div>
                        <div class ="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Make the room as title specific</label>
                                    <div class="pb-2"><small>(The logged-in user's title must match the specified title. Otherwise, the user will be blocked from accessing this room.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" onchange="show_title_field();" id="room_make_title_specific" name="room_make_title_specific">
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Make the room as title split</label>
                                    <div class="pb-2"><small>(The logged-in user's title will be used while generating room.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" id="room_make_title_split" name="room_make_title_split">
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="display:none;">
                                    <label class="fs-14 text-black fw-medium">Title <span style="color:red"> * </span></label>
                                    <?php echo field_role(); ?>
                                </div><!-- end form-group -->
                            </div>
                        </div>
                        <div class ="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Make the room as skill specific</label>
                                    <div class="pb-2"><small>(The logged-in user's skill must match the specified skill. Otherwise, the user will be blocked from accessing this room.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" onchange="show_skill_field();" id="room_make_skill_specific" name="room_make_skill_specific">
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Make the room as skill split</label>
                                    <div class="pb-2"><small>(The logged-in user's skill will be used while generating room.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" id="room_make_skill_split" name="room_make_skill_split">
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group skill_select" style="display:none;">
                                    <label class="fs-14 text-black fw-medium">Skill <span style="color:red"> * </span></label>
                                    <?php echo field_skill(); ?>
                                </div><!-- end form-group -->
                            </div>
                        </div>
                        <div class ="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Publish this room globally</label>
                                    <div class="pb-2"><small>(Globally any logged-in user's can access this room.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" onchange="show_private_field();" id="room_publish" name="room_publish" checked>
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                            <div class="col-md-4 private_room" style="display:none;">
                                <div class="form-group">
                                    <label class="fs-14 text-black fw-medium mb-0">Make the room as private</label>
                                    <div class="pb-2"><small>(Site TAOH_ROOT_PATH_HASH should match with room sub_secret_token, Otherwise, the user will be blocked from accessing this room.)</small></div>
                                    <label class="switch">
                                        <input type="checkbox" id="room_private" name="room_private">
                                        <span class="slider round"></span>
                                    </label>
                                </div><!-- end form-group -->
                            </div>
                        </div>
                        <div class ="row">
                            <div class="form-group">
                                <div class="mb-40px">
                                    <button class="btn theme-btn mt-2 submit" id="network_publish_form" type="submit">Submit Form</button>
                                </div>
                            </div><!-- end form-group -->
                        </div>
					</div>
				</form>
			</div>
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end networks-area -->
<script type="text/javascript">
    var data_url_short = '';
    var data_url_sq = '';
    var returndata = true;
    $(document).ready(function () {
        var today = '<?php echo $today; ?>';
        $("#start_datetime_lock").attr('min', today);
    });
    $("input[name='chat_room_status']").change(function() {
        var radio_val = $(this).val();
        if(radio_val == 2) {
            $('.external_div').show();
        } else {
            $('.external_div').hide();
        }
    });
    function enable_lock() {
        if($('input[name="lock_req"]').is(':checked')) {
            $('input[name="lock_code"]').parent().show();
        } else {
            $('input[name="lock_code"]').parent().hide();
        }
    }
    function enable_datetime_lock(){
        if($('input[name="datetime_lock_req"]').is(':checked')) {
            $('.datetimeshow').show();
        } else {
            $('.datetimeshow').hide();
        }
    }
    function show_company_field() {
        if($('input[name="room_make_cmp_specific"]').is(':checked')) {
            $('select[id="companySelect"]').parent().show();
            $('select[id="companySelect"]').attr("required", true);
        } else {
            $('select[id="companySelect"]').parent().hide();
            $('select[id="companySelect"]').attr("required", false);
        }
    }
    function show_title_field() {
        if($('input[name="room_make_title_specific"]').is(':checked')) {
            $('select[id="roleSelect"]').parent().show();
            $('select[id="roleSelect"]').attr("required", true);
        } else {
            $('select[id="roleSelect"]').parent().hide();
            $('select[id="roleSelect"]').attr("required", false);
        }
    }
    function show_country_field() {
        if($('input[name="room_make_country_specific"]').is(':checked')) {
            $('select[id="locationSelect"]').parent().show();
            $('select[id="locationSelect"]').attr("required", true);
        } else {
            $('select[id="locationSelect"]').parent().hide();
            $('select[id="locationSelect"]').attr("required", false);
        }
    }
    function show_skill_field() {
        if($('input[name="room_make_skill_specific"]').is(':checked')) {
            $('select[id="skillSelect"]').parent().show();
            $('select[id="skillSelect"]').attr("required", true);
        } else {
            $('select[id="skillSelect"]').parent().hide();
            $('select[id="skillSelect"]').attr("required", false);
        }
    }
    function show_private_field() {
        if($('input[name="room_publish"]').is(':not(:checked)')) {
            $('.private_room').show();
        } else {
            $('.private_room').hide();
        }
    }
    $("#start_datetime_lock").on("change", function(){
        $("#end_datetime_lock").attr("min", $(this).val());
    });
    $('#short_img').change(function() { //alert('file changed');
        var formData = new FormData(document.getElementById("networking_form"));
        fetch("<?php echo TAOH_CDN_PREFIX; ?>/cache/upload/now", {
            method: "POST",
            body: formData,
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                data_url_short = data.output;
                $('#short_img').val(data_url_short);
            }
        })
        .catch((error) => {
            /* console.error("Error:", error);
            document.getElementById("responseMessage").style.color = "red";
            document.getElementById("responseMessage").innerHTML = "An error occurred: " + error.output;
            document.getElementById("responseMessage").style.display = "block"; */
        });
        $('#short_img').attr('name', 'short_img');
        $('#sq_img').attr('name', 'fileToUpload');
	});
    $('#sq_img').change(function() { //alert('file changed');
        var formData = new FormData(document.getElementById("networking_form"));
        fetch("<?php echo TAOH_CDN_PREFIX; ?>/cache/upload/now", {
            method: "POST",
            body: formData,
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                data_url_sq = data.output;
                $('#sq_img').val(data_url_sq);
            }
        })
        .catch((error) => {
            /* console.error("Error:", error);
            document.getElementById("responseMessage").style.color = "red";
            document.getElementById("responseMessage").innerHTML = "An error occurred: " + error.output;
            document.getElementById("responseMessage").style.display = "block"; */
        });
        $('#sq_img').attr('name', 'sq_img');
	});
    document.getElementById("networking_form").addEventListener("submit", function (event) {
        //event.preventDefault();
        var serialize = $('#networking_form').serialize();
        var editor_val = $('#description').summernote('code');
        var html_editor_val = $('#html_description').summernote('code');
        var msg_from_owner = $('#msg_from_owner').summernote('code');

        var data = {
            'taoh_action': 'toah_network_form_post',
            'taoh_form_data': serialize,
            'short_image': data_url_short,
            'sq_image': data_url_sq,
            'description': editor_val,
            'html_description': html_editor_val,
            'msg_from_owner': msg_from_owner,
        };
        if($('input[name="lock_req"]').is(':checked')) {
            if($('#lock_code').val() == '') {
                $('#lock_code').focus();
                $('#lock_code').css('border-color', 'red');
                $('#lock_code-error').html('Lock Code is required!');
                returndata = false;
                return false;
            } else {
                $('#lock_code').css('border-color', '');
                $('#lock_code-error').html('');
                $('#lock_code-error').hide();
            }
        }
        if($('input[name="room_make_cmp_specific"]').is(':checked')) {
            if($('#companySelect').val() == '') {
                $('#companySelect').focus();
                returndata = false;
                return false;
            }else{
                returndata = true;
            }
        }
        if($('input[name="room_make_title_specific"]').is(':checked')) {
            if($('#roleSelect').val() == ''){
                $('#roleSelect').focus();
                returndata = false;
                return false;
            }else{
                returndata = true;

            }
        }
        if($('input[name="room_make_country_specific"]').is(':checked')) {
            if($('#locationSelect').val() == ''){
                console.log($('#locationSelect').val());
                $('#locationSelect').focus();
                returndata = false;
                return false;
            }else{
                returndata = true;
            }
        }
        if($('input[name="room_make_skill_specific"]').is(':checked')) {
            console.log($('#skillSelect').val());
            if($('#skillSelect').val() == ''){
                $('#skillSelect').focus();
                returndata = false;
                return false;
            }else{
                returndata = true;
            }
        }
        if($('input[name="datetime_lock_req"]').is(':checked')) {
            if($('#start_datetime_lock').val() == '' || $('#end_datetime_lock').val() == ''){
                $('#start_datetime_lock').focus();
                returndata = false;
                return false;
            }else{
                returndata = true;
            }
        }
        //alert(returndata);
        //var r_data = serialize+"&resume_link="+data_url+"&cover_letter=" + encodeURIComponent(editor_val);
        if(returndata) {
            jQuery.post("<?php echo taoh_site_ajax_url(); ?>", data, function(responses) {
                res = responses;
                console.log(res);
                if(res.success) {
                    alert('Room Created successfully!');
                    window.location.href = '<?php echo TAOH_SITE_URL_ROOT.'/club/rooms/?ops=delete_cache&mod=rooms'; ?>';
                } else {
                    $('#network_publish_form').prop("disabled", false);
                    // add spinner to button
                    $('#network_publish_form').html(
                        `Submit Form`
                    )
                    alert(res.message);
                }
            }).fail(function() {
                console.log( "Network issue111!" );
            })
        }
    });

    $("#room_title,#room_keyword").keypress(function(event) {
        var character = String.fromCharCode(event.keyCode);
        return isValid(character);
    });

    function isValid(str) {
        return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>]/g.test(str);
    }
</script>
<?php
taoh_get_footer();
?>