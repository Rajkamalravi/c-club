<?php
if (!function_exists('avatar_select')) {
    function avatar_select($current = "")
    {
        $return = "<div id='avatarSelect'></div>
  <style>
	.selected-box {
	    border: none !important;
      cursor: pointer;
	}
	.component-icon {
	    display: none;
	}
	icon-select .icon {
	    background: none !important;
	    width: 40px !important;
	    height: 40px !important;
	    border: none !important;
	    margin: 0 !important;
	}
	div#avatarSelect-box-scroll {
	    width: 425px;
	    padding: 10px;
	    text-align: center;
			z-index: 9;
			height: auto;
	}
	</style>
  <script type='text/javascript' src='" . TAOH_CDN_PREFIX . "/assets/iconselect/iconselect.js'></script>
  <script> avatarSelect('" . $current . "','" . TAOH_AVATAR_URL . "'); </script>";
        return $return;
    }
}

if (!function_exists('field_location')) {
    function field_location($coordinates = "", $location = "", $geohash = "", $js = 0, $required = 0)
    {
        $required = ($required) ? 'required' : '';
        $str = '<select id="locationSelect" placeholder="Location search" autocomplete="off" class="locationSelect" name="coordinates" ' . $required . '>';
        if ($coordinates && $location) {
            $str .= '<option value="' . $coordinates . '">' . $location . '</option>';
        }
        $str .= '</select>';
        $str .= '<input id="coordinateLocation" type="hidden" name="full_location" value="' . $location . '">';
        $str .= '<input id="geohash" type="hidden" name="geohash" value="' . $geohash . '">';
        $str .= '<script>locationSelect();</script>';

        return $str;
    }
}

if (!function_exists('emp_field_location')) {
    function emp_field_location($coordinates = "", $location = "", $geohash = "", $js = "", $required = 0, $index = 0)
    {
        $required = ($required) ? 'required' : '';
        $str = '<select id="emp_locationSelect_' . $index . '" placeholder="Location search" autocomplete="off" class="emp_locationSelect_' . $index . '" name="emp_coordinates_' . $index . '" ' . $required . '>';
        if ($coordinates && $location) {
            $str .= '<option value="' . $coordinates . '">' . $location . '</option>';
        }
        $str .= '</select>';
        $str .= '<input id="emp_coordinateLocation' . $index . '" type="hidden" name="emp_full_location_' . $index . '" value="' . $location . '">';
        $str .= '<input id="emp_geohash' . $index . '" type="hidden" name="emp_geohash_' . $index . '" value="' . $geohash . '">';
        if ($js) {
            $str .= '<script>emp_locationSelect(' . $index . ');</script>';
        } else {
            $str .= '<script>emp_locationSelect(' . $index . ');</script>';
        }

        return $str;
    }
}

if (!function_exists('field_search')) {
    function field_search($callback = "")
    {
        return '<input onchange="' . $callback . '(this)" type="text" id="searchQuery" class="form-control" placeholder="Search"/>';
    }
}

if (!function_exists('field_time_zone')) {
    function field_time_zone($value = "", $required = 0, $disabled = 0)
    {
        $disabled = $disabled == 1 ? 'disabled' : '';
        $str = '<input ' . $disabled . ' type="text" value="' . $value . '" id="local_timezoneSelect" name="local_timezone" placeholder="Type to select" ' . ($required ? "required" : "") . ' />';
        $str .= '<script>timeZoneSelect();</script>';
        return $str;
    }
}

if (!function_exists('field_company')) {
    function field_company($options = "", $required = 0)
    {
        $required = ($required) ? 'required' : '';
        $str = '<select id="companySelect" class="companySelect" name="company:company[]" placeholder="Type to select" ' . $required . '>';
        if (@$options) {
            foreach ($options as $key => $value) {
                list ($pre, $post) = explode(':>', $value);
                $str .= "<option value='$key' selected='selected' data-slug='$pre'>$post</option>";
            }
        }
        $str .= '</select><script>companySelect();</script>';
        return $str;
    }
}

