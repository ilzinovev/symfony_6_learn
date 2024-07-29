<?php

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagTransformer implements DataTransformerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TagRepository $tagRepository
    ) {
    }


    public function transform($tag): string
    {
        if (null === $tag) {
            return '';
        }

        return $tag->getId();
    }


    public function reverseTransform(mixed $tags = null): ?ArrayCollection
    {
        // no issue number? It's optional, so that's ok
        if (!$tags) {
            return null;
        }

        $items = explode(',', $tags);
        $items = array_map('trim', $items);
        $items = array_unique($items);


        $tags = new ArrayCollection();
        foreach ($items as $item) {
            $tags->add($item);
        }
        $issue = $this->tagRepository
            ->find($tag);


        return $tags;
    }
}