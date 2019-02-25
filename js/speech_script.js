try {
  var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  var recognition = new SpeechRecognition();
}
catch(e) {
  console.error(e);
  $('.no-browser-support').show();
  $('.app').hide();
}

function getRotationDegrees(obj) {
    var matrix = obj.css("-webkit-transform") ||
    obj.css("-moz-transform")    ||
    obj.css("-ms-transform")     ||
    obj.css("-o-transform")      ||
    obj.css("transform");
    if(matrix !== 'none') {
        var values = matrix.split('(')[1].split(')')[0].split(',');
        var a = values[0];
        var b = values[1];
        var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
    } else { var angle = 0; }
    //return (angle < 0) ? angle + 360 : angle;
    return angle;
}


var noteTextarea = $('#note-textarea');
var instructions = $('#recording-instructions');

var noteContent = '';

// Get all notes from previous sessions and display them.
var notes = getAllNotes();
renderNotes(notes);



/*-----------------------------
      Voice Recognition
------------------------------*/

// If false, the recording will stop after a few seconds of silence.
// When true, the silence period is longer (about 15 seconds),
// allowing us to keep recording even when the user pauses.
recognition.continuous = true;

// This block is called every time the Speech APi captures a line.
recognition.onresult = function(event) {

  // event is a SpeechRecognitionEvent object.
  // It holds all the lines we have captured so far.
  // We only need the current one.
  var current = event.resultIndex;

  // Get a transcript of what was said.
  var transcript = event.results[current][0].transcript;

  // Add the current transcript to the contents of our Note.
  // There is a weird bug on mobile, where everything is repeated twice.
  // There is no official solution so far so we have to handle an edge case.
  var mobileRepeatBug = (current == 1 && transcript == event.results[0][0].transcript);

  if(!mobileRepeatBug) {
    noteContent += transcript;
    noteTextarea.val(noteContent+"\r\n");

    var str = $('input').val();

    var iHeard = jQuery.trim(transcript);
    console.log("I HEARD: "+iHeard);

    if (iHeard.toLowerCase().indexOf("disable") >= 0 ||
        iHeard.toLowerCase().indexOf("disabled") >= 0 ||
        iHeard.toLowerCase().indexOf("do not show") >= 0 ||
        iHeard.toLowerCase().indexOf("don't show") >= 0 ||
        iHeard.toLowerCase().indexOf("dont show") >= 0 ||
        iHeard.toLowerCase().indexOf("erase") >= 0 ||
        iHeard.toLowerCase().indexOf("remove") >= 0 ||
        iHeard.toLowerCase().indexOf("delete") >= 0 ||
        iHeard.toLowerCase().indexOf("f*****") >= 0 ||
        iHeard.toLowerCase().indexOf("disposed") >= 0 ||
        iHeard.toLowerCase().indexOf("dispose") >= 0
        ){
      console.log('I Heard, DELETE: '+iHeard);
      markImageDisplay();


    }else if (iHeard.toLowerCase().indexOf("orientation") >= 0 ||
        iHeard.toLowerCase().indexOf("bad orientation") >= 0 ||
        iHeard.toLowerCase().indexOf("wrong way") >= 0 ||
        iHeard.toLowerCase().indexOf("turn it") >= 0
        ){
      console.log('I Heard, THIS IS A BAD ORIENTATION  -  '+iHeard);
      markBadOrientation();
    }else if (iHeard.toLowerCase().indexOf("stop") >= 0 ||
        iHeard.toLowerCase().indexOf("pause") >= 0 ||
        iHeard.toLowerCase().indexOf("hold on") >= 0 ||
        iHeard.toLowerCase().indexOf("wait") >= 0
        )
    {
      console.log('I Heard, WAIT/PAUSE  -  '+iHeard);
      clearTimeout(timer);

    }else if (iHeard.toLowerCase().indexOf("detail") >= 0 ||
        iHeard.toLowerCase().indexOf("info") >= 0 ||
        iHeard.toLowerCase().indexOf("extended info") >= 0 ||
        iHeard.toLowerCase().indexOf("data") >= 0
        )
    {
      console.log('I Heard, EXTENDED DATA  -  '+iHeard);
      clearTimeout(timer);

      if(jQuery('#info').hasClass('dback')){
        jQuery('#info').removeClass('dback');
        jQuery('#info').addClass('noshow');
      }else{
        jQuery('#info').addClass('dback');
        jQuery('#info').removeClass('noshow');
      }
    }else if (iHeard.toLowerCase().indexOf("continue") >= 0 ||
        iHeard.toLowerCase().indexOf("keep going") >= 0 ||
        iHeard.toLowerCase().indexOf("next") >= 0 ||
        iHeard.toLowerCase().indexOf("continue") >= 0 ||
        iHeard.toLowerCase().indexOf("forward") >= 0
        )
    {
      console.log('I Heard, NEXT  -  '+iHeard);
      location.reload();
    }else if (iHeard.toLowerCase().indexOf("rotate 90") >= 0){
      console.log('I Heard, rotate 90  -  '+iHeard);
      jQuery('img').css('transform','rotate(90deg)');
    }else if (iHeard.toLowerCase().indexOf("rotate 180") >= 0){
      console.log('I Heard, rotate 180  -  '+iHeard);
      jQuery('img').css('transform','rotate(180deg)');
    }else if (iHeard.toLowerCase().indexOf("rotate 270") >= 0){
      console.log('I Heard, rotate 270  -  '+iHeard);
      jQuery('img').css('transform','rotate(270deg)');
    }else if (iHeard.toLowerCase().indexOf("rotate 360") >= 0){
      console.log('I Heard, rotate 360  -  '+iHeard);
      jQuery('img').css('transform','rotate(360deg)');
    }else if (iHeard.toLowerCase().indexOf("rotate left") >= 0){
      var rot=getRotationDegrees(jQuery('img'))+90;
      console.log('I Heard, rotate LEFT ROT: '+rot+'  -  '+iHeard);
      jQuery('img').css('transform','rotate('+rot+'deg)');
    }else if (iHeard.toLowerCase().indexOf("rotate right") >= 0){
      var rot=getRotationDegrees(jQuery('img'))-90;
      console.log('I Heard, rotate RIGHT ROT: '+rot+'  -  '+iHeard);
      jQuery('img').css('transform','rotate('+rot+'deg)');
    }else if (iHeard.toLowerCase().indexOf("rotate clear") >= 0){
      console.log('I Heard, rotate CLEAR - '+iHeard);
      jQuery('img').css('transform','unset');
    }else if (iHeard.toLowerCase().indexOf("rotate save") >= 0){
      console.log('I Heard, rotate SAVE - '+iHeard);
      var rot=getRotationDegrees(jQuery('img'));
      alert('AJAX SAVE transform('+rot+'deg); TBD');
    }else if (iHeard.toLowerCase().indexOf("what did you hear") >= 0 ||
      iHeard.toLowerCase().indexOf("show voice") >= 0 ||
      iHeard.toLowerCase().indexOf("close voice") >= 0){
      console.log('SHOW SPEECH CONTAINER')
      if(jQuery('.container').hasClass('noshow')){
        jQuery('.container').removeClass('noshow');
      }else{
        jQuery('.container').addClass('noshow');
      }
    }else if (iHeard.toLowerCase().indexOf("go back") >= 0 ||
      iHeard.toLowerCase().indexOf("show again") >= 0 ||
      iHeard.toLowerCase().indexOf("replay") >= 0){
      console.log('I Heard, SHOW AGAIN  -  '+iHeard);
      show_alert('SHOW AGAIN','SHOWING AGAIN',30000);

      jQuery.ajax({
        method: "POST",
        url: "ajax_photo.php",
        data: { action: 'go_back'
        }
      }).done(function(e) {

        var obj = jQuery.parseJSON(e);
        location.reload();

      });


    }else if (iHeard.toLowerCase().indexOf("rating") >= 0 ||
        iHeard.toLowerCase().indexOf("reading") >= 0 ||
        iHeard.toLowerCase().indexOf("set rating") >= 0 ||
        iHeard.toLowerCase().indexOf("rate") >= 0
      ){
        var rating=parseInt($.trim(iHeard.toLowerCase().slice(-2)));
        if(Math.floor(rating)>0 && Math.floor(rating)<11){
          show_alert('RATING DETECTED','SAVE RATING: '+Math.floor(rating),3000);
        }else{
          console.log('WTF');
          debugger;
        }
    }


  }
};

