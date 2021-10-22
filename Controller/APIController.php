<?php

declare(strict_types=1);

namespace Pumukit\PaellaStatsBundle\Controller;

use Pumukit\PaellaStatsBundle\Document\UserAction;
use Pumukit\PaellaStatsBundle\Services\UserActionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/paella")
 */
class APIController extends AbstractController
{
    private $documentManager;
    private $userActionService;

    public function __construct(DocumentManager $documentManager, UserActionService $userActionService)
    {
        $this->documentManager = $documentManager;
        $this->userActionService = $userActionService;
    }

    /**
     * @Route("/save_single/{videoID}", requirements={"in": "\d+", "out": "\d+"})
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     *
     * @param mixed $videoID
     */
    public function saveSingleAction(Request $request, $videoID): JsonResponse
    {
        $this->saveAction($request, $videoID, $request->get('in'), $request->get('out'));

        return new JsonResponse(
            [
                'id' => $videoID,
            ]
        );
    }

    /**
     * @Route("/save_group/{videoID}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     *
     * @param mixed $videoID
     */
    public function saveGroupAction(Request $request, $videoID)
    {
        $intervals = $request->get('intervals');

        if (is_array($intervals)) {
            foreach ($intervals as $interval) {
                if (isset($interval['in'], $interval['out'])) {
                    $this->saveAction($request, $videoID, $interval['in'], $interval['out']);
                }
            }
        }

        return new JsonResponse(
            [
                'id' => $videoID,
            ]
        );
    }

    private function saveAction(Request $request, $multimediaObject, $in, $out): void
    {
        $ip = $request->getClientIp();
        $userAgent = $request->server->get('HTTP_USER_AGENT');
        $user = ($this->getUser()) ? $this->getUser()->getId() : null;
        $session = new Session();
        $session = $session->getId();
        $isLive = json_decode($request->get('isLive'));

        $series = $this->userActionService->getSerieFromVideo($multimediaObject);

        $userAction = new UserAction($ip, $session, $userAgent, $multimediaObject, $series, $in, $out, $isLive, $user);

        $this->documentManager->persist($userAction);
        $this->documentManager->flush();
    }
}
