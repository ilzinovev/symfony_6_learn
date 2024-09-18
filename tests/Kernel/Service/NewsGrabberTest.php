<?php

namespace Tests\Kernel\Service;

use App\Entity\Blog;
use App\Factory\BlogFactory;
use App\Factory\UserFactory;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use App\Service\HttpClient;
use App\Service\NewsGrabber;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class NewsGrabberTest extends KernelTestCase
{

    use ResetDatabase, Factories;

    public function testSomething(): void
    {
        self::bootKernel();

        $user           = UserFactory::createOne()->_real();
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('find')->willReturn($user);
        static::getContainer()->set(UserRepository::class, $userRepository);


        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->method('get')->willReturnCallback(function ($url) {
            if ($url == 'https://www.engadget.com/news/') {
                return file_get_contents('tests/DataProvider/index.html');
            } else {
                static $index = 1;
                $html = file_get_contents($url);
                file_put_contents('tests/DataProvider/news' . $index . '.html', $html);
                $content = file_get_contents('tests/DataProvider/news' . $index . '.html');
                $index++;

                return $content;
            }


            //     ->with('https://www.engadget.com/news/')
            //  ->willReturn(file_get_contents('tests/DataProvider/index.html'));
        });

        static::getContainer()->set(HttpClient::class, $httpClient);


        $newsGrabber = self::getContainer()->get(NewsGrabber::class);
        assert($newsGrabber instanceof NewsGrabber);

        $logger = $this->createMock(LoggerInterface::class);
        $newsGrabber->setLogger($logger)->importNews();


        $blogRepository = self::getContainer()->get(BlogRepository::class);
        assert($blogRepository instanceof BlogRepository);

        $blogs = $blogRepository->getBlogs();

        self::assertCount(6, $blogs);
    }
}
