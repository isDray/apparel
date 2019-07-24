@extends('layout.layoutNoLeft')

@section('title', "$title")

@section('description', '')
@section('keyword', '')

@section('content')

<div class="row">
<form action="{{url('/test')}}" method="post">
{!! csrf_field() !!}
<div class="col s12 m12 l12 page_main">
    
    <!-- 收貨資料區塊 -->
    <div class="row">
        
        <div class="col s12 m12 l8 offset-l2 " id="fillBox">
            
            <div class="col s12 m12 l12">
            <span>配送區域:</span>
                
                <!-- 國家 -->
                <select id='country'>
                    @foreach( $countrys as $countryk => $country)
                        @if(session()->has('chsCountry'))
                            <option @if( $country['region_id'] == session()->get('chsCountry') ) SELECTED @endif value="{{$country['region_id']}}">{{$country['region_name']}}</option>
                        @else
                            <option @if( $country['region_id'] == 1 ) SELECTED @endif value="{{$country['region_id']}}">{{$country['region_name']}}</option>
                        @endif
                    @endforeach
                </select>
                <!-- 國家結束 -->
                
                <!-- 州 -->
                @if( $provinces != false )
                <select id='province'>
                    @foreach( $provinces as $provincek => $province)
                        @if(session()->has('chsCountry'))
                            <option @if( $province['region_id'] == session()->get('chsProvince') ) SELECTED @endif value="{{$province['region_id']}}" >{{$province['region_name']}}</option>
                        @else
                            <option @if( $province['region_id'] == 1 ) SELECTED @endif value="{{$province['region_id']}}">{{$province['region_name']}}</option>
                        @endif
                    @endforeach
                </select>
                @endif
                <!-- 州結束 -->
                
                <!-- 縣市 -->
                @if( $citys != false )
                <select id='city'>
                    @foreach( $citys as $cityk => $city)

                        @if(session()->has('chsCity'))
                            <option @if( $city['region_id'] == session()->get('chsCity') ) SELECTED @endif value="{{$city['region_id']}}" >{{$city['region_name']}}</option>
                        @else
                            <option @if( $city['region_id'] == 1 ) SELECTED @endif value="{{$city['region_id']}}">{{$city['region_name']}}</option>
                        @endif  

                    @endforeach                    
                </select>
                @endif
                <!-- 縣市結束 -->

            </div>
            
            <!-- 配送方式 -->
            <div class="col s12 m12 l12" id='shipArea'>
                <span>配送方式:</span><br>

                @foreach( $shipping_list as $shipping_listk => $shipping_listv)
                <div class='col s4 m3 l3 shipBox'>
                    <input type="radio" name="shipping" id="shipbox{{$shipping_listk}}" value="{{$shipping_listv['shipping_id']}}" @if( session()->has('chsShip') && session()->get('chsShip') == $shipping_listv['shipping_id']) checked @endif>
                    <label class='shipLabel' for="shipbox{{$shipping_listk}}" >
                        {{$shipping_listv['shipping_name']}}
                        <br>
                        {{$shipping_listv['shipping_fee']}}
                        <br>
                        消費滿{{$shipping_listv['shipping_fee_free']}}免運
                    </label>

                    <span class='hideInput'>
                        
                        <table class="striped">
                            <!-- 針對全家增設獨立表格 -->
                            @if( $shipping_listv['shipping_code'] == 'super_get' )  
                            <tr>
                                <th>超商店名：</th>
                                <td>
                                    <input type="text" class="super_name2" name="super_name2" value="@if(session()->has('FAMI')){{session()->get('FAMI')['CVSStoreName']}}@endif" style="width:180px" readonly disabled="disabled"/>
                                    <span id="super_get_btn" onclick="open_select_store('FAMI')" style="margin-bottom:10px">選擇全家門市</span>
                                </td>
                            </tr>

                            <tr>
                                <th>超商地址：</th>
                                <td>
                                    
                                    <input type="text" class="super_addr2" name="super_addr2" value="@if(session()->has('FAMI')){{session()->get('FAMI')['CVSAddress']}}@endif"  readonly disabled="disabled"/>
                            
                                    <input type="hidden" class="super_no2" name="super_no2" value="@if(session()->has('FAMI')){{session()->get('FAMI')['CVSStoreID']}}@endif" disabled="disabled" />
                            
                                    <input type="hidden" value="" class="now_shipping_code" name="now_shipping_code" disabled="disabled" />
                            
                                    <input type="hidden" class="super_type" name="super_type" value="FAMI" disabled="disabled" />                                    

                                </td>
                            </tr>                                    

                            @endif
                            <!-- 針對全家增設獨立表格結束 -->

                            <!-- 針對7-11增設獨立表格 -->
                            @if( $shipping_listv['shipping_code'] == 'super_get2' )   
                            <tr>
                                <th>超商店名：</th>
                                <td>
                                    <input type="text" class="super_name2" name="super_name2" value="@if(session()->has('UNIMART')){{session()->get('UNIMART')['CVSStoreName']}}@endif" style="width:180px" readonly disabled="disabled" />
                                    <span id="super_get_btn" onclick="open_select_store('UNIMART')" style="margin-bottom:10px" >選擇7-11門市</span><br>                                    
                                </td>
                            </tr>
                            <tr>
                                <th>超商地址：</th>
                                <td>
                                    <input type="text" class="super_addr2" name="super_addr2" value="@if(session()->has('UNIMART')){{session()->get('UNIMART')['CVSAddress']}}@endif" readonly disabled="disabled" />
                            
                                    <input type="hidden" class="super_no2" name="super_no2" value="@if(session()->has('UNIMART')){{session()->get('UNIMART')['CVSStoreID']}}@endif" disabled="disabled" />
                                
                                    <input type="hidden" value="" class="now_shipping_code" name="now_shipping_code" disabled="disabled" />
                                    
                                    <input type="hidden" class="super_type" name="super_type" value="UNIMART" disabled="disabled" />  
                                </td>
                            </tr>                            
                          
                            @endif
                            <!-- 針對7-11增設獨立表格結束 -->

                            <!-- 針對萊爾富增設獨立表格 -->
                            @if( $shipping_listv['shipping_code'] == 'super_get3' ) 
                            <tr>
                                <th>超商店名：</th>
                                <td>
                                    <input type="text" class="super_name2" name="super_name2" value="@if(session()->has('HILIFE')){{session()->get('HILIFE')['CVSStoreName']}}@endif" style="width:180px" readonly disabled="disabled"/>
                                    <span id="super_get_btn" onclick="open_select_store('HILIFE')" style="margin-bottom:10px">選擇萊爾富門市</span>                            
                                </td>
                            </tr>  
                            <tr>
                                <th>超商地址：</th>
                                <td>
                                    <input type="text" class="super_addr2" name="super_addr2" value="@if(session()->has('HILIFE')){{session()->get('HILIFE')['CVSAddress']}}@endif" readonly disabled="disabled"/>
                                    <input type="hidden" class="super_no2" name="super_no2" value="@if(session()->has('HILIFE')){{session()->get('HILIFE')['CVSStoreID']}}@endif" disabled="disabled"/>
                                    <input type="hidden" value="" class="now_shipping_code" name="now_shipping_code" disabled="disabled"/>
                                    <input type="hidden" class="super_type" name="super_type" value="" disabled="disabled"/>                                      
                                </td>
                            </tr>                                                        
                        
                            @endif
                            <!-- 針對萊爾富增設獨立表格結束 -->
                                   
                            @if( $shipping_listv['shipping_code'] == 'super_get' || $shipping_listv['shipping_code'] == 'super_get2' || $shipping_listv['shipping_code'] == 'super_get3')
                            <tr>
                                <th><font color="red">*</font>收貨人：</th>
                                <td><input type="text" class="super_consignee" name="super_consignee" value="" style="width:150px" disabled="disabled" /></td>
                            </tr>
                            <tr>
                                <th><font color="red">*</font>手機：</th>
                                <td><input type="text" class="super_mobile" name="super_mobile" value="" style="width:150px" placeholder="格式:0912345678" disabled="disabled" /></td>
                            </tr>
                            <tr>
                                <th><font color="red">*</font>電子郵件：</th>
                                <td><input type="text" class="super_email" name="super_email" value="" style="width:200px" disabled="disabled" />(訂單收信用)</td>
                            </tr>                                                    
                        
                        
                    
                            @else
                            <tr>
                                <th><font color="red">*</font>收件人</th>
                                <td><input type="text" name="consignee" value="" class="" disabled="disabled"/>
                            </tr>
  
                            <tr class="odd">
                                <th width="80px"><font color="red">*</font>收貨地址</th>
                                <td><input type="text" name="address" value="" class=""/></td>
                            </tr>
                                
                            <tr>
                                <th>郵遞區號</th>
                                <td class="last"><input type="text" name="zipcode" value="" class=""/></td>
                            </tr>  

                            <tr>
                              <th><font color="red"></font>電子郵件</th>
                              <td class="last"><input name="email" type="text" value="" class=""/>(訂單收信用)
                              </td>
                            </tr>

                            <tr>
                              <th><font color="red">*</font>手機</th>
                              <td class="last"><input name="mobile" type="text" value="" class="" placeholder="格式:0912345678"/>
                              </td>
                            </tr>     
                            <tr>
                              <th>電話</th>
                              <td><input type="text" name="tel" value="" class=""/></td>
                            </tr>


                            <tr class="odd last">
                                <th>送貨時間</th>
                                
                                <td class="last">
                                    <select name="best_time">
                                        <option value="" >請選擇</option>
                                        <option value="13點前" >13點前</option>
                                        <option value="13~18點前" >13~18點前</option>
                                        <option value="不指定" >不指定</option>
                                    </select>
                                    
                                    <br>
                                    <span style="color:#ff4899">*(使用宅配寄送用戶可選擇)</span>
                                </td>
                            
                            </tr>
                            
                            <tr>
                                
                                <th>收貨備註</th>
                                <td class="last">
                                    <select name="sign_building">
                                        
                                        <option value="本人親收">
          
                                            本人親收
          
                                        </option>
                                
                                        <option value="管理員(警衛)代收" >管理員(警衛)代收</option>
                                
                                        <option value="親友代收" >親友代收</option>
                                    
                                    </select>
                                    
                                    <br>
                                    
                                    <span style="color:#ff4899">*(使用宅配寄送用戶可選擇)</span>
                                </td>
                            </tr>  
                            @endif                                                

                        </table>                        

                    </span>
                </div>
                @endforeach
            </div>
            <!-- 配送方式結束 -->

            <!-- 收貨人資料 -->
            <div class="col s12 m12 l12" id='consigneeArea'>

            </div>
            <!-- 收貨人資料結束 -->
                                    
                     

        </div>
    </div>
    <!-- 收貨資料區塊結束 -->

