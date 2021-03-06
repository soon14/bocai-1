<?php
session_start();
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
include_once "../../app/member/utils/login_check.php";
include_once "../../app/member/utils/error_handle.php";
include_once "../../app/member/utils/convert_name.php";
include_once "../../app/member/utils/time_util.php";

include_once "../../app/member/class/six_lottery_odds.php";
include_once "../../app/member/class/six_lottery_order.php";
include_once "../../app/member/class/six_lottery_schedule.php";
include_once "../../app/member/class/user_group.php";
$C_Patch=$_SERVER['DOCUMENT_ROOT'];
include_once($C_Patch."/app/member/cache/ltConfig.php");
include_once($C_Patch."/app/member/class/common_class.php");

$goldArray = $_POST["gold"];
$oddsArray = $_POST["odds"];
$gid = $_POST["gid"];

//echo json_encode($oddsArray);exit;

include_once "../../member/lt/lt_util.php";

$validateOdd = "true";
$rType = $gid;
$rTypeName = getZhLhcName($rType);
$betInfoArray = array();
$bet_money_total = 0;
$bet_win_total = 0;



//验证money_log是否存在错误
$sql = "select assets,balance,order_value from money_log where user_id='".$_SESSION["userid"]."' order by id desc limit 0,2";
$query	=	$mysqli->query($sql);
$rs = array();
while($row = $query->fetch_array()){
    $rs[] = $row;
}
/*if(count($rs)>1){
    if($rs[0]["assets"]!=$rs[1]["balance"]){
        error2("账号金额异常，请联系管理人员。");
    }
}*/

