<?php

namespace App\Http\Controllers;
use DB; 
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Redirect;
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

                $request->session()->put('chsCountry', 1);
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
                    //var_dump($val['shipping_code']);

                    
                    
                    // 根據是否有購物車session , 決定計算運費的資料
                    if( $request->session()->has('cart') ){
                        
                        $calcCart = $request->session()->get('cart');

                    }else{
                        
                        $calcCart = array();
                    }

                    // 計算運費
                    $tmpfee = $this->shipping_fee( $calcCart ,$shipping_cfg );
                    
                    $shipping_list[$key]['shipping_fee'] = $tmpfee['fee'];
                    $shipping_list[$key]['shipping_fee_free'] = $tmpfee['free'];


                }            

            }
            
            // 取得付款資料
            $payment_list = $this->available_payment_list(1,'');

            if( isset($payment_list) ){

                foreach ($payment_list as $key => $payment){
                    /*
                    if ($payment['is_cod'] == '1'){
                        
                        $payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
                    }
                    */

                    /* 如果有易寶神州行支付 如果訂單金額大於300 則不顯示 */
                    if ($payment['pay_code'] == 'yeepayszx' && $total['amount'] > 300){
                        
                        unset($payment_list[$key]);
                    }
                    
                    /* 如果為其它付款，則不顯示*/
                    if ($payment['pay_code'] == 'other_cod'){
                        
                        unset($payment_list[$key]);
                    } 

                }
            }

            return view('filldata')->with([ 'title'     => '填寫資料收貨',
                                            'countrys'  => $countrys,
                                            'provinces' => $provinces,
                                            'citys'     => $citys,
                                            'shipping_list' => $shipping_list,
                                            'payment_list'  => $payment_list
                                          ]);            


        }else{

            // 如果購物車不存在直接跳回首頁
            return redirect('/');

        }
    }




    /*----------------------------------------------------------------
     | 寫入訂單
     |----------------------------------------------------------------
     |
     */
    public function done( Request $request ){
        


        // 檢查購物車有商品 , 如果沒有購物車或者沒有商品就視為違法操作直接導回首頁
        if( !$request->session()->has('cart') || count( $request->session()->get('cart') ) < 1 ){
            
            return redirect('/');
        }

        // 驗證機制 , 根據配送方式不同需要驗證的欄位也不同
        if( !isset( $request->shipping ) || empty( $request->shipping ) ){

            return redirect()->back();
        }

        if( in_array( $request->shipping , ['17','18','19'] )){
            
            $validationCond = [
                'shipping'    => 'required', 
                'super_name2' => 'required',
                'super_addr2' => 'required',
                'super_type'  => 'required',
                'super_no2'   => 'required',
                'super_consignee' => 'required',
                'super_mobile'    => 'required|regex:/^09[0-9]{8}$/',
                'super_email'     => 'nullable|email',
                'payment'      => 'required', 
                'carruer_type' => 'required',
            ];
            
            $validationMsg = [   'shipping.required' => '配送方式尚未選取',
                'super_name2.required' => '超商尚未選取',
                'super_addr2.required' => '超商地址尚未選取',
                'super_type.required'  => '超商地址尚未選取',
                'super_no2.required'   => '超商地址尚未選取',
                'super_consignee.required' => '收貨人姓名為必填',
                'super_mobile.required' => '手機欄位為必填',
                'super_mobile.regex'=> '手機格式錯誤',
                'super_email.email' => '電子郵件格式錯誤',
                'payment.required'  => '付款方式為必選',
                'carruer_type.required' => '電子發票需選取'
            ];

            // 自然人憑證
            if(  $request->carruer_type == 2 ){

                $validationCond['ei_code'] = 'required|regex:/^[A-Z]{2}[0-9]{14}$/';

                $validationMsg['ei_code.required'] = '自然人憑證需填寫';

                $validationMsg['ei_code.regex'] = '自然人憑證格式錯誤';
            }

            // 手機載具
            if(  $request->carruer_type == 3 ){

                $validationCond['ei_code'] = 'required|regex:/^\/{1}[0-9A-Z\.\-\+]{7}$/';

                $validationMsg['ei_code.required'] = '手機載具需填寫';

                $validationMsg['ei_code.regex'] = '手機載具格式錯誤';                
            }            
            
            if( !empty($request->inv_payee) || !empty($request->inv_content) ){

                $validationCond['inv_payee']   = 'required';

                $validationCond['inv_content'] = 'required';

                $validationMsg['inv_payee.required']   = '如果需開立統編 , 統編為必填';

                $validationMsg['inv_content.required'] = '如果需開立統編 , 公司抬頭為必填';

            }
            $validator = Validator::make($request->all(), $validationCond , $validationMsg );

            if ($validator->fails()) {
                
                //var_dump($validator->errors());
                
            }
        }else{
            $validationCond = [
                'shipping'    => 'required', 
                'consignee'   => 'required',
                'address'     => 'required',
                'mobile'    => 'required|regex:/^09[0-9]{8}$/',
                'email'     => 'nullable|email',
                'payment'      => 'required', 
                'carruer_type' => 'required',
            ];
            
            $validationMsg = [   
                'shipping.required' => '配送方式尚未選取',
                'consignee.required' => '收貨人姓名為必填',
                'address.required' => '收貨人地址為必填',
                'mobile.required' => '手機欄位為必填',
                'mobile.regex'=> '手機格式錯誤',
                'email.email' => '電子郵件格式錯誤',
                'payment.required'  => '付款方式為必選',
                'carruer_type.required' => '電子發票需選取'
            ];
            // 自然人憑證
            if(  $request->carruer_type == 2 ){

                $validationCond['ei_code'] = 'required|regex:/^[A-Z]{2}[0-9]{14}$/';

                $validationMsg['ei_code.required'] = '自然人憑證需填寫';

                $validationMsg['ei_code.regex'] = '自然人憑證格式錯誤';
            }

            // 手機載具
            if(  $request->carruer_type == 3 ){

                $validationCond['ei_code'] = 'required|regex:/^\/{1}[0-9A-Z\.\-\+]{7}$/';

                $validationMsg['ei_code.required'] = '手機載具需填寫';

                $validationMsg['ei_code.regex'] = '手機載具格式錯誤';                
            }            
            
            if( !empty($request->inv_payee) || !empty($request->inv_content) ){

                $validationCond['inv_payee']   = 'required';

                $validationCond['inv_content'] = 'required';

                $validationMsg['inv_payee.required']   = '如果需開立統編 , 統編為必填';

                $validationMsg['inv_content.required'] = '如果需開立統編 , 公司抬頭為必填';

            }
            $validator = Validator::make($request->all(), $validationCond , $validationMsg );       

            if ($validator->fails()) {
                
                //var_dump($validator->errors());
                
            }     
        } 

        if ($validator->fails()) {
                
            //return redirect()->back(); 
        }  
        
        //var_dump($request->all());
        // 如果要開統編 , 則不開立一般發票
        if( $request->inv_payee != '' ||  $request->inv_content != ''  ) $request->carruer_type = '0' ;

        $inv_type = ( $request->inv_payee != '' ||  $request->inv_content != ''  ) ? '三聯式發票' : '一般發票開立' ;
        
        $consignee = [];
        // 初始最後要寫入的訂單資訊 
        $order = array(
        'shipping_id'     => intval( $request->shipping ),
        'pay_id'          => intval( $request->payment ),
        'pack_id'         => 0,//isset($_POST['pack']) ? intval($_POST['pack']) : 0,
        'card_id'         => 0,//isset($_POST['card']) ? intval($_POST['card']) : 0,
        'card_message'    => '',//trim($_POST['card_message']),
        'surplus'         => 0.00,//isset($_POST['surplus']) ? floatval($_POST['surplus']) : 0.00,
        'integral'        => 0,//isset($_POST['integral']) ? intval($_POST['integral']) : 0,
        'bonus_id'        => 0,//isset($_POST['bonus']) ? intval($_POST['bonus']) : 0,
        'need_inv'        => 0,//empty($_POST['need_inv']) ? 0 : 1,
        'inv_type'        => $inv_type,
        'inv_payee'       => trim($request->inv_payee),
        'inv_content'     => trim($request->inv_content),
        'postscript'      => trim($request->postscript),
        'how_oos'         => '',//isset($_LANG['oos'][$_POST['how_oos']]) ? addslashes($_LANG['oos'][$_POST['how_oos']]) : '',
        'need_insure'     => 0,//isset($_POST['need_insure']) ? intval($_POST['need_insure']) : 0,
        'user_id'         => 0,//$_SESSION['user_id'],
        'add_time'        => (time() - date('Z')),
        'order_status'    => 0,
        'shipping_status' => 0,
        'pay_status'      => 0,
        'agency_id'       => 0,//get_agency_by_regions(array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district'])),
        'carruer_type'    => trim($request->carruer_type),
        'ei_code'         => trim($request->ei_code),
        'from_ad_od_sn'   => 0,
        'from_ip'         => $this->real_ip(),
        );
        
        $order['extension_code'] = '';
        $order['extension_id'] = 0;
        
        // 由於沒有會員 , 固定不會有積分相關資料
        $order['surplus']  = 0;
        $order['integral'] = 0;
        
        // 費用計算
        $total = $this->order_fee( $request->session()->get('cart') );
        
        $order['bonus'] = 0;
        $order['goods_amount'] = $total['goods_price'];
        $order['discount'] = 0;
        $order['surplus']  = 0;
        $order['tax']      = 0;
        $order['rent_total'] = 0;
        $order['bonus_id'] = 0;

        $shipcode = DB::table('shipping')
                 ->select('shipping_code','shipping_name')
                 ->where('shipping_id','=',$request->shipping)
                 ->where('enabled','=',1)
                 ->first();

        $order['shipping_code'] = $shipcode->shipping_code;
        
        $super_name      =  trim($request->super_name2);
        $super_addr      =  trim($request->super_addr2);
        $super_no        =  trim($request->super_no2);
        $super_consignee =  trim($request->super_consignee);
        $super_mobile    =  trim($request->super_mobile);
        $super_email     =  trim($request->super_email);

        if( $order['shipping_code']  == 'super_get' ){

        }elseif( $order['shipping_code']  == 'super_get2' ){

        }elseif( $order['shipping_code']  == 'super_get3' ){

        }

        $showDoneString = False ; 

        if( $order['shipping_code']  == 'super_get' || $order['shipping_code']  == 'super_get2' || $order['shipping_code']  == 'super_get3' ){
            
            $showDoneString = True;
        }

        // 除了超商配送外  , 其他物流
        if( $order['shipping_code']  == 'ecan'  || $order['shipping_code']  == 'postoffice' || $order['shipping_code']  == 'flat'
         || $order['shipping_code']  == 'flat_lan' || $order['shipping_code']  == 'hct' || $order['shipping_code']  == 'hct_shun'
         || $order['shipping_code']  == 'kerry_tj' || $order['shipping_code']  == 'tjoin' || $order['shipping_code']  == 'acac'
         ){ 

            // 整理收貨人資料
            $consignee['address']   = isset($request->address) ? trim($request->address) : '' ;
            $consignee['consignee'] = isset($request->consignee) ? trim($request->consignee) : '' ;
            $consignee['email']     = isset($request->email) ? trim($request->email) : '' ;
            $consignee['zipcode']   = isset($request->zipcode) ? trim($request->zipcode) : '' ;
            $consignee['tel']       = isset($request->tel) ? trim($request->tel) : '' ; 
            $consignee['best_time'] = isset($request->best_time) ? trim($request->best_time) : '' ;
            $consignee['mobile']    = isset($request->mobile) ? trim($request->mobile) : '' ;
            $consignee['sign_building'] = isset($request->sign_building) ? trim($request->sign_building) : '' ;

        }       

        $order['shipping_name'] = $shipcode->shipping_name;

        if( $order['shipping_code']  == 'super_get' || $order['shipping_code']  == 'super_get2' || $order['shipping_code']  == 'super_get3' ){
            
            $order['shipping_type'] = addslashes( $request->super_type );
            $order['shipping_super_name'] = addslashes( $request->super_name2 );
            $order['shipping_super_no'] = addslashes( $request->super_no2);
            $order['shipping_super_addr'] = addslashes( $request->super_addr2 );
            $order['address'] = addslashes( $request->super_name2 ).'_'.addslashes( $request->super_addr2 );

        }   
        
        // 收貨人訊息轉換
        foreach ($consignee as $key => $value){
            
            $order[$key] = addslashes($value);
        }

       
        /*超商寫入*/
        if( $order['shipping_code']  == 'super_get' || $order['shipping_code']  == 'super_get2' || $order['shipping_code']  == 'super_get3' ){

            $order['mobile']    = addslashes( trim( $request->super_mobile ));
            $order['consignee'] = addslashes( trim( $request->super_consignee));
            $order['email']     = addslashes( trim( $request->super_email));
            $order['tel']   = '';
        }           

        $order['scode'] = 9453;
        
        // 運費
        $allFee = $this->available_shipping_list( [$request->country , $request->province ] );
        
        $allFee = json_decode( $allFee , true );
        
        // var_dump($allFee);
        foreach ($allFee as $allFeek => $allFeev) {
            
            if( $allFeev['shipping_id'] == $request->shipping ){

                $shipping_cfg = $this->unserialize_config($allFeev['configure']);

                $tmpfee = $this->shipping_fee( $request->session()->get('cart') ,$shipping_cfg );
                
                break;
            }
        }
        
        $order['shipping_fee'] = ($order['goods_amount'] >= $tmpfee['free'])?0:$tmpfee['fee'];

        $order['insure_fee'] = 0;

        if ($order['pay_id'] > 0){
            
            $payment = DB::table('payment')->where('pay_id','=',$order['pay_id'])->first();
            
            $order['pay_name'] = addslashes( $payment->pay_name );
        }    

        $order['order_amount'] = $order['goods_amount'] + $order['shipping_fee'] + $order['tax'];

        // 保留原始手機及家電
        $mobileForMail   = $order['mobile'];
        $telForMail      = $order['tel'];
    
        // 採用訂單編號執行加密後,再對該筆訂單進行更新動作
        $order['mobile'] = empty($order['mobile']) ? '' : $this->mobileEncode( '' , $order['mobile'] );
        $order['tel']    = empty($order['tel']) ?    '' : $this->telEncode   ( '' , $order['tel']);

        // 開始寫入訂單
        $error_no = 0;
        
        // $this->get_order_sn();
        unset($order['need_inv']);
        unset($order['need_insure']);
        unset($order['shipping_code']);
        

        
        $inSwitch = 1;
        
        do{
            
            $order['order_sn'] = $this->get_order_sn();

            try {
    
                $res = DB::table('order_info')->insertGetId( $order );
                
                $lastID = $res;

                $inSwitch = 0;

            } catch (\Exception $e) {
                
                if (strpos( $e->getMessage() ,'1062 Duplicate entry') == false) {

                    $inSwitch = 0;

                }
    
            }

        }while ( $inSwitch == 1 );
        
        $goodList = array_keys( $request->session()->get('cart') );

        $goods = DB::table('goods')
                    ->select('goods_id','goods_name','goods_sn','goods_number','market_price','is_real','extension_code','rent_price','upon_stock','in_stock')
                    ->whereIn('goods_id',$goodList)
                    ->get();
        
        // 轉換為陣列
        $goods = json_decode( $goods , true );

        foreach ($goods as $goodk => $good) {
            
            $goods[$goodk]['order_id'] = $lastID;
            $goods[$goodk]['product_id'] = 0;
        

        }
        // 將商品細項寫入訂單
        exit;
        $sql = "INSERT INTO " . $ecs->table('order_goods') . "( " .
                "order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
                "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id, start_date, end_date, rent_price, upon_stock, in_stock) ".

            " SELECT '$new_order_id', c.goods_id, c.goods_name, c.goods_sn, c.product_id, c.goods_number, c.market_price, c.goods_price * IFNULL( DATEDIFF( c.end_date, c.start_date ), 1), c.goods_attr, c.is_real, c.extension_code, c.parent_id, c.is_gift, c.goods_attr_id, c.start_date, c.end_date, g.rent_price, c.upon_stock, c.in_stock ".
            " FROM " . $ecs->table('cart') ." c LEFT JOIN ". $ecs->table('goods') ." g ON c.goods_id = g.goods_id ".
            " WHERE c.session_id = '".SESS_ID."' AND c.rec_type = '$flow_type'";
        
    
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
     | 修改選取的配送方式
     |----------------------------------------------------------------
     |
     */
    public function shipChange( Request $request ){
        

        $request->session()->put('chsShip', $request->ship);

        return json_encode( [true,''] );

        
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




    /*----------
     |
     |
     |
     */
    public function available_payment_list($support_cod, $cod_fee = 0, $is_online = false){
        
        $sqlData = DB::table('payment')
                        ->select('pay_id', 'pay_code', 'pay_name', 'pay_fee', 'pay_desc', 'pay_config', 'is_cod')
                        ->where('enabled','=' , 1)
                        ->orderBy('pay_order', 'asc');
        

        if (!$support_cod){   

            // $sql .= 'AND is_cod = 0 '; // 如果不支持货到付款

            $sqlData->where('is_cod','=',0);
        }
        
        if ($is_online){

            // $sql .= "AND is_online = '1' ";
            $sqlData->where('is_online','=',1);
        }

        $returnDatas = $sqlData->get();
        
        $returnDatas = json_decode( $returnDatas , true );

        return $returnDatas;
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
     | 計算運費
     |----------------------------------------------------------------
     | 此運費計算模式為享愛網計費方式的精簡版本 , 由於在服飾網沒有會
     | 員等級 , 單純以價格計算 , 所以採用相對簡單的方式
     |
     */
    public function shipping_fee( $calcCarts , $cfg ){
        
        // 預設目前訂單為0元
        $total = 0;
        
        // 計算總價
        foreach ($calcCarts as $calcCartk => $calcCart) {
            
            $total += $calcCart['subTotal'];

        }
        
        if( $total >= $cfg['free_money'] ){

            return ['fee'=>0 , 'free'=>$cfg['free_money'] ];
        
        }else{

            return ['fee'=>$cfg['base_fee'] , 'free'=>$cfg['free_money'] ];
        }

    }    
    



    /*----------------------------------------------------------------
     | 開啟超商選擇
     |----------------------------------------------------------------
     |
     */
    public function storeMap( Request $request ){
        
        // 要執行的動作
        $status = 'get_store_map';
        
        // 如果是綠界回傳 , 則直接存進session即可
        if( isset($request->CVSStoreID) && isset($request->CVSStoreName) ){

            $status = 'store_call_back';
            //LogisticsSubType
            $CVSArr = [];
            
            $CVSArr['CVSStoreID']   = $request->CVSStoreID;
            $CVSArr['CVSStoreName'] = $request->CVSStoreName;
            $CVSArr['CVSAddress']   = $request->CVSAddress;

            $request->session()->put("{$request->LogisticsSubType}", $CVSArr );
        }        

        // 超商代碼
        $type   = $request->type;

        //裝置代碼
        $device = $request->device;

        
        // 如果不是要選取超商 , 則直接轉跳至收貨人訊息葉面即可
        if( $status == 'get_store_map' ){

            return view("storeMap")->with([
                                           'status' => $status,
                                           'type'   => $type,
                                           'device' => $device
                                         ]);

        }else{

            return redirect("/fillData");
        }

    }



    
    /*----------
     |
     |
     |
     */
    function real_ip(){

        static $realip = NULL;
    
        if ($realip !== NULL)
        {
            return $realip;
        }
    
        if(isset($_COOKIE['real_ipd']) && !empty($_COOKIE['real_ipd'])){
            $realip = $_COOKIE['real_ipd'];  
            return $realip;
        }
        
        if (isset($_SERVER))
        {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    
                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip)
                {
                    $ip = trim($ip);
    
                    if ($ip != 'unknown')
                    {
                        $realip = $ip;
    
                        break;
                    }
                }
            }
            elseif (isset($_SERVER['HTTP_CLIENT_IP']))
            {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }
            else
            {
                if (isset($_SERVER['REMOTE_ADDR']))
                {
                    $realip = $_SERVER['REMOTE_ADDR'];
                }
                else
                {
                    $realip = '0.0.0.0';
                }
            }
        }
        else
        {
            if (getenv('HTTP_X_FORWARDED_FOR'))
            {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            }
            elseif (getenv('HTTP_CLIENT_IP'))
            {
                $realip = getenv('HTTP_CLIENT_IP');
            }
            else
            {
                $realip = getenv('REMOTE_ADDR');
            }
        }
    
        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
        setcookie("real_ipd", $realip, time()+36000, "/");  /*添加*/
        return $realip;
    }
    



    /*----------
     |
     |
     |
     */
    function cart_goods(){
    // $sql = "SELECT c.* ," .
    //         " c.goods_price * c.goods_number AS subtotal, " .
    //         " g.rent_price * c.goods_number AS rent_total " .
    //         "FROM " . $GLOBALS['ecs']->table('cart') .' AS c ' .
    //         'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = c.goods_id '.
    //         " WHERE session_id = '" . SESS_ID . "' " .
    //         "AND rec_type = '$type'";

    // $arr = $GLOBALS['db']->getAll($sql);

    // /* 格式化价格及礼包商品 */
    // foreach ($arr as $key => $value)
    // {
    //     $ddays = ($value['start_date'] == '0000-00-00' && $value['end_date'] == '0000-00-00') ? 1 : diff_days($value['start_date'], $value['end_date']) ;
    //     $arr[$key]['goods_price']  = $value['goods_price'] * $ddays;
    //     $arr[$key]['subtotal']     = $value['subtotal'] * $ddays;
    //     $arr[$key]['formated_market_price'] = price_format($value['market_price'], false);
    //     //$arr[$key]['formated_goods_price']  = price_format($value['goods_price'], false);
    //     $arr[$key]['formated_subtotal']     = price_format($arr[$key]['subtotal'], false);

    //     if ($value['extension_code'] == 'package_buy')
    //     {
    //         $arr[$key]['package_goods_list'] = get_package_goods($value['goods_id']);
    //     }
    // }

    // return $arr;
}     
    



    /*----------------------------------------------------------------
     | 計算費用相關
     |----------------------------------------------------------------
     |
     */
    public function order_fee( $carts ){
        
        $total  = array('real_goods_count' => 0,
                        'gift_amount'      => 0,
                        'goods_price'      => 0,
                        'market_price'     => 0,
                        'discount'         => 0,
                        'pack_fee'         => 0,
                        'card_fee'         => 0,
                        'shipping_fee'     => 0,
                        'shipping_insure'  => 0,
                        'integral_money'   => 0,
                        'bonus'            => 0,
                        'surplus'          => 0,
                        'cod_fee'          => 0,
                        'pay_fee'          => 0,
                        'tax'              => 0
                  );  

        // 計算商品總價
        $tmpTotal = 0;
        
       // var_dump($carts);

        foreach ( $carts as $cartk => $cart ) {
            
            $tmpTotal += $cart['subTotal'] ;
        }
        
        $total['goods_price'] = $tmpTotal;

        return $total;
    }
 



    /*----------
     |
     |
     |
     */
    public function get_order_sn(){
        
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);

        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }




    /*----------------------------------------------------------------
     | 手機加密
     |----------------------------------------------------------------
     |
     */
    public function mobileEncode( $_key , $_num ){

        $_key = '8610';

        $idNums  = preg_split('//', $_key, -1, PREG_SPLIT_NO_EMPTY);

        $idSum   = 0;
  
        foreach ($idNums as $idNumk => $idNum) {
   
            $idSum += $idNum;

        }

        $position = $idSum % mb_strlen( $_num , "utf-8");
      
        if( $position == 0 ){
      
          $mergeNum = $_num.$_key;
      
          $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
          $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);  
          //return base64_encode(trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, md5($_key),$mergeNum, MCRYPT_MODE_ECB, $iv)));
          return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, md5($_key),$mergeNum, MCRYPT_MODE_ECB, $iv));
          
        }else{
         
          $mergeNum[0] = substr($_num, 0, $position);
          $mergeNum[1] = $_key;
          $mergeNum[2] = substr($_num, $position);
          $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
          $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);  
         
          //return base64_encode(trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, md5($_key),implode("",$mergeNum), MCRYPT_MODE_ECB, $iv)));
          return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, md5($_key),implode("",$mergeNum), MCRYPT_MODE_ECB, $iv));
      
        }
    }  




    /*----------------------------------------------------------------
     | 家電加密
     |----------------------------------------------------------------
     |
     */   
    function telEncode( $_key , $_num ){
    
        $_key = '8610';
        $idNums  = preg_split('//', $_key, -1, PREG_SPLIT_NO_EMPTY);
    
        $idSum   = 0;
        
        foreach ($idNums as $idNumk => $idNum) {
         
          $idSum += $idNum;
    
    
        }
        $position = $idSum % mb_strlen( $_num , "utf-8");
    
    
        if( $position == 0 ){
    
          $mergeNum = $_num.$_key;
    
          
          $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
          $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);  
          //return base64_encode(trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, md5($_key),$mergeNum, MCRYPT_MODE_ECB, $iv)));
          return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, md5($_key),$mergeNum, MCRYPT_MODE_ECB, $iv));
    
        }else{
    
          $mergeNum[0] = substr($_num, 0, $position);
          $mergeNum[1] = $_key;
          $mergeNum[2] = substr($_num, $position);
    
          $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
          $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    
          //return base64_encode(trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, md5($_key),implode("",$mergeNum), MCRYPT_MODE_ECB, $iv)));
          return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, md5($_key),implode("",$mergeNum), MCRYPT_MODE_ECB, $iv));
        }
    }     




    /*----------------------------------------------------------------
     | 測試用function
     |----------------------------------------------------------------
     |
     */
    public function test( Request $request ){
        
        var_dump( $request->all() );
    }
    
}
