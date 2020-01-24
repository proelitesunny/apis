<?php

namespace App\MyHealthcare\Repositories\HealthArticle;

use App\Models\HealthArticle;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use App\MyHealthcare\Helpers\Asset;

class HealthArticleRepository implements HealthArticleInterface
{
    private $healthArticle;
    
    private $asset;

    public function __construct(HealthArticle $healthArticle, Asset $asset) {
        $this->healthArticle = $healthArticle;
        $this->asset = $asset;
    }

    public function create($params) {
        
        try{
            
            $params['article_picture'] = $this->asset->storeAsset('health_articles', 'health_articles', $params['article_picture']);
            
            return $this->healthArticle->create($params);
            
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }        
    }

    public function checkUniqueArticleName($params){
        
        try{
            $healthArticles = $this->healthArticle->withTrashed()->where(function($query) use($params){
                
                if(isset($params['id'])){
                    $query->where('id',$params['id']);
                }
                
                if(isset($params['title'])){
                    $query->where('title',$params['title']);
                }
            });
            
            return $healthArticles->count();
            
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }
    }

    public function delete($id) {
        try{
            
            $healthArticle = $this->healthArticle->findOrFail($id);
            
            return $healthArticle->delete();
            
        } catch (ModelNotFoundException $ex) {
            throw new ModelNotFoundException($ex->getMessage(),$ex->getCode());
        } catch (Exception $e){
            throw new Exception($e->getMessage(),$e->getCode());
        }
    }

    public function find($id) {
        
        try{
            
            $healthArticle = $this->healthArticle->findOrFail($id);
            
            return $healthArticle;
        } catch (ModelNotFoundException $ex) {
            throw new ModelNotFoundException($ex->getMessage(),$ex->getCode());
        } catch (Exception $e){
            throw new Exception($e->getMessage(),$e->getCode());
        }
    }

    public function getAll($keyword = null) {
        
        try{
            
            return $this->healthArticle->where(function($query) use($keyword) {
                                            if ($keyword) {
                                                
                                                if(in_array(strtolower($keyword), ['active','inactive'])){                                                    
                                                    (strtolower($keyword)=='active') ? $query->where('status',1) : $query->where('status',0);
                                                }else{      
                                                
                                                    $query->where('title', 'LIKE', '%'.$keyword.'%') 
                                                          ->orWhere('description', 'LIKE', '%'.$keyword.'%');                                            
                                                }
                                            }
                                        })
                                        ->orderBy('updated_at', 'DESC')
                                        ->paginate(10);
            
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }        
    }

    public function getCount() {
        
        try{
            return $this->healthArticle->count();
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }
    }

    public function update($id, $params) {
        try{
            
            if(isset($params['article_picture']) && !empty($params['article_picture'])){
                $params['article_picture'] = $this->asset->storeAsset('health_articles', 'health_articles', $params['article_picture']);
            }else{
                unset($params['article_picture']);
            }
            
            return $this->healthArticle->findOrFail($id)->update($params);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }
    }
}