//获取赔率以及验证赔率
if($gid=="SP" || $gid=="SPbside"){
    if($gid=="SP"){
        $odds_SP = six_lottery_odds::getOddsByBallType("SP","a_side");
    }elseif( $gid=="SPbside"){
        $odds_SP = six_lottery_odds::getOdds("SP");
    }
    $odds_SP_other = six_lottery_odds::getOddsByBallType("SP","other");

    //验证赔率是否被更改
    for($i=1;$i<50;$i++){
        $numString = $i<10 ? ('0'.$i) : $i;
        if($goldArray["SP".$numString]>0){
            if($odds_SP["h".$i] != $oddsArray["SP".$numString]){
                $validateOdd = "false";
            }
        }
    }
    if(($oddsArray['SP_ODD']!=$odds_SP_other["h1"])|| ($oddsArray['SP_EVEN']!=$odds_SP_other["h2"])|| ($oddsArray['SP_OVER']!=$odds_SP_other["h3"])
        || ($oddsArray['SP_UNDER']!=$odds_SP_other["h4"])|| ($oddsArray['SF_OVER']!=$odds_SP_other["h9"])|| ($oddsArray['SP_SODD']!=$odds_SP_other["h5"])
        || ($oddsArray['SP_SEVEN']!=$odds_SP_other["h6"])|| ($oddsArray['SP_SOVER']!=$odds_SP_other["h7"])|| ($oddsArray['SP_SUNDER']!=$odds_SP_other["h8"])
        || ($oddsArray['SF_UNDER']!=$odds_SP_other["h10"])|| ($oddsArray['HS_EO']!=$odds_SP_other["h16"])|| ($oddsArray['HS_EU']!=$odds_SP_other["h17"])
        || ($oddsArray['HS_OO']!=$odds_SP_other["h14"])|| ($oddsArray['HS_OU']!=$odds_SP_other["h15"])){

        $validateOdd = "false";
    }
}elseif(in_array($gid,array("N1","N2","N3","N4","N5","N6"))){
    $oddsN = six_lottery_odds::getOdds($gid);

    //验证赔率是否被更改
    for($i=1;$i<50;$i++){
        $numString = $i<10 ? ('0'.$i) : $i;
        if($goldArray[$gid.$numString]>0){
            if($oddsN["h".$i] != $oddsArray[$gid.$numString]){
                $validateOdd = "false";
            }
        }
    }
}elseif($gid=="NA"){
    $odds_NA = six_lottery_odds::getOdds("NA");
    $odds_NA_other = six_lottery_odds::getOddsByBallType("NA","other");

    //验证赔率是否被更改
    for($i=1;$i<50;$i++){
        $numString = $i<10 ? ('0'.$i) : $i;
        if($goldArray[$gid.$numString]>0){
            if($odds_NA["h".$i] != $oddsArray[$gid.$numString]){
                $validateOdd = "false";
            }
        }
    }
    if(($oddsArray['NA_ODD']!=$odds_NA_other["h1"])|| ($oddsArray['NA_EVEN']!=$odds_NA_other["h2"])
         || ($oddsArray['NA_OVER']!=$odds_NA_other["h3"])|| ($oddsArray['NA_UNDER']!=$odds_NA_other["h4"])){

        $validateOdd = "false";
    }
}elseif($gid=="NO"){
    $odds1_other = six_lottery_odds::getOddsByBallType("N1","other");
    $odds2_other = six_lottery_odds::getOddsByBallType("N2","other");
    $odds3_other = six_lottery_odds::getOddsByBallType("N3","other");
    $odds4_other = six_lottery_odds::getOddsByBallType("N4","other");
    $odds5_other = six_lottery_odds::getOddsByBallType("N5","other");
    $odds6_other = six_lottery_odds::getOddsByBallType("N6","other");

    for($i=1;$i<7;$i++){
        if($i == 1){
            $odds_other = $odds1_other;
        }elseif($i == 2){
            $odds_other = $odds2_other;
        }elseif($i == 3){
            $odds_other = $odds3_other;
        }elseif($i == 4){
            $odds_other = $odds4_other;
        }elseif($i == 5){
            $odds_other = $odds5_other;
        }elseif($i == 6){
            $odds_other = $odds6_other;
        }

        if(($oddsArray['NO'.$i.'_ODD']!=$odds_other["h1"])|| ($oddsArray['NO'.$i.'_EVEN']!=$odds_other["h2"])|| ($oddsArray['NO'.$i.'_OVER']!=$odds_other["h3"])
            || ($oddsArray['NO'.$i.'_UNDER']!=$odds_other["h4"])|| ($oddsArray['NO'.$i.'_SODD']!=$odds_other["h5"])|| ($oddsArray['NO'.$i.'_SEVEN']!=$odds_other["h6"])
            || ($oddsArray['NO'.$i.'_SOVER']!=$odds_other["h7"])|| ($oddsArray['NO'.$i.'_SUNDER']!=$odds_other["h8"])|| ($oddsArray['NO'.$i.'_FOVER']!=$odds_other["h9"])
            || ($oddsArray['NO'.$i.'_FUNDER']!=$odds_other["h10"])|| ($oddsArray['NO'.$i.'_R']!=$odds_other["h11"])|| ($oddsArray['NO'.$i.'_G']!=$odds_other["h12"])
            || ($oddsArray['NO'.$i.'_B']!=$odds_other["h13"])){

            $validateOdd = "false";
        }
    }
}elseif($gid=="OEOU"){
    $odds1_other = six_lottery_odds::getOddsByBallType("N1","other");
    $odds2_other = six_lottery_odds::getOddsByBallType("N2","other");
    $odds3_other = six_lottery_odds::getOddsByBallType("N3","other");
    $odds4_other = six_lottery_odds::getOddsByBallType("N4","other");
    $odds5_other = six_lottery_odds::getOddsByBallType("N5","other");
    $odds6_other = six_lottery_odds::getOddsByBallType("N6","other");

    $odds_NA_other = six_lottery_odds::getOddsByBallType("NA","other");
    $odds_SP_other = six_lottery_odds::getOddsByBallType("SP","other");

    for($i=1;$i<7;$i++){
        if($i == 1){
            $odds_other = $odds1_other;
        }elseif($i == 2){
            $odds_other = $odds2_other;
        }elseif($i == 3){
            $odds_other = $odds3_other;
        }elseif($i == 4){
            $odds_other = $odds4_other;
        }elseif($i == 5){
            $odds_other = $odds5_other;
        }elseif($i == 6){
            $odds_other = $odds6_other;
        }

        if(($oddsArray['NO'.$i.'_ODD']!=$odds_other["h1"])|| ($oddsArray['NO'.$i.'_EVEN']!=$odds_other["h2"])|| ($oddsArray['NO'.$i.'_OVER']!=$odds_other["h3"])
            || ($oddsArray['NO'.$i.'_UNDER']!=$odds_other["h4"])|| ($oddsArray['NO'.$i.'_SODD']!=$odds_other["h5"])|| ($oddsArray['NO'.$i.'_SEVEN']!=$odds_other["h6"])
            || ($oddsArray['NO'.$i.'_SOVER']!=$odds_other["h7"])|| ($oddsArray['NO'.$i.'_SUNDER']!=$odds_other["h8"])){

            $validateOdd = "false";
        }
        if(($oddsArray['NA_ODD']!=$odds_NA_other["h1"])|| ($oddsArray['NA_EVEN']!=$odds_NA_other["h2"])
            || ($oddsArray['NA_OVER']!=$odds_NA_other["h3"])|| ($oddsArray['NA_UNDER']!=$odds_NA_other["h4"])){

            $validateOdd = "false";
        }
        if(($oddsArray['SP_ODD']!=$odds_SP_other["h1"])|| ($oddsArray['SP_EVEN']!=$odds_SP_other["h2"])|| ($oddsArray['SP_OVER']!=$odds_SP_other["h3"])
            || ($oddsArray['SP_UNDER']!=$odds_SP_other["h4"])|| ($oddsArray['SP_SODD']!=$odds_SP_other["h5"])|| ($oddsArray['SP_SEVEN']!=$odds_SP_other["h6"])
            || ($oddsArray['SP_SOVER']!=$odds_SP_other["h7"])|| ($oddsArray['SP_SUNDER']!=$odds_SP_other["h8"])){

            $validateOdd = "false";
        }
    }
}elseif($gid=="SPA"){
    $odds_SP_other = six_lottery_odds::getOddsByBallType("SP","other");
    $odds_SPA = six_lottery_odds::getOdds("SPA");

    //验证赔率是否被更改
    for($i=1;$i<10;$i++){
        if($goldArray["SP_A".$i]>0){
            if($odds_SPA["h".$i] != $oddsArray["SP_A".$i]){
                $validateOdd = "false";
            }
        }
    }
    if(($oddsArray['SP_AA']!=$odds_SPA["h10"])|| ($oddsArray['SP_AB']!=$odds_SPA["h11"])|| ($oddsArray['SP_AC']!=$odds_SPA["h12"])
        ||($oddsArray['SH0']!=$odds_SPA["h13"])|| ($oddsArray['SH1']!=$odds_SPA["h14"])|| ($oddsArray['SH2']!=$odds_SPA["h15"])
        ||($oddsArray['SH3']!=$odds_SPA["h16"])|| ($oddsArray['SH4']!=$odds_SPA["h17"])|| ($oddsArray['SF0']!=$odds_SPA["h18"])
        ||($oddsArray['SF1']!=$odds_SPA["h19"])|| ($oddsArray['SF2']!=$odds_SPA["h20"])|| ($oddsArray['SF3']!=$odds_SPA["h21"])
        ||($oddsArray['SF4']!=$odds_SPA["h22"])|| ($oddsArray['SF5']!=$odds_SPA["h23"])|| ($oddsArray['SF6']!=$odds_SPA["h24"])
        ||($oddsArray['SF7']!=$odds_SPA["h25"])|| ($oddsArray['SF8']!=$odds_SPA["h26"])|| ($oddsArray['SF9']!=$odds_SPA["h27"])
        || ($oddsArray['SP_R']!=$odds_SP_other["h11"])|| ($oddsArray['SP_G']!=$odds_SP_other["h12"])||($oddsArray['SP_B']!=$odds_SP_other["h13"])){

        $validateOdd = "false";
    }
}elseif($gid=="C7"){
    $odds_C7 = six_lottery_odds::getOdds("C7");

    //验证赔率是否被更改
    for($i=1;$i<10;$i++){
        if($goldArray["NA_A".$i]>0){
            if($odds_C7["h".$i] != $oddsArray["NA_A".$i]){
                $validateOdd = "false";
            }
        }
    }
    if(($oddsArray['NA_AA']!=$odds_C7["h10"])|| ($oddsArray['NA_AB']!=$odds_C7["h11"])|| ($oddsArray['NA_AC']!=$odds_C7["h12"])
        ||($oddsArray['C7_R']!=$odds_C7["h13"])|| ($oddsArray['C7_B']!=$odds_C7["h14"])|| ($oddsArray['C7_G']!=$odds_C7["h15"])
        ||($oddsArray['C7_N']!=$odds_C7["h16"])
        ){

        $validateOdd = "false";
    }
}elseif($gid=="SPB"){
    $odds_SPB = six_lottery_odds::getOdds("SPB");

    //验证赔率是否被更改
    for($i=1;$i<10;$i++){
        if($goldArray["SP_B".$i]>0){
            if($odds_SPB["h".$i] != $oddsArray["SP_B".$i]){
                $validateOdd = "false";
            }
        }
    }
    if(($oddsArray['SP_BA']!=$odds_SPB["h10"])|| ($oddsArray['SP_BB']!=$odds_SPB["h11"])|| ($oddsArray['SP_BC']!=$odds_SPB["h12"])
        ||($oddsArray['NF0']!=$odds_SPB["h13"])|| ($oddsArray['NF1']!=$odds_SPB["h14"])|| ($oddsArray['NF2']!=$odds_SPB["h15"])
        ||($oddsArray['NF3']!=$odds_SPB["h16"])|| ($oddsArray['NF4']!=$odds_SPB["h17"])|| ($oddsArray['NF5']!=$odds_SPB["h18"])
        ||($oddsArray['NF6']!=$odds_SPB["h19"])|| ($oddsArray['NF7']!=$odds_SPB["h20"])|| ($oddsArray['NF8']!=$odds_SPB["h21"])
        ||($oddsArray['NF9']!=$odds_SPB["h22"])
        || ($oddsArray['TX2']!=$odds_SPB["h23"])|| ($oddsArray['TX5']!=$odds_SPB["h24"])|| ($oddsArray['TX6']!=$odds_SPB["h25"])
        || ($oddsArray['TX7']!=$odds_SPB["h26"])|| ($oddsArray['TX_ODD']!=$odds_SPB["h27"])|| ($oddsArray['TX_EVEN']!=$odds_SPB["h28"])
    ){

        $validateOdd = "false";
    }
}elseif($gid=="HB"){
    $odds_HB = six_lottery_odds::getOdds("HB");

    if(($oddsArray['HB_RODD']!=$odds_HB["h1"])|| ($oddsArray['HB_REVEN']!=$odds_HB["h2"])||($oddsArray['HB_ROVER']!=$odds_HB["h3"])
        ||($oddsArray['HB_RUNDER']!=$odds_HB["h4"])|| ($oddsArray['HB_GODD']!=$odds_HB["h5"])||($oddsArray['HB_GEVEN']!=$odds_HB["h6"])
        ||($oddsArray['HB_GOVER']!=$odds_HB["h7"])|| ($oddsArray['HB_GUNDER']!=$odds_HB["h8"])||($oddsArray['HB_BODD']!=$odds_HB["h9"])
        ||($oddsArray['HB_BEVEN']!=$odds_HB["h10"])|| ($oddsArray['HB_BOVER']!=$odds_HB["h11"])|| ($oddsArray['HB_BUNDER']!=$odds_HB["h12"])
        ||($oddsArray['HH_ROO']!=$odds_HB["h13"])|| ($oddsArray['HH_ROE']!=$odds_HB["h14"])|| ($oddsArray['HH_RUO']!=$odds_HB["h15"])
        ||($oddsArray['HH_RUE']!=$odds_HB["h16"])|| ($oddsArray['HH_GOO']!=$odds_HB["h17"])|| ($oddsArray['HH_GOE']!=$odds_HB["h18"])
        ||($oddsArray['HH_GUO']!=$odds_HB["h19"])|| ($oddsArray['HH_GUE']!=$odds_HB["h20"])|| ($oddsArray['HH_BOO']!=$odds_HB["h21"])
        ||($oddsArray['HH_BOE']!=$odds_HB["h22"])|| ($oddsArray['HH_BUO']!=$odds_HB["h23"])|| ($oddsArray['HH_BUE']!=$odds_HB["h24"])
    ){

        $validateOdd = "false";
    }
}elseif($gid=="NAP"){
    $odds_NAP1 = six_lottery_odds::getOdds("NAP1");
    $odds_NAP2 = six_lottery_odds::getOdds("NAP2");
    $odds_NAP3 = six_lottery_odds::getOdds("NAP3");
    $odds_NAP4 = six_lottery_odds::getOdds("NAP4");
    $odds_NAP5 = six_lottery_odds::getOdds("NAP5");
    $odds_NAP6 = six_lottery_odds::getOdds("NAP6");

    $game1 = $_POST["game1"];
    $game2 = $_POST["game2"];
    $game3 = $_POST["game3"];
    $game4 = $_POST["game4"];
    $game5 = $_POST["game5"];
    $game6 = $_POST["game6"];

    $radio1 = $_POST["radio1"];
    $radio2 = $_POST["radio2"];
    $radio3 = $_POST["radio3"];
    $radio4 = $_POST["radio4"];
    $radio5 = $_POST["radio5"];
    $radio6 = $_POST["radio6"];

    $oddindex1 = $_POST["oddindex1"];
    $oddindex2 = $_POST["oddindex2"];
    $oddindex3 = $_POST["oddindex3"];
    $oddindex4 = $_POST["oddindex4"];
    $oddindex5 = $_POST["oddindex5"];
    $oddindex6 = $_POST["oddindex6"];

    $bet_info_nap = "";
    $bet_rate_nap = "";
    if($game1){
        if($radio1 != $odds_NAP1["h".$oddindex1]){
            $validateOdd = "false";
        }
        $bet_info_nap .= $game1.",";
        $bet_rate_nap .= $radio1.",";
    }
    if($game2){
        if($radio2 != $odds_NAP2["h".$oddindex2]){
            $validateOdd = "false";
        }
        $bet_info_nap .= $game2.",";
        $bet_rate_nap .= $radio2.",";
    }
    if($game3){
        if($radio3 != $odds_NAP3["h".$oddindex3]){
            $validateOdd = "false";
        }
        $bet_info_nap .= $game3.",";
        $bet_rate_nap .= $radio3.",";
    }
    if($game4){
        if($radio4 != $odds_NAP4["h".$oddindex4]){
            $validateOdd = "false";
        }
        $bet_info_nap .= $game4.",";
        $bet_rate_nap .= $radio4.",";
    }
    if($game5){
        if($radio5 != $odds_NAP5["h".$oddindex5]){
            $validateOdd = "false";
        }
        $bet_info_nap .= $game5.",";
        $bet_rate_nap .= $radio5.",";
    }
    if($game6){
        if($radio6 != $odds_NAP6["h".$oddindex6]){
            $validateOdd = "false";
        }
        $bet_info_nap .= $game6.",";
        $bet_rate_nap .= $radio6.",";
    }
    $bet_info_nap = substr($bet_info_nap,0,-1);
    $bet_rate_nap = substr($bet_rate_nap,0,-1);

    $bet_win_total = 0;
    $bet_money_one = $goldArray;
    validateNumber($bet_info_nap,"正码过关");
    if(!(intval($bet_money_one)>0)){
        error2("输入金额为负数或者不大于0，请重新下注。");
    }
    $betInfo_one = $bet_info_nap;
    $bet_rate_one = $bet_rate_nap;
    $bet_money_total = $bet_money_one;
}elseif($gid=="NX"){
    $odds_NX = six_lottery_odds::getOdds("NX");

    $number_nx = $_POST["num"];
    $select_count_nx = count(explode(",",$number_nx));

    validateNumber($number_nx,"连肖");
    if($select_count_nx<2 || $select_count_nx>11){
        error2("超出范围，请重新下注。");
        exit;
    }

    $bet_money_one = $goldArray;
    if(!(intval($bet_money_one)>0)){
        error2("输入金额为负数或者不大于0，请重新下注。");
    }
    $betInfo_one = $number_nx;
    $bet_rate_one = $odds_NX["h".$select_count_nx];
    $bet_money_total = $bet_money_one;
    $bet_win_total = $bet_rate_one*$bet_money_total;
}elseif($gid=="LX" || $gid=="LF"){//连肖连尾
    $odds_LX2 = six_lottery_odds::getOdds("LX2");
    $odds_LX3 = six_lottery_odds::getOdds("LX3");
    $odds_LX4 = six_lottery_odds::getOdds("LX4");
    $odds_LX5 = six_lottery_odds::getOdds("LX5");

    $odds_LF2 = six_lottery_odds::getOdds("LF2");
    $odds_LF3 = six_lottery_odds::getOdds("LF3");
    $odds_LF4 = six_lottery_odds::getOdds("LF4");
    $odds_LF5 = six_lottery_odds::getOdds("LF5");

    $totalArray = $_POST["totalArray"];
    $oddsIndexArray = $_POST["oddsIndexArray"];
    $bet_money_one = $_POST["gold"];
    $lx_name = $_POST["lx_name"];
    $rTypeName = $lx_name;

    $goldArray = array();
    $oddsArray = array();
    $betInfoArray = array();

    $minCount = count(explode(",", $totalArray[0]));
    if($gid=="LX"){
        foreach($totalArray as $key => $value){
            validateNumber($value,"连肖");
        }
        if($minCount==2){
            $odds_select = $odds_LX2;
        }elseif($minCount==3){
            $odds_select = $odds_LX3;
        }elseif($minCount==4){
            $odds_select = $odds_LX4;
        }elseif($minCount==5){
            $odds_select = $odds_LX5;
        }
    }elseif($gid=="LF"){
        foreach($totalArray as $key => $value){
            validateNumber($value,"连尾");
        }
        if($minCount==2){
            $odds_select = $odds_LF2;
        }elseif($minCount==3){
            $odds_select = $odds_LF3;
        }elseif($minCount==4){
            $odds_select = $odds_LF4;
        }elseif($minCount==5){
            $odds_select = $odds_LF5;
        }
    }
    foreach($totalArray as $key => $value){
        $goldArray[] = $bet_money_one;
        if(!(intval($bet_money_one)>0)){
            error2("输入金额为负数或者不大于0，请重新下注。");
        }
        $betInfoArray[] = $value;
        $oddsArray[] = $odds_select["h".$oddsIndexArray[$key]];
        $bet_money_total = $bet_money_total + $bet_money_one;
        $bet_win_total = $bet_win_total + $bet_money_one*$odds_select["h".$oddsIndexArray[$key]];
    }
    $validateLxArray = explode(",", $betInfoArray[0]);
    for($i=0; $i<count($validateLxArray);$i++){
        for($j=0; $j<count($validateLxArray);$j++){
            if($validateLxArray[$i]==$validateLxArray[$j] && $i!=$j){
                error2("下注内容有误。");
                exit;
            }
        }
    }

    if($minCount<2 || $minCount>5){
        error2("超出范围，请重新下注。");
        exit;
    }
}elseif($gid=="NI"){//自选不中
    $odds_NI = six_lottery_odds::getOdds("NI");

    $totalArray = $_POST["totalArray"];
    $bet_money_one = $_POST["gold"];
    $ni_name = $_POST["ni_name"];
    $rTypeName = $ni_name;

    $goldArray = array();
    $oddsArray = array();
    $betInfoArray = array();

    $minCount = count(explode(", ", $totalArray[0]));
    if($minCount==5){
        $oddsValue = $odds_NI["h1"];
    }elseif($minCount==6){
        $oddsValue = $odds_NI["h2"];
    }elseif($minCount==7){
        $oddsValue = $odds_NI["h3"];
    }elseif($minCount==8){
        $oddsValue = $odds_NI["h4"];
    }elseif($minCount==9){
        $oddsValue = $odds_NI["h5"];
    }elseif($minCount==10){
        $oddsValue = $odds_NI["h6"];
    }elseif($minCount==11){
        $oddsValue = $odds_NI["h7"];
    }elseif($minCount==12){
        $oddsValue = $odds_NI["h8"];
    }
    foreach($totalArray as $key => $value){
        $goldArray[] = $bet_money_one;
        validateNumber($value,"自选不中");
        if(!(intval($bet_money_one)>0)){
            error2("输入金额为负数或者不大于0，请重新下注。");
        }
        $betInfoArray[] = $value;
        $oddsArray[] = $oddsValue;
        $bet_money_total = $bet_money_total + $bet_money_one;
        $bet_win_total = $bet_win_total + $bet_money_one*$oddsValue;
    }

    if($minCount<5 || $minCount>12){
        error2("超出范围，请重新下注。");
        exit;
    }
    $niArray = explode(",",$totalArray[0]);
    for($i=0;$i<count($niArray);$i++){
        for($j=$i+1;$j<count($niArray);$j++){
            if($niArray[$i]==$niArray[$j]){
                error2("投注内容重复，请重新下注。");
                exit;
            }
        }
    }
}elseif($gid=="CH"){//连码
    $odds_CH = six_lottery_odds::getOdds("CH");

    $totalArray = $_POST["totalArray"];
    $bet_money_one = $_POST["gold"];
    $ch_name = $_POST["ch_name"];
    $rTypeName = $ch_name;

    $goldArray = array();
    $oddsArray = array();
    $betInfoArray = array();

    $minCount = count(explode(", ", $totalArray[0]));

    if($ch_name=="四全中"){
        $oddsValue = $odds_CH["h1"];
        if($minCount!=4){
            $validateOdd == "false";
        }
    }elseif($ch_name=="三全中"){
        $oddsValue = $odds_CH["h2"];
        if($minCount!=3){
            $validateOdd == "false";
        }
    }elseif($ch_name=="三中二"){
        $oddsValue = $odds_CH["h4"];
        if($minCount!=3){
            $validateOdd == "false";
        }
    }elseif($ch_name=="二全中"){
        $oddsValue = $odds_CH["h5"];
        if($minCount!=2){
            $validateOdd == "false";
        }
    }elseif($ch_name=="二中特"){
        $oddsValue = $odds_CH["h7"];
        if($minCount!=2){
            $validateOdd == "false";
        }
    }elseif($ch_name=="特串"){
        $oddsValue = $odds_CH["h8"];
        if($minCount!=2){
            $validateOdd == "false";
        }
    }

    foreach($totalArray as $key => $value){
        $goldArray[] = $bet_money_one;
        validateNumber($value,"连码");
        if(!(intval($bet_money_one)>0)){
            error2("输入金额为负数或者不大于0，请重新下注。");
        }
        $betInfoArray[] = $value;
        $oddsArray[] = $oddsValue;
        $bet_money_total = $bet_money_total + $bet_money_one;
        $bet_win_total = $bet_win_total + $bet_money_one*$oddsValue;
    }
}

