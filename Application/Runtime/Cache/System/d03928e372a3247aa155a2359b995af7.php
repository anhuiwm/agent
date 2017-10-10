<?php if (!defined('THINK_PATH')) exit();?>﻿<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    

    <title>321捕鱼后台管理中心:
        
    </title>
    <link href="/Public/boot/css/bootstrap.min.css" rel="stylesheet">
    <link href="/Public/<?php echo C(DEFAULT_THEME);?>/Static/css/base.css" rel="stylesheet" type="text/css" media="screen">

    <link href="/Public/<?php echo C(DEFAULT_THEME);?>/Static/css/myboot.css" rel="stylesheet" type="text/css" media="screen">
    <link rel="stylesheet" type="text/css" href="/Public/plug/datetimepicker/jquery.datetimepicker.css" />
    <link href="/Public/icon/iconfont.css" rel="stylesheet">


    <script src="/Public/boot/js/jquery.js"></script>

    <script src="/Public/plug/datetimepicker/build/jquery.datetimepicker.full.js"></script>
    <script src="/Public/plug/My97DatePicker/WdatePicker.js"></script>

    <script src="/Public/plug/datetimepicker/build/jquery.datetimepicker.full.js"></script>
    <script src="/Public/plug/bsdatetimepicker/build/jquery.datetimepicker.full.js"></script>


    <link href="/Public/plug/bsdatetimepicker/js/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <script src="/Public/plug/bsdatetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script src="/Public/plug/bsdatetimepicker/js/locales/bootstrap-datetimepicker.fr.js"></script>

    <script>
        $.datetimepicker.setLocale('zh');
    </script>

    <script type="text/javascript">
        //时间选择
        function selecttime(flag) {
            if (flag == 1) {
                var endTime = $("#countTimeend").val();
                if (endTime != "") {
                    WdatePicker({
                        dateFmt: 'yyyy-MM-dd HH:mm',
                        maxDate: endTime
                    });
                } else {
                    WdatePicker({
                        dateFmt: 'yyyy-MM-dd HH:mm'
                    });
                }
            } else {
                var startTime = $("#countTimestart").val();
                if (startTime != "") {
                    WdatePicker({
                        dateFmt: 'yyyy-MM-dd HH:mm',
                        minDate: startTime
                    });
                } else {
                    WdatePicker({
                        dateFmt: 'yyyy-MM-dd HH:mm'
                    });
                }
            }
        }


        //时间选择
        function selectdate(flag) {
            if (flag == 1) {
                var endTime = $("#enddate").val();
                if (endTime != "") {
                    WdatePicker({
                        dateFmt: 'yyyy-M-d',
                        maxDate: endTime
                    });
                } else {
                    WdatePicker({
                        dateFmt: 'yyyy-M-d'
                    });
                }
            } else {
                var startTime = $("#startdate").val();
                if (startTime != "") {
                    WdatePicker({
                        dateFmt: 'yyyy-M-d',
                        minDate: startTime
                    });
                } else {
                    WdatePicker({
                        dateFmt: 'yyyy-M-d '
                    });
                }
            }
        }

        function selectonetime(flag) {
            WdatePicker({
                dateFmt: 'yyyy-MM-dd HH:mm'
            });
        }

        function selectdaytime(flag) {
            WdatePicker({
                dateFmt: 'HH:mm:ss'
            });
        }
    </script>

    <?php
 if(strpos($HTTP_SERVER_VARS[HTTP_USER_AGENT], "MSIE 8.0")) { echo ' <link href="/Public/<?php echo C(DEFAULT_THEME);?>/Static/css/ie.css" rel="stylesheet" type="text/css" media="screen">'; } ?>
        
</head>

