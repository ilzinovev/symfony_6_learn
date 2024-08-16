<?php

// src/MessageHandler/SmsNotificationHandler.php
namespace App\MessageHandler;

use App\Entity\Blog;
use App\Message\ContentWatchJob;
use App\Repository\BlogRepository;
use App\Service\ContentWatchApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ContentWatchHandler
{

    public function __construct(
        private ContentWatchApi $contentWatchApi,
        private BlogRepository $blogRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(ContentWatchJob $contentWatchJob)
    {
        $blogId = (int)$contentWatchJob->getContent();
        $blog   = $this->blogRepository->find($blogId);
        $blog->setPercent($this->contentWatchApi->checkText($blog->getText()));
        $this->em->flush();
    }
}