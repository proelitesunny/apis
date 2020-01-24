<?php

namespace App\MyHealthcare\Repositories\Page;

use App\Models\Page;
use App\MyHealthcare\Helpers\Asset;

class PageRepository implements PageInterface
{
	/**
	 * @var Page
	 */
	private $page;

	/**
     * @var Asset
     */
    private $asset;

	/**
	 * PageRepository constructor.
	 * @param Page $page
	 */
	public function __construct(Page $page, Asset $asset) {
		$this->page = $page;
		$this->asset = $asset;
	}

	public function getAll($keyword = null)
    {
        if (!$keyword) {
            return $this->page->paginate(10);
        }
        return $this->page->where('title', 'LIKE', '%'.$keyword.'%')
            ->orWhere('description', 'LIKE', '%'.$keyword.'%')
            ->paginate(10);
    }



	public function find($id)
    {
        return $this->page->find($id);
    }

    public function create($params)
    {
        $page = $this->page;

        $page->title = $params['title'];

        $page_slug = str_replace(' ', '-', strtolower($params['title'])) ;
        
        $page->slug = $page_slug;

        $page->description = $params['description'];

        $page->icon = isset($params['icon']) ? $this->asset->storeAsset('pages', 'pages', $params['icon']) : null;

        $page->banner_image = isset($params['banner_image']) ?
            $this->asset->storeAsset('pages', 'pages', $params['banner_image']) :
            null;

        $page->save();

        return $page;
    }

    public function update($id, $params)
    {
        
        $lastFile = null;

        $page = $this->page->find($id);

        //$page->title = $params['title'];
        $page->description = $params['description'];

        if (isset($params['icon']) && $params['icon'] != '') {
            $lastFile = $page->icon;

            $page->icon = $this->asset->storeAsset('pages', 'pages', $params['icon']);
        }

         if (isset($params['banner_image']) && $params['banner_image'] != '') {

            $page->banner_image = $this->asset->storeAsset('pages', 'pages', $params['banner_image']);
        }

        $page->save();

        if ($lastFile) {
            $this->asset->deleteAsset($lastFile);
        }

        return $page;
    }

    public function delete($id)
    {
        $page = $this->page->find($id);
        $page->delete();
        //$page->hospitalSpecialities()->sync([]);
    }

    public function getList()
    {
        return $this->page->pluck('title', 'id');
    }

    public function getCount()
    {
        return $this->page->count();
    }

    //function for API
    public function getPage($keyword)
    {
        
        if ($keyword) {

            return $this->page->where('slug', 'LIKE', '%'.$keyword.'%')->first();
        }
    }
}
