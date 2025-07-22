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
        $electId = $request->get('electId');
        $proId = $request->get('proId');

        $sql = "SELECT pro_voter_id,pro_id,elect_id,pro_id_code,voter_name,cellphone,voter_group,has_photo,photo_at,has_id,is_transfered,
                has_new_photo, has_new_id, cropped_photo,old_voter_group, is_non_voter, generated_id_no,  date_generated ,has_claimed, claimed_at,
                municipality_no, brgy_no,precinct_no,municipality_name,barangay_name,updated_at,updated_by,old_cellphone,position,status,province_code
                FROM tbl_project_voter 
                 WHERE updated_at >= ? AND updated_at < ? 
                 AND (municipality_no = ? OR ? IS NULL) 
                 AND pro_id = ? AND elect_id = ? AND has_new_photo = 1 
                 AND (sync_date <> ? OR sync_date IS NULL)";

        $stmt = $emRemote->getConnection()->prepare($sql);
        $stmt->bindValue(1, $startDate);
        $stmt->bindValue(2, $endDate);
        $stmt->bindValue(3, $municipalityNo);
        $stmt->bindValue(4, empty($municipalityNo) ? null : $municipalityNo);
        $stmt->bindValue(5, $proId);
        $stmt->bindValue(6, $electId);
        $stmt->bindValue(7, date('Y-m-d'));

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
                    ->findOneBy([
                        'voterName' => $item['voter_name'],
                        'municipalityNo' => $item['municipality_no'],
                        'brgyNo' => $item['brgy_no'],
                        'electId' =>  $item['elect_id']
                    ]);

                $filename = "";
                $copyPhoto = false;

                if ($localProjectVoter != null) {
                    // local copy found
                    $echoText .= $processed . ' ' . $localProjectVoter->getVoterName() . " Local copy has found." . '<br/>';

                    // $echoText .= 'local updated : ' . $localProjectVoter->getUpdatedAt()->format('Y-m-d H:i:s') . '<br/>';
                    // $echoText .= 'remote updated : ' . $item['updated_at'] . '<br/>';

                    $localDate = ($localProjectVoter->getUpdatedAt() == null || $localProjectVoter->getUpdatedAt() == '') ? "2021-01-01" : $localProjectVoter->getUpdatedAt()->format('Y-m-d H:i:s');
                    $localTime = strtotime($localDate);
                    $remoteTime = strtotime($item['updated_at']);

                    $echoText .= 'local updated time : ' . $localTime . '<br/>';
                    $echoText .= 'remote updated item : ' . $remoteTime . '<br/>';

                    if ($localTime < $remoteTime) {

                        if($item['has_photo'] == 1 || $item['has_new_photo'] == 1){
                            if($localProjectVoter->getHasNewId() != 1 ){
                                $localProjectVoter->setProIdCode($item['pro_id_code']);
                                $localProjectVoter->setGeneratedIdNo($item['generated_id_no']);
                                $localProjectVoter->setDateGenerated($item['date_generated']);
                            }
                            
                            $localProjectVoter->setHasPhoto($item['has_photo']);
                        }

                        $localProjectVoter->setCellphone($item['cellphone']);
                        $localProjectVoter->setOldCellphone($item['old_cellphone']);
                        $localProjectVoter->setVoterGroup($item['voter_group']);
                        $localProjectVoter->setOldVoterGroup($item['old_voter_group']);
                        $localProjectVoter->setPosition($item['position']);
                        $localProjectVoter->setIsNonVoter($item['is_non_voter']);

                        // if($item['is_transfered'] == 1 ){
                        //     $localProjectVoter->setIsTransfered($item['is_transfered']);
                        // }

                        if($item['has_new_photo'] == 1){
                            $copyPhoto = true;
                            $localProjectVoter->setHasNewPhoto($item['has_new_photo']);
                            if(!empty($item['photo_at']) && $item['photo_at'] != ''){
                                //$localProjectVoter->setPhotoAt(new \DateTime($item['photo_at']));
                            }
                        }

                        if($item['cropped_photo'] == 1){
                            $localProjectVoter->setCroppedPhoto($item['cropped_photo']);
                        }

                        $localProjectVoter->setUpdatedAt(new \DateTime($item['updated_at']));
                        $localProjectVoter->setUpdatedBy($item['updated_by']);
                        $localProjectVoter->setStatus($item['status']);

                        $hasId = 0;
                        $hasNewId = 0;
                        $hasClaimed = 0;

                        if ($localProjectVoter->getHasId() == 1 || $item['has_id'] == 1) {
                            $hasId = 1;
                            $localProjectVoter->setHasId($hasId);
                        }

                        if ($localProjectVoter->getHasNewId() == 1 || $item['has_new_id'] == 1) {
                            $hasNewId = 1;
                            $localProjectVoter->setHasNewId($hasNewId);
                        }

                        if ($localProjectVoter->getHasClaimed() == 1 || $item['has_claimed'] == 1) {
                            $hasClaimed = 1;
                            $localProjectVoter->setHasClaimed($hasClaimed);

                            if($item['has_claimed'] == 1){
                                $localProjectVoter->setClaimedAt($item['claimed_at']);
                            }
                        }

                        $localProjectVoter->setSyncDate(date('Y-m-d'));


                        $echoText .= "local copy has been updated. <br/>";

                        $filename = $localProjectVoter->getProId() . '_' . $localProjectVoter->getGeneratedIdNo();
                       
                        $totalUpdated++;

                        $em->flush();

                    } else {
                        // skip update
                        $echoText .= "local copy overwrite has been skipped.<br/>";
                        $totalSkipped++;
                    }
                } else {
                    // insert non-voter
                    // check if non-voter 
                    // check if has_photo
                    // check if has_generated_id_no

                    $echoText .= $processed . ' ' . $item['voter_name'] . " Inserting non voter.<br/>";

                    if (!empty($item['pro_id_code']) && !empty($item['generated_id_no'])) {

                        $newNonVoter = new ProjectVoter();

                        $newNonVoter->setProIdCode($item['pro_id_code']);
                        $newNonVoter->setProId($item['pro_id']);
                        $newNonVoter->setElectId($item['elect_id']);
                        $newNonVoter->setProvinceCode($item['province_code']);

                        $newNonVoter->setGeneratedIdNo($item['generated_id_no']);
                        $newNonVoter->setDateGenerated($item['date_generated']);

                        $newNonVoter->setVoterName($item['voter_name']);
                        $newNonVoter->setMunicipalityName($item['municipality_name']);
                        $newNonVoter->setMunicipalityNo($item['municipality_no']);

                        $newNonVoter->setBarangayName($item['barangay_name']);
                        $newNonVoter->setBrgyNo($item['brgy_no']);

                        $newNonVoter->setPrecinctNo($item['precinct_no']);

                        $newNonVoter->setCellphone($item['cellphone']);
                        $newNonVoter->setOldCellphone($item['old_cellphone']);

                        $newNonVoter->setVoterGroup($item['voter_group']);
                        $newNonVoter->setOldVoterGroup($item['old_voter_group']);

                        $newNonVoter->setPosition($item['position']);

                        $newNonVoter->setHasPhoto($item['has_photo']);
                        $newNonVoter->setHasNewPhoto($item['has_new_photo']);

                        $newNonVoter->setHasId($item['has_id']);
                        $newNonVoter->setHasNewId($item['has_new_id']);

                        if($item['has_new_photo'] == 1){
                            $copyPhoto = true;
                            $newNonVoter->setPhotoAt(new \DateTime($item['photo_at']));
                        }

                        if ($item['has_claimed'] == 1) {
                            $hasClaimed = 1;
                            $newNonVoter->setHasClaimed($hasClaimed);

                            if($item['has_claimed'] == 1){
                                $newNonVoter->setClaimedAt($item['claimed_at']);
                            }
                        }

                        $newNonVoter->setCroppedPhoto($item['cropped_photo']);
                        //$newNonVoter->setIsTransfered($item['is_transfered']);
                        $newNonVoter->setIsNonVoter($item['is_non_voter']);

                        $newNonVoter->setUpdatedAt(new \DateTime($item['updated_at']));
                        $newNonVoter->setCreatedAt(new \DateTime($item['updated_at']));
                        $newNonVoter->setCreatedBy($item['updated_by']);
                        $newNonVoter->setUpdatedBy($item['updated_by']);
                        $newNonVoter->setStatus($item['status']);
                        $newNonVoter->setSyncDate(date('Y-m-d'));

                        $em->persist($newNonVoter);
                        $em->flush();

                        $validator = $this->get('validator');
                        $violations = $validator->validate($newNonVoter);

                        $errors = [];

                        if (count($violations) > 0) {
                            foreach ($violations as $violation) {
                                $errors[$violation->getPropertyPath()] = $violation->getMessage();
                                $echoText .= $violation->getPropertyPath() . ' : ' . $violation->getMessage() . "<br/>";
                            }
                        }

                        $filename = $newNonVoter->getProId() . '_' . $newNonVoter->getGeneratedIdNo();
                        

                        $echoText .= "Non voter has been inserted.<br/>";
                        $totalUpdated++;
                    } else {
                        // skip insert
                        $echoText .= "Insert cancelled. non qualified non voter.<br/>";
                        $totalSkipped++;
                    }
                }

                $em->flush();

                if ($copyPhoto) {
                    $rootDir = __DIR__ . '/../../../web/uploads/images/';
                    $imagePath = $rootDir . $filename . '.jpg';
                    $remoteImgSrcUrl = "http://" . $remoteIP . "/voter/photo/";

                    file_put_contents($imagePath, fopen($remoteImgSrcUrl . $filename, 'r'));
                }

                $currPercentage = (int) (($processed / $total) * 100);

                if ($currPercentage != $prevPercentage) {
                    $prevPercentage = $currPercentage;

                    echo $currPercentage;
                }

                // if ($localProjectVoter) {
                //     //echo print_r($item['voter_name']);
                //     echo 'Voter Found : ' . $localProjectVoter->getVoterName() . ' : ' . $item['municipality_name'] . ', ' . $item['barangay_name'] . '<br/>';
                // } else {
                //     echo 'Voter Not found';
                // }

                // echo $echoText;

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

        $sql = "SELECT municipality_name, COUNT(*) as total_items FROM tbl_project_voter 
                 WHERE updated_at >= ? AND updated_at < ? 
                 AND (municipality_no = ? OR ? IS NULL) 
                 AND pro_id = ? AND elect_id = ? 
                 AND (sync_date <> ? or sync_date IS NULL) 
                 AND has_new_photo = 1 
                 GROUP BY municipality_name
                 ORDER BY municipality_name";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, $startDate);
        $stmt->bindValue(2, $endDate);
        $stmt->bindValue(3, $municipalityNo);
        $stmt->bindValue(4, empty($municipalityNo) ? null : $municipalityNo);
        $stmt->bindValue(5, $proId);
        $stmt->bindValue(6, $electId);
        $stmt->bindValue(7, date('Y-m-d'));

        $stmt->execute();
        $data = array();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $em->clear();

        return new JsonResponse($data);
    }
}
