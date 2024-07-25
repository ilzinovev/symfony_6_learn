<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'blog_default')]
    public function index(BlogRepository $blogRepository, EntityManagerInterface $em): Response
    {
        $blog = $blogRepository->findOneBy(['id' => 1]);
        $blog->setTitle('Title2');
        $em->flush();
        exit;
       /* $blog = (new Blog())
            ->setTitle('Title')
            ->setDescription('Description')
            ->setText('Text');

        $em->persist($blog);
        $em->flush();*/

        return $this->render('index.html.twig', []);
    }
}
