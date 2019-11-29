<?php 
/**
 * @author Key 
 * 商品类
 */
class model_commonshop_products{
    var $db;

    function __construct($customer_id)
    {
        $this->customer_id = $customer_id;
        $this->db = DB::getInstance();
    }

    /**
     * [获取商品属性]
     * @param  [type] $ids [属性集合]
     * @param  [type] $pid [商品编号]
     * @return [type]      [description]
     */
    public function get_product_pro($ids,$pid){
        if( is_array($ids) ){
            $ids = implode(',', $ids);
        }
        $sql = "SELECT parent_id,id,name from weixin_commonshop_pros where isvalid=true  and id in({$ids}) ";
        $pid_arr2 = $this->db->getALL($sql);
        foreach ($pid_arr2 as $key => $value) {
            $pid_arr[] = $value['parent_id'];
        }
        $pids = implode(',', $pid_arr);

        if( $pids ){
            $ids_arr = explode(',', $ids);
            $sql = "SELECT id,name from weixin_commonshop_pros where isvalid=true and id in({$pids}) and customer_id='{$this->customer_id}' ";
            $pa_data = $this->db->getAll($sql);
            foreach ($pa_data as $key => $value) {
                $id = $value['id'];
                foreach ($pid_arr2 as $k => $val) {
                    if( in_array($val['id'],$ids_arr) && $id == $val['parent_id'] ){
                        $parent[] = $value;
                        $chi[$value['id']][] = $val;
                    }
                }
            }
            foreach ($chi as $key => $value) {
                foreach ($pid_arr2 as $key => $val) {
                    if( $key == $value['id'] ){
                        $pa_data = $val;
                        break;
                    }
                }
                $pa_data['chi'] = $value;
                $partents[] = $pa_data;
            }
        }

        // 批发属性
        $sql = "SELECT id,wholesale_parentid,wholesale_childid FROM  weixin_commonshop_product_extend WHERE isvalid=true AND customer_id='{$this->customer_id}' AND pid='{$pid}' LIMIT 1";
        $extend = $this->db->getRow($sql);
        if( $extend['id'] ){
            $is_wholesale = 1;//判断是否拥有批发属性
            $sql = "SELECT id,name FROM weixin_commonshop_pros WHERE isvalid=true AND id='{$extend['wholesale_parentid']}' AND parent_id=-1 AND is_wholesale=1 LIMIT 1";
            $extend_pro['parent'] = $this->db->getRow($sql);
            $extend_parent = $extend_pro['parent'];
        }
        if( $extend['wholesale_childid'] ){
            $parent_id = $extend['wholesale_parentid'];
            $wholesale_childid = str_replace('_', ',', $extend['wholesale_childid']);
            $query_child = "SELECT id,name,wholesale_num FROM weixin_commonshop_pros WHERE isvalid=true AND parent_id=$parent_id AND id in ({$wholesale_childid})";
            $extend_pro['children'] = $this->db->getAll($query_child);
            $par['chi'] = $extend_pro['children'];
            $extend_parent['chi'] = $par['chi'];
        }
        if( $extend_parent ){
            $partents[] = $extend_parent;
        }
        return $partents;
    }

    public function get_pro_name($ids=''){
        $sql = "SELECT id,name,parent_id from weixin_commonshop_pros where isvalid=true and customer_id='{$this->customer_id}' ";
        if( $ids != -1 ){
             $sql .= " and id in ({$ids}) ";
        }
        $data = $this->db->getAll($sql);
        // $data['sql'] = $sql;
        foreach ($data as $key => &$value) {
            $paid = $value['parent_id'];
            if( $paid ){
                $sql = "SELECT id,name from weixin_commonshop_pros where isvalid=true and id='{$paid}' ";
                $pa_data = $this->db->getRow($sql);
                $str = "{$str}{$pa_data['name']}:{$value['name']} ";
            }
        }
        return $str;
    }

    /*public function get_product_detail(){
        $sql = "SELECT "
    }*/
}
 ?>