@extends('layout.layout')

@section('title', "$title")

@section('description', '')
@section('keyword', '')

@section('content')

<div class="row">

<div class="col s12 m9 l10">
<!-- 首頁第一層 -->
<div class='row scrollspy ' id="introduction">

    <div id="indexCarousel" class="carousel carousel-slider center col s12 m12 ">
        <a class="carousel-item" href="#one!"><img src="https://***REMOVED***.com/***REMOVED***/data/afficheimg/20181121pldish.jpg"></a>
        <a class="carousel-item" href="#two!"><img src="https://***REMOVED***.com/***REMOVED***/data/afficheimg/20180914ihoisd.jpg"></a>
        <a class="carousel-item" href="#three!"><img src="https://***REMOVED***.com/***REMOVED***/data/afficheimg/20181025kttqec.jpg"></a>

        <div class='pre'>
            <i class="medium material-icons  blue-grey-text text-lighten-5">chevron_left</i>
        </div>

        <div class='next'>
            <i class="medium material-icons  blue-grey-text text-lighten-5">chevron_right</i>
        </div>
    </div>
     
</div>
<!-- /首頁第一層 -->


<!-- 最新商品 -->
<div class='row scrollspy' id="structure">

  @foreach( $firstTens as $firstTenk => $firstTen)

  <div class="col s6 m6 l4">
      <div class="card hoverable ">
        <div class="card-image">
        
        <picture>
            <source media="(max-width: 600px)" srcset="https://***REMOVED***.com/***REMOVED***/{{$firstTen['goods_thumb']}}">
            <source media="(max-width: 992px)" srcset="https://***REMOVED***.com/***REMOVED***/{{$firstTen['goods_img']}}">
            <img src="https://***REMOVED***.com/***REMOVED***/{{$firstTen['original_img']}}" alt="Flowers" data-src="">   
        </picture>  

        <span class="card-title">Card Title</span>
        
        </div>
        <div class="card-content">
          <p class="truncate">{{ $firstTen['goods_name'] }} </p>
          <h5 class="right-align pink-text text-lighten-2">${{ intval($firstTen['shop_price']) }}</h5>
        </div>
        <div class="card-action">
          <a class="pink lighten-2 waves-effect waves-light btn-small single_add_btn" goods_id="{{ $firstTen['goods_id'] }}" ><i class="material-icons left">add_shopping_cart</i>加入購物車</a>
        </div>
      </div>
  </div>
  @endforeach
</div>  
<!-- /最新商品 -->
</div>

<div class="col hide-on-small-only m3 l2">
  <ul class="section table-of-contents pin-top scrollspy">
    <li><a href="#introduction">回到頂端</a></li>
    <li><a href="#structure">推薦商品</a></li>
  </ul>
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

    elementPosition = $('.pin-top').offset();
    $('.pin-top').css('position', 'fixed').css('top', scrollHright+15);    




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