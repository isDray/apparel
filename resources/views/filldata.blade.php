@extends('layout.layoutNoLeft')

@section('title', "$title")

@section('description', '')
@section('keyword', '')

@section('content')

<div class="row">
<form action="{{url('/done')}}" method="post" id="checkout_form">
{!! csrf_field() !!}
<div class="col s12 m12 l12 page_main">
    
    <!-- 收貨資料區塊 -->
    <div class="row">
        
        @if ($errors->any())

            <ul class="collection with-header col s12 m12 l8 offset-l2" style="padding:0px;">
            <li class="collection-header  orange darken-3"><h6>錯誤訊息</h6></li>
            @foreach ($errors->all() as $error)
            <li class="collection-item">{{$error}}</li>
            @endforeach
            </ul>

        @endif
        <div class="col s12 m12 l8 offset-l2 z-depth-5 " id="fillBox">

      
            <div class="col s12 m12 l12">
            <span>配送區域:</span>
                
                <!-- 國家 -->
                <select id='country' name="country">
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
                <select id='province' name="province" >
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
                <select id='city' name="city">
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
                                    <input type="hidden" class="super_type" name="super_type" value="HILIFE" disabled="disabled"/>                                      
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
                                <th>電子郵件：</th>
                                <td><input type="text" class="super_email" name="super_email" value="" style="width:200px" disabled="disabled" />(訂單收信用)</td>
                            </tr>                                                    
                        
                        
                    
                            @else
                            <tr>
                                <th><font color="red">*</font>收貨人</th>
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

            <!-- 付款方式 -->
            <div class="col s12 m12 l12" id='payArea'>
                <span>付款方式:</span><br>

                @foreach( $payment_list as $paymentk => $payment)
                <div class="col s4 m3 l3 payBox">
                    
                    <input type="radio" name="payment" id="paybox{{$paymentk}}" value="{{$payment['pay_id']}}">
                    <label class='paymentLabel' for="paybox{{$paymentk}}" >
                        {{$payment['pay_name']}}
                    </label>
                    <span class='payIntro'>
                        {!!$payment['pay_desc']!!}
                    </span>

                </div>
                @endforeach

            </div>
            <!-- 付款方式結束 -->

            <!-- 付款方式解釋 -->
            <div class="col s12 m12 l12" id='payDesArea'>
                <div class="col s12 m12 l12" id="payDesBox">
                </div>
            </div>
            <!-- 付款方式解釋結束 -->

            <!-- 電子發票 -->
            <div class="col s12 m12 l12" id="invArea">
                
                <span>電子發票:</span><br>
                                
                <SELECT id="carruer_type" name="carruer_type" onChange="return showdiv()" style="margin-bottom:10px;">
                    <OPTION value="1">依會員(無載具者用)</OPTION>
                    <OPTION value="2">自然人憑證</OPTION>
                    <OPTION value="3">手機載具</OPTION>
                    <OPTION value="4">捐贈</OPTION>
                    <OPTION value="5">索取紙本發票</OPTION>
                </SELECT>

            </div>
            <!-- 電子發票結束 -->

            <!-- 電子發票附加  -->
            <div class="col s12 m12 l12" id="invMoreArea">

                <span id='invText1' style="display:none;" class="invTool" >輸入自然人憑證號碼:</span>
                <span id='invText2' style="display:none;" class="invTool" >輸入向財政部申請之手機條碼:</span>

                <br>

               
                    <SELECT id="loveCode" name="loveCode" style="margin-bottom:10px;display:none" class="invTool">
                        <OPTION value="5252">社團法人中華民國身心障礙聯盟</OPTION>
                        <OPTION value="321">財團法人中華民國唐氏症基金會</OPTION>
                        <OPTION value="885521">財團法人中華民國兒童福利聯盟文教基金會</OPTION>
                        <OPTION value="919">財團法人創世社會福利基金會</OPTION>
                        <OPTION value="7699">財團法人基督教瑪喜樂社會福利基金會</OPTION> 
                    </SELECT>
                      
                    <input type="text" name="ei_code" size="30" id="eiCode" style="margin: 5px;display:none" class="invTool">

                    <p id="loveText" style="color:rgb(253, 0, 115);margin: 0px;display:none;" class="invTool" >(感謝您的愛心捐贈，系統將發票開立後，資料將通知各該受捐贈機構，依據法令規定，已捐贈的發票無法索取紙本發票及更改捐贈對象，如有退換貨需求，本公司將會將該發票作廢。)</font>

                    </p>


            </div>
            <!-- 電子發票附加結束 -->

            <!-- 統編資料 -->
            <div class="col s12 m12 l12" id="companyArea">
                <span>統編資料 - </span> <a href="javascript:;"><span class="add_inv">如需要請點選</span></a>

                <div class="option_inner inv_info" style="display:none;">

                    <table>
                        <tr>
                            <th width="100">統一編號：</th>
                            <td>
                                <input type="text" name="inv_payee" value="" >
                            </td>
                        </tr>

                        <tr>
                            <th>發票抬頭：</th>
                            <td>
                                <input type="text" name="inv_content" value="">
                            </td>
                        </tr>

                    </table>

                </div>                
            </div>
            <!-- 統編資料結束 -->

            <!-- 備註 -->
            <div class="col s12 m12 l12" id="noteArea">
            <span>訂單備註:</span>
                
                <textarea name="postscript" rows="5" id="postscript"  placeholder="可在此留言備註配送事項" ></textarea>            

            </div>
            <!-- 備註結束 -->
            
            <div class="col s12 m12 l12" id="submitArea">                                    
                
                <input type="submit" class="btn-large  pink accent-1" value="送出" id="checkOutBtn">     

            </div>

        </div>
    </div>
    <!-- 收貨資料區塊結束 -->

