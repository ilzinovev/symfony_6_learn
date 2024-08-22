<?php

namespace App\Tests\Kernel\App\Repository;

use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlogRepositoryTest extends KernelTestCase
{
    public function testSomething(): void
    {
        self::bootKernel();

        $blogRepository = self::getContainer()->get(BlogRepository::class);

        $result = $blogRepository->getBlogs();
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }
}
