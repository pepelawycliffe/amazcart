<?php

namespace Modules\GeneralSetting\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Language\Repositories\LanguageRepository;


class GeneralSettingController extends Controller
{

    protected $languageRepo;

    public function __construct(LanguageRepository $languageRepo)
    {
        $this->languageRepo = $languageRepo;
    }

    // Settings info
    public function index(){
        $settings = DB::table('general_settings')->select('site_title', 'company_name','country_name', 'zip_code','address','phone','email','currency_symbol','logo','favicon','currency_code','copyright_text','language_code')->get();
        return response()->json([
            'settings' => $settings,
            'msg' => 'success'
        ]);
    }

    // Languages

    public function getActiveLanguages(){
        $languages = $this->languageRepo->getActiveAll();
        return response()->json([
            'languages' => $languages,
            'msg' => 'success'
        ]);
    }
}
