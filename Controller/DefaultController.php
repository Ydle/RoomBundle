<?php

namespace Ydle\RoomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
            $roomId = $room->getId();
            $em = $this->getDoctrine()->getManager();
            $em->persist($room);
            $em->flush();
            $message = $this->get('translator')->trans('Pièce ajoutée avec succès');
            if($roomId){
                $message = 'Pièce modifié avec succès';
                $this->get('ydle.logger')->log('info', 'Room #'.$room->getId().' modified', 'hub');
            } else {
                $this->get('ydle.logger')->log('info', 'Room #'.$room->getId().' created', 'hub');
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
        $roomId = $request->get('room');
        $object = $this->get("ydle.rooms.manager")->getRepository()->find($roomId);
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->remove($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', 'Room removed', 'hub');
        $this->get('ydle.logger')->log('info', 'Room #'.$roomId.' deleted');
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
        
        if($isActive)  {
            $this->get('ydle.logger')->log('info', 'Room #'.$object->getId().' activated', 'hub');
        } else {
            $this->get('ydle.logger')->log('info', 'Room #'.$object->getId().' deactivated', 'hub');
        }
        return $this->redirect($this->generateUrl('rooms'));
    }
    
    public function detailAction(Request $request)
    {
        $room = $this->get("ydle.rooms.manager")->getRepository()->find($request->get('room'));
        
        $lastData = $this->get('ydle.data.manager')->getLastData($room->getId());
        
        return $this->render('YdleRoomBundle:Default:detail.html.twig', array(
            'room' => $room,
            'data' => $lastData
        ));
    }
    
    public function dataAction(Request $request)
    {
        $msg = 'ok';
        $result = array();
        $roomId = $request->get('room');
        $nodeId = $request->get('node');
        $typeId = $request->get('type');
        $startDate = date("2014-01-27 00:00:00");
        $label = '';
        if(!$type = $this->get('ydle.sensortypes.manager')->getRepository()->find($typeId)){
            $msg = 'ko';
        } else {

            $params = array(
                'room_id' => $roomId,
                'node_id' => $nodeId,
                'type_id' => $typeId,
                'start_date' => $startDate
            );
            $data = $this->get("ydle.data.manager")->findByRoom($params);

            foreach($data as $res){
                $result[] = array($res->getCreated()->format('U') * 1000, $res->getData());
            }
            $label = $type->getName();
        }
        return new JsonResponse(array('msgReturn' => $msg, 'label' => $label, 'data' => $result, 'roomId' => $roomId, 'nodeId' => $nodeId));
    }
}
