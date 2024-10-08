<?php

// src/EventListener/UserChangedNotifier.php
namespace App\EventListener;

use App\Entity\Blog;
use App\Entity\User;
use App\Message\ContentWatchJob;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

/*
#[AsEntityListener(event: Events::postUpdate, method: 'preUpdate', entity: Blog::class)]
class BlogListener
{
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function preUpdate(Blog $blog, PostUpdateEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $user = $em->getRepository(User::class)->find(1);
        $blog->setPercent(1);
    }
}*/

#[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
class BlogListener
{


    private array $entities = [];

    public function __construct(
        private MessageBusInterface $bus
    ) {
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function postFlush(PostFlushEventArgs $event): void
    {
        foreach ($this->entities as $entity) {
            $this->bus->dispatch(new ContentWatchJob($entity->getId()));
        }
    }

    public function postPersist(PostPersistEventArgs $event): void
    {
        if ($event->getObject() instanceof Blog) {
            $this->entities[] = $event->getObject();
        }
    }
}