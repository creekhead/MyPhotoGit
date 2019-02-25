<?php

$servername = "localhost";
$username = "pk";
$password = "";
$dbname = "ourpictures";

// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
	//echo "Mysql Connected".PHP_EOL;
}

echo "Start parse: ".PHP_EOL;

$onedrive_fullpath='/mnt/5tb_nfs/00-OneDrive_2018/0-PICTURES - Our Life - Sorted/';
$onedrive_webpath='/mnt/5tb_nfs/00-OneDrive_2018/0-PICTURES - Our Life - Sorted/';


$folders = scandir($onedrive_fullpath);

foreach($folders as $key=>$eachFolder){
	echo 'eachFolder: '.$eachFolder.PHP_EOL;
	if($eachFolder!='2018') continue;

	if($eachFolder=='.'
		|| $eachFolder=='..'
		|| $eachFolder=='.tmp.drivedownload'
		|| $eachFolder=='0-MISC_PIC_BACKUP'
		|| $eachFolder=='1980-1989-'
	) continue;


	if(is_dir($onedrive_fullpath.$eachFolder)){
		#TRUE
	    $all=getDirContents($onedrive_fullpath.$eachFolder);

	    foreach($all as $key=>$eachOne){

			$eachImage=str_replace('/mnt/5tb_nfs/00-OneDrive_2018/0-PICTURES - Our Life - Sorted/', '', $eachOne);
			$fullPath=$onedrive_fullpath.$eachImage;
			echo 'eachImage: '.$eachImage.PHP_EOL;

			$t=explode('/', $eachImage);
			$year=$t[0];
			$filesize=@filesize($fullPath);

			//GET and MATCH STORED DATA
			$sql="select * from ourpictures_new where year='".$year."' and filename='".$eachImage."'";
			//echo "SQL: ".$sql.PHP_EOL;
			$cur_image=run_sql($sql,$conn);
			$cur_filesize=((isset($cur_image['filesize'])) ? $cur_image['filesize'] : 0);
			$cur_id=((isset($cur_image['id'])) ? $cur_image['id'] : 0);

			//echo 'cur_filesize: '.$cur_filesize.PHP_EOL;
			//echo 'filesize: '.$filesize.PHP_EOL;
			//echo 'cur_id: '.$cur_id.PHP_EOL;

			if($cur_filesize==$filesize){
				//same
				echo "DB AND IMAGE MATCHES".PHP_EOL;
				continue;
			}else{
				//doesnt match/doesnt exist
				if($cur_filesize==0){
					echo "NO DB RECORD".PHP_EOL;
				}else{
					echo "Record exists, deleting record and re-create".PHP_EOL;
					$sql="delete from ourpictures_new where id='".$cur_id."'";
					$del=run_sql($sql,$conn);
				}
			}
			//print_r($image);

			if (strpos($fullPath,'mp4') || strpos($fullPath,'avi')) {
			 	$exif = array();
			}else{
			 	$exif = @exif_read_data($fullPath, 'IFD0');
			}

			//$exif = @exif_read_data($fullPath, 0, true);
			$created_date=date ("m/d/y H:i:s", filemtime($fullPath));
			$modified_date=date ("m/d/y H:i:s", filectime($fullPath));
			//echo 'getExifDateMod: '.PHP_EOL;
			$mod_date=getExifDateMod($exif,$fullPath);
			//echo 'getExifSerialized: '.PHP_EOL;
			$exif_serial=getExifSerialized($exif);

			$wh=@getimagesize($fullPath);
			if(is_array($wh)){
				$width=((isset($wh[0])) ? $wh[0] : 0);
				$height=((isset($wh[1])) ? $wh[1] : 0);
			}else{
				$width=0;
				$height=0;
			}

			if(is_dir($fullPath) || $filesize==0){
				if($filesize==0){
					write_error_log("EMPTY: ".$fullPath." SIZE: ".$filesize);
					//echo "EMPTY: ".$onedrive_webpath.$eachImage.PHP_EOL;
					continue;
				}else{
					write_error_log("DIR: ".$fullPath." SIZE: ".$filesize);
					//echo "DIR: ".$onedrive_webpath.$eachImage.PHP_EOL;
					continue;
				}
			}

			echo 'size: '.$filesize.PHP_EOL;
			echo 'year: '.$year.PHP_EOL;
			echo 'height: '.$height.PHP_EOL;
			echo 'width: '.$width.PHP_EOL;
			echo 'created_date: '.$created_date.PHP_EOL;
			echo 'modified_date: '.$modified_date.PHP_EOL;
			echo 'mod_date: '.$mod_date.PHP_EOL;
			echo 'exif_serial: '.$exif_serial.PHP_EOL;
			echo '------------'.PHP_EOL;

			//echo "<img src='".$fullPath."'>";
			insert_sql($eachImage,$year,$mod_date,$created_date,$modified_date,$exif_serial,$filesize,$width,$height,$conn);
			write_log("PROCESSED Key: ".$key." IMAGE: ".$eachImage);

			//if($key>15) exit();
		}
	}
}




