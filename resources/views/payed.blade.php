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

            <ul class="collection with-header z-depth-2" id="payedCollection">
                @if( $res == true)
                <li class="collection-header teal lighten-1"><h6>付款成功</h6></li>
                <li id="emptyCart" class="collection-item">
                    <h5>付款已完成 , 訂單編號: {{$order_sn}}</h5>
                </li>
                @else
                <li class="collection-header  red accent-3"><h6>付款失敗</h6></li>
                <li id="emptyCart" class="collection-item">
                    <h5>付款失敗</h5>
                </li>                 
                @endif

            </ul>

        </div>
    </div>
    <!-- 訂單結果清單結束 -->


</div>
</div>

@endsection

@section('selfCss')
<link rel="stylesheet" type="text/css" href="{{ asset('css/payed.css') }}">
@endsection

@section('selfJs')
<script type="text/javascript">
$(document).ready(function(){
});
</script>
@endsection