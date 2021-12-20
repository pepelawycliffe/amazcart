<?php
namespace Modules\Product\Repositories;

use Modules\Product\Entities\ProductTag;

class TagRepository
{

    public function tagList(){
        return ProductTag::latest()->paginate(10);
    }

    public function getByTag($tag){
        return ProductTag::where('tag', $tag)->first();
    }

}
