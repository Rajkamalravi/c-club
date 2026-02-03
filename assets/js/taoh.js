let originalTitle = document.title;
let blinkInterval;
let isTitleBlinking = false;

var domain = window.location.hostname;

var domainWithProtocol = window.location.protocol + "//" + domain;
var domain_path = domainWithProtocol +'/'+_taoh_plugin_name;

$(document).ready(function () {


});

$(document).on('click', '.profile_follow_btn', function() {
    let followBtn = $(this);
    let follow_ptoken = followBtn.data('ptoken');
    let current_follow_status = parseInt(followBtn.getSyncedData('follow_status'), 10) || 0;
    let current_btn_text = followBtn.text().trim();
    let follow_page = followBtn.data('page');
    let followBtnIcon = followBtn.find('.follow-user-plus-icon');
    let type = 1;

    if (current_follow_status) {
        taoh_set_info_message('You have already followed this profile.');
        return; // Already following, no action needed - :rk temp added
    }

    if (follow_ptoken?.trim()) { // my_pToken?.trim() && my_pToken !== follow_ptoken
        if (followBtnIcon.length) {
            followBtnIcon.removeClass('fa-user-plus').addClass('fa-spinner fa-spin');
        }

        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'POST',
            data: {
                taoh_action: 'follow_profile',
                to_ptoken: follow_ptoken,
                type,
                action_type: current_follow_status ? 'unfollow' : 'follow',
                follow_page,
            },
            success: function (response) {
                if (response.success) {
                    if (current_follow_status) {
                        // Unfollowed
                        followBtn.setSyncedData('follow_status', 0);
                        if (followBtnIcon.length) {
                          followBtnIcon.removeClass('fa-spinner fa-spin').addClass('fa-user-plus');
                        }

                        // followBtn.text('Follow');
                        taoh_set_success_message('You have unfollowed this profile.');
                    } else {
                        // Followed
                        followBtn.setSyncedData('follow_status', 1);
                        if (followBtnIcon.length) {
                          followBtnIcon.removeClass('fa-spinner fa-spin').addClass('fa-user-plus');
                        }

                        // followBtn.text('Following');
                        let successMessage = (response.output === 'already_followed')
                            ? 'You are already following this profile.'
                            : 'You are now following this profile.';

                        taoh_set_success_message(successMessage);

                        const profileFollowBtn = $(`.profile_follow_btn[data-ptoken="${follow_ptoken}"]`);
                        if(profileFollowBtn?.length > 0) {
                            profileFollowBtn.attr('data-follow_status', 1);
                        }

                        if (response.output === 'followed_successfully') {

                            if (my_following_ptoken_list !== undefined && Array.isArray(my_following_ptoken_list)) {
                                my_following_ptoken_list.push(follow_ptoken);
                            }

                            const followcountTitleViewElem = $(`.follow-count-title-view[data-ptoken="${follow_ptoken}"]`);
                            if (followcountTitleViewElem.length) {
                                let existingFollowersCount = parseInt(followcountTitleViewElem.getSyncedData('fscount'), 10) || 0;
                                let existingFollowingCount = parseInt(followcountTitleViewElem.getSyncedData('fgcount'), 10) || 0;

                                let newFollowersCount = existingFollowersCount + 1;
                                let newFollowingCount = existingFollowingCount;

                                followcountTitleViewElem.setSyncedData({
                                  fscount: newFollowersCount,
                                  fgcount: newFollowingCount
                                });

                                followcountTitleViewElem.attr('title', `Followers: ${newFollowersCount}, Following: ${newFollowingCount}`);
                            }

                            const followersCountViewElem = $(`.followers-count-view[data-ptoken="${follow_ptoken}"]`);
                            if (followersCountViewElem.length) {
                                let existingFollowersCount = parseInt(followersCountViewElem.getSyncedData('fscount'), 10) || 0;
                                let newFollowersCount = existingFollowersCount + 1;

                                followersCountViewElem.setSyncedData('fscount', newFollowersCount);
                                followersCountViewElem.text(newFollowersCount + ' Followers');
                            }
                        }
                    }
                } else {
                    if (followBtnIcon.length) {
                      followBtnIcon.removeClass('fa-spinner fa-spin').addClass('fa-user-plus');
                    }
                    // followBtn.text(current_btn_text);
                    if (response.output === 'not_logged_in') {
                      taoh_set_error_message('You need to be logged in to perform this action.');
                    } else {
                      taoh_set_error_message('An error occurred while processing your request.');
                    }
                }
            },
            error: function() {
                if (followBtnIcon.length) {
                  followBtnIcon.removeClass('fa-spinner fa-spin').addClass('fa-user-plus');
                }
                // followBtn.text(current_btn_text);
                taoh_set_error_message('An error occurred while processing your request.');
            }
        });
    }
});

$(document).on('click', '.profile_following_btn', function () {
  let followingBtn = $(this);
  let following_ptoken = followingBtn.getSyncedData('ptoken');
  let following_count = parseInt(followingBtn.getSyncedData('fgcount'), 10) || 0;

  if (following_count && following_ptoken?.trim()) {
    window.open(_taoh_site_url_root + '/following/' + following_ptoken, '_blank');
  } else {
    taoh_set_info_message('No following found for this profile.');
  }
});

$(document).on('click', '.profile_followers_btn', function () {
  let followersBtn = $(this);
  let followers_ptoken = followersBtn.getSyncedData('ptoken');
  let followers_count = parseInt(followersBtn.getSyncedData('fscount'), 10) || 0;

  if (followers_count && followers_ptoken?.trim()) {
    window.open(_taoh_site_url_root + '/followers/' + followers_ptoken, '_blank');
  } else {
    taoh_set_info_message('No followers found for this profile.');
  }
});

$(document).on('click', '.skill_directory', function(e) {
    let skillElem = $(this);
    if (skillElem.parents('.stop_propagation').length > 0) {
        e.stopPropagation();
    }

    // let skillId = skillElem.getSyncedData('skillid');
    let skillSlug = skillElem.getSyncedData('skillslug');
    if(skillSlug) {
        window.open(`${_taoh_site_url_root}/directory/profile/skill/${skillSlug}`, '_blank');
    }
});

$(document).on('click', '.company_directory', function (e) {
    let companyElem = $(this);
    if (companyElem.parents('.stop_propagation').length > 0) {
        e.stopPropagation();
    }

    let companySlug = companyElem.getSyncedData('companyslug');
    if (companySlug) {
        window.open(_taoh_site_url_root + '/directory/profile/company/' + companySlug, '_blank');
    }
});

$(document).on('click', '.role_directory', function (e) {
    let roleElem = $(this);
    if (roleElem.parents('.stop_propagation').length > 0) {
        e.stopPropagation();
    }

    let roleSlug = roleElem.getSyncedData('roleslug');
    if (roleSlug) {
        window.open(_taoh_site_url_root + '/directory/profile/role/' + roleSlug, '_blank');
    }
});

$(document).on('click', '.profile_flag_directory', function (e) {
    let profileFlagElem = $(this);
    if (profileFlagElem.parents('.stop_propagation').length > 0) {
        e.stopPropagation();
    }

    let profileFlagSlug = profileFlagElem.getSyncedData('flagslug');
    if (profileFlagSlug) {
        window.open(_taoh_site_url_root + '/directory/profile/flag/' + profileFlagSlug, '_blank');
    }
});

$(document).on('click', '.profile_hobby_directory', function (e) {
    let profileHobbyElem = $(this);
    if (profileHobbyElem.parents('.stop_propagation').length > 0) {
        e.stopPropagation();
    }

    let profileHobbySlug = profileHobbyElem.getSyncedData('hobbyslug');
    if (profileHobbySlug) {
        window.open(_taoh_site_url_root + '/directory/profile/hobbies/' + profileHobbySlug, '_blank');
    }
});

$.fn.awloader = function (state) {
  if (state === "show") {
    this.addClass('aw-loader');
  } else {
    this.removeClass('aw-loader');
  }
};

$.fn.setSyncedData = function (key, value) {
  if (typeof key === 'object') {
    // Bulk setter
    this.each(function () {
      const $el = $(this);
      $.each(key, function (k, v) {
        const dataAttr = 'data-' + k.replace(/([A-Z])/g, '-$1').toLowerCase();
        $el.attr(dataAttr, v);
        $el.data(k, v);
      });
    });
  } else if (typeof key === 'string') {
    // Single key-value setter
    const dataAttr = 'data-' + key.replace(/([A-Z])/g, '-$1').toLowerCase();
    this.each(function () {
      const $el = $(this);
      $el.attr(dataAttr, value);
      $el.data(key, value);
    });
  }
  return this;
};

$.fn.getSyncedData = function (key) {
  if (this.length === 0) return undefined;
  const dataAttr = 'data-' + key.replace(/([A-Z])/g, '-$1').toLowerCase();
  return this.attr(dataAttr);
};

function awloader(element, state) {
  if (!element) return;

  if (state === "show") {
    element.classList.add('aw-loader');
  } else {
    element.classList.remove('aw-loader');
  }
}

