<?php

$total=5;

$width=600;

$height=300;
$twidth=82;
$theight=52;

$border=16;
$tborder=4;

$cantidad=7;

$width_thumbs_total=($twidth+1)*($cantidad+1);

$width_window=round($width-(2*$border));

$widthzone=round($total*($twidth+1));
$paggingtop=20;
$site_url = $_REQUEST["urlcss"];
$u_agent = $_SERVER['HTTP_USER_AGENT'];

 if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $width_window=$width;
		$paggingtop+=$tborder;
    } 
	
	

echo '
/*--Main Container--*/

.varsslider {
	width: '.$twidth.';
	height: '.$total.';
}
.varsslider1 {
	width: '.$border.';
	height: '.$tborder.';
}
.varsslider2 {
	width: '.$cantidad.';
	height: '.$tborder.';
}
.main_view {
	z-index: 6000;
position: relative;
width: '.($width_window+($border*2)).'px;
	opacity: 0;
	filter: alpha(opacity = 00);
}
/*--Window/Masking Styles--*/
.window {
	z-index: 5000;
	background: #000;
	height: '.$height.'px;	
	width: '.($width_window).'px;
	overflow: hidden; /*--Hides anything outside of the set width/height--*/
	position: relative;
	text-align: left;
	margin:0;
	border: '.$border.'px solid #222;
	-moz-border-radius: '.$border.'px;
	
	-khtml-border-radius: '.$border.'px;
	
	-o-border-radius: '.$border.'px;
	
	-webkit-border-top: '.$border.'px;
	
	 
  border-radius: '.$border.'px;
}


.textslider {
	position: absolute;
		color: #fff;
	 background-image:url(\'http://localhost/word/wp-content/plugins/slider-pro/images/textbg.png\');
	padding: '.round($border).'px;
	font-size: 18px;
	opacity: 0.0;
	filter: alpha(opacity = 00);
  
  	-moz-border-radius: '.$border.'px;
	-moz-border-radius: '.$border.'px;
	-khtml-border-radius: '.$border.'px;
	-khtml-border-radius: '.$border.'px;
	
	-webkit-border-top: '.$border.'px;
	-webkit-border-top: '.$border.'px;
	  border-radius: '.$border.'px;
  border-radius: '.$border.'px;
}




.image_reel {
	
	position: absolute;
	top: 0; left: 0;
	list-style: none;
	
	
	
}
.image_reel img {float: left; position: absolute; opacity: 0.0;}

/*--Paging Styles--*/
.paging {
	
	width: '.($width).'px;
	position: relative;
	list-style: none;
	margin: 0;
	
	height: '.$theight.'px;
  margin-top: -'.$paggingtop.'px;
  

}



.thumbnailszone {
	float: left;
	position: relative;
	overflow: hidden;
	width: '.$widthzone.'px;
	height: '.$theight.'px;
	list-style: none;
	display:inline; 
	margin:0;
}

.prev_sliderbutton {
	float: left;
	position: relative;
	background: #777;
	margin-top: 0px;
	margin-left: 0px;
	padding-top: '.round(($theight-30)/2).'px;
	padding-bottom: '.round(($theight-30)/2).'px;
	margin-right: 1px;
	text-decoration: none;
	text-align:center;
	color: #fff;
	list-style: none;
  -khtml-border-radius-bottomleft: '.$tborder.'px;
  -khtml-border-radius-bottomright: '.$tborder.'px;
  -moz-border-radius-bottomleft: '.$tborder.'px;
  -moz-border-radius-bottomright: '.$tborder.'px;
  -webkit-border-bottom-left-radius: '.$tborder.'px;
  -webkit-border-bottom-right-radius: '.$tborder.'px;
  border-bottom-left-radius: '.$tborder.'px;
  border-bottom-right-radius: '.$tborder.'px;



	
}

.thumbnail {
 position: relative;
	
	overflow: hidden;
	width: '.round($twidth-(2*$tborder)).'px;
	height: '.($theight-$tborder).'px;
	margin-left: '.$tborder.'px;
	margin-right: '.$tborder.'px;
	margin-bottom: '.$tborder.'px;
	
	list-style: none;	
}

.title_thumbnail {
 position: relative;

	width: '.round($twidth-(2*$tborder)).'px;
	height: '.round(($theight-(2*$tborder))/2).'px;
	margin: '.($tborder).'px;

	
	top: -'.round($theight/2).'px;
	 background-image:url(\'http://localhost/word/wp-content/plugins/slider-pro/images/textbg.png\');
	font-size:12px;
	color: #fff;
	overflow: hidden;
}



.thumbnailsmove {

	width: '.$width_thumbs_total.'px;
	height: '.$theight.'px;
	 position: relative;
	float: left;
	margin: 0;
	list-style: none;
}

.boxlink {
	z-index: 3000;
position: relative;
	overflow: hidden;
list-style: none;
	margin-top: 0px;
		width: '.$twidth.'px;
	height: '.$theight.'px;
	 top: -'.round(($theight*2)).'px;
	
}
.shadow2 {
	z-index: 1000;
position: relative;
	margin: 0px;
	padding:0px;
	 background-image:url(\''.$site_url.'/wp-content/plugins/slider-pro/images/shadow.png\');
	list-style: none;
	width: '.($widthzone+60).'px;
	height: 20px;
	 
	
}

.paging a {
	position: relative;
	float: left;
	background: #777;
	margin-top: 0px;
	margin-left: 0px;
	margin-bottom: 8px;
	margin-right: 1px;
	text-decoration: none;
	list-style: none;
  -khtml-border-radius-bottomleft: '.$tborder.'px;
  -khtml-border-radius-bottomright: '.$tborder.'px;
  -moz-border-radius-bottomleft: '.$tborder.'px;
  -moz-border-radius-bottomright: '.$tborder.'px;
  -webkit-border-bottom-left-radius: '.$tborder.'px;
  -webkit-border-bottom-right-radius: '.$tborder.'px;
  border-bottom-left-radius: '.$tborder.'px;
  border-bottom-right-radius: '.$tborder.'px;
overflow: hidden;
	width: '.$twidth.'px;
	height: '.$theight.'px;
	
	
}


.thumbnailimg {
position: relative;
width: 300px;
max-width:400px !important;

}




';

?>