<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\AddCommentType;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{

    /** 
     * @Route("/", name="home")
    */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig', ['title' => "Bienvenue sur notre blog Symfony", 'age' => 25]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        // $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'title' => 'Liste des articles',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function create(Article $articleCreate = null, Request $request, EntityManagerInterface $manager): Response
    {
       /* if($request->request->count() > 0)
        {
            $articleCreate = new Article;

            /$articleCreate->setTitle($request->request->get('title'))
                            ->setContent($request->request->get('content'))
                            ->setImage($request->request->get('image'))
                            ->setCreatedAt(new \DateTime);

            $manager->persist($articleCreate);
            $manager->flush();

            return $this->redirectToRoute('blog_show', [
                'id' => $articleCreate->getId()
            ]);

        }*/

        if(!$articleCreate)
        {
        $articleCreate = new Article;
        }

       // $articleCreate->setTitle("Titre à la con")
                        //->setContent("Contenu à la con");

        /*$form = $this->createFormBuilder($articleCreate)
                    ->add('title')
                    ->add('content')
                    ->add('image')
                    ->getForm();*/

        $form = $this->createForm(ArticleFormType::class, $articleCreate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            if(!$articleCreate->getId())
            {
            $articleCreate->setCreatedAt(new \DateTime);
            }
            

            $manager->persist($articleCreate);
            $manager->flush();

            return $this->redirectToRoute('blog_show', [
                'id' => $articleCreate->getId()
            ]);
        }

        return $this->render('blog/create.html.twig', [
            "formArticle" => $form->createView(),
            "editMode" => $articleCreate->getId()
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager): Response
    {
        //$repoArticle = $this->getDoctrine()->getRepository(Article::class);
        //$article = $repoArticle->find($id);
        $comment = new Comment;
        $formComment = $this->createForm(AddCommentType::class, $comment);

        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid())
        {
            $comment->setCreatedAt(new \DateTime);
            $comment->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', "Le commentaire a bien été posté !");
            return $this->redirectToRoute('blog_show', ['id' => $article->getId() ]);
        }

        

        return $this->render('blog/show.html.twig', [
            'articleTwig' => $article,
            'formContent' => $formComment->createView()
        ]);
    }

}
