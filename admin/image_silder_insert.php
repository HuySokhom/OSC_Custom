<?php
require('includes/application_top.php');
require(DIR_WS_INCLUDES . 'template_top.php');
?>
<link href="css/uploadfilemulti.css" rel="stylesheet">
<script src="js/jquery-1.8.0.min.js"></script>
<script src="js/jquery.fileuploadmulti.min.js"></script>

<form action="" method="POST" enctype="multipart/form-data">
	<table border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td height="20px">			
			</td>
		</tr>
		<tr>
			<td height="20px">
				<h1>Image Slider</h1>
			</td>
		</tr>
		<tr>
			<td>Title:</td>
			<td><input type="text" name="title"></td>
		</tr>
		<tr>
			<td>Link:</td>
			<td><input type="text" name="link"></td>
		</tr>
		<tr>
			<td>image:</td>
			<td>
<!-- 				<input type="file" name="file"> -->
				<div id="mulitplefileuploader">Upload</div>
	<div id="status"></div>
			</td>
		</tr>
		<tr>
		<td><input type="submit" name="submit" value="upload"></td>
		</tr>
	</table>
	
</form>

<?php 
// 	if ( $_POST['submit'] ) {
// 		$query = '';
// 		$filename = $_FILES['file']['name'];
// 		$fileType = $_FILES['file']['type'];
// 		$fileSize = $_FILES['file']['size'];
// 		$filePath = $_FILES['file']['tmp_path'];	
// 		$output_dir = "uploads/";
// 		$title = $_POST['title'];
// 		$link = $_POST['link'];

// 		if ($filename != "") {
// 			$RandomNum = time ();
// 			$ImageExt = substr ( $filename, strrpos ( $filename, '.' ) );
// 			$ImageExt = str_replace ( '.', '', $ImageExt );
// 			$ImageName = preg_replace ( "/\.[^.\s]{3,4}$/", "", $filename );
// 			$NewImageName = $ImageName . '-' . $RandomNum . '.' . $ImageExt;

// 			move_uploaded_file ( $_FILES["myfile"]["tmp_name"], $output_dir . $NewImageName );

// 			tep_db_query("insert into image_slider (title, link, image) values ('" . $title . "', '" . $link . "', '" . $NewImageName . "')");
// 		}
// 	}
	
?>

<script>
$(document).ready(function()
{
	var settings = {
		url: "upload.php",
		method: "POST",
		allowedTypes:"jpg,png,gif,doc,pdf,zip",
		fileName: "myfile",
		multiple: true,
		onSuccess:function(files,data,xhr)
		{console.log(data);
			$("#status").html("<font color='green'>Upload is success</font>");
		},
	    afterUploadAll:function()
	    {
	        alert("all images uploaded!!");
	    },
		onError: function(files,status,errMsg)
		{
			$("#status").html("<font color='red'>Upload is Failed</font>");
		}
	};
	$("#mulitplefileuploader").uploadFile(settings);
});
</script>