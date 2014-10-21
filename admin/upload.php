<?php
require 'includes/application_top.php';
//If directory doesnot exists create it.
$output_dir = "uploads/";

if(isset($_FILES["myfile"]))
{
	$ret = array ();
	
	$error = $_FILES["myfile"]["error"];
   
    if (! is_array ( $_FILES["myfile"]['name'] )) 	// single file
	{
		$RandomNum = time ();
		
		$ImageName = str_replace ( ' ', '-', strtolower ( $_FILES['myfile']['name'] ) );
		$ImageType = $_FILES['myfile']['type']; // "image/png", image/jpeg etc.
		
		$ImageExt = substr ( $ImageName, strrpos ( $ImageName, '.' ) );
		$ImageExt = str_replace ( '.', '', $ImageExt );
		$ImageName = preg_replace ( "/\.[^.\s]{3,4}$/", "", $ImageName );
		$NewImageName = $ImageName . '-' . $RandomNum . '.' . $ImageExt;
		
		move_uploaded_file ( $_FILES["myfile"]["tmp_name"], $output_dir . $NewImageName );
		// echo "<br> Error: ".$_FILES["myfile"]["error"];
		$title='g';
		$link='gg';
		tep_db_query("insert into image_slider (title, link, image) values ('" . $title . "', '" . $link . "', '" . $NewImageName . "')");

		
	} else {
		$fileCount = count ( $_FILES["myfile"] ['name'] );
		for($i = 0; $i < $fileCount; $i ++) {
			$RandomNum = time ();
			$title = 'test';
			$link = 'test';
			$ImageName = str_replace ( ' ', '-', strtolower ( $_FILES['myfile']['name'] [$i] ) );
			$ImageType = $_FILES['myfile']['type'] [$i]; // "image/png", image/jpeg etc.
			
			$ImageExt = substr ( $ImageName, strrpos ( $ImageName, '.' ) );
			$ImageExt = str_replace ( '.', '', $ImageExt );
			$ImageName = preg_replace ( "/\.[^.\s]{3,4}$/", "", $ImageName );
			$NewImageName = $ImageName . '-' . $RandomNum . '.' . $ImageExt;

			$ret [$NewImageName] = $output_dir . $NewImageName;
			move_uploaded_file ( $_FILES["myfile"]["tmp_name"] [$i], $output_dir . $NewImageName );
			tep_db_query("insert into image_slider (title, link, image) values ('" . $title . "', '" . $link . "', '" . $ImageName . "')");
		}
	}
    
    echo json_encode($ret);
 
}

?>