function avatarSelect(current,avatar_url) {
  var iconSelect;
  //window.onload = function(){
      iconSelect = new IconSelect("avatarSelect");
      $('#avatarSelect').append('<input type="hidden" name="avatar" value=""/>');

      document.getElementById('avatarSelect').addEventListener('changed', function(e){
         selectedText = iconSelect.getSelectedValue();
         $("#avatarSelect input").val(selectedText)
      });

      var icons = [
        {"iconValue": "default", "iconFilePath": avatar_url+"avatar/PNG/128/avatar_def.png"},
        {
        "iconValue": "avatar_001",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_001.png"
        },
        {
        "iconValue": "avatar_002",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_002.png"
        },
        {
        "iconValue": "avatar_003",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_003.png"
        },
        {
        "iconValue": "avatar_004",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_004.png"
        },
        {
        "iconValue": "avatar_005",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_005.png"
        },
        {
        "iconValue": "avatar_006",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_006.png"
        },
        {
        "iconValue": "avatar_007",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_007.png"
        },
        {
        "iconValue": "avatar_008",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_008.png"
        },
        {
        "iconValue": "avatar_009",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_009.png"
        },
        {
        "iconValue": "avatar_010",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_010.png"
        },
        {
        "iconValue": "avatar_011",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_011.png"
        },
        {
        "iconValue": "avatar_012",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_012.png"
        },
        {
        "iconValue": "avatar_013",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_013.png"
        },
        {
        "iconValue": "avatar_014",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_014.png"
        },
        {
        "iconValue": "avatar_015",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_015.png"
        },
        {
        "iconValue": "avatar_016",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_016.png"
        },
        {
        "iconValue": "avatar_017",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_017.png"
        },
        {
        "iconValue": "avatar_018",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_018.png"
        },
        {
        "iconValue": "avatar_019",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_019.png"
        },
        {
        "iconValue": "avatar_020",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_020.png"
        },
        {
        "iconValue": "avatar_021",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_021.png"
        },
        {
        "iconValue": "avatar_022",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_022.png"
        },
        {
        "iconValue": "avatar_023",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_023.png"
        },
        {
        "iconValue": "avatar_024",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_024.png"
        },
        {
        "iconValue": "avatar_025",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_025.png"
        },
        {
        "iconValue": "avatar_026",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_026.png"
        },
        {
        "iconValue": "avatar_027",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_027.png"
        },
        {
        "iconValue": "avatar_028",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_028.png"
        },
        {
        "iconValue": "avatar_029",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_029.png"
        },
        {
        "iconValue": "avatar_030",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_030.png"
        },
        {
        "iconValue": "avatar_031",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_031.png"
        },
        {
        "iconValue": "avatar_032",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_032.png"
        },
        {
        "iconValue": "avatar_033",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_033.png"
        },
        {
        "iconValue": "avatar_034",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_034.png"
        },
        {
        "iconValue": "avatar_035",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_035.png"
        },
        {
        "iconValue": "avatar_036",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_036.png"
        },
        {
        "iconValue": "avatar_037",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_037.png"
        },
        {
        "iconValue": "avatar_038",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_038.png"
        },
        {
        "iconValue": "avatar_039",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_039.png"
        },
        {
        "iconValue": "avatar_040",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_040.png"
        },
        {
        "iconValue": "avatar_041",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_041.png"
        },
        {
        "iconValue": "avatar_042",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_042.png"
        },
        {
        "iconValue": "avatar_043",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_043.png"
        },
        {
        "iconValue": "avatar_044",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_044.png"
        },
        {
        "iconValue": "avatar_045",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_045.png"
        },
        {
        "iconValue": "avatar_046",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_046.png"
        },
        {
        "iconValue": "avatar_047",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_047.png"
        },
        {
        "iconValue": "avatar_048",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_048.png"
        },
        {
        "iconValue": "avatar_049",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_049.png"
        },
        {
        "iconValue": "avatar_050",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_050.png"
        },
        {
        "iconValue": "avatar_051",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_051.png"
        },
        {
        "iconValue": "avatar_052",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_052.png"
        },
        {
        "iconValue": "avatar_053",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_053.png"
        },
        {
        "iconValue": "avatar_054",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_054.png"
        },
        {
        "iconValue": "avatar_055",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_055.png"
        },
        {
        "iconValue": "avatar_056",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_056.png"
        },
        {
        "iconValue": "avatar_057",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_057.png"
        },
        {
        "iconValue": "avatar_058",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_058.png"
        },
        {
        "iconValue": "avatar_059",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_059.png"
        },
        {
        "iconValue": "avatar_060",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_060.png"
        },
        {
        "iconValue": "avatar_061",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_061.png"
        },
        {
        "iconValue": "avatar_062",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_062.png"
        },
        {
        "iconValue": "avatar_063",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_063.png"
        },
        {
        "iconValue": "avatar_064",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_064.png"
        },
        {
        "iconValue": "avatar_065",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_065.png"
        },
        {
        "iconValue": "avatar_066",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_066.png"
        },
        {
        "iconValue": "avatar_067",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_067.png"
        },
        {
        "iconValue": "avatar_068",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_068.png"
        },
        {
        "iconValue": "avatar_069",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_069.png"
        },
        {
        "iconValue": "avatar_070",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_070.png"
        },
        {
        "iconValue": "avatar_071",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_071.png"
        },
        {
        "iconValue": "avatar_072",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_072.png"
        },
        {
        "iconValue": "avatar_073",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_073.png"
        },
        {
        "iconValue": "avatar_074",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_074.png"
        },
        {
        "iconValue": "avatar_075",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_075.png"
        },
        {
        "iconValue": "avatar_076",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_076.png"
        },
        {
        "iconValue": "avatar_077",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_077.png"
        },
        {
        "iconValue": "avatar_078",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_078.png"
        },
        {
        "iconValue": "avatar_079",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_079.png"
        },
        {
        "iconValue": "avatar_080",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_080.png"
        },
        {
        "iconValue": "avatar_081",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_081.png"
        },
        {
        "iconValue": "avatar_082",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_082.png"
        },
        {
        "iconValue": "avatar_083",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_083.png"
        },
        {
        "iconValue": "avatar_084",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_084.png"
        },
        {
        "iconValue": "avatar_085",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_085.png"
        },
        {
        "iconValue": "avatar_086",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_086.png"
        },
        {
        "iconValue": "avatar_087",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_087.png"
        },
        {
        "iconValue": "avatar_088",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_088.png"
        },
        {
        "iconValue": "avatar_089",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_089.png"
        },
        {
        "iconValue": "avatar_090",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_090.png"
        },
        {
        "iconValue": "avatar_091",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_091.png"
        },
        {
        "iconValue": "avatar_092",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_092.png"
        },
        {
        "iconValue": "avatar_093",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_093.png"
        },
        {
        "iconValue": "avatar_094",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_094.png"
        },
        {
        "iconValue": "avatar_095",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_095.png"
        },
        {
        "iconValue": "avatar_096",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_096.png"
        },
        {
        "iconValue": "avatar_097",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_097.png"
        },
        {
        "iconValue": "avatar_098",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_098.png"
        },
        {
        "iconValue": "avatar_099",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_099.png"
        },
        {
        "iconValue": "avatar_100",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_100.png"
        },
        {
        "iconValue": "avatar_101",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_101.png"
        },
        {
        "iconValue": "avatar_102",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_102.png"
        },
        {
        "iconValue": "avatar_103",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_103.png"
        },
        {
        "iconValue": "avatar_104",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_104.png"
        },
        {
        "iconValue": "avatar_105",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_105.png"
        },
        {
        "iconValue": "avatar_106",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_106.png"
        },
        {
        "iconValue": "avatar_107",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_107.png"
        },
        {
        "iconValue": "avatar_108",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_108.png"
        },
        {
        "iconValue": "avatar_109",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_109.png"
        },
        {
        "iconValue": "avatar_110",
        "iconFilePath": avatar_url+"avatar/PNG/128/avatar_110.png"
        }
      ];

      iconSelect.refresh(icons);
      if(current) {
        let index = icons.findIndex(o => o.iconValue === current);
        iconSelect.setSelectedIndex(index);
      }

  //}
}

//Blog Category select
function blogCategorySelect() {
  new TomSelect('#blogCategorySelect',{
      maxItems: 10,
      valueField: 'slug',
      labelField: 'title',
      searchField: ['title', 'slug'],
      load: function(query, callback) {
        var data = {
           'taoh_action': 'taoh_get_blog_categories',
           'query': query
         };
         let setData = {}
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          callback(res);
        }).fail(function() {
          callback();
        })
      },
      // custom rendering function for options
      render: {
        option: function(item, escape) {
          return `<div class="py-2 d-flex">
                <div class="mb-1">
                  <span class="h5">
                    ${ escape(item.title) }
                  </span>
                </div>
              </div>`;
        }
      }
    });
}

//Get Current Job role Chat/Public info Tab
function skillSelect() {
  new TomSelect('#skillSelect',{
      create: true,
      maxItems: 10,
      valueField: 'id',
      labelField: 'label',
      searchField: ['label','value'],

  		onOptionAdd: function(value, callback) {
  			var data = {
           'taoh_action': 'taoh_add_skills',
           'skill': value,
  				 'mod': 'skill'
         };
  			jQuery.post(_taoh_site_ajax_url, data, function(response) {
    			//console.log(response)
        })
  			// data.value = 'some';
  			// console.log(data);
  		},
      load: function(query, callback) {
        var data = {
           'taoh_action': 'taoh_get_skills',
           'query': query
         };
         let setData = {}
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          callback(res);
        }).fail(function() {
          callback();
        })
      },
      // custom rendering function for options
      render: {
        option: function(item, escape) {
          return `<div class="py-2 d-flex">
                <div class="mb-1">
                  <span class="h5">
                    ${ escape(item.label) }
                  </span>
                </div>
              </div>`;
        },
      }
    });

  $("#skillSelect").on('change', function(){
      $('.ts-control input').val('');
      //$('#skillSelect-ts-control').remove();
  })
}

function tagsSelect() {
  new TomSelect('#tagsSelect',{
    // You can add TomSelect options here
    plugins: ['remove_button'],
    create: true,
    sortField: {
        field: 'text',
        direction: 'asc'
    },
  });
}

function edu_skillSelect(index) {
  console.log(index);
  new TomSelect('#edu_skillSelect_'+index,{
      create: true,
      maxItems: 10,
      valueField: 'id',
      labelField: 'label',
      searchField: ['label','value'],

  		onOptionAdd: function(value, callback) {
  			var data = {
           'taoh_action': 'taoh_add_skills',
           'skill': value,
  				 'mod': 'skill'
         };
  			jQuery.post(_taoh_site_ajax_url, data, function(response) {
    			//console.log(response)
        })
  			// data.value = 'some';
  			// console.log(data);
  		},
      load: function(query, callback) {
        var data = {
           'taoh_action': 'taoh_get_skills',
           'query': query
         };
         let setData = {}
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          callback(res);
          var scrollableContent = document.getElementById('edu_modal');
          scrollableContent.scrollTop = scrollableContent.scrollHeight; // Scroll to bottom
        }).fail(function() {
          callback();
        })
      },
      // custom rendering function for options
      render: {
        option: function(item, escape) {
          return `<div class="py-2 d-flex">
                <div class="mb-1">
                  <span class="h5">
                    ${ escape(item.label) }
                  </span>
                </div>
              </div>`;
        },
      }
    });

  $("#edu_skillSelect"+index).on('change', function(){
      $('.ts-control input').val('');
      //$('#skillSelect-ts-control').remove();
  })
}

function emp_skillSelect(index) {
  console.log(index);
  new TomSelect('#emp_skillSelect_'+index,{
      create: true,
      maxItems: 10,
      valueField: 'id',
      labelField: 'label',
      searchField: ['label','value'],

  		onOptionAdd: function(value, callback) {
  			var data = {
           'taoh_action': 'taoh_add_skills',
           'skill': value,
  				 'mod': 'skill'
         };
  			jQuery.post(_taoh_site_ajax_url, data, function(response) {
    			//console.log(response)
        })
  			// data.value = 'some';
  			// console.log(data);
  		},
      load: function(query, callback) {
        var data = {
           'taoh_action': 'taoh_get_skills',
           'query': query
         };
         let setData = {}
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          callback(res);
          var scrollableContent = document.getElementById('emp_modal');
          scrollableContent.scrollTop = scrollableContent.scrollHeight; // Scroll to bottom
        }).fail(function() {
          callback();
        })
      },
      // custom rendering function for options
      render: {
        option: function(item, escape) {
          return `<div class="py-2 d-flex">
                <div class="mb-1">
                  <span class="h5">
                    ${ escape(item.label) }
                  </span>
                </div>
              </div>`;
        },
      }
    });

  $("#emp_skillSelect"+index).on('change', function(){
      $('.ts-control input').val('');
      //$('#skillSelect-ts-control').remove();
  })
}

