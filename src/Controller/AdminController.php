<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\AdminFormType;
use App\Form\AddCommentType;
use App\Form\AdminRegistrationFormType;
use App\Form\ArticleFormType;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
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
            if($categorys->getArticles()->isEmpty())
            {
                $nomCategory = $categorys->getTitle();
                $manager->remove($categorys);
                $manager->flush();

                $this->addFlash('success', "La catégorie " . $nomCategory. " a bien été supprimé !");
                
            }
            else
            {
                $this->addFlash("danger", "Il n'est pas possible de supprimer la catégorie car il reste des articles associés à celle ci");
            }
            return $this->redirectToRoute('admin_categories');
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
    public function adminFormCategory(Request $request, EntityManagerInterface $manager, Category $category = null): Response
    {
        if(!$category)
        {
            $category = new Category;
        }

        $formCategory = $this->createForm(AdminFormType::class, $category, [
            'validation_groups' => ['category']
        ]);
        $formCategory->handleRequest($request);

        if($formCategory->isSubmitted() && $formCategory->isValid())
        {
            if(!$category->getId())
            {
                $message = "La catégorie " . $category->getTitle() . " a été enregistré avec succés !";
            }
            else
            {
                $message = "La catégorie " . $category->getTitle() . " a été modifié avec succés !";
            }

            $manager->persist($category);
            $manager->flush();

            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_categories');
        }
        
        return $this->render('admin/admin_form_category.html.twig',[
            'formCategory' => $formCategory->createView()
        ]);
    }

    /**
     * @Route("/admin/comments", name="admin_comments")
     * @Route("/admin/comment/{id}/remove", name="admin_remove_comments")
     */
    public function adminComment(EntityManagerInterface $manager, CommentRepository $repoComments, Comment $comment = null): Response
    {
        $colonnes = $manager->getClassMetadata(Comment::class)->getFieldNames();
        $comments = $repoComments->findAll();

        if($comment)
        {
            $manager->remove($comment);
            $manager->flush();

            $this->addFlash('success', "Le commentaire a bien été supprimé !");
            return $this->redirectToRoute('admin_comments');
        }

        return $this->render('admin/admin_comments.html.twig', [
            'colonnes' => $colonnes,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/admin/comment/{id}/edit", name="admin_edit_comment")
     */
    public function editComment(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        $formComment = $this->createForm(AddCommentType::class, $comment);
        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid())
        {
            $manager->persist($comment);
            $manager->flush();
            
            $this->addFlash('success', "Le commentaire a bien été modifié !");
            return $this->redirectToRoute('admin_comments');
        }
        
        return $this->render('admin/admin_edit_comment.html.twig', [
            'formComment' => $formComment->createView()
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_users")
     * @Route("/admin/user/{id}/remove", name="admin_remove_user")
     */
    public function adminUsers(EntityManagerInterface $manager, UserRepository $repoUsers, User $userbdd = null): Response
    {
        $colonnes = $manager->getClassMetadata(User::class)->getFieldNames();
        $user = $repoUsers->findAll();

        if($userbdd)
        {
            $manager->remove($userbdd);
            $manager->flush();

            $this->addFlash("success", "L'utilisateur a été supprimé avec succès !");
            return $this->redirectToRoute("admin_users");
        }

        return $this->render('admin/admin_users.html.twig', [
            "colonnes" => $colonnes,
            "users" => $user
        ]);
    }

    /**
    * @Route("/admin/user/{id}/edit", name="admin_edit_user")
    */
    public function adminUserEdit(User $user, Request $request, EntityManagerInterface $manager): Response
    {
        $formUser = $this->createForm(AdminRegistrationFormType::class, $user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success", "La modification a été effectuée !");
            return $this->redirectToRoute('admin_users');
        }

        return $this->render("admin/admin_edit_user.html.twig", [
            'formUser' => $formUser->createView()
        ]);
    }

}
