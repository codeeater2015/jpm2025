<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
* @Route("/monitoring")
*/

class MonitoringController extends Controller 
{
    const STATUS_ACTIVE = 'A';
    const MODULE_MAIN = "UPDATE_MANAGER";

    /**
     * @Route("", name="monitoring_index", options={"main" = true})
     */

    public function indexAction(Request $request)
    {
        // $this->denyAccessUnlessGranted("entrance",self::MODULE_MAIN);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $hostIp = $this->getParameter('host_ip');

        return $this->render('template/monitoring/index.html.twig', ['user' => $user, 'hostIp' => $hostIp]);
    }

    /**
     * @Route("/reactivation", name="reactivation_monitoring_index", options={"main" = true})
     */

     public function reactivationMonitoringAction(Request $request)
     {
         // $this->denyAccessUnlessGranted("entrance",self::MODULE_MAIN);
         $user = $this->get('security.token_storage')->getToken()->getUser();
         $hostIp = $this->getParameter('host_ip');
 
         return $this->render('template/monitoring/reactivation.html.twig', ['user' => $user, 'hostIp' => $hostIp]);
     }

    /**
     * @Route("/ajax_get_to_send_voter/{proId}/{electId}",
     *   name="ajax_get_to_send_voter",
     *   options={"expose" = true}
     * )
     * @Method("GET")
     */

    public function ajaxGetToSendVoter(Request $request, $proId, $electId)
    {
        $em = $this->getDoctrine()->getManager();

        $sql = "SELECT pv.barangay_name,pv.voter_name,pv.pro_id_code,pv.voter_id,pv.pro_voter_id,pv.updated_at
                FROM tbl_project_voter pv WHERE pv.to_send = 1 AND pv.pro_id = ? AND  pv.elect_id =  ? 
                ORDER BY pv.voter_name ASC LIMIT 500";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, $proId);
        $stmt->bindValue(2, $electId);
        $stmt->execute();
        $data = array();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $em->clear();

        return new JsonResponse($data);
    }

    /**
     * @Route("/ajax_post_to_send_records",
     *   name="ajax_post_to_send_records",
     *   options={"expose" = true}
     * )
     * @Method("POST")
     */

    public function ajaxPostToSendRecords(Request $request)
    {

        $self = $this;
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $emRemote = $this->getDoctrine()->getManager("remote");

        $remoteIP = $this->getParameter('remote_ip_address');
        $remoteDatasource = $this->getParameter("remote_datasource");

        $projectVoters = $request->get('projectVoters');

        if (count($projectVoters) <= 0) {
            return new JsonResponse(['projectVoters' => "Action denied. You cannot proceed on importing  data with an empty list."], 400);
        }

        $response = new StreamedResponse();
        $response->headers->set("Cache-Control", "no-cache, must-revalidate");
        $response->headers->set("X-Accel-Buffering", "no");
        $response->setStatusCode(200);

        $response->setCallback(function () use ($self, $em, $emRemote, $user, $projectVoters, $remoteIP, $remoteDatasource) {

            $processed = 0;
            $imported = 0;
            $total = count($projectVoters);
            $currPercentage = 0;
            $prevPercentage = 0;

            foreach ($projectVoters as $proVoterId) {
                $processed++;

                $localProjectVoter = $em->getRepository("AppBundle:ProjectVoter")
                    ->find($proVoterId);
                    
                $projectVoter = $emRemote->getRepository("AppBundle:ProjectVoter")
                    ->find($proVoterId);

                if ($projectVoter && $localProjectVoter) {
                    $projectVoter->setProIdCode($localProjectVoter->getProIdCode());
                    $projectVoter->setGeneratedIdNo($localProjectVoter->getGeneratedIdNo());
                    $projectVoter->setDateGenerated($localProjectVoter->getDateGenerated());
                    $projectVoter->setCellphone($localProjectVoter->getCellphone());
                    $projectVoter->setVoterGroup($localProjectVoter->getVoterGroup());
                    $projectVoter->setPosition($localProjectVoter->getPosition());
                    $projectVoter->setHasPhoto($localProjectVoter->getHasPhoto());
                    $projectVoter->setPhotoAt($localProjectVoter->getPhotoAt());
                    $projectVoter->setHasId($localProjectVoter->getHasId());
                    $projectVoter->setToSend(0);
                    $projectVoter->setIsNonVoter($localProjectVoter->getIsNonVoter());
                    $projectVoter->setUpdatedAt($localProjectVoter->getUpdatedAt());
                    $projectVoter->setUpdatedBy($localProjectVoter->getUpdatedBy());
                    $projectVoter->setStatus($localProjectVoter->getStatus());
                    $imported++;
                }else {
                }

                $localProjectVoter->setToSend(0);

                $em->flush();
                $emRemote->flush();

                $currPercentage = (int) (($processed / $total) * 100);

                if ($currPercentage != $prevPercentage) {
                    $prevPercentage = $currPercentage;

                    echo $currPercentage;
                }

                ob_flush();
                flush();
            }

            // $header->setTotalProcessed($processed);
            // $header->setTotalImported($imported);

            $em->flush();

            $em->clear();
            $emRemote->clear();
        });

        return $response;
    }