//User Location
function locationSelect() {
  new TomSelect('#locationSelect',{
    create: false,
    maxItems: 1,
    placeholder: 'Location search',
  	valueField: 'coordinates',
  	labelField: 'location',
  	searchField: ['location','coordinates'],
	
  	load: function(query, callback) {
      var data = {
         'taoh_action': 'taoh_get_location',
         'query': query
       };
       let setData = {}
      jQuery.post(_taoh_site_ajax_url, data, function(response) {
          if (Array.isArray(response)) {
              const results = response.map(item => ({
                  location: item.location.split(",").map(s => s.trim()).filter(Boolean).join(", "),
                  coordinates: item.coordinates
              }));
              callback(results);
          } else {
              callback([]);
          }
        /* let res = response.map((item) => {
          return {
            location: (item.location).split(",").map(s => s.trim()).filter(Boolean).join(", "),
            coordinates: item.coordinates
          };
        });
        callback(res); */
      }).fail(function() {
        callback([]);
      })
  	},
    onFocus: function(){
      $('#locationSelect-ts-control').keyup(function(){
        $('.locationSelect .item').html('');
      }) 
    },
    onChange: function(item) {
      let coordinates = item.split('::');
      if(coordinates.length === 2 ) {
        let lon = coordinates[1];
        let lat = coordinates[0];
        var data = {
           'taoh_action': 'taoh_get_geohash',
           'lon': lon,
           'lat': lat
         };

        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          $("#geohash").val(res.geohash);
        }).fail(function() {
        })
		
      }

      //alert(item);
    },
  	// custom rendering function for options
  	render: {
  		option: function(item, escape) {
  			return `<div class="py-2 d-flex">
  						<div class="mb-1">
  							<span class="h5">
  								${ escape(item.location) }
  							</span>
  						</div>
  					</div>`;
  		},


  	},
    onItemAdd(value, $item) {
		let label = $($item).html();
		$('#coordinateLocation').val(label);
		let countryCode = label.split(',');
		
		let coordinates = value.split('::');
        let lon = coordinates[1];
        let lat = coordinates[0];
		
		if(typeof timeZoneInstance != 'undefined') {
			var data = {
			   'op': 'timezone',
			   'country_code': countryCode[2],
			   'lat': lat,
			   'lon': lon,
			 };
		 
			jQuery.get('https://opslogy.com/mapn/', data, function(response) {
				let res = JSON.parse(response);
				timeZoneInstance.addOption({
					name: res.output,
				});
				timeZoneInstance.setValue(res.output); 
			}).fail(function() {
			})
			
		}
	
    }
  });
}

function joblocationSelect() {
  new TomSelect('#joblocationSelect',{
    create: false,
    maxItems: 1,
  	valueField: 'coordinates',
  	labelField: 'location',
  	searchField: ['location','coordinates'],
	
  	load: function(query, callback) {
      var data = {
         'taoh_action': 'taoh_get_location',
         'query': query
       };
       let setData = {}
      jQuery.post(_taoh_site_ajax_url, data, function(response) {
        let res = response.map((item) => {
          return {
            location: (item.location).split(",").map(s => s.trim()).filter(Boolean).join(", "),
            coordinates: item.coordinates
          };
        });
        callback(res);
      }).fail(function() {
        callback();
      })
  	},
    onFocus: function(){
      $('#locationSelect-ts-control').keyup(function(){
        $('.locationSelect .item').html('');
      }) 
    },
    onChange: function(item) {
      let coordinates = item.split('::');
      if(coordinates.length === 2 ) {
        let lon = coordinates[1];
        let lat = coordinates[0];
        var data = {
           'taoh_action': 'taoh_get_geohash',
           'lon': lon,
           'lat': lat
         };

        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          $("#geohash").val(res.geohash);
        }).fail(function() {
        })
		
      }

      //alert(item);
    },
  	// custom rendering function for options
  	render: {
  		option: function(item, escape) {
  			return `<div class="py-2 d-flex">
  						<div class="mb-1">
  							<span class="h5">
  								${ escape(item.location) }
  							</span>
  						</div>
  					</div>`;
  		},


  	},
    onItemAdd(value, $item) {
		let label = $($item).html();
		$('#coordinateLocation').val(label);
		let countryCode = label.split(',');
		
		let coordinates = value.split('::');
        let lon = coordinates[1];
        let lat = coordinates[0];
		
		if(typeof timeZoneInstance != 'undefined') {
			var data = {
			   'op': 'timezone',
			   'country_code': countryCode[2],
			   'lat': lat,
			   'lon': lon,
			 };
		 
			jQuery.get('https://opslogy.com/mapn/', data, function(response) {
				let res = JSON.parse(response);
				timeZoneInstance.addOption({
					name: res.output,
				});
				timeZoneInstance.setValue(res.output); 
			}).fail(function() {
			})
			
		}
	
    }
  });
}

function emp_locationSelect(index) {
  new TomSelect('#emp_locationSelect_'+index,{
    create: false,
    maxItems: 1,
  	valueField: 'coordinates',
  	labelField: 'location',
  	searchField: ['location','coordinates'],
	
  	load: function(query, callback) {
      var data = {
         'taoh_action': 'taoh_get_location',
         'query': query
       };
       let setData = {}
      jQuery.post(_taoh_site_ajax_url, data, function(response) {
        let res = response.map((item) => {
          return {
            location: (item.location).split(",").map(s => s.trim()).filter(Boolean).join(", "),
            coordinates: item.coordinates
          };
        });
        callback(res);
      }).fail(function() {
        callback();
      })
  	},
    onFocus: function(){
      $('#emp_locationSelect_'+index+'-ts-control').keyup(function(){
        $('.emp_locationSelect_'+index+' .item').html('');
      }) 
    },
    onChange: function(item) {
      let coordinates = item.split('::');
      if(coordinates.length === 2 ) {
        let lon = coordinates[1];
        let lat = coordinates[0];
        var data = {
           'taoh_action': 'taoh_get_geohash',
           'lon': lon,
           'lat': lat
         };

        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          $("#emp_geohash"+index).val(res.geohash);
        }).fail(function() {
        })
		
      }

      //alert(item);
    },
  	// custom rendering function for options
  	render: {
  		option: function(item, escape) {
  			return `<div class="py-2 d-flex">
  						<div class="mb-1">
  							<span class="h5">
  								${ escape(item.location) }
  							</span>
  						</div>
  					</div>`;
  		},


  	},
    onItemAdd(value, $item) {
		let label = $($item).html();
		$('#emp_coordinateLocation'+index).val(label);
		let countryCode = label.split(',');
		
		let coordinates = value.split('::');
        let lon = coordinates[1];
        let lat = coordinates[0];
		
		if(typeof timeZoneInstance != 'undefined') {
			var data = {
			   'op': 'timezone',
			   'country_code': countryCode[2],
			   'lat': lat,
			   'lon': lon,
			 };
		 
			jQuery.get('https://opslogy.com/mapn/', data, function(response) {
				let res = JSON.parse(response);
				timeZoneInstance.addOption({
					name: res.output,
				});
				timeZoneInstance.setValue(res.output); 
			}).fail(function() {
			})
			
		}
	
    }
  });
}

//Role select
function roleSelect() {
  //Get Current Job role Chat/Public info Tab
  new TomSelect('#roleSelect',{
      create: true,
      maxItems: 1,
  		valueField: 'id',
  		labelField: 'label',
  		searchField: ['label','value'],
      onOptionAdd: function(value, callback) {
        var data = {
           'taoh_action': 'taoh_add_role',
           'role': value,
           'mod': 'role'
         };
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          //console.log(response)
        })
        // data.value = 'some';
        // console.log(data);
      },
  		load: function(query, callback) {
        var data = {
           'taoh_action': 'taoh_get_roles',
           'query': query
         };
         let setData = {}
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          callback(res);
        }).fail(function() {
          callback();
        })
  		},
      onFocus: function(){
        $('.roleSelect').keyup(function(){
          $('.roleSelect .item').html('');
        }) 
      },
  		// custom rendering function for options
  		render: {
  			option: function(item, escape) {
  				return `<div class="py-2 d-flex">
  							<div class="mb-1">
  								<span class="h5">
  									${ escape(item.label) }
  								</span>
  							</div>
  						</div>`;
  			}
  		}

  	});

}

function emp_roleSelect(index=0) {
  //Get Current Job role Chat/Public info Tab
  new TomSelect('#emp_roleSelect_'+index,{
      create: true,
      maxItems: 1,
  		valueField: 'id',
  		labelField: 'label',
  		searchField: ['label','value'],
      onOptionAdd: function(value, callback) {
        var data = {
           'taoh_action': 'taoh_add_role',
           'role': value,
           'mod': 'role'
         };
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          //console.log(response)
        })
        // data.value = 'some';
        // console.log(data);
      },
  		load: function(query, callback) {
        var data = {
           'taoh_action': 'taoh_get_roles',
           'query': query
         };
         let setData = {}
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          callback(res);
        }).fail(function() {
          callback();
        })
  		},
  		// custom rendering function for options
  		render: {
  			option: function(item, escape) {
  				return `<div class="py-2 d-flex">
  							<div class="mb-1">
  								<span class="h5">
  									${ escape(item.label) }
  								</span>
  							</div>
  						</div>`;
  			}
  		}

  	});

}

function roleTypeSelect() {
  new TomSelect("#roleTypeSelect",{
     maxItems: 10
  });
}

function emp_roleTypeSelect(index) {
  new TomSelect("#emp_roleTypeSelect_"+index,{
     maxItems: 5
  });
}

function roleTypeSelect_hire() {
  new TomSelect("#roleTypeSelect_hire",{
     maxItems: 10
  });
}

function roleTypeSelect_job() {
  new TomSelect("#roleTypeSelect_job",{
     maxItems: 10
  });
}

function flagsSelect() {
  new TomSelect("#flagsSelect",{
     maxItems: 5
  });
}

function flagsSelect_hire() {
  new TomSelect("#flagsSelect_hire",{
     maxItems: 5
  });
}

function flagsSelect_job() {
  new TomSelect("#flagsSelect_job",{
     maxItems: 5
  });
}


//Company select
function companySelect() {
  new TomSelect('#companySelect',{
      create: true,
      maxItems: 1,
      valueField: 'id',
      labelField: 'label',
      searchField: ['label','value'],
      onOptionAdd: function(value, callback) {
  			var data = {
           'taoh_action': 'taoh_add_company',
           'company': value,
  				 'mod': 'company'
         };
  			jQuery.post(_taoh_site_ajax_url, data, function(response) {
    			//console.log(response)
        })
  			// data.value = 'some';
  			// console.log(data);
  		},
      load: function(query, callback) {
        var data = {
           'taoh_action': 'taoh_get_companies',
           'query': query
         };
         let setData = {}
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          callback(res);
        }).fail(function() {
          callback();
        })
      },
      onFocus: function(){
        $('.companySelect').keyup(function(){
          $('.companySelect .item').html('');
        }) 
      },
      // custom rendering function for options
      render: {
        option: function(item, escape) {
          return `<div class="py-2 d-flex">
                <div class="mb-1">
                  <span class="h5">
                    ${ escape(item.label) }
                  </span>
                </div>
              </div>`;
        }
      }
    });
}

function emp_companySelect(index=0) {
  new TomSelect('#emp_companySelect_'+index,{ 
      create: true,
      maxItems: 1,
      valueField: 'id',
      labelField: 'label',
      searchField: ['label','value'],
      onOptionAdd: function(value, callback) {
  			var data = {
           'taoh_action': 'taoh_add_company',
           'company': value,
  				 'mod': 'company'
         };
  			jQuery.post(_taoh_site_ajax_url, data, function(response) {
    			//console.log(response)
        })
  			// data.value = 'some';
  			// console.log(data);
  		},
      load: function(query, callback) {
        var data = {
           'taoh_action': 'taoh_get_companies',
           'query': query
         };
         let setData = {}
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          callback(res);
        }).fail(function() {
          callback();
        })
      },
      // custom rendering function for options
      
      render: {      
        option: function(item, escape) {
          return `<div class="py-2 d-flex">
                <div class="mb-1">
                  <span class="h5">
                    ${ escape(item.label) }
                  </span>
                </div>
              </div>`;
        }
      }
    });
}

