<?php
// events_modals.php - Contains all modal HTML content

if ($show_rsvp_ticket) {
    ?>
    <!-- RSVP Ticket Modal -->
    <div class="modal fade" id="rsvpTicketModal" tabindex="-1" role="dialog" aria-labelledby="rsvpTicketModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rsvpTicketModalTitle">Event Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer justify-content-center"></div>
            </div>
        </div>
    </div>
    <?php
}

if (!$valid_user) {
    ?>
    <!-- Complete Settings Modal -->
    <div class="modal fade" id="completeSettingsModal" tabindex="-1" role="dialog" aria-labelledby="completeSettingsModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completeSettingsModalTitle">Complete settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">You cannot attend the event without complete settings; please use the opportunity now to complete your settings.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn theme-btn-primary complete_settings_now">Complete Settings Now</button>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<!-- Contact Host Modal -->
<div class="modal fade" id="contacthostModal" tabindex="-1" role="dialog" aria-labelledby="contacthostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contacthostModalLabel">Contact Host</h5>
                <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body h-auto">
                <form name="contacthostForm" id="contacthostForm" enctype="multipart/form-data" method="post">
                    <div class="form-group">
                        <input type="text" class="form-control" name="title" id="title" placeholder="Title" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="description" id="description" rows="4" placeholder="Describe here..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" id="contacthostSubmit" class="btn btn-primary"><i></i>Submit Report</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Co-attendees Modal -->
<div class="modal fade dark-head-v1-modal" id="addCoAttendessModal" tabindex="-1" role="dialog" aria-labelledby="addCoAttendessModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header p-4 px-lg-5">
                <div class="modal-title">
                    <div class="d-flex align-items-center" style="gap: 21px;">
                        <svg class="d-none d-sm-block" width="59" height="59" viewBox="0 0 59 59" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="29.5" cy="29.5" r="28.5" fill="black" stroke="#D3D3D3" stroke-width="2"/>
                            <path d="M18.65 23.25C18.65 21.5924 19.3032 20.0027 20.4659 18.8306C21.6287 17.6585 23.2057 17 24.85 17C26.4943 17 28.0713 17.6585 29.2341 18.8306C30.3968 20.0027 31.05 21.5924 31.05 23.25C31.05 24.9076 30.3968 26.4973 29.2341 27.6694C28.0713 28.8415 26.4943 29.5 24.85 29.5C23.2057 29.5 21.6287 28.8415 20.4659 27.6694C19.3032 26.4973 18.65 24.9076 18.65 23.25ZM14 40.5498C14 35.7402 17.8653 31.8438 22.6364 31.8438H27.0636C31.8347 31.8438 35.7 35.7402 35.7 40.5498C35.7 41.3506 35.0558 42 34.2614 42H15.4386C14.6442 42 14 41.3506 14 40.5498ZM38.4125 32.2344V29.1094H35.3125C34.6683 29.1094 34.15 28.5869 34.15 27.9375C34.15 27.2881 34.6683 26.7656 35.3125 26.7656H38.4125V23.6406C38.4125 22.9912 38.9308 22.4688 39.575 22.4688C40.2192 22.4688 40.7375 22.9912 40.7375 23.6406V26.7656H43.8375C44.4817 26.7656 45 27.2881 45 27.9375C45 28.5869 44.4817 29.1094 43.8375 29.1094H40.7375V32.2344C40.7375 32.8838 40.2192 33.4062 39.575 33.4062C38.9308 33.4062 38.4125 32.8838 38.4125 32.2344Z" fill="white"/>
                        </svg>
                        <div>
                            <h5>Add Co-attendees</h5>
                            <p>Your sponsorship allows you to bring 3 Co-Attendees</p>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn v1-modal-close-btn" data-dismiss="modal" aria-label="Close">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.6449 2.04935C12.1134 1.58082 12.1134 0.819928 11.6449 0.351398C11.1763 -0.117133 10.4154 -0.117133 9.9469 0.351398L6 4.30205L2.04935 0.355146C1.58082 -0.113384 0.819928 -0.113384 0.351398 0.355146C-0.117133 0.823676 -0.117133 1.58457 0.351398 2.0531L4.30205 6L0.355146 9.95065C-0.113384 10.4192 -0.113384 11.1801 0.355146 11.6486C0.823676 12.1171 1.58457 12.1171 2.0531 11.6486L6 7.69795L9.95065 11.6449C10.4192 12.1134 11.1801 12.1134 11.6486 11.6449C12.1171 11.1763 12.1171 10.4154 11.6486 9.9469L7.69795 6L11.6449 2.04935Z" fill="#ffffff"/>
                    </svg>
                </button>
            </div>
            
            <div class="modal-body px-4 px-lg-5">
                <form>
                    <h6 class="mb-3 pb-1">Co-Attendee 1</h6>
                    <div class="row mx-0 border pt-3 mb-4 px-lg-3">
                        <div class="form-group col-lg-6">
                            <label for="">First Name*</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Last Name *</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-lg-12">
                            <label for="">Email *</label>
                            <input type="email" class="form-control">
                        </div>
                    </div>

                    <h6 class="mb-3 pb-1">Co-Attendee 2</h6>
                    <div class="row mx-0 border pt-3 mb-4 px-lg-3">
                        <div class="form-group col-lg-6">
                            <label for="">First Name*</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Last Name *</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-lg-12">
                            <label for="">Email *</label>
                            <input type="email" class="form-control">
                        </div>
                    </div>

                    <h6 class="mb-3 pb-1">Co-Attendee 3</h6>
                    <div class="row mx-0 border pt-3 mb-4 px-lg-3">
                        <div class="form-group col-lg-6">
                            <label for="">First Name*</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Last Name *</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-lg-12">
                            <label for="">Email *</label>
                            <input type="email" class="form-control">
                        </div>
                    </div>

                    <button type="button" class="btn v1-dark-btn-lg mb-5">Add Co-attendees</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Sponsor Info Modal -->
<div class="modal fade sponsorship-option" id="sponsorInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>