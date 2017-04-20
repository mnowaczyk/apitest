<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Entity\Message;
use ApiBundle\Form\Type\MessageType;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class MessageController extends FOSRestController
{
    /**
     * 
     * Lists all messages
     * ### Response example ###
     * <pre>
     * [
     *      {
     *          "id":1,
     *          "title":"aaaaa",
     *          "content":"bbbbb",
     *          "parent":null,
     *      },
     *      {
     *           "id":3,
     *           "title":"aaaa",
     *           "content":"bbbbb",
     *           "parent":null,
     *      },
     *      {
     *         "id":2,
     *         "title":"aaa",
     *         "content":"bbb",
     *         "parent":1,
     *      },
     * 
     * ]
     * 
     * </pre>
     * @return array
     * 
     * @Rest\View()
     * @Rest\Get("/messages")
     * @ApiDoc(resource=true, description="Show all messages",
     * output={"class"="array<ApiBundle\Entity\Message>", "groups"={"default"}}
     * )
     * 
     */
    public function getMessagesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository('ApiBundle:Message')->findAll();
        
        return $messages;
    }
    /**
     * 
     * Shows one message
     * ### Response example ###
     * <pre>
     * {
     *      "id":1,
     *      "title":"aaaaa",
     *      "content":"bbbbb",
     *      "parent":null,
     * }
     * </pre>
     * @return array
     * 
     * @Rest\View()
     * @Rest\Get("/messages/{id}")
     * @ApiDoc(resource=true, description="Show one message",  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="message id"
     *      }
     *  },
     *  output={"class"="ApiBundle\Entity\Message", "groups"={"default"}}
     * )
     * 
     */
    public function getMessageAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('ApiBundle:Message')->find($id);
        if (!$message){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        
        return $message;
    }
    
    
     
    /**
     * 
     * Shows thread starting with specified message
     * ###  Response example ###
     * <pre>
     *  {
     *     "id":1,
     *     "title":"test",
     *     "content":"parent message content",
     *     "children":[
     *        {
     *           "id":2,
     *           "title":"test",
     *           "content":"child message content",
     *           "children":[
     *  
     *           ]
     *        }
     *     ]
     *  }
     * </pre>
     * @return array
     * 
     * @Rest\View(serializerGroups={"default", "thread"})
     * @Rest\Get("/thread/{id}")
     * @ApiDoc(resource=true, description="Show a tree (thread) of messages given root message id", requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="root message id"
     *      }
     *  },
     * output={"class"="ApiBundle\Entity\Message", "groups"={"thread", "default"}})
     * 
     */
    public function threadAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('ApiBundle:Message')->find($id);
        if (!$message){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        
        return $message;
    }
    
    
    /**
     * 
     * Creates new message
     * ### Request example ###
     * <pre>
     * {
     *      "title":"aaaaa",
     *      "content":"bbbbb",
     * }
     * </pre>
     * ### Response example ###
     * <pre>
     * {
     *      "id":1,
     *      "title":"aaaaa",
     *      "content":"bbbbb",
     *      "parent":null,
     * }
     * </pre>
     * @return array
     * 
     * @Rest\View()
     * @Rest\Post("/messages")
     * @ApiDoc(resource=true, description="Create new message",
     * statusCodes={
     *         201="Returned when successful"
     *  },
     * input="ApiBundle\Form\Type\MessageType",
     * responseMap = {
     *        201 = {
     *            "class" = Message::class,
     *            "groups"={"default"}
     *        }}
     * )
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

            return View::create($message, 201);
        }
        
        return View::create($form, 400);
        
    }
    
    /**
     * 
     * Update existing message
     * 
     * ### Request and response are the same as when creating new message ###
     * @return array
     * 
     * @Rest\View()
     * @Rest\Put("/messages/{id}")
     * @ApiDoc(resource=true, description="Update existing message", requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="root message id"
     *      }
     *  },
     *  statusCodes={
     *         200="Returned when successful"
     *  },
     *  input="ApiBundle\Form\Type\MessageType",
     *  output={"class"="ApiBundle\Entity\Message", "groups"={"default"}}
     * )
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

            return $message;
        }
        
        return View::create($form, 400);
        
    }
    
    
    /**
     * 
     * Add a child message to existing message
     * 
     * @return array
     * 
     * @Rest\View()
     * @Rest\Post("/messages/{id}/add_child")
     * @ApiDoc(resource=true, description="Add a child message to existing message", requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="parent message id"
     *      }
     *  },
     *  statusCodes={
     *         201="Returned when successful"
     *  },
     *  input="ApiBundle\Form\Type\MessageType",
     *  output={"class"="ApiBundle\Entity\Message", "groups"={"default"}}
     * 
     * )
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

            return $message;
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
     * @ApiDoc(resource=true, description="Deletes a message",
     * statusCodes={
     *         200="Returned when successful"
     *  })
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
