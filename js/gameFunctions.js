var xhtmlns = "http://www.w3.org/1999/xhtml";
var svgns = "http://www.w3.org/2000/svg";
var BOARDX = 50;				//starting pos of board
var BOARDY = 50;				//look above
var boardArr = new Array();		//2d array [row][col]
var pieceArr = new Array();		//2d array [player][piece] (player is either 0 or 1)
var BOARDWIDTH = 8;				//how many squares across
var BOARDHEIGHT = 8;			//how many squares down
//the problem of dragging....
var myX;						//hold my last pos.
var myY;						//hold my last pos.
var mover='';					//hold the id of the thing I'm moving
var turn;						//hold whose turn it is (0 or 1)
var scrollX;
var scrollY;
var yourTurn='';
var ajaxData;

function scrollWindow(){

	scrollX = window.pageXOffset;
	scrollY = window.pageYOffset;
	return new Array(scrollX,scrollY);
}

function gameInit(){
	ajaxData = gameId;
	
	myArray1234 = scrollWindow();
	 
	//create a parent to stick board in...
	var gEle=document.createElementNS(svgns,'g');
	gEle.setAttributeNS(null,'transform','translate('+BOARDX+','+BOARDY+')');
	gEle.setAttributeNS(null,'id','game_'+gameId);
	
	//stick g on board
	document.getElementsByTagName('svg')[0].insertBefore(gEle,document.getElementsByTagName('svg')[0].childNodes[5]);
	//create the board...
	//var x = new Cell(document.getElementById('someIDsetByTheServer'),'cell_00',75,0,0);
	drop = 'true';
	for(i=0;i<BOARDWIDTH;i++){
		boardArr[i]=new Array();
		
		for(j=0;j<BOARDHEIGHT;j++){
		 	
			boardArr[i][j]=new Cell(document.getElementById('game_'+gameId),'cell_'+j+i,50,j,i,drop);

		}
		drop = 'false';
	}
	
	//new Piece(board,player,cellRow,cellCol,type,num)
	//create red
	pieceArr[0]=new Array();
	var idCount=0;
	var temp,temp2;
	if(turn == playerId){
		temp = playerId;
		document.getElementById('youPlayer').setAttribute("class","btn");
		temp2 = player2Id;
	}else{
		document.getElementById('opponentPlayer').setAttribute("class","btn");
		temp = player2Id;
		temp2 = playerId;
	}

	for(var i=0; i<(BOARDHEIGHT*(BOARDWIDTH-1));i++){
		if(i%2 == 0){
		 	pieceArr[0][idCount]=new Piece('game_'+gameId,temp,575,150,'Checker',idCount,'#338EE5');
		 	idCount++;
		 }else{
			pieceArr[0][idCount]=new Piece('game_'+gameId,temp2,575,250,'Checker',idCount,'#F5750C');
	 		idCount++;
	 	 }
	}
 	
 	document.getElementsByTagName('svg')[0].addEventListener('mouseup',releaseMove,false);
 	
 	document.getElementsByTagName('svg')[0].addEventListener('mousemove',go,false);
 	
	document.getElementById('youPlayer').firstChild.data+=player;
	
	document.getElementById('opponentPlayer').firstChild.data+=player2;
 	
	if(temp == playerId){
		document.getElementById('p1').setAttributeNS(null,'display','inline');
	}else{
		document.getElementById('p2').setAttributeNS(null,'display','inline');
	}

 	getTurnData(gameId);
}
			
///////////////////////Dragging code/////////////////////////

////setMove/////
//	set the id of the thing I'm moving...
////////////////
function setMove(which){	
	myArray1234 = scrollWindow();
 	mover = which;
 	xy=getTransform(mover);
 	myX=xy[0];
 	myY=xy[1];

 	getPiece(which).putOnTop(which);
}
			
///////////////////////////////Utilities////////////////////////////////////////
////get Piece/////
//	get the piece (object) from the id and return it...
////////////////
function getPiece(which){
	return (pieceArr[0][parseInt(which.substring((which.search(/\|/)+1),which.length))]);
}

			
////releaseMove/////
//	clear the id of the thing I'm moving...
////////////////
function releaseMove(evt){
	//check hit (need the current coords)
	// get the x and y of the mouse
	if(mover != ''){
		//is it YOUR turn?
		getTurnData(ajaxData);
		setTimeout(function(){
			if(turn == playerId){
				var hit=checkHit(evt.clientX - $('svg').position().left - 18 + scrollArray[0],evt.clientY - $('svg').position().top + scrollArray[1],mover,'rm');
			}else{
				var hit=false;
				nytwarning();
			}
			if(hit==true){
			//I'm on the square...
			//send the move to the server!!!
			}else{
				//move back
				setTransform(mover,myX,myY,1);
			}
			mover = '';	
		},100);
	}
}
			
			
////go/////
//	move the thing I'm moving...
////////////////
function go(evt){
	scrollArray = scrollWindow();
	if(mover != ''){
		setTransform(mover,evt.clientX - $('svg').position().left - 18 + scrollArray[0],evt.clientY - $('svg').position().top - 18 + scrollArray[1],0);
	}
	
}
			