if (!function_exists('emp_field_company')) {
    function emp_field_company($options = "", $required = 0, $index = 0)
    {
        $required = ($required) ? 'required' : '';
        $str = '<select id="emp_companySelect_' . $index . '" name="emp_company_' . $index . '[]" placeholder="Type to select" ' . $required . '>';
        if (@$options) {
            foreach ($options as $key => $value) {
                list ($pre, $post) = explode(':>', $value);
                $str .= "<option value='$key' selected='selected'>$post</option>";
            }
        }
        $str .= '</select><script>emp_companySelect(' . $index . ');</script>';
        return $str;
    }
}

if (!function_exists('edu_field_company')) {
    function edu_field_company($options = "", $required = 0, $index = 0)
    {
        $required = ($required) ? 'required' : '';
        $str = '<select id="edu_companySelect_' . $index . '" name="edu_name_' . $index . '[]" placeholder="Type to select" ' . $required . '>';
        if (@$options) {
            foreach ($options as $key => $value) {
                list ($pre, $post) = explode(':>', $value);
                $str .= "<option value='$key' selected='selected'>$post</option>";
            }
        }
        $str .= '</select><script>edu_companySelect(' . $index . ');</script>';
        return $str;
    }
}

if (!function_exists('field_role')) {
    function field_role($options = "", $required = 0)
    {
        $required = ($required) ? 'required' : '';
        $str = '<select id="roleSelect" class="roleSelect"  name="title:title[]" placeholder="Type to select" ' . $required . '>';
        if (@$options) {
            foreach ($options as $key => $value) {
                list ($pre, $post) = explode(':>', $value);
                $str .= "<option value='$key' selected='selected' data-slug='$pre'>$post</option>";
            }
        }
        $str .= '</select><script>roleSelect();</script>';
        return $str;
    }
}

if (!function_exists('emp_field_role')) {
    function emp_field_role($options = "", $required = 0, $index = 0)
    {
        $required = ($required) ? 'required' : '';
        $str = '<select id="emp_roleSelect_' . $index . '"  name="emp_title_' . $index . '[]" placeholder="Type to select" ' . $required . '>';
        if (@$options) {
            foreach ($options as $key => $value) {
                list ($pre, $post) = explode(':>', $value);
                $str .= "<option value='$key' selected='selected'>$post</option>";
            }
        }
        $str .= '</select><script>emp_roleSelect(' . $index . ');</script>';
        return $str;
    }
}

if (!function_exists('field_skill')) {
    function field_skill($options = "", $required = 0)
    {
        $required = ($required) ? 'required' : '';
        $str = '<select id="skillSelect" multiple name="skill:skill[]" placeholder="Type to select" ' . $required . '>';
        if (@$options) {
            foreach ($options as $key => $value) {
                list ($pre, $post) = explode(':>', $value);
                $str .= "<option value='$key' selected='selected' data-slug='$pre'>$post</option>";
            }
        }
        $str .= '</select><script>skillSelect();</script>';
        return $str;
    }
}

if (!function_exists('edu_field_skill')) {
    function edu_field_skill($options = "", $required = 0, $index = 0)
    {
        $required = ($required) ? 'required' : '';
        $str = '<select id="edu_skillSelect_' . $index . '" multiple name="edu_skill_' . $index . '[]" placeholder="Type to select" ' . $required . '>';
        if (@$options) {
            foreach ($options as $key => $value) {
                list ($pre, $post) = explode(':>', $value);
                $str .= "<option value='$key' selected='selected'>$post</option>";
            }
        }
        $str .= '</select><script>edu_skillSelect(' . $index . ');</script>';
        return $str;
    }
}

