let myCityCurrentQuery = '';
let myCityLoadingMore = false;

jQuery.validator.addMethod("requireOneOfTwoImg", function (value, element, otherSelector) {
    const avatarVal = $('input[name="avatar_image"]').val();
    const otherVal = $.trim($(otherSelector).val());
    const val = $.trim(value);

    if (avatarVal) {
        return true;
    }

    if (element.type === "file") {
        return val !== "" || (otherVal !== "" && otherVal !== "default");
    }
    return (val !== "" && val !== "default") || otherVal !== "";
}, "Please provide either an avatar or a profile picture.");

jQuery.validator.addMethod('profileFileSize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param);
}, function(param, element) {
    const maxMB = param / (1024 * 1024);
    const displayMB = (maxMB % 1 === 0) ? maxMB.toFixed(0) : maxMB.toFixed(2);
    return 'File size must be less than ' + displayMB + ' MB';
});

$(document).ready(function () {
    if ($('#country_code').length > 0) {
        new TomSelect('#country_code', {
            create: false,
            preload: 'focus',
            maxItems: 1,
            valueField: 'code',
            labelField: 'name',
            searchField: ['name'],
            placeholder: 'Select your country',
            load: function (query, callback) {
                jQuery.get('https://opslogy.com/mapn/', {
                    op: 'country',
                }, function (response) {
                    const res = JSON.parse(response);
                    callback(res?.output || []);
                }).fail(() => callback());
            },
            render: {
                option: function (item, escape) {
                    return `
                        <div class="py-2 d-flex">
                            <div class="mb-1">
                                <span class="h5">${escape(item.name)}</span>
                            </div>
                        </div>`;
                }
            },
            onChange: function (value) {
                if (value) {
                    const country = this.options[value];
                    if (country && country.code) {
                        clearTomSelect('#my_city');
                        $('input[name="geohash"]').val('');
                        $('input[name="full_location"]').val('');
                    }
                }

                if (ensureMyCityErrorLabel()) {
                    $('.my_city_order_error').hide();
                    $('#country_code, #my_city').valid();
                }
            }
        });
    }

    if ($('#my_city').length > 0) {
        const myCityTomSelectInstance = new TomSelect('#my_city', {
            create: false,
            maxItems: 1,
            valueField: 'coordinates',
            labelField: 'location',
            searchField: ['location', 'coordinates'],
            placeholder: 'Select your city',
            sortField: null,
            load: function (query, callback) {
                myCityCurrentQuery = query;

                if (ensureMyCityErrorLabel()) {
                    $('.my_city_order_error').hide();
                    $('#country_code, #my_city').valid();

                    const countryCode = $('#country_code').val();

                    jQuery.post(_taoh_site_ajax_url, {
                        taoh_action: 'taoh_get_location',
                        country_code: countryCode,
                        query: query,
                        offset: 0
                    }, function (response) {
                        if (Array.isArray(response)) {
                            const results = response.map(item => ({
                                location: item.location.split(",").map(s => s.trim()).filter(Boolean).join(", "),
                                coordinates: item.coordinates
                            }));
                            callback(results);
                        } else {
                            callback([]);
                        }
                    }).fail(() => callback([]));
                } else {
                    callback([]);
                }
            },
            onChange: function (value) {
                if (ensureMyCityErrorLabel()) {
                    $('.my_city_order_error').hide();
                    $('#country_code, #my_city').valid();
                }

                const coordinates = value.split('::');
                if (coordinates.length === 2) {
                    const [lat, lon] = coordinates;
                    jQuery.post(_taoh_site_ajax_url, {
                        'taoh_action': 'taoh_get_geohash',
                        'lat': lat,
                        'lon': lon
                    }, function (response) {
                        $('input[name="geohash"]').val(response.geohash);

                        if (ensureMyCityErrorLabel()) {
                            $('.my_city_order_error').hide();
                            $('#country_code, #my_city').valid();
                        }
                    }).fail(() => {
                        clearTomSelect('#my_city');
                        $('input[name="geohash"]').val('');
                        $('input[name="full_location"]').val('');

                        if (ensureMyCityErrorLabel()) {
                            $('.my_city_order_error').hide();
                            $('#country_code, #my_city').valid();
                        }
                    });
                } else {
                    clearTomSelect('#my_city');
                    $('input[name="geohash"]').val('');
                    $('input[name="full_location"]').val('');

                    if (ensureMyCityErrorLabel()) {
                        $('.my_city_order_error').hide();
                        $('#country_code, #my_city').valid();
                    }
                }
            },
            render: {
                option: function (item, escape) {
                    return `
                            <div class="py-2 d-flex">
                                <div class="mb-1">
                                    <span class="h5">${escape(item.location)}</span>
                                </div>
                            </div>`;
                }
            },
            onItemAdd: function (value, item) {
                const label = $(item).text();
                $('input[name="full_location"]').val(label);

                const countryCode = label.split(',')[2]?.trim();
                const [lat, lon] = value.split('::');

                const timeZoneInstance = document.querySelector('#local_timezone')?.tomselect;
                if (timeZoneInstance && countryCode) {
                    jQuery.get('https://opslogy.com/mapn/', {
                        op: 'timezone',
                        country_code: countryCode,
                        lat: lat,
                        lon: lon
                    }, function (response) {
                        const res = JSON.parse(response);
                        if (res?.output) {
                            timeZoneInstance.addOption({name: res.output});
                            timeZoneInstance.setValue(res.output);
                        }
                    });
                }
            }
        });

        if(myCityTomSelectInstance) {
            myCityTomSelectInstance.on('dropdown_open', function () {
                const dropdownContent = myCityTomSelectInstance.dropdown_content;

                if (!dropdownContent) return;

                dropdownContent.addEventListener('scroll', function () {
                    const scrollTop = dropdownContent.scrollTop;
                    const scrollHeight = dropdownContent.scrollHeight;
                    const clientHeight = dropdownContent.clientHeight;
                    const offset = 50; // Trigger 50px before the end

                    if (!myCityLoadingMore && scrollTop + clientHeight + offset >= scrollHeight) {
                        myCityLoadingMore = true;

                        const countryCode = $('#country_code').val();

                        jQuery.post(_taoh_site_ajax_url, {
                            taoh_action: 'taoh_get_location',
                            country_code: countryCode,
                            query: myCityCurrentQuery,
                            offset: Math.max(0, Object.keys(myCityTomSelectInstance.options).length - 1)
                        }, function (response) {
                            const results = response.map(item => ({
                                location: item.location.split(",").map(s => s.trim()).filter(Boolean).join(", "),
                                coordinates: item.coordinates
                            }));

                            const currentScrollTop = dropdownContent.scrollTop;

                            myCityTomSelectInstance.addOptions(results);
                            myCityTomSelectInstance.refreshOptions(false); // Don't perform filtering based on the current search query

                            // Manual DOM append to avoids existing options re-order
                            // results.forEach(opt => {
                            //     if (!myCityTomSelectInstance.options[opt.coordinates]) {
                            //         myCityTomSelectInstance.options[opt.coordinates] = opt;
                            //
                            //         const rendered = myCityTomSelectInstance.render('option', opt);
                            //         dropdownContent.appendChild(rendered);
                            //     }
                            // });

                            // Restore scroll position
                            dropdownContent.scrollTop = currentScrollTop;

                            myCityLoadingMore = false;
                        }).fail(() => {
                            myCityLoadingMore = false;
                        });
                    }
                });
            });
        }
    }

    if ($('#local_timezone').length > 0) {
        new TomSelect('#local_timezone', {
            create: false,
            maxItems: 1,
            valueField: 'name',
            labelField: 'name',
            searchField: ['name'],
            load: function (query, callback) {
                jQuery.post(_taoh_site_ajax_url, {
                    taoh_action: 'taoh_get_timezones',
                    query: query
                }, function (response) {
                    const res = (response.response || []).map(name => ({name}));
                    callback(res);
                }).fail(() => callback());
            },
            render: {
                option: function (item, escape) {
                    return `
                        <div class="py-2 d-flex">
                            <div class="mb-1">
                                <span class="h5"> ${escape(item.name)} </span>
                            </div>
                        </div>`;
                }
            },
            onChange: function (value) {
                if (value) {
                    $('#local_timezone').valid();
                }
            }
        });
    }

    if ($('#profile_company').length > 0) {
        new TomSelect('#profile_company', {
            create: true,
            maxItems: 1,
            valueField: 'id',
            labelField: 'label',
            searchField: ['label', 'value'],
            load: function (query, callback) {
                jQuery.post(_taoh_site_ajax_url, {
                    taoh_action: 'taoh_get_companies',
                    query: query
                }, function (response) {
                    callback(response);
                }).fail(() => callback());
            },
            render: {
                option: function (item, escape) {
                    return `
                        <div class="py-2 d-flex">
                            <div class="mb-1">
                                <span class="h5"> ${escape(item.label)} </span>
                            </div>
                        </div>`;
                }
            },
            onOptionAdd: function(value) {
                jQuery.post(_taoh_site_ajax_url, {
                    taoh_action: 'taoh_add_company',
                    company: value,
                    mod: 'company'
                });
            }
        });
    }

    if ($('#profile_role').length > 0) {
        new TomSelect('#profile_role', {
            create: true,
            maxItems: 1,
            valueField: 'id',
            labelField: 'label',
            searchField: ['label', 'value'],
            load: function (query, callback) {
                jQuery.post(_taoh_site_ajax_url, {
                    taoh_action: 'taoh_get_roles',
                    query: query
                }, function (response) {
                    callback(response);
                }).fail(() => callback());
            },
            render: {
                option: function (item, escape) {
                    return `
                        <div class="py-2 d-flex">
                            <div class="mb-1">
                                <span class="h5"> ${escape(item.label)} </span>
                            </div>
                        </div>`;
                }
            },
            onOptionAdd: function(value) {
                jQuery.post(_taoh_site_ajax_url, {
                    taoh_action: 'taoh_add_role',
                    role: value,
                    mod: 'role'
                });
            }
        });
    }

    $('#profile_form_1').validate({
        ignore: ":hidden:not(#avatar)",
        rules: {
            profile_picture: {
                requireOneOfTwoImg: 'input[name="avatar"]',
                profileFileSize: 1024 * 1024, // 1 MB in bytes
            },
            avatar: {
                requireOneOfTwoImg: '#profile_picture'
            },
            fname: {
                required: true,
                minlength: 2,
            },
            lname: {
                required: true,
            },
            email: {
                required: true,
                email: true
            },
            type: {
                required: true,
            },
            country_code: {
                required: true,
            },
            coordinates: {
                required: true,
            },
            local_timezone: {
                required: true,
            }
        },
        messages: {
            profile_picture: {
                requireOneOfTwoImg: "Please upload a profile picture or select an avatar."
            },
            avatar: {
                requireOneOfTwoImg: "Please select an avatar or upload a profile picture."
            },
            fname: {
                required: "First name is required",
            },
            lname: {
                required: "Last name is required",
            },
            email: {
                required: "Email is required",
                email: "Please enter a valid email address"
            },
            type: {
                required: "Please select your profile type"
            },
            country_code: {
                required: "Please select your country"
            },
            coordinates: {
                required: "Please select your city"
            },
            local_timezone: {
                required: "Timezone is required"
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr('name') === 'coordinates') {
                $('.my_city_order_error').hide();
                error.insertAfter(element.next('.ts-wrapper'));
            } else if (element.closest('.btn-group').length) {
                error.insertAfter(element.closest('.btn-group'));
            } else if (element.hasClass('ts-hidden-accessible')) {
                error.insertAfter(element.next('.ts-wrapper'));
            } else {
                element.after(error);
            }
        },
        submitHandler: function (form) {
            const ts = document.querySelector('#country_code')?.tomselect;

            let profile_form_1 = $('#profile_form_1');
            let submit_btn = profile_form_1.find('button[type="submit"]');
            let submit_btn_icon = submit_btn.find('i');

            let formData = new FormData(form);
            formData.append('taoh_action', 'basic_profile_update');
            if (ts) {
                const selectedValue = ts.getValue();
                const selectedData = ts.options[selectedValue];

                formData.append('country_name', selectedData.name);
            }

            submit_btn_icon.removeClass('fa-save').addClass('fa-spinner fa-spin');
            submit_btn.prop('disabled', true);

            $.ajax({
                url: profile_form_1.attr('action'),
                type: 'post',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function (response) {
                    if (response.status) {
                        let loginType = response?.data?.type ?? '';
                        let profileStage = parseInt(response?.data?.profile_stage, 10) || 0;

                        if ($('#basicSettingsModal').length > 0) {
                            $('#basicSettingsModal').modal('hide');
                        }

                        let current_profile_stage = parseInt($('.current_profile_stage').val() || 0) || 0;
                        if (loginType === 'employer' && current_profile_stage < 1) {
                            localStorage.setItem('show_jobPostModal', 1);
                        }

                        let successMessage = 'Your basic settings have been successfully completed.';
                        if(profileStage > 0 && profileStage < 3) {
                            successMessage += ' You are now eligible to join the event or explore the site, and you can complete the remaining setup at your convenience.';
                        }
                        taoh_set_success_message(successMessage, false, 'toast-middle', [
                            {
                                text: 'OK',
                                action: () => {
                                    window.location.reload();
                                },
                                class: 'dojo-v1-btn float-right mt-3 mb-3'
                            }
                        ]);

                        // $.confirm({
                        //     title: 'Success',
                        //     content: successMessage,
                        //     type: 'green',
                        //     buttons: {
                        //         confirm: {
                        //             text: 'OK',
                        //             action: function () {
                        //                 window.location.reload();
                        //             }
                        //         }
                        //     }
                        // });
                    } else {
                        if (response.error && response.error === 'profile_picture_not_provided') {
                            taoh_set_error_message('Please provide either an avatar or a profile picture.', false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {},
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);
                            // $.alert('Please provide either an avatar or a profile picture.');

                        } else if(response.error && response.error === 'upload_failed'){
                            taoh_set_error_message('Profile picture upload failed! Please try again.', false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {},
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);
                            // $.alert('Profile picture upload failed! Please try again.');

                        } else {
                            taoh_set_error_message('Failed to update your basic profile info! Try Again', false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {},
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);
                            // $.alert('Failed to update your basic profile info! Try Again');
                        }
                    }
                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                    submit_btn.prop('disabled', false);
                },
                error: function (xhr) {
                    console.log('Basic Info create error:', xhr.status);
                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                    submit_btn.prop('disabled', false);
                }
            });

        }
    });

});

