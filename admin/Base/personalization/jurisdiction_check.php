<?php
    //查询拼团开关
    $pt_sql = "select count(1) as pt_is from columns as col inner join customer_funs as funs where col.sys_name='拼团活动' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $pt_is = 0;
    $pt_result = _mysql_query($pt_sql) or die('sql_pt failed: ' . mysql_error());
    while ($row = mysql_fetch_object($pt_result)) {
       $pt_is   = $row->pt_is;  
    }
    //查询拼团开关 End
    
    //查询客服开关
    $kf_sql = "select count(1) as kf_is from columns as col inner join customer_funs as funs where col.sys_name='在线客服' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $kf_is = 0;
    $kf_result = _mysql_query($kf_sql) or die('sql_kf failed: ' . mysql_error());
    while ($row = mysql_fetch_object($kf_result)) {
       $kf_is   = $row->kf_is;  
    }
    //查询客服开关 End
    
    //查询订货系统开关
    $dh_sql = "select count(1) as dh_is from columns as col inner join customer_funs as funs where col.sys_name='订货系统' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $dh_is = 0;
    $dh_result = _mysql_query($dh_sql) or die('sql_dh failed: ' . mysql_error());
    while ($row = mysql_fetch_object($dh_result)) {
       $dh_is   = $row->dh_is;  
    }
    //查询订货系统开关 End
    
    //查询f2c系统开关
    $f2c_sql = "select count(1) as f2c_is from columns as col inner join customer_funs as funs where col.sys_name='F2C系统' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $f2c_is = 0;
    $f2c_result = _mysql_query($f2c_sql) or die('sql_f2c failed: ' . mysql_error());
    while ($row = mysql_fetch_object($f2c_result)) {
       $f2c_is   = $row->f2c_is;  
    }
    //查询f2c系统开关 End

    //查询微店模式开关
    $wd_sql = "select count(1) as wd_is from columns as col inner join customer_funs as funs where col.sys_name='微店模式' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $wd_is = 0;
    $wd_result = _mysql_query($wd_sql) or die('sql_wd failed: ' . mysql_error());
    while ($row = mysql_fetch_object($wd_result)) {
       $wd_is   = $row->wd_is;  
    }
    //查询微店模式开关 End
    
    //查询自定义模板开关
    $diy_sql = "select count(1) as diy_is from columns as col inner join customer_funs as funs where col.sys_name='自定义模板' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $diy_is = 0;
    $diy_result = _mysql_query($diy_sql) or die('sql_diy failed: ' . mysql_error());
    while ($row = mysql_fetch_object($diy_result)) {
       $diy_is   = $row->diy_is;  
    }
    //查询自定义模板开关 End
    
    //查询大礼包开关
    $lb_sql = "select count(1) as lb_is from columns as col inner join customer_funs as funs where col.sys_name='升级大礼包' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $lb_is = 0;
    $lb_result = _mysql_query($lb_sql) or die('sql_lb failed: ' . mysql_error());
    while ($row = mysql_fetch_object($lb_result)) {
       $lb_is   = $row->lb_is;  
    }
    //查询大礼包开关 End
    
    //查询商圈美食开关
    $cater_sql = "select count(1) as cater_is from columns as col inner join customer_funs as funs where col.sys_name='商圈-美食' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $cater_is = 0;
    $cater_result = _mysql_query($cater_sql) or die('sql_cater failed: ' . mysql_error());
    while ($row = mysql_fetch_object($cater_result)) {
       $cater_is   = $row->cater_is;  
    }
    //查询商圈美食开关 End
    
    //查询商圈KTV开关
    $ktv_sql = "select count(1) as ktv_is from columns as col inner join customer_funs as funs where col.sys_name='商圈-ktv' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $ktv_is = 0;
    $ktv_result = _mysql_query($ktv_sql) or die('sql_ktv failed: ' . mysql_error());
    while ($row = mysql_fetch_object($ktv_result)) {
       $ktv_is   = $row->ktv_is;  
    }
    //查询商圈KTV开关 End
    
    //查询商圈酒店开关
    $hotel_sql = "select count(1) as hotel_is from columns as col inner join customer_funs as funs where col.sys_name='商圈-酒店' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $hotel_is = 0;
    $hotel_result = _mysql_query($hotel_sql) or die('sql_hotel failed: ' . mysql_error());
    while ($row = mysql_fetch_object($hotel_result)) {
       $hotel_is   = $row->hotel_is;  
    }
    //查询商圈酒店开关 End
    
    //查询线下商城开关
    $cityshop_sql = "select count(1) as cityshop_is from columns as col inner join customer_funs as funs where col.sys_name='商圈-线下商城' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $cityshop_is = 0;
    $cityshop_result = _mysql_query($cityshop_sql) or die('sql_cityshop failed: ' . mysql_error());
    while ($row = mysql_fetch_object($cityshop_result)) {
       $cityshop_is   = $row->cityshop_is;  
    }
    //查询线下商城开关 End
    
    //查询合作商开关
    $hzs_sql = "select count(1) as hzs_is from columns as col inner join customer_funs as funs where col.sys_name='商城供应商模式' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $hzs_is = 0;
    $hzs_result = _mysql_query($hzs_sql) or die('sql_hzs failed: ' . mysql_error());
    while ($row = mysql_fetch_object($hzs_result)) {
       $hzs_is   = $row->hzs_is;  
    }
    //查询合作商开关 End
    
    //查询区域商开关
    $qys_sql = "select count(1) as qys_is from columns as col inner join customer_funs as funs where col.sys_name='商城区域团队奖励' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $qys_is = 0;
    $qys_result = _mysql_query($qys_sql) or die('sql_qys failed: ' . mysql_error());
    while ($row = mysql_fetch_object($qys_result)) {
       $qys_is   = $row->qys_is;  
    }
    //查询区域商开关 End
    
    //查询会员卡开关
    $card_sql = "select count(1) as card_is from columns as col inner join customer_funs as funs where col.sys_name='微会员卡' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $card_is = 0;
    $card_result = _mysql_query($card_sql) or die('sql_card failed: ' . mysql_error());
    while ($row = mysql_fetch_object($card_result)) {
       $card_is   = $row->card_is;  
    }
    //查询会员卡开关 End
    
    //查询电商直播开关
    $broadcast_sql = "select count(1) as broadcast_is from columns as col inner join customer_funs as funs where col.sys_name='电商直播' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $broadcast_is = 0;
    $broadcast_result = _mysql_query($broadcast_sql) or die('sql_broadcast failed: ' . mysql_error());
    while ($row = mysql_fetch_object($broadcast_result)) {
       $broadcast_is   = $row->broadcast_is;  
    }
    //查询电商直播开关 End
    
    //查询积分签到开关
    $integral_sign_sql = "select count(1) as integral_sign_is from columns as col inner join customer_funs as funs where col.sys_name='积分签到' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $integral_sign_is = 0;
    $integral_sign_result = _mysql_query($integral_sign_sql) or die('sql_broadcast failed: ' . mysql_error());
    while ($row = mysql_fetch_object($integral_sign_result)) {
       $integral_sign_is   = $row->integral_sign_is;  
    }
    //查询积分签到开关 End    
    
    //查询积分商城开关
    $integral_shop_sql = "select count(1) as integral_shop_is from columns as col inner join customer_funs as funs where col.sys_name='积分商城' and col.isvalid=true and funs.column_id=col.id and funs.isvalid=true and funs.customer_id=".$customer_id;
    $integral_shop_is = 0;
    $integral_shop_result = _mysql_query($integral_shop_sql) or die('sql_broadcast failed: ' . mysql_error());
    while ($row = mysql_fetch_object($integral_shop_result)) {
       $integral_shop_is   = $row->integral_shop_is;  
    }
    //查询积分商城开关 End     
