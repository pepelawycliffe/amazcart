<?php

namespace Modules\Utilities\Repositories;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Modules\FrontendCMS\Entities\DynamicPage;
use Modules\Marketing\Entities\FlashDeal;
use Modules\Marketing\Entities\NewUserZone;
use Modules\Product\Entities\Brand;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\ProductTag;
use Modules\Seller\Entities\SellerProduct;
use Illuminate\Support\Facades\Hash;

use Brian2694\Toastr\Facades\Toastr;
use Modules\Blog\Entities\BlogPost;

class UtilitiesRepository
{
    public function updateUtility($utilities)
    {
        if ($utilities == 'optimize_clear') {
            Artisan::call('optimize:clear');

            $dirname = base_path('/bootstrap/cache/');

            if (is_dir($dirname)) {
                $dir_handle = opendir($dirname);
            } else {
                $dir_handle = false;
            }
            if (!$dir_handle)
                return false;
            while ($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($dirname . "/" . $file))
                        unlink($dirname . "/" . $file);
                    else
                        File::deleteDirectory($dirname . '/' . $file);
                }
            }
            closedir($dir_handle);
        } elseif ($utilities == "clear_log") {
            array_map('unlink', array_filter((array)glob(storage_path('logs/*.log'))));
            array_map('unlink', array_filter((array)glob(storage_path('debugbar/*.json'))));
        } elseif ($utilities == "change_debug") {
            envu([
                'APP_DEBUG' => env('APP_DEBUG') ? "false" : "true"
            ]);
        } elseif ($utilities == "force_https") {
            envu([
                'FORCE_HTTPS' => env('FORCE_HTTPS') ? "false" : "true"
            ]);
        } elseif ($utilities == "xml_sitemap") {
        } else {
            return 'not_done';
        }
        return 'done';
    }

    public function get_xml_data($request)
    {

        if (in_array('pages', $request->sitemap)) {
            $data['pages'] = DynamicPage::all();
        }
        if (in_array('products', $request->sitemap)) {
            $data['products'] = SellerProduct::where('status', 1)->activeSeller()->get();
        }

        if (in_array('categories', $request->sitemap)) {
            $data['categories'] = Category::where('status', 1)->get();
        }

        if (in_array('brands', $request->sitemap)) {
            $data['brands'] = Brand::where('status', 1)->get();
        }

        if (in_array('blogs', $request->sitemap)) {
            $data['blogs'] = BlogPost::where('status', 1)->get();
        }

        if (in_array('tags', $request->sitemap)) {
            $data['tags'] = ProductTag::distinct()->with('tag')->get(['tag_id']);
        }
        if (in_array('flash_deal', $request->sitemap)) {
            $data['flash_deal'] = FlashDeal::where('status', 1)->first();
        }
        if (in_array('new_user_zone', $request->sitemap)) {
            $data['new_user_zone'] = NewUserZone::where('status', 1)->first();
        }
        return $data;
    }

    public function reset_database($request)
    {
        if ($request->password == "") {
            Toastr::error(__('common.enter_your_password'));
        } elseif (Hash::check($request->password, auth()->user()->password)) {
            Toastr::success(__('utilities.database_reset_successful'));
            Artisan::call('migrate:fresh');
        } else {
            Toastr::error(__('common.invalid_password'));
        }
    }
}