recognition.onstart = function() {
  instructions.text('Voice recognition activated. Try speaking into the microphone.');
}

recognition.onspeechend = function() {
  instructions.text('You were quiet for a while so voice recognition turned itself off.');
}

recognition.onerror = function(event) {
  if(event.error == 'no-speech') {
    instructions.text('No speech was detected. Try again.');
  };
}



/*-----------------------------
      App buttons and input
------------------------------*/

$('#start-record-btn').on('click', function(e) {
  if (noteContent.length) {
    noteContent += ' ';
  }
  recognition.start();
});


$('#pause-record-btn').on('click', function(e) {
  recognition.stop();
  instructions.text('Voice recognition paused.');
});

// Sync the text inside the text area with the noteContent variable.
noteTextarea.on('input', function() {
  noteContent = $(this).val();
})


/*-----------------------------
      Speech Synthesis
------------------------------*/

function readOutLoud(message) {
	var speech = new SpeechSynthesisUtterance();

  // Set the text and voice attributes.
	speech.text = message;
	speech.volume = 1;
	speech.rate = 1;
	speech.pitch = 1;

	window.speechSynthesis.speak(speech);
}



/*-----------------------------
      Helper Functions
------------------------------*/

function renderNotes(notes) {
  var html = '';
  if(notes.length) {
    notes.forEach(function(note) {
      html+= `<li class="note">
        <p class="header">
          <span class="date">${note.date}</span>
          <a href="#" class="listen-note" title="Listen to Note">Listen to Note</a>
          <a href="#" class="delete-note" title="Delete">Delete</a>
        </p>
        <p class="content">${note.content}</p>
      </li>`;
    });
  }
  else {
    html = '<li><p class="content">You don\'t have any notes yet.</p></li>';
  }

}


