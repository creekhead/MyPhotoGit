<?php
include('config/db.php');

$onedrive_fullpath='/var/www/html/myphoto/OurPhotos/0-PICTURES - Our Life - Sorted/';

//TOP is local, the bottom is for localhost only
$onedrive_webpath='OurPhotos/0-PICTURES - Our Life - Sorted/';

$onedrive_webpath_local='file:///mnt/5tb/00-OneDrive_2018/0-PICTURES%20-%20Our%20Life%20-%20Sorted/';

if($lastinv=check_goback()){
	$debug=true;
	//$image['filename']='2005/2005_05 - Nicks Communion n Party/IMGP5139.JPG';
	//$image['filename']=$lastinv[2];
	//$exif=exif_read_data($onedrive_webpath.$image['filename'], 'IFD0');	echo 'pic: <b>';
	//$image['exif']=$exif;

	$sql="SELECT * FROM ourpictures_new where display!=1 and filename='".$lastinv[1]."'";
	$image=run_sql($sql,$conn);

}else{
	//get NEW image to show
	$count=0;
	while(empty($image)){
		$sqladdon='';
		if(isset($_COOKIE['year'])){
			$sqladdon="AND year like '%".$_COOKIE['year']."%' ";
		}
		if(isset($_COOKIE['keyword'])){
			$sqladdon="AND filename like '%".$_COOKIE['keyword']."%' ";
		}
		$sql = "SELECT * FROM ourpictures_new
				WHERE display!=1
				AND filename not like '%.avi%'
				AND filename not like '%.mp4%'
				".$sqladdon."
				ORDER BY RAND() LIMIT 1;";

		$image=run_sql($sql,$conn);
		$count++;
		if($count>10) exit();
	}
	//save it
	save_lastone($image['filename']);
}

$html='';
$id=$image['id'];
$pic=$image['filename'];
$width=$image['width'];
$height=$image['height'];
$filename=$onedrive_webpath.$image['filename'];

if($width>$height){
	$o='landscape';
	$css="height: 100vh;width: 100%;";
}else{
	$o='portrait';
	$css="height: 100%;width: 100vh;";
}

$year=$image['year'];
$date_taken=$image['date_taken'];
$date_created=$image['date_created'];
$date_modified=$image['date_modified'];
$exif_orient=((isset($image['exif']['Orientation'])) ? $image['exif']['Orientation'] : '');

//$orientation=orientationCheck($filename);
$orientation='';

$t=pathinfo($filename);
$dirname=$t['dirname'];
$basename=$t['basename'];
$extension=$t['extension'];
$file=$t['filename'];
$link='http://pkminty/myphoto/'.$dirname;

$msg='Image: '.$pic.' Orient: '.$orientation.'/'.$exif_orient;
write_log($msg);

//record EXIF
if($exif_orient!=1 && $exif_orient!=6){
	write_log_debug('Filename: '.$pic.' EXIF Orient: '.$exif_orient,$pic,$exif_orient);
}

	//exif
	$html.='<div id="info" class="noshow">';
		$html.='Name: <b>'.$pic.'</b><BR>';
		$html.='O: <b>'.$orientation.'/'.$exif_orient.'/'.$o.'</b><BR>';
		$html.='WIDTH: <b>'.$width.' HEIGHT: '.$height.'</b><BR>';
		$html.='PATH: <b>'.$pic.'</b><BR>';
		$html.='LINK: <b><a href="'.$link.'" target="_blank">'.$link.'</a></b><BR>';
		$html.='FILE: <b>'.$file.'.'.$extension.'</b><BR>';
		$html.="<BR><PRE>";
		//$html.=print_r($image,true);
	$html.="</div>";

	//display image
	if(strpos($filename,'.MP4') || strpos($filename,'.mp4')){
		$html.='<video id="videoBox" src="'.$filename.'" style="width: 100%; height: 100%;" controls playsinline autoplay muted loop>';
			$html.='This is fallback content to display for user agents that do not support the video tag.';
		$html.='</video>';
	}else{

		if($orientation=='landscape'){
			//$css="width: 100vw;";
		}else{
			//$css="height: 100vh;";
		}

		$html.=PHP_EOL.PHP_EOL;
		$html.= "<div id='photo_container'>".PHP_EOL;
			if($exif_orient==6){
				$html.= "<img src='".$filename."' ext='6' imgid='".$id."' dim='w: ".$width." h: ".$height." o: ".$o."' style='transform: rotate(90deg);".$css."'>";
				write_log_debug('Filename: '.$pic.' EXIF Orient: '.$exif_orient,$pic,$exif_orient); //270??
			}elseif($exif_orient==3){
				$html.= "<img src='".$filename."' ext='3' imgid='".$id."' dim='w: ".$width." h: ".$height." o: ".$o."' style='transform: rotate(180deg);".$css."'>";
				write_log_debug('Filename: '.$pic.' EXIF Orient: '.$exif_orient,$pic,$exif_orient);
			}elseif($exif_orient==1){
				$html.= "<img src='".$filename."' ext='1' imgid='".$id."' dim='w: ".$width." h: ".$height." o: ".$o."' style='".$css."'>";
				write_log_debug('Filename: '.$pic.' EXIF Orient: '.$exif_orient,$pic,$exif_orient);
			}else if($orientation=='landscape'){
				$html.= "<img src='".$filename."' ext='landscape' imgid='".$id."' dim='w: ".$width." h: ".$height." o: ".$o."' style='".$css."'>";
			}else{
				$html.= "<img src='".$filename."' ext='else' imgid='".$id."' dim='w: ".$width." h: ".$height." o: ".$o."' style='".$css."'>";
			}
		$html.= PHP_EOL."</div>";
	}

	if(isset($_GET['json'])){
		$all=json_encode(array('html'=>$html));
		print($all);
	}else{
		echo $html.PHP_EOL.PHP_EOL;
	}


/*
exif[orientation] == 3: 180
exif[orientation] == 6: 270
exif[orientation] == 8: 90
 */


?>
