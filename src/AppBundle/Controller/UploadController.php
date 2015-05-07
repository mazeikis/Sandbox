<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Image;
use AppBundle\Form\Type\UploadFormType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UploadController extends Controller
{
    public function indexAction(Request $request)
    {
    
        $image = new Image();       
        $form = $this->createForm(new UploadFormType());
        $user = $this->getUser();
        $form->handleRequest($request);
        if($form->isValid()){ 
            if (false === $this->get('security.authorization_checker')->isGranted('create', $image, $user)) {
                throw new AccessDeniedException('Unauthorised access!');
            }          
            $data = $form->getData();
            $imageSizeDetails = getimagesize($data['file']->getPathName());
            $resolution = strval($imageSizeDetails[0]).' x '.strval($imageSizeDetails[1]);
            $randomName =  sha1(uniqid(mt_rand(), true));
            $image->setFileName($randomName)
                  ->setSize($data['file']->getSize())
                  ->setResolution($resolution)
                  ->setExtension($data['file']->getClientOriginalExtension())
                  ->setTitle($data['title'])
                  ->setDescription($data['description'])
                  ->setCreated(new \Datetime())
                  ->setUpdated(new \Datetime())
                  ->setOwner($user);
            $data['file']->move(__DIR__.'/../../../web/images', $randomName.'.'.$data['file']->getClientOriginalExtension());
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Image uploaded!');
            return $this->redirectToRoute('_gallery');
        } else {
          //empty
        }

        return $this->render('AppBundle:Twig:upload.html.twig', array('title' => 'sandbox|project', 'form' => $form->createView()));
    }
}