if($validateOdd == "false"){
	if($_GET['style']){
		echo "<script>alert('赔率变更，请重新下注。');window.location.href='/main.php?ack=uu'</script>";
		exit;
	}else{
		error2("赔率变更，请重新下注。");
		 exit;
	}
}
//计算总金额以及下注额
if(in_array($gid,array("NAP","NX","LX","LF","NI","CH"))){

}else{
    foreach($goldArray as $key => $value) {
        if(intval($goldArray[$key])<0){
			if($_GET['style']){
				echo "<script>alert('输入金额为负数或者不大于0，请重新下注。');window.location.href='/main.php?ack=uu'</script>";
				exit;
			}else{
				error2("输入金额为负数或者不大于0，请重新下注。");
			}
        }
        if($goldArray[$key]){
            $bet_money_total = $bet_money_total + $goldArray[$key];
            $bet_win_total = $bet_win_total + $goldArray[$key]*$oddsArray[$key];
            $betInfoArray[$key] = getBetInfo($key,$rType);
        }
    }
}

//会员余额
$balance	=	0;//投注后
$assets		=	0;//投注前
global $mysqli;
$sql		= 	"select money from user_list where user_id='$userid' limit 1";
$query 		=	$mysqli->query($sql);
$rs			=	$query->fetch_array();
if($rs['money']){
    $assets	=	round($rs['money'],2);
    $balance=	$assets-$bet_money_total;
}else{
	if($_GET['style']){
		echo "<script>alert('账户异常,请联系客服!');window.location.href='/main.php?ack=uu'</script>";
		exit;
	}else{
		error2("账户异常,请联系客服!");
	}
}
if($balance<0){ //投注后，用户余额不能小于0
	if($_GET['style']){
		echo "<script>alert('账户余额不足');window.location.href='/main.php?ack=uu'</script>";
		exit;
	}else{
		error1("账户余额不足!");
	}
}
if($is_close){
	if($_GET['style']){
		echo "<script>alert('改投注已过时,请联系客服!');window.location.href='/main.php?ack=uu'</script>";
		exit;
	}else{
		error2("改投注已过时,请联系客服!");
	}
}

