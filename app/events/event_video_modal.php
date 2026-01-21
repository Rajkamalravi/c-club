<div class="modal fade" id="orgVideoModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="upgradeModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg bg-white" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white" style="border: none;">
                <div class="w-100">
                    <button type="button" class="btn pull-right" data-dismiss="modal" aria-label="Close">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z"
                                  fill="#D3D3D3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="modal-body">
                <div id="org_video_holder">
                    <!-- Video content will be loaded here dynamically -->
                </div>
            </div>

            <div class="modal-footer justify-content-center py-0" style="border: none;">
                <button type="button" id="orgVideoAccept" class="btn btn-primary mb-3" data-event_token="">Ok Got it!</button>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    async function constructOrganizerVideoModalContent(event_output, user_timezone) {
        const conttoken_data = event_output.conttoken;
        const eventToken = event_output.eventtoken;
        const org_video_message_link = conttoken_data.org_video_message_link;

        if (!org_video_message_link) {
            return;
        }

        const org_video_watched_key = 'org_video_watched_' + eventToken;
        $('#orgVideoAccept').setSyncedData('event_token', eventToken);

        const org_video_message_src = getEmbedSrc(org_video_message_link);
        if (org_video_message_src) {
            let event_live_state_data = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', conttoken_data.locality, user_timezone, 0);

            const video_iframe = `<div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="${org_video_message_src}" allowfullscreen style="width:100%; height:400px; border:none;"></iframe>
                    </div>`;

            $('#org_video_holder').html(video_iframe);

            if (event_live_state_data?.state === 'live') {
                $('#org_video_modal_btn').show();
                if(localStorage.getItem(org_video_watched_key) !== '1') {
                    $('#orgVideoModal').modal('show');
                }
            } else {
                $('#org_video_modal_btn').hide();
                $('#orgVideoModal').modal('hide');
            }
        } else {
            $('#org_video_holder').html('<p class="px-3">No valid video message available.</p>');
            $('#org_video_modal_btn').hide();
            $('#orgVideoModal').modal('hide');
        }
    }

    $(document).on('click', '#orgVideoAccept', function (e) {
        e.preventDefault();
        const eventToken = $(this).getSyncedData('event_token');
        if (eventToken) {
            const org_video_watched_key = 'org_video_watched_' + eventToken;
            localStorage.setItem(org_video_watched_key, '1');
        }
        $('#orgVideoModal').modal('hide');
    });
</script>