<?php

namespace PixellWeb\Pennylane\app\Ressources;


use PixellWeb\Pennylane\app\Data\Requests\SaveProductData;
use PixellWeb\Pennylane\app\Data\Responses\ProductData;

class Product extends Ressource
{

    public function list(): \Spatie\LaravelData\CursorPaginatedDataCollection|\Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection
    {

        $products = $this->crawler->get('products');

        return ProductData::collection($products['items']);

    }


    public function create(SaveProductData $product_data): ProductData
    {
        $product = $this->crawler->post('products', $product_data->toArray());

        return ProductData::from($product);
    }

    public function update(SaveProductData|array $product_data, int $product_id): ProductData
    {
        $data = is_object($product_data) ? $product_data->toArray() : $product_data;

        $product = $this->crawler->put('products/'.$product_id, $data);

        return ProductData::from($product);
    }

}