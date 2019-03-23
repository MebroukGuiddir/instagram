<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use App\Service\DBRequest;
class AjaxController extends AbstractController
{


     /**
    * @Route("/ajax/set_slot", name="set_slot_status", methods={"POST"},condition="request.isXmlHttpRequest()")
    */

    public function setSlotStatus(Request $req,DBRequest $bd){
     
        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(['error' => 'auth required'], 401);
         }
        $value=$bd->setSlot($this->getUser(),$req->request->get('slot'),$req->request->get('value'));  
        
        return new JsonResponse(['Success'=> json_encode($value)],200);


    }

    /**
    * @Route("/ajax/edit_profile", name="edit_profile", methods={"POST"},condition="request.isXmlHttpRequest()")
    */

    public function editProfile(Request $req,DBRequest $bd){
     
        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(['error' => 'auth required'], 401);
         }
        if($req->request->get('pwdConfirm')!== $req->request->get('pwd')) 
            return new JsonResponse(['error'=> ""],200);
        $value=$bd->editProfile($this->getUser(),$req->request->get('pwd'),$req->request->get('email'));  
        
        return new JsonResponse(['Success'=> "profile"],200);


    }

     /**
    * @Route("/ajax/config_bot", name="set_config", methods={"POST"},condition="request.isXmlHttpRequest()")
    */

    public function setBotParameters(Request $req,LoggerInterface $logger,DBRequest $service){

        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(['error' => 'auth required'], 401);
         }
        $logger->info($this->getUser()->getUsername());
        $value=$service->setParams($this->getUser(),$req->request->all()); 
     return new JsonResponse(['output'=> $value]);


    }

    /**
    * @Route("/ajax/set_bot_status", name="set_bot_status", methods={"POST"},condition="request.isXmlHttpRequest()")
    */

    public function setBotStatus(Request $req){

        $bot=$req->request->get('bot');
        $value=$req->request->get('value');
        $message=$bot." bot turned ".$value;
        return new JsonResponse(['output'=> $message]);

    }

}
