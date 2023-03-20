<?php

namespace App\Http\Controllers\Insight;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\AuthApi;
use App\Models\Customer;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    public function index(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $page = $param->get('page');
        $limit = 10 ;
        $offset = ( $limit * $page ) - $limit ;
        $sort = $param->get('sort');
        $categoryId = $param->get('category_id');
        $isFeatured = $param->get('is_featured');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dataObj = Article::select(["article.id","article.title","article.description","article.created_date","article.image","article.sort_num","article.status","article.category_id","article_category.name as article_category_name","article.link","article.link_caption"])
                    ->where('article.status',1)
                    ->leftJoin('article_category', 'article_category.id', '=', 'article.category_id');
                if($categoryId) $dataObj = $dataObj->where('article.category_id',$categoryId);
                if($isFeatured) $dataObj = $dataObj->where('article.featured',$isFeatured);
                $dataObj = $dataObj->orderBy('article.created_date','desc')->limit($limit)->offset($offset)->get();
                if($dataObj) {
                    $data = $dataObj;
                    $success = true;
                    $msg = 'OK';
                }else{
                    $msg = 'article not found';
                }
            }else{
                $msg = 'You Not Valid';
            }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }
    public function category(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dataObj = ArticleCategory::where('status',1)->orderBy('sort_num','asc')->orderBy('name','asc')->get();
                if($dataObj) {
                    $data = $dataObj;
                    $success = true;
                    $msg = 'OK';
                }else{
                    $msg = 'banner not found';
                }
            }else{
                $msg = 'You Not Valid';
            }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }
    public function detail(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $article_id = $param->get('article_id');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dataObj = Article::select(["article.id","article.title","article.description","article.created_date","article.image","article.sort_num","article.status","article.category_id","article_category.name as article_category_name","article.link","article.link_caption"])
                    ->leftJoin('article_category', 'article_category.id', '=', 'article.category_id')
                    ->where('article.id',$article_id)
                    ->where('article.status',1)->first();
                if($dataObj) {
                    $data = $dataObj;
                    $success = true;
                    $msg = 'OK';
                }else{
                    $msg = 'article not found';
                }
            }else{
                $msg = 'You Not Valid';
            }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }
}
