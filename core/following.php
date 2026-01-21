<?php
taoh_get_header();

$pagename = 'following';

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$valid_user = $taoh_user_is_logged_in && in_array($user_info_obj?->profile_complete ?? null, [1, '1'], true);
if (!$valid_user) {
    taoh_set_error_message('You must be logged in and have a complete profile to view this page.');
    taoh_redirect( TAOH_SITE_URL_ROOT);
}

if (!$ptoken) {
    taoh_set_error_message('Invalid request. Please try again. If the problem persists, please contact support.');
    taoh_redirect( TAOH_SITE_URL_ROOT);
}

$my_ptoken = $user_info_obj->ptoken ?? '';
$my_following_count = (int)($user_info_obj->tao_following_count ?? 0);
$my_followers_count = (int)($user_info_obj->tao_followers_count ?? 0);

$is_my_profile_view = $my_ptoken === $ptoken;

if (!$is_my_profile_view) {
    taoh_set_error_message('You can only view your own following list.');
    taoh_redirect( TAOH_SITE_URL_ROOT);
}

$my_following_list = [];
$my_following_ptoken_list = [];
if ($taoh_user_is_logged_in) {
    $taoh_vals = [
        'mod' => 'core',
        'token' => taoh_get_api_token(),
        'ptoken' => $my_ptoken,
        'follow_type' => 'following',
    ];
    $taoh_vals['cache_name'] = 'followup_' . $taoh_vals['follow_type'] . '_list_' . $taoh_vals['ptoken'] . '_' . hash('crc32', http_build_query($taoh_vals));

    $taoh_vals['cache_required'] = 0;
//     $taoh_vals['debug_api'] = 1;
//     echo taoh_apicall_get('core.followup.get.list', $taoh_vals);exit();

    $followup_result = taoh_apicall_get('core.followup.get.list', $taoh_vals);
    $followup_result_array = json_decode($followup_result, true);
    if ($followup_result_array && in_array($followup_result_array['success'], [true, 'true']) && !empty($followup_result_array['output'])) {
        $my_following_list = (array)$followup_result_array['output'];
        $my_following_ptoken_list = array_column($my_following_list, 'ptoken');
    }
}

