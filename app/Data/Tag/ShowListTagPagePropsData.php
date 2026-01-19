<?php

namespace App\Data\Tag;

use Spatie\LaravelData\Data;

class ShowListTagPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\Tag\ListTagItemData[] */
        public array $tag_list,
    ) {}
}
