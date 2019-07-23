@extends('layout.layoutNoLeft')

@section('title', "$title")

@section('description', '')
@section('keyword', '')

@section('content')

<div class="row">
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

                     

        </div>
    </div>
    <!-- 收貨資料區塊結束 -->

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
});
</script>
@endsection