    let event_exhibitor_cache = [];

    function OpenExhibitorFormTooltip(){
        $("#ExhibitorFormContent").hide();
        $("#ExhibitorFormTooltip").show();
    }

     function closeExhibitorFormTooltip(){
        $("#ExhibitorFormContent").show();
        $("#ExhibitorFormTooltip").hide();
    }

    $(document).ready(function(){
        $(".hall-field").select2({width: '100%'});

        // $('#setup_exhibitor_slot_form input[name="enable_tao_networking"]').on('change', function () {
        //     if($('#setup_exhibitor_slot_form input[name="enable_tao_networking"]:checked').val() == 1){
        //         $("#exh_external_video_room_link").val('');
        //         $("#video_conference_on_exhibit").hide();
        //         $(".exh_streaming_link_wrapper").show();
        //     } else{
        //         $("#exh_streaming_link").val('');
        //         $(".exh_streaming_link_wrapper").hide();
        //         $("#video_conference_on_exhibit").show();
        //     }
        // });

        _getEventMetaInfo({ eventtoken: eventToken }, true)
            .then(({ requestData, response }) => {
                event_exhibitor_cache = (response && response.output && response.output.event_exhibitor) || [];
            })
            .catch(err => {
                console.error('Failed to load event exhibitor meta info:', err);
                event_exhibitor_cache = [];
            });

        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param * 1000000)
        }, 'File size must be less than {0} MB');

        $.validator.addMethod('unique_exhibitor_title', function (value, element) {
            if (this.optional(element)) return true; // let required handle empty

            const title = (value || '').toLowerCase().trim();
            const exhToken = $('#setup_exhibitor_slot_form #exhibitor_id').val() || '';

            let is_unique = true;

            event_exhibitor_cache.forEach(function (exhibitor) {
                if (!exhibitor || !exhibitor.exh_session_title) return;

                const existingTitle = exhibitor.exh_session_title.toLowerCase().trim();
                const existingId    = String(exhibitor.ID);

                // same title but different ID => not unique
                if (existingTitle === title && existingId !== String(exhToken)) {
                    is_unique = false;
                }
            });

            return is_unique;
        }, 'This title is already taken. Please use another title');

        $('#setup_exhibitor_slot_form').validate({
            rules: {
                exh_session_title: {
                    required: true,
                    unique_exhibitor_title: true
                },
                exh_subtitle: {
                    required: false
                },
                exh_description: {
                    required: true
                },
                'exh_hall[]': {
                    required: true
                },
                exh_logo_upload: {
                    required: function () {
                        return ($('#exh_logo_upload').val() === '') && ($('#exh_logo').val() === '');
                    },
                    extension: "jpg|jpeg|png",
                    filesize: 5
                },
                exh_banner_upload: {
                    required: function () {
                        return false; // ($('#exh_banner_upload').val() === '') && ($('#exh_banner').val() === '');
                    },
                    extension: "jpg|jpeg|png",
                    filesize: 5
                },
                exh_hero_button_text: {
                    required: false,
                    // maxlength: 20,
                    minlength: 2
                },
                exh_hero_button_url: {
                    required: false,
                    url: true
                },
                exh_raffle_status: {
                    required: true,
                },
                exh_raffle_title: {
                    required: true,
                    // maxlength: 150,
                    minlength: 3
                },
                exh_raffle_description: {
                    required: true,
                    // maxlength: 500,
                },
                exh_raffle_start_time: {
                    required: function () {
                        return $('input[name="exh_raffles_timebound_option"]:checked').val() === '1';
                    }
                },
                exh_raffle_stop_time: {
                    required: function () {
                        return $('input[name="exh_raffles_timebound_option"]:checked').val() === '1';
                    }
                },
                exh_contact_email: {
                    required: function () {
                        return $("#is_organizer").val() != 1;
                    },
                    email: true
                }
            },
            messages: {
                exh_session_title: {
                    required: "Title is required"
                },
                exh_subtitle: {
                    required: "Subtitle is required"
                },
                exh_description: {
                    required: "Description is required"
                },
                'exh_hall[]': {
                    required: "Associated Hall is required"
                },
                exh_logo_upload: {
                    required: "Logo image is required",
                    extension: "Only JPG, JPEG, or PNG files are allowed."
                },
                exh_banner_upload: {
                    required: "Banner image is required",
                    extension: "Only JPG, JPEG, or PNG files are allowed."
                },
                exh_hero_button_text: {
                    required: "Button text is required"
                },
                exh_hero_button_url: {
                    required: "Button URL is required"
                },
                exh_raffle_status: {
                    required: "Raffle Status is required"
                },
                exh_raffle_title: {
                    required: "Raffle Title is required"
                },
                exh_raffle_description: {
                    required: "Raffle Description is required"
                },
                exh_raffle_start_time: {
                    required: "Raffle Start time is required"
                },
                exh_raffle_stop_time: {
                    required: "Raffle Stop time is required"
                },
                exh_contact_email: {
                    required: "Contact Email is required",
                }
            },
            errorPlacement: function (error, element) {
                if (element.is(":checkbox")) {
                    error.appendTo(element.parent());
                } else if (typeof element.data('error_id') !== 'undefined' && element.data('error_id') !== false) {
                    $(element.data('error_id')).html(error);
                } else if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.siblings('.select2'));
                } else {
                    element.after(error);
                }
            },
            submitHandler: function (form) {
                const is_organizer = $("#is_organizer").val();

                let setup_exhibitor_slot_form = $('#setup_exhibitor_slot_form');
                let formData = new FormData(form);
                formData.append('is_organizer', is_organizer);

                let submit_btn = setup_exhibitor_slot_form.find('button[type="submit"]');
                submit_btn.prop('disabled', true);

                let submit_btn_icon = submit_btn.find('i');
                submit_btn_icon.removeClass('fa-save').addClass('fa-spinner fa-spin');

                $.ajax({
                    url: setup_exhibitor_slot_form.attr('action'),
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (response) {
                        if (response.success) {
                            delete_events_meta_into(eventtoken);
                            delete_events_into('event_details_sponsor_' + eventtoken);
                            delete_events_into('event_MetaInfo_' + eventtoken);
                            form.reset();
                            $('#exh_logo_preview').empty();
                            $('#exh_banner_preview').empty();
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                            submit_btn.prop('disabled', false);
                            $('#exhibitorSlotModal').modal('hide');
                            taoh_set_success_message('Exhibitor slot added successfully', false);
                            location.reload();
                        } else {
                            submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                            submit_btn.prop('disabled', false);
                            taoh_set_error_message('Failed to process your data! Try Again', false);
                        }
                        // loader(false, $("#addexh_loaderArea"));
                    },
                    error: function (xhr, status, error) {
                        console.log('Error:', xhr.status);
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                        submit_btn.prop('disabled', false);
                    }
                });

            }
        });

        $(document).on('change', '#exh_logo_upload', function(e) {
            let file = e.target.files[0];
            const maxSize = 1024 * 1024; // 1MB in bytes

            if (file && file.size > maxSize) {
                taoh_set_error_message("File size exceeds 1MB limit!",false);
                this.value = ''; // Clear the input
            }else{
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#exh_logo_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Exhibitor Logo" />`);
                }
                reader.readAsDataURL(file);
            }
        });

        $(document).on('change', '#exh_banner_upload', function(e) {
            let file = e.target.files[0];
            const maxSize = 1024 * 1024; // 1MB in bytes

            if (file && file.size > maxSize) {
                taoh_set_error_message("File size exceeds 1MB limit!",false);
                this.value = ''; // Clear the input
            }else{
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#exh_banner_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Exhibitor Banner" />`);
                }
                reader.readAsDataURL(file);
            }
        });
    });

    $('#exhibitorSlotModal').on('show.bs.modal', () => {
          $("#ExhibitorFormContent").show();
        $("#ExhibitorFormTooltip").hide();
    });
