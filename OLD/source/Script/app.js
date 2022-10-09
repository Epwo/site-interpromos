var TempCurve = []
const CurveList = []
window.onload = function() {

	var myCanvas = document.getElementById("myCanvas");
	var ctx = myCanvas.getContext("2d");

	var modal = document.getElementById("PromptHelp");
	var btn = document.getElementById('ButtonHelp');
	var btnBack = document.getElementById("BackButton");
	var span = document.getElementsByClassName("close")[0];


    // Fill Window Width and Height
    myCanvas.width = window.innerWidth;
	myCanvas.height = window.innerHeight;
	
	
	//button things
	btnBack.onclick = function(){
		ctrz()
	}
	//open
	btn.onclick = function() {
		console.log("prompt modal");
		modal.style.display = "block";
		
	  }
	//close on X
	span.onclick = function() {
		console.log("quit via x");
		modal.style.display = "none";

	  }
	//close when outside
	window.onclick = function(event) {
		if (event.target == modal) {
			console.log("quit via outside");
			modal.style.display = "none";
		}
	  }

	// Set Background Color
    ctx.fillStyle="#fff";
    ctx.fillRect(0,0,myCanvas.width,myCanvas.height);

	  
	function ctrz(){
		console.log('control  + z'); 
		CurveList.pop();
		//suppr everything
		ctx.clearRect(0, 0, myCanvas.width, myCanvas.height);
		//redraw
		for(let i=0;i<CurveList.length;i++){
			ctx.beginPath();
			ctx.moveTo(CurveList[i][0], CurveList[i][1]);
			for(const elem of CurveList[i]){
				console.log(elem);
				ctx.lineTo(elem[0],elem[1])
			}
			ctx.stroke();
		}
	}



    // Mouse Event Handlers
	if(myCanvas){
		var isDown = false;
		var canvasX, canvasY;
		ctx.lineWidth = 5;
		
		$(document).keydown(function(e){
			if( e.which === 90 && e.ctrlKey){
				ctrz()
			 }
		  })

		  
		$(myCanvas)
		.mousedown(function(e){
			TempCurve.push(new Array(e.clientX,e.clientY));
			isDown = true;
			ctx.beginPath();
			canvasX = e.pageX - myCanvas.offsetLeft;
			canvasY = e.pageY - myCanvas.offsetTop;
			ctx.moveTo(canvasX, canvasY);
		})
		.mousemove(function(e){
			if(isDown !== false) {
				TempCurve.push(new Array(e.clientX,e.clientY));
				canvasX = e.pageX - myCanvas.offsetLeft;
				canvasY = e.pageY - myCanvas.offsetTop;
				ctx.lineTo(canvasX, canvasY);
				ctx.strokeStyle = "#000";
				ctx.stroke();
			}
		})
		.mouseup(function(e){
			if(TempCurve.length > 5){
				CurveList.push(TempCurve);
			}
			TempCurve = [];
			onmousemove = null;
			isDown = false; 
			ctx.closePath();
		});
	}
	
	// Touch Events Handlers
	draw = {
		started: false,
		start: function(evt) {

			ctx.beginPath();
			ctx.moveTo(
				evt.touches[0].pageX,
				evt.touches[0].pageY
			);

			this.started = true;

		},
		move: function(evt) {

			if (this.started) {
				ctx.lineTo(
					evt.touches[0].pageX,
					evt.touches[0].pageY
				);

				ctx.strokeStyle = "#000";
				ctx.lineWidth = 5;
				ctx.stroke();
			}

		},
		end: function(evt) {
			this.started = false;
		},
        cancel: function(evt){

        }
	};
	
	// Touch Events
	myCanvas.addEventListener('touchstart', draw.start, false);
	myCanvas.addEventListener('touchend', draw.end, false);
	myCanvas.addEventListener('touchmove', draw.move, false);
	
	// Disable Page Move
	document.body.addEventListener('touchmove',function(evt){
		evt.preventDefault();
	},false);
};