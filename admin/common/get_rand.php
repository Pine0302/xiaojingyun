<?php
/*
 * 使用方法：
 * require_once(ROOT_DIR . 'mshop/admin/common/get_rand.php');
 *
 * $rand_utility = new rand_utility();
 *
 * //传入参数奖项数组 $prize_arr ， 数组里面必须含有 奖项 id 奖项名字 name 奖项概率 probability
 * 
 * //返回中奖数组 $result["yes"] 不中奖数组 $result["no"]
 *
 * $result = $rand_utility->luck_draw($prize_arr);	
 *
 *
 *
 *
 */

class rand_utility
{
	function __construct() {

	}
	
		
	/*

	 * 经典的概率算法，

	 * $proArr是一个预先设置的数组，

	 * 假设数组为：array(100,200,300，400)，

	 * 开始是从1,1000 这个概率范围内筛选第一个数是否在他的出现概率范围之内，

	 * 如果不在，则将概率空间，也就是k的值减去刚刚的那个数字的概率空间，

	 * 在本例当中就是减去100，也就是说第二个数是在1，900这个范围内筛选的。

	 * 这样 筛选到最终，总会有一个数满足要求。

	 * 就相当于去一个箱子里摸东西，

	 * 第一个不是，第二个不是，第三个还不是，那最后一个一定是。

	 * 这个算法简单，而且效率非常 高，

	 */
	function get_rand($proArr) {

		$result = ''; 
		
		//概率数组的总概率精度
		$proSum = array_sum($proArr); 

		//概率数组循环
		foreach($proArr as  $key  => $proCur) {

			$randNum= mt_rand(1, $proSum);

			if($randNum<= $proCur) {

				$result= $key;
				break;

			} else{

				$proSum-= $proCur;

			}      

		}

		unset ($proArr); 

		return $result;

	}
	
	function luck_draw($prize_arr=array()){
		
		/*

		 * 奖项数组

		 * 是一个二维数组，记录了所有本次抽奖的奖项信息，

		 * 其中id表示中奖等级，name表示奖品，probability表示中奖概率。

		 * 注意其中的probability必须为整数，你可以将对应的 奖项的probability设置成0，即意味着该奖项抽中的几率是0，

		 * 数组中probability的总和（基数），基数越大越能体现概率的准确性。

		 * 本例中probability的总和为100，那么平板电脑对应的 中奖概率就是1%，

		 * 如果probability的总和是10000，那中奖概率就是万分之一了。

		 *

		 */
		 
		/*
		//测试奖项数组
		$prize_arr= array(

			'0'=> array('id'=>1,'name'=>'平板电脑','probability'=>1),

			'1'=> array('id'=>2,'name'=>'数码相机','probability'=>5),

			'2'=> array('id'=>3,'name'=>'音箱设备','probability'=>10),

			'3'=> array('id'=>4,'name'=>'4G优盘','probability'=>12),

			'4'=> array('id'=>5,'name'=>'10Q币','probability'=>22),

			'5'=> array('id'=>6,'name'=>'下次没准就能中哦','probability'=>50),
		);
		*/
		 

		/*

		 * 每次前端页面的请求，PHP循环奖项设置数组，

		 * 通过概率计算函数get_rand获取抽中的奖项id。

		 * 将中奖奖品保存在数组$res['yes']中，

		 * 而剩下的未中奖的信息保存在$res['no']中，

		 * 最后输出json个数数据给前端页面。

		 */

		foreach($prize_arr as  $key  => $val) {

			$arr[$val['id']] = $val['probability'];
			
		}

		$rid = $this->get_rand($arr); //根据概率获取奖项id

		 

		 foreach($prize_arr as  $key  => $val) {
			if( $val['id'] == $rid ){
				$res['yes'] = $prize_arr[$key];
				
				unset($prize_arr[$key]); //将中奖项从数组中剔除，剩下未中奖项
				
				shuffle($prize_arr); //打乱数组顺序
				
				$res['no'] = $prize_arr;
				
				break;
			}
			
			
		}

		return $res;
	
	}
	
	
}




?>
