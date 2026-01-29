$('document').ready(function() {
    // $("#setting_form").validate({
    //     rules: {
    //         fname: {required:true},
    //         lname: {required:true},
    //         email : {
    //             required : false,
    //             email : true
    //         },
    //         type:"required",
    //         chat_name:"required",
    //         aboutme:"required",
    //         funfact:"required",
    //     },
    //     messages: {
    //         fname: {required : "First Name is required"},
    //         lname: {required : "Last Name is required"},
    //         email:{
    //             url : "Please enter vaild email"
    //         },
    //         type:"Profile type is required",
    //         chat_name:"Chat name is required",
    //         aboutme:"About Me is required",
    //         funfact:"Fun Fact is required",
    //     },
    //     submitHandler: function (form) {
    //         v5 = $("input[name=avatar]").val();
    //         if(v5 == "default") {
    //             $('#avatar-error').html('Avatar is required');
    //             $('#avatar-error').show();
    //         }
    //         else {
    //             $('#avatar-error').html('');
    //             $('#avatar-error').hide();
    //             form.submit();
    //         }
    //     }
    // });

    $("#new_setting_form").validate({
        rules: {
            fname: {required:true},
            lname: {required:true},
            email : {
                required : true,
                email : true
            },
            type:"required",
            chat_name:"required",
           // aboutme:"required",
           // funfact:"required",
        },
        messages: {
            fname: {required : "First Name is required"},
            lname: {required : "Last Name is required"},
            email:{
                url : "Please enter vaild email"
            },
            type:"Profile type is required",
            chat_name:"Chat name is required",
           // aboutme:"About Me is required",
           // funfact:"Fun Fact is required",
        },
        submitHandler: function (form) {
            v5 = $("input[name=avatar]").val();
            if(v5 == "default") {
                $('#avatar-error').html('Avatar is required');
                $('#avatar-error').show();
            }
            else {
                $('#avatar-error').html('');
                $('#avatar-error').hide();
                form.submit();
            }
        }
    });

    $("#networking_form").validate({
        rules: {
            room_title: {required:true},
            room_keyword: {required:true},
            external_link: {
                required: false,
                url: true
            },
            description: {required:true},
            fileToUpload: {required:true},
            sq_img: {required:true},
            lock_code: {
                required: "#lock_req:checked"
            },
            start_datetime_lock: {
                required: "#datetime_lock_req:checked"
            },
            end_datetime_lock: {
                required: "#datetime_lock_req:checked"
            }

        },
        messages: {
            room_title: {required : "Room Title is required"},
            room_keyword: {required : "Room Keyword is required"},
            external_link: {url : "Please enter valid URL"},
            description: {required : "Description is required"},
            fileToUpload: {required : "Short Image is required"},
            sq_img: {required : "Square Image is required"},
            lock_code: {required : "Lock Code is required"},
            start_datetime_lock: {required : "Start Date Time is required"},
            end_datetime_lock: {required : "End Date Time is required"},
        },
        submitHandler: function (form) {
            var textmsg = $('#description').summernote('code');
			if (textmsg == '' || textmsg == '<p><br></p>') {
				$('#description-error').html('Description is required')
				$('#description-error').show();
				return false;
			}
			else{
				$('#description-error').html('');
				$('#description-error').hide();
				$('#network_publish_form').prop("disabled", true);
				// add spinner to button
				$('#network_publish_form').html(
					`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
				)
			}
        }
    });
});