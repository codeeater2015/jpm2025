<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Route("/raffle")
 */

class RaffleController extends Controller
{
    const STATUS_ACTIVE = 'A';
    const MODULE_MAIN = "PULAHAN_MODULE";

	/**
    * @Route("", name="raffle_index", options={"main" = true})
    */

    public function indexAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $hostIp = $this->getParameter('host_ip');
        $imgUrl = $this->getParameter('img_url');

        return $this->render('template/raffle/index.html.twig',[ 'user' => $user, 'hostIp' => $hostIp , 'imgUrl' => $imgUrl ]);
    }

    /**
     * @Route("/ajax_raffle_get_active_event_attendees",
     *       name="ajax_raffle_get_active_event_attendees",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxGetRaffleActiveEventAttendees(Request $request)
     {
        $em = $this->getDoctrine()->getManager("electPrep2024");
         
        $sql = "SELECT ed.*
        FROM tbl_project_event_detail ed
        INNER JOIN tbl_project_event_header eh ON ed.event_id = eh.event_id 
        
        WHERE eh.status = 'A' ";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

         return new JsonResponse([
             "data" => $data
         ]);
     }
 
}
