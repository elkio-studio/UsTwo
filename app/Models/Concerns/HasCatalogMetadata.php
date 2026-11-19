<?php

namespace App\Models\Concerns;

trait HasCatalogMetadata
{
    public function initializeHasCatalogMetadata(): void
    {
        $this->mergeCasts(['catalog_fields' => 'array', 'metadata_version' => 'integer']);
    }

    public static function presentCatalogFields(array $values): array
    {
        return array_values(array_filter(static::CATALOG_METADATA, function ($field) use ($values) {
            $value = $values[$field] ?? null;

            return $value !== null && (! is_string($value) || trim($value) !== '');
        }));
    }

    public function importedMetadata(): array
    {
        $catalog = $this->getAttribute(static::CATALOG_PROVIDER) !== null;
        $imported = $this->catalog_fields;
        if ($catalog && ! is_array($imported)) {
            $imported = static::presentCatalogFields($this->getAttributes());
        }

        return $catalog ? ($imported ?? []) : [];
    }

    public function editableMetadata(): array
    {
        return array_values(array_diff(static::EDITABLE_METADATA, $this->importedMetadata()));
    }
}
