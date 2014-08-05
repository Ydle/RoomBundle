<?php
/*
  This file is part of Ydle.

    Ydle is free software: you can redistribute it and/or modify
    it under the terms of the GNU  Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Ydle is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU  Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with Ydle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Ydle\RoomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ydle\RoomBundle\Entity\Room;

use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{

    /**
     * Homepage for rooms managment
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function indexAction(Request $request)
    {
        
        return $this->render('YdleRoomBundle:Default:index.html.twig', array(
        ));
    }
   


    /**
     * Display a form to create or edit a room
     *
     * @param Request $request
     */
    public function roomFormAction(Request $request)
    {
        $room = new Room();
        // Manage edition mode
        $this->currentRoom = $request->get('room');
        if($this->currentRoom){
            $room = $this->get("ydle.rooms.manager")->getRepository()->find($request->get('room'));
        }
        $action = $this->get('router')->generate('submitRoomForm', array('room' => $this->currentRoom));

        $form = $this->createForm("room_form", $room);
        $form->handleRequest($request);


        return $this->render('YdleRoomBundle:Default:form.html.twig', array(
            'action' => $action,
//            'form' => $form->createView()
        ));
    }
   
    public function submitRoomFormAction(Request $request)
    {
        $statusCode = 200;
        $room = new Room();
        // Manage edition mode
        $this->currentRoom = $request->get('room');
        if($this->currentRoom){
            $room = $this->get("ydle.rooms.manager")->getRepository()->find($request->get('room'));
        }
        $action = $this->get('router')->generate('submitRoomForm', array('room' => $this->currentRoom));

        $form = $this->createForm("room_form", $room);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($room);
            $em->flush();
            $message = $this->get('translator')->trans('room.add.success');
            if($room->getId()){
                $message = $this->get('translator')->trans('room.edit.success');
            }
            $this->get('session')->getFlashBag()->add('notice', $message);
            $this->get('ydle.logger')->log('info', $message, 'hub');
            $statusCode = 201;
        } else {
            $statusCode = 400;
        }

        $html =  $this->renderView('YdleRoomBundle:Default:form.html.twig', array(
            'action' => $action,
            'form' => $form->createView()
        ));

        $response = new Response();
        $response->setContent($html);
        $response->setStatusCode($statusCode);
        $response->headers->set('Content-Type', 'text/html');
        return $response;
    }
 
    
    /**
     * Delete a room
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function roomdeleteAction(Request $request)
    {
        $roomId = $request->get('room');
        $object = $this->get("ydle.rooms.manager")->getRepository()->find($roomId);
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->remove($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', 'Room removed');
        $this->get('ydle.logger')->log('info', 'Room #'.$roomId.' deleted', 'hub');
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
                $data = $res->getData();
                switch($type->getUnit()){
                    case '°':
                        $data = $data / 100;
                        break;
                    case '%':
                        $data = $data / 100;
                        break;
                }
                $result[] = array($res->getCreated()->format('U') * 1000, $data);
            }
            $label = $type->getName();
        }
        return new JsonResponse(array('msgReturn' => $msg, 'label' => $label, 'data' => $result, 'roomId' => $roomId, 'nodeId' => $nodeId));
    }
}