function saveNote(dateTime, content) {
  localStorage.setItem('note-' + dateTime, content);
}


function getAllNotes() {
  var notes = [];
  var key;
  for (var i = 0; i < localStorage.length; i++) {
    key = localStorage.key(i);

    if(key.substring(0,5) == 'note-') {
      notes.push({
        date: key.replace('note-',''),
        content: localStorage.getItem(localStorage.key(i))
      });
    }
  }
  return notes;
}


function deleteNote(dateTime) {
  localStorage.removeItem('note-' + dateTime);
}

function markImageDisplay() {

    var curImage=jQuery('img').attr('src').replace('OurPhotos/0-PICTURES - Our Life - Sorted/','');
      var imgId=jQuery('img').attr('imgid');

      jQuery.ajax({
        method: "POST",
        url: "ajax_photo.php",
        data: { action: 'delete_image',
                curImage: curImage,
                imgId: imgId
        }
      }).done(function(e) {

        var obj = jQuery.parseJSON(e);

        show_alert('DELETE','IMGID: '+imgId+'<br>Action: '+obj.msg,5000);


      });

}

function markBadOrientation() {

      var curImage=jQuery('img').attr('src').replace('OurPhotos/0-PICTURES - Our Life - Sorted/','');
      var imgId=jQuery('img').attr('imgid');

      jQuery.ajax({
        method: "POST",
        url: "ajax_photo.php",
        data: { action: 'bad_orientation',
                curImage: curImage,
                imgId: imgId
        }
      }).done(function(e) {

        var obj = jQuery.parseJSON(e);

        show_alert('ORIENTATION','IMGID: '+imgId+'<br>Action: Marked as BAD',5000);


      });

}