</div>

</form>

<div id="modal1" class="modal">
    <div class="modal-content">
      <h4>表單驗證錯誤</h4>
      <p id='formErrTxt'></p>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">Agree</a>
    </div>
</div>

</div>

@endsection

@section('selfCss')
<link rel="stylesheet" type="text/css" href="{{ asset('css/filldata.css') }}">
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
    



    /*----------------------------------------------------------------
     | 頁面載入時判斷是否要進行呈現表單
     |----------------------------------------------------------------
     |
     */
    if ($("input[name='shipping']:checked").val()) {
        
        $("input[name='shipping']").trigger('change');

    }




    /*----------------------------------------------------------------
     | 切換付款方式時 , 呈現不同說明
     |----------------------------------------------------------------
     |
     */
    $("input[name='payment']").change(function(){
        
        var chsPayment = $("input[name='payment']:checked");

        $("#payDesBox").empty();
        
        var nowPaymentDes = chsPayment.parent().children(".payIntro").html();

        $("#payDesBox").html( nowPaymentDes );
    });




    /*----------------------------------------------------------------
     | 呈現統一編號資料
     |----------------------------------------------------------------
     |
     */
    $(".add_inv").click(function(){
        
        if( $('.inv_info').is(":visible") ){
              
            $('.inv_info').children('input').val("");
            $('.inv_info').hide();

        }else{

            $('.inv_info').show();
        }
    });
    

 $('.modal').modal();

    /*----------------------------------------------------------------
     | 表單檢查
     |----------------------------------------------------------------
     |
     */
    $('#checkout_form').submit(function(){ 
        return true;
        
        // 立即封鎖提交按鈕 , 避免重複提交
        $("#checkOutBtn").prop('disabled', true);

        var nowCountry = $("select[name=country]").val();
        
        var form = $(this); 
        // 確認配送區域

        
        if( $("#country").val() == 0 ){

            $("#formErrTxt").empty();
            $("#formErrTxt").append('請確認配送區域確實填寫。');
            $('.modal').modal('open');
            $("#checkOutBtn").prop('disabled', false);

            return false;
        }
        
        // 只有台灣要做2.3階層判斷
        if( nowCountry == '1'){
        
            if( $("#province").val() == 0 ){

                $("#formErrTxt").empty();
                $("#formErrTxt").append('請確認配送區域確實填寫。');
                $('.modal').modal('open');
                $("#checkOutBtn").prop('disabled', false);                
                return false;
            }

            if( $("#city").val() == 0 ){

                $("#formErrTxt").empty();
                $("#formErrTxt").append('請確認配送區域確實填寫。');
                $('.modal').modal('open');
                $("#checkOutBtn").prop('disabled', false);                
                return false;
            }
        }  
            
        // 判斷選取配送方式
        if (form.find('input[name="shipping"][type!="hidden"]').length > 0 && form.find('input[name="shipping"]:checked').length < 1) {
            $("#formErrTxt").empty();
            $("#formErrTxt").append('尚未選取配送方式');
            $('.modal').modal('open');
            $("#checkOutBtn").prop('disabled', false);      
            return false;
        }        
        
        // 取出配送方式  
        var nowShip = form.find('input[name=shipping]:checked').val();

        // 取出發票載具
        var carruer_type = $("#carruer_type").val();          
        
        // 根據不同配送方式使用不同驗證
        if( nowShip == '17' || nowShip == '18' || nowShip == '19' ){
        

            if( !$("input[name='super_name2']").last().val().length  ){
    

                $("#formErrTxt").empty();
                $("#formErrTxt").append('超商店名欄位為必填。');
                $('.modal').modal('open');                
                $("#checkOutBtn").prop('disabled', false);          
                return false;
            }   
    
            if( !$("input[name='super_addr2']").last().val().length  ){
    

                $("#formErrTxt").empty();
                $("#formErrTxt").append('超商地址欄位為必填。');
                $('.modal').modal('open');                 
                $("#checkOutBtn").prop('disabled', false);          
                return false;
            }
           
            if( !$("input[name='super_consignee']").last().val().length  ){

                $("#formErrTxt").empty();
                $("#formErrTxt").append('收貨人欄位為必填。');
                $('.modal').modal('open');                            
                $("#checkOutBtn").prop('disabled', false);          
                return false;
            }     
    
            if( !$("input[name='super_mobile']").last().val().length  ){

                $("#formErrTxt").empty();
                $("#formErrTxt").append('手機欄位為必填。');
                $('.modal').modal('open');                
                $("#checkOutBtn").prop('disabled', false);          
                return false;
            }   
    
            regex = /^[09]{2}[0-9]{8}$/;
            if (!regex.test($("input[name='super_mobile']").last().val()) ){
    
                $("#formErrTxt").empty();
                $("#formErrTxt").append('手機格式錯誤,請再次確認。');
                $('.modal').modal('open');                       
                $("#checkOutBtn").prop('disabled', false);          
                return false;  
            }
    
            if( carruer_type == '2'){
    
                var regexp = /^[A-Z]{2}[0-9]{14}$/;
                
                if( !regexp.test( $("#eiCode").val() ) ){
    
                    $("#formErrTxt").empty();
                    $("#formErrTxt").append('自然人憑證格式錯誤,請再次確認。<br> 自然人憑證格式為:<br> 2碼大寫英文 + 14碼由0 ~ 9數字所組成 <br>範例:AW12556987322213');
                    $('.modal').modal('open');                         
                    $("#checkOutBtn").prop('disabled', false);          
                    return false;               
                }
                
            }        
            if( carruer_type == '3'){
                
                var regexp = /^\/{1}[0-9A-Z\.\-\+]{7}$/;
    
                if( !regexp.test( $("#eiCode").val() ) ){
    
                    $("#formErrTxt").empty();
                    $("#formErrTxt").append('手機載具格式錯誤,請再次確認。 <br> 手機載具格式為:<br> 以 / 開頭 + 7碼由 0~9、A~Z(大寫)、符號: . + - 所組成<br>範例:/94168A+');
                    $('.modal').modal('open');                    
                    $("#checkOutBtn").prop('disabled', false);          
                    return false;               
                }       
    
            }        
            /*
            if( !$("input[name='super_email']").last().val().length  ){
    
                cAlert("電子郵件欄位為必填。");
                return false;
            }
            regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if( !regex.test($("input[name='super_email']").last().val()) ){
    
                cAlert("電子郵件格式錯誤,請再次確認。");
                return false; 
            }                 
            */
        
        }else{

            if( !$("input[name='consignee']").last().val().length  ){

                $("#formErrTxt").empty();
                $("#formErrTxt").append('收件人欄位為必填。');
                $('.modal').modal('open');                 
                $("#checkOutBtn").prop('disabled', false);          
                return false;
            }

            if( !$("input[name='address']").last().val().length ){
            
                $("#formErrTxt").empty();
                $("#formErrTxt").append('收件地址欄位為必填。');
                $('.modal').modal('open');                      
                $("#checkOutBtn").prop('disabled', false);          
                return false;           
            }
        /*
        if( !$("input[name='email']").last().val().length ){
            
            cAlert("電子郵件欄位為必填。");
            return false;           
        }

        regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if( !regex.test($("input[name='email']").last().val()) ){

            cAlert("電子郵件格式錯誤,請再次確認。");
            return false; 
        }
        */
            if( !$("input[name='mobile']").last().val().length){
            
                $("#formErrTxt").empty();
                $("#formErrTxt").append('手機欄位為必填。');
                $('.modal').modal('open');                   
                $("#checkOutBtn").prop('disabled', false);          
                return false;           
            }

        
            regex = /^[09]{2}[0-9]{8}$/;
            
            if (!regex.test($("input[name='mobile']").last().val()) ){

                $("#formErrTxt").empty();
                $("#formErrTxt").append('手機格式錯誤,請再次確認。');
                $('.modal').modal('open');                  
                $("#checkOutBtn").prop('disabled', false);          
                return false;  
            }

            if( carruer_type == '2'){

                var regexp = /^[A-Z]{2}[0-9]{14}$/;
            
                if( !regexp.test( $("#eiCode").val() ) ){

                    $("#formErrTxt").empty();
                    $("#formErrTxt").append('自然人憑證格式錯誤,請再次確認。<br> 自然人憑證格式為:<br> 2碼大寫英文 + 14碼由0 ~ 9數字所組成 <br>範例:AW12556987322213');
                    $('.modal').modal('open');                    
                    $("#checkOutBtn").prop('disabled', false);          
                    return false;               
                }
             
            }        
            
            if( carruer_type == '3'){
            
                var regexp = /^\/{1}[0-9A-Z\.\-\+]{7}$/;

                if( !regexp.test( $("#eiCode").val() ) ){

                    $("#formErrTxt").empty();
                    $("#formErrTxt").append('手機載具格式錯誤,請再次確認。 <br> 手機載具格式為:<br> 以 / 開頭 + 7碼由 0~9、A~Z(大寫)、符號: . + - 所組成<br>範例:/94168A+');
                    $('.modal').modal('open');                      
                    $("#checkOutBtn").prop('disabled', false);          
                    return false;               
                }       

            }
        }

        if (form.find('input[name="payment"][type!="hidden"]').length > 0 && form.find('input[name="payment"]:checked').length < 1) {
            
            $("#formErrTxt").empty();
            $("#formErrTxt").append('尚未選取付款方式');
            $('.modal').modal('open');   
            $("#checkOutBtn").prop('disabled', false);      
            return false;
        }
        
    });

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




/*----------------------------------------------------------------
 | 根據選取的電子發票格式呈現不同欄位
 |----------------------------------------------------------------
 |
 */
function showdiv( ){

    var nowinv = $("#carruer_type").val();
    
    $(".invTool").hide();

    if( nowinv == 2 ){

        $("#invText1").show();
        $("#eiCode").show();
    }

    if( nowinv == 3 ){

        $("#invText2").show();
        $("#eiCode").show();
    }

    if( nowinv == 4 ){

        $("#loveCode").show();
        $("#loveText").show();
    }    

}
</script>
@endsection