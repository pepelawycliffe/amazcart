<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Modules\FrontendCMS\Services\ContactContentService;
use \Modules\FrontendCMS\Services\QueryService;
use Exception;
use Modules\UserActivityLog\Traits\LogActivity;

class ContactUsController extends Controller
{

    protected $contactContentService;
    protected $queryService;

    public function __construct(ContactContentService $contactContentService, QueryService $queryService)
    {
        $this->contactContentService = $contactContentService;
        $this->queryService = $queryService;
        $this->middleware('maintenance_mode');
    }

    public function index(){
        try{
            $contactContent = $this->contactContentService->getAll();
            $QueryList = $this->queryService->getAllActive();

            return view(theme('pages.contact_us'),compact('contactContent','QueryList'));
        }catch(Exception $e){
            LogActivity::errorLog($e->getMessage());
            return $e->getMessage();
        }
    }
}
