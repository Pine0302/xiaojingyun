<?php
/**
 * User: chy
 * Date: 2018/5/10
 * Time: 11:15
 * Explain: 需求统一接口要求类
 */



interface HyBaseInterface
{
     /*
     * 后台获取配置
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function admin_get();
    /*
     * 后台保存配置
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function admin_save();
    /*
     * 后台删除配置
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function admin_del();
    /*
     * 前台获取配置
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_setting();
    /*
     * 前台获取额外数据
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_get();
    /*
     * 前台算法
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_cal();
    /*
     * 前台主要业务
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_main();
    /*
     * 前台设计的表操作
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_sql();
    /*
     * 前台设计的日志
     * @xxx：
     * @xxx：
     * @return：返回结果
     */
    public function busses_log($str);
}