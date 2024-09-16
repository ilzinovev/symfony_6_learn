<?php

namespace App\Service;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class NewsGrabber
{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly BlogRepository $blogRepository
    ) {
    }

    public function importNews(): void
    {
        $client = new Client([
            'timeout' => 15.0,
        ]);

        $texts    = [];
        $response = $client->get('https://www.engadget.com/news/');
        $crawler  = new Crawler($response->getBody()->getContents());
        $crawler->filter('h4.My\(0\) > a')->each(
            function (Crawler $crawler) use (&$texts) {
                $texts[] = [
                    'title' => $crawler->text(),
                    'href'  => $crawler->attr('href')
                ];
            }
        );

        unset($crawler);

        foreach ($texts as &$text) {
            $response     = $client->get('https://www.engadget.com' . $text['href']);
            $crawler      = new Crawler($response->getBody()->getContents());
            $crawlerBody  = $crawler->filter('div.caas-body')->first();
            $text['text'] = $crawlerBody->text();
        }

        unset($text);

        $this->saveNews($texts);
    }

    private function saveNews(array $texts): void
    {
        $blogUser = $this->userRepository->find(132);

        foreach ($texts as $item) {
            if($this->blogRepository->getByTitle($item['title'])){
                continue;
            }
            $blog
                = new Blog($blogUser);
            $blog->setTitle($item['title'])
                ->setDescription(mb_substr($item['text'], 0, 1000))
                ->setText($item['text'])
                ->setStatus('pending');

            $this->em->persist($blog);
        }

        $this->em->flush();
    }
}