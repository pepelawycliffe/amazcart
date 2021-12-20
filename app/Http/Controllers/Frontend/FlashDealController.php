<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\FlashDealService;
use Illuminate\Http\Request;

class FlashDealController extends Controller
{
    protected $flashDealService;
    public function __construct(FlashDealService $flashDealService)
    {
        $this->flashDealService = $flashDealService;
        $this->middleware('maintenance_mode');
    }

    public function show($slug){
        
        $Flash_Deal = $this->flashDealService->getById($slug);

        $products =  $Flash_Deal->products()->whereHas('product', function($query){
            $query->where('status', 1)->whereHas('product', function($query){
                $query->where('status', 1);
            });
        })->paginate(12);
        
        if($Flash_Deal->status == 0){
            if(auth()->check() && auth()->user()->role->type == 'admin'){
                return view(theme('pages.flash_deal'), compact('Flash_Deal','products'));
            }else{
                return abort(404);
            }
        }else{
            return view(theme('pages.flash_deal'), compact('Flash_Deal','products'));
        }
        
    }

    public function fetchData(Request $request, $slug){

        $Flash_Deal = $this->flashDealService->getById($slug);
        $products =  $Flash_Deal->products()->whereHas('product', function($query){
            $query->where('status', 1)->whereHas('product', function($query){
                $query->where('status', 1);
            });
        })->paginate(12);
        return view(theme('partials.flash_deal_paginate_data'), compact('Flash_Deal','products'));

    }

}
