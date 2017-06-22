<?php

namespace Pumukit\PaellaStatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\Session;
use Pumukit\PaellaStatsBundle\Document\UserAction;


/**
 * @Route("/paella")
 */
class APIController extends Controller
{

	/**
     * @Route("/group/{idVideo}")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function groupSaveAction(Request $request, $idVideo)
    {

        $intervals = $request->get('intervals');
        $isLive = json_decode($request->get('isLive'));
        
        if (is_array($intervals) && $idVideo){
            foreach ($intervals as $interval){
                if($interval['in'] && $interval['out']){
                    $this->saveAction($idVideo, $interval['in'], $interval['out'], $isLive);
                }
            }
        }

        return new JsonResponse(
            array(
                    'id' => $idVideo
                )
            );
    }


    /**
     * @Route("/single/{idVideo}")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function singleSaveAction(Request $request, $idVideo)
    {

        if($idVideo){
            $this->saveAction($idVideo, $request->get('in'), $request->get('out'), $request->get('isLive'));
        }

        return new JsonResponse(
            array(
                    'id' => $idVideo
                )
            );
    }


    /**
     * @Route("/test.{_format}", defaults={"_format"="json"}, requirements={"_format": "json|xml"})
     * @Method("GET")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function testAction(Request $request)
    {
        return new Response("hello world!");
    }



    private function saveAction($multimediaObject, $in, $out, $isLive){

        $ip = $this->container->get('request')->getClientIp();
        $userAgent = $this->container->get('request')->headers->get('User-Agent');
        $user = ($this->getUser()) ? $this->getUser()->getId() : null;
        $session = new Session(); 
        $session = $session->getId();

        $userAction = new UserAction($ip, $session, $userAgent, $multimediaObject, $in, $out, False, $user);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($userAction);
        $dm->flush();
    }
}