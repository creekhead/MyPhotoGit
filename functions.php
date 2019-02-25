<?php


function orientationCheck($image) {
	list($width, $height) = getimagesize($image);

	if( $width > $height)
	    $orientation = "landscape";
	else
	    $orientation = "portrait";

	return $orientation;
}


function run_sql($sql,$conn){
	if(!$conn) {
		die("No SQL Connection??");
	}

    $result = $conn->query($sql);
    $row=array();

	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	    	if(empty($row)){
	    		continue;
	    	}

	    	$ret=$row;
	        if(!empty($ret['exif'])){
	        	$exif=unserialize($row['exif']);
	        	$ret['exif']=$exif;
	        }
	    }
	} else {
	    write_log('QUERY ZERO: '.$sql);
	}

	return $ret;

}

function write_log($msg){
	$fp = fopen('log/image_log.log', 'a');
	fwrite($fp, date('m/d/y H:i:s')." : ".$msg.PHP_EOL);
	fclose($fp);
}

function write_delete_log($image){
	$debug_log_filename='log/delete_image.html';
	if(!is_file($debug_log_filename)){
	    file_put_contents($debug_log_filename, '');
	}
	$fp = fopen($debug_log_filename, 'a');
	fwrite($fp, "<img src='/myphoto/OurPhotos/0-PICTURES - Our Life - Sorted/".$image."' width=350>".PHP_EOL);
	fclose($fp);
}
function write_orientation_log($image){
	$debug_log_filename='log/orientation_fix.log';
	if(!is_file($debug_log_filename)){
	    file_put_contents($debug_log_filename, '');
	}
	$fp = fopen($debug_log_filename, 'a');

	fwrite($fp, "/mnt/5tb_nfs/00-OneDrive_2018/0-PICTURES - Our Life - Sorted/".$image.PHP_EOL);
	fclose($fp);
}

function write_sql_update_log($sql){
	$debug_log_filename='log/sql_update_log.log';
	if(!is_file($debug_log_filename)){
	    file_put_contents($debug_log_filename, '');
	}
	$fp = fopen($debug_log_filename, 'a');
	fwrite($fp, date('m/d/y H:i:s')." : ".$sql.PHP_EOL);
	fclose($fp);
}

function write_log_debug($msg,$url='',$exif_orient=''){
	if (!strpos($url,'Our Life')) {
		$url='OurPhotos/0-PICTURES - Our Life - Sorted/'.$url;
	}

	//text log
	$debug_log_filename='log/image_debug.log';

	if(!is_file($debug_log_filename)){
	    file_put_contents($debug_log_filename, '');
	}

	$fp = fopen($debug_log_filename, 'a');
	fwrite($fp, date('m/d/y H:i:s')." : ".$msg.PHP_EOL);
	fclose($fp);

	//HTML log
	$debug_log_filename='log/image_debug_html.php';

	$url='<a href="'.$url.'" exif="'.$exif_orient.'" title="'.$msg.' - EXIF: '.$exif_orient.'" alt="'.$msg.' - EXIF: '.$exif_orient.'" target="_blank">'.$url.'</a>';

	if(!is_file($debug_log_filename)){
		$php="<?php include_once('modal_html.html');?><br>".PHP_EOL.PHP_EOL;
	    file_put_contents($debug_log_filename, $php);
	}

	$fp = fopen($debug_log_filename, 'a');
	fwrite($fp, date('m/d/y H:i:s')." : ".$msg.' EXIF_ORIENT: '.$exif_orient.'<br>');
	fwrite($fp, date('m/d/y H:i:s')." : ".$url.'<br>');
	fwrite($fp, date('m/d/y H:i:s')."----".'<br>');
	fwrite($fp, PHP_EOL);
	fclose($fp);
}


function check_goback(){
	$save_filename='log/save_lastone_goback';

	if(is_file($save_filename)){
		unlink($save_filename);
		$prev_tmp=file_get_contents('log/save_lastone');
		$prev=unserialize($prev_tmp);

		//unlink($save_filename);
		return $prev;
	}
	return false;
}

function save_lastone($fn){
	$save_filename='log/save_lastone';

	if(!is_file($save_filename)){
	    file_put_contents($save_filename, '');
	}else{
		$prev_tmp=file_get_contents($save_filename);
		$prev=unserialize($prev_tmp);
	}

	if(is_array($prev) && count($prev)>=3){
		$prev[]=$fn;
		$prev_new[0]=$prev[1];
		$prev_new[1]=$prev[2];
		$prev_new[2]=$fn;
		unset($prev);
		$prev=$prev_new;
	}else{
		$prev[]=$fn;
	}

	$fp = fopen($save_filename, 'w');
	fwrite($fp, serialize($prev));
	fclose($fp);

}
?>
