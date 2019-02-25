<?php
include('config/db.php');

$onedrive_fullpath='/var/www/html/myphoto/OurPhotos/0-PICTURES - Our Life - Sorted/';

//TOP is local, the bottom is for localhost only
$onedrive_webpath='OurPhotos/0-PICTURES - Our Life - Sorted/';

$onedrive_webpath_local='file:///mnt/5tb/00-OneDrive_2018/0-PICTURES%20-%20Our%20Life%20-%20Sorted/';


if(!file_exists($onedrive_webpath)){
	die('<div style="color:white;font-size:20px">The OneDrive folder is NOT mounted, stopping...</div>');
}else{
	echo "Photo folder is fine!";
}
