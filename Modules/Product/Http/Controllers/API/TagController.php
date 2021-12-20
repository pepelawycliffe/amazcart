<?php

namespace Modules\Product\Http\Controllers\API;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Repositories\TagRepository;


class TagController extends Controller
{
    protected $tagRepository;
    public function __construct(TagRepository $tagRepository){
        $this->tagRepository = $tagRepository;
    }

    // Product tags

    public function index(){
        $tags = $this->tagRepository->tagList();
        if(count($tags) > 0){
            return response()->json([
                'tags' => $tags,
                'message' => 'success'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Empty list'
            ], 404);
        }
    }


    // Single Tag

    public function show($tag){
        $tag = $this->tagRepository->getByTag($tag);

        if($tag){
            return response()->json([
                'tags' => $tag,
                'message' => 'success'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Not found'
            ], 404);
        }
    }
}
