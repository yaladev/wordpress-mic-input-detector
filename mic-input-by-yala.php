<?php
/**
 * Plugin Name: Mic Input by Yala
 * Plugin URI: https://github.com/yaladev/
 * Description: Shortcode [yala-mic-input] displays mic input to be used on pages, posts and lessons.
 * Version: 0.1
 * Text Domain: yala-mic-detector
 * Author: Yala Agency
 * Author URI: https://www.yala.agency
 */
 
 function yala_mic_detector($atts) {
	$Content = "<script language='javascript'>
			
			var audioContext = null;
			var meter = null;
			var canvasContext = null;
			var WIDTH=500;
			var HEIGHT=50;
			var rafID = null;

			var iniciarMic = function() {

				canvasContext = document.getElementById( 'meter' ).getContext('2d');
				
			    window.AudioContext = window.AudioContext || window.webkitAudioContext;
				
			    audioContext = new AudioContext();

			    try {


			    	  navigator.mediaDevices.getUserMedia({ audio: true, video: false })
      .then(gotStream, didntGetStream);

			      
			    } catch (e) {
			        alert('getUserMedia threw exception :' + e);
			    }

			}


			function didntGetStream() {
			    alert('Stream generation failed.');
			}

			var mediaStreamSource = null;

			function gotStream(stream) {
			    mediaStreamSource = audioContext.createMediaStreamSource(stream);

			    meter = createAudioMeter(audioContext);
			    mediaStreamSource.connect(meter);

			    drawLoop();
			}

			function drawLoop( time ) {
			    canvasContext.clearRect(0,0,WIDTH,HEIGHT);

			    if (meter.checkClipping())
			        canvasContext.fillStyle = 'red';
			    else
			        canvasContext.fillStyle = 'green';

			    canvasContext.fillRect(0, 0, meter.volume*WIDTH*1.4, HEIGHT);

			    rafID = window.requestAnimationFrame( drawLoop );
			}


			function createAudioMeter(audioContext,clipLevel,averaging,clipLag) {
				var processor = audioContext.createScriptProcessor(512);
				processor.onaudioprocess = volumeAudioProcess;
				processor.clipping = false;
				processor.lastClip = 0;
				processor.volume = 0;
				processor.clipLevel = clipLevel || 0.98;
				processor.averaging = averaging || 0.95;
				processor.clipLag = clipLag || 750;
				processor.connect(audioContext.destination);

				processor.checkClipping =
					function(){
						if (!this.clipping)
							return false;
						if ((this.lastClip + this.clipLag) < window.performance.now())
							this.clipping = false;
						return this.clipping;
					};

				processor.shutdown =
					function(){
						this.disconnect();
						this.onaudioprocess = null;
					};

				return processor;
			}

			function volumeAudioProcess( event ) {
				var buf = event.inputBuffer.getChannelData(0);
			    var bufLength = buf.length;
				var sum = 0;
			    var x;

			    for (var i=0; i<bufLength; i++) {
			    	x = buf[i];
			    	if (Math.abs(x)>=this.clipLevel) {
			    		this.clipping = true;
			    		this.lastClip = window.performance.now();
			    	}
			    	sum += x * x;
			    }

			    var rms =  Math.sqrt(sum / bufLength);

			    this.volume = Math.max(rms, this.volume*this.averaging);
			}

		</script>
		<canvas id='meter' width='500' height='50'></canvas>
		<input type='button' id='startmic' value='Usar microfone' onclick='iniciarMic()'>";
	 
    return $Content;
}

add_shortcode('yala-mic-input', 'yala_mic_detector');
