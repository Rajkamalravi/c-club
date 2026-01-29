
<script>
    var my_ptoken = "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>";
    var eve = "<?php echo $event_token ?? ''; ?>";

    function getEventRooms(eventtoken,response,hallColorArray,search='',tab_name='') {
        var speak_list = response.output.event_speaker;
        var exh_list = response.output.event_exhibitor;
        var is_content = 0;
        $('#rooms_list').html('');
        var content = '';
        var u = 0;

        if(exh_list !=undefined && exh_list.length > 0){
            content += `<div class="w-100 mx-auto mx-xl-0 pr-1 pr-lg-3">`;
            $.each(exh_list, function (i, v) {
                if(v.exh_room_keywords){
                    $.each(v.exh_room_keywords, function (i, exhkeywords) {
                        content += `<div id="exhr_${v.ID}" class="hall-list d-flex flex-column flex-md-row mb-3" style="min-height: 50px !important;">
                                         <div  class="info d-flex flex-column justify-content-center px-3 py-2 px-lg-4" style="flex: 1; border: 1px solid #d3d3d3;min-height: 50px !important;">
                                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between" style="gap: 12px;">
                                                <div style="flex: 1;">
                                                    ${exhkeywords}
                                                </div>
                                                <div class="button-container d-flex flex-wrap" style="gap: 6px;">
                                                    <a target="_blank" href="#" class="btn more-info">
                                                        <span>Join</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                    });
                    is_content = 1;
                }
            });
            if(!is_content){
                content +=   `No Rooms found`;
            }
            content += `<div>`;
            is_content =1;
        }
        if(is_content){
            $('#rooms_list').html(content);
        }
        loader(false, $("#speaker_loaderArea"));

    }

</script>
