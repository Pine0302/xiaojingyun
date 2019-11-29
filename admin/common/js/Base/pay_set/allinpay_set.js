function submitV(a){	
	document.getElementById("upform").submit();
}
function selPayType(v){
 
    switch(parseInt(v,10)){
	   case 1:
	      document.getElementById("div_key").style.display="none";
	      break;
	   case 2:
	      document.getElementById("div_key").style.display="block";
	      break;
	}
 }
