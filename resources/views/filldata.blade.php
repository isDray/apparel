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
                    <input type="radio" name="shipping" id="shipbox{{$shipping_listk}}">
                    <label class='shipLabel' for="shipbox{{$shipping_listk}}" >
                        {{$shipping_listv['shipping_name']}}
                        <br>
                        {{$shipping_listv['shipping_fee']}}
                        <br>
                        消費滿{{$shipping_listv['shipping_fee_free']}}免運
                    </label>

                    <span class='hideInput'>
                        

                        
                        <table class="striped">
  
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
    



    /*----------
     |
     |
     |
     */
    $(".shipBox").click(function(){
        
        var chsShip = $(this);

        // 清空收貨人訊息區塊
        $("#consigneeArea").empty();

        // 取消鎖定
        $(".hideInput input").removeAttr("disabled");
        
        var tmpHTML = chsShip.children('.hideInput').html();

        $("#consigneeArea").html( tmpHTML );
        
        // 恢復鎖定
        $(".hideInput input").attr("disabled","disabled");
    })

});
</script>
@endsection