     /**
     * @Route("/datatable",
     *     name="ajax_datatable_to_send_voter",
     *     options={"expose" = true}
     *     )
     * @Method("GET")
     */

    public function datatableToSendVoterAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $filters = array();
        $filters['v.province_code'] = $request->get("provinceCode");
        $filters['v.municipality_no'] = $request->get("municipalityNo");
        $filters['v.brgy_no'] = $request->get("brgyNo");
        $filters['v.precinct_no'] = $request->get("precinctNo");

        $filters['v.voter_name'] = $request->get("voterName");
        $filters['v.birthdate'] = $request->get("birthdate");
        $filters['v.cellphone'] = $request->get("cellphone");
        $filters['v.voter_group'] = $request->get("voterGroup");
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
                if ($field == 'v.voter_id' || $field == 'v.elect_id' || $field == 'v.voter_group'|| $field == 'v.rec_form_sub' || $field == 'v.house_form_sub' || $field == 'v.is_non_voter' ) {
                    $whereStmt .= "{$field} = '{$searchText}' AND ";
                }if ($field == 'v.municipality_no' || $field == 'v.brgy_no' || $field == 'v.precinct_no' || $field == 'v.province_code' || $field == 'v.brgy_cluster') {
                    $temp = $searchText == "" ? null : "'{$searchText}  '";
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

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $sql = "SELECT COALESCE(count(v.pro_voter_id),0) FROM tbl_project_voter v WHERE v.to_send = 1";
        $stmt = $em->getConnection()->query($sql);
        $recordsTotal = $stmt->fetchColumn();

        $sql = "SELECT COALESCE(COUNT(v.pro_voter_id),0) FROM tbl_project_voter v
                WHERE v.to_send = 1 ";

        $sql .= $whereStmt . ' ' . $orderStmt;

        $stmt = $em->getConnection()->query($sql);
        $recordsFiltered = $stmt->fetchColumn();

        $sql = "SELECT v.* FROM tbl_project_voter v WHERE v.to_send = 1 " . $whereStmt . ' ' . $orderStmt . " LIMIT {$length} OFFSET {$start} ";

        $stmt = $em->getConnection()->query($sql);
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

    private function genOrderStmt($request,$columns){

        $orderStmt = "ORDER BY  ";

        for ( $i=0 ; $i<intval(count($request->query->get('order'))); $i++ )
        {
            if ( $request->query->get('columns')[$request->query->get('order')[$i]['column']]['orderable'] )
            {
                $orderStmt .= " ".$columns[$request->query->get('order')[$i]['column']]." ".
                    ($request->query->get('order')[$i]['dir']==='asc' ? 'ASC' : 'DESC') .", ";
            }
        }

        $orderStmt = substr_replace( $orderStmt, "", -2 );
        if ( $orderStmt == "ORDER BY" )
        {
            $orderStmt = "";
        }

        return $orderStmt;
    }
    

    /**
     * @Route("/ajax_monitoring_reactivation_summary_by_municipality",
     *       name="ajax_monitoring_reactivation_summary_by_municipality",
     *       options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxMonitoringReactivationSummaryByMunicipality(Request $request)
     {
         $em = $this->getDoctrine()->getManager();
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';
 
         $sql = "SELECT pv.municipality_name,
                (SELECT COUNT(DISTINCT ppv.precinct_no) From tbl_project_voter ppv WHERE ppv.municipality_no = pv.municipality_no AND ppv.precinct_no IS NOT NULL and ppv.precinct_no <> '' ) as total_precincts,
                (SELECT COUNT(ppv.pro_voter_id) FROM tbl_project_voter ppv WHERE ppv.municipality_no = pv.municipality_no AND ppv.precinct_no IS NOT NULL AND ppv.precinct_no <> '' ) AS total_registered_voter,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LGC' AND pv.has_photo = 1  THEN 1 END),0) AS prev_lgc,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LOPP' AND pv.has_photo = 1 THEN 1 END),0) AS prev_lopp,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LPPP' AND pv.has_photo = 1 THEN 1 END),0) AS prev_lppp,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LPPP1' AND pv.has_photo = 1 THEN 1 END),0) AS prev_lppp1,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LPPP2' AND pv.has_photo = 1 THEN 1 END),0) AS prev_lppp2,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LPPP3' AND pv.has_photo = 1 THEN 1 END),0) AS prev_lppp3,
		        COALESCE(COUNT( CASE WHEN pv.old_voter_group IN ('LGC','LOPP','LPPP','LPPP1','LPPP2','LPPP3') AND pv.has_photo = 1 THEN 1 END),0) AS total_prev_member,
		
		
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LGC' AND pv.has_new_photo = 1 THEN 1 END),0) AS renewed_lgc,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LOPP' AND pv.has_new_photo = 1 THEN 1 END),0) AS renewed_lopp,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LPPP' AND pv.has_new_photo = 1 THEN 1 END),0) AS renewed_lppp,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LPPP1' AND pv.has_new_photo = 1 THEN 1 END),0) AS renewed_lppp1,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LPPP2' AND pv.has_new_photo = 1 THEN 1 END),0) AS renewed_lppp2,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group = 'LPPP3' AND pv.has_new_photo = 1 THEN 1 END),0) AS renewed_lppp3,
                COALESCE(COUNT( CASE WHEN pv.old_voter_group IN ('LGC','LOPP','LPPP','LPPP1','LPPP2','LPPP3') AND pv.has_new_photo = 1 THEN 1 END),0) AS total_renew_member,
		

                COALESCE(COUNT( CASE WHEN pv.voter_group = 'LGC' AND (pv.old_voter_group = '' OR pv.old_voter_group IS NULL) AND pv.has_new_photo = 1 THEN 1 END),0) AS new_lgc,
                COALESCE(COUNT( CASE WHEN pv.voter_group = 'LOPP' AND (pv.old_voter_group = '' OR pv.old_voter_group IS NULL) AND pv.has_new_photo = 1 THEN 1 END),0) AS new_lopp,
                COALESCE(COUNT( CASE WHEN pv.voter_group = 'LPPP' AND (pv.old_voter_group = '' OR pv.old_voter_group IS NULL) AND pv.has_new_photo = 1 THEN 1 END),0) AS new_lppp,
                COALESCE(COUNT( CASE WHEN pv.voter_group = 'LPPP1' AND (pv.old_voter_group = '' OR pv.old_voter_group IS NULL) AND pv.has_new_photo = 1 THEN 1 END),0) AS new_lppp1,
                COALESCE(COUNT( CASE WHEN pv.voter_group = 'LPPP2' AND (pv.old_voter_group = '' OR pv.old_voter_group IS NULL) AND pv.has_new_photo = 1 THEN 1 END),0) AS new_lppp2,
                COALESCE(COUNT( CASE WHEN (pv.voter_group = 'LPPP3' OR pv.voter_group is null or pv.voter_group = '') AND (pv.old_voter_group = '' OR pv.old_voter_group IS NULL) AND pv.has_new_photo = 1 THEN 1 END),0) AS new_lppp3,
		        COALESCE(COUNT( CASE WHEN  (pv.old_voter_group = '' OR pv.old_voter_group IS NULL OR pv.old_voter_group = 'null') AND pv.has_new_photo = 1 THEN 1 END),0) AS total_new_member,
                COALESCE(COUNT( CASE WHEN  (pv.old_voter_group = '' OR pv.old_voter_group IS NULL OR pv.old_voter_group = 'null') AND pv.has_new_photo = 1 AND pv.is_non_voter = 0 THEN 1 END),0) AS total_new_voter_member,
                COALESCE(COUNT( CASE WHEN  (pv.old_voter_group = '' OR pv.old_voter_group IS NULL OR pv.old_voter_group = 'null') AND pv.has_new_photo = 1 AND pv.is_non_voter = 1 THEN 1 END),0) AS total_new_nonvoter_member

                from tbl_project_voter pv where pv.elect_id = 423 and pv.pro_id = 3 
                
                AND pv.municipality_no NOT IN ('01', '16')

                group BY pv.municipality_name";

         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $searchText);
         $stmt->execute();
 
         $data = $stmt->fetchAll();
 
         if (count($data) <= 0) {
             return new JsonResponse(array());
         }
 
         $em->clear();
 
         return new JsonResponse($data);
     }
}
