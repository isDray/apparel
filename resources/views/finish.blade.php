@extends('layout.layoutNoLeft')

@section('title', "$title")

@section('description', '')
@section('keyword', '')

@section('content')

<div class="row">
<div class="col s12 m12 l12 page_main">
    
    <!-- 訂單結果清單 -->
    <div class="row">
        <div class="col s12 m12 l8 offset-l2 ">

            <ul class="collection with-header z-depth-2" id="finishCollection">
                <li class="collection-header  pink accent-1"><h6>訂購完成</h6></li>

                    <li id="emptyCart" class="collection-item">
                        <h5 id="orderSnText">感謝您在本店購物！您的訂單已提交成功，請記住您的訂單編號：{{$order_sn}}</h5>
                        <h6 id="orderSubText">您選定的配送方式為：{{$ship_way}} 您選定的支付方式為：{{$pay_way}} 您的應付款金額為：${{$order_amount}}</h6>
                        <p> 
                        {!!$shipTip!!}    
                        <br>或您可於9:00~1800 直接撥打客服專線:04-8740413或0915-588683 完成確認。
                        @if( $showDoneString == true )
                        <br>(限選擇超商取貨者，如不便接聽亦可直接傳簡訊告知"訂單編號確認訂單"亦可，收到簡訊後將直接出貨)
                        @endif
                        </p>
                        
                        @if( $pay_online != false)
                        {!!$pay_online!!}                       
                        @endif
                    </li> 
            </ul>

        </div>
    </div>
    <!-- 訂單結果清單結束 -->


</div>
</div>

@endsection

@section('selfCss')
<link rel="stylesheet" type="text/css" href="{{ asset('css/finish.css') }}">
@endsection

@section('selfJs')
<script type="text/javascript">
$(document).ready(function(){
});
</script>

@if( $pay_online != false)
<script type="text/javascript">
$(window).on('load',function(){
    $("#__allpayForm").submit();   
});
</script>
@endif

@endsection