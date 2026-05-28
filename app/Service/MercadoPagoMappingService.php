<?php

namespace App\Service;

use App\Models\MercadoPagoMapping;
use App\Models\Place;

class MercadoPagoMappingService
{
    private ?MercadoPagoMapping $default = null;

    public function getCategoryId(string $description): ?int
    {
        return $this->resolve($description)->category_id;
    }

    public function getPlaceId(string $description): ?int
    {
        $mapping = $this->resolve($description);

        if (!$mapping->place_name) {
            return null;
        }

        return Place::firstOrCreate(['name' => $mapping->place_name])->id;
    }

    private function resolve(string $description): MercadoPagoMapping
    {
        $description = strtolower($description);

        $mapping = MercadoPagoMapping::where('is_default', false)
            ->get()
            ->first(fn($m) => str_contains($description, $m->keyword));

        return $mapping ?? $this->getDefault();
    }

    private function getDefault(): MercadoPagoMapping
    {
        return $this->default ??= MercadoPagoMapping::where('is_default', true)->firstOrFail();
    }
}
