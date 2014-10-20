<?php
require('includes/application_top.php');
require(DIR_WS_INCLUDES . 'template_top.php');
?>
<link href="css/uploadfilemulti.css" rel="stylesheet">
<script src="js/jquery-1.8.0.min.js"></script>
<script src="js/jquery.fileuploadmulti.min.js"></script>

<table border="0" cellspacing="0" cellpadding="2">
	<tr>
		<td height="20px">			
		</td>
	</tr>
	<tr>
		<td height="20px">
			<h1>Image Silder</h1>
		</td>
	</tr>
	<tr>
		<td>Title:</td>
		<td><input type="text"></td>
	</tr>
	<tr>
		<td>Link:</td>
		<td><input type="text"></td>
	</tr>
	<tr>
		<td>image:</td>
		<td>
			<div id="mulitplefileuploader">Upload</div>
			<div id="status"></div>
		</td>
	</tr>
	<tr>
	<td><button>save</button></td>
	</tr>
</table>
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
		{console.log(files);
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