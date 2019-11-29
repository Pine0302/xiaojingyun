<?php
/*
* 获取产品数据，获取单件产品属性
* $Author:  $
* 2017-8-23  $
*/

/*

*/

class model_common{
	var $db;

	function __construct() {
        $this->db = DB::getInstance();
		//var_dump($this->db);
    }

    /*
	 * djy
	 * 获取产品数据
    */
	function get_products($parm=array()){
        $customer_id  = $parm['cust_id'];
        $search_key = $parm['search_key'];
        
        $search_pid   = $search_key['product_id'];
        $search_pname = $search_key['product_name'];
        $search_ptype = $search_key['type_id'];
        $page   = $parm['page'];
        if($page < 1){
            $page = 1;
        }
        $count     = $parm['page_size'];
        $field        = $parm['field'];
        
        $where = " customer_id='$customer_id' AND isvalid=true AND isout=false ";
        
        
        if( !empty($search_pname) ){
			$where .= " AND name like '%".$search_pname."%'";
		}
		if( $search_pid > 0 ){
			$where .= " AND id=".$search_pid;
		}
		if( $search_ptype > 0 ){
			$typeson_id=array();
			/* 查找该分类的所有子分类 start */
			$sqltype = "SELECT id FROM weixin_commonshop_types WHERE customer_id='$customer_id' AND isvalid=true AND is_shelves=1 AND LOCATE(',".$search_ptype.",', gflag)>0 ";
            $product_types = $this->db->getAll($sqltype);
            foreach($product_types as $key => $value ){
                $child_id = $value['id'];
				
				$typeson_id[] = $child_id;
            }
			/* 查找该分类的所有子分类 end */
			
 			if(empty($typeson_id)){
				$typeson_id = $search_ptype; 
			}else{
				array_push($typeson_id,$search_ptype);
				$typeson_id = implode(',',$typeson_id);
			}
			
			$where .= " and (";
			$typeson_id_arr = explode(",",$typeson_id);
			$typeson_id_count = count($typeson_id_arr);
			for( $j=0; $j<$typeson_id_count; $j++ ){
				$o_typeid = $typeson_id_arr[$j];
				if( $j == 0 ){
					$where .= "( LOCATE(',".$o_typeid.",', type_ids)>0)";
				}else{
					$where .= " or (LOCATE(',".$o_typeid.",', type_ids)>0)";
				}
			}
			$where .= ")";
		}
        
        $sql = "SELECT $field FROM weixin_commonshop_products WHERE $where and id not in( select product_id from ".WSY_SHOP.".integral_setting_product iap where cust_id = ".$customer_id." and type=0  and pros_id =  -1 and isvalid = 1)";
        $count_sql = "SELECT count(id) as total FROM weixin_commonshop_products WHERE $where and id not in( select product_id from ".WSY_SHOP.".integral_setting_product where cust_id = ".$customer_id." and type=0 and pros_id =  -1 and isvalid = 1)";
        $all     = $this->db->getRow($count_sql)['total'];
        if($count != ''){
            $page_count   = ceil($all/$count);
        }else{
            $page_count   = ceil($all/20);
        }
        
        
        $sql .= " ORDER BY asort_value DESC,id DESC ";
		if( $page != '' && $count != '' ){
			$sql .= " LIMIT ".($page-1)*$count.",".$count;
		}else{
            $sql .= " LIMIT 0,20";
        }
        //echo $sql;
        $result = $this->db->getAll($sql);
        $list_num = count($result);
        
        foreach ( $result as $k => $v ) {
			$type_name = '';
			$type_ids = trim($v['type_ids'],',');
			if( $type_ids != '' ){
				$query_type = "SELECT name FROM weixin_commonshop_types WHERE customer_id=".$customer_id." AND isvalid=true AND id IN (".$type_ids.")";
                $result_type = $this->db->getAll($query_type);
                foreach ( $result_type as $k2 => $v2 ) {
                    $type_name .= $v2['name']."/";
                }
				$type_name = substr($type_name,0,-1);
				
			}
			$result[$k]['type_name'] = $type_name;
		}
        $list = $result;
        $result != false ? $res = array('errcode' => 0,'errmsg'=>'获取成功','datas'=>array('list'=>$list,'page_count'=>$page_count,'total'=>$all,'list_num'=>$list_num )) : $res = array('errcode' => 400,'errmsg'=>'获取失败');
    	return $res;
	}
    
    /*
	 * djy
	 * 获取单件产品属性
    */
	function get_product_pros($parm=array()){
        $pid  = $parm['pid'];
        $customer_id  = $parm['customer_id'];
        
        $product_toppros = array();//产品顶级属性数组
        $product_subpros = array();//产品子属性数组
        
        $sql = "SELECT propertyids FROM weixin_commonshop_products where id='$pid'";
        $propertyids = $this->db->getOne($sql);
        
        /*属性开始*/
        $proLst = new ArrayList();

        $propertyarr = explode("_",$propertyids);
        $pcount = count($propertyarr);
        for($i=0;$i<$pcount;$i++){
           $property_id = $propertyarr[$i];
           $proLst->Add($property_id);
        }
        $default_pids = "";
        $proHash = new HashTable();
        //var_dump($proLst);
        /*属性结束*/
        
         $sql2="select id ,name from weixin_commonshop_pros where  parent_id=-1 and isvalid=true and customer_id='$customer_id'";
         $allpros = $this->db->getAll($sql2);
         foreach($allpros as $key => $value ){
             $prname = $value['name'];
             $prid = $value['id'];
             $product_toppros[$key] = $value;
             $ishasSet_t = false;
             
             
             $sql3="select id,name from weixin_commonshop_pros where isvalid=true and parent_id='$prid'";
             $subpros = $this->db->getAll($sql3);
            // var_dump($subpros);
             foreach($subpros as $key2 => $value2 ){
                 $subname = $value2['name'];
                 $subid = $value2['id'];
                 
                 if($proLst->Contains($subid) and !empty($subname)){
                    $ishasSet_t = true;
                    $product_subpros[$prid][] = $value2;

                 }
                 
             }
             if(!$ishasSet_t){
                unset($product_toppros[$key]);
            }
             
             
         }
       
        
        
        return array('product_toppros'=>$product_toppros,'product_subpros'=>$product_subpros);
	}

	/***
	 * 功能描述:查看后台主题颜色
	 * @param customer_id  商家ID(int)
	 * @return $theme 	   颜色(string)
	 * @author:  lqh
	 * 2017-10-12 
	 */
	function find_theme($customer_id){
		//获取主题颜色
		$query = 'SELECT theme FROM customers where isvalid=true and id='.$customer_id;
		$result = $this->db->getRow($query);
		if(!empty($result['theme'])){
			$theme=$result['theme'];
		}else{
			$theme="blue";
		}
		return $theme;
	}
}
