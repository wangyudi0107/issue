<?php
namespace app\index\controller;
header("Content-Type:text/html; charset=utf-8");//防止输出乱码
use think\Controller;
class Test extends Controller{
	public function index(){
		return view();
	}
	
	public function bag(){
		// 接受金额和数量
		$money = input('money');
		$num = input('num');
		// 判断红包金额或者数量是否发完了
		if($money<=0||$num<=0){
			echo json_encode(array('status'=>(int)0,'红包剩余金额'=>$money,'剩余数量'=>$num));exit;
		}else{
			if($money/$num < 0.3){
				echo json_encode(array('status'=>(int)2,'msg'=>'每个红包不得小于0.3元'));exit;
			}else{
				$data = $this -> checked($money,$num);
				echo json_encode(array('status'=>(int)1,'抢到金额'=>$data['get_money'],'剩余金额'=>$data['money'],'剩余数量'=>$data['num'],'data'=>$data));exit;
			}
		}
	}
	
	public function checked($money,$num){
		// 如果只剩一个红包剩余钱都给这个人
		if($num==1){
			$data['get_money'] = $money;
			$data['money'] = $money - $data['get_money'];
		}else{
			// 设定最小值为30分
			$min = 30;
			// 最大值为平均值的2倍,保证红包沿着平均线正态分布
			$max = round(($money/$num)*2,2);
			// 通过随机数获取抽中金额(单位:/分)
			$get_money = mt_rand($min,$max*100-$min);//避免抢到最后有人没有金额，保留最小值到最后
			// 避免浮点运算,均在100倍下整型运算
			$data['get_money'] =  $get_money/100;
			$data['money'] = floor($money*100 - $get_money)/100;
		}
		$data['num'] = --$num;
		return $data;
	}
	
	public function sort(){
		$arr[] = 2;
		$arr[]=5;
		$arr[]=1;
		krsort($arr);
		dump($arr);
	}
	
	public function md(){
		$str = 'Wyd123456';
		$salt='_Random_NUM';
		dump($str.$salt);
		$str = md5($str.$salt);
		dump($str);
	}
	
}