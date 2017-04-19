<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Entity\Message;
use ApiBundle\Form\Type\MessageType;
use FOS\RestBundle\View\View;

class MessageController extends FOSRestController
{
    /**
     * 
     * Lists all messages
     * 
     * @return array
     * 
     * @Rest\View()
     * @Rest\Get("/messages")
     * 
     */
    public function getMessagesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository('ApiBundle:Message')->findAll();
        
        return ['messages' => $messages];
    }
    /**
     * 
     * Shows one message
     * 
     * @return array
     * 
     * @Rest\View()
     * @Rest\Get("/messages/{id}")
     * 
     */
    public function getMessageAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('ApiBundle:Message')->find($id);
        if (!$message){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        
        return ['message' => $message];
    }
    
    
     
    /**
     * 
     * Shows thread starting with specified message
     * 
     * @return array
     * 
     * @Rest\View(serializerGroups={"default", "thread"})
     * @Rest\Get("/thread/{id}")
     * 
     */
    public function threadAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('ApiBundle:Message')->find($id);
        if (!$message){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        
        return ['message'=>$message];
    }
    
    
    /**
     * 
     * Creates new message
     * 
     * @return array
     * 
     * @Rest\View()
     * @Rest\Post("/messages")
     * 
     */
    public function postMessageAction(Request $request)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return View::create(['message' =>  $message], 201);
        }
        
        return View::create($form, 400);
        
    }
    
    /**
     * 
     * Updates existing message
     * 
     * @return array
     * 
     * @Rest\View()
     * @Rest\Put("/messages/{id}")
     * 
     */
    public function putMessageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('ApiBundle:Message')->find($id);
        if (!$message){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        
        $form = $this->createForm(MessageType::class, $message, ['method' => 'PUT']);
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $em->persist($message);
            $em->flush();

            return ['message' =>  $message];
        }
        
        return View::create($form, 400);
        
    }
    
    
    /**
     * 
     * Adds a child message to existing one
     * 
     * @return array
     * 
     * @Rest\View()
     * @Rest\Post("/messages/{id}/add_child")
     * 
     */
    public function addChildMessageAction(Request $request, $id)
    {
        $message = new Message();
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository('ApiBundle:Message')->find($id);
        if (!$parent){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        $message->setParentMessage($parent);
        $form = $this->createForm(new MessageType(), $message);
        
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            
            $em->persist($message);
            $em->flush();

            return ['message' =>  $message];
        }
        
        return View::create($form, 400);
    }
    
    /**
     * 
     * Deletes a message
     * 
     * @return array
     * 
     * @Rest\View()
     * @Rest\Delete("/messages/{id}")
     * 
     */
    public function deleteMessageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('ApiBundle:Message')->find($id);
        if (!$message){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        $em->remove($message);
        $em->flush();
        return View::create(null, 204);
    }
    
}