<body>



    <!-- 头部功能菜单 -->
    <div class="top">
        
    <!-- 必要时，可以替换header -->


    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
                <a class="navbar-brand" href="#">321捕鱼后台管理中心</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">

                    <?php if($PUB_TOPMENU_LIST != 0): if(is_array($PUB_TOPMENU_LIST)): foreach($PUB_TOPMENU_LIST as $key=>$row): if($row['url'] != '0'): ?><li>
                                    <a class="model_name" href="<?php echo ($row['url']); ?>">
                                        <span class="<?php echo ($row['img']); ?>" aria-hidden="true"></span> &nbsp;&nbsp;<?php echo ($row['title']); ?>
                                    </a>
                                </li>
                                <?php else: ?>
                                <li>
                                    <a class="model_name" href="<?php echo U($row['node_name'].'/Index/index');?>">
                                        <span class="<?php echo ($row['img']); ?>" aria-hidden="true"></span> &nbsp;&nbsp;<?php echo ($row['title']); ?>
                                    </a>
                                </li><?php endif; endforeach; endif; endif; ?>
                </ul>
                <!--<ul class="nav navbar-nav">
                    <li class="dropdown">
                        选择服务器:
                        <select class="model_name">
                            <?php if(is_array($dbs)): foreach($dbs as $key=>$row): ?><option value=<?php echo ($key); ?>><?php echo ($key); ?></option>
                                <?php if($key == $db): ?><option value=<?php echo ($key); ?> selected="selected" ><?php echo ($key); ?></option>
                                    <?php else: ?>
                                    <option value=<?php echo ($key); ?>><?php echo ($key); ?></option><?php endif; endforeach; endif; ?>
                        </select>
                    </li>
                </ul>-->
 
                <ul class="nav navbar-nav navbar-mid header-tools">
                    <li><a href="#"></a></li>
                </ul>

                <ul class="nav navbar-nav navbar-mid header-tools">
                    <li><a href="#">当前服:<?php echo ($db); ?></a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">选择服务器<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <?php if(is_array($dbs)): foreach($dbs as $key=>$row): ?><li><a href="<?php echo U('GmTool/Command/set_db',array('key'=>$key));?>"><?php echo ($key); ?></a></li><?php endforeach; endif; ?>
                        </ul>
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right header-tools">
                    <li><img src="/Public/default/Static/images/avatar2.jpg" class="portrait"></li>
                    <!--<li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">服务器:<?php echo ($db); ?><span class="caret"></span></a>
                    </li>-->

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo ($_SESSION['user']['username']); ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">修改个人资料</a></li>
                            <li><a href="#">修改密码</a></li>
                        </ul>
                    </li>
                    <li><a href="<?php echo U('Auth/Index/logout');?>">注销系统</a></li>
                </ul>

            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>




    </div>


    <div class="row top50">
        <div class="col-md-2 col-sm-3 col-xs-12">
            <div class="sleft">
                <div class="menu">
                    
                        <!-- 侧栏列表 -->
                        <div class="panel-group" id="sec_menu" role="tablist" aria-multiselectable="true">
 <?php if(is_array($SEC_MENU)): foreach($SEC_MENU as $key=>$row): ?><div class="panel panel-default">
    <div class="panel-heading" role="tab" id="sec_menu_<?php echo ($row['id']); ?>">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#sec_menu" href="#link_sec_menu_<?php echo ($row['id']); ?>" aria-expanded="false" aria-controls="#link_sec_menu_<?php echo ($row['id']); ?>">
          <span class="<?php echo ($row['img']); ?>" aria-hidden="true"></span>
            &nbsp;&nbsp;<?php echo ($row['title']); ?>
        </a>
      </h4>
    </div>
    <?php if($row['node_name'] == $PUB_URL_NODE): ?><div id="link_sec_menu_<?php echo ($row['id']); ?>" class="panel-collapse collapse  in" role="tabpanel" aria-labelledby="sec_menu_<?php echo ($row['id']); ?>">
    <?php else: ?>
      <div id="link_sec_menu_<?php echo ($row['id']); ?>" class="panel-collapse collapse " role="tabpanel" aria-labelledby="sec_menu_<?php echo ($row['id']); ?>"><?php endif; ?>
      <div class="panel-body">
          <?php $next_menu=get_menu(array('type'=>'2','pid'=>$row['id'])) ?>
          <?php if(is_array($next_menu)): foreach($next_menu as $key=>$row1): if($row1['node_name'] == $PUB_NODE): ?><a type="button" class="link next_active" href="<?php echo U($row1['node_name']);?>">
            <?php else: ?>
              <a type="button" class="link" href="<?php echo U($row1['node_name']);?>"><?php endif; ?>
              <span class="<?php echo ($row1['img']); ?>" aria-hidden="true"></span>
              &nbsp;&nbsp;<?php echo ($row1['title']); ?>
            </a><?php endforeach; endif; ?>
      </div>
    </div>
  </div>
  </if><?php endforeach; endif; ?>
</div>

                    
                </div>
            </div>
        </div>
        <div class="col-md-10 col-sm-9 col-xs-12 sright">

            <div class="main">
                <!-- 右侧标题区 [必须]-->
                <div class="main_map">
                    
                        <ol class="breadcrumb">
                            <?php echo ($path_nav); ?>
                            <?php if($back_url != false): ?><li style="float:right"><a href="<?php echo ($back_url); ?>" class="btn btn-success btn-xs">返回上页</a></li><?php endif; ?>

                        </ol>
                    
                </div>

                <div class="main_body">
                    <!-- 工具条【可选】 -->
                    

                    <!-- 主要内容区域【可选】 -->
                    
<form class="form-horizontal" action="" method="POST">
<div class='fms'>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">基本配置</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home">
    <!-- ===============上====================== -->
      
	<div class="form-group">
		<label class="col-sm-2 control-label">组名称</label>
		<div class="col-sm-9">
			<input type="hidden" name="id" value="<?php echo ($data['id']); ?>">
			<input type="text" class="form-control" id="id" name="title" placeholder="组名称" value="<?php echo ($data['title']); ?>">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label">状态</label>
		<div class="col-sm-9">
			<select class="form-control" name="status">
				<?php if($data['status'] == 1): ?><option value="1" selected="selected">启用</option>
				<option value="0">禁用</option>		
				<?php else: ?>
				<option value="1">启用</option>
				<option value="0" selected="selected">禁用</option><?php endif; ?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label">节点</label>
		<div class="col-sm-9">
