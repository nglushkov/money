<?php

namespace App\Service;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MercadoPagoService
{
    private const BASE_URL = 'https://api.mercadopago.com';
    private const PAGE_SIZE = 100;

    public function __construct(private readonly string $accessToken) {}

    public function getPayments(Carbon $from, Carbon $to): Collection
    {
        $params = [
            'sort'       => 'date_created',
            'criteria'   => 'desc',
            'range'      => 'date_created',
            'begin_date' => $from->utc()->format('Y-m-d\TH:i:s.000-00:00'),
            'end_date'   => $to->utc()->format('Y-m-d\TH:i:s.000-00:00'),
            'limit'      => self::PAGE_SIZE,
            'offset'     => 0,
        ];

        $results = collect();

        do {
            $response = Http::withToken($this->accessToken)
                ->get(self::BASE_URL . '/v1/payments/search', $params);

            $response->throw();

            $data    = $response->json();
            $page    = collect($data['results'] ?? []);
            $total   = $data['paging']['total'] ?? 0;

            $results = $results->concat($page);
            $params['offset'] += self::PAGE_SIZE;
        } while ($params['offset'] < $total && $page->isNotEmpty());

        return $results;
    }
}
