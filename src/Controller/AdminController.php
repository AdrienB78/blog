<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\AdminFormType;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/articles", name="admin_articles")
     * @Route("/admin/{id}/remove", name="admin_remove_article")
     */
    public function adminArticles(EntityManagerInterface $manager, ArticleRepository $repoArticle, Article $article = null): Response
    {
        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();
        
        $articles = $repoArticle->findAll();

        if($article)
        {
            $id = $article->getId();
            $manager->remove($article);
            $manager->flush();

            $this->addFlash('success', "L'article n°$id a bien été supprimé !");
            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/admin_articles.html.twig', [
            'colonnes' => $colonnes,
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/{id}/edit-article", name="admin_edit_article")
     */
    public function adminEditArticle(Article $article, Request $request, EntityManagerInterface $manager)
    {

        $formArticle = $this->createForm(ArticleFormType::class, $article);
        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid())
        {
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', "L'article " . $article->getId() . " a bien été modifié !");
            return $this->redirectToRoute('admin_articles');
        }

        
        return $this->render('admin/admin_edit_article.html.twig', [
            'idArticle' => $article->getId(),
            'formArticle' => $formArticle->createView()
        ]);
    }

    /**
     * @Route("/admin/categories", name="admin_categories")
     * @Route("/admin/{id}/remove-categories", name="admin_remove_categories")
     */
    public function adminCategory(CategoryRepository $repoCategory, EntityManagerInterface $manager, Category $categorys = null): Response
    {
        $colonnes = $manager->getClassMetadata(Category::class)->getFieldNames();
        $category = $repoCategory->findAll();

        if($categorys)
        {
            $nomCategory = $categorys->getTitle();
            $manager->remove($categorys);
            $manager->flush();

            $this->addFlash('success', "La catégorie " . $nomCategory. " a bien été supprimé !");
            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/admin_category.html.twig', [
            "colonnes" => $colonnes,
            'category' => $category
        ]);
    }

    /**
     * @Route("/admin/categories-new", name="admin_new_categories")
     * @Route("/admin/{id}/categories-edit", name="admin_edit_categories")
     */
    public function adminFormCategory(Request $request, EntityManagerInterface $manager, Category $category): Response
    {
        $formCategory = $this->createForm(AdminFormType::class, $category);
        $formCategory->handleRequest($request);
        
        return $this->render('admin/admin_form_category.html.twig');
    }
}
