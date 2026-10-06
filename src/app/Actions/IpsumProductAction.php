<?php

namespace PixellWeb\Pennylane\app\Actions;


use Ipsum\Reservation\app\Models\Prestation\Prestation;
use PixellWeb\Pennylane\app\Data\Requests\SaveProductData;
use PixellWeb\Pennylane\app\Data\Responses\ProductData;
use PixellWeb\Pennylane\app\Ressources\Product;

class IpsumProductAction
{
    public function __construct(
        private Product $product,
    ) {}

    public function syncFromProvider(ProductData $productData): Prestation
    {
        return Prestation::updateOrCreate([
            'reference_externe' => $productData->id,
        ],
            $productData->toIpsum()
        );
    }

    public function syncToProvider(Prestation $prestation): ProductData
    {
        $save_product_data = SaveProductData::fromIpsum($prestation);

        if ($prestation->reference_externe) {
            return $this->product->update($save_product_data, $prestation->reference_externe);
        }

        $product_data = $this->product->create($save_product_data);

        $prestation->reference_externe = $product_data->id;
        $prestation->save();

        return $product_data;
    }
}


