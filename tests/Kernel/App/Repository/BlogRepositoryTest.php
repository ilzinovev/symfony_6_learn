<?php

namespace App\Tests\Kernel\App\Repository;

use App\Factory\BlogFactory;
use App\Factory\UserFactory;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class BlogRepositoryTest extends KernelTestCase
{

    use ResetDatabase, Factories;

    public function testSomething(): void
    {
        self::bootKernel();

        $user = UserFactory::createOne();
        BlogFactory::createOne(['user' => $user, 'title' => 'test']);
        BlogFactory::createMany(6, ['user' => $user]);

        $blogRepository = self::getContainer()->get(BlogRepository::class);

        $blogs = $blogRepository->getBlogs();

        $this->assertCount(7, $blogs);

        $this->assertSame('test', $blogs[0]->getTitle());
    }
}
