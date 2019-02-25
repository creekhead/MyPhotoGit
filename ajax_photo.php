<?php

include('config/db.php');

$post=$_GET;
if(!isset($post['action'])){
	$post=$_POST;
}
$action=$post['action'];

if($action=='go_back'){
	$save_filename='log/save_lastone_goback';
	file_put_contents($save_filename, '1');
	echo json_encode(array('msg'=>'success'));
}

if($action=='set_cookie'){
	$year=((isset($post['year'])) ? $post['year'] : '');
	if(!empty($year)){
		setcookie("year", $year);
		echo "cookie set";
		exit;
	}

	$keyword=((isset($post['keyword'])) ? $post['keyword'] : '');
	if(!empty($keyword)){
		setcookie("keyword", $keyword);
		echo "cookie set";
		exit;
	}


}

if($action=='delete_image'){
	$id=$post['imgId'];
	$image=$post['curImage'];

	//echo "ID: ".$id."<BR>";
	//echo "image: ".$image."<BR>";
	//print_r($post);

	//$sql="select * from ourpictures where id='".$id."' and filename='".$image."'";

	$update="UPDATE ourpictures_new SET display = 1 WHERE id=".$id.";";
	$result = $conn->query($update);

	if($result==1){
		echo json_encode(array('msg'=>'success'));
	}else{
		echo json_encode(array('msg'=>'error'));
	}

	write_delete_log($image);
	write_sql_update_log($update.' RESULT: '.$result);
}

if($action=='bad_orientation'){
	$id=$post['imgId'];
	$image=$post['curImage'];

	//echo "ID: ".$id."<BR>";
	//echo "image: ".$image."<BR>";
	//print_r($post);

	//$sql="select * from ourpictures where id='".$id."' and filename='".$image."'";

	$update="UPDATE ourpictures_new SET display = 5 WHERE id=".$id.";";
	$result = $conn->query($update);

	if($result==1){
		echo json_encode(array('msg'=>'success'));
	}else{
		echo json_encode(array('msg'=>'error'));
	}

	write_orientation_log($image);
	write_sql_update_log($update.' RESULT: '.$result);
}
//

?>
