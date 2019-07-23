@extends('layout.layout')

@section('title', "$title")

@section('description', '')
@section('keyword', '')

@section('content')

<div class="row">

<div class="col s12 m12 l12 page_main">
    
    <div class="col s12 m12 l9 #f5f5f5 grey lighten-4" id='goodsInfo'>
        
        <!-- 商品名稱 -->
        <div class='col s12 m12 l12 '>
            <h1>{{$goods['goods_name']}}</h1>
        </div>
        <!-- 商品名稱結束 -->

    <!-- 主圖呈現 -->
    <div class="col s12 m12 l5">
        
        <div class="card-panel " id="goodsPicBox">
            
            <picture>
                <source media="(max-width: 600px)" srcset="https://***REMOVED***.com/***REMOVED***/{{$goods['goods_thumb']}}">
                <source media="(max-width: 992px)" srcset="https://***REMOVED***.com/***REMOVED***/{{$goods['goods_img']}}">
                <img src="https://***REMOVED***.com/***REMOVED***/{{$goods['original_img']}}" alt="{{$goods['goods_name']}}">   
            </picture>    

        </div>

    </div>
    <!-- 主圖呈現 -->

    <!-- 操作區塊 -->
    <div class="col s12 m12 l7">

      <div class="card-panel" id='operateBox'>
            
            <h2>商品售價:{{ round($goods['shop_price']) }}</h2>

            <h2>商品編號:{{ $goods['goods_sn'] }}</h2>
           
            <p>數量:
                 
                @if( $goods['goods_number'] > 0)
                <select name='num' id='num'> 
                    @for( $num = 1 ; $num <= $goods['goods_number'] ; $num ++ )
                    <option value="{{$num}}" >{{$num}}</option>
                    @endfor
                </select>
                @else
                <span id='prepare' > 補貨中 </span>
                @endif

            </p>
            <span class="waves-effect waves-light btn pink accent-1  @if( $goods['goods_number'] < 1) disabled @endif inner_add_to_cart" value="{{ $goods['goods_id'] }}">加入購物車</span>

      </div>

    </div>
    <!-- 操作區塊 -->

    <!-- 介紹區塊 -->
    <div class="col s12 m12 l12">
        <div class="card-panel" id='detail_box'>
        @foreach( $goodsImgs as $goodsImgk => $goodsImg )
        <picture>
            <!-- <source media="(max-width: 600px)" srcset="https://***REMOVED***.com/***REMOVED***/{{$goodsImg['thumb_url']}}"> -->
            <source media="(max-width: 992px)" srcset="https://***REMOVED***.com/***REMOVED***/{{$goodsImg['img_url']}}">
            <img src="https://***REMOVED***.com/***REMOVED***/{{$goodsImg['img_original']}}" alt="{{$goods['goods_name']}}-詳細圖片-{{$goodsImgk+1}}">   
        </picture>          
        @endforeach
        {!!$goods['goods_desc']!!}
        </div>
    </div>
    <!-- 介紹區塊結束 -->

    </div>


</div>


</div>

@endsection

@section('selfCss')
<style type="text/css">
#goodsInfo > div > h1 {
    font-size: 2em;
}
#goodsPicBox > picture > img {
    object-fit:contain;
    max-width: 100%;
}
select{
    display: inline-block!important;
    width: auto!important;
    border:1px solid #a0a0a0;
    height: auto;
}
#operateBox p , h2{
    font-size: 20px;
    font-weight: 900;
}
#prepare{
    color:red;
}
#description{

    overflow: hidden;
}
#detail_box > picture > img {

    max-width: 100%;
}


</style>
@endsection

@section('selfJs')
<script type="text/javascript">

$(document).ready(function(){

    /*----------------------------------------------------------------
     | 計算要用的高度
     |----------------------------------------------------------------
     | DOM 載入完成之後 , 計算出所需高度
     |
     |
     */
    PB_height = $("#goodsPicBox").height();
    $("#operateBox").height( PB_height );
    



    /*----------------------------------------------------------------
     | 加入購物車
     |----------------------------------------------------------------
     | 點擊加入購物車按鈕後 , ajax 執行加入購物車動作
     |
     */
    $(".inner_add_to_cart").click(function(e){
        
        //M.toast({html: '加入購物車' , classes:'cusNotice' })
        
        var goods_id = $(this).attr('value');
        var number   = $("#num").val();
        console.log( number );

        var request = $.ajax({
            url: "{{url('/addToCart')}}",
            method: "POST",
            data: { 
                    _token : "{{ csrf_token() }}",
                    
                    goods_id : goods_id,
                    number   : number,

                             
                  },
            dataType: "json"
        });
 
        request.done(function( res ) {

            if( res[0] == true ){
                
                M.toast({html: '<i class="material-icons">check</i>'+res[1] , classes:'cusNoticeS' });

                // 清空原始購物車
                $("#dropdown1").empty();

                // 產生新購物車
                newList = "";

                tmpCartTotal = 0;

                $.each(res[2], function( reCartK , reCartV ) {                    
                    newList += "<li class='cartItem'>";
                    newList += "<img src='https://***REMOVED***.com/***REMOVED***/"+reCartV['thumbnail']+"'>";
                    newList += "<div class='cartTextBox'>";
                    newList += "<p class='cartGoods' style='padding-right:30px;' >"+reCartV['name']+"</p>";
                    newList += "<p class='cartGoodsList right-align 'style='padding-right:30px;'>"+reCartV['goodsPrice']+"*"+reCartV['num']+"="+reCartV['subTotal']+"</p>";
                    newList += "</div>";
                    newList += "</li>";
                    tmpCartTotal += reCartV['subTotal'];
                }); 
                
                newList += "<li>";
                newList += "<div class='right-align' style='padding-right:15px;'>共:"+tmpCartTotal+"</div>";
                newList += "<div class='right-align'><a href='{{url('/checkout')}}' class='waves-effect waves-light btn'>去結帳</a></div>";
                newList += "</li>";

                $("#dropdown1").append(newList);

            }else{
                
                M.toast({html: '<i class="material-icons">close</i>'+res[1] , classes:'cusNoticeE' });

            }

        });
 
        request.fail(function( jqXHR, textStatus ) {
            //console.log( "Request failed: " + textStatus );
        });
    });
});

</script>
@endsection