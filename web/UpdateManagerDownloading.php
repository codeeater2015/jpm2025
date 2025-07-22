<?php
namespace AppBundle\Controller;

use AppBundle\Entity\DataUpdateDetail;
use AppBundle\Entity\DataUpdateHeader;
use AppBundle\Entity\ProjectVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Route("/manage-updates")
 */

class UpdateManagerController extends Controller
{
    const STATUS_ACTIVE = 'A';
    const MODULE_MAIN = "UPDATE_MANAGER";

    /**
     * @Route("", name="update_manager_index", options={"main" = true})
     */

    public function indexAction(Request $request)
    {
        // $this->denyAccessUnlessGranted("entrance",self::MODULE_MAIN);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $hostIp = $this->getParameter('host_ip');

        return $this->render('template/update-manager/index.html.twig', ['user' => $user, 'hostIp' => $hostIp]);
    }

    /**
     * @Route("/ajax_get_did_change_voter/{proId}/{electId}",
     *   name="ajax_get_did_change_voter",
     *   options={"expose" = true}
     * )
     * @Method("GET")
     */

    public function ajaxGetDidChangeVoter(Request $request, $proId, $electId)
    {
        $em = $this->getDoctrine()->getManager();

        $sql = "SELECT pv.barangay_name,pv.voter_name,pv.pro_id_code,pv.voter_id,pv.pro_voter_id,pv.updated_at
                FROM tbl_project_voter pv WHERE pv.did_changed = 1 AND pv.pro_id = ? AND  pv.elect_id =  ? 
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
     * @Route("/ajax_post_updated_records",
     *   name="ajax_post_updated_records",
     *   options={"expose" = true}
     * )
     * @Method("POST")
     */

    public function ajaxPostUpdatedRecords(Request $request)
    {

        $self = $this;
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $emRemote = $this->getDoctrine()->getManager("remote");

        $remoteIP = $this->getParameter('remote_ip_address');
        $remoteDatasource = $this->getParameter("remote_datasource");

        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $municipalityNo = $request->get('municipalityNo');
        $brgyNo = $request->get('brgyNo');
        $electId = $request->get('electId');
        $proId = $request->get('proId');

        $sql = "SELECT pro_voter_id, pandesal_wave1, pandesal_wave1_date, pandesal_wave1_desc, updated_at,voter_name,status,is_jtr_member,is_jtr_leader,has_new_photo,has_new_id,has_photo,has_id,cropped_photo,updated_by
                FROM tbl_project_voter 
                 WHERE updated_at >= ? AND updated_at < ? 
                 AND (municipality_no = ? OR ? IS NULL) 
                 AND (brgy_no = ? OR ? IS NULL) 
                 AND pro_id = ? AND elect_id = ? AND pandesal_wave1 = 1 
                 AND (sync_date <> ? OR sync_date IS NULL) ";

        $stmt = $emRemote->getConnection()->prepare($sql);
        $stmt->bindValue(1, $startDate);
        $stmt->bindValue(2, $endDate);
        $stmt->bindValue(3, $municipalityNo);
        $stmt->bindValue(4, empty($municipalityNo) ? null : $municipalityNo);
        $stmt->bindValue(5, $brgyNo);
        $stmt->bindValue(6, empty($brgyNo) ? null : $brgyNo);
        $stmt->bindValue(7, $proId);
        $stmt->bindValue(8, $electId);
        $stmt->bindValue(9, date('Y-m-d'));

        $stmt->execute();
        $data = array();

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        if (count($data) <= 0) {
            return new JsonResponse(['projectVoters' => "Action denied. You cannot proceed on importing  data with an empty list."], 400);
        }

        // /return new JsonResponse($data);

        $response = new StreamedResponse();
        $response->headers->set("Cache-Control", "no-cache, must-revalidate");
        $response->headers->set("X-Accel-Buffering", "no");
        $response->setStatusCode(200);

        $response->setCallback(function () use ($self, $em, $emRemote, $user, $data, $remoteIP, $remoteDatasource,$startDate,$endDate) {

            $echoText = "";

            $header = new DataUpdateHeader();
            $header->setDataSource($remoteDatasource);
            $header->setStartDate($startDate);
            $header->setEndDate($endDate);
            $header->setTotalCount(count($data));
            $header->setCreatedAt(new \DateTime());
            $header->setCreatedBy($user->getUsername());
            $header->setStatus("A");

            $em->persist($header);
            $em->flush();

            $processed = 0;
            $totalUpdated = 0;
            $totalSkipped = 0;
            $imported = 0;
            $total = count($data);
            $currPercentage = 0;
            $prevPercentage = 0;

            foreach ($data as $item) {
                $processed++;

                $localProjectVoter = $em->getRepository("AppBundle:ProjectVoter")
                                         ->find($item['pro_voter_id']);

                $filename = "";

                if ($localProjectVoter != null) {

                        if($item['pandesal_wave1'] == 1 ){
                            $localProjectVoter->setPandesalWave1($item['pandesal_wave1']);
                            $localProjectVoter->setPandesalWave1Date($item['pandesal_wave1_date']);
                            $localProjectVoter->setPandesalWave1Desc($item['pandesal_wave1_desc']);
                        }
                        
                        if($item['has_photo'] == 1 || $item['has_new_photo'] == 1){
                            $localProjectVoter->setHasNewPhoto($item['has_new_photo']);
                        }

                        if($item['has_id'] == 1 || $item['has_new_id'] == 1){
                            $localProjectVoter->setHasNewId($item['has_new_id']);
                        }
                        
                        if($item['cropped_photo'] == 1){
                            $localProjectVoter->setCroppedPhoto($item['cropped_photo']);
                        }

                        $localProjectVoter->setStatus($item['status']);
                        $localProjectVoter->setIsJtrMember($item['is_jtr_member']);
                        $localProjectVoter->setIsJtrLeader($item['is_jtr_leader']);

                        $localProjectVoter->setUpdatedAt(new \DateTime($item['updated_at']));
                        $localProjectVoter->setUpdatedBy($item['updated_by']);
                        $totalUpdated++;
                        $em->flush();

                } else {
                    $totalSkipped++;
                }

                $em->flush();

                $currPercentage = (int) (($processed / $total) * 100);

                if ($currPercentage != $prevPercentage) {
                    $prevPercentage = $currPercentage;

                    echo $currPercentage;
                }

                ob_flush();
                flush();
            }

            $header->setTotalProcessed($processed);
            $header->setTotalImported($imported);
            $header->setTotalUpdated($totalUpdated);
            $header->setTotalSkipped($totalSkipped);

            $em->flush();
            $em->clear();

            $emRemote->clear();
        });

        return $response;
    }

    /**
     * @Route("/ajax_get_data_import_datatable",
     *     name="ajax_get_data_import_datatable",
     *     options={"expose" = true})
     *
     * @Method("GET")
     */

    public function dataImportDatatableAction(Request $request)
    {

        $filters = [];
        $filters['h.created_at'] = $request->get("createdAt");

        $columns = [
            0 => 'h.pro_id',
            1 => 'h.data_source',
            2 => 'h.total_count',
            3 => 'h.total_processed',
            4 => 'h.total_imported',
            5 => 'h.created_at',
            6 => 'h.created_by',
        ];

        $whereStmt = " AND (";

        foreach ($filters as $field => $searchText) {
            if ($searchText != "") {
                $whereStmt .= "{$field} LIKE '%$searchText%' AND ";
            }
        }

        $whereStmt = substr_replace($whereStmt, "", -4);

        if ($whereStmt == " A") {
            $whereStmt = "";
        } else {
            $whereStmt .= ")";
        }

        $orderStmt = " ORDER BY h.hdr_id DESC";

        $start = 1;
        $length = 1;

        if (null !== $request->query->get('start') && null !== $request->query->get('length')) {
            $start = intval($request->query->get('start'));
            $length = intval($request->query->get('length'));
        }

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $sql = "SELECT COALESCE(count(h.hdr_id),0) FROM tbl_data_update_header h";
        $stmt = $em->getConnection()->query($sql);
        $recordsTotal = $stmt->fetchColumn();

        $sql = "SELECT COALESCE(COUNT(h.hdr_id),0) FROM tbl_data_update_header h WHERE 1=1 ";

        $sql .= $whereStmt . ' ' . $orderStmt;
        $stmt = $em->getConnection()->query($sql);
        $recordsFiltered = $stmt->fetchColumn();

        $sql = "SELECT h.* FROM tbl_data_update_header h WHERE 1=1 " . $whereStmt . ' ' . $orderStmt . " LIMIT {$length} OFFSET {$start} ";

        $stmt = $em->getConnection()->query($sql);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $draw = (null !== $request->query->get('draw')) ? $request->query->get('draw') : 0;
        $res['data'] = $data;
        $res['recordsTotal'] = $recordsTotal;
        $res['recordsFiltered'] = $recordsFiltered;
        $res['draw'] = $draw;

        $em->clear();

        return new JsonResponse($res);
    }

    /**
     * @Route("/ajax_get_data_import_detail_datatable",
     *     name="ajax_get_data_import_detail_datatable",
     *     options={"expose" = true})
     *
     * @Method("GET")
     */

    public function dataImportDetailDatatableAction(Request $request)
    {

        $filters = [];

        $filters['d.hdr_id'] = $request->get("hdrId");
        $filters['d.voter_name'] = $request->get("voterName");
        $filters['d.status'] = $request->get("status");
        $filters['d.has_id'] = $request->get("hasId");
        $filters['d.has_photo'] = $request->get("hasPhoto");

        $columns = [
            1 => 'd.voter_name',
            2 => 'd.voter_group',
            3 => 'd.has_photo',
            4 => 'd.has_id',
            5 => 'd.cellphone',
            6 => 'd.updated_at',
            7 => 'd.updated_by',
            8 => 'd.status',
        ];

        $whereStmt = " AND (";

        foreach ($filters as $field => $searchText) {
            if ($searchText != "") {
                if ($field == 'd.hdr_id' || $field == 'd.status') {
                    $whereStmt .= "{$field} = '$searchText' AND ";
                } else {
                    $whereStmt .= "{$field} LIKE '%$searchText%' AND ";
                }
            }
        }

        $whereStmt = substr_replace($whereStmt, "", -4);

        if ($whereStmt == " A") {
            $whereStmt = "";
        } else {
            $whereStmt .= ")";
        }

        $orderStmt = " ORDER BY d.voter_name";

        $start = 1;
        $length = 1;

        if (null !== $request->query->get('start') && null !== $request->query->get('length')) {
            $start = intval($request->query->get('start'));
            $length = intval($request->query->get('length'));
        }

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $sql = "SELECT COALESCE(count(d.dtl_id),0) FROM tbl_data_update_detail d";
        $stmt = $em->getConnection()->query($sql);
        $recordsTotal = $stmt->fetchColumn();

        $sql = "SELECT COALESCE(COUNT(d.dtl_id),0) FROM tbl_data_update_detail d WHERE 1=1 ";

        $sql .= $whereStmt . ' ' . $orderStmt;
        $stmt = $em->getConnection()->query($sql);
        $recordsFiltered = $stmt->fetchColumn();

        $sql = "SELECT d.* FROM tbl_data_update_detail d WHERE 1=1 " . $whereStmt . ' ' . $orderStmt . " LIMIT {$length} OFFSET {$start} ";

        $stmt = $em->getConnection()->query($sql);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $draw = (null !== $request->query->get('draw')) ? $request->query->get('draw') : 0;
        $res['data'] = $data;
        $res['recordsTotal'] = $recordsTotal;
        $res['recordsFiltered'] = $recordsFiltered;
        $res['draw'] = $draw;

        $em->clear();

        return new JsonResponse($res);
    }

    /**
     * @Route("/ajax_get_available_data/{proId}/{electId}",
     *   name="ajax_get_available_data",
     *   options={"expose" = true}
     * )
     * @Method("GET")
     */

    public function ajaxGetAvailableData(Request $request, $proId, $electId)
    {
        $em = $this->getDoctrine()->getManager('remote');
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $municipalityNo = $request->get('municipalityNo');
        $brgyNo = $request->get('brgyNo');

        $sql = "SELECT municipality_name, barangay_name, COUNT(*) as total_items FROM tbl_project_voter 
                 WHERE updated_at >= ? AND updated_at < ? 
                 AND (municipality_no = ? OR ? IS NULL) 
                 AND (brgy_no = ? OR ? IS NULL) 
                 AND pro_id = ? AND elect_id = ? 
                 AND (sync_date <> ? or sync_date IS NULL) 
                 AND pandesal_wave1 = 1 
                 GROUP BY municipality_name, barangay_name
                 ORDER BY municipality_name, barangay_name";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, $startDate);
        $stmt->bindValue(2, $endDate);
        $stmt->bindValue(3, $municipalityNo);
        $stmt->bindValue(4, empty($municipalityNo) ? null : $municipalityNo);
        $stmt->bindValue(5, $brgyNo);
        $stmt->bindValue(6, empty($brgyNo) ? null : $brgyNo);
        $stmt->bindValue(7, $proId);
        $stmt->bindValue(8, $electId);
        $stmt->bindValue(9, date('Y-m-d'));

        $stmt->execute();
        $data = array();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $em->clear();

        return new JsonResponse($data);
    }
}
