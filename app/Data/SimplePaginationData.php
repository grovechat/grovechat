<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SimplePaginationData extends Data
{
    public function __construct(
        public int $current_page,
        public int $last_page,
        public int $per_page,
        public int $total,
    ) {}
}