$(document).on('change', '#profile_picture', function () {
    let file = this.files[0];
    if (file) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $(".profile-image").hide();
            $('.avatar_settings').html(`<img src="${e.target.result}" alt="Avatar"><div id="removeImage" class="delete-icon"></div>`);
            $('.profile_picture_label').text(file.name.length > 20
                ? file.name.substring(0, 20) + '...'
                : file.name
            );
            $('.avatar-container').show();
        };
        reader.readAsDataURL(file);

        $(this).valid();
    } else {
        $('input[name="avatar_image"]').val('');
        $('.profile_picture_label').text('Choose file');
        $('.avatar-container').hide();

        $(".profile-image").show();
    }
});

$(document).on('click', '#removeImage', function () {
    $('#profile_picture').val('').trigger('change');
});

$(document).on('click', '.complete_basic_settings', function () {
    showBasicSettingsModal();
});

function showBasicSettingsModal(disableClose = false) {
    const $m = $('#basicSettingsModal');
    if ($m.hasClass('show')) return;

    $m.find('.basicSettingsModalCloseBtn')[disableClose ? 'hide' : 'show']();
    $m.modal({ backdrop: disableClose ? 'static' : true, keyboard: !disableClose }).modal('show');
}

function ensureMyCityErrorLabel() {
    const countryCode = $('#country_code').val();

    // If no country is selected, block city loading
    if (!countryCode) {
        clearTomSelect('#my_city');
        $('input[name="geohash"]').val('');
        $('input[name="full_location"]').val('');

        const cityEl = document.querySelector('#my_city');
        if (cityEl?.tomselect) {
            cityEl.tomselect.close(); // close city dropdown
        }

        // Focus and open the country dropdown if it’s TomSelect
        const countryEl = document.querySelector('#country_code');
        if (countryEl?.tomselect) {
            countryEl.tomselect.open();
        } else {
            $('#country_code').focus();
        }

        let existingError = $('#my_city-error');
        if (existingError.length) {
            existingError.text('Select a country before choosing a city').show();
        } else {
            $('.my_city_order_error').text('Select a country before choosing a city').show();
        }

        return false;
    } else {
        return true;
    }
}

function clearTomSelect(selector) {
    const ts = document.querySelector(selector)?.tomselect;
    if (ts) {
        ts.clear();              // Clears any selected value
        ts.clearOptions();       // Removes all dropdown options
        ts.setTextboxValue('');  // Clears typed input
    }
}