$max_money = common_class::getMaxMoney($userid);
$max_money_already = common_class::getMaxMoneyAlready_lhc($userid,$qishu);

if($max_money > 0 && ($max_money_already+$bet_money_total)>$max_money){
	if($_GET['style']){
		echo "<script>alert('超过当期下注最大金额，请联系管理人员。');window.location.href='/main.php?ack=uu'</script>";
		exit;
	}else{
		echo '<script type="text/javascript">alert("超过当期下注最大金额，请联系管理人员。");</script>';
		exit;
	}
}

if(!six_lottery_order::add_order($userid,$rTypeName,$rType,$qishu,
                    $bet_money_total,$balance,$bet_win_total,$assets,
                    $goldArray,$oddsArray,$betInfoArray,
                    $gid,$bet_money_one,$betInfo_one,$bet_rate_one
                    )){
	if($_GET['style']){
		echo "<script>alert('交易失败');window.location.href='/main.php?ack=uu'</script>";
		exit;
	}else{
		error2("交易失败");
	}
}

$mysqli->close();

if($_GET['style']){
	echo "<script>alert('提交成功');window.location.href='/main.php?ack=uu'</script>";
	exit;
}else{
	echo $balance;
	exit;
}



function getBetInfo($key,$rType){
    $betInfo = "";
    if(in_array($rType,array("SP","SPbside","NA","N1","N2","N3","N4","N5","N6"))){
        $betSp = substr($key,2,2)+0;
        if($betSp>0 && $betSp<50){
            $betInfo = $betSp;
        }else{
            if($key == "SP_ODD"){
                $betInfo = "特别号 单";
            }elseif($key == "SP_EVEN"){
                $betInfo = "特别号 双";
            }elseif($key == "SP_OVER"){
                $betInfo = "特别号 大";
            }elseif($key == "SP_UNDER"){
                $betInfo = "特别号 小";
            }elseif($key == "SF_OVER"){
                $betInfo = "特别号 尾大";
            }elseif($key == "SP_SODD"){
                $betInfo = "特别号 和单";
            }elseif($key == "SP_SEVEN"){
                $betInfo = "特别号 和双";
            }elseif($key == "SP_SOVER"){
                $betInfo = "特别号 和大";
            }elseif($key == "SP_SUNDER"){
                $betInfo = "特别号 和小";
            }elseif($key == "SF_UNDER"){
                $betInfo = "特别号 尾小";
            }elseif($key == "HS_EO"){
                $betInfo = "特别号 大双";
            }elseif($key == "HS_EU"){
                $betInfo = "特别号 小双";
            }elseif($key == "HS_OO"){
                $betInfo = "特别号 大单";
            }elseif($key == "HS_OU"){
                $betInfo = "特别号 小单";
            }
            elseif($key == "NA_ODD"){
                $betInfo = "总和 单";
            }elseif($key == "NA_EVEN"){
                $betInfo = "总和 双";
            }elseif($key == "NA_OVER"){
                $betInfo = "总和 大";
            }elseif($key == "NA_UNDER"){
                $betInfo = "总和 小";
            }
        }
    }elseif($rType == "NO" || $rType=="OEOU"){
        $betNumber = substr($key,2,1);
        $betInfoPre = "";
        if($betNumber == 1){
            $betInfoPre = "正码一";
        }elseif($betNumber == 2){
            $betInfoPre = "正码二";
        }elseif($betNumber == 3){
            $betInfoPre = "正码三";
        }elseif($betNumber == 4){
            $betInfoPre = "正码四";
        }elseif($betNumber == 5){
            $betInfoPre = "正码五";
        }elseif($betNumber == 6){
            $betInfoPre = "正码六";
        }elseif(substr($key,0,2)=="SP"){
            $betInfoPre = "特别号";
        }elseif(substr($key,0,2)=="NA"){
            $betInfoPre = "总和";
        }
        if(strpos($key,"_ODD") !== false){
            $betInfo = "单";
        }elseif(strpos($key,"_EVEN") !== false){
            $betInfo = "双";
        }elseif(strpos($key,"_OVER") !== false){
            $betInfo = "大";
        }elseif(strpos($key,"_UNDER") !== false){
            $betInfo = "小";
        }elseif(strpos($key,"_SODD") !== false){
            $betInfo = "和单";
        }elseif(strpos($key,"_SEVEN") !== false){
            $betInfo = "和双";
        }elseif(strpos($key,"_SOVER") !== false){
            $betInfo = "和大";
        }elseif(strpos($key,"_SUNDER") !== false){
            $betInfo = "和小";
        }elseif(strpos($key,"_FOVER") !== false){
            $betInfo = "尾大";
        }elseif(strpos($key,"_FUNDER") !== false){
            $betInfo = "尾小";
        }elseif(strpos($key,"_R") !== false){
            $betInfo = "红波";
        }elseif(strpos($key,"_G") !== false){
            $betInfo = "绿波";
        }elseif(strpos($key,"_B") !== false){
            $betInfo = "蓝波";
        }
        $betInfo = $betInfoPre." ".$betInfo;
    }elseif($rType == "SPA"){
        if($key=="SP_A1"){
            $betInfo = "鼠";
        }elseif($key=="SP_A2"){
            $betInfo = "牛";
        }elseif($key=="SP_A3"){
            $betInfo = "虎";
        }elseif($key=="SP_A4"){
            $betInfo = "兔";
        }elseif($key=="SP_A5"){
            $betInfo = "龙";
        }elseif($key=="SP_A6"){
            $betInfo = "蛇";
        }elseif($key=="SP_A7"){
            $betInfo = "马";
        }elseif($key=="SP_A8"){
            $betInfo = "羊";
        }elseif($key=="SP_A9"){
            $betInfo = "猴";
        }elseif($key=="SP_AA"){
            $betInfo = "鸡";
        }elseif($key=="SP_AB"){
            $betInfo = "狗";
        }elseif($key=="SP_AC"){
            $betInfo = "猪";
        }
        elseif($key=="SP_R"){
            $betInfo = "红波";
        }elseif($key=="SP_G"){
            $betInfo = "绿波";
        }elseif($key=="SP_B"){
            $betInfo = "蓝波";
        }

        elseif($key=="SH0"){
            $betInfo = "头0";
        }elseif($key=="SH1"){
            $betInfo = "头1";
        }elseif($key=="SH2"){
            $betInfo = "头2";
        }elseif($key=="SH3"){
            $betInfo = "头3";
        }elseif($key=="SH4"){
            $betInfo = "头4";
        }
        elseif($key=="SF0"){
            $betInfo = "尾0";
        }elseif($key=="SF1"){
            $betInfo = "尾1";
        }elseif($key=="SF2"){
            $betInfo = "尾2";
        }elseif($key=="SF3"){
            $betInfo = "尾3";
        }elseif($key=="SF4"){
            $betInfo = "尾4";
        }elseif($key=="SF5"){
            $betInfo = "尾5";
        }elseif($key=="SF6"){
            $betInfo = "尾6";
        }elseif($key=="SF7"){
            $betInfo = "尾7";
        }elseif($key=="SF8"){
            $betInfo = "尾8";
        }elseif($key=="SF9"){
            $betInfo = "尾9";
        }
    }elseif($rType == "C7"){
        if($key=="NA_A1"){
            $betInfo = "鼠";
        }elseif($key=="NA_A2"){
            $betInfo = "牛";
        }elseif($key=="NA_A3"){
            $betInfo = "虎";
        }elseif($key=="NA_A4"){
            $betInfo = "兔";
        }elseif($key=="NA_A5"){
            $betInfo = "龙";
        }elseif($key=="NA_A6"){
            $betInfo = "蛇";
        }elseif($key=="NA_A7"){
            $betInfo = "马";
        }elseif($key=="NA_A8"){
            $betInfo = "羊";
        }elseif($key=="NA_A9"){
            $betInfo = "猴";
        }elseif($key=="NA_AA"){
            $betInfo = "鸡";
        }elseif($key=="NA_AB"){
            $betInfo = "狗";
        }elseif($key=="NA_AC"){
            $betInfo = "猪";
        }
        elseif($key=="C7_R"){
            $betInfo = "正肖 红波";
        }elseif($key=="C7_G"){
            $betInfo = "正肖 绿波";
        }elseif($key=="C7_B"){
            $betInfo = "正肖 蓝波";
        }elseif($key=="C7_N"){
            $betInfo = "正肖 和局";
        }
    }elseif($rType == "SPB"){
        if($key=="SP_B1"){
            $betInfo = "鼠";
        }elseif($key=="SP_B2"){
            $betInfo = "牛";
        }elseif($key=="SP_B3"){
            $betInfo = "虎";
        }elseif($key=="SP_B4"){
            $betInfo = "兔";
        }elseif($key=="SP_B5"){
            $betInfo = "龙";
        }elseif($key=="SP_B6"){
            $betInfo = "蛇";
        }elseif($key=="SP_B7"){
            $betInfo = "马";
        }elseif($key=="SP_B8"){
            $betInfo = "羊";
        }elseif($key=="SP_B9"){
            $betInfo = "猴";
        }elseif($key=="SP_BA"){
            $betInfo = "鸡";
        }elseif($key=="SP_BB"){
            $betInfo = "狗";
        }elseif($key=="SP_BC"){
            $betInfo = "猪";
        }

        elseif($key=="NF0"){
            $betInfo = "尾0";
        }elseif($key=="NF1"){
            $betInfo = "尾1";
        }elseif($key=="NF2"){
            $betInfo = "尾2";
        }elseif($key=="NF3"){
            $betInfo = "尾3";
        }elseif($key=="NF4"){
            $betInfo = "尾4";
        }elseif($key=="NF5"){
            $betInfo = "尾5";
        }elseif($key=="NF6"){
            $betInfo = "尾6";
        }elseif($key=="NF7"){
            $betInfo = "尾7";
        }elseif($key=="NF8"){
            $betInfo = "尾8";
        }elseif($key=="NF9"){
            $betInfo = "尾9";
        }

        elseif($key=="TX2"){
            $betInfo = "234肖";
        }elseif($key=="TX5"){
            $betInfo = "5肖";
        }elseif($key=="TX6"){
            $betInfo = "6肖";
        }elseif($key=="TX7"){
            $betInfo = "7肖";
        }elseif($key=="TX_ODD"){
            $betInfo = "总肖单";
        }elseif($key=="TX_EVEN"){
            $betInfo = "总肖双";
        }
    }elseif($rType == "HB"){
        if($key=="HB_RODD"){
            $betInfo = "红单";
        }elseif($key=="HB_REVEN"){
            $betInfo = "红双";
        }elseif($key=="HB_ROVER"){
            $betInfo = "红大";
        }elseif($key=="HB_RUNDER"){
            $betInfo = "红小";
        }elseif($key=="HB_GODD"){
            $betInfo = "绿单";
        }elseif($key=="HB_GEVEN"){
            $betInfo = "绿双";
        }elseif($key=="HB_GOVER"){
            $betInfo = "绿大";
        }elseif($key=="HB_GUNDER"){
            $betInfo = "绿小";
        }elseif($key=="HB_BODD"){
            $betInfo = "蓝单";
        }elseif($key=="HB_BEVEN"){
            $betInfo = "蓝双";
        }elseif($key=="HB_BOVER"){
            $betInfo = "蓝大";
        }elseif($key=="HB_BUNDER"){
            $betInfo = "蓝小";
        }
        elseif($key=="HH_ROO"){
            $betInfo = "红大单";
        }elseif($key=="HH_ROE"){
            $betInfo = "红大双";
        }elseif($key=="HH_RUO"){
            $betInfo = "红小单";
        }elseif($key=="HH_RUE"){
            $betInfo = "红小双";
        }elseif($key=="HH_GOO"){
            $betInfo = "绿大单";
        }elseif($key=="HH_GOE"){
            $betInfo = "绿大双";
        }elseif($key=="HH_GUO"){
            $betInfo = "绿小单";
        }elseif($key=="HH_GUE"){
            $betInfo = "绿小双";
        }elseif($key=="HH_BOO"){
            $betInfo = "蓝大单";
        }elseif($key=="HH_BOE"){
            $betInfo = "蓝大双";
        }elseif($key=="HH_BUO"){
            $betInfo = "蓝小单";
        }elseif($key=="HH_BUE"){
            $betInfo = "蓝小双";
        }
    }

    return $betInfo;
}

function validateNumber($value,$type){
    $array = explode(",",$value);
    if($type=="连肖"){
        foreach($array as $value){
            if(!($value=="鼠" || $value=="牛" || $value=="虎" || $value=="兔" || $value=="龙" || $value=="蛇" ||
                $value=="马" || $value=="羊" || $value=="猴" || $value=="鸡" || $value=="狗" || $value=="猪")){
                error2("你选择的号码有问题，请重新下注。".$type);
            }
        }
    }elseif($type=="连尾"){
        foreach($array as $value){
            if(!(intval($value)>-1 && intval($value)<10)){
                error2("你选择的号码有问题，请重新下注。".$type);
            }
        }
    }elseif($type=="正码过关"){
        foreach($array as $value){
            if(mb_substr($value,0,2,'utf-8')!="正码"){
                error2("你选择的号码有问题，请重新下注。".$type);
            }
        }
    }else{
        foreach($array as $value){
            if(!(intval($value)>0 && intval($value)<50)){
                error2("你选择的号码有问题，请重新下注。".$type);
            }
        }
    }
}