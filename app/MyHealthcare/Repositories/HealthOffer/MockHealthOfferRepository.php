<?php

namespace App\MyHealthcare\Repositories\HealthOffer;

class MockHealthOfferRepository implements HealthOfferInterface
{
	/**
	 * @var HealthOffer
	 */
	private $healthOffer;

	/**
	 * MockHealthOfferRepository constructor.
	 * @param HealthOffer $healthOffer
	 */
	public function __construct(\Faker\Generator $faker) {
		$this->healthOffer = $faker;
    }

    public function find($id)
    {
		return (object)[
			'id' => $id,
			'name' => $this->healthOffer->name,
			'offer' => 'Avail 20% off',
			'icon' => 'image/2017/5/fortis.jpg',
			'image' => 'image/2017/5/fortis.jpg',
			'description' => $this->healthOffer->paragraph(5)
		];
    }


    public function getHealthOffers()
    {
    	return collect([
			(object)[
				'id' => rand(1,9),
				'name' => $this->healthOffer->name,
				'offer' => 'Avail 20% off',
				'icon' => 'image/2017/5/fortis.jpg',
				'image' => 'image/2017/5/fortis.jpg',
				'description' => $this->healthOffer->paragraph(5)
			],
			(object)[
				'id' => rand(1,9),
				'name' => $this->healthOffer->name,
				'offer' => 'Avail 20% off',
				'icon' => 'image/2017/5/fortis.jpg',
				'image' => 'image/2017/5/fortis.jpg',
				'description' => $this->healthOffer->paragraph(5)
			],
			(object)[
				'id' => rand(1,9),
				'name' => $this->healthOffer->name,
				'offer' => 'Avail 20% off',
				'icon' => 'image/2017/5/fortis.jpg',
				'image' => 'image/2017/5/fortis.jpg',
				'description' => $this->healthOffer->paragraph(5)
			],
			(object)[
				'id' => rand(1,9),
				'name' => $this->healthOffer->name,
				'offer' => 'Avail 20% off',
				'icon' => 'image/2017/5/fortis.jpg',
				'image' => 'image/2017/5/fortis.jpg',
				'description' => $this->healthOffer->paragraph(5)
			],
			(object)[
				'id' => rand(1,9),
				'name' => $this->healthOffer->name,
				'offer' => 'Avail 20% off',
				'icon' => 'image/2017/5/fortis.jpg',
				'image' => 'image/2017/5/fortis.jpg',
				'description' => $this->healthOffer->paragraph(5)
			],
			(object)[
				'id' => rand(1,9),
				'name' => $this->healthOffer->name,
				'offer' => 'Avail 20% off',
				'icon' => 'image/2017/5/fortis.jpg',
				'image' => 'image/2017/5/fortis.jpg',
				'description' => $this->healthOffer->paragraph(5)
			],
			(object)[
				'id' => rand(1,9),
				'name' => $this->healthOffer->name,
				'offer' => 'Avail 20% off',
				'icon' => 'image/2017/5/fortis.jpg',
				'image' => 'image/2017/5/fortis.jpg',
				'description' => $this->healthOffer->paragraph(5)
			],
			(object)[
				'id' => rand(1,9),
				'name' => $this->healthOffer->name,
				'offer' => 'Avail 20% off',
				'icon' => 'image/2017/5/fortis.jpg',
				'image' => 'image/2017/5/fortis.jpg',
				'description' => $this->healthOffer->paragraph(5)
			],
			(object)[
				'id' => rand(1,9),
				'name' => $this->healthOffer->name,
				'offer' => 'Avail 20% off',
				'icon' => 'image/2017/5/fortis.jpg',
				'image' => 'image/2017/5/fortis.jpg',
				'description' => $this->healthOffer->paragraph(5)
			],
			(object)[
				'id' => rand(1,9),
				'name' => $this->healthOffer->name,
				'offer' => 'Avail 20% off',
				'icon' => 'image/2017/5/fortis.jpg',
				'image' => 'image/2017/5/fortis.jpg',
				'description' => $this->healthOffer->paragraph(5)
			]
		]);
    }
}
