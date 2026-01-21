<?php
//require_once 'C:\Users\Lenovo\Downloads\Wellness 2.php';
//require_once 'C:\Users\Lenovo\Downloads\Generic2.txt';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Organization.txt';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Productivity.txt';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Upskilling.txt';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Learning.txt';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Mindfulness.txt';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Future of work.txt';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Resume.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Handling Change.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Interview.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Job Search.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Jobs of Future.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Leadership.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Mentorship_Coach.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Networking.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Branding.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Career development.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Growth Mindset.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Conflict Management.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt category blogs\Wellness.php';

//require_once 'C:\Users\Lenovo\Downloads\pods_php\analytics.php';
//require_once 'C:\Users\Lenovo\Downloads\pods_php\construction.php';
//require_once 'C:\Users\Lenovo\Downloads\pods_php\Energy.php';
//require_once 'C:\Users\Lenovo\Downloads\pods_php\Retail.php';
//require_once 'C:\Users\Lenovo\Downloads\pods_php\transit.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Software Development.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Govjobs.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\SQA Testing.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Startup-Entrpreneurship.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Students.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Technician.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Flexible-Gig.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Grants.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Manufacturing.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Non-profits.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Recruiter.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Returnship.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Trucking.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Healthcare.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Veterans.php';
//require_once 'C:\Users\Lenovo\Downloads\Chatgpt new podblogs consolidated\Skilled.php';

//require_once 'C:\Users\Lenovo\Downloads\pods-consolidated\Blogs-Consolidated.php';
//require_once 'C:\Users\devir\Downloads\Wellness.php';
//require_once 'C:\Users\devir\Downloads\Wellness 2.php';
//require_once 'C:\Users\devir\Downloads\Wellness 3.php';
//require_once 'D:\xampp\htdocs\Researchers club.php';
//require_once 'D:\xampp\htdocs\bluk\11072023\Leadership.php';


$slug = array('general-search-strategy','interview','job-search','networking','resume','jobs-of-future','branding','career-development','conflict-management','growth-mindset','handling-change','mentor-coach','organization','productivity','leadership','learning','mindfulness','upskilling','future-of-work','general-work-strategy');

// if category is the slug mentioned above, then category = slug and global = 1
// if category is not the slug mentioned above, then category = general and global = 0 and media_type = category
foreach($textfile as $key => $val){
    if($val['title'] != 'TITLEHERE'){
        if(in_array(strtolower($val['category']), $slug)){
            $title = $val['title'];
            $category = strtolower($val['category']); 
            $global = 1;
            $media_type = '';
            $desc = $val['body'];
        }else{
            $title = $val['title'];
            $category = $val['category']; //$category = 'general'; 
            $global = 0; 
            $media_type = $val['category'];
            $desc = $val['body'];
        }
    
        $post_array = array(
            "title" => $title,
            "category" => $category,
            "type" => "reads", // reads for blogs, and flash for flashcards
            "global" => $global, // 1 for global, 0 for private
            "description" => $desc,
            "media_type" => $media_type, // image,media, soundcloud, youtube, text
            "media_link" => "", // image,media, soundcloud, youtube, text
            "image" => "",
        );
        echo '<pre>'; print_r($post_array); exit();

        $postdata = http_build_query( $post_array );
        $opts = array(
        'http' => array(
                    'method'  => 'POST',
                    'header'  => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => $postdata,
            ),
            "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
            ),
        );
        $context  = stream_context_create( $opts );
        //echo $postdata; exit(); 
        //print_r($mail_array); exit();
        $url = "https://api.tao.ai/scripts/addblurb.php";
        echo file_get_contents( $url, false, $context );
    }
}