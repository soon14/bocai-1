<!DOCTYPE HTML>
<html>
<head>
	<title><?=$web_site['web_name']?></title>
	<meta charset="utf-8">
	<meta http-equiv="Cache-control" content="max-age=1700">
<meta name="viewport" content="user-scalable=no, width=device-width">
<meta name="MobileOptimized" content="320">
	<meta name="viewport" content="width=device-width,user-scalable=no,target-densitydpi=medium-dpi" />
	<script src="js/jquery-1.10.1.min.js" type="text/javascript"></script>
	<script>
		if( window != window.top ){
			window.top.location.href = window.location.href ;
		}
		var ClientW = $(window).width();
		$('html').css('fontSize',ClientW/3+'px');

		$(window).resize(function(){
			var ClientW = $(window).width();
			$('html').css('fontSize',ClientW/3+'px');
		});
	</script>
	<script src="js/index.js" type="text/javascript"></script>
	<link href="css/index.css" rel="stylesheet" type="text/css">
	<link href="css/lanrenzhijia.css" type="text/css" rel="stylesheet" />
</head>
<body>
	<article class="mainBox">
		
			

<?php

session_start();
$uid=isset($_SESSION['uid'])? $_SESSION['uid']:'';
if($uid){
	include_once('include/address.mem.php');
	include_once('include/config.php');
	include_once('include/function.php');
	check_login(); //验证用户是否已登陆
	$userid=intval($_SESSION["userid"]);
	$sql	=	"select money from user_list where user_id='".$userid."' limit 0,1";
	$query	=	$mysqli->query($sql);
	$row	=	$query->fetch_array();
?>
<header id="headerBox">
		<img class="logo" src="img/logo.png" /> 
		<span class="languang"></span>
		<a href="logout.php">退出</a>
</header>
<nav id="user">
	<div>
		<em></em>
		<span class="login" href="">账号：<?=$_SESSION["username"]?></span>
		<span class="account" href="">金额：<?=double_format($row['money'])?></span>
	</div>
	<div><!-- history_account.php --> <!-- menus.php -->
		<a class="his_money" href="/Member.php" title="账户历史">账户历史</a>
		<a class="his_money" href="/Member.php" title="交易状况">交易状况</a>
		<span class="move_top"></span>
	</div>	
</nav>
<!-- 代码end -->


<!-- js调用部分begin -->
<script src="js/jquery.min.js"></script>
<script src="js/jquery.flexslider-min.js"></script>
<script>
$(function(){
	$('.flexslider').flexslider({
		directionNav: true,
		pauseOnAction: false
	});
});
</script>
<!-- js调用部分end -->

<?php  }else{  ?>
<header id="headerBox">
			<img class="logo" src="img/logo.png" /> 
			<span class="languang">
				<!-- <ul class="lang_box">
				<li>
					<div>
						<span></span>
						简体中文
					</div>
				</li>
				<li>
					<div>
						<span></span>
						 &nbsp;English
					</div>
				</li>
							</ul> -->
			</span>
</header>
<!-- <nav id="user">
	<div>
		<a class="login" href="login/login.php">登录</a>
		<a class="account" href="register/register.php">免费开户</a>
	</div>
</nav> -->
<!-- 代码end -->


<!-- js调用部分begin -->
<!-- <script src="js/jquery.min.js"></script>
<script src="js/jquery.flexslider-min.js"></script>
<script>
$(function(){
	$('.flexslider').flexslider({
		directionNav: true,
		pauseOnAction: false
	});
});
</script> -->
<!-- js调用部分end -->

</div>
<?php
}
?>



	
			<section id="picList">
		<div id="ivo" style="width:100%;min-height:1.2rem;"> 
				<!--<div class="flexslider">
				<ul class="slides">
					<li style="background:url(images/img1.jpg) center no-repeat;"></li>
					<li style="background:url(images/img2.jpg) center no-repeat;"></li>
					<li style="background:url(images/img3.jpg) center no-repeat;"></li>
				</ul>
			</div> -->
				<!--a href="/login/deposit.php"-->
				<a href="/cl/index.php?module=MACenterView&method=bankATM1"><!--/cl/pages/bankM.php -->
					<img src="/img/deposit.png" alt="" title="" />
					<h2>存款</h2>
				</a>
				<a href="<?php if($uid){echo 'wapcl/?module=MACenterView&other=bankTake';}else{echo 'live/ag.php';}?>">
					<img src="/img/draw.png" alt="" title="" />
					<h2>取款</h2>
				</a>
				<a href="<?php if($uid){echo 'wapcl/?module=MACenterView&other=moneyView';}else{echo 'live/bb.php';}?>">
					<img src="/img/draw.png" alt="" title="" />
					<h2>额度转换</h2>
				</a>
				<a href="<?php if($uid){echo 'live/bbin.php';}else{echo 'login/login.php';}?>">
					<img src="/img/sys04.png" alt="" title="" />
					<h2>彩票游戏</h2>
				</a>
				<a href="<?php if($uid){echo 'live/bbin.php';}else{echo 'login/login.php';}?>">
					<img src="/img/game1.png" alt="" title="" />
					<h2>AG真人</h2>
				</a>
				<a href="<?php if($uid){echo 'live/bbin.php';}else{echo 'login/login.php';}?>">
					<img src="/img/zhenren.png" alt="" title="" />
					<h2>BBIN真人</h2>
				</a>
				<!--<a href="/main.php?3">
					<img src="/img/game3.png" alt="" title="" />
					<h2>香港六合彩</h2>
				</a> -->
				<a href="<?php if($uid){echo 'live/bbin.php';}else{echo 'login/login.php';}?>">
					<img src="/img/game2.png" alt="" title="" />
					<h2>体育赛事</h2>
				</a>
				
				<!-- <a href="javascript:;">
					<img src="img/sys04.png" alt="" title="" />
					<h2>交易平台</h2>
				</a> -->
				<a href="Member.php">
					<img src="img/sys04.png" alt="" title="" />
					<h2>会员中心</h2>
				</a>
				<a href="Contact .php">
					<img src="img/contact.png" alt="" title="" />
					<h2>联系我们</h2>
				</a>
				<!-- <a href="regard.php">
					<img src="img/zhenren.png" alt="" title="" />
					<h2>关于我们</h2>
				</a> -->
			</div>
			</section>
			<footer id="footer">
				<a href="/register/register.php">免费开户</a>
				<!--<a href="/newag2/ed5.php">额度转换</a> -->
				<a href="http://www.yl00853.com">电脑版</a> 
				<a href="/login/login.php">登录</a>
			</footer>
			<div class="zhenzao"></div>
		</article>
	</body>
	<script type="text/javascript">
		//下拉菜单
		(function(){
			if($('#user div:first em').size()){
				var aBtn = $('#user div:first em') ;
				var aBox = $('#user') ;
				var aH = aBox.outerHeight() ;
				var onOff = false;

				aBtn.on('touchstart',show);

				function show(){
					onOff = !onOff;
					if(onOff){
						$('.zhenzao').css({'position':'absolute','left':0,'top':0,'width':'100%','height':'100%','zIndex':50,'background':'rgba(0,0,0,0.5)'});
						$('#user').css('background','rgba(63, 58, 36, 0.6)');
						aBox.height(aH + aBox.find('div').eq(1).outerHeight(true));

					}else{
						$('.zhenzao').css({'position':'','width':0,'height':0,'background':''});
						$('#user').css('background','rgba(0,0,0,0.6)');
						aBox.height(aH);
					}

					$('.zhenzao').on('touchmove',fade);

					function fade(){
						$('#picList').off('touchmove');
						onOff = false;
						$('#user').css('background','rgba(0,0,0,0.6)');
						aBox.height(aH);
						setTimeout(function(){
							$('.zhenzao').css({'position':'','width':0,'height':0,'background':''});
						},500);
						
					}

				}
			}
		})();

		//languang
		(function(){
			$('.languang').on('touchend',createList);

			var onOff = false;
			function createList(){
				onOff = !onOff;
				var longBox = $('<ul class="lang_box"><li><div><span></span>简体中文</div></li><li><div><span></span>&nbsp;English</div></li></ul>');
				if(onOff){
					$('.languang').append(longBox);
					setTimeout(function(){
						$('.lang_box').css({'width':0.9+'rem','height':0.7+'rem'});
					},30);

					$('#picList').on('touchend',fan);
					function fan(){
						onOff = false;
						aHide();
					}
				}else{
					aHide();
				}
				
				$('.lang_box div').on('touchend',lglist);
				function lglist(ev){
					$(this).css({'transform':'scale(0.7,0.7)','color':'rgb(255, 216, 81)'});
					var This = $(this);
					setTimeout(function(){
						This.css({'transform':'scale(1,1)','color':'#5E430D'});
						var urlVal = (This.find('span').css('background'))
						This.parents('span').css({'background':urlVal});

						onOff = false;
						aHide();
					},90);

					ev.stopPropagation();
				}
				
			}

			function aHide(){
				$('.lang_box').css({'width':0,'height':0});
				setTimeout(function(){
					$('.lang_box').remove();
				},500);
			}
		})();
	</script>
</html>

