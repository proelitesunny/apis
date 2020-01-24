<?php

namespace App\MyHealthcare\Repositories\Configuration;

use App\Models\Configuration;
use Illuminate\Support\Facades\Auth;

class ConfigurationRepository implements ConfigurationInterface
{
	/**
	 * @var Setting
	 */
	private $configuration;

	/**
	 * SettingRepository constructor.
	 * @param Setting $configuration
	 */
	public function __construct(Configuration $configuration) {
		$this->configuration = $configuration;
	}

	public function getAll($keyword = null)
    {
        if (!$keyword) {
            return $this->configuration->paginate(10);
        }
        return $this->configuration->where('configuration_key', 'LIKE', '%'.$keyword.'%')
            ->orWhere('configuration_value', 'LIKE', '%'.$keyword.'%')
            ->paginate(10);
    }

	public function find($id)
    {
        return $this->configuration->find($id);
    }

	public function create($request)
    {
       	$configuration = $this->configuration;

        $configuration->configuration_key = $request->get('configuration_key');

        $configuration->configuration_value = $request->get('hdn_config_value_array');

        $configuration->updated_by = Auth::id();

        $configuration->created_by = Auth::id();

        $configuration->save();

        return $configuration;
    }

    public function update($id, $request)
    {
       	$configuration = $this->find($id);

        $configuration->configuration_key = $request->get('configuration_key');

        $configuration->configuration_value = $request->get('hdn_config_value_array');

        $configuration->updated_by = Auth::id();

        $configuration->save();

        return $configuration;
    }

    public function delete($id)
    {
        $configuration = $this->find($id);
        $configuration->delete();
    }

    public function getConfiguration($keyword)
    {
      if($keyword){

            return $this->configuration->where('configuration_key', 'LIKE', '%'.$keyword.'%')->first();
        }  
    }
}
