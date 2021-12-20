<?php

namespace Modules\Utilities\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\UserActivityLog\Traits\LogActivity;
use Modules\Utilities\Repositories\UtilitiesRepository;

class UtilitiesController extends Controller
{
    protected $utilitiesRepository;
    public function __construct(UtilitiesRepository $utilitiesRepository)
    {
        $this->middleware('maintenance_mode');
        $this->utilitiesRepository = $utilitiesRepository;
    }

    public function index(Request $request)
    {
        try{
            if($request->has('utilities')){
                if(env('APP_SYNC')){
                    Toastr::error(__('common.restricted_in_demo_mode'));
                    return redirect()->back();
                }
                if($request->utilities == 'xml_sitemap'){
                    return redirect()->route('utilities.xml_sitemap');
                }
                $result = $this->utilitiesRepository->updateUtility($request->utilities);
                if($result == 'done'){
                    Toastr::success(__('common.operation_done_successfully'), __('common.success'));
                    LogActivity::successLog('Utility Operation Done.');
                }else{
                    Toastr::error(__('common.error_message'),__('common.error'));
                }
                return redirect()->back();

            }else{
                return view('utilities::index');
            }
        }catch(Exception $e){
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->back();
        }
    }

    public function xml_sitemap(Request $request){

        if($request->sitemap){
            $data = $this->utilitiesRepository->get_xml_data($request);
            return response()->view('utilities::xml_sitemap', $data)->header('Content-Type', 'text/xml');
        }else{
            Toastr::error(__('utilities.choose_sitemap_option'), __('common.error'));
            return back();
        }
    }

    public function reset_database(Request $request)
    {
        try {
            $this->utilitiesRepository->reset_database($request);
            return back();
        }catch(Exception $e){
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->back();
        }
    }

}
