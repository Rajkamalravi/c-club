<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
<?php if($data){ 

$image_id = taoh_get_youtubeId($data);

//$image = explode('https://youtu.be/',$data)[1]; 
//$image_id = explode('?',$image)[0];
//$image_id = 'DAl3dbTnAgw';   
?>
<div class="company-details-panel mb-30px" id="company-videos">
    <div class="pt-3 video-box">
        <!-- image url - https://img.youtube.com/vi/YouTubeID/ImageFormat.jpg -->
	<img class="w-100 rounded-rounded lazy" src="https://img.youtube.com/vi/<?php echo $image_id;?>/maxresdefault.jpg" data-src="https://img.youtube.com/vi/<?php echo $image_id;?>/maxresdefault.jpg" alt="video image">
	<div class="video-content">
		<!-- <a class="icon-element icon-element-lg hover-y mx-auto" href="<?php //echo $event_arr['conttoken']['event_video'];?>"     data-fancybox="" title="Play Video"> -->
            <a class="icon-element icon-element-lg hover-y mx-auto" href="https://www.youtube.com/embed/<?php echo $image_id;?>?rel=0" data-fancybox="" title="Play Video">
			<svg width="24" height="24" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 58.752 58.752" xml:space="preserve">
				<path fill="#0d233e" d="M52.524,23.925L12.507,0.824c-1.907-1.1-4.376-1.097-6.276,0C4.293,1.94,3.088,4.025,3.088,6.264v46.205
				c0,2.24,1.204,4.325,3.131,5.435c0.953,0.555,2.042,0.848,3.149,0.848c1.104,0,2.192-0.292,3.141-0.843l40.017-23.103
				c1.936-1.119,3.138-3.203,3.138-5.439C55.663,27.134,54.462,25.05,52.524,23.925z M49.524,29.612L9.504,52.716
				c-0.082,0.047-0.18,0.052-0.279-0.005c-0.084-0.049-0.137-0.142-0.137-0.242V6.263c0-0.1,0.052-0.192,0.14-0.243
				c0.042-0.025,0.09-0.038,0.139-0.038c0.051,0,0.099,0.013,0.142,0.038l40.01,23.098c0.089,0.052,0.145,0.147,0.145,0.249
				C49.663,29.47,49.611,29.561,49.524,29.612z"></path>
			</svg>
		</a>
	</div>
</div>
</div>
<?php } ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script>
    $(document).ready(function(){
     var companyDetailGallery = $('[data-fancybox="company-detail-gallery"]');
     if (companyDetailGallery.length) {
            companyDetailGallery.fancybox();
        }
    });
</script>