</div>

<input type="submit">
</form>
</div>

@endsection

@section('selfCss')
<link rel="stylesheet" type="text/css" href="{{ asset('css/filldata.css') }}">

<style type="text/css">
span#super_get_btn {
  padding: 10px;
  display: block;
  color: #fff;
  background: #333;
  margin-top: 15px;
  /*max-width: 50%;*/
  cursor: pointer;
}
.consignee.option_inner.clearfix {
  padding-left: 0;
}

#super_get_btn{
  margin-left: 10px;
  display: inline!important;
  /*display:inline!important;*/
  font-weight: 900;
  border-radius: 6px;
  font-family: '微軟正黑體';
  background-color: #4CAF50!important; 
  color: white; 
  -webkit-transition-duration: 0.1s; /* Safari */
  transition-duration: 0.1s;
  cursor: pointer; 
  width: auto!important;
}
#super_get_btn:hover{
  -webkit-box-shadow: 2px 2px 1px 1px #316b33 inset;
  -moz-box-shadow: 2px 2px 1px 1px #316b33 inset;
  box-shadow: 2px 2px 1px 1px #316b33 inset;  
}

</style>
@endsection

@section('selfJs')
<script type="text/javascript">
$(document).ready(function(){
    

    /*----------------------------------------------------------------
     | 選取國家動態轉換州的內容
     |----------------------------------------------------------------
     |
     */
    $("#country").change(function(){
        
        var area = $(this).val();

        var countryAjax = $.ajax({
            url: "{{url('/areaChange')}}",
            method: "POST",
            data: { 
                    _token : "{{ csrf_token() }}",
                    area : area ,
                    type : 1
            },
            dataType: "json"
        });
 
        countryAjax.done(function( res ) {
            
            if( res[0] == true ){

                location.reload();
            }
        });
 
        countryAjax.fail(function( jqXHR, textStatus ) {
        
        });        
    });




    /*----------------------------------------------------------------
     | 選取州轉換程式內容
     |----------------------------------------------------------------
     |
     */
    $("#province").change(function(){
        
        var area = $(this).val();

        var countryAjax = $.ajax({
            url: "{{url('/areaChange')}}",
            method: "POST",
            data: { 
                    _token : "{{ csrf_token() }}",
                    area : area ,
                    type : 2
            },
            dataType: "json"
        });
 
        countryAjax.done(function( res ) {
            
            if( res[0] == true ){

                location.reload();
            }
        });
 
        countryAjax.fail(function( jqXHR, textStatus ) {
        
        });        
    });




    /*----------------------------------------------------------------
     | 城市切換 
     |----------------------------------------------------------------
     |
     */
    $("#city").change(function(){
        
        var area = $(this).val();

        var countryAjax = $.ajax({
            url: "{{url('/areaChange')}}",
            method: "POST",
            data: { 
                    _token : "{{ csrf_token() }}",
                    area : area ,
                    type : 3
            },
            dataType: "json"
        });
 
        countryAjax.done(function( res ) {
            
            // if( res[0] == true ){

            //     location.reload();
            // }
        });
 
        countryAjax.fail(function( jqXHR, textStatus ) {
        
        });        
    });     
    



    /*----------------------------------------------------------------
     | 表單呈現及隱藏切換
     |----------------------------------------------------------------
     |
     */
    $("input[name='shipping']").change(function(){

        var ship = $("input[name='shipping']:checked").val();

        // 先記錄選擇了甚麼配送
        var shipAjax = $.ajax({
            url: "{{url('/shipChange')}}",
            method: "POST",
            data: { 
                    _token : "{{ csrf_token() }}",
                    ship : ship ,
            },
            dataType: "json"
        });
 
        shipAjax.done(function( res ) {
            
        });
 
        shipAjax.fail(function( jqXHR, textStatus ) {
        }); 

        var chsShip = $("input[name='shipping']:checked").parent();
        
        // 清空收貨人訊息區塊
        $("#consigneeArea").empty();

        // 取消鎖定
        $(".hideInput input").removeAttr("disabled");
        
        var tmpHTML = chsShip.children('.hideInput').html();

        $("#consigneeArea").html( tmpHTML );
        
        // 恢復鎖定
        $(".hideInput input").attr("disabled","disabled");
    })

    if ($("input[name='shipping']:checked").val()) {
        
        $("input[name='shipping']").trigger('change');

    }    

});




/*----------------------------------------------------------------
 | 判斷是否為手機
 |----------------------------------------------------------------
 | 此方法使用是否有觸控事件做為判斷依據 , 如果有的話 , 表示為行動
 | 裝置 , 但是如果是有觸控螢幕的筆電會有被誤判的情形發生
 |
 */
function isMobile() {

  try{ document.createEvent("TouchEvent"); return true; }

  catch(e){ return false;}

}




/*----------------------------------------------------------------
 | 呼叫選取地址
 |----------------------------------------------------------------
 | 由於選取超商地址及編號的介面分為兩種 , 最好優先判斷是要呼叫
 | 手機版還是電腦版 , 避免畫面跑版
 |
 */
if( !isMobile() ){
// 電腦版
var open_select_store = function(type){
    
    window.open("{{url('/storeMap')}}"+"/0/"+type,'_self','');
}

}else{

// 手機版
var open_select_store = function(type){
    
    window.open("{{url('/storeMap')}}"+"/1/"+type,'_self','');
}    
}
</script>
@endsection