<?php

namespace App\MyHealthcare\Repositories\Faq;

use App\Models\Faq;

class FaqRepository implements FaqInterface
{
	/**
	 * @var Faq
	 */
	private $faq;

	/**
	 * FaqRepository constructor.
	 * @param Faq $faq
	 */
	public function __construct(Faq $faq) {
		$this->faq = $faq;
	}

	public function getAll($keyword = null)
    {
        if (!$keyword) {
            return $this->faq->orderBy('id', 'desc')->paginate(10);
        }
        return $this->faq->with('pages')->where(function($query) use($keyword) {$query ->orWhere('title', 'LIKE', '%'.$keyword.'%'); $query ->orWhere('description', 'LIKE', '%'.$keyword.'%');})->orWhereHas('pages', function($q) use($keyword){ $q->where('title','Like','%'.$keyword.'%'); })
            ->paginate(10);
    }

    public function find($id)
    {
        return $this->faq->find($id);
    }

    public function create($params)
    {
        $faq = $this->faq;

        $faq->title = $params['title'];
        $faq->description = $params['description'];

        $faq->save();

        return $faq;
    }

    public function update($id, $params)
    {

        $faq = $this->faq->find($id);

        $faq->title = $params['title'];
        $faq->description = $params['description'];

        $faq->save();

        return $faq;
    }

    public function delete($id)
    {
        $faq = $this->faq->find($id);
        $faq->delete();
    }

    public function getCount()
    {
        return $this->faq->count();
    }
}
