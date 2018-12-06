<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\Good;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Closure;

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
        $ad_code = $request->has('ad_code') ? $request->post('ad_code') : 0;
        if($ad_code){
            $area = Area::where('ad_code', $ad_code)->first();
        }else{
            return ['code' => 0, 'message' => 'ad_code 参数必传', 'data' => ''];
        }

        // 1：企业  2：个人
        $sold_type = $request->has('sold_type') ? $request->post('sold_type') : 1;
        // 1 综合  2 价格  3 销量
        $sort_type = $request->has('sort_type') ? $request->post('sort_type') : 1;

        switch ($sort_type) {
            case 1:
                $data_lists = Good::where('category', $sold_type)
                                    ->where(function ($query) use ($area){
                                        $query->where('apply_city', 'like', '%'.$area->ad_code.'%')
                                              ->orWhere('apply_city', '-1');
                                    })
                                    ->select(['id', 'name', 'price', 'sold_out', 'category', 'show_pic'])
                                    ->orderBy('sold_out', 'desc')
                                    ->orderBy('price', 'asc')
                                    ->get();
                break;
            case 2:
                $data_lists = Good::where('category', $sold_type)
                                    ->where(function ($query) use ($area){
                                        $query->where('apply_city', 'like', '%'.$area->ad_code.'%')
                                              ->orWhere('apply_city', '-1');
                                    })
                                    ->select(['id', 'name', 'price', 'sold_out', 'category', 'show_pic'])
                                    ->orderBy('price', 'asc')
                                    ->get();
                break;
            case 3:
                $data_lists = Good::where('category', $sold_type)
                                    ->where(function ($query) use ($area){
                                        $query->where('apply_city', 'like', '%'.$area->ad_code.'%')
                                              ->orWhere('apply_city', '-1');
                                    })
                                    ->select(['id', 'name', 'price', 'sold_out', 'category', 'show_pic'])
                                    ->orderBy('sold_out', 'desc')
                                    ->get();
                break;
            default:
                # code...   
                break;
        }

        return ['code' => 1, 'message' => '请求首页商品列表成功', 'data' => $data_lists];
    }
}
