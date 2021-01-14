<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//moeny驱动类
class money {

    private $var_temp = 0;

    //递归检测金额是否存在重复
    public function garden($mysql, $land_id, $money, $n = null) {
        $this->var_temp = $this->var_temp + 1;
        $money_garden = $money + (mt_rand(1, 99) / 100); //需要递归查找的金额
        $money_query = $mysql->query("qrcode", "land_id={$land_id} and money={$money_garden}");
        //如果计数器递归达到99次,那么将中断本次交易
        if ($this->var_temp >= 99) {
            return false;
        }
        if (!is_array($money_query[0])) {
            //检测订单中是否重复金额
            $money_takes = $mysql->query("takes", "land_id={$land_id} and money={$money_garden} and state=1");
            if (!is_array($money_takes[0])) {
                $money_1 = $money_garden;
                return $money_1; //跳出递归
            } else {
                //延时递归
                $this->garden($mysql, $land_id, $money); //递归点
            }
        } else {
            //sleep(2);
            $this->garden($mysql, $land_id, $money); //递归点
        }
    }

    //随机减
    public function garden_desc($mysql, $land_id, $money, $n = null) {
        $this->var_temp = $this->var_temp + 1;
        $array = range(90, 99);
        foreach ($array as $val) {
            $temp_money = number_format(floatval($money - 1 + $val / 100), 2, ".", "");
            $new_array[] = $temp_money;
        }
        $money_takes = $mysql->query("takes", "land_id={$land_id} and money={$money} and state=1", "money");
        if (!is_array($money_takes[0])) {
            $length = count($new_array);
            $i = rand(0, $length - 1);
            $money_garden = $new_array[$i];
            return $money_garden; //跳出递归
        } else {
            $money_array = array_column($money_takes, 'money');
            $c = array_values(array_diff($new_array, $money_array));
            $length = count($c);
            if ($length >= 1) {
                $i = rand(0, $length - 1);
                $money_garden = $c[$i];
                return $money_garden; //跳出递归
            } else {
                return false;
            }
        }
    }

