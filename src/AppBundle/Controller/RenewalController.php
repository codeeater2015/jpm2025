<?php
namespace AppBundle\Controller;

use AppBundle\Entity\RecruitmentDetail;
use AppBundle\Entity\RecruitmentHeader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\RenewedId;

/**
 * @Route("/renew")
 */

class RenewalController extends Controller
{
    const STATUS_ACTIVE = 'A';
    const STATUS_INACTIVE = 'I';
    const STATUS_BLOCKED = 'B';
    const STATUS_PENDING = 'PEN';

    /**
     * @Route("", name="renew_index", options={"main" = true })
     */

    public function indexAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $hostIp = $this->getParameter('host_ip');
        $imgUrl = $this->getParameter('img_url');

        return $this->render('template/renewal/index.html.twig', ['user' => $user, "hostIp" => $hostIp, 'imgUrl' => $imgUrl]);
    }
    

     /**
     * @Route("/ajax_post_renew_id",
     *     name="ajax_post_renew_id",
     *    options={"expose" = true}
     * )
     * @Method("POST")
     */

     public function ajaxPostRenewIdAction(Request $request)
     {
        $em = $this->getDoctrine()->getManager("electPrep2024");
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $proVoterId = $request->get('proVoterId');

        $entity = $em->getRepository("AppBundle:ProjectVoter")->find($proVoterId);

        if (!$entity) {
            return new JsonResponse([], 404);
        }

        $renewed = new RenewedId();
        $renewed->setProVoterId($entity->getProVoterId());
        $renewed->setProIdCode($entity->getProIdCode());
        $renewed->setVoterName($entity->getVoterName());
        $renewed->setGeneratedIdNo($entity->getGeneratedIdNo());
        $renewed->setCreatedBy($user->getUsername());
        $renewed->setCreatedAt(new \DateTime);
        $renewed->setStatus("A");

        $validator = $this->get('validator');
        $violations = $validator->validate($renewed);

        $errors = [];

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return new JsonResponse($errors, 400);
        }

        $em->persist($renewed);
        $em->flush();

        $entity->setHasNewPhoto(1);
        $entity->setCroppedPhoto(1);
        $entity->setUpdatedAt(new \DateTime());
        $entity->setUpdatedBy($user->getUsername());
        $em->flush();

        $serializer = $this->get("serializer");
 
        return new JsonResponse($serializer->normalize($renewed));
     }
 

     /**
     * @Route("/ajax_datatable_renewed_id",
     *     name="ajax_datatable_renewed_id",
     *     options={"expose" = true}
     *     )
     * @Method("GET")
     */

    public function datatableVoterAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $filters = array();
        $filters['v.province_code'] = $request->get("provinceCode");
        $filters['v.voter_group'] = $request->get("voterGroup");
        $filters['v.municipality_no'] = $request->get("municipalityNo");
        $filters['v.brgy_no'] = $request->get("brgyNo");
        $filters['v.precinct_no'] = $request->get("precinctNo");

        $filters['v.voter_name'] = $request->get("voterName");
        $filters['v.birthdate'] = $request->get("birthdate");
        $filters['v.cellphone'] = $request->get("cellphone");
        $filters['v.house_form_sub'] = $request->get("recFormSub");
        $filters['v.rec_form_sub'] = $request->get("houseFormSub");
        $filters['v.record_source'] = $request->get("recordSource");
        $filters['v.has_attended'] = $request->get("hasAttended");
        $filters['v.is_non_voter'] = $request->get("isNonVoter");
        $filters['v.elect_id'] = $request->get('electId');

        $columns = array(
            0 => 'v.voter_id',
            1 => 'v.voter_name',
            2 => 'v.on_network',
            3 => 'v.voted_2017',
            6 => 'v.precinct_no',
        );

        $whereStmt = " AND (";

        foreach ($filters as $field => $searchText) {
            if ($searchText != "") {
                if ($field == 'v.voter_id' || $field == 'v.elect_id' || $field == 'v.voter_group'  || $field == 'v.is_non_voter'  || $field == 'v.province_code' || $field == 'v.municipality_no' || $field == 'v.brgy_no' || $field == 'v.has_attended' ) {
                    $whereStmt .= "{$field} = '{$searchText}' AND ";
                }elseif ($field == 'v.precinct_no') {
                    $temp = $searchText == "" ? null : "'{$searchText}'";
                    $whereStmt .= "({$field} = '{$searchText}' OR {$temp} IS NULL) AND ";
                } else {
                    $whereStmt .= "{$field} LIKE '%{$searchText}%' AND ";
                }
            }
        }

        $whereStmt = substr_replace($whereStmt, "", -4);

        if ($whereStmt == " A") {
            $whereStmt = "";
        } else {
            $whereStmt .= ")";
        }

        $orderStmt = "";

        if (null !== $request->query->get('order')) {
            $orderStmt = $this->genOrderStmt($request, $columns);
        }

        $start = 0;
        $length = 1;

        if (null !== $request->query->get('start') && null !== $request->query->get('length')) {
            $start = intval($request->query->get('start'));
            $length = intval($request->query->get('length'));
        }

        $em = $this->getDoctrine()->getManager("electPrep2024");
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $sql = "SELECT COALESCE(count(v.pro_voter_id),0) FROM tbl_project_voter v ";
        $stmt = $em->getConnection()->query($sql);
        $recordsTotal = $stmt->fetchColumn();

        $sql = "SELECT COALESCE(COUNT(v.pro_voter_id),0) FROM tbl_renewed_id ri , tbl_project_voter v
                WHERE 1 AND ri.pro_voter_id = v.pro_voter_id ";

        $sql .= $whereStmt . ' ' . $orderStmt;

        $stmt = $em->getConnection()->query($sql);
        $recordsFiltered = $stmt->fetchColumn();

        $sql = "SELECT v.* FROM tbl_renewed_id ri , tbl_project_voter v WHERE 1 AND ri.pro_voter_id = v.pro_voter_id  " . $whereStmt . ' ' . $orderStmt . " LIMIT {$length} OFFSET {$start} ";

        $stmt = $em->getConnection()->query($sql);
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $row['cellphone_no'] = $row['cellphone'];
            $data[] = $row;
        }

        $draw = (null !== $request->query->get('draw')) ? $request->query->get('draw') : 0;
        $res['data'] = $data;
        $res['recordsTotal'] = $recordsTotal;
        $res['recordsFiltered'] = $recordsFiltered;
        $res['draw'] = $draw;

        $em->clear();

        return new JsonResponse($res);
    }

    private function genOrderStmt($request, $columns)
    {

        $orderStmt = "ORDER BY  ";

        for ($i = 0; $i < intval(count($request->query->get('order'))); $i++) {
            if ($request->query->get('columns')[$request->query->get('order')[$i]['column']]['orderable']) {
                $orderStmt .= " " . $columns[$request->query->get('order')[$i]['column']] . " " .
                    ($request->query->get('order')[$i]['dir'] === 'asc' ? 'ASC' : 'DESC') . ", ";
            }
        }

        $orderStmt = substr_replace($orderStmt, "", -2);
        if ($orderStmt == "ORDER BY") {
            $orderStmt = "";
        }

        return $orderStmt;
    }

}
