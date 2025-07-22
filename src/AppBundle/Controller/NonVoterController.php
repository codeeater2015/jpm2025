<?php
namespace AppBundle\Controller;

use AppBundle\Entity\ProjectVoter;
use AppBundle\Entity\HouseholdHeader;
use AppBundle\Entity\HouseholdDetail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Route("/non-voter")
 */

class NonVoterController extends Controller
{
    /**
     * @Route("", name="non_voter_index", options={"main" = true })
     */

    public function indexAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $hostIp = $this->getParameter('host_ip');
        $imgUrl = $this->getParameter('img_url');

        return $this->render('template/non-voter/index.html.twig', ['user' => $user, "hostIp" => $hostIp, 'imgUrl' => $imgUrl]);
    }
}