<!-- ==========================授权======================================== -->
			<!-- 节点 -->
<table class="table table-bordered datalist table-hover">
  <thead>
    <tr>
		<th>
			节点
		</th>

		<th width=50 style="text-align:center;">
			授权
		</th>
    </tr>
  </thead>
  <tbody>
  	<?php if(is_array($nav_menu)): foreach($nav_menu as $key=>$row): ?><tr style="background:#d9edf7">
			<td>
				<label class="control-label">
			 		<?php echo ($row['title']); ?>【<?php echo ($row['node_name']); ?>】
			 	</label>
			</td>

			

			<td>
				<label class="control-label">
					<input type="checkbox" class="node_one" <?php echo (is_checked($row['id'],$data['rules'])); ?> name="rules[]" value="<?php echo ($row['id']); ?>">
				</label>
			</td>
        </tr>
        <?php $controller_menu=get_node_auth(array('pid='.$row['id']));?>
        <?php if($controller_menu != 0): if(is_array($controller_menu)): foreach($controller_menu as $key1=>$row1): ?><tr>
					<td>
						<label class="control-label">
			 			<span style="font-family: fantasy;">|---&nbsp;</span><?php echo ($row1['title']); ?>【<?php echo ($row1['node_name']); ?>】
			 			</label>
					</td>
					
					<td>
					<label class="control-label"><input type="checkbox"  class="node_two" name="rules[]" <?php echo (is_checked($row1['id'],$data['rules'])); ?> value="<?php echo ($row1['id']); ?>"></label>
					</td>
		        </tr>

		        <?php $action_menu=get_node_auth(array('pid='.$row1['id']));?>
		        <?php if($action_menu != 0): if(is_array($action_menu)): foreach($action_menu as $key1=>$row2): ?><tr>
							<td>
								<label class="control-label">
							 	<span style="font-family: fantasy;">|-----|---&nbsp;</span><?php echo ($row2['title']); ?>【<?php echo ($row2['node_name']); ?>】
							 	</label>
							</td>
							<td>
								<label class="control-label">
									<input type="checkbox"  name="rules[]" <?php echo (is_checked($row2['id'],$data['rules'])); ?> class="node_three" value="<?php echo ($row2['id']); ?>">
								</label>
								
							</td>
				        </tr>


				        <?php $actions=get_node_auth(array('pid='.$row2['id']));?>
				        <?php if($action_menu != 0): if(is_array($actions)): $key3 = 0; $__LIST__ = $actions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row3): $mod = ($key3 % 2 );++$key3;?><tr>
									<td style="color:#ABABAB">
										<label class="control-label">
									 	<span style="font-family: fantasy;">|-----|-----|---&nbsp;</span><?php echo ($row3['title']); ?>【<?php echo ($row3['node_name']); ?>】
									 	</label>
									</td>
									<td>
										<label class="control-label">
											<input type="checkbox"  name="rules[]" <?php echo (is_checked($row3['id'],$data['rules'])); ?> class="node_four" value="<?php echo ($row3['id']); ?>">
										</label>
									</td>
						        </tr><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; endif; endforeach; endif; endif; endforeach; endif; ?>
  </tbody>
</table>
   <!-- ============end节点================ -->
<!-- ==========================授权===================================== -->
			

		</div>
	</div>


    <!-- ===============下====================== -->
    </div>
  </div>

</div><!--fms-->

  
  <div class="form-group form_sub">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="submit" name="submit" value="提 交" class="btn btn-default">
    </div>
  </div>
<!-- 基本配置 -->
</form>




<!-- 这里可以添加帮助控件 -->


                </div>
                <!-- main_body -->
            </div>
        </div>
    </div>



    
        
            <div class="footer">
版权归321捕鱼开发团队所有
</div>



<script src="/Public/boot/js/bootstrap.min.js"></script>

<script src="/Public/plug/datetime/bootstrap-datetimepicker.js" ></script>
<script src="/Public/plug/datetime/bootstrap-datetimepicker.zh-CN.js"></script>
<script>
$(".datetimepicker").datetimepicker({
    language:  "zh-CN",
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    forceParse: 0,
    showMeridian: 1
});

$(".datepicker").datetimepicker({
    language:  "zh-CN",
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    forceParse: 0,
    showMeridian: 1
});
</script>

<script src="/Public/default/Static/js/my.js"></script>



<!-- <div id="ld" style="position:absolute; left:0px; top:0px; width:100%; height:100%; background-color:#FFFFFF;opacity:0.5; z-index:1000;">
<div id="center" style="position:absolute;"> </div>
</div>  -->



</body>
</html>

        
    


    
    <?php if($_SESSION['act_info'] != false): ?><div class="alt_msg">
            <div class="show_msg">
                <?php
 echo $_SESSION['act_info']; unset($_SESSION['act_info']); ?>
            </div>
        </div>
        <script type="text/javascript">
            var intimer = setInterval(function() { //开启定时器
                {
                    $(".alt_msg").fadeOut(500);
                    clearInterval(intimer); //清除定时器
                }
            }, 2000);
        </script><?php endif; ?>
</body>

</html>