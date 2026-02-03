$(document).ready(function(){
	// copy btn 
    $(document).on('click','.copys-btn', function(event) {
        /* Select text area by id*/
        var Text = document.getElementById("copys-input");
        /* Select the text inside text area. */
        Text.select();
        /* Copy selected text into clipboard */
        var copy_text = navigator.clipboard.writeText(Text.value);
        if(copy_text){    
        $(".text-success-message").addClass('active');
		setTimeout(function(){
			$(".text-success-message").removeClass('active'); 
        }, 1000);
        }
    });

    $(document).on('click','.share_count', function(event) {
        var dataId = $(this).attr("data-click");
        var datagptoken = $(this).attr("data-gptoken");
        var datagconntoken = $(this).attr("data-gconntoken");
        var datasocial = $(this).attr("data-social");
        var dataconttype = $('.social-icon-box').attr("data-conttype");
        var dataajax = $('.social-icon-box').attr("data-ajax");

        save_metrics(dataconttype,share,datagconntoken);

		
        $("#exampleModal1").modal('hide');
        if(dataId == 'facebook'){
            window.open(datasocial, '_blank').focus();
        }else if (dataId == 'twitter') {
            window.open(datasocial, '_blank').focus();
        }else if (dataId == 'linkedin') {
            window.open(datasocial, '_blank').focus();
        }else{
            window.open(datasocial, '_blank').focus();
        }
		
	});
});