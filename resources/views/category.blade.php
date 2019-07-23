@extends('layout.layout')

@section('title', "$title")

@section('description', '')
@section('keyword', '')

@section('content')

<div class="row">

<div class="col s12 m12 l12 page_main">
    
    @foreach( $goods as $goodk => $good)
    <a href="{{url('/showGoods'.'/'.$good['goods_id'])}}">
    <div class="col s6 m6 l3">
        <div class="card hoverable ">
            <div class="card-image">
        
                <picture>
                    <source media="(max-width: 600px)" srcset="https://***REMOVED***.com/***REMOVED***/{{$good['goods_thumb']}}">
                    <source media="(max-width: 992px)" srcset="https://***REMOVED***.com/***REMOVED***/{{$good['goods_img']}}">
                    <img src="https://***REMOVED***.com/***REMOVED***/{{$good['original_img']}}" alt="{{$good['goods_name']}}}">   
                </picture>  

                <span class="card-title">Card Title</span>
        
            </div>
            
            <div class="card-content">
                
                <p class="truncate">{{ $good['goods_name'] }} </p>
                <h5 class="right-align pink-text text-lighten-2">${{ intval($good['shop_price']) }}</h5>
            </div>
            
            <div class="card-action">
                <a class="pink lighten-2 waves-effect waves-light btn-small single_add_btn" goods_id="{{ $good['goods_id'] }}" ><i class="material-icons left">add_shopping_cart</i>加入購物車</a>
            </div>

      </div>
    </div> 
    </a>   
    @endforeach

</div>


</div>

<!-- 分頁 -->
<div class="row">
    <div class="col s12 m12 l12 center-align">
    {!!$pageHtml!!}
    </div>
</div>
@endsection

@section('selfCss')
<link rel="stylesheet" type="text/css" href="{{ asset('css/index.css') }}">
@endsection

@section('selfJs')
<script type="text/javascript">

$(document).ready(function(){

    scrollHright = $(".navbar-fixed").height();

    $('.scrollspy').scrollSpy({scrollOffset:scrollHright});
    /*
    $('.carousel').carousel({ 'shift':200,
        'duration':200,
        'indicators':true,
    });
    
    $('.carousel.carousel-slider').carousel({
        fullWidth: true,
        indicators: true
    });

    var instance = M.Carousel.getInstance($('.carousel.carousel-slider'));
    
    $(".pre").click(function(){
        instance.prev(1);
    });

    $(".next").click(function(){
        instance.next(1);
    });
    */
    elementPosition = $('.pin-top').offset();
    $('.pin-top').css('position', 'fixed').css('top', scrollHright+15);    




    /*----------------------------------------------------------------
     | 計算要用的高度
     |----------------------------------------------------------------
     | DOM 載入完成之後 , 計算出所需高度
     |
     |
     */
    ci_width = $(".card-image").first().width();
    console.log( ci_width );
    $(".card-image").height( ci_width );


    /*----------------------------------------------------------------
     | 加入購物車
     |----------------------------------------------------------------
     | 點擊加入購物車按鈕後 , ajax 執行加入購物車動作
     |
     */
    $(".add_btn").click(function(e){
        
        var quick = 0;
        var spec  = [];
        var goods_id = $(this).attr('goods_id');
        var number = 1;
        var parent = 0;
             
        var request = $.ajax({
            url: "{{url('/addToCart')}}",
            method: "POST",
            data: { 
                    _token : "{{ csrf_token() }}",
                    goods  : { quick:quick,
                               spec :spec,
                               goods_id : goods_id,
                               number   : number,
                               parent   : parent

                             }
                  },
            dataType: "json"
        });
 
        request.done(function( msg ) {

            console.log( msg );
        });
 
        request.fail(function( jqXHR, textStatus ) {
            console.log( "Request failed: " + textStatus );
        });
    });
});

</script>
@endsection