function edu_companySelect(index=0) {
  new TomSelect('#edu_companySelect_'+index,{ 
      create: true,
      maxItems: 1,
      valueField: 'id',
      labelField: 'label',
      searchField: ['label','value'],
      onOptionAdd: function(value, callback) {
  			var data = {
           'taoh_action': 'taoh_add_company',
           'company': value,
  				 'mod': 'company'
         };
  			jQuery.post(_taoh_site_ajax_url, data, function(response) {
    			//console.log(response)
        })
  			// data.value = 'some';
  			// console.log(data);
  		},
      load: function(query, callback) {
        var data = {
           'taoh_action': 'taoh_get_companies',
           'query': query
         };
         let setData = {}
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response;
          callback(res);
        }).fail(function() {
          callback();
        })
      },
      // custom rendering function for options
      
      render: {      
        option: function(item, escape) {
          return `<div class="py-2 d-flex">
                <div class="mb-1">
                  <span class="h5">
                    ${ escape(item.label) }
                  </span>
                </div>
              </div>`;
        }
      }
    });
}

//Timezone select
function timeZoneSelect(index = 0) {

  document.querySelectorAll('.select_local_timezone').forEach((el)=>{
	  //#local_timezoneSelect
   timeZoneInstance = new TomSelect(el,{
      create: false,
      maxItems: 1,
      valueField: 'name',
      labelField: 'name',
      searchField: 'name',
      load: function(query, callback) {
        var data = {
          'taoh_action': 'taoh_get_timezones',
          'query': query
        };
        jQuery.post(_taoh_site_ajax_url, data, function(response) {
          let res = response.response;
          let out = [];
          res.map(function(name){
            out.push({name})
          })
          console.log(out);
          callback(out);
        }).fail(function() {
          callback();
        })
      },
      // custom rendering function for options
      render: {
        option: function(item, escape) {
          return `<div class="py-2 d-flex">
                <div class="mb-1">
                  <span class="h5">
                    ${ escape(item.name) }
                  </span>
                </div>
              </div>`;
        },


      },
      onItemAdd(value, $item) {
        //$('#coordinateLocation').val($($item).html());
      }
    });
});

//#local_timezoneSelect
   timeZoneInstance = new TomSelect('#local_timezoneSelect,#local_timezoneSelect_exh, #local_timezoneSelect_spk .timezone-select_'+index,{
    create: false,
    maxItems: 1,
  	valueField: 'name',
  	labelField: 'name',
  	searchField: 'name',
  	load: function(query, callback) {
      var data = {
         'taoh_action': 'taoh_get_timezones',
         'query': query
       };
      jQuery.post(_taoh_site_ajax_url, data, function(response) {
        let res = response.response;
  			let out = [];
  			res.map(function(name){
  				out.push({name})
  			})
  			console.log(out);
        callback(out);
      }).fail(function() {
        callback();
      })
  	},
  	// custom rendering function for options
  	render: {
  		option: function(item, escape) {
  			return `<div class="py-2 d-flex">
  						<div class="mb-1">
  							<span class="h5">
  								${ escape(item.name) }
  							</span>
  						</div>
  					</div>`;
  		},


  	},
    onItemAdd(value, $item) {
      //$('#coordinateLocation').val($($item).html());
    }
  });
  
}

function moreInputFaq(name, edit, index) {
  index = parseInt(index);
  this.removeFaq = function removeFaq(e, el) {
     $(el).closest('.item').remove();
     index = index-1;
  }

  this.faqTemplate = function(add = false) {
    return `<div class="item row mt-2 align-items-center border-top pt-2">
      <div class="col-11">
        <input type="text" name="${name}[${index}][question]" class="form-control mb-2"  placeholder="Question">
		<textarea type="text" name="${name}[${index}][answer]" placeholder="Answer" class="form-control"></textarea>
      </div>

      <div class="col-1">
      ${add ? `<span onclick="addFaq()" class="btn-primary rounded-circle p-1 fs-12"><i class="fas fa-plus"></i></span>
          ` : `  <span onclick="removeFaq(event, this)" class="btn-danger rounded-circle p-1 fs-12"><i class="fas fa-times"></i></span>`
        }
      </div>
    </div>`
  }

  if(!edit) {
   // $('#addMoreFaq').append(inputTemplate(false));
  }

  this.addFaq = function addFaq() {
    index = index + 1;
    $('#addMoreFaq').append(faqTemplate);

  }
}

function moreInputInit(name, edit, index) {
  index = parseInt(index);
  this.removeInput = function removeInput(e, el) {
     $(el).closest('.item').remove();
     index = index-1;
  }

  this.inputTemplate = function(add = false) {
    return `<div class="item row mt-2 align-items-center">
      <div class="col">
        <input type="text" name="${name}[${index}][label]" class="form-control" placeholder="">
      </div>
      <div class="col">
        <textarea type="text" name="${name}[${index}][value]" class="form-control" placeholder=""></textarea>
      </div>
      <div class="col-1">
      ${add ? `<span onclick="addInput()" class="btn-primary rounded-circle p-1 fs-12"><i class="fas fa-plus"></i></span>
          ` : `  <span onclick="removeInput(event, this)" class="btn-danger rounded-circle p-1 fs-12"><i class="fas fa-times"></i></span>`
        }
      </div>
    </div>`
  }

  if(!edit) {
    $('#addMoreInput').append(inputTemplate(true));
  }

  this.addInput = function addInput() {
    index = index + 1;
    $('#addMoreInput').append(inputTemplate);

  }
}

