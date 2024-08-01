<?php

namespace App\Filter;

class BlogFilter
{
    private ?string $title = null;
    private ?string $text = null;

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }


    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }
}