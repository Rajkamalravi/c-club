<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
</head>
<body>
    <h2>Upload a File</h2>
    <form id="fileUploadForm" action="<?php echo TAOH_CDN_PREFIX; ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" id="fileToUpload" accept=".doc, .docx, .png, .gif, .jpg, .jpeg, .bmp, .pdf, .mp3, .mov,.cache">
        <input type="hidden" value="tc2asi3iida2" name="opscode">
        <input type="submit" value="Upload File" name="submit">
    </form>

    <div id="responseMessage" style="display: none;"></div>

    <script>
    document.getElementById("fileUploadForm").addEventListener("submit", function (event) {
        event.preventDefault();

        var formData = new FormData(this);

        fetch("<?php echo TAOH_CDN_PREFIX; ?>/cache/upload/now", {
            method: "POST",
            body: formData,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    document.getElementById("responseMessage").style.color = "green";
                    document.getElementById("responseMessage").innerHTML =
                        "File uploaded successfully. File location: " + data.output;
                } else {
                    document.getElementById("responseMessage").style.color = "red";
                    document.getElementById("responseMessage").innerHTML = "File upload failed: " + data.output;
                }
                document.getElementById("responseMessage").style.display = "block";
            })
            .catch((error) => {
                console.error("Error:", error);
                document.getElementById("responseMessage").style.color = "red";
                document.getElementById("responseMessage").innerHTML = "An error occurred: " + error.output;
                document.getElementById("responseMessage").style.display = "block";
            });
    });
</script>

</body>
</html>
