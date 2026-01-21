<?php

?>
<?php taoh_get_header(); ?>
<style>
.bug-report-container {
  background-color: #fff;
  padding: 20px;
  max-width: 500px;
  margin: auto;
  border-radius: 6px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

textarea, input, select {
 min-height: 42px;
}

button {
  background-color: #007BFF;
  color: white;
  padding: 10px;
  border: none;
  width: 100%;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}
</style>
<div class="container">
  <div class="bug-report-container my-5">
    <h2>Report a Bug</h2>
    <form id="bugForm" enctype="multipart/form-data" method="post">
      <div class="form-group">
        <label class="fs-13 text-black lh-20 fw-medium">Description <span class="text-danger">*</span></label>
        <textarea class="form-control" name="description" placeholder="Describe the bug..." required></textarea>
      </div>
      <div class="form-group">
        <label class="fs-13 text-black lh-20 fw-medium">Steps to reproduce</label>
        <textarea class="form-control" name="steps" placeholder="Steps to reproduce (optional)"></textarea>
      </div>
      <div class="form-group">
        <label class="fs-13 text-black lh-20 fw-medium">Severity</label>
        <select class="form-control" name="severity">
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
        </select>
      </div>
      <div class="form-group">
        <label class="fs-13 text-black lh-20 fw-medium">Screenshots</label>
        <input class="form-control" type="file" name="screenshot" accept="image/*" />
      </div>
      <div class="form-group">
        <label class="fs-13 text-black lh-20 fw-medium">Contact info <span class="text-danger">*</span></label>
        <input class="form-control" type="email" name="contact_info" placeholder="you@example.com" required>
      </div>
      <button type="submit" class="mt-3"><i class=""></i>Submit</button>
      <p id="responseMsg"></p>
    </form>
  </div>
</div>

<script>
$('#bugForm').on('submit', function(e) {
    e.preventDefault(); // Prevent form submission
// alert('test bug form');
    const formData = new FormData(this);
    formData.append('taoh_action', 'taoh_report_bug');
    var visitedUrls = JSON.parse(localStorage.getItem('visitedUrls')) || [];
    console.log(visitedUrls);
    formData.append('visited_url', 'visitedUrls');

    let submit_btn = $(this).find('button[type="submit"]');
    submit_btn.prop('disabled', true);

    let submit_btn_icon = submit_btn.find('i');
    submit_btn_icon.removeClass('fa-play-circle-o').addClass('fa-spinner fa-spin');

    $.ajax({
		url: '<?php echo taoh_site_ajax_url(); ?>',
		type: 'post',
		data: formData,
		dataType: 'json',
		processData: false,
		contentType: false,
		cache: false,
		success: function (response) {
			// console.log(response);
            location.reload();
        },
		error: function (xhr, status, error) {
			console.log(xhr.responseText);
		}
	});
});
</script>

<?php taoh_get_footer();  ?>
