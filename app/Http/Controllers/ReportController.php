<?php

namespace App\Http\Controllers;

use App\Models\AccountsTree;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyMovement;
use App\Models\EnterMoney;
use App\Models\EnterOld;
use App\Models\EnterWork;
use App\Models\ExitMoney;
use App\Models\ExitOld;
use App\Models\ExitWork;
use App\Models\Holder;
use App\Models\Item;
use App\Models\Karat;
use App\Models\Pricing;
use App\Models\Warehouse;
use App\Models\CompanyInfo;
use App\Models\ExitWorkDetails ;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    //
    public function item_list_report(){
        $karats = Karat::all();
        $categories = Category::all();
        $pricings = Pricing::all();
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.item_list_report' , compact('karats' , 'categories' , 'routes'));
    }
    public function item_list_report_search(Request $request){
        $items = Item::with('karat' , 'category') -> where('item_type' , '<>' , 2);

        if($request -> karat > 0) $items = $items -> where('karat_id' , '=' ,$request -> karat ) -> get();
        if($request -> category > 0) $items = $items -> where('category_id' , '=' ,$request -> category ) -> get();
        if($request -> code != null ) $items = $items -> where('code' , '=' , $request ->code ) -> get();
        if($request -> name != null) $items = $items->where('name_ar' , 'like' , '%'.$request -> name .'%') -> get();
        if($request -> weight > 0) $items = $items -> where('weight' , '=' ,$request -> weight ) -> get();

        if($request -> karat == 0  && $request -> category == 0 &&
            $request -> code == null && $request -> name == null && $request -> weight == 0){
            $data = $items -> get();

        } else {
            $data = $items ;
        }

        $fcode = $request -> fcode ?? '000001';
        $tcode = $request -> tcode ?? '999999';
        $items2 = [] ;


        foreach ($data as $item){
            if((int)$item -> code  >= (int) $fcode && (int)$item -> code  <= (int) $tcode){
                array_push($items2 , $item);
            }
        }


        if(!$request -> fcode && ! $request -> tcode ){
            $items2 = $data ;
        }

        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $company = CompanyInfo::all() -> first();

        return view('Report.item_list_report_result' , ['data' => $items2 , 'routes' => $routes , 'company' => $company])  ;

    }


    public function sold_items_report(){
        $karats = Karat::all();
        $categories = Category::all();
        $pricings = Pricing::all();
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.sold_item_list_report' , compact('karats' , 'categories' , 'routes'));
    }

    public function sold_items_report_search(Request $request){


        // $items = DB::table('items') -> join('karats' , 'items.karat_id' , '=' , 'karats.id')
        //     -> join('categories' , 'items.category_id' , '=' , 'categories.id')
        //     -> join('exit_work_details' , 'items.id' , '=','exit_work_details.item_id')
        //     -> select('items.*' , 'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' ,
        //         'categories.name_ar as category_name_ar' , 'categories.name_en as category_name_en', 'exit_work_details.bill_id as bill_id' )
        //     -> where('item_type' , '<>' , 2)
        //     -> where('state' , '=' , 0);

        $items = DB::table('exit_work_details')
            -> join('exit_works' , 'exit_work_details.bill_id' , '=' , 'exit_works.id')
            -> join('karats' , 'exit_work_details.karat_id' , '=' , 'karats.id')
            ->join('items' , 'exit_work_details.item_id' , '=' , 'items.id')
            -> select('items.*' , 'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' ,
                'exit_work_details.bill_id as bill_id' , 'exit_works.date as bill_date' ,'exit_works.bill_number as bill_no')
            -> where('exit_works.total_money' , '>' , 0) ;





        if($request -> karat > 0) $items = $items -> where('items.karat_id' , '=' ,$request -> karat ) -> get();
        // if($request -> category > 0) $items = $items -> where('items.category_id' , '=' ,$request -> category ) -> get();
        if($request -> code != null ) $items = $items -> where('items.code' , '=' , $request ->code ) -> get();
        if($request -> name != null) $items = $items->where('items.name_ar' , 'like' , '%'.$request -> name .'%') -> get();
        if($request -> weight > 0) $items = $items -> where('items.weight' , '=' ,$request -> weight ) -> get();


        if($request -> karat == 0  &&
            $request -> code == null && $request -> name == null && $request -> weight == 0){
            $data = $items -> get();

        } else {
            $data = $items ;
        }
        // foreach ($data as $item){
        //   $bill = ExitWork::find($item -> bill_id);
        //   $item -> bill_date = $bill -> date ;
        //     $item -> bill_no = $bill -> bill_number ;
        // }
        if($request -> has('isStartDate')) $data = $data -> where('bill_date' , '>=' , Carbon::parse($request -> StartDate) -> startOfDay());
        if($request -> has('isEndDate'))   $data = $data -> where('bill_date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);



        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }


        $company = CompanyInfo::all() -> first();
        return view('Report.sold_item_list_report_result' , compact('data' , 'routes' , 'period' , 'period_ar' , 'company'))  ;
    }

    public function sales_report(){
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.sales_report' , compact('routes'))  ;
    }
    public function sales_report_search(Request $request){
        $data = DB::table('exit_work_details')
            -> join('exit_works' , 'exit_work_details.bill_id' , '=' , 'exit_works.id')
            ->join('items' , 'exit_work_details.item_id' , '=' , 'items.id')
            ->join('karats' , 'exit_work_details.karat_id' , '=' , 'karats.id')
            ->select('exit_works.bill_number' , 'exit_works.date' , 'exit_works.id' ,  'exit_works.client_id as client_id',
                'exit_works.discount', 'items.name_ar as item_name_ar' , 'items.name_en as item_name_en'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'exit_work_details.weight' , 'exit_work_details.gram_price' ,
                'exit_work_details.gram_manufacture' , 'exit_work_details.gram_tax','exit_work_details.net_money' , 'exit_work_details.karat_id')
            -> where('exit_works.net_money' ,'>' , 0)
            -> orderBy('exit_works.id');


        $data2 = DB::table('exit_old_details')
            -> join('exit_olds' , 'exit_old_details.bill_id' , '=' , 'exit_olds.id')
            ->join('karats' , 'exit_old_details.karat_id' , '=' , 'karats.id')
            ->select('exit_olds.bill_number' , 'exit_olds.date' , 'exit_olds.discount' ,'exit_olds.id' , 'exit_olds.supplier_id as client_id'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'exit_old_details.weight' , 'exit_old_details.gram_price' ,
                'exit_old_details.gram_manufacture' , 'exit_old_details.gram_tax','exit_old_details.net_money' , 'exit_old_details.karat_id')
            -> orderBy('exit_olds.id');



        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        if($request -> has('isStartDate')) $data2 = $data2 -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data2 = $data2 -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        if($request ->FromBillNumber ) {
            $fromBill = substr($request -> FromBillNumber , 5  );
            $prefix = substr($request -> FromBillNumber , 0 , 5 );
            if($prefix  == 'SWSI-'){
                $bil = ExitWork::where('bill_number' , '=' , $request -> FromBillNumber) -> first();
                if($bil){
                    $data2 = [];
                    $data = $data -> where('exit_works.id' , '>=' , $bil -> id );
                }

            }
            else{
                $bil = ExitOld::where('bill_number' , '=' , $request -> FromBillNumber) -> first();
                if($bil){
                    $data= [];
                    $data2 = $data -> where('exit_olds.id' , '>=' , $bil -> id );
                }

            }
        }

        if($request ->ToBillNumber ) {
            $fromBill = substr($request -> ToBillNumber , 5  );
            $prefix = substr($request -> ToBillNumber , 0 , 5 );
            if($prefix  == 'SWSI-'){
                $bil = ExitWork::where('bill_number' , '=' , $request -> ToBillNumber) -> first();
                if($bil){
                    $data2 = [];
                    $data = $data -> where('exit_works.id' , '<=' , $bil -> id );

                }

            }
            else{
                $bil = ExitOld::where('bill_number' , '=' , $request -> ToBillNumber) -> first();
                if($bil){
                    $data= [];
                    $data2 = $data -> where('exit_olds.id' , '<=' , $bil -> id );
                }

            }
        }


        $bills = array();
        $data22 =[] ;





        foreach (is_array($data) ? $data   : $data -> get()  as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client =   $client -> name ;
            else
                $bill -> client = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }
        foreach ( is_array($data2) ? $data2   : $data2 -> get() as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client =   $client -> name ;
            else
                $bill -> client = '';

            $bill -> type = 0 ;
            $bill -> item_name_ar  = '--';
            $bill -> item_name_en  = '--';
            array_push($bills , $bill);
            array_push($data22 , $bill);

        }



        $all = $data -> get() -> merge($data22);

        $grouped_ar = $all   -> groupBy('karat_name_ar');
        $grouped_en = $all   -> groupBy('karat_name_en');

        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);



        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }

        $company = CompanyInfo::all() -> first();

        return view('Report.sales_report_result' , compact('bills', 'grouped_ar' ,'grouped_en' , 'routes' , 'period' , 'period_ar' ,'company' ))  ;
    }

    public function purchase_report(){
        $pricings = Pricing::all();
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.purchase_report' , compact('routes'))  ;
    }

    public function purchase_report_search(Request $request){
        $data = DB::table('enter_work_details')
            -> join('enter_works' , 'enter_work_details.bill_id' , '=' , 'enter_works.id')
            ->join('karats' , 'enter_work_details.karat_id' , '=' , 'karats.id')
            ->select('enter_works.bill_number' , 'enter_works.id' , 'enter_works.date' , 'enter_works.supplier_id as supplier_id'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'enter_work_details.weight' , 'enter_work_details.made_money' ,
                'enter_work_details.net_weight' , 'enter_work_details.net_money' , 'enter_work_details.karat_id' , 'enter_work_details.weight21') ;


        $data2 = DB::table('enter_old_details')
            -> join('enter_olds' , 'enter_old_details.bill_id' , '=' , 'enter_olds.id')
            ->join('karats' , 'enter_old_details.karat_id' , '=' , 'karats.id')
            ->select('enter_olds.bill_number'  , 'enter_olds.id', 'enter_olds.date' , 'enter_olds.client_id as supplier_id'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'enter_old_details.weight' , 'enter_old_details.made_money' ,
                'enter_old_details.net_weight' , 'enter_old_details.net_money' , 'enter_old_details.karat_id' , 'enter_old_details.weight21') ;



        if($request -> has('isStartDate')) $data = $data -> where('enter_works.date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('enter_works.date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());


        if($request -> has('isStartDate')) $data2 = $data2 -> where('enter_olds.date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data2 = $data2 -> where('enter_olds.date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());


        $bills = array();
        foreach ($data-> get() as $bill){
            $supplier = Company::find($bill -> supplier_id);
            if($supplier)
                $bill -> supplier =   $supplier -> name ;
            else
                $bill -> supplier = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }
        foreach ($data2 -> get() as $bill){
            $supplier = Company::find($bill -> supplier_id);
            if($supplier)
                $bill -> supplier =   $supplier -> name ;
            else
                $bill -> supplier = '';
            $bill -> type = 0 ;
            array_push($bills , $bill);
        }

        $all = $data -> get() -> merge($data2 -> get());

        $grouped_ar = $all   -> groupBy('karat_name_ar');
        $grouped_en = $all   -> groupBy('karat_name_en');

        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        return view('Report.purchase_report_result' , compact('bills', 'grouped_ar' ,'grouped_en' , 'routes' , 'period' , 'period_ar' , 'company'))  ;


    }
    public function vendor_account(){
        $vendors = Company::all();
        $pricings = Pricing::all();
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.vendor_account' , compact('vendors' , 'routes'));
    }

    public function vendor_account_search(Request $request){
        $client = Company::find($request -> vendor_id);
        $type = $client -> group_id ;
        $data = CompanyMovement::where('company_id' , '=' , $request -> vendor_id);

        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        $movements = $data -> get() ;
        $slag =  14;
        $subSlag = 145 ;


        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }

        $company = CompanyInfo::all() -> first();

        return view('company.accountMovement' , compact('type' , 'movements' , 'slag' , 'subSlag' , 'routes' , 'period' , 'period_ar', 'company'));
    }
    public function gold_stock_report(){

        $pricings = Pricing::all();
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        return view('Report.gold_stock_report' , compact('routes'));
    }

    public function gold_stock_search(Request  $request){
        $workWarehouses = Warehouse::where('type' , '=' , 1); // ->get() -> groupBy('karat_id') ;
        $oldWarehouses = Warehouse::where('type' , '=' , 0) ; // -> get() -> groupBy('karat_id') ;

        if($request -> has('isStartDate')) $workWarehouses = $workWarehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $workWarehouses = $workWarehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        if($request -> has('isStartDate')) $oldWarehouses = $oldWarehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $oldWarehouses = $oldWarehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());



        $karats = Karat::all();
        $work = $workWarehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });
        $old = $oldWarehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });




        $works = DB::table('exit_work_details')
            -> join('exit_works' , 'exit_work_details.bill_id' , '=' , 'exit_works.id')
            -> where('exit_works.total_money' , '<' , 0)
            ->select('exit_work_details.*' , 'exit_works.date');

        $olds = DB::table('exit_old_details')
            -> join('exit_olds' , 'exit_old_details.bill_id' , '=' , 'exit_olds.id')
            -> where('exit_olds.total_money' , '<' , 0)
            ->select('exit_old_details.*' , 'exit_olds.date');


        $workR = $works ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'RWeight' => $item -> sum('weight'),
            ];
        });

        $oldR = $olds ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'RWeight' => $item -> sum('weight'),
            ];
        });





        $slag =  14;
        $subSlag = 146 ;

        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }


        $company = CompanyInfo::all() -> first();
        return view('Item.gold_stock' , compact('work' , 'old' , 'karats' , 'slag' , 'subSlag' , 'routes' ,
            'period' , 'period_ar' , 'company'  , 'workR' , 'oldR')) ;
    }

    public function daily_all_movements(){
        $pricings = Pricing::all();
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.daily_all_movements' , compact('routes'));
    }

    public function daily_all_movements_search(Request $request){
        $karats = Karat::all();

        $workWarehouses = Warehouse::where('type' , '=' , 1);
        $oldWarehouses = Warehouse::where('type' , '=' , 0) ;

        if($request -> has('isStartDate')) $workWarehouses = $workWarehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $workWarehouses = $workWarehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        if($request -> has('isStartDate')) $oldWarehouses = $oldWarehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $oldWarehouses = $oldWarehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        $work = $workWarehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });
        $old = $oldWarehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });

        $enterMoney = EnterMoney::all();
        $exitMoney = ExitMoney::all();

        if($request -> has('isStartDate')) $enterMoney = $enterMoney -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $enterMoney = $enterMoney -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> has('isStartDate')) $exitMoney = $exitMoney -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $exitMoney = $exitMoney -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }

        $company = CompanyInfo::all() -> first();


        return view('Report.daily_all_movements_result' , compact('karats' , 'work' , 'old' , 'enterMoney' ,
            'exitMoney' , 'routes' , 'period' , 'period_ar' , 'company'));
    }


    public function account_balance_search(Request $request){
        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);



        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }



        $accounts = DB::table('accounts_trees')
            ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
            ->select('accounts_trees.code','accounts_trees.name',
                DB::raw('sum(account_movements.credit) as credit'),
                DB::raw('sum(account_movements.debit) as debit'))
            ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name')
            ->where('account_movements.date','>=',$startDate)
            ->where('account_movements.date','<=',$endDate)

            ->get();

        foreach ($accounts as $account){

            $accountBalance = DB::table('accounts_trees')
                ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                ->select('accounts_trees.code','accounts_trees.name',
                    DB::raw('SUM(CASE WHEN account_movements.notes = "" THEN account_movements.credit END) credit'),
                    DB::raw('SUM(CASE WHEN account_movements.notes = "" THEN account_movements.debit END) debit'))
                ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name')
                ->where('account_movements.date','<',$startDate)
                ->where('accounts_trees.code','<',$account->code)
                ->get()->first();

            if($accountBalance){
                $account->before_credit = $accountBalance->credit;
                $account->before_debit = $accountBalance->debit;
            } else {
                $account->before_credit = 0;
                $account->before_debit = 0;
            }

        }
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $company = CompanyInfo::all() -> first();
        return view('Report.account_balance_report',compact('accounts' , 'routes' , 'period' , 'period_ar' , 'company'));

    }

    public function account_balance(){
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.account_balance' , compact('routes'));
    }


    public function box_movement_report(){
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.box_movement_report' , compact('routes'));
    }
    public function box_movement_report_search(Request $request){
        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);

        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
        }

        $enterMoney = EnterMoney::where('payment_method' , '=' , 0)
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate)
            -> get();

        $exitMoney = ExitMoney::where('payment_method' , '=' , 0)
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate)
            -> get();

        $catchs = DB::table('catch_recipts')
            -> select('catch_recipts.*' )
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate)
            -> get();


        $expenses = DB::table('expenses')
            -> select('expenses.*' )
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate)
            -> get();

        $holders = [];
        foreach ($enterMoney as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> based_on_bill_number ? $em -> based_on_bill_number  : $em -> doc_number  ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  $em -> based_on_bill_number ? (str_starts_with($em -> based_on_bill_number , 'SWSI') ? 'فاتور بيع ذهب مشغول' : 'فاتورة بيع ذهب كسر')  : 'مستند دخول نقدية' ;
            $holder -> credit = $em -> amount ;
            $holder -> debit = 0 ;
            array_push($holders , $holder);
        }

        foreach ($exitMoney as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> based_on_bill_number ? $em -> based_on_bill_number  : $em -> doc_number  ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  $em -> based_on_bill_number ? (str_starts_with($em -> based_on_bill_number , 'SPOI') ? 'فاتور شراء ذهب كسر' : '')  : 'مستند خروج نقدية' ;
            $holder -> credit = 0 ;
            $holder -> debit = $em -> amount ;
            array_push($holders , $holder);
        }

        foreach ($catchs as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> docNumber ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  'مستند قبض حر'  ;
            $holder -> credit = $em -> amount ;
            $holder -> debit =  0;
            array_push($holders , $holder);
        }
        foreach ($expenses as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> docNumber ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  'مستند صرف حر'  ;
            $holder -> credit = 0 ;
            $holder -> debit =  $em -> amount;
            array_push($holders , $holder);
        }


        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }


        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }

        $company = CompanyInfo::all() -> first();

        return view('Report.box_movement_report_result' , compact('routes' , 'holders' , 'period' , 'period_ar' , 'company'));


    }
    public function bank_movement_report(){
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.bank_movement_report' , compact('routes'));
    }
    public function bank_movement_report_search(Request $request){
        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);

        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
        }

        $enterMoney = EnterMoney::where('payment_method' , '=' , 1)
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate)
            -> get();

        $exitMoney = ExitMoney::where('payment_method' , '=' , 1)
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate)
            -> get();


        $holders = [];
        foreach ($enterMoney as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> based_on_bill_number ? $em -> based_on_bill_number  : $em -> doc_number  ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  $em -> based_on_bill_number ? (str_starts_with($em -> based_on_bill_number , 'SWSI') ? 'فاتور بيع ذهب مشغول' : 'فاتورة بيع ذهب كسر')  : 'مستند دخول نقدية' ;
            $holder -> credit = $em -> amount ;
            $holder -> debit = 0 ;
            array_push($holders , $holder);
        }

        foreach ($exitMoney as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> based_on_bill_number ? $em -> based_on_bill_number  : $em -> doc_number  ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  $em -> based_on_bill_number ? (str_starts_with($em -> based_on_bill_number , 'SPOI') ? 'فاتور شراء ذهب كسر' : '')  : 'مستند خروج نقدية' ;
            $holder -> credit = 0 ;
            $holder -> debit = $em -> amount ;
            array_push($holders , $holder);
        }


        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }

        $company = CompanyInfo::all() -> first();

        return view('Report.bank_movement_report_result' , compact('routes' , 'holders' , 'period' , 'period_ar' , 'company'));
    }

    public function sales_total_report(){
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.sales_total_report' , compact('routes'));

    }
    public function sales_total_report_search(Request $request){
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $data = ExitWork::where('net_money' , '>' , 0);

        $data2 = ExitOld::where('net_money' ,'>' , 0);

        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data = $data -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data = $data -> where('net_money' , '=' ,$request -> netMoney );

        if($request -> has('isStartDate')) $data2 = $data2 -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data2 = $data2 -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data2 = $data2 -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data2 = $data2 -> where('net_money' , '=' ,$request -> netMoney );


        $bills = array();
        $data22 =[] ;
        foreach ($data-> get() as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client = $client -> name;
            else
                $bill -> client = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }
        foreach ($data2 -> get() as $bill){
            $client = Company::find($bill -> supplier_id);
            if($client)
                $bill -> client = $client -> name;
            else
                $bill -> client = '';

            $bill -> type = 0 ;
            $bill -> item_name_ar  = '--';
            $bill -> item_name_en  = '--';
            array_push($bills , $bill);
            array_push($data22 , $bill);

        }

        $all =  collect($bills)  -> merge($data22);


        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        return view('Report.sales_total_report_result' , compact('all' , 'routes' ,'period' , 'period_ar' , 'company'));


    }

    public function purchase_total_report(){
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.purchase_total_report' , compact('routes'));
    }
    public function purchase_total_report_search(Request $request){
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $data = EnterWork::where('net_money' , '>' , 0);

        $data2 = EnterOld::where('net_money' ,'>' , 0);

        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data = $data -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data = $data -> where('net_money' , '=' ,$request -> netMoney );

        if($request -> has('isStartDate')) $data2 = $data2 -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data2 = $data2 -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data2 = $data2 -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data2 = $data2 -> where('net_money' , '=' ,$request -> netMoney );


        $bills = array();
        $data22 =[] ;


        foreach ($data-> get() as $bill){
            $supplier = Company::find($bill -> supplier_id);
            if($supplier)
                $bill -> supplier =   $supplier -> name ;
            else
                $bill -> supplier = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }
        foreach ($data2 -> get() as $bill){
            $supplier = Company::find($bill -> client_id);
            if($supplier)
                $bill -> supplier =   $supplier -> name ;
            else
                $bill -> supplier = '';
            $bill -> type = 0 ;
            array_push($bills , $bill);
        }


        $all =  collect($bills)  -> merge($data22);
        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }

        $company = CompanyInfo::all() -> first();

        return view('Report.purchase_total_report_result' , compact('all' , 'routes' , 'period' , 'period_ar' , 'company'));

    }
    public function purchase_sales_total_report(){
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.purchase_sales_total_report' , compact('routes'));
    }
    public function purchase_sales_total_report_search(Request $request){

    }

    public function movement_report(){
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.movement_report' , compact('routes'));
    }

    public function movement_report_search(Request $request){

        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();
        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }

        $company = CompanyInfo::all() -> first();

        $karats = Karat::all();

        $Warehouses = Warehouse::where('type' , '<>' , 2);
        if($request -> has('isStartDate')) $Warehouses = $Warehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $Warehouses = $Warehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        $ware = $Warehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });
        $data = collect($ware);



        $returnW = DB::table('exit_work_details')
            -> join('exit_works' , 'exit_work_details.bill_id' , '=' , 'exit_works.id')
            -> select('exit_work_details.*' , 'exit_works.date' )
            ->where('exit_works.returned_bill_id' , '>'  , 0);
        $returnO = DB::table('exit_old_details')
            -> join('exit_olds' , 'exit_old_details.bill_id' , '=' , 'exit_olds.id')
            -> select('exit_old_details.*' , 'exit_olds.date' )
            ->where('exit_olds.returned_bill_id' , '>'  , 0) ;
        if($request -> has('isStartDate')) $returnW = $returnW -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $returnW = $returnW -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> has('isStartDate')) $returnO = $returnO -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $returnO = $returnO -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        $reW = $returnW ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'weight' => $item -> sum('weight'),
            ];
        });


        $reO = $returnO ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'weight' => $item -> sum('weight'),
            ];
        });

        $salesW = DB::table('exit_works')
            ->where('exit_works.returned_bill_id' , '='  , 0)
            -> sum('exit_works.total_money');

        $salesO = DB::table('exit_olds')
            ->where('exit_olds.returned_bill_id' , '='  , 0)
            -> sum('exit_olds.total_money');

        $returnW = DB::table('exit_works')
            ->where('exit_works.returned_bill_id' , '<>'  , 0)
            -> sum('exit_works.total_money');

        $returnO = DB::table('exit_olds')
            ->where('exit_olds.returned_bill_id' , '<>'  , 0)
            -> sum('exit_olds.total_money');


        $purchaseW = DB::table('enter_works')
            -> sum('enter_works.total_money');

        $purchaseO = DB::table('enter_olds')
            -> sum('enter_olds.total_money');


        $salesWorkVAl = DB::table('exit_work_details')
            ->join('exit_works' , 'exit_work_details.bill_id' , '=' , 'exit_works.id')
            -> join('items' , 'exit_work_details.item_id' , '=' ,'items.id')
            ->where('exit_works.returned_bill_id' , '='  , 0)
            -> select(DB::raw('sum(items.made_Value * items.weight) as total'))->get() -> first();

        $returnWorkVAl = DB::table('exit_work_details')
            ->join('exit_works' , 'exit_work_details.bill_id' , '=' , 'exit_works.id')
            -> join('items' , 'exit_work_details.item_id' , '=' ,'items.id')
            ->where('exit_works.returned_bill_id' , '<>'  , 0)
            -> select(DB::raw('sum(items.made_Value * items.weight) as total'))->get() -> first();

        $expenses = DB::table('expenses')
            ->join('accounts_trees' , 'expenses.to_account' , '=' , 'accounts_trees.id')
            ->select('expenses.*' , 'accounts_trees.name as account_name');

        if($request -> has('isStartDate')) $expenses = $expenses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $expenses = $expenses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());



        $exp = $expenses ->get() -> groupBy('account_name') -> map(function ($item) {
            return [
                'total' => $item -> sum('amount'),
            ];
        });




        return view('Report.movement_report_result' , compact('company' , 'routes' , 'data' , 'period' , 'period_ar' , 'karats' , 'reW' , 'reO' ,
            'salesW' , 'salesO' , 'returnW' , 'returnO' , 'purchaseW' , 'purchaseO' , 'salesWorkVAl' , 'returnWorkVAl' , 'exp'));

    }

    public function account_movement_report(){
        //account_movement
        $accounts = AccountsTree::all();
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }
        return view('Report.account_movement' , compact('routes' , 'accounts'));

    }
    public function account_movement_report_search(Request $request){
        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);



        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتي اليوم' ;
        }



        $accounts = DB::table('accounts_trees')
            ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
            ->select('accounts_trees.code','accounts_trees.name',
                DB::raw('sum(account_movements.credit) as credit'),
                DB::raw('sum(account_movements.debit) as debit'))
            ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name')
            ->where('account_movements.date','>=',$startDate)
            ->where('account_movements.date','<=',$endDate)
            ->where('accounts_trees.id' , '=' , $request -> account_id)

            ->get();

        foreach ($accounts as $account){

            $accountBalance = DB::table('accounts_trees')
                ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                ->select('accounts_trees.code','accounts_trees.name',
                    DB::raw('SUM(CASE WHEN account_movements.notes = "" THEN account_movements.credit END) credit'),
                    DB::raw('SUM(CASE WHEN account_movements.notes = "" THEN account_movements.debit END) debit'))
                ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name')
                ->where('account_movements.date','<',$startDate)
                ->where('accounts_trees.code','<',$account->code)
                ->get()->first();

            if($accountBalance){
                $account->before_credit = $accountBalance->credit;
                $account->before_debit = $accountBalance->debit;
            } else {
                $account->before_credit = 0;
                $account->before_debit = 0;
            }

        }
        $roles = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($roles as $role){
            array_push($routes , $role -> route);
        }

        $company = CompanyInfo::all() -> first();
        return view('Report.account_movement_report',compact('accounts' , 'routes' , 'period' , 'period_ar' , 'company'));
    }

}
