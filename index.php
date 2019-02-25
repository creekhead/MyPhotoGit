<?php

?>

<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Voice Controlled Photo App</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/images.css">
        <!--
<link rel="stylesheet" href="css/shoelace.css">


        -->
        <link rel='shortcut icon' type='image/x-icon' href='/favicon.ico' />

    </head>

<!-- #04041b overflow: hidden;

 -->
<body style="background: #04041b;overflow: hidden;height: 100%;text-align: center;margin: 0;color: dodgerblue">



<div id="photo_image_container">
<?php


	require('photo_scripts.php');


?>
</div>
<div id="photo_image_container_next">
</div>

<div id="dialog" title="Basic dialog">
    <p id=diag_msg></p>
</div>

		<!-- <script type="text/javascript" src="js/jquery.js"></script> -->
<?php
$speech_test = file_get_contents('speech_htmltest.php');
echo $speech_test;
?>
<script src="js/jquery_3_2_1.min.js"></script>
<script type="text/javascript" src="js/speech_script.js"></script>
<script type="text/javascript" src="js/images.js"></script>

<!-- DIALOG -->
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

</body>
</html>

<style>
#photo_image_container_next{
    display: none;
}
</style>
