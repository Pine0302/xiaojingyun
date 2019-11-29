function submitV(a){
	var init_reward = document.getElementById("init_reward").value;
	if(isNaN(init_reward)){
		alert('推广比例必须为数字1');
		return false;
	} 
    
	if(init_reward>1 || init_reward<0){
		alert("推广比例不得大于1或者小于0");
		$('#init_reward').val("");
		return false;
	}
	var reward_level = document.getElementById("reward_level").value;
	if(isNaN(reward_level)){
		alert('推广比例必须为数字');
		return false;
	}
	var init_reward_1 = document.getElementById("init_reward_1").value;
	if(isNaN(init_reward_1)){
		alert('奖励必须为数字');
		return false;
	}
	var init_reward_2 = document.getElementById("init_reward_2").value;
	if(isNaN(init_reward_2)){
		alert('奖励必须为数字');
		return false;
	}
	var init_reward_3 = document.getElementById("init_reward_3").value;
	if(isNaN(init_reward_3)){
		alert('奖励必须为数字');
		return false;
	}
	var d = parseFloat(init_reward_1)*100 + parseFloat(init_reward_2)*100+parseFloat(init_reward_3)*100;
		d = (d/100).toFixed(2);
	if(is_8shopdistr==1){
		var init_reward_4 = document.getElementById("init_reward_4").value;
		if(isNaN(init_reward_4)){
			alert('奖励必须为数字');
			return false;
		}
		var init_reward_5 = document.getElementById("init_reward_5").value;
		if(isNaN(init_reward_5)){
			alert('奖励必须为数字');
			return false;
		}
		var init_reward_6 = document.getElementById("init_reward_6").value;
		if(isNaN(init_reward_6)){
			alert('奖励必须为数字');
			return false;
		} 
		var init_reward_7 = document.getElementById("init_reward_7").value;
		if(isNaN(init_reward_7)){
			alert('奖励必须为数字');
			return false;
		} 
		var init_reward_8 = document.getElementById("init_reward_8").value;
		if(isNaN(init_reward_8)){
			alert('奖励必须为数字');
			return false;
		}
		var d = parseFloat(init_reward_1)*100 + parseFloat(init_reward_2)*100+parseFloat(init_reward_3)*100+parseFloat(init_reward_4)*100+parseFloat(init_reward_5)*100+parseFloat(init_reward_6)*100+parseFloat(init_reward_7)*100+parseFloat(init_reward_8)*100;
		d = (d/100).toFixed(2);
		// console.log(d);
		//alert(d);

	}
	if(d>1){
		alert('佣金总和不能超过1!');
		return false;
	}
	
	
	document.getElementById("upform").submit();
}
