<?php
namespace Modules\FrontendCMS\Repositories;

use Modules\FrontendCMS\Entities\DynamicPage;

class DynamicPageRepository {

    protected $dynamicPage;

    public function __construct(DynamicPage $dynamicPage){
        $this->dynamicPage = $dynamicPage;
    }


    public function getAll()
    {
        return $this->dynamicPage::where('is_static',0)->get();
    }
    public function getActiveAll()
    {
        return $this->dynamicPage::where('status',1)->get();
    }

    public function save($data)
    {
        
        return $this->dynamicPage::create([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'status' => $data['status']
        ]);
        
    }

    public function update($data, $id)
    {   
        return $this->dynamicPage::where('id',$id)->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'status' => $data['status']
        ]);

    }

    public function delete($id){
        $dynamicPage = $this->dynamicPage->findOrFail($id);
        
        if(count($dynamicPage->MenuElements) > 0 || count($dynamicPage->footerWidgets) > 0){
            return "not_possible";
        }
        $dynamicPage->delete();

        return 'possible';
    }

    public function show($id){
        $dynamicPage = $this->dynamicPage->findOrFail($id);
        return $dynamicPage;
    }

    public function edit($id){
        $dynamicPage = $this->dynamicPage->findOrFail($id);
        return $dynamicPage;
    }
    public function statusUpdate($data, $id){
        return $this->dynamicPage::where('id',$id)->update([
            'status' => $data['status']
        ]);
    }
}
