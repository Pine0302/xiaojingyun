<?php

     /*
    版权信息:  秘密信息
    功能描述：云店奖励——店头背景查询
    开 发 者：zjj-v397
    开发日期： 2018-04-10
    重要说明：
     */
    public function select_setting_of_store($id){

        $sql = "SELECT yundian_bg FROM ".WSY_REBATE.".weixin_yundian_setting where id = ".$id."";
        $res = $this->db->getOne($sql);
        $upfileUrl = explode('|', $res);
        return $upfileUrl;
    } 

     /*
    版权信息:  秘密信息
    功能描述：云店奖励——店头背景
    开 发 者：zjj-v397
    开发日期： 2018-04-10
    重要说明：
     */
    public function setting_of_store($data){
        $upfileUrl = $data['pathArray'];
        $upfileUrl = implode('|', $upfileUrl);

        $sql = "UPDATE ".WSY_REBATE.".weixin_yundian_setting SET yundian_bg='".$upfileUrl."' where id = 1";
        $res = $this->db->query($sql);
        if($res){
            $result = array('errcode' => 1 ,'errmsg' => '修改成功');
        }else{
            $result = array('errcode' => 400 ,'errmsg' => '修改失败');
        }
        return $result;
    }

     /*
    版权信息:  秘密信息
    功能描述：云店奖励——店头背景
    开 发 者：zjj-v397
    开发日期： 2018-04-10
    重要说明：
     */
    public function description_select($id){
        $sql = "select name,now_price,description,storenum,default_imgurl from ".WSY_PROD.".weixin_commonshop_products where id='".$id."'";
        $result = $this->db->getRow($sql);
        return $result;
    }