    //随机增加
    public function garden_randasc($mysql, $land_id, $money, $n = null) {
        $dir_path = $_SERVER['DOCUMENT_ROOT'] . "/temp";
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777, true);
        }
        $this->var_temp = $this->var_temp + 1;
        $array = range(1, 20);
        foreach ($array as $val) {
            $temp_money = number_format(floatval($money + $val / 100), 2, ".", "");
            $new_array[] = $temp_money;
        }
        $money_takes = $mysql->query("takes", "land_id={$land_id} and money={$money} and state=1", "money");
        if (!is_array($money_takes[0])) {
            $length = count($new_array);
            $i = rand(0, $length - 1);
            $money_garden = $new_array[$i];
            $last_money = file_get_contents($dir_path . "/tempG_" . $land_id . ".txt");
            if ($last_money != $money_garden) {
                file_put_contents($dir_path . "/tempG_" . $land_id . ".txt", $money_garden);
                return $money_garden; //跳出递归
            } else {
                $this->garden_randasc($mysql, $land_id, $money);
            }
        } else {
            $money_array = array_column($money_takes, 'money');
            $c = array_values(array_diff($new_array, $money_array));
            $length = count($c);
            if ($length >= 1) {
                $i = rand(0, $length - 1);
                $money_garden = $c[$i];
                $last_money = file_get_contents($dir_path . "/tempG_" . $land_id . ".txt");
                if ($last_money != $money_garden) {
                    file_put_contents($dir_path . "/tempG_" . $land_id . ".txt", $money_garden);
                    return $money_garden; //跳出递归
                } else {
                    $this->garden_randasc($mysql, $land_id, $money);
                }
            } else {
                return false;
            }
        }
    }

    //生成随机金额模式1
    public function garden_m1($mysql, $land_id, $money, $tempMoney) {
        $this->var_temp = $this->var_temp + 1;
        if ($money < 1) {
            return false;
        }
        if ($tempMoney >= 99) {
            $tempMoney = 80;
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/temp/tempD_" . $land_id . "_" . intval($money) . ".txt", $tempMoney);
        //$count_takes = $mysql->query("takes", "land_id={$land_id} and money_index={$money} and qr_type=5 and state=1");
        if ($this->var_temp >= 19) {
            return false;
        }
        //unset($count_takes);
        $money_garden = $money - 1 + ($tempMoney / 100);
        $money_takes = $mysql->query("takes", "land_id={$land_id} and money={$money_garden} and qr_type=5 and state=1");
        //if (!is_array($money_query[0])){
        //检测订单中是否重复金额        
        if (!is_array($money_takes[0])) {
            unset($money_takes);
            return $money_garden; //跳出递归
        } else {
            unset($money_takes);
            //延时递归
            if ($tempMoney == 99) {
                $tempMoney = 80;
            } else {
                $tempMoney += 1;
            }
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/temp/tempD_" . $land_id . "_" . intval($money) . ".txt", $tempMoney);
            $this->garden_m1($mysql, $land_id, $money, $tempMoney); //递归点
        }
        //$money_garden = $money - (mt_rand(1, 20) / 100); //需要递归查找的金额
        //$money_query = $mysql->query("qrcode","land_id={1} and money={$money_garden}");
        //}else{
        //sleep(2);
        //   $this->garden($mysql, $land_id, $money);//递归点
        //}
    }

    //生成随机金额模式1
    public function garden_asc($mysql, $land_id, $money, $tempMoney, $qr_type) {
        $this->var_temp = $this->var_temp + 1;
        if ($money < 1) {
            return false;
        }
        if ($tempMoney >= 99) {
            $tempMoney = 1;
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/temp/tempE_" . $land_id . "_" . intval($money) . ".txt", $tempMoney);
        //$count_takes = $mysql->query("takes", "land_id={$land_id} and money_index={$money} and qr_type=5 and state=1");
        if ($this->var_temp >= 99) {
            return false;
        }
        $money_garden = $money + ($tempMoney / 100);
        $money_takes = $mysql->query("takes", "land_id={$land_id} and money={$money_garden} and qr_type={$qr_type} and state=1");

        //if (!is_array($money_query[0])){
        //检测订单中是否重复金额        
        if (!is_array($money_takes[0])) {
            unset($money_takes);
            return $money_garden; //跳出递归
        } else {
            unset($money_takes);
            if ($tempMoney == 99) {
                $tempMoney = 1;
            } else {
                $tempMoney += 1;
            }
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/temp/tempE_" . $land_id . "_" . intval($money) . ".txt", $tempMoney);
            //延时递归
            $this->garden_asc($mysql, $land_id, $money, $tempMoney, $qr_type); //递归点
        }
    }

    //生成随机金额公共模式（云闪付）
    public function garden_public($mysql, $land_id, $money, $tempMoney) {
        $this->var_temp = $this->var_temp + 1;
        if ($money < 1) {
            return false;
        }
        if ($tempMoney >= 99) {
            $tempMoney = 1;
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/temp/temp_" . $land_id . "_" . intval($money) . ".txt", $tempMoney);
        //$count_takes = $mysql->query("takes", "land_id={$land_id} and money_index={$money} and qr_type=5 and state=1");
        if ($this->var_temp >= 99) {
            return false;
        }
        $money_garden = $money + ($tempMoney / 100);
        $money_takes = $mysql->query("takes", "land_id={$land_id} and money={$money_garden} and state=1");

        //if (!is_array($money_query[0])){
        //检测订单中是否重复金额        
        if (!is_array($money_takes[0])) {
            unset($money_takes);
            return $money_garden; //跳出递归
        } else {
            unset($money_takes);
            if ($tempMoney == 99) {
                $tempMoney = 1;
            } else {
                $tempMoney += 1;
            }
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/temp/temp_" . $land_id . "_" . intval($money) . ".txt", $tempMoney);
            //延时递归
            $this->garden_public($mysql, $land_id, $money, $tempMoney, $qr_type); //递归点
        }
    }

    //生成随机金额公共模式（E模式）
    public function garden_real($mysql, $land_id, $money, $tempMoney) {
        $this->var_temp = $this->var_temp + 1;
        if ($money < 1) {
            return false;
        }
        if ($tempMoney <= 80) {
            $tempMoney = 99;
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/temp/tempP_" . $land_id . "_" . intval($money) . ".txt", $tempMoney);
        //$count_takes = $mysql->query("takes", "land_id={$land_id} and money_index={$money} and qr_type=5 and state=1");
        if ($this->var_temp >= 20) {
            return false;
        }
        $money_garden = $money - 1 + ($tempMoney / 100);
        $money_takes = $mysql->query("takes", "land_id={$land_id} and money={$money_garden} and state=1");

        //if (!is_array($money_query[0])){
        //检测订单中是否重复金额        
        if (!is_array($money_takes[0])) {
            unset($money_takes);
            return $money_garden; //跳出递归
        } else {
            unset($money_takes);
            if ($tempMoney == 80) {
                $tempMoney = 99;
            } else {
                $tempMoney -= 1;
            }
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/temp/tempP_" . $land_id . "_" . intval($money) . ".txt", $tempMoney);
            //延时递归
            $this->garden_real($mysql, $land_id, $money, $tempMoney); //递归点
        }
    }

}
