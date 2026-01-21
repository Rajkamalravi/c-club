<?php

if ( isset( $data ) && $data == 'employer'){
?> <div class="p-4">
	<h3 class="fs-17 pb-3 text-color-2">Post jobs and find candidates</h3>
	<div class="divider"><span></span></div>
		<p class="fs-14 lh-22 pb-2 pt-3">Post jobs, engage candidates and find the best match</p>
		<div id="accordion" class="generic-accordion pt-4">
			<div class="">
				<div class="card-header" id="headingOne">
					<button class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<span><span class="pr-2 fs-16">1.</span> Post open roles</span>
						<i class="la la-angle-down collapse-icon"></i>
					</button>
				</div>
				<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="card-body">
						<ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet-2 fs-14">
							<li class="lh-18 text-black-50 mb-0">Click post a job button and add 1 or multiple jobs. Add job details.</li>
						</ul>
					</div>
				</div>
			</div><!-- end card -->
			<div class="">
				<div class="card-header" id="headingTwo">
					<button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
						<span><span class="pr-2 fs-16">2.</span> Engage with candidates</span>
						<i class="la la-angle-down collapse-icon"></i>
					</button>
				</div>
				<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
					<div class="card-body">
						<p class="fs-14 lh-22 text-black-50">
							Candidates may reach out to you if they have questions about specific jobs through JobChat or through email. Answer their queries and engage them to get the suitable candidates to apply.
						</p>
					</div>
				</div>
			</div><!-- end card -->
			<div class="">
				<div class="card-header" id="headingThree">
					<button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
						<span><span class="pr-2 fs-16">3.</span> Select best candidates</span>
						<i class="la la-angle-down collapse-icon"></i>
					</button>
				</div>
				<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
					<div class="card-body">
						<p class="fs-14 lh-22 text-black-50">
							Once candidates apply through your preferred means, select the best suited.
						</p>
					</div>
				</div>
			</div><!-- end card -->
		</div><!-- end accordion -->
</div><!-- end card -->
<?php
} else {
?>
<div class="p-4 mob-hide">
	<h3 class="fs-17 pb-3 text-color-2">Search for jobs and apply</h3>
	<div class="divider"><span></span></div>
	<p class="fs-14 lh-22 pb-2 pt-3">Chat with a recruiter and apply to a job for informed candidacy and better outcomes.</p>
	<div id="accordion" class="generic-accordion pt-4">
			<div class="">
					<div class="card-header" id="headingOne">
							<button class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
									<span><span class="pr-2 fs-16">1.</span> Search for jobs</span>
									<i class="la la-angle-down collapse-icon"></i>
							</button>
					</div>
					<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
							<div class="card-body">
									<ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet-2 fs-14">
											<li class="lh-18 text-black-50 mb-0">Fill job role and location in the search box at the top of the page and click Search to find open roles.</li>
									</ul>
							</div>
					</div>
			</div><!-- end card -->
			<div class="">
					<div class="card-header" id="headingTwo">
							<button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
									<span><span class="pr-2 fs-16">2.</span> Chat with recruiters</span>
									<i class="la la-angle-down collapse-icon"></i>
							</button>
					</div>
					<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
							<div class="card-body">
									<p class="fs-14 lh-22 text-black-50">
											Click the jobs that you are interested in to see the details. If you have any questions, click the JobChat button to send a message to the recruiter. The recruiter will answer your questions, so you only apply to jobs that are the best fit to improve your success rate.
									</p>
							</div>
					</div>
			</div><!-- end card -->
			<div class="">
					<div class="card-header" id="headingThree">
							<button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
									<span><span class="pr-2 fs-16">3.</span> Apply for jobs</span>
									<i class="la la-angle-down collapse-icon"></i>
							</button>
					</div>
					<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
							<div class="card-body">
									<p class="fs-14 lh-22 text-black-50">
											The Job description page will have the details of how to apply for the job. Check out other resources and apps on Hires to help with job application, networking with other professionals and preparing for job interviews.
									</p>
							</div>
					</div>
			</div><!-- end card -->
	</div><!-- end accordion -->
</div><!-- end card -->

<?php
}
?>