?>
<select name="column_id" id="column_id">
    <option value=-1>--请选择--</option>
    <option value=-2 <?php if($column_id==-2){ ?> selected <?php } ?>>自定义URL</option>
    <?php 
        $query = 'SELECT id,name,funs FROM page_column_t where isvalid=true and type='.$column_type;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $sub_id = $row->id;
            $name = $row->name;
            $funs = $row->funs;

            if ($funs == 'diy_template'){
                continue;
            }

            if ($funs == 'order_detail'){
                continue;
            }

            if ($funs == 'my_shop_reward'){
                continue;
            }
            
            //当栏目对应功能没有开启的时候，则不显示此选项
            if (!$kf_is && ($funs == 'customer_service')){
                continue;
            }

            if (!$pt_is && ($funs == 'popularity' || $funs == 'ordinary' || $funs == 'ordinary2' || $funs == 'ordinary3' || $funs == 'my_collages_record_list_view' || $funs == 'popularity_group' )){
                continue;
            }

            if (!$dh_is && ($funs == 'ordering_retail' || $funs == 'proxy_apply' || $funs == 'proxy_login' )){
                continue;
            }

            if (!$f2c_is && ($funs == 'f2c' )){
                continue;
            }

            if (!$wd_is && ($funs == 'my_microshop' )){
                continue;
            }

            if (!$lb_is && ($funs == 'package_list' )){
                continue;
            }

            if (!$cater_is && ($funs == 'cater' )){
                continue;
            }

            if (!$ktv_is && ($funs == 'ktv' )){
                continue;
            }

            if (!$hotel_is && ($funs == 'hotel' )){
                continue;
            }

            if (!$cityshop_is && ($funs == 'shop' || $funs == 'shop_list' )){
                continue;
            }

            if (!$hzs_is && ($funs == 'co_operative' )){
                continue;
            }

            if (!$qys_is && ($funs == 'wholesalers' )){
                continue;
            }

            if (!$card_is && ($funs == 'card' )){
                continue;
            }

            if (!$broadcast_is && ($funs == 'micro_broadcast' )){
                continue;
            }
            
            if (!$integral_sign_is && ($funs == 'integral_sign' )){
                continue;
            }
            
            if (!$integral_shop_is && ($funs == 'integral_shop' )){
                continue;
            }

            //当栏目对应功能没有开启的时候，则不显示此选项 end
            

    ?>
       <option value="<?php echo $sub_id; ?>" data-column="data_<?php echo $sub_id; ?>" data-funs="<?php echo $funs; ?>" <?php if($column_id==$sub_id){ ?> selected <?php } ?>><?php echo $name; ?></option>
    <?php } ?>  
</select>