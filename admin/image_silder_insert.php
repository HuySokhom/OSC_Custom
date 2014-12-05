<?php
require('includes/application_top.php');
require(DIR_WS_INCLUDES . 'template_top.php');
?>
<script src="js/jquery-1.11.0.min.js"></script>

<form name="upload_image">
	<table>
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
			<td><input type="text" name="title" /></td>
		</tr>
		<tr>
			<td>Link:</td>
			<td><input type="text" name="link" /></td>
		</tr>
		<tr>
			<td>image:</td>
			<td>
				<input type="file" name="image" />
			</td>
		</tr>
	</table>	
</form>
<br/><br/>
<button class="save">upload</button>
<a href="images_slider.php">
	<button>back</button>
</a>
<script>
$(function(){
	var $form =$('form[name="upload_image"]');
	
	$('button.save').click(function(){
        console.log('hi');
        var valTitle = $form.find('input[name="title"]').val();
        var valLink = $form.find('input[name="link"]').val();
        var valImage = $form.find('input[name="image"]').val();
    	var info = {
    	    "title" : valTitle,
			"link" : valLink,
			"image" : valImage
    	};
        return $.ajax({
            type:'POST',
            url: 'api/ImageSlider',
            contentType: 'application/json; charset=utf-8',
			data: JSON.stringify(info),
            success:function(data){
                console.log("success");
                console.log(data);
            },
            error: function(data){
                console.log("error");
                console.log(data);
            }
        });
    });
});
</script>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>