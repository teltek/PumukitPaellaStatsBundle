<?php

namespace Pumukit\PaellaStatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * @Route("/paella")
 */
class APIController extends Controller
{

	/**
     * @Route("/group")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function groupSaveAction(Request $request)
    {

        $id = $request->get('id');
        $intervals = $request->get('intervals');
        $isLive = $request->get('isLive');
        $ip = $this->container->get('request')->getClientIp();

        return new JsonResponse(
            array(
                    'id' => $id,
                    'intervals' => $intervals,
                    'isLive' => $isLive,
                    'ip' => $ip
                )
            );
    }


    /**
     * @Route("/single")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function singleSaveAction(Request $request)
    {

        $id = $request->get('id');
        $in = $request->get('in');
        $out = $request->get('out');
		$ip = $this->container->get('request')->getClientIp();

        return new JsonResponse(
            array(
                    'id' => $id,
                    'in' => $in,
                    'out' => $out,
                    'ip' => $ip
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

}