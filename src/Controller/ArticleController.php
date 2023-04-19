<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Article;
use App\Form\AddArticleType;
use App\Form\DeleteType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'list_articles')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {

        $em = $doctrine->getManager();
        $articles = $em->getRepository(Article::class)->findAll();

        $session = $request->getSession();
        $notification = $session->get('notification');
        $type_notif = $session->get('type_notif');

        $session->remove('notification');
        $session->remove('type_notif');

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'notification' => $notification,
            'type_notif' => $type_notif
        ]);
    }

    #[Route('/article-add', name: 'add_article')]
    public function addArticle(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $article = new Article();
        $form = $this->createForm(AddArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();

            $article->setBody(nl2br($article->getBody()));
            $em->persist($article);
            $em->flush($article);

            $session = $request->getSession();
            $session->set('notification', 'Article ajouté avec succès');
            $session->set('type_notif', 'alert-success');
            return $this->redirectToRoute('list_articles');
        }

        return $this->render('article/addArticle.html.twig', [
            'form' => $form->createView()
        ]);

    }
    
    #[Route('/article-edit/{id_article}', name: 'edit_article')]
    public function editArticle($id_article, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $article = $em->getRepository(Article::class)->find($id_article);

        if($article === null){
            return $this->redirectToRoute('list_articles');
        }

        $session = $request->getSession();

        if(isset($request->request->all()['delete']['id_article'])){
            $em->remove($article);
            $em->flush();
            
            $session->set('notification', 'Article supprimé avec succès');
            $session->set('type_notif', 'alert-success');
            return $this->redirectToRoute('list_articles');
        }

        $form = $this->createForm(AddArticleType::class, $article);
        $deleteForm = $this->createForm(DeleteType::class);
        
        $deleteForm->handleRequest($request);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $article = $form->getData();
            $em->flush($article);

            $session->set('notification', 'Article modifié avec succès');
            $session->set('type_notif', 'alert-success');
            return $this->redirectToRoute('view_article', ['id_article'=> $id_article]);
        }

        return $this->render('article/editArticle.html.twig', [
            'form' => $form->createView(),
            'deleteForm' => $deleteForm->createView(),
            'article' => $article,
        ]);

    }

    #[Route('/article/{id_article}', name: 'view_article')]
    public function viewArticle($id_article, ManagerRegistry $doctrine, Request $request): Response
    {

        $em = $doctrine->getManager();
        $article = $em->getRepository(Article::class)->find($id_article);

        if($article === null){
            return $this->redirectToRoute('list_articles');
        }

        if(is_null($article)){
            $this->redirectToRoute('list_articles');
        }

        $session = $request->getSession();
        $notification = $session->get('notification');
        $type_notif = $session->get('type_notif');

        $session->remove('notification');
        $session->remove('type_notif');

        return $this->render('article/viewArticle.html.twig', [
            'article' => $article,
            'notification' => $notification,
            'type_notif' => $type_notif,
        ]);
    }
}
