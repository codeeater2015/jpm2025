<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Route("/kfc-summary")
 */

class KfcSummaryController extends Controller
{
     /**
     * @Route("", name="kfc_summary_index", options={"main" = true })
     */

     public function indexAction(Request $request)
     {
         return $this->render('template/kfc-summary/index.html.twig');
     }
}
