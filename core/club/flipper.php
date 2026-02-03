<?php
taoh_get_header();


$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$valid_dir_viewer = $taoh_user_is_logged_in && $user_info_obj->profile_complete && $user_info_obj->unlist_me_dir !== 'yes';

?>


<style>
.flipper {
    display: inline-block;
}

.big-title, .big-title span {
    font-size: 66px;
    font-weight: 700px;
    line-height: 1;
}

.big-title span {
   margin-right: 10px;
}


.word {
    display: none; 
}

.word.active {
    display: block; 
    /* background: #2557A7; */
    color: #2557A7;
    /* padding: 0.5rem; */
	animation: slide-in-anim 100ms ease-out forwards;
}


@keyframes slide-in-anim {
	0% {
        transform: translateY(30%);
	}
	100% {
		transform: translatey(0);
	}
}
</style>


<div class="bg-white the-start-club">
    <div class="header container py-4 row">
        <h1 class="col-lg-11 d-flex flex-wrap big-title">
            <span>Where</span> 
            <span class="flipper" style="">
                <span class="word" id="word1">careers</span>
                <span class="word" id="word2">communities</span>
                <span class="word" id="word3">collaborations</span>
            </span> 
            <span>Happens for
            HR Specialists</span> 
        </h1>
    </div>
</div>
<script>
    let currentIndex = 0;
    const words = document.querySelectorAll('.flipper .word');

    function flipWords() {
        words[currentIndex].classList.remove('active');
        currentIndex = (currentIndex + 1) % words.length;
        words[currentIndex].classList.add('active');
    }

    setInterval(flipWords, 1500);

    words[currentIndex].classList.add('active');
</script>
<?php
taoh_get_footer();