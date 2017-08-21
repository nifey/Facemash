var id1,id2;
var previd;
var prevcount;

function start(){
	var xhr = new XMLHttpRequest();
	xhr.open("POST","update.php",true);
	xhr.overrideMimeType("application/json");
	xhr.send('{"jsontype":"start"}');
	xhr.addEventListener("readystatechange",processRequest,false);
	function processRequest(e){
		if (xhr.readyState==4&&xhr.status==200){
			var response = JSON.parse(xhr.responseText);
			id1=response.id1;
			id2=response.id2;
			update(response.src1,response.src2);
		}
	}

}

function change(a){
	var xhr = new XMLHttpRequest();
	xhr.open("POST","update.php",true);
	xhr.overrideMimeType("application/json");
	if(a==1){
		var req ='{"jsontype":"change","id1":"' + id1 + '" , "score1":"1","id2":"'+ id2 +'" ,"score2":"0"}' ;
	} else {
		var req ='{"jsontype":"change","id1":"' + id1 + '" , "score1":"0","id2":"'+ id2 +'" ,"score2":"1"}' ;
	}
	xhr.send(req);
	xhr.addEventListener("readystatechange",processRequest,false);
	function processRequest(e){
		if (xhr.readyState==4&&xhr.status==200){
			var response = JSON.parse(xhr.responseText);
			id1=response.id1;
			id2=response.id2;
			update(response.src1,response.src2);
		}
	}
}

function update(url1,url2){
	document.getElementById("img1").src=url1;
	document.getElementById("img2").src=url2;
}
