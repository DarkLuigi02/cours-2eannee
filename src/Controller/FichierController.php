<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\FichierType;
use App\Entity\Fichier;
use App\Entity\Theme;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;


class FichierController extends AbstractController
{
    #[Route('/ajoutfichier', name: 'ajoutfichier')]
    public function ajoutfichier(Request $request): Response
    {
        $fichier = new Fichier();
        $form = $this->createform(FichierType::class, $fichier);

        if ($request->get('id')!=null){
            $f = $doctrine->getRepository(Fichier::class)->find($request->get('id'));
            try {
                $filesystem = new Filesystem();
                if ($filesystem->exists($this->getParameter('file_directory').'/'.$f->getNom())){
                    $filesystem->remove([$this->getParameter('file_directory').'/'.$f->getNom()]);
                }
            }catch (UOExceptionInterface $exception){
                $this->addFlash('notice', 'Encore une Erreur');                
            }

            $em->remove($f);
            $em->flush();
            return $this->redirectToRoute('profile');
        }

        $doctrine = $this->getDoctrine();
        $fichiers = $doctrine->getRepository(Fichier::class)->findBy(array(), array('date'=>'DESC'));
        
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){

                $idTheme = $form->get('theme')->getData();
                $theme = $this->getDoctrine()->getRepository(Theme::class)->find($idTheme);

                //dump($theme);

                $fichierPhysique= $fichier->getNom();

                $fichier->setDate(new \DateTime());
                $ext='';
                if($fichierPhysique->guessExtension()!=null){
                    $ext= $fichierPhysique->guessExtension();
                }
                $fichier->setUtilisateur($this->getUser());
                $fichier->setExtension($ext);
                $fichier->setOriginal($fichierPhysique->getClientOriginalName());
                $fichier->setTaille($fichierPhysique->getSize());
                $fichier->setNom(md5(uniqid()));
                $fichier->addTheme($theme);
                try{
                    $this->addFlash('notice',"le fichier est envoyé");
                    $fichierPhysique->move($this->getParameter('file_directory'),$fichier->getNom());
                    $em = $this-> getDoctrine()->getManager();
                    $em->persist($fichier);
                    $em->flush();
                }catch(FileExeption $e){
                    $this->addFlash('notice',"Erreur dans l'envoi");
                }
                
                return $this->redirectToRoute('profile');
            }
        }        
        return $this->render('fichier/ajoutFichier.html.twig', ['form'=>$form->createView(), 'fichiers'=>$fichiers]); 
    }

    #[Route('/miles/listefichier', name: 'listefichier')]
    public function listefichier(Request $request): Response
    {
        $fichier = new Fichier();
        $form = $this->createform(FichierType::class, $fichier);

        if ($request->get('id')!=null){
            $f = $doctrine->getRepository(Fichier::class)->find($request->get('id'));
            try {
                $filesystem = new Filesystem();
                if ($filesystem->exists($this->getParameter('file_directory').'/'.$f->getNom())){
                    $filesystem->remove([$this->getParameter('file_directory').'/'.$f->getNom()]);
                }
            }catch (UOExceptionInterface $exception){
                $this->addFlash('notice', 'Encore une Erreur');                
            }

            $em->remove($f);
            $em->flush();
            return $this->redirectToRoute('listefichier');
        }

        $doctrine = $this->getDoctrine();
        $fichiers = $doctrine->getRepository(Fichier::class)->findBy(array(), array('date'=>'DESC'));
        
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){

                $idTheme = $form->get('theme')->getData();
                $theme = $this->getDoctrine()->getRepository(Theme::class)->find($idTheme);

                //dump($theme);

                $fichierPhysique= $fichier->getNom();

                $fichier->setDate(new \DateTime());
                $ext='';
                if($fichierPhysique->guessExtension()!=null){
                    $ext= $fichierPhysique->guessExtension();
                }
                $fichier->setExtension($ext);
                $fichier->setOriginal($fichierPhysique->getClientOriginalName());
                $fichier->setTaille($fichierPhysique->getSize());
                $fichier->setNom(md5(uniqid()));
                $fichier->addTheme($theme);
                try{
                    $this->addFlash('notice',"le fichier est envoyé");
                    $fichierPhysique->move($this->getParameter('file_directory'),$fichier->getNom());
                    $em = $this-> getDoctrine()->getManager();
                    $em->persist($fichier);
                    $em->flush();
                }catch(FileExeption $e){
                    $this->addFlash('notice',"Erreur dans l'envoi");
                }
                
                return $this->redirectToRoute('listefichier');
            }
        }        
        return $this->render('fichier/listeFichier.html.twig', ['form'=>$form->createView(), 'fichiers'=>$fichiers]); 
    }

    #[Route('/telechargement-fichier/{id}', name: 'telechargement',requirements:["id"=>"\d+"])]
    public function telechargementFichier(int $id)
    {
        $doctrine = $this->getDoctrine();
        $repoFichier = $doctrine->getRepository(Fichier::class);
        $fichier = $repoFichier->find($id);
        if ($fichier == null){
            $this->redirectToRoute('ajoutfichier');
        }else{
          return $this->file($this->getParemeter('file_directory').'/'.$fichier->getNom(),$fichier->getOriginal());  
        }
    }

    #[Route('/partage-fichier/{id}', name: 'partage',requirements:["id"=>"\d+"])]
    public function partageFichier(int $id)
    {
        $doctrine = $this->getDoctrine();
        $repoFichier = $doctrine->getRepository(Fichier::class);
        $fichier = $repoFichier->find($id);
        if ($fichier == null){
            $this->redirectToRoute('ajoutfichier');
        }else{
          return $this->file($this->getParemeter('file_directory').'/'.$fichier->getNom(),$fichier->getOriginal());  
        }
    }
}