if (!function_exists('emp_field_skill')) {
    function emp_field_skill($options = "", $required = 0, $index = 0)
    {
        $required = ($required) ? 'required' : '';
        $str = '<select id="emp_skillSelect_' . $index . '" multiple name="emp_skill_' . $index . '[]" placeholder="Type to select" ' . $required . '>';
        if (@$options) {
            foreach ($options as $key => $value) {
                list ($pre, $post) = explode(':>', $value);
                $str .= "<option value='$key' selected='selected'>$post</option>";
            }
        }
        $str .= '</select><script>emp_skillSelect(' . $index . ');</script>';
        return $str;
    }
}

if (!function_exists('field_fname')) {
    function field_fname($option = "")
    {
        return '<div class="form-group">
      <input class="form-control form--control" value="' . $option . '" required type="text" id="fname" name="fname">
  </div>';
    }
}

if (!function_exists('field_lname')) {
    function field_lname($option = "")
    {
        return '<div class="form-group">
      <input class="form-control form--control" value="' . $option . '" required type="text" id="lname" name="lname">
  </div>';
    }
}

if (!function_exists('field_email')) {
    function field_email($option = "", $readonly = "")
    {
        return '<div class="form-group">
      <input class="form-control form--control" value="' . $option . '" required ' . $readonly . ' type="text" name="email">
  </div>';
    }
}

if (!function_exists('field_race')) {
    function field_race($option = "")
    {
        $race = array(
            "not" => "I prefer not to say",
            "ind" => "American Indian or Alaska Native",
            "asi" => "Asian",
            "bla" => "Black or African American",
            "his" => "Hispanic/Latino",
            "isl" => "Native Hawaiian/Other Pacific Islander",
            "whi" => "White",
        );

        $out = '<div class="form-group">';
        $out .= '<select class="form-control form--control" required name="race">';
        foreach ($race as $key => $value) {
            if ($option == $key) {
                $out .= '<option selected value="' . $key . '">';
            } else {
                $out .= '<option value="' . $key . '">';
            }
            $out .= $value;
            $out .= '</option>';
        }
        $out .= '</select>';
        $out .= '</div>';

        return $out;
    }
}

if (!function_exists('field_role_type')) {
    function field_role_type($options_get = "")
    {
        $roletypes = array(
            "remo" => "Remote Work",
            "full" => "Full Time",
            "part" => "Part Time",
            "temp" => "Temporary",
            "free" => "Freelance",
            "cont" => "Contract",
            "pdin" => "Paid Internship",
            "unin" => "Unpaid Internship",
            "voln" => "Volunteer",
        );
        $options = [];
        if ($options_get != '') {
            $options = $options_get;
        }
        $out = '<div class="form-group">';
        $out .= '<select id="roleTypeSelect" multiple name="roletype[]" autocomplete="off">';
        foreach ($roletypes as $key => $value) {
            $checked = "";
            if (isset($options) && in_array($key, @$options)) {
                $checked = "selected";
            }
            $out .= '<option ' . $checked . ' value="' . $key . '">';

            $out .= $value;
            $out .= '</option>';
        }
        $out .= '</select>';
        $out .= '</div>';
        $out .= '<script>roleTypeSelect()</script>';
        return $out;
    }
}

