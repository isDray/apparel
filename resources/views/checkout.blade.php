@extends('layout.layoutNoLeft')

@section('title', "$title")

@section('description', '')
@section('keyword', '')

@section('content')

<div class="row">
<div class="col s12 m12 l12 page_main">
    
    <!-- 購買清單區塊 -->
    <div class="row">
        <div class="col s12 m12 l8 offset-l2 ">

            <ul class="collection with-header z-depth-2" id="chekoutCollection">
                <li class="collection-header  pink accent-1"><h6>購物車清單</h></li>
                @if(Session::has('cart') && count(Session::get('cart')) > 0 )
                                    
                    @php
                        $cartTotal = 0;
                    @endphp
   
                    @foreach( Session::get('cart') as $cartdata)
                    <li class='cartItem'>
                        <div class='row'>
                        <div class='col s12 m3 l2 checkoutImgBox'>
                            <img src="https://***REMOVED***.com/***REMOVED***/{{ $cartdata['thumbnail'] }}">
                        </div>
                        <div class='col s12 m9 l5 goodsNameBox' >
                            <p class='cartGoods' >{{ $cartdata['name'] }}</p>
                        </div>
                        <div class='col s4 m3 l1'>
                            <p>價格:{{ $cartdata['goodsPrice'] }}</p>
                            <!-- <p class='cartGoodsList right-align 'style='padding-right:30px;'>{{ $cartdata['goodsPrice'] }} * {{ $cartdata['num'] }} = {{ $cartdata['subTotal'] }}  </p> -->
                        </div>
                        <div class='col s4 m3 l2'>
                            <p class='checkoutItemNum'>數量:</p>
                            <select name='num' class='checkoutNum' goods_id="{{$cartdata['id']}}"> 
                                <option value="{{ $cartdata['num'] }}" selected>{{ $cartdata['num'] }}</option>
                            </select>                            

                        </div>
                        <div class='col s4 m3 l1'>
                            <p>小計:{{ $cartdata['subTotal'] }}</p>
                        </div>    
                        <div class='col s6 m3 l1'>
                            <p class='cartBtnBox'><a class='waves-effect waves-light btn red accent-3 chkout_rm_goods' goods_id="{{$cartdata['id']}}"><i class="material-icons dp48">delete_forever</i></a></p>
                        </div> 
                        </div>                                            
                    </li>
                    
                    @php
                        $cartTotal += $cartdata['subTotal'];
                    @endphp

                    @endforeach
                    <!-- 總和區塊 -->
                    <li>
                        <div class='row'>
                            <div class='col s4 offset-s8 m3 offset-m6 l1 offset-l10 checkoutImgBox'>
                                總和:{{$cartTotal}}
                            </div>                            
                        </div>
                    </li>
                    <!-- 總和區塊結束 -->

                    <!-- 去結帳按鈕 -->
                    <li>
                        <div class='row center-align'>
                            <a class="waves-effect waves-light btn pink accent-1" href="{{url('/fillData')}}">前往結帳</a>                           
                        </div>
                    </li>                    
                    <!-- 去結帳按鈕結束 -->
                @else
                    <li id="emptyCart" class="collection-item">
                        <h4>購物車目前尚無商品</h4>
                    </li> 
                @endif

            </ul>

        </div>
    </div>
    <!-- 購買清單區塊結束 -->


</div>
</div>

@endsection

@section('selfCss')
<link rel="stylesheet" type="text/css" href="{{ asset('css/checkout.css') }}">
@endsection

@section('selfJs')
<script type="text/javascript">
$(document).ready(function(){
    
    /*----------------------------------------------------------------
     | 動態抓取數量
     |----------------------------------------------------------------
     |
     |
     */
    $('body').on('mouseover', '.checkoutNum', function() {
        
        var goods_id  =  $(this).attr('goods_id');
        var selectNum =  $(this);
        var nowSelect =  $(this).val();

        var ajaxGetGoodsNum = $.ajax({
            url: "{{url('/getGoodsNum')}}",
            method: "POST",
            data: { 
                _token : "{{ csrf_token() }}",
                goods_id : goods_id 
            },
            dataType: "json"
        });
 
        ajaxGetGoodsNum.done(function( res ) {
            
            if( res[0] == true ){
                
                selectNum.empty();
                for (var i = 1; i <= res[1]; i++) {
                    
                    if( i == nowSelect){
                        
                        selectNum.append("<option selected >"+i+"</option>");

                    }else{

                        selectNum.append("<option>"+i+"</option>");
                    }
                };

            }

        });
 
        ajaxGetGoodsNum.fail(function( jqXHR, textStatus ) {
            //alert( "Request failed: " + textStatus );
        });        
    });




    /*----------------------------------------------------------------
     | 切換數量
     |----------------------------------------------------------------
     |
     |
     */
    $('body').on('change', '.checkoutNum', function() {
        
        var wantNum = $(this).val();
        var goods_id  =  $(this).attr('goods_id');

        var ajaxGoodsNumChange = $.ajax({
            url: "{{url('/changeGoodsNum')}}",
            method: "POST",
            data: { 
                _token : "{{ csrf_token() }}",
                goods_id : goods_id ,
                wantNum  : wantNum ,
            },
            dataType: "json"
        });
 
        ajaxGoodsNumChange.done(function( res ) {
            
            if( res[0] == true ){
                M.toast({html: '<i class="material-icons">check</i>'+res[1] , classes:'cusNoticeS' });
                location.reload();
            }else{
                M.toast({html: '<i class="material-icons">check</i>'+res[1] , classes:'cusNoticeE' });
            }

        });
 
        ajaxGoodsNumChange.fail(function( jqXHR, textStatus ) {
            //alert( "Request failed: " + textStatus );
        });          

    });




    /*----------------------------------------------------------------
     | 移除購物車商品
     |----------------------------------------------------------------
     | 將商品由購物車移除後 , 重整頁面
     |
     */
    $('body').on('click', '.chkout_rm_goods', function() {
        
        var rm_goods_id = $(this).attr('goods_id');
        var chkoutRmAjax = $.ajax({
            
            url: "{{url('/rmFromCart')}}",
            method: "POST",
            data: { _token : "{{ csrf_token() }}",
                    goods_id : rm_goods_id 
            },
            dataType: "json"
        });
 
        chkoutRmAjax.done(function( res ) {

            if( res[0] == true ){
                M.toast({html: '<i class="material-icons">check</i>'+res[1] , classes:'cusNoticeS' });
                location.reload();
            }

        });
 
        chkoutRmAjax.fail(function( jqXHR, textStatus ) {
            //alert( "Request failed: " + textStatus );
        });

    });
});
</script>
@endsection