?>
    <style>
        .profile_follow_btn {
            background-color: transparent;
        }

        .profile_follow_btn[data-follow_status="1"] {
            background-color: #2557A7 !important;
            border: 1px solid #2557A7 !important;
            color: #ffffff !important;
        }

        .follow-heading-wrapper {
            max-width: 786px;
            margin-left: auto;
            margin-right: auto;
        }

        .follow-heading {
            font-weight: 600;
        }

        .page-body {
            background-color: #fff !important;
        }

        #login-prompt {
            display: block !important;
        }

        /* #following_profiles_blk {
            height: 600px;
        }*/

        #following_profiles {
            min-height: 250px;
            /*max-height:500px;*/
            /*overflow:auto;*/
            /*width:100%;*/
        }

        .no_result img {
            width: 25% !important;
        }

        .no_result p {
            margin: 20px 0 !important;
        }
    </style>

    <div class="directory">
        <header class="sticky-top bg-white border-bottom border-bottom-gray" style="top: 0; z-index: 99;">
            <section class="hero-area bg-white shadow-sm">
                <!-- <span class="stroke-shape stroke-shape-1"></span>
                <span class="stroke-shape stroke-shape-2"></span>
                <span class="stroke-shape stroke-shape-3"></span> -->
                <span class="stroke-shape stroke-shape-4"></span>
                <span class="stroke-shape stroke-shape-5"></span>
                <span class="stroke-shape stroke-shape-6"></span>
                <div class="container px-1">
                    <div class="row justify-content-center">
                        <div class="col-6 search-filter-section" style="display: none;">
                            <form id="searchFilter" class="search-form p-0 rounded-0 bg-transparent shadow-none position-relative z-index-1">
                                <div class="d-flex flex-wrap justify-content-center mt-4 mb-2">
                                    <div class="col-md-7">
                                        <input name="query" class="form-control pl-40px" type="text" id="query" maxlength="120" value="<?= $_GET['search'] ?? '' ?>" placeholder="Search for (skill, role or organization)">
                                        <span class="fa fa-search search-icon align-items-start"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-primary btn-block search-btn align-items-start" id="search" type="submit">Search<i class="la la-search ml-1"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </header>

        <div class="col-md-11 m-auto">
            <div class="d-flex align-items-center follow-heading-wrapper justify-content-between">
                <h2 class="follow-heading text-left mt-3 mb-3"><i class="fa fa-users mr-1"></i>Following</h2>
                <h6 id="total_list_count_vw" style="display: none;">Total: <span id="total_list_count">0</span></h6>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-12 mt-2" id="following_profiles_blk">
                    <div class="col-md-12 no_result" id="following_no_result" style="display: none;"></div>
                    <div class="aw aw-logo aw-loader" id="following_profiles"></div>

                    <div class="d-flex justify-content-center">
                        <div class="pt-4 pb-4" id="pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.2/jquery.twbsPagination.min.js"></script>

    <script type="text/javascript">
        let user_is_logged_in = <?= json_encode($taoh_user_is_logged_in ?? false); ?>;
        let pToken = '<?php echo $ptoken ?? ''; ?>';

        let my_following_ptoken_list = JSON.parse(`<?= json_encode(($my_following_ptoken_list ?? [])); ?>`);

        let itemsPerPage = 20; // zero for no limit
        let currentPage = 1;
        let loading = false;
        let endOfContent = false;
        let followingProfiles_isProcessing = false;

        let following_profiles_loader = $('.aw');
        let following_profiles = $('#following_profiles');
        const followingProfilesContainer = document.getElementById('following_profiles');

        $(document).ready(function () {
            loadProfiles('init');

            $('#searchFilter').on('submit', function (e) {
                e.preventDefault();
                // let formData = new FormData(this);
                // if (formData.get('query').trim() !== '') {
                    following_profiles.empty();
                    following_profiles_loader.awloader('show');
                    currentPage = 1;
                    loadProfiles('search');
                // }
            });

        });

        function loadProfiles(callFromEvent = 'init') {
            if (followingProfiles_isProcessing) {
                setTimeout(() => {
                    loadProfiles(callFromEvent);
                }, 2000);
                return;
            }

            loading = true;
            followingProfiles_isProcessing = true;

            const q = $('#query').val()?.trim() || '';

            let data = {
                'taoh_action': 'taoh_followup_users_list',
                'search': q,
                'ptoken': pToken,
                'follow_type': 'following',
            };

            if(itemsPerPage){
                data.offset = (currentPage - 1) * itemsPerPage;
                data.limit = itemsPerPage;
            }

            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function (response, textStatus, jqXHR) {
                    if (response.success) {
                        appendProfiles(response, callFromEvent);
                        $('.search-filter-section').show();
                    } else {
                        taoh_set_error_message('Unable to fetch profiles. Please try again later. If the issue persists, please contact support.', false, 'toast-middle-right', [
                            {
                                text: 'OK',
                                action: () => {
                                    window.location.href = _taoh_site_url_root + '/profile/' + pToken;
                                },
                                class: 'dojo-v1-btn float-right mt-3 mb-3'
                            }
                        ]);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching profiles:', error);
                    following_profiles_loader.awloader('hide');
                    loading = false;
                }
            });
        }

        async function appendProfiles(response, callFromEvent = 'init') {
            following_profiles_loader.awloader('hide');
            loading = false;

            if (response.success && response.output?.length > 0) {
                $('#following_no_result').hide();
                following_profiles.show();

                const maxSkillsToShow = 3;

                for (const userInfo of response.output) {
                    // if (!userInfo.profile_complete) continue;

                    const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;
                    const userAvatarSrc = buildAvatarImageOptimistic(userInfo.avatar_image, fallbackSrc, (updatedSrc) => {
                        const avatarImgElem = following_profiles.find(`.profile-pic[data-ptoken="${userInfo.ptoken}"]`);
                        if (avatarImgElem.length && avatarImgElem.attr('src') !== updatedSrc) {
                            avatarImgElem.attr('src', updatedSrc);
                        }
                    });

                    // Split skills into visible and remaining
                    const skills = userInfo.skill
                        ? Object.entries(userInfo.skill).filter(([id, skill]) => skill.value?.trim())
                        : [];

                    const visibleSkills = skills.slice(0, maxSkillsToShow);
                    const remainingSkills = skills.slice(maxSkillsToShow);

                    let skillHTML = visibleSkills.map(([id, skill]) => `<span class="btn skill-list skill_directory" data-skillid="${id}" data-skillslug="${skill.slug}">${skill.value}</span>`).join(' ');

                    if (remainingSkills.length > 0) {
                        // Container for the remaining skills
                        skillHTML += `<span class="remaining-skills-container" style="display: none;">` +
                            remainingSkills.map(([id, skill]) => `<span class="btn skill-list skill_directory" data-skillid="${id}" data-skillslug="${skill.slug}">${skill.value}</span>`).join(' ') +
                            `</span>`;

                        // Add the remaining skill count
                        skillHTML += ` <span class="remaining-skills rounded-pill cursor-pointer" data-count="${remainingSkills.length}" style="color: #6f42c1;">+${remainingSkills.length}</span>`;
                    }

                    const companyContent = userInfo.company ? Object.values(userInfo.company)
                        .filter((company) => company.value?.trim())
                        .map(company => company.value)
                        .join(', ') : '';

                    const roleContent = userInfo.title ? Object.values(userInfo.title)
                        .filter((role) => role.value?.trim())
                        .map(role => role.value)
                        .join(', ') : '';

                    let isFollowing = false;
                    if (Array.isArray(my_following_ptoken_list) && my_following_ptoken_list.includes(userInfo.ptoken)) {
                        isFollowing = true;
                    }


                    let profileCardHtml = `
                        <div class="com-v1-strip position-relative px-3 py-2 mb-3 d-flex flex-wrap align-items-center profile-${userInfo.ptoken}" style="gap: 12px;">
                            <a class="d-flex flex-column align-items-center chat-username" style="gap: 3px;" href="javascript:void(0);">
                                <img class="lazy profile-pic"  src="${userAvatarSrc}" alt="${userInfo.chat_name}" data-ptoken="${userInfo.ptoken}">
                                <p class="text-capitalize p-type-badge">${userInfo.type || 'Professional'}</p>
                            </a>

                            <div style="flex: 1;min-width: 230px;">
                                <div class="d-flex flex-wrap justify-content-between flex-column flex-lg-row align-items-lg-center my-1" style="gap: 12px;">
                                    <div>
                                        <span class="strip-name text-capitalize"> ${userInfo.chat_name} </span>

                                        <div class="d-flex align-items-center flex-wrap lh-1" style="gap: 12px;">
                                            <p class="strip-followers mb-1 d-flex align-items-center">
                                                <svg class="mr-1" width="15" height="11" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.205 2.94C2.205 2.16026 2.51475 1.41246 3.06611 0.861106C3.61746 0.309749 4.36526 0 5.145 0C5.92474 0 6.67254 0.309749 7.22389 0.861106C7.77525 1.41246 8.085 2.16026 8.085 2.94C8.085 3.71974 7.77525 4.46754 7.22389 5.01889C6.67254 5.57025 5.92474 5.88 5.145 5.88C4.36526 5.88 3.61746 5.57025 3.06611 5.01889C2.51475 4.46754 2.205 3.71974 2.205 2.94ZM0 11.0778C0 8.81541 1.83291 6.9825 4.09533 6.9825H6.19467C8.45709 6.9825 10.29 8.81541 10.29 11.0778C10.29 11.4545 9.98452 11.76 9.60783 11.76H0.682172C0.305484 11.76 0 11.4545 0 11.0778ZM11.5763 7.16625V5.69625H10.1062C9.80077 5.69625 9.555 5.45048 9.555 5.145C9.555 4.83952 9.80077 4.59375 10.1062 4.59375H11.5763V3.12375C11.5763 2.81827 11.822 2.5725 12.1275 2.5725C12.433 2.5725 12.6788 2.81827 12.6788 3.12375V4.59375H14.1488C14.4542 4.59375 14.7 4.83952 14.7 5.145C14.7 5.45048 14.4542 5.69625 14.1488 5.69625H12.6788V7.16625C12.6788 7.47173 12.433 7.7175 12.1275 7.7175C11.822 7.7175 11.5763 7.47173 11.5763 7.16625Z" fill="#555555"></path>
                                                </svg>

                                                <span>
                                                    <span class="mr-2 followers-count-view" data-ptoken="${userInfo.ptoken}" data-fscount="${safeParseInt(userInfo.tao_followers_count, 0)}">${safeParseInt(userInfo.tao_followers_count, 0)} Followers</span>
                                                    <span class="mr-2 following-count-view" data-ptoken="${userInfo.ptoken}" data-fgcount="${safeParseInt(userInfo.tao_following_count, 0)}">${safeParseInt(userInfo.tao_following_count, 0)} Following</span>
                                                </span>
                                            </p>
                                        </div>

                                        ${userInfo.full_location ? `<p class="strip-loc mb-1 d-flex align-items-center">
                                            <svg class="mr-1" width="11" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="#636161"></path>
                                            </svg>
                                            ${userInfo.full_location}
                                        </p>` : ''}

                                        <p class="strip-company mb-1 d-flex align-items-center lh-1">
                                            ${companyContent.trim() ? `<svg class="mr-1" width="11" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="#636161"></path>
                                            </svg>
                                            <span>${companyContent}</span>` : ''}

                                            ${roleContent.trim() ? `<svg class="ml-2 mr-1" width="11" height="11" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="#636161"></path>
                                            </svg>
                                            <span>${roleContent}</span>` : ''}
                                        </p>

                                        <div class="skill-con skills-v2-con mt-2">${skillHTML}</div>
                                    </div>
                                    <div class="d-flex bor-btn-con">
                                        <button type="button" data-profile_token="${userInfo.ptoken}" class="btn btn-sm mr-2 fs-12 openProfileModal">
                                            <i class="fa fa-user mr-1" aria-hidden="true"></i>
                                            <span>View Profile</span>
                                        </button>
                                        <button type="button" class="btn bor-btn profile_follow_btn" data-ptoken="${userInfo.ptoken}" data-follow_status="${isFollowing ? 1 : 0}" data-page="directory" title="${isFollowing ? 'Following' : 'Click to Follow'}">
                                            <i class="fas fa-user-plus fa-sm follow-user-plus-icon" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    following_profiles.append(profileCardHtml);
                }

                if (response.total > itemsPerPage) {
                    $('#pagination').show();
                    show_pagination('#pagination', response.total);
                } else {
                    $('#pagination').hide();
                }

                // Instantly jump to top of new content
                const following_profiles_elem = document.getElementById('following_profiles');
                if (following_profiles_elem) {
                    const top = following_profiles_elem.getBoundingClientRect().top + window.pageYOffset - 300;
                    window.scrollTo({ top, behavior: 'auto' });
                }

                /*if (response.total > 0) {
                    let displayTotalCount = $('#following_profiles li.directory-v2-list').length;
                    $('#total_list_count').text(`${displayTotalCount} of ${response.total} results`);
                    $('#total_list_count_vw').show();
                } else {
                    $('#total_list_count_vw').hide();
                }*/

            } else {
                endOfContent = true;
                if (!response.success || (callFromEvent !== 'scroll' && following_profiles.children('.profile').length === 0)) {
                    following_profiles.hide();
                    show_empty_result_screen($('#following_no_result'), callFromEvent);
                }
                // $('#total_list_count_vw').hide();
            }
            followingProfiles_isProcessing = false;
        }

        function show_empty_result_screen(element, callFromEvent = 'init') {
            element.empty();
            let noResultHtml = `<img src="${_taoh_site_url_root + '/assets/images/no_results_found.svg'}" alt="No Results" style="width: 25%">
                <div class="noresult_html">`;

            if(callFromEvent === 'search'){
                noResultHtml += `<h3>We couldn't find exactly what you were looking for</h3>
                    <p>Your search did not return any results. Please refine your search terms to improve the outcome.</p>
                    <a href="${_taoh_site_url_root + '/following/' + pToken}" class="btn theme-btn mb-4">BROWSE FOLLOWING</a>`;
            } else {
                noResultHtml += `<h3>There are no entries to display</h3>
                    <p>There are no entries to display at the moment. Please check back later.</p>
                    <a href="${_taoh_site_url_root}" class="btn theme-btn mb-4">GO HOMEPAGE</a>`;
            }
            noResultHtml += `</div>`;

            element.append(noResultHtml);
            element.show();
        }

        function show_pagination(holder, totalItems = 0) {
            $(holder).twbsPagination({
                totalPages: Math.ceil(totalItems / itemsPerPage),
                visiblePages: 0,        // 4 will render First / Prev / Next / Last + 2 numbers
                initiateStartPageClick: false,
                first: '<<',
                prev: '<',
                next: '>',
                last: '>>',
                onPageClick: function (event, page) {
                    currentPage = page;
                    following_profiles.empty();
                    following_profiles_loader.awloader('show');
                    loadProfiles('pagination');
                }
            });
        }
    </script>

<?php
taoh_get_footer();