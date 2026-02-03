function getCompiledProfileHtml(data) {
    let is_user_logged_in = true;

    return new Promise((resolve, reject) => {
        let about_type = '';
        let about_me = (data.aboutme).replace(/[\s\t]+/g, ' ');
        let fun_fact = (data.funfact).replace(/[\s\t]+/g, ' ');

        if(typeof data.about_type !== 'undefined' && data.about_type !== null){
            about_type = (data.about_type).replace(/[\s\t]+/g, ' ');
        }

        if(!is_user_logged_in){
            about_me = about_me.substring(0, 100);
            fun_fact = fun_fact.substring(0, 100);
            about_type = about_type.substring(0, 100);
        }

        // let edu_tot_count = 0;
        // let edu_last_key = 0;
        // let edu_list = [];
        // if(Array.isArray(data.education) && data.education.length > 0){
        //     edu_tot_count = data.education.length;
        //     edu_last_key = edu_tot_count - 1;
        //     edu_list = data.education;
        //
        // }


        var html = '<div>' +
        '<div class="media media-card">' +
        '<div class="media-body">';

            html += `<div class="row">
                            <div class="col-lg-3 pr-0">
                                <img src="${_taoh_ops_prefix + '/avatar/PNG/128/' + data.avatar + '.png'}" alt="avatar">
                            </div>
                            <div class="col-lg-9 pl-2">
                                <span class="text-black mr-3">${data.chat_name}</span><br>
                                <span class="prof-link text-capitalize">${data.type ? data.type : 'professional'}</span>
                            </div>
                        </div>
                        <div class="mt-1">${data.full_location}</div>`;

        html += '<div>';

        html += '<div class="accordion collapsed profilemoreinfo_accordion" data-toggle="collapse" data-target="#profileMoreInfo" aria-expanded="false" aria-controls="profileMoreInfo">' +
                    '<span>Show More &nbsp;<span class="accicon"><i class="fas fa-angle-down rotate-icon"></i></span></span>' +
                '</div>';

        html += '<div id="profileMoreInfo" class="collapse">';

        if (about_me.trim() !== '') {
            html += `<div class="media media-card">
                <div class="media-body">
                    <div class="mb-2"><h5 class="media-card-title">About</h5></div>
                    <div class="mt-2 mb-2">
                        ${about_me}
                    </div>
                </div>
            </div>`;
        }

        if (fun_fact.trim() !== '') {
            html += `<div class="media media-card">
                <div class="media-body">
                    <div class="mb-2"><h5 class="media-card-title">Fun Fact</h5></div>
                    <div class="mt-2 mb-2">
                        ${fun_fact}
                    </div>
                </div>
            </div>`;
        }

        if(Array.isArray(data.skill) && data.skill.length > 0) {
            html += `<div class="media media-card">
                <div class="media-body">
                    <div class="mb-2"><h5 class="media-card-title">Skills</h5></div>
                    <div class="mt-2 mb-2">`;

                    (data.skill).forEach(function (item, index) {
                        html += `<span class="skill-link">${item['value']}</span>`;
                    });

            html += `</div>
                </div>
            </div>`;
        }

        if (about_type.trim() !== '') {
            html += `<div class="media media-card">
                <div class="media-body">
                    <div class="mb-2"><h5 class="media-card-title">About Profile Type</h5></div>
                    <div class="mt-2 mb-2">
                        ${about_type}
                    </div>
                </div>
            </div>`;
        }

        let emp_list = data.employee;
        if(Array.isArray(emp_list) && emp_list.length > 0) {
            if (Array.isArray(emp_list[0]['title'])) {
                html += `<div class="media-card">
                            <div class="mb-5">
                                <h5 class="media-card-title float-left">Experience</h5>
                            </div>`;


                for (let emp_keys in emp_list) {
                    let emp_vals = emp_list[emp_keys];

                    let em_title = emp_vals['emp_title'] ? emp_vals['emp_title'] : emp_vals['title'];
                    let em_pre, em_post = '';
                    if (Array.isArray(em_title)) {
                        (em_title).forEach(function (em_value, index) {
                            [em_pre, em_post] = em_value.split(':>');
                        });
                    }

                    let em_company = emp_vals['emp_company'] ? emp_vals['emp_company'] : emp_vals['company'];
                    let em_cmp_pre, em_cmp_post;
                    if (Array.isArray(em_company)) {
                        (em_company).forEach(function (em_cmp_value, index) {
                            [em_cmp_pre, em_cmp_post] = em_cmp_value.split(':>');
                        });
                    }
                    let get_present_not = emp_vals['current_role'] === 'on' ? ' Present' : getMonthFromNumber(emp_vals['emp_end_month'], 'short') + ' ' + emp_vals['emp_year_end'];

                    let emp_placeType = emp_vals['emp_placeType'];
                    if (emp_placeType === 'rem') {
                        emp_placeType = ' . Remote';
                    } else if (emp_placeType === 'ons') {
                        emp_placeType = '. Onsite';
                    } else if (emp_placeType === 'hyb') {
                        emp_placeType = '. Hybrid';
                    } else {
                        emp_placeType = '';
                    }

                    let skills = emp_vals['skill'];
                    let items = '';
                    if (Array.isArray(skills)) {
                        items = skills.map(s_vals => s_vals.split(':>'));
                    }

                    let roletype_arr = {
                        "remo": "Remote Work",
                        "full": "Full Time",
                        "part": "Part Time",
                        "temp": "Temporary",
                        "free": "Freelance",
                        "cont": "Contract",
                        "pdin": "Paid Internship",
                        "unin": "Unpaid Internship",
                        "voln": "Volunteer"
                    };
                    let roletype = emp_vals['emp_roletype'];
                    let role_items = '';
                    if (Array.isArray(roletype)) {
                        role_items = roletype.map(value => ' . ' + roletype_arr[value]).join('');
                    }

                    html += `<div class="mt-5 mb-2">
                        <div class="d-flex mt-3">
                            <div style="height:45px;width:45px;" class="media-img d-block">
                                <img src="${_taoh_site_url_root + '/assets/images/work.png'}" alt="company logo">
                            </div>
                            <div class="media-body border-left-0 emp_response">
                                <div>
                                    <h5 class="mb-1 fs-16 fw-medium float-left"><a>${em_post}</a></h5>
                                </div><br>
                                <p class="lh-20 fs-13 font-weight-bold">${em_cmp_post}${role_items}</p>
                                <p class="lh-20 fs-13">${emp_vals['emp_start_month']} ${emp_vals['emp_year_start']} - ${get_present_not}
                                <span>${getDiffDates(emp_vals['emp_year_start'], emp_vals['emp_start_month'], emp_vals['emp_year_end'], emp_vals['emp_end_month'])}</span></p>
                                <p class="mb-2 lh-20 fs-13">${emp_vals['emp_full_location'] + emp_placeType}</p>
                                <p class="lh-20 mb-3 fs-13">${edu_vals['emp_responsibilities'].length <= 200 ? edu_vals['emp_responsibilities'] : edu_vals['emp_responsibilities'].substring(0, 200) + '......'}</p>
                                ${Array.isArray(emp_vals['skill']) ? `<p class="lh-20 fs-13"><span class="lh-20 fs-13 font-weight-bold">Skills: </span>${items[1]}</p>` : ''}
                            </div>
                            
                        </div>
                    </div>`;

                }


                html += `</div>`;
            }
        }

        let edu_list = data.education;
        if(Array.isArray(edu_list) && edu_list.length > 0) {
            if (Array.isArray(edu_list[0]['company'])) {
                html += `<div class="media-card">
                            <div class="mb-5">
                                <h5 class="media-card-title float-left">Education</h5>
                            </div>`;

                for (let edu_keys in edu_list) {
                    let edu_vals = edu_list[edu_keys];
                    let ed_name = edu_vals['company'];
                    for (let ed_key in ed_name) {
                        let ed_value = ed_name[ed_key];
                        let [ed_pre, ed_post] = ed_value.split(':>');
                    }

                    let degeree_arr = {
                        "highschool": "High School Diploma or GED",
                        "vocational": "Vocational/Technical Diploma",
                        "associate": "Associate Degree",
                        "bachelor": "Bachelor's Degree",
                        "master": "Master's Degree",
                        "doctorate": "Doctorate or Professional Degree",
                        "other": "Other (for degeree not listed above)"
                    };
                    let degree_get = edu_vals['edu_degree'];
                    let degree_items = '';
                    for (let d_key in degree_get) {
                        let d_value = degree_get[d_key];
                        degree_items = degeree_arr[d_value];
                    }

                    let d_skills = edu_vals['skill'];
                    let d_items = '';
                    for (let d_keys in d_skills) {
                        let d_vals = d_skills[d_keys];
                        d_items = d_vals.split(':>');
                    }

                    html += `<div class="mt-5 mb-2">
                      <div class="d-flex mt-3">
                        <span style="height:45px;width:45px;" class="media-img d-block">
                          <img src="${_taoh_site_url_root + '/assets/images/education.png'}" alt="company logo">
                        </span>
                        <div class="media-body border-left-0">
                          <div>
                            <h5 class="mb-1 fs-16 fw-medium float-left"><a>${ed_post}</a></h5>
                          </div><br>
                          <p class="lh-20 fs-13 font-weight-bold">${degree_items}, ${edu_vals['edu_specalize']}</p>
                          <p class="lh-20 fs-13">${getMonthFromNumber(edu_vals['edu_start_month'], 'short')} ${edu_vals['edu_start_year']} - ${getMonthFromNumber(edu_vals['edu_end_month'], 'short')} ${edu_vals['edu_complete_year']}</p>
                          ${edu_vals['edu_grade'] !== '' ? `<p class="lh-20 fs-13 font-weight-bold"><span class="lh-20 fs-13">Grade: </span>${edu_vals['edu_grade']}</p>` : ''}
                          ${edu_vals['edu_activities'] !== '' ? `<p class="mt-2 lh-20 fs-13"><span class="lh-20 fs-13 font-weight-bold">Activities and societies: </span>${edu_vals['edu_activities']}</p>` : ''}
                          <p class="mt-2 lh-20 fs-13">${edu_vals['edu_description'].length <= 200 ? edu_vals['edu_description'] : edu_vals['edu_description'].substring(0, 200) + '......'}</p>
                          ${Array.isArray(edu_vals['skill']) ? `<p class="mt-2 lh-20 fs-13"><span class="lh-20 fs-13 font-weight-bold">Skills: </span>${d_items.join(', ')}</p>` : ''}
                        </div>
                      </div>
                    </div>`;
                }

                html += '</div>';
            }
        }


        html += '</div>';

        html += '</div>';


        html += '</div>' +
            '</div>' +
            '</div>';


        resolve(html);
    });
}
function getMonthFromNumber(monthNumber, type = 'short') {
    let monthNames = [];
    if(type === 'short'){
        monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    }else{
        monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
    }

    return monthNames[monthNumber - 1];
}