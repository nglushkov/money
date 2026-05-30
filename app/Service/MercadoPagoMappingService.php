<?php

namespace App\Service;

use App\Models\MercadoPagoMapping;

class MercadoPagoMappingService
{
    private ?array $mappings = null;

    public function getCategoryId(string $description): ?int
    {
        return $this->resolve($description)?->category_id;
    }

    public function getPlaceId(string $description): ?int
    {
        return $this->resolve($description)?->place_id;
    }

    public function hasMatch(string $description): bool
    {
        return $this->resolve($description) !== null;
    }

    private function resolve(string $description): ?MercadoPagoMapping
    {
        $description = strtolower($description);

        $this->mappings ??= MercadoPagoMapping::where('is_default', false)
            ->orderByRaw('LENGTH(keyword) DESC')
            ->get()
            ->all();

        foreach ($this->mappings as $mapping) {
            if (str_contains($description, strtolower($mapping->keyword))) {
                return $mapping;
            }
        }

        return null;
    }
}
