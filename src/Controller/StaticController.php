<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use App\Form\AvisType;
use App\Form\InscriptionType;
use App\Form\InscriptionCType;
use App\Form\AjoutUserType;
use App\Entity\Contact;
use App\Entity\Avis;
use App\Entity\Utilisateur;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StaticController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->render('static/accueil.html.twig', []);
    }

/* #[Route('/contact', name: 'contact')]
    public function contact(Request $request, \Swift_Mailer $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createform(ContactType::class, $contact);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $nom= $form->get('nom')->getData();
                $sujet= $form->get('sujet')->getData();
                $contenu= $form->get('message')->getData();
                $this->addFlash('notice',"les info sont bien envoyé par".$nom->getNom());
                $message = (new \Swift_Message($form->get('sujet')->getData()))
                ->setFrom($from->get('email')->getData())
                ->setTo('coquelle.gael@gmail.com')
                //->setBody($from->get('message')->getData());
                ->setBody($this->renderView('emails/contact-mail.html.twig', array('nom'=>$nom, 'sujet'=>$sujet, 'message'=>$contenu)),'text/html');
                $mailer->send($message);
                return $this->redirectToRoute('contact');
            }
        }
        return $this->render('static/contact.html.twig', ['form'=>$form->createView()]);
    }*/

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, \Swift_Mailer $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createform(ContactType::class, $contact);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $this->addFlash('notice',"les info sont bien envoyé par".$contact->getNom());
                $message = (new \Swift_Message($contact->getSujet()))
                ->setFrom($contact>getEmail())
                ->setTo('coquelle.gael@gmail.com')
                ->setBody($this->renderView('emails/contact-mail.html.twig', array('nom'=>$contact->getNom(), 'sujet'=>$contact->getSujet(), 'message'=>$contact->getMessage())),'text/html');
                $mailer->send($message);
                
                $em = $this-> getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();
                return $this->redirectToRoute('contact');
            }
        }
        return $this->render('static/contact.html.twig', ['form'=>$form->createView()]);
    }

    #[Route('/miles/liste-contact', name: 'listecontact')]
    public function Listecontact(): Response
    {
        $repoContact = $this->getDoctrine()->getRepository(Contact::class);
        $contacts = $repoContact->findBy(array(),array('nom'=>'ASC'));
        
        
        return $this->render('static/listecontact.html.twig', ['contacts'=>$contacts]);
    }

    #[Route('/miles/modifcontact-fichier/{id}', name: 'modifcontact',requirements:["id"=>"\d+"])]
    public function modifContact(Request $request, int $id)
    {
        $avis=$this->getDoctrine()->getRepository(Contact::class)->find($id);

        $form = $this->createForm(ContactType::class,$contact);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
        
                $em = $this-> getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();
                return $this->redirectToRoute('contact');
            }
        }             
          return $this->render('static/modifcontact.html.twig', ['form'=>$form->createView()]);  
    }
    
    #[Route('/inscription', name: 'inscription')]
    public function incription(Request $request): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createform(InscriptionType::class, $utilisateur);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $this->addFlash('notice',"Merci de votre inscription");
                $utilisateur->setDateInscription(new \DateTime());
                $em = $this-> getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();

                return $this->redirectToRoute('app_login');
                
            }
        }               
        return $this->render('static/incription.html.twig', ['form'=>$form->createView(), 'utilisateurs'=>$utilisateur]); 
    }  
    
    #[Route('/inscriptionComplet', name: 'inscriptionC')]
    public function incriptionComplet(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {   
        
        $utilisateur = new Utilisateur();
        $user = new User();
        $utilisateur->setUser($user);
        $user->setUtilisateur($utilisateur);
        $form = $this->createform(InscriptionCType::class, $utilisateur);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $this->addFlash('notice',"Merci de votre inscription");
                $user->setEmail($form->get('email')->getData());
                $user->setPassword($passwordHasher->hashPassword($user,$form->get('password')->getData()));
                $photoPhysique= $photo->getNom();
                $ext=null;
                if($photoPhysique->guessExtension()!= 'png' || 'gif' || 'jpg'|| 'jpeg'){
                        $ext= $photoPhysique;
                }else{
                        $this->addFlash('notice',"Ce n'est pas un format image");
                }
                $user->setPhoto($ext);
                $user->setRoles(array('ROLE_USER'));

                $em = $this-> getDoctrine()->getManager();
                $em->persist($utilisateur);
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('accueil');
                
            }
        }               
        return $this->render('static/incriptionComplet.html.twig', ['form'=>$form->createView(), 'utilisateurs'=>$utilisateur]); 
    }  
 
    #[Route('/avis', name: 'avis')]
    public function avis(Request $request): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $this->addFlash('notice',"Merci d'avoir donné votre avis M/Mme.".$avis->getNom());
                
                $em = $this-> getDoctrine()->getManager();
                $em->persist($avis);
                $em->flush();
                return $this->redirectToRoute('avis');
            }
        }
        return $this->render('static/avis.html.twig', ['form'=>$form->createView()]);
    }

    #[Route('/ajoutavis', name: 'ajoutavis')]
        public function ajoutavis(Request $request): Response
        {
            $avis = new Avis();
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
                return $this->redirectToRoute('ajoutfichier');
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
                    
                    return $this->redirectToRoute('ajoutfichier');
                }
            }        
            return $this->render('fichier/ajoutFichier.html.twig', ['form'=>$form->createView(), 'fichiers'=>$fichiers]); 
        }
    

    #[Route('/mention', name: 'mention')]
    public function mention(): Response
    {
        return $this->render('static/mention.html.twig', []);
    }

    #[Route('/miles/modifavis-fichier/{id}', name: 'modifavis',requirements:["id"=>"\d+"])]
    public function modifAvis(Request $request, int $id)
    {
        $avis=$this->getDoctrine()->getRepository(Avis::class)->find($id);

        $form = $this->createForm(AvisType::class,$avis);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
        
                $em = $this-> getDoctrine()->getManager();
                $em->persist($avis);
                $em->flush();
                return $this->redirectToRoute('avis');
            }
        }             
          return $this->render('avis/modifavis.html.twig', ['form'=>$form->createView()]);  
    }

    #[Route('/apropos', name: 'apropos')]
    public function apropos(): Response
    {
        return $this->render('static/apropos.html.twig', []);
    }
    
    #[Route('/ajoutuser', name: 'ajoutuser')]
    public function ajoutUser(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createform(AjoutUserType::class, $user);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $user->setRoles(array('Role_User'));
                $user->setPassword($passwordHasher->hashPassword($user,$user->getPassword()));
                $em = $this-> getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $this->addFlash('notice',"inscription Reussi");

                return $this->redirectToRoute('app_login');
                
            }
        }               
        return $this->render('static/ajout-user.html.twig', ['form'=>$form->createView()]); 
    } 
}