function moreInputPermissionInit(name, edit, index) {
  index = parseInt(index);

  this.alphaGen = function () {
    let alphaCode = "";
    let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for (var i = 0; i < 5; i++) {
      alphaCode += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    return alphaCode;
  }


  this.removePermissionInput = function removePermissionInput(e, el) {
     $(el).closest('.item').remove();
     index = index-1;
  }

  this.visibilityChange = function(e, el) {
    if($(el).val() == "invite_code") {
      $(el).closest('.visibility-container').find('.code-container').html(`<input type="text" name="${name}[${index}][code]" class="form-control mt-2 text-uppercase" value="${alphaGen()}">`);
    } else {
      $(el).closest('.visibility-container').find('.code-container').empty();
    }
    console.log($(el).val());
    console.log(e);
  }

  this.template = function(add = false) {
    return `<div class="item row mt-2 mb-2 align-items-center border-top pt-3">
      <div class="col">
        <input type="text" required name="${name}[${index}][title]" value="" class="form-control" placeholder="Ex: Organizer">
        <textarea placeholder="Short description about this role." class="form-control mt-2" name="${name}[${index}][message]"></textarea>
        <textarea placeholder="Any question for this role type?" class="form-control mt-2" name="${name}[${index}][question]"></textarea>
        <div class="mt-2 visibility-container">
          <b>Visibility</b>:
          <input checked onclick="visibilityChange(event, this)" class="ml-2" name="${name}[${index}][visibility]" id="openToAll${index}" type="radio" value="open_to_all">
          <label for="openToAll${index}">Open to All</label>

          <input onclick="visibilityChange(event, this)" class="ml-2" name="${name}[${index}][visibility]" id="inviteCode${index}" type="radio" value="invite_code">
          <label for="inviteCode${index}">Invite Code</label>


          <div class="code-container"></div>
        </div>
      </div>
      <div class="col-1">
      ${add ? `<span onclick="addPermissionInput()" class="btn-primary rounded-circle p-1 fs-12"><i class="fas fa-plus"></i></span>
          ` : `  <span onclick="removePermissionInput(event, this)" class="btn-danger rounded-circle p-1 fs-12"><i class="fas fa-times"></i></span>`
        }
      </div>
    </div>`
  }

  if(!edit) {
    $('#addMoreInputPermission').append(template(true));
  }

  this.addPermissionInput = function addPermissionInput() {
    index = index + 1;
    $('#addMoreInputPermission').append(template(false));
  }

}

function moreDatesInit(name, edit, index) {
  index = parseInt(index);

  this.removeInput = function removeInput(e, el) {
     $(el).closest('.item').remove();
     index = index-1;
  }

  this.dateTemplate = function(add = false) {
    return `<div class="item row mt-2 align-items-center bg-gray border py-3">
      <div class="col-4">
        <label>Start At</label>
        <input type="datetime-local" name="${name}[start_at]" class="form-control">
      </div>
      <div class="col-4">
        <label>End At</label>
        <input type="datetime-local" name="${name}[end_at]" class="form-control">
      </div>
      <div class="col-4">
        <label>Attende Limit</label>
        <input type="text" name="attendees" class="form-control">
      </div>

      <div class="col-12 mt-4 text-right">
        <input class="btn btn-success p-1 text-capitalize" type="submit" name="action" value="addevent" />
        <span onclick="removeInput(event, this)" class="btn btn-warning p-1">Cancel</span>
      </div>
    </div>
    <div class="divider my-3"></div>`
  }

  if(!edit) {
    $('#addMoreDates').append(dateTemplate(true));
  }

  this.addMoreDate = function addMoreDate() {
    index = index + 1;
    $('#addMoreDates').append(dateTemplate);
  }

  this.eventAction = function eventAction(token, msg) {
    if (confirm(msg)) {
      $('#eventField').html('<input name="eventtoken" type="hidden" value="'+token+'">');
    } else {
      return false;
    }
  }
}

//Format Response For role,company,skillchat
/* function format_object(data) {
  const output = {};
  output.total = data.output.total;
  output.count = data.output.count;
  output.items = [];
  let keyUpdated = [];

  function search(nameKey, myArray){
    for (var i=0; i < myArray.length; i++) {
        if (myArray[i].id === nameKey) {
            return myArray[i];
        }
    }
  }
  console.log( data.output.list);
  for (let [key, result] of Object.entries(data.output.list)) {

    if(result != null){
        if (typeof result.company != "undefined") {
        for (const [id, name] of Object.entries(result.company)) {
            var text = name.split(":>");
            result.company = {"id": id, "slug": text[0], name: text[1]};
        }
        }

        if (typeof result.locn != "undefined") {
        for (const [id, name] of Object.entries(result.locn)) {
            var text = name.split(":>");
            result.locn = {"id": id, "slug": text[0], name: text[1]};
        }
        }

        if (typeof result.skill != "undefined") {
        for (const [id, name] of Object.entries(result.skill)) {
            var text = name.split(":>");
            result.skill = {"id": id, "slug": text[0], name: text[1]};
        }
        }

        if (typeof result.rolechat != "undefined") {
        for (const [id, name] of Object.entries(result.rolechat)) {
            var text = name.split(":>");
            result.rolechat = {"id": id, "slug": text[0], name: text[1]};
        }
        }

        if (typeof result.roletype != "undefined") {
        let role = [
            {id: "remo", text: "Remote Work", color: "primary"},
            {id: "full", text: "Full Time", color: "success"},
            {id: "part", text: "Part Time", color: "danger"},
            {id: "temp", text: "Temporary", color: "warning"},
            {id: "free", text: "Freelance", color: "info"},
            {id: "cont", text: "Contract", color: "secondary"},
            {id: "pdin", text: "Paid Internship", color: "dark"},
            {id: "unin", text: "Unpaid Internship", color: "muted"},
            {id: "voln", text: "Volunteer", color: "success"}
        ];

        let roles = [];
        $.each(result.roletype, function( index, value ) {
            roles.push(search(value, role));
        });
        result.roletype = roles;
        }
        output.items.push(result);
    }

   }
   return output;
} */

function format_object(data) {
  
      const obj_result = {
        total: data.output.total,
        count: data.output.count,
        list: []
    };
  function search(nameKey, myArray) {
      return myArray.find(item => item.id === nameKey);
  }

  function transformProperties(obj) {
    if (typeof obj !== "undefined" && typeof obj === "object") {
      return Object.entries(obj).map(([key, value]) => {
        if (typeof value === "string" && value.includes(":>")) {
          const [slug, name] = value.split(":>");
          return { id: key, slug, name };
        }else {
          return {id: value['id'], slug: value['slug'], name: value['name']};
        }
      });
    }
    return {};
  }

  const role = [
      { id: "remo", text: "Remote Work", color: "primary" },
      { id: "full", text: "Full Time", color: "success" },
      { id: "part", text: "Part Time", color: "danger" },
      { id: "temp", text: "Temporary", color: "warning" },
      { id: "free", text: "Freelance", color: "info" },
      { id: "cont", text: "Contract", color: "secondary" },
      { id: "pdin", text: "Paid Internship", color: "dark" },
      { id: "unin", text: "Unpaid Internship", color: "muted" },
      { id: "voln", text: "Volunteer", color: "success" }
  ];

  for (const result of Object.values(data.output.list)) {
      if (result != null) {
          result.company = transformProperties(result.company);
          result.locn = transformProperties(result.locn);
          result.skill = transformProperties(result.skill);
          result.rolechat = transformProperties(result.rolechat);

          if (typeof result.roletype !== "undefined") {
              result.roletype = result.roletype.map(value => search(value, role));
          }

          obj_result.list.push(result);
      }
  }

  const returnData = {
    success: data.success,
    output: obj_result
  };

  return returnData;
}
function format_object_event(data) {
  const obj_result = {
    total: '',
    count: '',
    list: []
};
  
  
  function search(nameKey, myArray) {
      return myArray.find(item => item.id === nameKey);
  }

  function transformProperties(obj) {
    if (typeof obj !== "undefined" && typeof obj === "object") {
      return Object.entries(obj).map(([key, value]) => {
        if (typeof value === "string" && value.includes(":>")) {
          const [slug, name] = value.split(":>");
          return { id: key, slug, name };
        }else {
          return {id: value['id'], slug: value['slug'], name: value['name']};
        }
      });
    }
    return {};
  }

  const role = [
      { id: "remo", text: "Remote Work", color: "primary" },
      { id: "full", text: "Full Time", color: "success" },
      { id: "part", text: "Part Time", color: "danger" },
      { id: "temp", text: "Temporary", color: "warning" },
      { id: "free", text: "Freelance", color: "info" },
      { id: "cont", text: "Contract", color: "secondary" },
      { id: "pdin", text: "Paid Internship", color: "dark" },
      { id: "unin", text: "Unpaid Internship", color: "muted" },
      { id: "voln", text: "Volunteer", color: "success" }
  ];

  for (const result of Object.values(data)) {
      if (result != null) {
          result.company = transformProperties(result.company);
          result.locn = transformProperties(result.locn);
          result.skill = transformProperties(result.skill);
          result.rolechat = transformProperties(result.rolechat);

          if (typeof result.roletype !== "undefined") {
              result.roletype = result.roletype.map(value => search(value, role));
          }

          obj_result.list.push(result);
      }
  }

  const returnData = {
    success: true,
    output: obj_result
  };

  return returnData;
}

function generateSkillHTML(skills) {
  const skillLinks = skills.map(skill => {
      return `<span class="badge text-dark fs-14 cursor-pointer skill_directory" data-skillid="${skill.id}" data-skillslug="${skill.slug}">${skill.name}</span>`;
  }).join(', ');

  return `<span class="badge fs-14">Skill:</span> <span class="">${skillLinks}</span>`;
}

function generateRoleHTML(roles,$raw=0) {
  const roleLinks = roles.map(role => {
      return `<span class="badge text-success fs-14 cursor-pointer role_directory" data-roleid="${role.id}" data-roleslug="${role.slug}">${role.name}</span>`;
  }).join(', ');

  if($raw)
    return `<span class="">${roleLinks}</span>`;
  else
  return `<span class="badge fs-14">Role:</span> <span class="">${roleLinks}</span>`;
}

function ucfirst(string) {
	if (!string) return string; // Check if the string is empty or null
	return string.charAt(0).toUpperCase() + string.slice(1);
}

function generateCompanyHTML(companies,$raw=1) {
  const companyLinks = companies.map(company => {
      return `<span class="company_directory cursor-pointer underline-on-hover" data-companyid="${company.id}" data-companyslug="${company.slug}">${ucfirst(company.name)}</span>`;
  }).join(', ');

  if($raw)
    return `<span class="">${companyLinks}</span>`;
  else
  return `<span class="badge fs-14">Company:</span> <span class="">${companyLinks}</span>`;
}

function generateLocationHTML(locations) {
    return `<span class="badge fs-14">Location:</span><span class=""><a target=_BLANK class=\"badge text-muted fs-14\">${locations}</a></span>`;
}

function newgenerateSkillHTML(skills) {
  // const skillLinks = skills.map(skill => {
  //   return `<li><a target="_BLANK" href="${_taoh_site_url_root}/asks/chat/Skill/${skill.title}/${skill.id}">
  //               <span title="Join the role chat for ${skill.title}">${skill.title}</span>
  //           </a></li>`;
  //   }).join('');

    const skillLinks = skills.map(skill => `<li><span class="cursor-pointer skill_directory" data-skillid="${skill.id}" data-skillslug="${skill.slug}">${skill.title}</span></li>`).join(' ');

    return `${skillLinks}`;
}

function newgenerateSkillHTMLForAsk(skills) {
  // const skillLinks = skills.map(skill => {
  //   return `<li><a target="_BLANK" href="${_taoh_site_url_root}/asks/chat/Skill/${skill.slug}/${skill.id}">
  //               <span title="Join the role chat for ${skill.name}">${skill.name}</span>
  //           </a></li>`;
  //   }).join('');

    const skillLinks = skills.map(skill => `<li><span class="cursor-pointer skill_directory" data-skillid="${skill.id}" data-skillslug="${skill.slug}">${skill.name}</span></li>`).join(' ');

    return `${skillLinks}`;
}

function newavatardisplay(avatar,img,path){
  var avatar_img = '';
  if(img !='' && img!= undefined ){
    avatar_img = ` <img width="25" height="25" style="border-radius: 20px;" src="${img}" alt="Profile Image">`;
  }
  else if(avatar!='' && avatar!= undefined){
    avatar_img = ` <img width="25" height="25" src="${path}/avatar/PNG/128/${avatar}.png" alt="" />`;
  }
  else
  avatar_img = ` <img width="25" height="25" src="${path}/avatar/PNG/128/avatar_def.png" alt="" />`;

  return avatar_img;
}

function newgenerateCompanyHTML(companies,cmp_name=false) {
  if(cmp_name == true){
    var companyLinks = companies.map(company => {
      return `<span class="company_directory cursor-pointer underline-on-hover" data-companyid="${company.id}" data-companyslug="${company.slug}">${company.title}</span>`;
    }).join('');
  }else{
    var companyLinks = companies.map(company => {
      return `<span class="company_directory cursor-pointer underline-on-hover" data-companyid="${company.id}" data-companyslug="${company.slug}">${company.name}</span>`;
    }).join('');
  }

  return `${companyLinks}`;
}

function newgenerateLocationHTML(locations) {
  return `${locations}`;
}

//Date 20221009125645 to date conversion
function date_zone(date, separator, timezone) {
  d = date.split("");
  date = d[0]+d[1]+d[2]+d[3]+"-"+d[4]+d[5]+"-"+d[6]+d[7]+"T"+d[6]+d[7]+":"+d[8]+d[9];
  date = new Date(date);
  let day = date.getDate().toLocaleString('en-US', { timezone });
  let month = date.getMonth() + 1;
  let year = date.getFullYear();
  let hour = date.getHours();
  let min = date.getMinutes();
  if (day < 10) { day = '0' + day; }
  if (month < 10) { month = '0' + month; }
  if (hour < 10) { hour = '0' + hour; }
  if (min < 10) { min = '0' + min; }
  return day + separator + month + separator + year +" "+ hour+":"+min;
}


//loading icon function
function loader(status, area, width = 75) {
  if(status === true) {
    $(area).empty().append('<img id="loaderEmail" width="'+width+'" src="https://cdn.tao.ai/assets/wertual/images/taoh_loader.gif">');
  } else {
    $(area).empty();
  }
}

//title to slug converter
function convertToSlug(Text) {
  return Text.toLowerCase()
             .replace(/[^\w ]+/g, '')
             .replace(/ +/g, '-');
}

// Get youtube id
function getYoutubeId(url) {
  let id = url.split('v=')[1];
  let ampersandPosition = id.indexOf('&');
  if(ampersandPosition != -1) {
    id = id.substring(0, ampersandPosition);
  }
  return id;
}

function htmlDecode(input){
  var e = document.createElement('textarea');
  e.innerHTML = input;
  // handle case of empty input
  return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
}

/************************ Helper Methods ***********************************/

function taohLoader(elem, state){
  if (state == true || state === "show") {
    elem.classList.add("show");
  } else {
    elem.classList.remove("show");
  }
}

function taoh_Loader(elem, state){
  if (state == true || state === "show") {
    elem.addClass('show');
  } else {
    elem.removeClass('show');
  }
}

function safeParseInt(value, fallback = 0) {
  const num = parseInt(value, 10);
  return Number.isFinite(num) ? num : fallback;
}

/* Html Title Tag Blink */
function startTitleBlinking(newTitle, interval = 1000) {
  if (isTitleBlinking) return;

  isTitleBlinking = true;
  let showingOriginalTitle = true;

  blinkInterval = setInterval(() => {
    document.title = showingOriginalTitle ? newTitle + ' ' + originalTitle : originalTitle;
    showingOriginalTitle = !showingOriginalTitle;
  }, interval);
}

function stopTitleBlinking() {
  clearInterval(blinkInterval);
  document.title = originalTitle;
  isTitleBlinking = false;
}
/* /Html Title Tag Blink */

function getTimeDifferenceInSeconds(d1, d2) {
  const diff = Math.abs(d1 - d2);
  return Math.floor((diff / 1000));
}

function formatBadgeDateTime(timestamp, timezone = 'America/New_York') {
  const weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
  const month_txt = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

  const d = new Date(timestamp);
  if (isNaN(d.getTime())) return false;

  const now = new Date();
  const diffInSeconds = Math.round((now - d) / 1000);
  const diffInDays = Math.round(diffInSeconds / 86400); // (60 * 60 * 24)

  let full_date;
  if (diffInDays === 0) {
    full_date = 'Today';
  } else if (diffInDays === 1) {
    full_date = 'Yesterday';
  } else if (diffInDays <= 7) {
    full_date = weekday[d.getDay()];
  } else {
    // full_date = `${d.getDate()} ${month_txt[d.getMonth()]} ${d.getFullYear()}`;
    full_date = formatTimestamp(d,'dd LLLL yyyy', 'UTC', timezone);
  }

  // const hours = d.getHours().toString().padStart(2, '0');
  // const minutes = d.getMinutes().toString().padStart(2, '0');
  // const hours_minutes = `${hours}:${minutes}`;
  const hours_minutes = formatTimestamp(d,'h:mm A', 'UTC', timezone);

  return [full_date, hours_minutes];
}


 function formatTimestamp(timestamp, format = 'yyyy-MM-dd h:mm A',  fromTimezone = 'UTC', toTimezone = 'America/New_York') {
    const { DateTime } = luxon;

    let dateTimeInFromTimezone;

    if (typeof timestamp === 'string') {
      // Parse string in format YYYYMMDDHHmmss
      dateTimeInFromTimezone = DateTime.fromFormat(timestamp,  'yyyyMMddHHmmss', { zone: fromTimezone });
    } else {
      // Assume it's a Date object
      dateTimeInFromTimezone = DateTime.fromMillis(timestamp.getTime(), {  zone: fromTimezone });
    }

    const dateTimeInToTimezone = dateTimeInFromTimezone.setZone(toTimezone);
    return dateTimeInToTimezone.toFormat(format);
  }


function formatTimestamp_RK_changed_to_above(timestamp, format = 'yyyy-MM-dd h:mm A', fromTimezone = 'UTC', toTimezone = 'America/New_York') {
  //alert(timestamp.getTime())
  const { DateTime } = luxon;
  const dateTimeInFromTimezone = DateTime.fromMillis(timestamp.getTime(), { zone: fromTimezone });
  const dateTimeInToTimezone = dateTimeInFromTimezone.setZone(toTimezone);

  return dateTimeInToTimezone.toFormat(format);

  // const date = new Date(timestamp);
  // const year = date.getFullYear();
  // // getMonth() returns a zero-based index, so add 1 to get the correct month
  // const month = (date.getMonth() + 1).toString().padStart(2, '0');
  // const day = date.getDate().toString().padStart(2, '0');
  // const hours = date.getHours().toString().padStart(2, '0');
  // const minutes = date.getMinutes().toString().padStart(2, '0');
  // const seconds = date.getSeconds().toString().padStart(2, '0');
  //
  // // Construct the format "YYYY-MM-DD HH:mm:ss"
  // return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

function timeAgo(dateOrTimestampString, format = true) {
  const past = new Date(dateOrTimestampString);
  const now = new Date();
  const diffInSeconds = Math.round((now - past) / 1000);
  const diffInMinutes = Math.round(diffInSeconds / 60);
  const diffInHours = Math.round(diffInMinutes / 60);
  const diffInDays = Math.round(diffInHours / 24);
  const diffInWeeks = Math.round(diffInDays / 7);
  const diffInMonths = Math.round(diffInDays / 30);
  const diffInYears = Math.round(diffInDays / 365);

  if (isNaN(past.getTime())) {
    return "Invalid date"; // or throw an error, depending on your preference
  }

  if (format === true) {
    if (diffInSeconds < 60) {
      return 'Just now';
    } else if (diffInMinutes < 60) {
      return `${diffInMinutes} min${diffInMinutes > 1 ? 's' : ''} ago`;
    } else if (diffInHours < 24) {
      return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
    } else if (diffInDays < 7) {
      return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`;
    } else if (diffInWeeks < 4) {
      return `${diffInWeeks} week${diffInWeeks > 1 ? 's' : ''} ago`;
    } else if (diffInMonths < 12) {
      return `${diffInMonths} month${diffInMonths > 1 ? 's' : ''} ago`;
    } else {
      return `${diffInYears} year${diffInYears > 1 ? 's' : ''} ago`;
    }
  } else {
    if (typeof format === 'string' && format) {
      return formatTimestamp(past, format);
    } else {
      return formatTimestamp(past);
    }
  }
}

function throttle(func, limit) {
  let inThrottle = false;

  return function() {
    if (!inThrottle) {
      func.apply(this, arguments);
      inThrottle = true;

      setTimeout(function() {
        inThrottle = false;
      }, limit);
    }
  };
}

function sortObjectByKey(obj, reverse = false) {
  const keys = Object.keys(obj).sort();
  if(reverse) keys.reverse();

  const sortedObj = {};
  keys.forEach(key => sortedObj[key] = obj[key]);

  return sortedObj;
}

function groupByArrayVal(grpArr, grpKey, ksort = false) {
  const outputArr = {};
  for (const [key, item] of Object.entries(grpArr)) {
    const groupKey = item[grpKey];
    if (!outputArr[groupKey]) outputArr[groupKey] = {};
    outputArr[groupKey][key] = item;
  }
  if (ksort) {
    const sortedKeys = Object.keys(outputArr).sort();
    const sortedOutputArr = {};
    sortedKeys.forEach(key => {
      sortedOutputArr[key] = outputArr[key];
    });
    return sortedOutputArr;
  }
  return outputArr;
}

function checkImageExists(url, cache = true) {
  if (cache && imageExistMap.has(url)) {
    return Promise.resolve(imageExistMap.get(url));
  }

  return new Promise((resolve) => {
    const img = new Image();
    img.onload = () => {
      if(cache) imageExistMap.set(url, true);
      resolve(true);
    };
    img.onerror = () => {
      if(cache) imageExistMap.set(url, false);
      resolve(false);
    };
    img.src = url;
  });
}

const buildAvatarImage = async (avatar_image, fallback) => {
  if (avatar_image) {
    const exists = await checkImageExists(avatar_image, true);
    return exists ? avatar_image : fallback;
  }

  return fallback;
};

const buildAvatarImageOptimistic = (avatar_image, fallback, onUpdate) => {
  if (avatar_image) {
    const cached = imageExistMap.get(avatar_image);

    if (cached === true) {
      return avatar_image;
    } else if (cached === false) {
      return fallback;
    } else {
      // Optimistically return avatar_image, but re-check in background
      checkImageExists(avatar_image, true).then((exists) => {
        if (typeof onUpdate === 'function') onUpdate(exists ? avatar_image : fallback);
      });
      return avatar_image;
    }
  }

  return fallback;
};

function truncateMessage(message, maxLength = 95, suffix = '...') {
  if (message.length > maxLength) {
    return message.slice(0, maxLength - 3) + suffix;
  }
  return message;
}

function isEmpty(value) {
  if (value === null || value === undefined) {
    return true;
  }
  if (typeof value === 'string' && value.trim() === '') {
    return true;
  }
  if (Array.isArray(value) && value.length === 0) {
    return true;
  }
  if (typeof value === 'object' && !Array.isArray(value) && Object.keys(value).length === 0) {
    return true;
  }

  return false;
}

function isEmptyObject(obj) {
  return Object.prototype.toString.call(obj) === '[object Object]' && Object.keys(obj).length === 0;
}

function isScrolledUp(container, scrollUpThreshold = 100) {
  // Check if the user is not at the bottom of the container
  return (container.scrollTop + container.clientHeight) < container.scrollHeight - scrollUpThreshold;
}

function getEmbedSrc(url, opts = {}) {
    try {
        const u = new URL(url), p = u.pathname.replace(/\/+$/, '');
        const q = (k) => u.searchParams.get(k);
        // YouTube
        if (/youtube\.com$|youtu\.be$/.test(u.hostname)) {
            if (p.startsWith("/embed/")) return url;
            if (p.startsWith("/shorts/")) return `https://www.youtube.com/embed/${p.split('/')[2]}`;
            if (u.hostname.includes("youtu.be")) return `https://www.youtube.com/embed/${p.slice(1)}`;
            if (p === "/watch" && q("v")) return `https://www.youtube.com/embed/${q("v")}`;
            if ((p === "/playlist" || p === "/watch") && q("list")) return `https://www.youtube.com/embed/videoseries?list=${q("list")}`;
        }
        // Vimeo
        if (/vimeo\.com$/.test(u.hostname)) {
            const m = p.match(/^\/(?:video\/)?(\d+)/);
            if (m) return `https://player.vimeo.com/video/${m[1]}`;
        }
        // Twitch (needs parent)
        if (/twitch\.tv$/.test(u.hostname)) {
            const parent = opts.parent || (typeof location !== "undefined" ? location.hostname : "");
            if (p.startsWith("/videos/")) return `https://player.twitch.tv/?video=${p.split('/')[2]}&parent=${parent}`;
            if (p.startsWith("/clip/")) return `https://clips.twitch.tv/embed?clip=${p.split('/')[2]}&parent=${parent}`;
            if (p.split('/')[1]) return `https://player.twitch.tv/?channel=${p.split('/')[1]}&parent=${parent}`;
        }
        // Dailymotion
        if (/dailymotion\.com$|dai\.ly$/.test(u.hostname)) {
            const id = u.hostname.includes('dai.ly') ? p.slice(1) : (p.match(/^\/video\/([^_/]+)/) || [])[1];
            if (id) return `https://www.dailymotion.com/embed/video/${id}`;
        }
        // Loom
        if (/loom\.com$/.test(u.hostname)) {
            const m = p.match(/\/share\/([a-z0-9]+)/i);
            if (m) return `https://www.loom.com/embed/${m[1]}`;
        }
        // Wistia
        if (/wistia\.(com|net)$/.test(u.hostname)) {
            const m = url.match(/(?:medias|embed)\/([a-z0-9]+)/i);
            if (m) return `https://fast.wistia.com/embed/medias/${m[1]}`;
        }
    } catch {
    }
    return null;
}

async function generatePoster(options) {
  let canvas = options.canvasId ? document.getElementById(options.canvasId) : document.createElement('canvas');
  let ctx = canvas.getContext('2d');

  canvas.width = options.width || canvas.width || 300;
  canvas.height = options.height || canvas.height || 150;

  // Background Gradient
  const gradient = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
  gradient.addColorStop(0, options.gradientStart || '#ff7e5f');
  gradient.addColorStop(1, options.gradientEnd || '#feb47b');
  ctx.fillStyle = gradient;
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  // Draw circles
  const drawCircles = () => {
    const colors = options.circleColors || ['#ff6f61', '#4ecdc4', '#3d84a8', '#ffe156', '#ff6b6b'];
    for (let i = 0; i < (options.circleCount || 5); i++) {
      ctx.beginPath();
      ctx.arc(Math.random() * canvas.width, Math.random() * canvas.height, Math.random() * (options.maxRadius || 30) + (options.minRadius || 10), 0, Math.PI * 2);
      ctx.fillStyle = colors[i % colors.length];
      ctx.fill();
    }
  };

  // Draw title with truncation
  const drawTitle = () => {
    const title = options.title || '';
    if (!title.trim()) return;

    const maxWidth = canvas.width * 0.8, fontSize = options.fontSize || 24, fontFamily = options.fontFamily || 'Arial';
    ctx.font = `${fontSize}px ${fontFamily}`;
    ctx.fillStyle = options.fontColor || '#ffffff';
    ctx.textAlign = 'center';

    const words = title.split(' '), lines = [];
    let currentLine = '';

    for (const word of words) {
      const testLine = currentLine + word + ' ';
      if (ctx.measureText(testLine).width > maxWidth && currentLine) {
        lines.push(currentLine);
        currentLine = word + ' ';
      } else {
        currentLine = testLine;
      }
      if (lines.length >= (options.maxLines || 2) - 1) break;
    }

    if (currentLine.trim()) lines.push(currentLine.trim());
    const startY = (canvas.height - (lines.length * (fontSize + 5))) / 2;

    lines.forEach((line, i) => ctx.fillText(line, canvas.width / 2, startY + (i * (fontSize + 5))));
  };

  // Load image from URL
  const getImageFromUrl = (url) => new Promise((resolve, reject) => {
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = () => resolve(img);
    img.onerror = (err) => reject(err);
    img.src = url;
  });

  // Draw logo
  const drawLogo = async () => {
    if (!options.logoUrl) return;
    const logoImg = await getImageFromUrl(options.logoUrl);
    ctx.drawImage(logoImg, options.logoX || 15, options.logoY || 45, options.logoWidth || 50, options.logoHeight || 50);
  };

  drawCircles();
  await drawLogo();
  drawTitle();

  return canvas;
}

function convertDeprecatedTimeZone(ianaTimeZone) {
  // Mapping of deprecated time zones to modern equivalents
  const timeZoneMap = {
    'Asia/Calcutta': 'Asia/Kolkata',
    'America/Argentina/Buenos_Aires': 'America/Argentina/Buenos_Aires',  // Correct, but included for completeness
    'Asia/Saigon': 'Asia/Ho_Chi_Minh',
    'Asia/Chungking': 'Asia/Shanghai',
    'Asia/Ujung_Pandang': 'Asia/Makassar',
    'Europe/Belfast': 'Europe/London',
    'Pacific/Yap': 'Pacific/Port_Moresby',  // Corrected to PNG region
    'Pacific/Samoa': 'Pacific/Pago_Pago',  // Correct Samoa mapping
    'Africa/Asmara': 'Africa/Asmera',
    'Asia/Istanbul': 'Europe/Istanbul',  // Modern mapping for Turkey
    'Pacific/Truk': 'Pacific/Chuuk',
    'America/Porto_Acre': 'America/Rio_Branco',
    'America/St_Barthelemy': 'America/Guadeloupe'
  };

  // Return the mapped time zone if it exists, otherwise return the input as-is
  return timeZoneMap[ianaTimeZone] || ianaTimeZone;
}

function hasDST(date = new Date()) {
  const january = new Date(date.getFullYear(), 0, 1,).getTimezoneOffset();
  const july = new Date(date.getFullYear(), 6, 1,).getTimezoneOffset();

  return Math.max(january, july) !== date.getTimezoneOffset();
}

function isDST(date, timeZone) {
  const { DateTime } = luxon;
  const dateTime = DateTime.fromJSDate(date, {zone: timeZone});
  return dateTime.isInDST;
  // // Get the UTC offsets for January and the given date
  // const janOffset = new Date(Date.UTC(date.getFullYear(), 0, 1)).toLocaleString('en-US', { timeZone });
  // const currentOffset = date.toLocaleString('en-US', { timeZone, timeZoneName: 'short' });
  //
  // // DST is active if the current date's offset is different from January's
  // return janOffset !== currentOffset;
}

function isValidNumber_isNaN(str) {
  return !isNaN(str) && str.trim() !== '';
}

function isValidNumber_regExp(str) {
  const numberRegex = /^-?\d+(\.\d+)?$/;
  return numberRegex.test(str.trim());
}

function isValidNumber_isFinite(str) {
  const num = Number(str);
  return Number.isFinite(num);
}

/************************ /Helper Methods ***********************************/

function parseYmdHis(s) {
    return {
        y: +s.slice(0, 4), m: +s.slice(4, 6), d: +s.slice(6, 8),
        H: +s.slice(8, 10), M: +s.slice(10, 12), S: +s.slice(12, 14),
    };
}

// Interpret YmdHis as *local wall time in `timeZone`* (handles DST correctly)
function makeZonedInstant(ymdHis, timeZone) {
    const {y, m, d, H, M, S} = parseYmdHis(ymdHis);
    const fmt = new Intl.DateTimeFormat('en-US', {
        timeZone, year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23',
    });
    const parts = d0 => Object.fromEntries(fmt.formatToParts(d0).map(p => [p.type, p.value]));
    let guess = new Date(Date.UTC(y, m - 1, d, H, M, S));
    // A couple of iterations resolve DST gaps/repeats
    for (let i = 0; i < 2; i++) {
        const a = parts(guess);
        const want = Date.UTC(y, m - 1, d, H, M, S);
        const got = Date.UTC(+a.year, +a.month - 1, +a.day, +a.hour, +a.minute, +a.second);
        guess = new Date(guess.getTime() + (want - got));
    }
    return guess;
}

/**
 * Get timezone abbreviation (DST aware)
 */
function getTzAbbreviation(date, timeZone) {
    const timeZoneMap = {
        // Americas
        'America/Havana': {standard: 'CST', daylight: 'CDT'},  // Cuba Standard/Daylight Time
        'America/Los_Angeles': {standard: 'PST', daylight: 'PDT'},  // Pacific Standard/Daylight Time
        'America/Denver': {standard: 'MST', daylight: 'MDT'},  // Mountain Standard/Daylight Time
        'America/Chicago': {standard: 'CST', daylight: 'CDT'},  // Central Standard/Daylight Time
        'America/New_York': {standard: 'EST', daylight: 'EDT'},  // Eastern Standard/Daylight Time
        'America/Toronto': {standard: 'EST', daylight: 'EDT'},  // Eastern Canada Standard/Daylight Time
        'America/Phoenix': {standard: 'MST'},  // (no DST)
        'America/Sao_Paulo': {standard: 'BRT'},  // Brazil Time (no DST)
        'America/Bogota': {standard: 'COT'},  // Colombia Time (no DST)
        'America/Caracas': {standard: 'VET'},  // Venezuela Time (no DST)
        'America/Lima': {standard: 'PET'},  // Peru Time (no DST)
        'America/Vancouver': {standard: 'PST'},  // (no DST)
        'America/Argentina/Buenos_Aires': {standard: 'ART'},  // (no DST)
        'America/Santiago': {standard: 'CLT'},  // (no DST)

        // Europe
        'Europe/London': {standard: 'GMT', daylight: 'BST'},  // Greenwich Mean Time / British Summer Time
        'Europe/Paris': {standard: 'CET', daylight: 'CEST'},  // Central European Time / Central European Summer Time
        'Europe/Berlin': {standard: 'CET', daylight: 'CEST'},
        'Europe/Moscow': {standard: 'MSK'},  // Moscow Standard Time (no DST)
        'Europe/Istanbul': {standard: 'TRT'},  // Turkey Time (no DST)
        'Europe/Madrid': {standard: 'CET'},  // (no DST)
        'Europe/Rome': {standard: 'CET'},  // (no DST)
        'Europe/Amsterdam': {standard: 'CET'},  // (no DST)
        'Europe/Oslo': {standard: 'CET'},  // (no DST)
        'Europe/Stockholm': {standard: 'CET'},  // (no DST)

        // Africa
        'Africa/Cairo': {standard: 'EET', daylight: 'EEST'},  // Eastern European Time / Summer Time
        'Africa/Johannesburg': {standard: 'SAST'},  // South Africa Standard Time (no DST)
        'Africa/Nairobi': {standard: 'EAT'},  // East Africa Time (no DST)

        // Asia
        'Asia/Dubai': {standard: 'GST'},  // Gulf Standard Time (no DST)
        'Asia/Karachi': {standard: 'PKT'},  // Pakistan Standard Time (no DST)
        'Asia/Kolkata': {standard: 'IST'},  // India Standard Time (no DST)
        'Asia/Calcutta': {standard: 'IST'},  // India Standard Time (no DST)
        'Asia/Dhaka': {standard: 'BST'},  // Bangladesh Standard Time (no DST)
        'Asia/Bangkok': {standard: 'ICT'},  // Indochina Time (no DST)
        'Asia/Singapore': {standard: 'SGT'},  // Singapore Time (no DST)
        'Asia/Shanghai': {standard: 'CST'},  // China Standard Time (no DST)
        'Asia/Tokyo': {standard: 'JST'},  // Japan Standard Time (no DST)
        'Asia/Seoul': {standard: 'KST'},  // Korea Standard Time (no DST)
        'Asia/Hong_Kong': {standard: 'HKT'},  // (no DST)
        'Asia/Kuala_Lumpur': {standard: 'MYT'},  // (no DST)
        'Asia/Jakarta': {standard: 'WIB'},  // (no DST)

        // Australia/Oceania
        'Australia/Perth': {standard: 'AWST'},  // Australian Western Standard Time (no DST)
        'Australia/Adelaide': {standard: 'ACST', daylight: 'ACDT'},  // Australian Central Standard/Daylight Time
        'Australia/Sydney': {standard: 'AEST', daylight: 'AEDT'},  // Australian Eastern Standard/Daylight Time
        'Pacific/Auckland': {standard: 'NZST', daylight: 'NZDT'},  // New Zealand Standard/Daylight Time
        'Pacific/Fiji': {standard: 'FJT'},  // Fiji Time (no DST)
        'Australia/Melbourne': {standard: 'AEST'},  // (no DST)
        'Australia/Brisbane': {standard: 'AEST'},  // (no DST)

        // Pacific Islands
        'Pacific/Guam': {standard: 'ChST'},  // Chamorro Standard Time (no DST)
        'Pacific/Honolulu': {standard: 'HST'},  // Hawaii Standard Time (no DST)
        'Pacific/Midway': {standard: 'SST'},  // Samoa Standard Time (no DST)

        // Antarctica
        'Antarctica/South_Pole': {standard: 'NZST', daylight: 'NZDT'},  // New Zealand Time in South Pole (with DST)

        // Atlantic
        'Atlantic/Azores': {standard: 'AZOT', daylight: 'AZOST'},  // Azores Time / Summer Time
        'Atlantic/Cape_Verde': {standard: 'CVT'},  // Cape Verde Time (no DST)
        'Atlantic/Bermuda': {standard: 'AST', daylight: 'ADT'},  // Atlantic Standard/Daylight Time

        // Middle East
        'Asia/Riyadh': {standard: 'AST'},  // (no DST)
        'Asia/Tehran': {standard: 'IRST'},  // (no DST)
        'Asia/Jerusalem': {standard: 'IST'},  // Israel Standard Time (no DST)

        // UTC Zones
        'Etc/UTC': {standard: 'UTC'},  // (no DST)
        'UTC': {standard: 'UTC'},  // (no DST)
        'GMT': {standard: 'GMT'},  // (no DST)
    };

    const entry = timeZoneMap[timeZone];
    const tzPart = new Intl.DateTimeFormat('en-US', {timeZone, timeZoneName: 'short'})
        .formatToParts(date)
        .find(p => p.type === 'timeZoneName')?.value || '';

    if (!entry) {
        // Fallback: use Intl's output (e.g., "GMT+5:30" or an abbr if provided)
        return tzPart.replace(/^GMT([+-]\d{1,2}:\d{2})$/, 'GMT$1');
    }

    if (entry.daylight && tzPart === entry.daylight) return entry.daylight;
    return entry.standard;
}

/**
 * Same-day detector (local to the zone)
 */
function isSameLocalDay(startYmdHis, endYmdHis, timeZone = 'UTC') {
    const f = makeZonedInstant(startYmdHis, timeZone);
    const t = makeZonedInstant(endYmdHis, timeZone);
    const df = new Intl.DateTimeFormat('en-US', {year: 'numeric', month: '2-digit', day: '2-digit', timeZone});
    return df.format(f) === df.format(t);
}

function get_localized_event_data(event_timestamp_data, user_timezone = 'UTC') {
    const { DateTime } = luxon;

    let event_locality = parseInt(event_timestamp_data.locality) || 0;
    const event_timezone = event_timestamp_data.timezone || 'UTC';

    let return_datetime;
    //alert(user_timezone)
    if (event_locality === 1) {
        // Global event
        // return {
        //     datetime: event_timestamp_data.utc_datetime,
        //     timezone: user_timezone
        // };
        return_datetime = DateTime.fromFormat(event_timestamp_data.utc_datetime, "yyyyMMddHHmmss", {zone: user_timezone});
        return {
            datetime: return_datetime.toFormat("yyyyMMddHHmmss"),
            timezone: user_timezone
        };
    } else {
        // Local event
        // const event_timezone = event_timestamp_data.timezone || 'UTC';
        // return {
        //     datetime: event_timestamp_data.local_datetime,
        //     timezone: event_timezone
        // };
        return_datetime = DateTime.fromFormat(event_timestamp_data.local_datetime, "yyyyMMddHHmmss", {zone: event_timezone});
        const localized = return_datetime.setZone(user_timezone);
        return {
            datetime: localized.toFormat("yyyyMMddHHmmss"),
            timezone: user_timezone
        };
    }

    
}

/**
 * Beautify Time
 * fmtStr tokens: {week}, {year}, {month}, {day}, {time}, {abbr}
 * options: { weekdayStyle: 'short'|'long', monthStyle: 'short'|'long' }
 *
 * */
function beautifyTime(
    ymdHis,
    timeZone = 'UTC',
    fmtStr = '{week}, {day} {month} {year} - {time} {abbr}',
    options = { weekdayStyle: 'short', monthStyle: 'short' }
) {
    const opts = options || {};
    const dt = makeZonedInstant(ymdHis, timeZone);

    const optsDate = {
        weekday: opts.weekdayStyle || 'short',
        year: 'numeric',
        month: opts.monthStyle || 'short',
        day: 'numeric',
        timeZone
    };
    const optsTime = {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
        timeZone,
        timeZoneName: 'short'
    };

    const partsDate = new Intl.DateTimeFormat('en-US', optsDate).formatToParts(dt);
    const partsTime = new Intl.DateTimeFormat('en-US', optsTime).formatToParts(dt);

    // Pull date fields
    const mapDate = Object.fromEntries(partsDate.map(p => [p.type, p.value]));
    const week = mapDate.weekday;
    const year = mapDate.year;
    const month = mapDate.month;
    const day = mapDate.day;

    // Separate time vs zone explicitly
    const tzPart = partsTime.find(p => p.type === 'timeZoneName')?.value || '';
    const timeNoTz = partsTime.filter(p => p.type !== 'timeZoneName').map(p => p.value).join('').trim();

    // Prefer your custom abbreviation; otherwise use the tz part; last resort: regex
    let abbr =
        getTzAbbreviation(dt, timeZone) ||
        tzPart ||
        (timeNoTz.match(/\b([A-Z]{2,5}|(?:UTC|GMT)[+-]\d{1,2}:\d{2})\b/)?.[1] ?? '');

    const time = partsTime.filter(p => p.type !== 'timeZoneName').map(p => p.value).join('').trim();

    return fmtStr
        .replace('{week}', week)
        .replace('{year}', year)
        .replace('{month}', month)
        .replace('{day}', day)
        .replace('{time}', time)
        .replace('{abbr}', abbr);
}

function formatEventDateTime(event_start_ymd, event_ends_ymd) {
    if (isSameLocalDay(event_start_ymd.datetime, event_ends_ymd.datetime, event_start_ymd.timezone)) {
        const f = beautifyTime(event_start_ymd.datetime, event_start_ymd.timezone, '{week}, {month} {day}, {year} - {time}');
        const t = beautifyTime(event_ends_ymd.datetime, event_ends_ymd.timezone, '{time} {abbr}');
        return `${f} - ${t}`;
    } else {
        const f = beautifyTime(event_start_ymd.datetime, event_start_ymd.timezone, '{week}, {month} {day}, {year}, {time} {abbr}');
        const t = beautifyTime(event_ends_ymd.datetime, event_ends_ymd.timezone, '{week}, {month} {day}, {year}, {time} {abbr}');
        return `${f} - ${t}`;
    }
}

function format_event_timestamp(event_timestamp_data, user_timezone = 'UTC', input = 'date', format = 'EEE, MMM dd, yyyy hh:mm A', need_tz = 1, debug = 0) {
    const { DateTime } = luxon;

    const event_timezone = isValidTimezone(event_timestamp_data.timezone) ? event_timestamp_data.timezone : 'UTC';
    const event_locality = parseInt(event_timestamp_data.locality) || 0;
    const defaultDatetime = '19700101000000'; // Default date format "yyyyMMddHHmmss"

    // Fallback to default if invalid or missing datetime
    const parseDate = (datetime) => DateTime.fromFormat(datetime, "yyyyMMddHHmmss").isValid ? datetime : defaultDatetime;

    let eventDatetime = event_locality === 1
        ? DateTime.fromFormat(parseDate(event_timestamp_data.utc_datetime), "yyyyMMddHHmmss", { zone: event_timezone })
        : DateTime.fromFormat(parseDate(event_timestamp_data.local_datetime), "yyyyMMddHHmmss", { zone: event_timezone });

    if (!eventDatetime.isValid) {
        console.warn('Invalid datetime provided', event_timestamp_data);
        eventDatetime = DateTime.fromFormat(defaultDatetime, "yyyyMMddHHmmss", { zone: event_timezone });
    }

    // Only set the user's timezone if it's a local event
    if (event_locality !== 1) {
        eventDatetime = eventDatetime.setZone(user_timezone);
    }

    const formattedDatetime = eventDatetime.toFormat(format);
    const timezoneAbbreviation = need_tz ? ` ${getTzAbbreviation(eventDatetime.toJSDate(), eventDatetime.zoneName)}` : '';

    return formattedDatetime + timezoneAbbreviation;
}

function eventLiveState(startTime, endTime, locality = 0, user_timezone = 'UTC', onlyLiveState = 1) {
    const {DateTime} = luxon;

    let eventStartDatetime, eventEndDatetime, returnState;
    locality = parseInt(locality || '0');

    // Helper function to parse datetime safely
    function parseDateTime(time, zone) {
        const dt = DateTime.fromFormat(time, "yyyyMMddHHmmss", {zone});
        return dt.isValid ? dt.toMillis() : null;
    }

    if (locality == 1) {
        // Global event
        eventStartDatetime = parseDateTime(startTime, user_timezone);
        eventEndDatetime = parseDateTime(endTime, user_timezone);
    } else {
        // Local event (UTC conversion)
        eventStartDatetime = parseDateTime(startTime, "UTC");
        eventEndDatetime = parseDateTime(endTime, "UTC");

        if (eventStartDatetime) {
            eventStartDatetime = DateTime.fromMillis(eventStartDatetime, {zone: "UTC"})
                .setZone(user_timezone)
                .toMillis();
        }

        if (eventEndDatetime) {
            eventEndDatetime = DateTime.fromMillis(eventEndDatetime, {zone: "UTC"})
                .setZone(user_timezone)
                .toMillis();
        }
    }

    // If date parsing fails, return 'after'
    if (!eventStartDatetime || !eventEndDatetime) {
        return 'after';
    }

    // Get current timestamp in the user's timezone
    const now = DateTime.now().setZone(user_timezone).toMillis();

    if (now < eventStartDatetime) {
        returnState = 'before';
    } else if (now > eventEndDatetime) {
        returnState = 'after';
    } else {
        returnState = 'live';
    }

    if (onlyLiveState) {
        return returnState;
    }

    return {
        state: returnState,
        start: eventStartDatetime,
        end: eventEndDatetime,
        user_timezone: user_timezone
    };
}

function isJoinEnabled(v) {
    const { DateTime } = luxon;

    const tz = v.spk_timezoneSelect || 'UTC';

    // Parse event window as LOCAL times in the event’s timezone, then compare as instants
    const startUtc = DateTime.fromISO(v.spk_datefrom, { zone: tz }).toUTC();
    const endUtc   = DateTime.fromISO(v.spk_dateto,   { zone: tz }).toUTC();
    if (!startUtc.isValid || !endUtc.isValid) return false;

    const nowUtc = DateTime.utc();

    return nowUtc >= startUtc && nowUtc <= endUtc;
}

function isValidTimezone(timezone) {
  try {
    Intl.DateTimeFormat(undefined, {timeZone: timezone});
    return true;
  } catch (e) {
    return false;
  }
}

function chunkArray(array, size) {
  return Array.from({ length: Math.ceil(array.length / size) }, (_, i) => array.slice(i * size, i * size + size));
}

function isValidURL(url) {
  try {
    new URL(url);
    return true;
  } catch (e) {
    return false;
  }
}

function getYouTubeThumbnail(videoSrc) {
  const videoId = videoSrc.split("v=")[1]?.split("&")[0] || videoSrc.split("youtu.be/")[1];
  return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
}

function getVimeoThumbnail(videoSrc, callback) {
  const videoId = videoSrc.split("vimeo.com/")[1];
  fetch(`https://vimeo.com/api/v2/video/${videoId}.json`)
      .then(response => response.json())
      .then(data => callback(data[0].thumbnail_large))
      .catch(() => callback("https://via.placeholder.com/150/FF0000/FFFFFF?text=Vimeo"));
}

function getMediaType(url) {
  if (url.match(/\.(jpeg|jpg|gif|png|webp)$/i)) {
    return 'image';
  } else if (
      url.includes('youtube.com') ||
      url.includes('youtu.be') ||
      url.includes('vimeo.com') ||
      url.match(/\.(mp4|webm|ogg|avi|mov|mkv|flv|wmv)$/i)
  ) {
    return 'video';
  } else {
    return 'unknown';
  }
}

function detectMediaType(url, timeoutMs = 5000) {
    return new Promise((resolve) => {
        let done = false;
        const finish = (type) => {
            if (done) return;
            done = true;
            resolve(type);
        };

        const timer = setTimeout(() => finish("unknown"), timeoutMs);

        // Try image
        const img = new Image();
        img.onload = () => { clearTimeout(timer); finish("image"); };
        img.onerror = () => {
            // Try video (metadata)
            const video = document.createElement("video");
            video.preload = "metadata";
            video.onloadedmetadata = () => { clearTimeout(timer); finish("video"); };
            video.onerror = () => { clearTimeout(timer); finish("unknown"); };
            video.src = url;
        };
        img.src = url;
    });
}

function parseJSONSafely(data) {
  if (typeof data === 'string') {
    try {
      return JSON.parse(data);
    } catch (error) {
      // console.error("Invalid JSON string:", error);
      return null;
    }
  } else if (typeof data === 'object' && data !== null) {
    return data; // Already an object, return as is
  } else {
    // console.error("Unsupported data type:", data);
    return null;
  }
}

function getInitials(input, letterCount = 2) {
  // Remove special characters and extra spaces
  const cleaned = input.replace(/[^a-zA-Z\s]/g, '').trim();
  const words = cleaned.split(/\s+/).filter(w => w.length > 0);

  let result = '';

  if (words.length >= 2) {
    for (let i = 0; i < Math.min(letterCount, words.length); i++) {
      result += words[i][0] || '';
    }
  } else if (words.length === 1) {
    result = words[0].slice(0, letterCount);
  }

  if(result == ''){
    result = input.slice(0, letterCount);
  }

  return result.toUpperCase();
}