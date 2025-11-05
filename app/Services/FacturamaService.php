<?php

namespace App\Services;

use Facturama\Client;
use App\Services\Modules\InvoiceModule;
use App\Services\Modules\ClientModule;
use App\Services\Modules\ProductModule;
use Exception;

class FacturamaService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            env('FACTURAMA_USERNAME'),
            env('FACTURAMA_PASSWORD'),
            ['base_uri' => env('FACTURAMA_API_URL')]
        );
    }

    public function getClients()
    {
        return $this->client->get('clients');
    }

    public function getProducts()
    {
        return $this->client->get('products');
    }
}
