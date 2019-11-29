<?php
/**
 * User: chy
 * Date: 2018/5/10
 * Time: 14:32
 * Explain: 公共方法类
 */

 

//通过此类把公共的方法拓展到引用类里面去，直接用 $this->方法()即可
trait HyComfun
{
    function log(){
        echo 'This is log<br>';
    }
}