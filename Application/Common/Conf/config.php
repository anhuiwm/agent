<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.uminicmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: www
// +----------------------------------------------------------------------
// | Created by: 2015-10-11 00:00:00
// +----------------------------------------------------------------------


//----------------------------------
// 基础配置文件
//----------------------------------
return array(
	//'配置项'=>'配置值'
	// 'LAYOUT_ON'=>true,
	'URL_MODEL' => '1',
	'VIEW_PATH'=>'./Public/',
	'STATIC_ROOT'=>'../Public/',
	'DEFAULT_MODULE'=>'Auth',

	// 入口路径
	'URL_INDEX'=>'index.php?s=',
	'TMPL_PARSE_STRING'  =>array(
		'__SHARE_VIEW__'=>'./Application/Public/View',
	),
	'DEFAULT_THEME' 	=> 'default',
	'SESSION_AUTO_START' => true,

	'LOAD_EXT_CONFIG' => 'db,data', //配置列表 各类数据 文件
    'CHARGE_RANGE' => array(
         11=>array("money"=>"6","name"=>"少量点券60"  ),
         12=>array("money"=>"30","name"=>"小堆点券300" ),
         13=>array("money"=>"98","name"=>"小袋点券980" ),
         14=>array("money"=>"198","name"=>"大袋点券1980"),
         15=>array("money"=>"328","name"=>"满箱点券3280"),
         16=>array("money"=>"648","name"=>"满柜点券6480"),
         41=>array("money"=>"28","name"=>"购买月卡"),
         51=>array("money"=>"1","name"=>"一元豪礼(活动用)")
    ),
        'DBS' =>array(
        '正式服'=>array(
            'DB_TYPE'=>'mysql',
            'DB_HOST'=>'rm-uf6mozrl6o240v725o.mysql.rds.aliyuncs.com',
            'DB_NAME'=>'fishgame',
            'DB_USER'=>'dumingqing',
            'DB_PWD'=>'!@#dmq1987',
            'DB_PORT'=>'3306',
            'url'=> "http://139.196.96.86:1680/pay_callback_lj.clkj",
            ),
        '测试服'=>array(
            'DB_TYPE'=>'mysql',
            'DB_HOST'=>'106.15.198.179',
            'DB_NAME'=>'fishgame',
            'DB_USER'=>'DingRuo321Fish',
            'DB_PWD'=>'!@#dmq1987.',
            'DB_PORT'=>'3306',
            'url'=> "http://106.15.198.179:1680/pay_callback_lj.clkj",
            ),
        '内网'=>array(
            'DB_TYPE'=>'mysql',
            'DB_HOST'=>'10.0.0.168',
            'DB_NAME'=>'fishgame',
            'DB_USER'=>'root',
            'DB_PWD'=>'root',
            'DB_PORT'=>'3306',
            'url'=> "http://10.0.0.168:1680/pay_callback_lj.clkj",
            ),

    )
);
