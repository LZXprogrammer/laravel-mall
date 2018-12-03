<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\Good;
use App\Models\Area;

class HomeController extends Controller
{
    /**
     * 首页轮播图
     *
     * @return \Illuminate\Http\Response
     */
    public function homeBanner()
    {
        $credit_banner = Article::select(['picture', 'url'])->whereType(1)->get();

        return $credit_banner;
    }

    /**
     * 首页商品列表
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function homeGoodLists(Request $request)
    {      
        // $city_code = $request->has('city_code') ? $request->get('city_code') : 0;
        // if($city_code){
        //     $area = Area::where('city_code', $city_code)->first();
        // }

        // // 1：企业  2：个人
        // $sold_type = $request->has('sold_type') ? $request->get('sold_type') : 1;
        // $sort_type = $request->has('sort_type') ? $request->get('sort_type') : 1;

        // // 1 综合  2 价格  3 销量
        // switch ($sort_type) {
        //     case 1:
                
        //         break;
        //     case 2:
        //         $data_lists = Good::where('city_code', $city_code)->get();
        //         break;
        //     default:
        //         # code...
        //         break;
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
