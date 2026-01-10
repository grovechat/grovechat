<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class S3StorageData extends Data
{
    public function __construct(
        public string $value,
        public string $label,
        public string $helpLink,
        /** @var \App\Data\S3StorageRegionData[] */
        public array $regions,
    ) {}
}
