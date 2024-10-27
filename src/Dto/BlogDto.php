<?php

namespace App\Dto;


class BlogDto
{

    public function __construct(
        public readonly ?string $title,
        public readonly ?string $description,
        public readonly ?string $text,
        public readonly ?string $tags,
    ) {
    }
}