if (!function_exists('emp_field_role_type')) {
    function emp_field_role_type($options_get = "", $js = 0, $index = 0)
    {
        $roletypes = array(
            "remo" => "Remote Work",
            "full" => "Full Time",
            "part" => "Part Time",
            "temp" => "Temporary",
            "free" => "Freelance",
            "cont" => "Contract",
            "pdin" => "Paid Internship",
            "unin" => "Unpaid Internship",
            "voln" => "Volunteer",
        );
        $options = [];
        if ($options_get != '') {
            $options = $options_get;
        }

        $out = '<div class="form-group">';
        if (!$js) {
            $out .= '<select id="emp_roleTypeSelect_' . $index . '" class="form-control" name="emp_roletype_' . $index . '[]" autocomplete="off">';
        }
        $out .= '<option value="">Choose</option>';
        foreach ($roletypes as $key => $value) {
            if (isset($options) && in_array($key, $options)) {
                $out .= '<option selected value="' . $key . '">' . $value . '</option>';
            } else {
                $out .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        if (!$js) {
            $out .= '</select>';
        }
        $out .= '</div>';
        //$out .='<script>emp_roleTypeSelect('.$index.')</script>';
        return $out;
    }
}

if (!function_exists('field_work_placetype')) {
    function field_work_placetype($options_get = '', $js = 0, $required = 0, $index = 0)
    {
        $roletype_arr = array(
            "ons" => "Onsite",
            "rem" => "Remote",
            "hyb" => "Hybrid",
        );
        $required = ($required) ? 'required' : '';
        $options = explode(" ", $options_get);
        if (!$js) {
            $str = '<select ' . $required . ' class="form-control placeType" name="emp_placeType_' . $index . '">';
        }
        $str .= '<option value="">Choose</option>';
        foreach ($roletype_arr as $key => $value) {
            if (in_array($key, $options)) {
                $str .= '<option selected value="' . $key . '">' . $value . '</option>';
            } else {
                $str .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        if (!$js) {
            $str .= '</select>';
        }
        return $str;
    }
}

if (!function_exists('field_industry')) {
    function field_industry($options_get = '', $js = 0, $required = 0, $index = 0)
    {
        $industry_arr = defined('TAOH_INDUSTRY_CATEGORIES')? TAOH_INDUSTRY_CATEGORIES : [];
        $required = ($required) ? 'required' : '';
        $options = explode(" ", $options_get);
        if (!$js) {
            $str = '<select ' . $required . ' class="form-control emp_industry" name="emp_industry_' . $index . '">';
        }
        $str .= '<option value="">Choose</option>';
        foreach ($industry_arr as $key => $value) {
            if (in_array($key, $options)) {
                $str .= '<option selected value="' . $key . '">' . $value . '</option>';
            } else {
                $str .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        if (!$js) {
            $str .= '</select>';
        }
        return $str;
    }
}

if (!function_exists('field_degeree')) {
    function field_degeree($options_get = '', $js = 0, $required = 0, $index = 0)
    {
        $degeree_arr = array(
            "highschool" => "High School Diploma or GED",
            "vocational" => "Vocational/Technical Diploma",
            "associate" => "Associate Degree",
            "bachelor" => "Bachelor's Degree",
            "master" => "Master's Degree",
            "doctorate" => "Doctorate or Professional Degree",
            "other" => "Other (for degeree not listed above)"
        );
        $required = ($required) ? 'required' : '';

        if (is_array($options_get)) {
            $options = $options_get;
        } else {
            $options = explode(" ", $options_get);
        }
        if (!$js) {
            $str = '<select id="edu_degree_' . $index . '" ' . $required . ' class="form-control edu_degree" name="edu_degree_' . $index . '[]">';
        }
        $str .= '<option value="">Choose an option below</option>';
        foreach ($degeree_arr as $key => $value) {
            if (in_array($key, $options)) {
                $str .= '<option selected value="' . $key . '">' . $value . '</option>';
            } else {
                $str .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        if (!$js) {
            $str .= '</select>';
        }
        return $str;
    }
}

if (!function_exists('field_role_type_hire_job')) {
    function field_role_type_hire_job($method, $options_get = "")
    {
        $roletypes = array(
            "remo" => "Remote Work",
            "full" => "Full Time",
            "part" => "Part Time",
            "temp" => "Temporary",
            "free" => "Freelance",
            "cont" => "Contract",
            "pdin" => "Paid Internship",
            "unin" => "Unpaid Internship",
            "voln" => "Volunteer",
        );
        $options = [];
        if ($options_get != '') {
            $options = $options_get;
        }
        $out = '<div class="form-group">';
        $out .= '<select id="roleTypeSelect_' . $method . '" multiple name="roletype_' . $method . '[]" autocomplete="off">';
        foreach ($roletypes as $key => $value) {
            $checked = "";
            if (isset($options) && in_array($key, @$options)) {
                $checked = "selected";
            }
            $out .= '<option ' . $checked . ' value="' . $key . '">';

            $out .= $value;
            $out .= '</option>';
        }
        $out .= '</select>';
        $out .= '</div>';
        if ($method == 'job') {
            $out .= '<script>roleTypeSelect_job()</script>';
        } else {
            $out .= '<script>roleTypeSelect_hire()</script>';
        }
        return $out;
    }
}

if (!function_exists('field_flags')) {
    function field_flags($options_get = "", $name = "")
    {
        $names = ($name) ? '_hire' : '';
        $flags = array(
            "none" => "None of the above",
            "vet" => "Veteran",
            "govt" => "Have Govt. Security Clearance",
            "visa" => "Have Valid Work Visa",
        );
        $options = [];
        if ($options_get != '') {
            $options = $options_get;
        }
        $out = '<div class="form-group">';
        $out .= '<select id="flagsSelect" class="flagsSelect" multiple autocomplete="off" name="flags[]">';
        foreach ($flags as $key => $value) {
            $checked = "";
            if (isset($options) && in_array($key, @$options)) {
                $checked = "selected";
            }
            $out .= '<option ' . $checked . ' value="' . $key . '">';

            $out .= $value;
            $out .= '</option>';
        }
        $out .= '</select>';
        $out .= '</div>';
        $out .= '<script>flagsSelect()</script>';
        return $out;
    }
}

if (!function_exists('field_flags_job_hire')) {
    function field_flags_job_hire($method, $options_get = "")
    {
        $flags = array(
            "none" => "None of the above",
            "vet" => "Veteran",
            "govt" => "Have Govt. Security Clearance",
            "visa" => "Have Valid Work Visa",
        );
        $options = [];
        if ($options_get != '') {
            $options = $options_get;
        }
        $out = '<div class="form-group">';
        $out .= '<select id="flagsSelect_' . $method . '" class="flagsSelect_' . $method . '" multiple autocomplete="off" name="flags_' . $method . '[]">';
        foreach ($flags as $key => $value) {
            $checked = "";
            if (isset($options) && in_array($key, @$options)) {
                $checked = "selected";
            }
            $out .= '<option ' . $checked . ' value="' . $key . '">';

            $out .= $value;
            $out .= '</option>';
        }
        $out .= '</select>';
        $out .= '</div>';
        if ($method == 'job') {
            $out .= '<script>flagsSelect_job()</script>';
        } else {
            $out .= '<script>flagsSelect_hire()</script>';
        }
        return $out;
    }
}

if (!function_exists('field_hide_show')) {
    function field_hide_show($name = "", $checked = "")
    {
        return '<div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
            <label class="btn">
                <input type="radio" name="' . $name . '" value="hide" ' . (($checked == "hide") ? "checked" : "") . '> Hide
            </label>
            <label class="btn">
                <input type="radio" name="' . $name . '" value="show" ' . (($checked == "show" || $checked == "") ? "checked" : "") . '> Show
            </label>
          </div>';
    }
}

if (!function_exists('field_yes_no')) {
    function field_yes_no($name = "", $checked = "")
    {
        return '<div class="btn-group btn--group btn-group-toggle" data-toggle="buttons">
            <label class="btn">
                <input type="radio" name="' . $name . '" ' . (($checked == "no" || $checked == "") ? "checked" : "") . ' value="no"> No
            </label>
            <label class="btn">
                <input type="radio" name="' . $name . '" ' . (($checked == "yes") ? "checked" : "") . ' value="yes"> Yes
            </label>
          </div>';
    }
}

?>