function insert_sql($eachImage,$year,$mod_date,$created_date,$modified_date,$exif_serial,$filesize,$width,$height,$conn){
	if(!$conn) {
		die("No SQL Connection");
	}
	 $sql = "INSERT INTO ourpictures_new (filename, year,date_taken, date_created,date_modified,exif,filesize,width,height,display)
		VALUES ('$eachImage', '$year','$mod_date', '$created_date','$modified_date','$exif_serial','$filesize','$width','$height',0);";
	echo "SQL: ".$sql;

	if ($conn->query($sql) === TRUE) {
		echo " - RECORDED".PHP_EOL;
		return true;
	} else {
		echo " - SQL Error ".PHP_EOL;
		write_error_log("SQL Error: " . $sql);
	}
}

function write_log($msg){
	$fp = fopen('log/image_insert_log.log', 'a');
	fwrite($fp, date('m/d/y H:i:s')." : ".$msg.PHP_EOL);
	fclose($fp);
}

function write_error_log($msg){
	$fp = fopen('log/image_insert_error_log.log', 'a');
	fwrite($fp, date('m/d/y H:i:s')." : ".$msg.PHP_EOL);
	fclose($fp);
}

function getExifDateMod($exif,$fullPath){
	$takenDate = NULL;
	if(empty($exif)){
		return '';
	}

	if(isset($exif['FILE']['FileDateTime']) && !empty($exif['FILE']['FileDateTime'])){
		$takenDate=date("m/d/y H:i:s", $exif['FILE']['FileDateTime']);
		return $takenDate;
	}else{
		write_error_log("Failed to get DateTaken: " . $fullPath);
		echo "Failed to get DateTaken: " . $fullPath.PHP_EOL;
		return '';
	}
}

function getExifSerialized($exif){

	if(isset($exif['COMPUTED'])){
		unset($exif['COMPUTED']);
	}

	if(isset($exif['ExtensibleMetadataPlatform'])){
		unset($exif['ExtensibleMetadataPlatform']);
	}
	if(isset($exif['ModeArray'])){
		unset($exif['ModeArray']);
	}
	if(isset($exif['ImageInfo'])){
		unset($exif['ImageInfo']);
	}
	if(isset($exif['UserComment'])){
		unset($exif['UserComment']);
	}
	if(is_array($exif)){
		foreach($exif as $key=>$eachNode){

			//IF AT ZERO POSITION
			if (strpos($key,'UndefinedTag')!==false) {
				// Needle Found
				echo "FOUND key: ".$key."<br>";
				unset($exif[$key]);
			}

		}
	}

	$exif_clean=trimData($exif);
	if(is_array($exif_clean))
		$exif_no_empty=array_filter($exif_clean);
	else
		$exif_no_empty='';

	if(empty($exif_no_empty))
		return '';
	else
		return str_replace("'", "\'", serialize($exif_no_empty));
}

function trimData($data){
   if($data == null)
       return null;

   if(is_array($data)){
       return array_map('trimData', $data);
   }else{
   	 return trim($data);
   }
}

function getDirContents($dir, &$results = array()){
    $files = scandir($dir);
	echo 'Dir: '.$dir.PHP_EOL;
    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
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
	    return $ret;
	}
}

/*
$sql = "SELECT * FROM ourpictures";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<BR><b>VAR: </b><PRE>";
        print_r($row);
        echo '<hr style="color:tomato">';
        print_r('<br><br/>Variable: row - /c/users/peter/appdata/local/temp/rsub-tfchww/images.php:18');
        exit;
    }
} else {
    echo "0 results";
}

 */