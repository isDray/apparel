<?php

namespace App\Http\Controllers;
use DB; 
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnterController extends Controller
{   
    public $showNum = 20;
    /*----------------------------------------------------------------
     | 控制器初始化
     |----------------------------------------------------------------
     | 於此階段設定各種通用的資訊
     |
     */
	public function __construct(){
        
        // 取出服飾分類
        $categorys = DB::table('category')->select('cat_id', 'cat_name')->whereIn('cat_id', [25,103,31,104,113,27,29])->get();
       
        $categorys = json_decode( $categorys , TRUE);

        View::share('categorys', $categorys);
    }




    /*----------------------------------------------------------------
     | 首頁控制器
     |----------------------------------------------------------------
     |
     */
    public function index( Request $request ){
        //$request->session()->forget('cart');

        // 取出服飾分類
        $firstTens = DB::table('goods')->where('cat_id', 25)->limit(6)->offset(60)->orderBy('add_time','DESC')->get();
        
        $firstTens = json_decode( $firstTens , TRUE);
        
        //md5(uniqid(mt_rand(), true));

        return view('index')->with([ 'firstTens' => $firstTens,
        	                         'title'     => '首頁'
        	                       ]);


    }



    
    /*----------------------------------------------------------------
     | 加入購物車
     |----------------------------------------------------------------
     |
     */
    public function addToCart( Request $request ){
        
        $validator = Validator::make($request->all(), [
            'goods_id' => 'bail|required|exists:goods,goods_id',
            'number'   => 'bail|required',
        ],
        [   'goods_id.required' => '缺少商品編號 , 請重新整理後再嘗試 ',
            'goods_id.exists'   => '商品編號不存在 , 請重新整理後再嘗試 ',
            'number.required'   => '缺少商品數量 , 請重新整理後再嘗試 ',
        ]
        );

        if ($validator->fails()) {

            $error = $validator->errors()->first();

            return json_encode( [ false , $error ] );
                
        }

        // 檢查數量是否足夠
        $goodsDetail = DB::table('goods')->where('goods_id', $request->goods_id)->first();
        
        if( $goodsDetail->goods_number < $request->number ){
            
            return json_encode( [false, '數量不足 , 請重新選擇商品數量'] );
        }
        
        // 如果已經有購物車session , 做增減即可
        if( $request->session()->has('cart') ){

            $tmpcart = $request->session()->get('cart');
            
            // 如果已經有購物車了 , 則判斷此商品是否已經存在購物車內
            if( array_key_exists("$request->goods_id", $tmpcart) ) {

                $totalNum = $tmpcart[$request->goods_id]['num'] + $request->number;

                if( ($goodsDetail->goods_number - $totalNum ) < 0 ){
                
                    return json_encode( [false , '目前此商品庫存只剩'.$goodsDetail->goods_number.'個 , 請調整訂購數量' , ] );
                   

                }  
                              
                

                $tmpcart[$request->goods_id]['num'] = $totalNum;
                $tmpcart[$request->goods_id]['goodsPrice'] = round($goodsDetail->shop_price);
                $tmpcart[$request->goods_id]['subTotal'] = round($goodsDetail->shop_price * $totalNum);

            }else{

                $tmpcart[ $goodsDetail->goods_id ] = [ 'name'      => $goodsDetail->goods_name,
                                                       'thumbnail' => $goodsDetail->goods_thumb,
                                                       'num'       => $request->number,
                                                       'goodsSn'   => $goodsDetail->goods_sn,
                                                       'goodsPrice'=> round($goodsDetail->shop_price),
                                                       'subTotal'  => round($request->number * $goodsDetail->shop_price),
                                                       'id'        => $goodsDetail->goods_id,
                                                     ];  
            }
        }else{
        // 如果沒有購物車session , 則立刻增加一組session
            
            $tmpcart[ $goodsDetail->goods_id ] = [ 'name'      => $goodsDetail->goods_name,
                                                   'thumbnail' => $goodsDetail->goods_thumb,
                                                   'num'       => $request->number,
                                                   'goodsSn'   => $goodsDetail->goods_sn,
                                                   'goodsPrice'=> round($goodsDetail->shop_price),
                                                   'subTotal'  => round($request->number * $goodsDetail->shop_price),
                                                   'id'        => $goodsDetail->goods_id,
                                                 ];            
        }
       
        ksort($tmpcart);
        $request->session()->put('cart', $tmpcart);

        return json_encode( [true, '成功加入購物車' , $tmpcart ] );

        
    }



    
    /*----------------------------------------------------------------
     | 移除商品 
     |----------------------------------------------------------------
     | 將商品由購物車內移除後 , 回傳最新購物車資料
     |
     */
    public function rmFromCart( Request $request ){
        
        if( $request->session()->has('cart') ){
            
            $tmpcart = $request->session()->get('cart');
            
            // 如果要刪除的商品有在購物車內 , 就直接執行刪除動作
            if( array_key_exists( $request->goods_id , $tmpcart ) ){
                
                unset( $tmpcart[ $request->goods_id ] );
                ksort($tmpcart);

            }

            $request->session()->put('cart', $tmpcart);
            
            return json_encode( [true, '成功移除商品' , $tmpcart ] );
        }  

    }
    



    /*----------------------------------------------------------------
     | 動態取得商品數量
     |----------------------------------------------------------------
     |
     |
     */
    public function getGoodsNum( Request $request ){
        
        $validator = Validator::make($request->all(), [
            'goods_id' => 'bail|required|exists:goods,goods_id',
        ],
        [   'goods_id.required' => '缺少商品編號 , 請重新整理後再嘗試 ',
            'goods_id.exists'   => '商品編號不存在 , 請重新整理後再嘗試 ',
        ]
        );

        if ($validator->fails()) {

            $error = $validator->errors()->first();

            return json_encode( [ false , $error ] );
                
        } 

        // 如果沒有錯誤 , 就開始撈取庫存
        $num = DB::table('goods')->select('goods_number')->where('goods_id', $request->goods_id )->first()->goods_number;

        return json_encode( [ true , $num ] );
    }




    /*----------------------------------------------------------------
     | 修改購物車數量
     |----------------------------------------------------------------
     |
     |
     */
    public function changeGoodsNum( Request $request ){
        
        if( $request->session()->has('cart') ){

            $tmpcart = $request->session()->get('cart');
            
            // 如果已經有購物車了 , 則判斷此商品是否已經存在購物車內
            if( array_key_exists("$request->goods_id", $tmpcart) ) {

                $goodsDetail = DB::table('goods')->where('goods_id', $request->goods_id)->first();

                $totalNum = $request->wantNum;

                if( ($goodsDetail->goods_number - $totalNum ) < 0 ){
                
                    return json_encode( [false , '目前此商品庫存只剩'.$goodsDetail->goods_number.'個 , 請調整訂購數量' , ] );

                }else{

                    $tmpcart[$request->goods_id]['num'] = $totalNum;
                    $tmpcart[$request->goods_id]['goodsPrice'] = round($goodsDetail->shop_price);
                    $tmpcart[$request->goods_id]['subTotal'] = round($goodsDetail->shop_price * $totalNum);   

                    ksort($tmpcart);
                    $request->session()->put('cart', $tmpcart);    

                    return json_encode( [true , '修改數量成功' , ] );             
                }                  

            }else{

                return json_encode( [false,'無法進行此操作']);
            }            

        }else{

            return json_encode( [false,'無法進行此操作']);
        }

    }




    /*----------------------------------------------------------------
     | 分類頁面
     |----------------------------------------------------------------
     |
     */
    public function showCategorys( Request $request ){
           
        // 計算總筆數
        $total = DB::table('goods')->where('cat_id', $request->id )->where('is_on_sale',1)->count();
        
        // 防呆機制
        if( $request->page > ceil($total/$this->showNum )){

            $request->page = ceil($total/$this->showNum );
        }

        if( $request->page < 1 ){

            $request->page = 1;
        }

        // 取出頁面商品
        $goods = DB::table('goods')->where('cat_id', $request->id )
                                   ->where('is_on_sale',1)
                                   ->skip( ( $request->page - 1) * $this->showNum )
                                   ->take( $this->showNum )
                                   ->get();

        $goods = json_decode( $goods , TRUE);
        
        $pageHtml = $this->getPageList( $request->page , $this->showNum , $total , 6 , 6 , url('/showCategorys/'.$request->id.'/') );

        return view('category')->with([ 'goods' => $goods,
                                        'title' => '分類頁面',
                                        'pageHtml' => $pageHtml,
                                     ]);        

    }




    /*----------------------------------------------------------------
     | 商品內頁
     |----------------------------------------------------------------
     |
     */
    public function showGoods( Request $request ){
        
        // 確認商品是否真的存在 , 如果不存在直接導回首頁
        $goodsExists = DB::table('goods')
                     ->where('goods_id', $request->id )
                     ->where('is_on_sale',1)->first();

        if( $goodsExists == NULL ){

            return redirect('/');
        }

        $goods = (Array)$goodsExists;
        
        // 取出商品所有圖檔
        $goodsImg = DB::table('goods_gallery')
                     ->where('goods_id', $request->id )
                     ->get();

        $goodsImgs = json_decode($goodsImg,True);
        

        // 如果商品存在 , 則直接呈現內頁
        return view('goods')->with([ 'goods' => $goods,
                                     'title' => "{$goods['goods_name']}",
                                     'goodsImgs' => $goodsImgs,
                                   ]);         
    }
    


    
    /*----------------------------------------------------------------
     | 最後確認頁面
     |----------------------------------------------------------------
     |
     |
     */
    public function checkout( Request $request ){
        
        return view('checkout')->with([ 'title' => '購物車檢視'
                                      ]);
    }
    



    /*----------------------------------------------------------------
     | 填寫收獲及付款資訊
     |----------------------------------------------------------------
     |
     */
    public function fillData( Request $request ){
        
        // 如果根本沒有購物車 , 直接返回首頁
        if( $request->session()->has('cart') && count($request->session()->get('cart')) > 0 ){
            
            // 取得國家級縣市
            $countrys = $this->get_regions();
            $countrys = json_decode($countrys,true);
            
            if( $request->session()->has('chsCountry') ){

                $tmpCountry = $request->session()->get('chsCountry');

            }else{
                
                $tmpCountry = 1;
            }

            // $request->session()->put('chsCountry', 833);
            // $request->session()->forget('chsCountry');
            if( $tmpCountry == 1){
                
                $provinces = $this->get_regions( 1 , $tmpCountry );
                $provinces = json_decode($provinces,true);
            
            }else{

                $provinces = false;
            }
            
            // var_dump($request->session()->get('chsProvince'));
            // 設定預設的州
            if( $request->session()->has('chsProvince') ){
               
                $tmpProvince = $request->session()->get('chsProvince');

            }else{
                
                $tmpProvince = 807;
            }
            
            if( ($tmpProvince == 807 && $tmpCountry ==1) || ($tmpProvince == 808 && $tmpCountry ==1) ){
                
                $citys = $this->get_regions( 2 , $tmpProvince);
                $citys = json_decode($citys,true);
            
            }else{
             
                $citys = false;
            }
            
            // 選取
            if( $tmpCountry != 1 ){
                $tmpProvince = 0;
            }

            $region = array($tmpCountry, $tmpProvince , 0, 0);

            $shipping_list     = $this->available_shipping_list($region);
            

            $shipping_list = json_decode( $shipping_list , true );
            
            // 移除不要用的配送方式
            $no_display = array('flat_lan','ecan_lan','hct','hct_shun','kerry_tj','acac'); 

            foreach ($shipping_list AS $key => $val){   
                
                $no_shipping_display = in_array( $val['shipping_code'] , $no_display) ? '1':'';
                
                if($no_shipping_display == 1 ){
                     
                    unset($shipping_list[$key]);

                }else{

                    // 計算價格
                    $shipping_cfg = $this->unserialize_config($val['configure']);
                    var_dump($val['shipping_code']);
                    var_dump($shipping_cfg);
                    echo "<br>----------------------------------<br>";


                }            

            }


            return view('filldata')->with([ 'title'     => '填寫資料收貨',
                                            'countrys'  => $countrys,
                                            'provinces' => $provinces,
                                            'citys'     => $citys
                                          ]);            


        }else{

            // 如果購物車不存在直接跳回首頁
            return redirect('/');

        }
    }
    



    /*----------------------------------------------------------------
     | 修改選取的地區
     |----------------------------------------------------------------
     |
     |
     */
    public function areaChange( Request $request ){
        
        if( $request->type == 1){

            $request->session()->put('chsCountry', $request->area);

            return json_encode( [true,''] );

        }

        if( $request->type == 2){

            $request->session()->put('chsProvince', $request->area);

            return json_encode( [true,''] );            

        }

        if( $request->type == 3){

            $request->session()->put('chsCity', $request->area);

            return json_encode( [true,''] );            

        }        
    }




    /*----------------------------------------------------------------
     | 計算分類
     |----------------------------------------------------------------
     |
     |
     */
    public function getPageList( $_now , $_show,  $_total , $_left , $_right  , $_url ){
    
        if( empty($_left) ){

            $_left = 3;
        }

        if( empty($_right) ){

            $_right = 3;
        }

        $totalPage = ceil($_total/$_show);

        $pageHtml  = "<ul class='pagination'>";

        // 判斷是否還有上一頁
        if(  $_now - 1 > 0 ){

            $pageHtml .= "<li ><a href='".$_url."/".($_now-1)."'><i class='material-icons'>chevron_left</i></a></li>";

        }else{

            $pageHtml .= "<li class='disabled' ><a><i class='material-icons'>chevron_left</i></a></li>";
        }
        
        // 計算前方頁面
        for ( $i = $_left; $i > 0 ; $i--) { 
            
            if( $_now - $i > 0){
                
                $pageHtml .= "<li ><a href='".$_url."/".($_now-$i)."'>".($_now - $i)."</a></li>";

            }

        }
        
        // 當下頁面
        $pageHtml .= "<li class='active'><a href='".$_url."/".$_now."'>". $_now ."</a></li>";

        // 計算後方頁面
        for ( $i = 1 ; $i <= $_right ; $i ++) { 
            
            if( $_now + $i <= $totalPage){
                
                $pageHtml .= "<li ><a href='".$_url."/".($_now+$i)."'>".($_now + $i)."</a></li>";

            }

        }        


        // 判斷是否還有下一頁
        if(  $_now + 1 > $totalPage ){
            
            $pageHtml .= "<li class='disabled' ><a><i class='material-icons'>chevron_right</i></a></li>";

        }else{

            $pageHtml .= "<li ><a href='".$_url."/".($_now+1)."'><i class='material-icons'>chevron_right</i></a></li>";
        }        

        $pageHtml .= "</ul>" ;

        return $pageHtml;
    }




    /*----------------------------------------------------------------
     | 取得國家列表
     |----------------------------------------------------------------
     |
     */
    public function get_regions($type = 0, $parent = 0){

        // $sql = 'SELECT region_id, region_name FROM ' . $GLOBALS['ecs']->table('region') .
        //     " WHERE region_type = '$type' AND parent_id = '$parent'";
        
        $returnData = DB::table('region')
                      ->where('region_type', $type )
                      ->where('parent_id', $parent )
                      ->get();

        return $returnData;
    }




    /*----------------------------------------------------------------
     | 取得可用的配送方式
     |----------------------------------------------------------------
     |
     */
    public function available_shipping_list($region_id_list){

        $returnData = DB::table('shipping')
                        ->leftJoin('shipping_area', 'shipping_area.shipping_id', '=', 'shipping.shipping_id')
                        ->leftJoin('area_region', 'area_region.shipping_area_id', '=', 'shipping_area.shipping_area_id')

                        ->select(['shipping.shipping_id', 'shipping.shipping_code', 'shipping.shipping_name' , 'shipping.shipping_desc' , 'shipping.insure', 'shipping.support_cod', 'shipping_area.configure'])
                        /*->table('shipping_area as a')
                        ->table('area_region as r')*/
                        ->whereIn('area_region.region_id', $region_id_list )
                        ->where('shipping.enabled',1)
                        ->orderBy('shipping.shipping_order', 'asc')
                        ->get();

        return $returnData;
    }  




    /*----------------------------------------------------------------
     | 拆解設定
     |----------------------------------------------------------------
     |
     |
     */   
    public function unserialize_config($cfg){

        if (is_string($cfg) && ($arr = unserialize($cfg)) !== false){
            
            $config = array();

            foreach ($arr AS $key => $val){

                $config[$val['name']] = $val['value'];
            }

            return $config;
        
        }else{
            
            return false;
        }
    }
    



    /*----------------------------------------------------------------
     |
     |----------------------------------------------------------------
     |
     |
     */
    function shipping_fee($shipping_code, $shipping_config, $goods_weight, $goods_amount, $goods_number=''){

        if (!is_array($shipping_config)){
            $shipping_config = unserialize($shipping_config);
        }

        $filename = ROOT_PATH . 'includes/modules/shipping/' . $shipping_code . '.php';
        
        if (file_exists($filename)){

            include_once($filename);

            $obj = new $shipping_code($shipping_config);

            return $obj->calculate($goods_weight, $goods_amount, $goods_number);
        
        }else{
            
            return 0;
        }
    
    }     
}
