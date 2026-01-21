<?php

if ( ! function_exists( 'get_keywords_data' ) ){
    function get_keywords_data(){

        $keywords_data = [
            "taoh_user_var_1" => [
                "title" => "Career Stage",
                "description" => "What is your current career stage?",
                "label" => "Career Stage",
                "values" => "Student,Early Career,Mid Career,Senior Professional,Executive,Retired,Freelancer/Consultant,Career Transition",
                "required" => true,
                "enable" => true,
                "allow_expand" => false
            ],
            "taoh_user_var_2" => [
                "title" => "Industry",
                "description" => "Which industry do you belong to?",
                "label" => "Industry",
                "values" => "Technology & IT,Healthcare & Medicine,Education & Academia,Finance & Banking,Marketing & Advertising,Nonprofit & Social Services,Government & Public Sector,Arts, Media & Entertainment,Engineering & Manufacturing,Legal & Law Enforcement,Retail & Consumer Services,Hospitality & Tourism,Energy & Utilities,Agriculture & Environment,Transportation & Logistics,Other (Please Specify)",
                "required" => true,
                "enable" => true,
                "allow_expand" => false
            ],
            "taoh_user_var_3" => [
                "title" => "Community Interests",
                "description" => "What are your primary interests in this community?",
                "label" => "Community Interests",
                "values" => "Networking,Career Growth,Skill Development,Finding a Mentor,Offering Mentorship,Job Opportunities,Volunteering,Entrepreneurship,Research Collaboration,Work-Life Balance,Community Projects,Professional Events,Other (Please Specify)",
                "required" => true,
                "enable" => true,
                "allow_expand" => false
            ]
        ];
        
        return $keywords_data;
    }

}


// for dir in /var/www/*/hires/; do if [ "$dir" != "/var/www/nonprofits.club/hires/" ]; then cp /var/www/nonprofits.club/hires/keywords.php "$dir"; fi; done

