<?php
function createthumb($name,$thumbname){
	$src_img=imagecreatefromjpeg($name);
	if ($src_img){
		$w = imagesx($src_img);
		$h = imagesy($src_img);

	//calculate new image dimensions (preserve aspect)
		$new_h=200;
		$new_w= min($new_h * ($w/$h), 400);
		
		$im2 = ImageCreateTrueColor($new_w, $new_h);
		if ($im2){
			imagecopyResampled ($im2, $src_img, 0, 0, 0, 0, $new_w, $new_h, $w, $h);		
			imagejpeg($im2, $thumbname);
			imagedestroy($im2);
		}
		imagedestroy($src_img);
		return true;
	} else {
		return false;
	}	
} 

//test section
if (isset($_GET['debug'])){
	$array = scandir('./reports');
	foreach($array as $file){
		$f=explode('.',$file);
		if ($f[1]=='jpg'){
			$report = "./reports/".$f[0];
			echo $report . "<BR/>";
			createthumb("$report.jpg",$report . "t.jpg");
		}
	}	
exit;
}

?>
