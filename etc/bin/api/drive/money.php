<?php
//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//moeny驱动类
class money{
    private $var_temp;
    //递归检测金额是否存在重复
    public function garden($mysql,$land_id,$money,$n = null){
        $money_garden = $money + (mt_rand(1,99)/100);//需要递归查找的金额
        $money_query = $mysql->query("qrcode","land_id={$land_id} and money={$money_garden}");
        $this->var_temp = $this->var_temp + 1;
        //如果计数器递归达到99次,那么将中断本次交易
        if ($this->var_temp >= 99) return false;
        if (!is_array($money_query[0])){
            //检测订单中是否重复金额
            $moeny_takes = $mysql->query("takes","land_id={$land_id} and money={$money_garden} and state=1");
            if (!is_array($moeny_takes[0])){
                return $money_garden;//跳出递归
            }else{
                //延时递归
                $this->garden($mysql, $land_id, $money);//递归点
            }
        }else{
            //sleep(2);
            $this->garden($mysql, $land_id, $money);//递归点
        }
    }
}