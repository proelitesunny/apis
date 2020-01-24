<?php

namespace App\MyHealthcare\Repositories\HealthOffer;

use App\MyHealthcare\Helpers\Asset;
use App\Models\HealthOffer;
use App\User;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class HealthOfferRepository implements HealthOfferInterface
{
    private $healthOffer;

    private $asset;

    /**
     * @var DatabaseManager
     */
    private $database;

    public function __construct(
        HealthOffer $healthOffer,
        Asset $asset,
        DatabaseManager $database
    ) {
        $this->healthOffer = $healthOffer;
        $this->asset = $asset;
        $this->database = $database;
    }

    public function create($params, $authorId)
    {
        $this->database->beginTransaction();
        try {
            $healthOffer = $this->healthOffer;

            $healthOffer->hospital_id = $params['hospital_id'];
            $healthOffer->name = $params['name'];
            $healthOffer->start_date = $params['start_date'];

            $healthOffer->end_date = isset($params['end_date']) && !empty($params['end_date']) ?
                $params['end_date'] : null;

            $healthOffer->description = isset($params['description']) && !empty($params['description']) ?
                $params['description'] : null;

            $healthOffer->how_to_avail = isset($params['how_to_avail']) && !empty($params['how_to_avail']) ?
                $params['how_to_avail'] : null;

            $healthOffer->terms_and_conditions = isset($params['terms_and_conditions']) && !empty($params['terms_and_conditions']) ?
                $params['terms_and_conditions'] : null;

            $healthOffer->offer_link = isset($params['offer_link']) && !empty($params['offer_link']) ?
                $params['offer_link'] : null;

            $healthOffer->offer_image = isset($params['offer_image']) ?
                $this->asset->storeAsset('healthOffers', 'healthOffers', $params['offer_image']) :
                null;

            $healthOffer->created_by = $authorId;
            $healthOffer->updated_by = $authorId;

            $healthOffer->save();

            $this->database->commit();

            return $healthOffer;
        } catch (\Exception $exception) {
            $this->database->rollBack();
            logger($exception->getMessage());
            return null;
        }
    }

    public function update($id, $params, $authorId)
    {
        $this->database->beginTransaction();
        try {
            $healthOffer = $this->healthOffer->findOrFail($id);

            $healthOffer->hospital_id = $params['hospital_id'];
            $healthOffer->name = $params['name'];
            $healthOffer->start_date = $params['start_date'];

            $healthOffer->end_date = isset($params['end_date']) && !empty($params['end_date']) ?
                $params['end_date'] : $healthOffer->end_date;

            $healthOffer->description = isset($params['description']) && !empty($params['description']) ?
                $params['description'] : $healthOffer->description;

            $healthOffer->how_to_avail = isset($params['how_to_avail']) && !empty($params['how_to_avail']) ?
                $params['how_to_avail'] : $healthOffer->how_to_avail;

            $healthOffer->terms_and_conditions = isset($params['terms_and_conditions']) && !empty($params['terms_and_conditions']) ?
                $params['terms_and_conditions'] : $healthOffer->terms_and_conditions;

            $healthOffer->offer_link = isset($params['offer_link']) && !empty($params['offer_link']) ?
                $params['offer_link'] : $healthOffer->offer_link;

            $healthOffer->offer_image = isset($params['offer_image']) ?
                $this->asset->storeAsset('healthOffers', 'healthOffers', $params['offer_image']) :
                $healthOffer->offer_image;

            $healthOffer->updated_by = $authorId;

            $healthOffer->status = $params['status'];

            $healthOffer->save();

            $this->database->commit();

            return $healthOffer;
        } catch (\Exception $exception) {
            $this->database->rollBack();
            logger($exception->getMessage());
            return null;
        }
    }

    public function find($id)
    {
        return $this->healthOffer->with('hospital')->findOrFail($id);
    }

    public function getHealthOffers()
    {
        return $this->healthOffer->with('hospital')->paginate(10);
    }

    public function delete($id, $authorId)
    {
        $this->database->beginTransaction();
        try {
            $healthOffer = $this->healthOffer->find($id);

            $healthOffer->delete();

            $this->database->commit();

            return true;
        } catch (\Exception $exception) {
            $this->database->rollBack();
            logger($exception->getMessage());
            return null;
        }
    }

    public function getCount() {
        
        try{
            return $this->healthOffer->count();
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }
    }

    public function getAll($keyword = null) {
        
        try{
            
            return $this->healthOffer->where(function($query) use($keyword) {
                if ($keyword) {
                    
                    if(in_array(strtolower($keyword), ['active','inactive'])){                                                    
                        (strtolower($keyword)=='active') ? $query->where('status',1) : $query->where('status',0);
                    }else{      
                    
                        $query->with('hospital')->where('name', 'LIKE', '%'.$keyword.'%') 
                              ->orWhere('description', 'LIKE', '%'.$keyword.'%');                                            
                    }
                }
            })
                ->orWhereHas('hospital', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                })
            ->orderBy('updated_at', 'DESC')
            ->paginate(10);
            
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }        
    }

}
