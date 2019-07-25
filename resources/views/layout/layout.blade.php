<!doctype html>
<html lang="{{ app()->getLocale() }}" >
    <head>
        
        <title>@yield('title') - {{config( 'app.name' )}}</title>
        <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
        
        
        <meta name="description" content="@yield('description')">
        <meta name="keywords" content="@yield('keyword')">
         
         
        <!-- 共用css -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{ asset('materialize/css/materialize.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/all.css') }}">
        <!-- /共用css -->

        <!-- 個人 css -->
        @yield('selfCss')        
        <!-- /個人 css -->

    </head>

    <body>
        
        <!-- 手機板專用購物車 -->
        <div id="mobileCartBox">
            
            <a class="dropdown-trigger btn-floating  pink darken-2" href='#' data-target='dropdown2' id='testBtn'>
                <i class="material-icons">shopping_cart</i>
            </a>

        </div>
        <!-- 手機板專用購物車結束 -->

        <ul id='dropdown2' class='dropdown-content collection'>
        @if(Session::has('cart'))
            @php
                $cartTotal = 0;
            @endphp
            @foreach( Session::get('cart')  as $cartdata)
            <li class='cartItem'>
                <img src="https://***REMOVED***.com/***REMOVED***/{{ $cartdata['thumbnail'] }}">
                <div class='cartTextBox'>
                    <p class='cartGoods' style='padding-right:30px;' >{{ $cartdata['name'] }}</p>
                    <p class='cartGoodsList right-align 'style='padding-right:30px;'>{{ $cartdata['goodsPrice'] }} * {{ $cartdata['num'] }} = {{ $cartdata['subTotal'] }}  </p>
                    <p class='cartBtnBox'><a class='waves-effect waves-light btn red accent-3 rm_goods' goods_id="{{$cartdata['id']}}">移除</a></p>
                </div>
            </li>
            @php
                $cartTotal += $cartdata['subTotal'];
            @endphp
            @endforeach
            <li>
            <div class='right-align' style='padding-right:15px;'>
                共:{{$cartTotal}}
            </div>
            <div class='right-align'>
                <a href="{{url('/checkout')}}" class="waves-effect waves-light btn">去結帳</a>
            </div>
            </li>
        @endif
        </ul>
        <ul id='dropdown1' class='dropdown-content collection'>
        @if(Session::has('cart'))
            @php
                $cartTotal = 0;
            @endphp
            @foreach( Session::get('cart')  as $cartdata)
            <li class='cartItem'>
                <img src="https://***REMOVED***.com/***REMOVED***/{{ $cartdata['thumbnail'] }}">
                <div class='cartTextBox'>
                    <p class='cartGoods' style='padding-right:30px;' >{{ $cartdata['name'] }}</p>
                    <p class='cartGoodsList right-align 'style='padding-right:30px;'>{{ $cartdata['goodsPrice'] }} * {{ $cartdata['num'] }} = {{ $cartdata['subTotal'] }}  </p>
                    <p class='cartBtnBox'><a class='waves-effect waves-light btn red accent-3 rm_goods' goods_id="{{$cartdata['id']}}">移除</a></p>
                </div>
            </li>
            @php
                $cartTotal += $cartdata['subTotal'];
            @endphp
            @endforeach
            <li>
            <div class='right-align' style='padding-right:15px;'>
                共:{{$cartTotal}}
            </div>
            <div class='right-align'>
                <a  href="{{url('/checkout')}}" class="waves-effect waves-light btn">去結帳</a>
            </div>
            </li>
        @endif
        </ul>

        <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper pink accent-1">

                <a href="{{url('/')}}" class="brand-logo">Logo</a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    @foreach( $categorys as $categoryk => $category)
                    <li><a href="{{url('/showCategorys/'.$category['cat_id'].'/1')}}">{{$category['cat_name']}}</a></li>
                    @endforeach
                    <li>
                        <a class="dropdown-trigger btn-floating  pink darken-2" href='#' data-target='dropdown1' id='cartBtn'><i class="material-icons">shopping_cart</i></a>
                    </li>
                </ul>
                
            </div>
        
        </nav>
        </div>

        <ul class="sidenav" id="mobile-demo">
            @foreach( $categorys as $categoryk => $category)
            <li><a href="{{url('/showCategorys/'.$category['cat_id'].'/1')}}">{{$category['cat_name']}}</a></li>
            @endforeach
        </ul>


        <!-- 主要區塊 -->
        <div id="mainArea" class="row">
            <div class='col s0 m3 l2 hide-on-small-only'>
                <ul id="leftMenu" class="collection with-header hide-on-small-only">
                    
                    <li class="collection-header">商品分類</li>
                    @foreach( $categorys as $categoryk => $category)
                    <a href="{{url('/showCategorys/'.$category['cat_id'].'/1')}}"> <li class="collection-item">{{$category['cat_name']}}</li> </a>
                    @endforeach
                </ul>
            </div>

            <div class='col s12 m9 l10'>
            @yield('content')
            </div>
        </div>
        <!-- /主要區塊 -->
        
        <!-- footer -->
        <footer class="page-footer pink accent-1">
          <div class="container">
            <div class="row">
              <div class="col l6 s12">
                <h5 class="white-text">Footer Content</h5>
                <p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer content.</p>
              </div>
              <div class="col l4 offset-l2 s12">
                <h5 class="white-text">Links</h5>
                <ul>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 1</a></li>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 2</a></li>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 3</a></li>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 4</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="footer-copyright">
            <div class="container">
            © 2014 Copyright Text <span id="app">@{{ message }}</span>
            <a class="grey-text text-lighten-4 right" href="#!">More Links</a>
            </div>
          </div>
        </footer>        
        <!-- /footer -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/vue.min.js') }}"></script>
        <script src="{{ asset('materialize/js/materialize.min.js') }}"></script>
        <script type="text/javascript">      
            $(document).ready(function(){
                
                $('.sidenav').sidenav();$('.dropdown-toggle').dropdown()

                $('.dropdown-trigger').dropdown({coverTrigger:false,constrainWidth:false,closeOnClick:false});

                // $("#cartBtnMobile").dropdown({coverTrigger:false,constrainWidth:false,closeOnClick:false});
                /*----------------------------------------------------------------
                 | 單筆加入購物車
                 |----------------------------------------------------------------
                 | 點擊加入購物車按鈕後 , ajax 執行加入購物車動作
                 |
                 */
                $(".single_add_btn").click(function(e){
        
                    //M.toast({html: '加入購物車' , classes:'cusNotice' })
        
                    var goods_id = $(this).attr('goods_id');
                    var number   = 1;

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
                
                            M.toast({html: '<i class="material-icons">check</i>'+res[1] , classes:'cusNoticeS'});

                            // 清空原始購物車
                            $("#dropdown1").empty();
                            $("#dropdown2").empty();

                            // 產生新購物車
                            newList = "";

                            tmpCartTotal = 0;

                            $.each(res[2], function( reCartK , reCartV ) {                    
                                
                                newList += "<li class='cartItem'>";
                                newList += "<img src='https://***REMOVED***.com/***REMOVED***/"+reCartV['thumbnail']+"'>";
                                newList += "<div class='cartTextBox'>";
                                newList += "<p class='cartGoods' style='padding-right:30px;'>"+reCartV['name']+"</p>";
                                newList += "<p class='cartGoodsList right-align 'style='padding-right:30px;'>"+reCartV['goodsPrice']+"*"+reCartV['num']+"="+reCartV['subTotal']+"</p>";
                                newList += "<p class='cartBtnBox'><a class='waves-effect waves-light btn red accent-3 rm_goods' goods_id='"+reCartV['id']+"'>移除</a></p>";
                                newList += "</div>";
                                newList += "</li>";
                                tmpCartTotal += reCartV['subTotal'];
                            }); 
                
                            newList += "<li>";
                            newList += "<div class='right-align' style='padding-right:15px;'>共:"+tmpCartTotal+"</div>";
                            newList += "<div class='right-align'><a href='{{url('/checkout')}}' class='waves-effect waves-light btn'>去結帳</a></div>";
                            newList += "</li>";

                            $("#dropdown1").append(newList);
                            $("#dropdown2").append(newList);
                        }else{
                
                            M.toast({html: '<i class="material-icons">close</i>'+res[1] , classes:'cusNoticeE' });

                        }

                    });
 
                    request.fail(function( jqXHR, textStatus ) {
                        //console.log( "Request failed: " + textStatus );
                    });
                });                

                /***************/


                /*----------------------------------------------------------------
                 | 移除商品
                 |----------------------------------------------------------------
                 | 在購物車中移除商品 , 並且重新呈現購物車
                 |
                 */
                $('body').on('click', '.rm_goods', function() {
                    
                    // 取的要刪除的商品ID
                    var delGoods = $(this).attr('goods_id');

                    // 開始啟動ajax 
                    var request = $.ajax({
                        url: "{{url('/rmFromCart')}}",
                        method: "POST",
                        data: { 
                            _token : "{{ csrf_token() }}",
                            goods_id : delGoods,
                        },
                        dataType: "json"
                    });
 
                    request.done(function( res ) {

                        if( res[0] == true ){
                
                            M.toast({html: '<i class="material-icons">check</i>'+res[1] , classes:'cusNoticeS' });

                            // 清空原始購物車
                            $("#dropdown1").empty();
                            $("#dropdown2").empty();

                            // 產生新購物車
                            newList = "";

                            tmpCartTotal = 0;

                            $.each(res[2], function( reCartK , reCartV ) {                    
                                
                                newList += "<li class='cartItem'>";
                                newList += "<img src='https://***REMOVED***.com/***REMOVED***/"+reCartV['thumbnail']+"'>";
                                newList += "<div class='cartTextBox'>";
                                newList += "<p class='cartGoods' style='padding-right:30px;'>"+reCartV['name']+"</p>";
                                newList += "<p class='cartGoodsList right-align 'style='padding-right:30px;'>"+reCartV['goodsPrice']+"*"+reCartV['num']+"="+reCartV['subTotal']+"</p>";
                                newList += "<p class='cartBtnBox'><a class='waves-effect waves-light btn red accent-3 rm_goods' goods_id='"+reCartV['id']+"'>移除</a></p>";
                                newList += "</div>";
                                newList += "</li>";
                                tmpCartTotal += reCartV['subTotal'];
                            }); 
                
                            newList += "<li>";
                            newList += "<div class='right-align' style='padding-right:15px;'>共:"+tmpCartTotal+"</div>";
                            newList += "<div class='right-align'><a href='{{url('/checkout')}}' class='waves-effect waves-light btn'>去結帳</a></div>";
                            newList += "</li>";

                            $("#dropdown1").append(newList);
                            $("#dropdown2").append(newList);

                            ddinstant1 = M.Dropdown.getInstance( $('.dropdown-trigger')[0] );
                            ddinstant1.recalculateDimensions();

                            ddinstant2 = M.Dropdown.getInstance( $('.dropdown-trigger')[1] );
                            ddinstant2.recalculateDimensions();
                            /*ddinstant2 = M.Dropdown.getInstance( $('#dropdown2') );
                            ddinstant2.recalculateDimensions();                            */
                        }
                    });     
                    
                    request.fail(function( jqXHR, textStatus ) {
                        //console.log( "Request failed: " + textStatus );
                    });
                });
            });
        </script>

        <!-- 個人 js -->
        @yield('selfJs')        
        <!-- /個人 js -->
    </body>

</html>