<?php

$return_url ='http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' ."{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

//$map_url = 'http://logistics-stage.allpay.com.tw/Express/map'; // test
//$map_url = 'https://logistics.ecpay.com.tw/Express/map';     // production
$map_url ='https://logistics.ecpay.com.tw/Express/map';
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>返回享愛服飾網</title>
 <style>
 * {
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  font-family: 微軟正黑體,arial;
}

body {
  padding: 0;
  margin: 0;
  text-align: center;
  font-family: 微軟正黑體,arial;
  background: #222;
}
h2{color:#fff;text-align:center}
h2 img{width:100%;max-width:257px;height:auto}
section {
  width: 600px;
  height: 300px;
  position: absolute;
  top: 35%;
  left: 50%;
  margin: -150px 0 0 -300px;
}

.spinner {
  width: 50px;
  height: 50px;
  border-radius: 100%;
  margin: auto;
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  margin: auto;
}
.model-8 {
  background: #222;
}
.model-8 .spinner {
  width: 100px;
  height: 20px;
  border-radius: 0;
}
.model-8 .spinner:before {
  content: '';
  position: absolute;
  width: 10px;
  height: 10px;
  left: 0;
  border-radius: 100%;
  -webkit-animation: shadowSize 2s ease-in infinite;
  animation: shadowSize 2s ease-in infinite;
  color: #fff;
}

@-webkit-keyframes shadow {
  from {
    box-shadow: 0px 0 0 1px inset;
  }
  to {
    box-shadow: 50px 0 0 1px inset;
  }
}
@keyframes shadow {
  from {
    box-shadow: 0px 0 0 1px inset;
  }
  to {
    box-shadow: 50px 0 0 1px inset;
  }
}
@-webkit-keyframes shadowSize {
  0% {
    box-shadow: 15px 0 0 0, 30px 0 0 0, 45px 0 0 0, 60px 0 0 0, 75px 0 0 0;
  }
  20% {
    box-shadow: 15px 0 0 5px, 30px 0 0 0, 45px 0 0 0, 60px 0 0 0, 75px 0 0 0;
  }
  40% {
    box-shadow: 15px 0 0 0, 30px 0 0 5px, 45px 0 0 0, 60px 0 0 0, 75px 0 0 0;
  }
  60% {
    box-shadow: 15px 0 0 0, 30px 0 0 0, 45px 0 0 5px, 60px 0 0 0, 75px 0 0 0;
  }
  80% {
    box-shadow: 15px 0 0 0, 30px 0 0 0, 45px 0 0 0, 60px 0 0 5px, 75px 0 0 0;
  }
  100% {
    box-shadow: 15px 0 0 0, 30px 0 0 0, 45px 0 0 0, 60px 0 0 0, 75px 0 0 5px;
  }
}
@keyframes shadowSize {
  0% {
    box-shadow: 15px 0 0 0, 30px 0 0 0, 45px 0 0 0, 60px 0 0 0, 75px 0 0 0;
  }
  20% {
    box-shadow: 15px 0 0 5px, 30px 0 0 0, 45px 0 0 0, 60px 0 0 0, 75px 0 0 0;
  }
  40% {
    box-shadow: 15px 0 0 0, 30px 0 0 5px, 45px 0 0 0, 60px 0 0 0, 75px 0 0 0;
  }
  60% {
    box-shadow: 15px 0 0 0, 30px 0 0 0, 45px 0 0 5px, 60px 0 0 0, 75px 0 0 0;
  }
  80% {
    box-shadow: 15px 0 0 0, 30px 0 0 0, 45px 0 0 0, 60px 0 0 5px, 75px 0 0 0;
  }
  100% {
    box-shadow: 15px 0 0 0, 30px 0 0 0, 45px 0 0 0, 60px 0 0 0, 75px 0 0 5px;
  }
}
</style>
</head>
<body onLoad="document.form1.submit();">
<section class="mod model-8">
  <h2><img src="ecs_static/img/logo.png"><br />資料傳送中...</h2>
  <div class="spinner"></div>
</section>
<?php if ($status == 'get_store_map') {?>
  <ul id="store-info" style="display: none;">
    <li>商店:<span id="LogisticsSubType"></span></li>
    <li>店鋪ID:<span id="CVSStoreID"></span></li>
    <li>分店名:<span id="CVSStoreName"></span></li>
    <li>地址:<span id="CVSAddress"></span></li>
    <li>電話:<span id="CVSTelephone"></span></li>
  </ul>

  <form name="form1" action="<?php echo $map_url ?>" method="post" >
    <input type="hidden" name="MerchantID" value="3044423">
    <input type="hidden" name="LogisticsType" value="CVS">
    <input type="hidden" name="LogisticsSubType" value="{{$type}}">
    <input type="hidden" name="IsCollection" value="N">
    <input type="hidden" name="ServerReplyURL" value="<?php echo $return_url; ?>">
    <input type="hidden" name="Device" value="{{$device}}">
  </form>
<?php } elseif ($status == 'store_call_back'){ ?>

<form name="form1" id="form1" action="<?php echo $submit_url?>" method="post" style="display:none;">
<table width="100%" border="0" cellspacing="2" cellpadding="2">
  <tbody>
    <tr bgcolor="#F0FAD2">
      <td width="74" style="font-size: 11pt;" align="center"> 門市編號</td>
      <td id="TagStoreID" style="font-size: 11pt;" align="left"><input name="cvs_id" value="<?php echo $_REQUEST['CVSStoreID'] ?>" />　</td>
    </tr>
    <tr bgcolor="#F0FAD2">
      <td style="font-size: 11pt;" align="center"> 門市名稱</td>
      <td id="TagStoreName" style="font-size: 11pt;" align="left"><input name="cvs_name" value="<?php echo $_REQUEST['CVSStoreName'] ?>" />　
      </td>
    </tr>
    <tr bgcolor="#F0FAD2">
      <td style="font-size: 11pt;" align="center" valign="top"> 門市地址</td>
      <td id="TagStoreAddress" style="font-size: 11pt;" align="left" valign="top"><input name="cvs_addr"  value="<?php echo $_REQUEST['CVSAddress'] ?>" /></td>
     <tr bgcolor="#F0FAD2">
      <td colspan="2" id="TagStoreAddress" style="font-size: 11pt;" align="center" valign="middle">
      <input name="送出" type="submit" value="送出" onClick="sendInfo()" id="送出">
      <input type="hidden" value="<?php echo $_REQUEST['LogisticsSubType'] ?>" name="cvs_type">
      </td>
    </tr>
    </tr>
  </tbody>
</table>
</form>



<?php } ?>
</body>

</html>
