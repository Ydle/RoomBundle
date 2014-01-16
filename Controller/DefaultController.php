<?php

namespace Ydle\RoomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ydle\RoomBundle\Entity\Room;

class DefaultController extends Controller
{

    /**
     * Homepage for rooms managment
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function indexAction(Request $request)
    {
        $rooms = $this->get("ydle.rooms.manager")->findAllByName();
        
        return $this->render('YdleRoomBundle:Default:index.html.twig', array(
        	'items' => $rooms
        ));
    }
    
    /**
     * Display a form to create or edit a room.
     * 
     * @param Request $request
     */
    public function formAction(Request $request)
    {
        // Manage edition mode
        $room = new Room();
        $this->currentRoom = $request->get('room');
        if($this->currentRoom){
            $room = $this->get("ydle.rooms.manager")->getRepository()->find($request->get('room'));
        }
        
        $form = $this->createForm("rooms_form", $room);
        $form->handleRequest($request);

        if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($room);
			$em->flush();
			$message = $this->get('translator')->trans('Pièce ajoutée avec succès');
			if($room->getId()){
				$message = 'Pièce modifié avec succès';
			}
			$this->get('session')->getFlashBag()->add('notice', $message);
			return $this->redirect($this->generateUrl('rooms'));
        }
        
        return $this->render('YdleRoomBundle:Default:form.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    
    /**
     * Delete a room
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function roomdeleteAction(Request $request)
    {
        $object = $this->get("ydle.rooms.manager")->getRepository()->find($request->get('room'));
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->remove($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', 'Room removed');
        return $this->redirect($this->generateUrl('rooms'));
    }
    
    /**
    * Manage activation or de-activation of a room
    * 
    * @param Request $request
    */
    public function roomactivationAction(Request $request)
    {
        $isActive = $request->get('active');
        $message = $isActive?'Room activated':'Room deactivated';
        $object = $this->get("ydle.rooms.manager")->getRepository()->find($request->get('room'))->setIsActive($isActive);
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->persist($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', $message);
        return $this->redirect($this->generateUrl('rooms'));
    }
}
