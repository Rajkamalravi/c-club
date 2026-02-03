    let event_speaker_cache = [];

    $(document).ready(function () {
        // new TomSelect('#local_timezoneSelect_session', {
        //     create: false,
        //     maxItems: 1,
        //     valueField: 'name',
        //     labelField: 'name',
        //     searchField: ['name'],
        //     load: function (query, callback) {
        //         jQuery.post(_taoh_site_ajax_url, {
        //             taoh_action: 'taoh_get_timezones',
        //             query: query
        //         }, function (response) {
        //             const res = (response.response || []).map(name => ({name}));
        //             callback(res);
        //         }).fail(() => callback());
        //     },
        //     render: {
        //         option: function (item, escape) {
        //             return `
        //                 <div class="py-2 d-flex">
        //                     <div class="mb-1">
        //                         <span class="h5"> ${escape(item.name)} </span>
        //                     </div>
        //                 </div>`;
        //         }
        //     }
        // });

        _getEventMetaInfo({ eventtoken: eventToken }, true)
            .then(({ requestData, response }) => {
                event_speaker_cache = (response && response.output && response.output.event_speaker) || [];
            })
            .catch(err => {
                console.error('Failed to load event exhibitor meta info:', err);
                event_speaker_cache = [];
            });

        $('#spk_form input[name="enable_tao_networking"]').on('change', function () {
            if($('#spk_form input[name="enable_tao_networking"]:checked').val() == 1){
                $("#spk_external_video_room_link").val('');
                $(".spk_external_video_room").hide();
                $(".spk_streaming_link_wrapper").show();
            }else{
                $("#spk_streaming_link").val('');
                $(".spk_streaming_link_wrapper").hide();
                $(".spk_external_video_room").show();
            }
        });

        $.validator.addMethod("greaterThan", function (value, element, selector) {
            const from = $(selector).val();
            if (!from || !value) return true;
            return value > from;
        }, "End date must be after start date");

        $.validator.addMethod("imageSize", function (value, element, param) { // Pass dimensions as an array
            if (element.files.length === 0) return true; // No file selected

            let file = element.files[0];
            let img = new Image();
            let URL = window.URL || window.webkitURL;
            let deferred = $.Deferred();

            img.src = URL.createObjectURL(file);
            img.onload = function () {
                URL.revokeObjectURL(img.src);
                // alert(img.width+'==='+img.height);
                // alert(param[0]+'==='+param[1]);
                if (img.width === param[0] && img.height === param[1]) {
                    deferred.resolve();
                } else {
                    deferred.reject();
                }
            };

            return deferred.promise();
        }, "Image must be exactly 1920x1080 pixels.");

        $.validator.addMethod('unique_speaker_title', function (value, element) {
            if (this.optional(element)) return true; // let required handle empty

            const title = (value || '').toLowerCase().trim();
            const spkToken = $('#spk_form #speaker_id').val() || '';

            let is_unique = true;

            event_speaker_cache.forEach(function (speaker) {
                if (!speaker || !speaker.spk_title) return;

                const existingTitle = speaker.spk_title.toLowerCase().trim();
                const existingId    = String(speaker.ID);

                // same title but different ID => not unique
                if (existingTitle === title && existingId !== String(spkToken)) {
                    is_unique = false;
                }
            });

            return is_unique;
        }, 'This title is already taken. Please use another title');

        $('#spk_form').validate({
            rules: {
                spk_title: {
                    required: true,
                    unique_speaker_title: true
                },
                spk_sdesc: {
                    required: true,
                },
                spk_desc: {
                    required: true,
                },
                spk_timezoneSelect: {
                    required: true,
                },
                spk_hall: {
                    required: true,
                },
                spk_datefrom: {
                    required: true,
                },
                spk_dateto: {
                    required: true,
                    greaterThan: "#spk_datefrom"
                },
                spk_linkedin: {
                    url: true
                },
                spk_logo_upload: {
                    extension: "jpg|jpeg|png"
                },
                spk_image_upload: {
                    extension: "jpg|jpeg|png",
                    imageSize: [1920, 1080]
                },
                spk_image: {
                    extension: "jpg|jpeg|png",
                    imageSize: [1920, 1080]
                },
                spk_zoom_url: {
                    url: true,
                    required: function () {
                        // return $('input[name="spk_video_room"]:checked').val() === 'zoom';
                        return $('#spk_video_room-yes').prop('checked');
                    }
                },
                spk_phycial_location: {
                    required: function () {
                        return $('#spk_video_room-physical').prop('checked');
                    }
                },
                spk_hero_button_text: {
                    required: true,
                },
                spk_hero_button_url: {
                    required: true,
                }
            },
            messages: {
                spk_title: {
                    required: "Session title is required",
                },
                spk_sdesc: {
                    required: "Session Subtitle is required",
                },
                spk_desc:{
                    required: "Session Description is required",
                },
                spk_timezoneSelect: {
                    required: "Timezone is required",
                },
                spk_hall: {
                    required: "Session Room is required",
                },
                spk_datefrom: {
                    required: "Speaker timeslot is required",
                },
                spk_dateto: {
                    required: "Speaker timeslot is required",
                },
                spk_linkedin: {
                    url: "Please enter a valid URL (e.g., https://example.com)."
                },
                spk_zoom_url: {
                    required: 'Zoom link is required',
                    url: "Please enter a valid URL (e.g., https://example.com)."
                },
                spk_logo_upload: {
                    extension: "Only JPG, JPEG, or PNG files are allowed.",
                },
                spk_image_upload: {
                    extension: "Only JPG, JPEG, or PNG files are allowed.",
                    imageSize: "Image must be exactly 1920x1080 pixels."
                },
                spk_image: {
                    extension: "Only JPG, JPEG, or PNG files are allowed.",
                    imageSize: "Image must be exactly 1920x1080 pixels."
                },
                spk_phycial_location: {
                    required: 'Hall No/ Location is required',
                },
                spk_hero_button_text: {
                    required: 'Hero Button Text is required',
                },
                spk_hero_button_url: {
                    required: 'Hero Button URL is required',
                }
            },
            errorPlacement: function (error, element) {
                if (element.hasClass('ts-hidden-accessible')) {
                    error.insertAfter(element.next('.ts-wrapper'));
                } else {
                    element.after(error);
                }
            }
        });

        $('#spk_form').on('submit', function (e) {
            e.preventDefault();

            $('#speakerSlotModal #speaker_blk:not(.show)').collapse('show');
            $('#speakerSlotModal #sessionStateCollapse:not(.show)').collapse('show');

            $('#spk_form .speakeritem').each(function () {
                const index = $(this).attr('data-morespeakerindex');

                console.log('Adding rules for speaker index:', index);

                $(this).find(`#spk_name_${index}`).rules("add", {
                    required: true,
                    messages: {
                        required: "Speaker name is required"
                    }
                });

                $(this).find(`#spk_desig_${index}`).rules("add", {
                    required: true,
                    messages: {
                        required: "Designation is required"
                    }
                });

                $(this).find(`#spk_company_${index}`).rules("add", {
                    required: true,
                    messages: {
                        required: "Company is required"
                    }
                });

                $(this).find(`#spk_bio_${index}`).rules("add", {
                    required: true,
                    messages: {
                        required: "Bio of the Speaker is required"
                    }
                });

                $(this).find(`#spk_profileimg_upload_${index}`).rules("add", {
                    required: (($(`#spk_profileimg_upload_${index}`).val() === '') && ($(`#spk_profileimg_${index}`).val() === '')),
                    extension: "jpg|jpeg|png",
                    messages: {
                        required: "Profile Image is required",
                        extension: "Only JPG, JPEG, or PNG files are allowed."
                    }
                });
            });

            if (!$('#spk_form').valid()) {
                return;
            }

            const is_organizer = $("#is_organizer").val();

            let spk_form = $('#spk_form');
            let formData = new FormData(document.getElementById("spk_form"));
            formData.append('is_organizer', is_organizer);

            let submit_btn = spk_form.find('button[type="submit"]');
            submit_btn.prop('disabled', true);

            let submit_btn_icon = submit_btn.find('i');
            submit_btn_icon.removeClass('fa-save').addClass('fa-spinner fa-spin');

            $("#spk_submit").attr('disabled', true);

            $.ajax({
                url: spk_form.attr('action'),
                type: 'post',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function (response) {
                    console.log(response);
                    // loader(false, $("#addspeaker_loaderArea"));
                    if (response.success) {
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                        submit_btn.prop('disabled', false);
                        delete_events_meta_into(eventToken);
                        taoh_set_success_message('Speaker Saved Successfully.');
                        $('#spk_form')[0].reset();
                        updateEventMetaInfo(eventToken, false);
                        $("#speakerSlotModal").modal("hide");
                        location.reload();
                    } else {
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                        submit_btn.prop('disabled', false);
                        taoh_set_error_message('Failed to process your data! Try Again', false);
                    }
                },
                error: function (xhr, status, error) {
                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                    submit_btn.prop('disabled', false);
                    console.log('Error:', xhr.status);
                }
            });
        });

        initRepeatableSpeaker($("#speaker_blk #repeatable_speaker"));

        $(document).on('change', '#spk_logo_upload', function(e) {
            let file = e.target.files[0];
            const maxSize = 1024 * 1024; // 1MB in bytes

            if (file && file.size > maxSize) {
                taoh_set_error_message("File size exceeds 1MB limit!", false);
                this.value = ''; // Clear the input
            } else {
                const reader = new FileReader();

                reader.onload = function (e) {
                    const result = e?.target?.result;

                    // Must be a string data URL and image/* of allowed types
                    const isValidDataUrl =
                        typeof result === 'string' &&
                        /^data:image\/(png|jpe?g|gif|webp);base64,/.test(result);

                    if (!isValidDataUrl) {
                        console.warn('Invalid reader result.');
                        return;
                    }

                    // Build the <img> safely (no innerHTML)
                    const $img = $('<img>', {
                        src: result,
                        class: 'img-fluid',
                        alt: 'Exhibitor Logo',
                        width: 50
                    });

                    $('#spk_logo_image_preview').empty().append($img);
                };

                reader.onerror = function (e) {
                    console.error('FileReader error:', e);
                };

                reader.onabort = function () {
                    console.warn('FileReader aborted.');
                };

                reader.readAsDataURL(file);
            }
        });
        $(document).on('change', '#spk_image_upload', function(e) {
            let file = e.target.files[0];
            const maxSize = 1024 * 1024; // 1MB in bytes

            if (file && file.size > maxSize) {
                taoh_set_error_message("File size exceeds 1MB limit!",false);
                this.value = ''; // Clear the input
            }else{
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#spk_image_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Exhibitor Logo" width="100" />`);
                }
                reader.readAsDataURL(file);
            }
        });
        $(document).on('change', '.spk_profileimg', function(e) {
            let idArr = $(this).attr("id");
            let file = e.target.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            if (file && file.size > maxSize) {
                taoh_set_error_message("File size exceeds 2MB limit!",false);
                this.value = ''; // Clear the input
            }else{
                let reader = new FileReader();
                curIdArr = idArr.split("spk_profileimg_upload_");
                curId = curIdArr[1];
                console.log(curId);
                reader.onload = function(e) {
                    $('#spk_profileimg_preview_'+curId).html(`<img src="${e.target.result}" class="img-fluid" alt="Exhibitor Logo" width="100" />`);
                }
                reader.readAsDataURL(file);
            }
        });
    });

    function updateSpeakerDeleteBtn() {
        let totalSpeakers = $('#repeatable_speaker .speakeritem').length;
        if (totalSpeakers <= 1) {
            $('#repeatable_speaker .speaker_delete').hide();
        } else {
            $('#repeatable_speaker .speaker_delete').show();
        }
    }

    function initRepeatableSpeaker(elem) {
        elem.repeatable({
            addTrigger: ".speaker_add",
            deleteTrigger: ".speaker_delete",
            template: "#speaker_template",
            itemContainer: ".speakeritem",
            itemIndex: false,
            min: 1,
            afterAdd: updateSpeakerDeleteBtn,
            afterDelete: updateSpeakerDeleteBtn
        });

        updateSpeakerDeleteBtn();
