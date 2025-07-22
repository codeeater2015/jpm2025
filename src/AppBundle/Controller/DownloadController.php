<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Route("/download")
 */

class DownloadController extends Controller
{
    /**
    * @Route("", name="download_index", options={"main" = true})
    */

    public function downloadsAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $hostIp = $this->getParameter('host_ip');
        $imgUrl = $this->getParameter('img_url');

        return $this->render('template/download/index.html.twig',[ 'user' => $user, 'hostIp' => $hostIp , 'imgUrl' => $imgUrl ]);
    } 
}
