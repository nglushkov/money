<?php

namespace App\Service;

use App\Models\MercadoPagoMapping;

class MercadoPagoMappingService
{
    private ?MercadoPagoMapping $default = null;

    public function getCategoryId(string $description): ?int
    {
        return $this->resolve($description)->category_id;
    }

    public function getPlaceId(string $description): ?int
    {
        return $this->resolve($description)->place_id;
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