////checkHit/////
//	takes care of the landing, if it's an important place 
////////////////
function checkHit(x,y,which,fromWhere){
	//change the x and y coords (mouse) to match the transform
	x=x-BOARDX;
	y=y-BOARDY;	
	for(j=0;j<BOARDHEIGHT;j++){
		var i=0;
		var drop = boardArr[i][j].myBBox;
		if(boardArr[1][j].occupied != ''){
			boardArr[0][j].droppable = false;
		}

		if(x>drop.x && x<(drop.x+drop.width) && y>drop.y && y<(drop.y+drop.height) && boardArr[i][j].droppable && boardArr[i][j].occupied == ''){
							
			//is it a legal move???
			//if it is - then
			//put me to the center....
			if(boardArr[1][j].occupied != ''){
				boardArr[0][j].droppable = false;
			}
			for(newI = 7; newI>0; newI--){

				if(boardArr[newI][j].occupied==''){
					setTransform(which,boardArr[newI][j].getCenterX(),boardArr[newI][j].getCenterY(),1);
					getPiece(which).changeCell(boardArr[newI][j].id,newI,j);
					break;
				}
			}

			newBoardData = which+'~'+newI+'~'+j+'~'+gameId;
			setBoardData(newBoardData);
			//change who's turn it is
			changeTurn();
			return true;
		}
	}
	return false;
}


			
////get Transform/////
//	look at the id of the piece sent in and work on it's transform
////////////////
function getTransform(which){
	var hold=document.getElementById(which).getAttributeNS(null,'transform');
	var retVal=new Array();
	retVal[0]=hold.substring((hold.search(/\(/) + 1),hold.search(/,/));			//x value
	retVal[1]=hold.substring((hold.search(/,/) + 1),hold.search(/\)/));;		//y value
	return retVal;
}
			
////set Transform/////
//	look at the id, x, y of the piece sent in and set it's translate
////////////////
function setTransform(which,x,y,move){
	if(move == 1){
		document.getElementById('ani_'+which).setAttributeNS(null,'from',x+',75');
		document.getElementById('ani_'+which).setAttributeNS(null,'to',x+','+y);
		document.getElementById('ani_'+which).beginElement();
		document.getElementById(which).setAttributeNS(null,'transform','translate('+x+','+y+')');
	}else{
		document.getElementById(which).setAttributeNS(null,'transform','translate('+x+','+y+')');
	}
}

////change turn////
//	change who's turn it is
//////////////////
function changeTurn(){
	

	if(turn == playerId){
		uData = gameId + '|' +player2Id;
		turn = player2Id;

		document.getElementById('opponentPlayer').setAttribute("class","btn");
		document.getElementById('youPlayer').setAttribute("class","");
		
	}else{
		uData = gameId + '|' +playerId;
		turn = player1Id;		
	}

	setTurnData(uData);
}


/////////////////////////////////Messages to user/////////////////////////////////
////nytwarning (not your turn)/////
//	tell player it isn't his turn!
////////////////
function nytwarning(){
		
		//http://stackoverflow.com/questions/12462036/dynamically-insert-foreignobject-into-svg-with-jquery
		
		var foreignObject = document.createElementNS('http://www.w3.org/2000/svg', 'foreignObject' );
		var body = document.createElement( 'body' ); // you cannot create bodies with .apend("<body />") for some reason
		$(foreignObject).attr("x", 450).attr("y", 20).attr("width", 122).attr("id", "nyt").attr("height", 18).append(body);
		$(body).append("<div class='well label label-important'>NOT YOUR TURN!</div>");
		$("#svgTag").append(foreignObject);
		$("#nyt").fadeOut(2000);
		setTimeout(function(){$("#nyt").remove()},3000);
	
}

////nypwarning (not your piece)/////
//	tell player it isn't his piece!
////////////////
function nypwarning(){
	var foreignObject = document.createElementNS('http://www.w3.org/2000/svg', 'foreignObject' );
	var body = document.createElement( 'body' ); // you cannot create bodies with .apend("<body />") for some reason
	$(foreignObject).attr("x", 450).attr("y", 35).attr("width", 122).attr("id", "nyp").attr("height", 18).append(body);
	$(body).append("<div class='label label-warning'>NOT YOUR PIECE!</div>");
	$("#svgTag").append(foreignObject);
	$("#nyp").fadeOut(2000);
	setTimeout(function(){$("#nyp").remove()},3000);
}