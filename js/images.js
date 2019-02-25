	var timer;
	var start_time = new Date();
	var staticTime=15000;

	function TrimSecondsMinutes(elapsed) {
	    if (elapsed >= 60)
	        return TrimSecondsMinutes(elapsed - 60);
	    return elapsed;
	}

	function show_alert(title,msg,time=2000){
        jQuery('#diag_msg').html(msg);
        jQuery( "#dialog" ).dialog({
            title: title,
            modal: true,
            open: function() {
                var foo = $(this);
                setTimeout(function() {
                   foo.dialog('close');
                }, time);
            }
        });
    }

	jQuery(document).ready(function() {

		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== 'h')
				return;

			show_alert('REMINDER','Z - Show extended info<br>S - Show speech<br>O - Mark bad orientation<br>D - Mark bad image<br>SPACE - Start voice<br>Y - Add year<br>K - Add KeyWord<br>',6000);

		});

		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== 'n')
				return;

			staticTime=10000;
			//getNextImage();

		});


		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== 'p')
				return;

			staticTime=500000;
		});

		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== 'y')
				return;

			var year=prompt('Enter year here:');
			jQuery.ajax({
				method: "POST",
				url: "ajax_photo.php",
				data: { action: 'set_cookie',
				        year: year
			}
			}).done(function(e) {

				//var obj = jQuery.parseJSON(e);

				//show_alert('DELETE','IMGID: '+imgId+'<br>Action: '+obj.msg,5000);


			});
		});

		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== 'k')
				return;

			var keyword=prompt('Enter year keyword:');
			jQuery.ajax({
				method: "POST",
				url: "ajax_photo.php",
				data: { action: 'set_cookie',
				        keyword: keyword
			}
			}).done(function(e) {

				//var obj = jQuery.parseJSON(e);

				//show_alert('DELETE','IMGID: '+imgId+'<br>Action: '+obj.msg,5000);


			});
		});


		/* Show overflow
		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== 'o')
				return;
			if(jQuery('body').css('overflow')=='hidden'){
				jQuery('body').css('overflow','unset');
			}else{
				jQuery('body').css('overflow','hidden');
			}


		});
		*/

		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== 'z')
				return;

			clearTimeout(timer);
			//recognition.stop();

			console.log('STOPPED TIMER and VOICE.STOP')
			if(jQuery('#info').hasClass('dback')){
				jQuery('#info').removeClass('dback');
				jQuery('#info').addClass('noshow');
			}else{
				jQuery('#info').addClass('dback');
				jQuery('#info').removeClass('noshow');
			}
 		});

		//press 's' to see speech
		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== 's')
				return;

			console.log('SHOW SPEECH CONTAINER')
			if(jQuery('.container').hasClass('noshow')){
				jQuery('.container').removeClass('noshow');
			}else{
				jQuery('.container').addClass('noshow');
			}
 		});


		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== 'o')
				return;

			console.log('MARK IMAGE NO DISPLAY');
			markBadOrientation();
		});

		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== ' ')
				return;

			console.log('MARK IMAGE NO DISPLAY');
			markImageDisplay();
		});

		jQuery(this).bind('keypress', function(e) {
			var key = (e.keyCode ? e.keyCode : e.charCode);
			if(String.fromCharCode(key) !== 'v')
				return;


				if(recognition.started!=true){
					console.log("Startign VOICE");
					recognition.start();
					recognition.started=true;
				}else{
					console.log("Stopping VOICE");
					recognition.stop();
					recognition.started=false;
				}


		});

		function getNextImage(){
			start_time = new Date();
			console.log("GET NEXT IMAGE");
			jQuery.ajax({
		        method: "POST",
		        url: "photo_scripts.php?json="+Math.floor(Math.random() * 999999999999)
		    }).done(function(e) {

		        var obj = jQuery.parseJSON(e);
		        console.log('ADDED NEXT IMAGE HTML');
		        console.log(obj.html);

				jQuery('#photo_image_container_next').append(obj.html);

		        jQuery("#photo_image_container_next > #photo_container > img")
				    .on('load', function() {

						var end_time = new Date();

						var elapsed_ms = end_time - start_time;
						var seconds = Math.round(elapsed_ms / 1000);
						var minutes = Math.round(seconds / 60);
						var hours = Math.round(minutes / 60);

						var sec = TrimSecondsMinutes(seconds);
						var min = TrimSecondsMinutes(minutes);


						if(sec>15){
							var timeToWait=staticTime - 15000;
						}else{
							var timeToWait=staticTime-(seconds*1000);
						}


						console.log("IMAGE loaded correctly Load: "+sec+" Wait: "+(timeToWait/1000));
						setTimeout(function(){

							jQuery('#photo_image_container').remove();
								jQuery('#photo_image_container_next').attr('id','photo_image_container');
							jQuery('#photo_image_container').after('<div id=photo_image_container_next></div>');
							console.log('NEXT IMAGE LOADED');

							getNextImage();
						}, timeToWait);

					})
				    .on('error', function() {
				    	console.log("error loading next image");
				    	getNextImage();
					});

		    });
		}

		jQuery("#photo_image_container > #photo_container > img")
				    .on('load', function() {
				    	setTimeout(function(){
							console.log('MAIN IMAGE LOADED');
							getNextImage();
						}, 10000);
				    })
				    .on('error', function() {
				    	console.log("error loading main image");
				    	getNextImage();
					});



		//timer = setTimeout(function(){
			//show_alert('RELOAD','Time up',2000);
			//location.reload();
		//}, 20000);

/*
		setInterval(function() {
 			jQuery.ajax({
		        method: "POST",
		        url: "photo_scripts.php?json="+Math.floor(Math.random() * 999999999999)
		    }).done(function(e) {

		        var obj = jQuery.parseJSON(e);
		        console.log('ADDED NEXT IMAGE');
		        console.log(obj.html);

				jQuery('#photo_image_container_next').append(obj.html);

	        	console.log('Added ONLOAD Image');
		        jQuery("#photo_image_container_next > #photo_container > img")
				    .on('load', function() {
				    	console.log("image loaded correctly");
				    	jQuery('#photo_image_container').remove();
	      				jQuery('#photo_image_container_next').attr('id','photo_image_container');
	        			jQuery('#photo_image_container').after('<div id=photo_image_container_next></div>');
					})
				    .on('error', function() { console.log("error loading image"); });

		    });
		    console.log('LOADNEXT');
		   /*
		    setTimeout(function(){
		    	console.log('SHOWING NEXT IMAGE');
				jQuery('#photo_image_container').remove();
		        jQuery('#photo_image_container_next').attr('id','photo_image_container');
		        jQuery('#photo_image_container').after('<div id=photo_image_container_next></div>');
			}, 15000);


    	}, 25000);
  */


		//console.log("Startign VOICE");
		//recognition.start();

	});
