<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use AppBundle\Entity\AssistanceHeader;
use AppBundle\Entity\AssistanceDetail;
use AppBundle\Entity\AssistanceProfile;

/**
 * @Route("/jpm-assistance")
 */

class JpmAssistanceController extends Controller
{
    /**
     * @Route("", name="jpm_assistance_index", options={"main" = true })
     */

    public function jpmAssistanceAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $hostIp = $this->getParameter('host_ip');
        $imgUrl = $this->getParameter('img_url');
        $reportUrl = $this->getParameter('report_url');

        return $this->render(
            'template/jpm-assistance/index.html.twig',
            [
                'user' => $user,
                'hostIp' => $hostIp,
                'imgUrl' => $imgUrl,
                'reportUrl' => $reportUrl
            ]
        );
    }

    /**
     * @Route("/ajax_post_jpm_assistance", 
     * 	name="ajax_post_jpm_assistance",
     *	options={"expose" = true}
     * )
     * @Method("POST")
     */

     public function ajaxPostJpmAssistanceAction(Request $request)
     {
         $user = $this->get('security.token_storage')->getToken()->getUser();
 
         $em = $this->getDoctrine()->getManager();
 
         $entity = new AssistanceHeader();


         $client = null;
         $dependent = null;

         if(!empty($request->get('clientProfileId'))){
            $client = $em->getRepository("AppBundle:AssistanceProfile")->find((int)$request->get('clientProfileId'));
         }
        
         if(!empty($request->get('dependentProfileId'))){
             $dependent = $em->getRepository("AppBundle:AssistanceProfile")->find((int)$request->get('dependentProfileId'));
         }

         if($client){
            $contactNo = !empty($request->get('contactNo')) ? $request->get('contactNo') : $client->getContactNo();
            $entity->setClientName($client->getFullname());
            $entity->setClientVoterName($client->getVoterName());
            $entity->setClientGeneratedIdNo($client->getGeneratedIdNo());
            $entity->setContactNo($contactNo);
            $entity->setMunicipalityNo($client->getMunicipalityNo());
            $entity->setMunicipalityName($client->getMunicipalityName());
            $entity->setBarangayNo($client->getBarangayNo());
            $entity->setBarangayName($client->getBarangayName());
            $entity->setDistrict($client->getDistrict());
            $entity->setPurok($client->getPurok());
         }else{
            return new JsonResponse(['clientProfileId' => 'Client  name cannot be empty.'], 400);
         }

         if($dependent){
            $entity->setDependentName($dependent->getFullname());
            $entity->setDependentVoterName($dependent->getVoterName());
            $entity->setDependentGeneratedIdNo($dependent->getGeneratedIdNo());
            $entity->setDependentAddress($dependent->getPurok() . ', ' . $dependent->getBarangayName() . ',' . $dependent->getMunicipalityName());
         }else{
            return new JsonResponse(['dependentProfileId' => 'Dependent not found.'], 400);
         }

         $entity->setClientProfileId($request->get('clientProfileId'));
         $entity->setDependentProfileId($request->get('dependentProfileId'));
         $entity->setDependentDiagnosis($request->get('dependentDiagnosis'));

         $entity->setHospital($request->get('hospital'));
         $entity->setFinalBill($request->get('finalBill'));
         $entity->setAmount($request->get('amount'));
         $entity->setTransType($request->get('transType'));
         $entity->setTransDate($request->get('transDate'));
         $entity->setControlNo($request->get('controlNo'));

         $entity->setCreatedAt(new \DateTime());
         $entity->setCreatedBy($user->getUsername());
         $entity->setUpdatedAt(new \DateTime());
         $entity->setUpdatedBy($user->getUsername());
         $entity->setRemarks($request->get('remarks'));
         $entity->setStatus("A");

 
         $validator = $this->get('validator');
         $violations = $validator->validate($entity,null, ['medicalCreate']);
 
         $errors = [];
 
         if (count($violations) > 0) {
             foreach ($violations as $violation) {
                 $errors[$violation->getPropertyPath()] = $violation->getMessage();
             }
             return new JsonResponse($errors, 400);
         }
 
         $em->persist($entity);
         $em->flush();
 
         $em->clear();
         $serializer = $this->get('serializer');
 
         return new JsonResponse($serializer->normalize($entity));
     }

     /**
     * @Route("/ajax_patch_jpm_assistance/{id}", 
     * 	name="ajax_patch_jpm_assistance",
     *	options={"expose" = true}
     * )
     * @Method("PATCH")
     */

     public function ajaxPatchJpmAssistanceAction(Request $request, $id)
     {
         $user = $this->get('security.token_storage')->getToken()->getUser();
 
         $em = $this->getDoctrine()->getManager();
         $entity = $em->getRepository("AppBundle:AssistanceHeader")->find($id);
         
         if(!$entity){
            return new JsonResponse(['message' => "Transaction not found."], 404);
         }

         $entity->setDependentDiagnosis($request->get('dependentDiagnosis'));
         $entity->setHospital($request->get('hospital'));
         $entity->setFinalBill($request->get('finalBill'));
         $entity->setAmount($request->get('amount'));
         $entity->setTransType($request->get('transType'));
         $entity->setTransDate($request->get('transDate'));
         $entity->setControlNo($request->get('controlNo'));

         $entity->setUpdatedAt(new \DateTime());
         $entity->setUpdatedBy($user->getUsername());
         $entity->setRemarks($request->get('remarks'));
         $entity->setStatus("A");
 
         $validator = $this->get('validator');
         $violations = $validator->validate($entity);
 
         $errors = [];
 
         if (count($violations) > 0) {
             foreach ($violations as $violation) {
                 $errors[$violation->getPropertyPath()] = $violation->getMessage();
             }
             return new JsonResponse($errors, 400);
         }
 
         $em->flush();
         $em->clear();
         $serializer = $this->get('serializer');
 
         return new JsonResponse($serializer->normalize($entity));
     }
 
    /**
     * @Route("/ajax_select2_assist_type",
     *       name="ajax_select2_assist_type",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxSelect2AssistType(Request $request)
     {
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';
 
         $em = $this->getDoctrine()->getManager();
 
         $sql = "SELECT DISTINCT c.trans_type FROM tbl_assistance_hdr c
                 WHERE  (c.trans_type LIKE ? OR ? IS NULL) AND c.trans_type IS NOT NULL ORDER BY c.trans_type ASC LIMIT 30 ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $searchText);
         $stmt->bindValue(2, ($request->get("searchText") == "") ? null : $request->get("searchText"));
 
         $stmt->execute();
         $data = $stmt->fetchAll();
 
         if (count($data) <= 0) {
             return new JsonResponse(array());
         }
 
         $em->clear();
 
         return new JsonResponse($data);
     }

     /**
     * @Route("/ajax_select2_assist_diagnosis",
     *       name="ajax_select2_assist_diagnosis",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxSelect2AssistDiagnosis(Request $request)
     {
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';
 
         $em = $this->getDoctrine()->getManager();
 
         $sql = "SELECT DISTINCT c.dependent_diagnosis FROM tbl_assistance_hdr c
                 WHERE  (c.dependent_diagnosis LIKE ? OR ? IS NULL) AND c.dependent_diagnosis IS NOT NULL ORDER BY c.dependent_diagnosis ASC LIMIT 30 ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $searchText);
         $stmt->bindValue(2, ($request->get("searchText") == "") ? null : $request->get("searchText"));
 
         $stmt->execute();
         $data = $stmt->fetchAll();
 
         if (count($data) <= 0) {
             return new JsonResponse(array());
         }
 
         $em->clear();
 
         return new JsonResponse($data);
     }

     /**
     * @Route("/ajax_select2_assist_hospital",
     *       name="ajax_select2_assist_hospital",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxSelect2AssistHospital(Request $request)
     {
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';
 
         $em = $this->getDoctrine()->getManager();
 
         $sql = "SELECT DISTINCT c.hospital FROM tbl_assistance_hdr c
                 WHERE  (c.hospital LIKE ? OR ? IS NULL) AND c.hospital IS NOT NULL ORDER BY c.hospital ASC LIMIT 30 ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $searchText);
         $stmt->bindValue(2, ($request->get("searchText") == "") ? null : $request->get("searchText"));
 
         $stmt->execute();
         $data = $stmt->fetchAll();
 
         if (count($data) <= 0) {
             return new JsonResponse(array());
         }
 
         $em->clear();
 
         return new JsonResponse($data);
     }

     /**
     * @Route("/ajax_select2_assist_district",
     *       name="ajax_select2_assist_district",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxSelect2District(Request $request)
     {
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';

         $em = $this->getDoctrine()->getManager();
 
         $sql = "SELECT DISTINCT c.district FROM tbl_assistance_profile c
                 WHERE  (c.district LIKE ? OR ? IS NULL) AND c.district IS NOT NULL ORDER BY c.district ASC LIMIT 30 ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $searchText);
         $stmt->bindValue(2, ($request->get("searchText") == "") ? null : $request->get("searchText"));
 
         $stmt->execute();
         $data = $stmt->fetchAll();
 
         if (count($data) <= 0) {
             return new JsonResponse(array());
         }
 
         $em->clear();
 
         return new JsonResponse($data);
     }

     /**
     * @Route("/ajax_select2_assist_purok",
     *       name="ajax_select2_assist_purok",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxSelect2Purok(Request $request)
     {
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';
         
         $em = $this->getDoctrine()->getManager();
         $sql = "SELECT DISTINCT c.purok FROM tbl_assistance_profile c
                 WHERE  (c.purok LIKE ? OR ? IS NULL) AND c.purok IS NOT NULL ORDER BY c.purok ASC LIMIT 30 ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $searchText);
         $stmt->bindValue(2, ($request->get("searchText") == "") ? null : $request->get("searchText"));
 
         $stmt->execute();
         $data = $stmt->fetchAll();
 
         if (count($data) <= 0) {
             return new JsonResponse(array());
         }
 
         $em->clear();
 
         return new JsonResponse($data);
     }

     /**
     * @Route("/ajax_select2_assist_civil",
     *       name="ajax_select2_assist_civil",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxSelect2Civil(Request $request)
     {
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';
 
         $em = $this->getDoctrine()->getManager();
         $sql = "SELECT DISTINCT c.civil_status FROM tbl_assistance_profile c
                 WHERE  (c.civil_status LIKE ? OR ? IS NULL) AND c.civil_status IS NOT NULL ORDER BY c.civil_status ASC LIMIT 30 ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $searchText);
         $stmt->bindValue(2, ($request->get("searchText") == "") ? null : $request->get("searchText"));
 
         $stmt->execute();
         $data = $stmt->fetchAll();
 
         if (count($data) <= 0) {
             return new JsonResponse(array());
         }
 
         $em->clear();
 
         return new JsonResponse($data);
     }


     /**
     * @Route("/ajax_select2_assist_occupation",
     *       name="ajax_select2_assist_occupation",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxSelect2Occupation(Request $request)
     {
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';
 
         $em = $this->getDoctrine()->getManager();
         $sql = "SELECT DISTINCT c.occupation FROM tbl_assistance_profile c
                 WHERE  (c.occupation LIKE ? OR ? IS NULL) AND c.occupation IS NOT NULL ORDER BY c.occupation ASC LIMIT 30 ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $searchText);
         $stmt->bindValue(2, ($request->get("searchText") == "") ? null : $request->get("searchText"));
 
         $stmt->execute();
         $data = $stmt->fetchAll();
 
         if (count($data) <= 0) {
             return new JsonResponse(array());
         }
 
         $em->clear();
 
         return new JsonResponse($data);
     }

     /**
     * @Route("/ajax_select2_assist_id_type",
     *       name="ajax_select2_assist_id_type",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxSelect2IdType(Request $request)
     {
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';
 
         $em = $this->getDoctrine()->getManager();
         $sql = "SELECT DISTINCT c.type_of_id FROM tbl_assistance_hdr c
                 WHERE  (c.type_of_id LIKE ? OR ? IS NULL) AND c.type_of_id IS NOT NULL ORDER BY c.type_of_id ASC LIMIT 30 ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $searchText);
         $stmt->bindValue(2, ($request->get("searchText") == "") ? null : $request->get("searchText"));
 
         $stmt->execute();
         $data = $stmt->fetchAll();
 
         if (count($data) <= 0) {
             return new JsonResponse(array());
         }
 
         $em->clear();
 
         return new JsonResponse($data);
     }

     /**
     * @Route("/ajax_select2_assist_educ_level",
     *       name="ajax_select2_assist_educ_level",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxSelect2EducLevel(Request $request)
     {
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';
 
         $em = $this->getDoctrine()->getManager();
         $sql = "SELECT DISTINCT c.dependent_educ_level FROM tbl_assistance_hdr c
                 WHERE  (c.dependent_educ_level LIKE ? OR ? IS NULL) AND c.dependent_educ_level IS NOT NULL ORDER BY c.dependent_educ_level ASC LIMIT 30 ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $searchText);
         $stmt->bindValue(2, ($request->get("searchText") == "") ? null : $request->get("searchText"));
 
         $stmt->execute();
         $data = $stmt->fetchAll();
 
         if (count($data) <= 0) {
             return new JsonResponse(array());
         }
 
         $em->clear();
 
         return new JsonResponse($data);
     }

    /**
     * @Route("/ajax_get_datatable_jpm_assistance", 
     * name="ajax_get_datatable_jpm_assistance", options={"expose"=true})
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetDatatableJpmAssistanceAction(Request $request)
    {
        $columns = array(
            0 => "h.id",
            1 => "h.trans_date",
            2 => "h.control_no",
            3 => "h.client_name",
            4 => "h.dependent_name",
            5 => "h.municipality_name",
            6 => "h.barangay_name",
            7 => "h.hospital"            
        );

        $sWhere = "";

        $select['h.municipality_name'] = $request->get('municipalityName');
        $select['h.barangay_name'] = $request->get('barangayName');
        $select['h.trans_date'] = $request->get('transDate');
        $select['h.control_no'] = $request->get('controlNo');
        $select['h.client_name'] = $request->get('clientName');
        $select['h.dependent_name'] = $request->get('dependentName');
        $select['h.hospital'] = $request->get('hospital');

        foreach ($select as $key => $value) {
            $searchValue = $select[$key];
            if ($searchValue != null || !empty($searchValue)) {
                $sWhere .= " AND " . $key . " LIKE \"%" . $searchValue . "%\"";
            }
        }

        $sOrder = "";

        if (null !== $request->query->get('order')) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval(count($request->query->get('order'))); $i++) {
                if ($request->query->get('columns')[$request->query->get('order')[$i]['column']]['orderable']) {
                    $selected_column = $columns[$request->query->get('order')[$i]['column']];
                    $sOrder .= " " . $selected_column . " " .
                        ($request->query->get('order')[$i]['dir'] === 'asc' ? 'ASC' : 'DESC') . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }

        $start = 1;
        $length = 1;

        if (null !== $request->query->get('start') && null !== $request->query->get('length')) {
            $start = intval($request->query->get('start'));
            $length = intval($request->query->get('length'));
        }

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $sql = "SELECT COALESCE(count(h.id),0) FROM tbl_assistance_hdr h WHERE 1 ";

        $stmt = $em->getConnection()->query($sql);
        $recordsTotal = $stmt->fetchColumn();

        $sql = "SELECT COALESCE(COUNT(h.id),0) FROM tbl_assistance_hdr h WHERE 1 ";

        $sql .= $sWhere . " " . $sOrder;
        $stmt = $em->getConnection()->query($sql);
        $recordsFiltered = $stmt->fetchColumn();

        $sql = "SELECT h.*
                FROM tbl_assistance_hdr h 
                WHERE 1 " . $sWhere . " " . $sOrder . " LIMIT {$length} OFFSET {$start} ";

        $stmt = $em->getConnection()->query($sql);
        $data = [];


        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $draw = (null !== $request->query->get('draw')) ? $request->query->get('draw') : 0;
        $res['data'] = $data;
        $res['recordsTotal'] = $recordsTotal;
        $res['recordsFiltered'] = $recordsFiltered;
        $res['draw'] = $draw;

        return new JsonResponse($res);
    }
    
    /**
     * @Route("/ajax_get_datatable_profile_assistance", 
     * name="ajax_get_datatable_profile_assistance", options={"expose"=true})
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetDatatableProfileAssistanceAction(Request $request)
    {
        $columns = array(
            0 => "h.id",
            1 => "h.trans_date",
            2 => "h.control_no",
            3 => "h.client_name",
            4 => "h.dependent_name",
            5 => "h.municipality_name",
            6 => "h.barangay_name",
            7 => "h.hospital"            
        );

        $sWhere = "";

        $select['h.client_name'] = $request->get('clientName');

        foreach ($select as $key => $value) {
            $searchValue = $select[$key];
            if ($searchValue != null || !empty($searchValue)) {
                if($key == 'h.client_name'){
                    $sWhere .= " AND (" . $key . " LIKE \"%" . $searchValue . "%\" " . " OR h.dependent_name " . " LIKE \"%" . $searchValue . "%\") ";
                }
            }
        }

        $sOrder = "";

        if (null !== $request->query->get('order')) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval(count($request->query->get('order'))); $i++) {
                if ($request->query->get('columns')[$request->query->get('order')[$i]['column']]['orderable']) {
                    $selected_column = $columns[$request->query->get('order')[$i]['column']];
                    $sOrder .= " " . $selected_column . " " .
                        ($request->query->get('order')[$i]['dir'] === 'asc' ? 'ASC' : 'DESC') . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }

        $start = 1;
        $length = 1;

        if (null !== $request->query->get('start') && null !== $request->query->get('length')) {
            $start = intval($request->query->get('start'));
            $length = intval($request->query->get('length'));
        }

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $sql = "SELECT COALESCE(count(h.id),0) FROM tbl_assistance_hdr h WHERE 1 ";

        $stmt = $em->getConnection()->query($sql);
        $recordsTotal = $stmt->fetchColumn();

        $sql = "SELECT COALESCE(COUNT(h.id),0) FROM tbl_assistance_hdr h WHERE 1 ";

        $sql .= $sWhere . " " . $sOrder;
        $stmt = $em->getConnection()->query($sql);
        $recordsFiltered = $stmt->fetchColumn();

        $sql = "SELECT h.*
                FROM tbl_assistance_hdr h 
                WHERE 1 " . $sWhere . " " . $sOrder . " LIMIT {$length} OFFSET {$start} ";

        $stmt = $em->getConnection()->query($sql);
        $data = [];


        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $draw = (null !== $request->query->get('draw')) ? $request->query->get('draw') : 0;
        $res['data'] = $data;
        $res['recordsTotal'] = $recordsTotal;
        $res['recordsFiltered'] = $recordsFiltered;
        $res['draw'] = $draw;

        return new JsonResponse($res);
    }

    /**
     * @Route("/ajax_get_datatable_assistance_profile", 
     * name="ajax_get_datatable_assistance_profile", options={"expose"=true})
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetDatatableAssistanceProfileAction(Request $request)
    {
        $columns = array(
            0 => "h.id",
            1 => "h.fullname",
            2 => "h.voter_name",
            3 => "h.municipality_name",
            4 => "h.barangay_name",
            5 => "h.purok",
            6 => "h.contact_no",
        );

        $sWhere = "";

        $select['h.municipality_name'] = $request->get('municipalityName');
        $select['h.barangay_name'] = $request->get('barangayName');
        $select['h.contact_no'] = $request->get('contactNo');
        $select['h.birthdate'] = $request->get('birthdate');
        $select['h.fullname'] = $request->get('fullname');
        $select['h.voter_name'] = $request->get('voterName');
        

        foreach ($select as $key => $value) {
            $searchValue = $select[$key];
            if ($searchValue != null || !empty($searchValue)) {
                $sWhere .= " AND " . $key . " LIKE \"%" . $searchValue . "%\"";
            }
        }

        $sOrder = "";

        if (null !== $request->query->get('order')) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval(count($request->query->get('order'))); $i++) {
                if ($request->query->get('columns')[$request->query->get('order')[$i]['column']]['orderable']) {
                    $selected_column = $columns[$request->query->get('order')[$i]['column']];
                    $sOrder .= " " . $selected_column . " " .
                        ($request->query->get('order')[$i]['dir'] === 'asc' ? 'ASC' : 'DESC') . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }

        $start = 1;
        $length = 1;

        if (null !== $request->query->get('start') && null !== $request->query->get('length')) {
            $start = intval($request->query->get('start'));
            $length = intval($request->query->get('length'));
        }

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $sql = "SELECT COALESCE(count(h.id),0) FROM tbl_assistance_profile h WHERE 1 ";

        $stmt = $em->getConnection()->query($sql);
        $recordsTotal = $stmt->fetchColumn();

        $sql = "SELECT COALESCE(COUNT(h.id),0) FROM tbl_assistance_profile h WHERE 1 ";

        $sql .= $sWhere . " " . $sOrder;
        $stmt = $em->getConnection()->query($sql);
        $recordsFiltered = $stmt->fetchColumn();

        $sql = "SELECT h.*
                FROM tbl_assistance_profile h 
                WHERE 1 " . $sWhere . " " . $sOrder . " LIMIT {$length} OFFSET {$start} ";

        $stmt = $em->getConnection()->query($sql);
        $data = [];


        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $draw = (null !== $request->query->get('draw')) ? $request->query->get('draw') : 0;
        $res['data'] = $data;
        $res['recordsTotal'] = $recordsTotal;
        $res['recordsFiltered'] = $recordsFiltered;
        $res['draw'] = $draw;

        return new JsonResponse($res);
    }


    /**
     * @Route("/ajax_post_assistance_profile",
     *     name="ajax_post_assistance_profile",
     *    options={"expose" = true}
     * )
     * @Method("POST")
     */

     public function ajaxPostAssistanceProfileAction(Request $request)
     {
         $em = $this->getDoctrine()->getManager();
         $user = $this->get('security.token_storage')->getToken()->getUser();
 
         $entity = new AssistanceProfile();

         $entity->setFirstname(trim(strtoupper($request->get('firstname'))));
         $entity->setMiddlename(trim(strtoupper($request->get('middlename'))));
         $entity->setLastname(trim(strtoupper($request->get('lastname'))));
         $entity->setExtname(trim(strtoupper($request->get('extName'))));
 
         $voterName = $entity->getLastname() . ', ' . $entity->getFirstname() . ' ' . $entity->getMiddlename() . ' ' . $entity->getExtname();
         $entity->setFullname(trim(strtoupper($voterName)));

         $entity->setBirthdate(trim($request->get('birthdate')));
         $entity->setContactNo($request->get('contactNo'));
         $entity->setGender($request->get('gender'));
         $entity->setOccupation(trim(strtoupper($request->get('occupation'))));
         $entity->setCivilStatus(trim(strtoupper($request->get('civilStatus'))));
         $entity->setMonthlyIncome(trim($request->get('monthlyIncome')));

         $entity->setDistrict(trim(strtoupper($request->get('district'))));
         $entity->setPurok(trim(strtoupper($request->get('purok'))));
         
         $entity->setMunicipalityNo($request->get('municipalityNo'));
         $entity->setBarangayNo($request->get('brgyNo'));

         $entity->setProVoterId($request->get('proVoterId'));
         $entity->setProIdCode($request->get('proIdCode'));
         $entity->setGeneratedIdNo($request->get('generatedIdNo'));
         $entity->setVMunicipalityName($request->get('vMunicipalityName'));
         $entity->setVBarangayName($request->get('vBarangayName'));
         $entity->setVoterName($request->get('voterName'));
         $entity->setIsNonVoter($request->get('isNonVoter'));

         $entity->setCreatedAt(new \DateTime());
         $entity->setCreatedBy($user->getUsername());
         $entity->setStatus('A');
 
         $validator = $this->get('validator');
         $violations = $validator->validate($entity);
 
         $errors = [];
 
         if (count($violations) > 0) {
             foreach ($violations as $violation) {
                 $errors[$violation->getPropertyPath()] = $violation->getMessage();
             }
             return new JsonResponse($errors, 400);
         }
 
         
 
         $sql = "SELECT * FROM psw_municipality
         WHERE province_code = ?
         AND municipality_no = ? ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, 53);
         $stmt->bindValue(2, $entity->getMunicipalityNo());
         $stmt->execute();
 
         $municipality = $stmt->fetch(\PDO::FETCH_ASSOC);
 
         if ($municipality != null) {
             $entity->setMunicipalityName($municipality['name']);
         }
 
         $sql = "SELECT * FROM psw_barangay
         WHERE brgy_code = ? ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, 53 . $entity->getMunicipalityNo() . $entity->getBarangayNo());
         $stmt->execute();
 
         $barangay = $stmt->fetch(\PDO::FETCH_ASSOC);
 
         if ($barangay != null) {
             $entity->setBarangayName($barangay['name']);
         }
         
         $em->persist($entity);


         $em->flush();
         $entity->setProfileId('BRKS' . $entity->getId());
         $em->flush();
         $em->clear();
 
         $serializer = $this->get('serializer');
 
         return new JsonResponse($serializer->normalize($entity));
     }

     /**
     * @Route("/ajax_patch_assistance_profile/{id}",
     *     name="ajax_patch_assistance_profile",
     *    options={"expose" = true}
     * )
     * @Method("PATCH")
     */

     public function ajaxPatchAssistanceProfileAction(Request $request, $id)
     {
         $em = $this->getDoctrine()->getManager();
         $user = $this->get('security.token_storage')->getToken()->getUser();
 
         $entity = $em->getRepository("AppBundle:AssistanceProfile")->find($id);
         
         if(!$entity){
            return new JsonResponse(['message' => "Profile not found."], 404);
         }

         $entity->setFirstname(trim(strtoupper($request->get('firstname'))));
         $entity->setMiddlename(trim(strtoupper($request->get('middlename'))));
         $entity->setLastname(trim(strtoupper($request->get('lastname'))));
         $entity->setExtname(trim(strtoupper($request->get('extName'))));
 
         $voterName = $entity->getLastname() . ', ' . $entity->getFirstname() . ' ' . $entity->getMiddlename() . ' ' . $entity->getExtname();
         $entity->setFullname(trim(strtoupper($voterName)));

         $entity->setBirthdate(trim($request->get('birthdate')));
         $entity->setContactNo($request->get('contactNo'));
         $entity->setGender($request->get('gender'));
         $entity->setOccupation(trim(strtoupper($request->get('occupation'))));
         $entity->setCivilStatus(trim(strtoupper($request->get('civilStatus'))));
         $entity->setMonthlyIncome(trim($request->get('monthlyIncome')));

         $entity->setDistrict(trim(strtoupper($request->get('district'))));
         $entity->setPurok(trim(strtoupper($request->get('purok'))));
         
         $entity->setMunicipalityNo($request->get('municipalityNo'));
         $entity->setBarangayNo($request->get('brgyNo'));

         $entity->setProVoterId($request->get('proVoterId'));
         $entity->setProIdCode($request->get('proIdCode'));
         $entity->setGeneratedIdNo($request->get('generatedIdNo'));
         $entity->setVMunicipalityName($request->get('vMunicipalityName'));
         $entity->setVBarangayName($request->get('vBarangayName'));
         $entity->setVoterName($request->get('voterName'));
         $entity->setIsNonVoter($request->get('isNonVoter'));

         $entity->setUpdatedAt(new \DateTime());
         $entity->setUpdatedBy($user->getUsername());
 
         $validator = $this->get('validator');
         $violations = $validator->validate($entity);
 
         $errors = [];
 
         if (count($violations) > 0) {
             foreach ($violations as $violation) {
                 $errors[$violation->getPropertyPath()] = $violation->getMessage();
             }
             return new JsonResponse($errors, 400);
         }
 
         $sql = "SELECT * FROM psw_municipality
         WHERE province_code = ?
         AND municipality_no = ? ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, 53);
         $stmt->bindValue(2, $entity->getMunicipalityNo());
         $stmt->execute();
 
         $municipality = $stmt->fetch(\PDO::FETCH_ASSOC);
 
         if ($municipality != null) {
             $entity->setMunicipalityName($municipality['name']);
         }
 
         $sql = "SELECT * FROM psw_barangay
         WHERE brgy_code = ? ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, 53 . $entity->getMunicipalityNo() . $entity->getBarangayNo());
         $stmt->execute();
 
         $barangay = $stmt->fetch(\PDO::FETCH_ASSOC);
 
         if ($barangay != null) {
             $entity->setBarangayName($barangay['name']);
         }

         $em->flush();
         $em->clear();
 
         $serializer = $this->get('serializer');
 
         return new JsonResponse($serializer->normalize($entity));
     }

    /**
    * @Route("/ajax_select2_assistance_profiles", 
    *       name="ajax_select2_assistance_profiles",
    *		options={ "expose" = true }
    * )
    * @Method("GET")
    */

    public function ajaxSelect2AssistanceProfiles(Request $request){
        $em = $this->getDoctrine()->getManager("electPrep2024");
        $user = $this->get('security.token_storage')->getToken()->getUser();


        $searchText = trim(strtoupper($request->get('searchText')));
        $searchText = '%' . strtoupper($searchText) . '%';
        
        $sql = "SELECT p.* FROM tbl_assistance_profile p 
                WHERE p.fullname LIKE ? 
                ORDER BY p.fullname ASC LIMIT 10";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1,$searchText);
        $stmt->execute();

        $data = [];
    
        while( $row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        if(count($data) <= 0)
            return new JsonResponse(array());

        $em->clear();

        return new JsonResponse($data);
    }


    /**
     * @Route("/ajax_get_assistance_header/{id}",
     *       name="ajax_get_assistance_header",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxGetAssistanceHeader($id)
     {
         $em = $this->getDoctrine()->getManager();
         $entity = $em->getRepository("AppBundle:AssistanceHeader")
             ->find($id);
 
         if (!$entity) {
             return new JsonResponse(['message' => 'not found']);
         }
 
         $serializer = $this->get("serializer");
         $entity = $serializer->normalize($entity);
 
         return new JsonResponse($entity);
     }

     /**
     * @Route("/ajax_get_assistance_profile/{id}",
     *       name="ajax_get_assistance_profile",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxGetAssistanceProfile($id)
     {
         $em = $this->getDoctrine()->getManager();
         $profile = $em->getRepository("AppBundle:AssistanceProfile")
             ->find($id);
 
         if (!$profile) {
             return new JsonResponse(['message' => 'not found']);
         }
 
         $serializer = $this->get("serializer");
         $profile = $serializer->normalize($profile);
 
         return new JsonResponse($profile);
     }

    /**
     * @Route("/ajax_post_assistance_detail/{id}", 
     * 	name="ajax_post_assistance_detail",
     *	options={"expose" = true}
     * )
     * @Method("POST")
     */

    public function ajaxPostAssistanceDetailAction(Request $request, $id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager("electPrep2024");


        $hdr = $em->getRepository("AppBundle:AssistanceHeader")->find($id);

        if (!$hdr) {
            return new JsonResponse(['message' => 'header not found'], 404);
        }

        $profile = $em->getRepository("AppBundle:AssistanceProfile")->find($request->get('profileId'));

        if (!$profile) {
            return new JsonResponse(['message' => 'profile not found.'], 404);
        }

        $entity = new AssistanceDetail();

        $entity->setHdrId($hdr->getId());
        $entity->setProfileId($request->get('profileId'));
        $entity->setBeneficiaryName($profile->getFullname());

        $entity->setMunicipalityName($profile->getMunicipalityName());
        $entity->setBarangayName($profile->getBarangayName());
        $entity->setMunicipalityNo($profile->getMunicipalityNo());
        $entity->setBarangayNo($profile->getBarangayNo());

        $entity->setVoterName($profile->getVoterName());
        $entity->setProVoterId($profile->getProVoterId());
        $entity->setProIdCode($profile->getProIdCode());
        $entity->setGeneratedIdNo($profile->getGeneratedIdNo());
        $entity->setIsNonVoter($profile->getIsNonVoter());

        $cellphoneNo = empty($request->get("cellphoneNo")) ? $profile->getCellphoneNo() : $request->get('cellphoneNo');

        $profile->setCellphoneNo($cellphoneNo);

        $entity->setCreatedAt(new \DateTime());
        $entity->setCreatedBy($user->getUsername());
        $entity->setUpdatedAt(new \DateTime());
        $entity->setUpdatedBy($user->getUsername());
        $entity->setStatus("A");

        $validator = $this->get('validator');
        $violations = $validator->validate($entity);

        $errors = [];

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return new JsonResponse($errors, 400);
        }

        $em->persist($entity);
        $em->flush();
        $em->clear();
        $serializer = $this->get('serializer');

        return new JsonResponse($serializer->normalize($entity));
    }
 

    /**
     * @Route("/ajax_delete_assistance_hdr/{id}", 
     * 	name="ajax_delete_assistance_hdr",
     *	options={"expose" = true}
     * )
     * @Method("DELETE")
     */

    public function ajaxDeleteAssistanceHdrAction($id)
    {
        $em = $this->getDoctrine()->getManager();
    
        $entity = $em->getRepository("AppBundle:AssistanceHeader")->find($id);

        if(!$entity)
            return new JsonResponse(null, 404);

        $em->remove($entity);
        $em->flush();

        return new JsonResponse(null, 200);
    }

    /**
     * @Route("/ajax_delete_assistance_profile/{id}", 
     * 	name="ajax_delete_assistance_profile",
     *	options={"expose" = true}
     * )
     * @Method("DELETE")
     */

    public function ajaxDeleteAssistanceProfileAction($id)
    {
        $em = $this->getDoctrine()->getManager();
    
        $entity = $em->getRepository("AppBundle:AssistanceProfile")->find($id);

        if(!$entity)
            return new JsonResponse(null, 404);

        $em->remove($entity);
        $em->flush();

        return new JsonResponse(null, 200);
    }


    /**
     * @Route("/ajax_post_group_assistance", 
     * 	name="ajax_post_group_assistance",
     *	options={"expose" = true}
     * )
     * @Method("POST")
     */

     public function ajaxPostGroupAssistanceAction(Request $request)
     {
         $user = $this->get('security.token_storage')->getToken()->getUser();
 
         $em = $this->getDoctrine()->getManager();
 
         $entity = new AssistanceHeader();

         $client = null;
         $dependent = null;

         if(!empty($request->get('clientProfileId'))){
            $client = $em->getRepository("AppBundle:AssistanceProfile")->find((int)$request->get('clientProfileId'));
         }
        
         if(!empty($request->get('dependentProfileId'))){
             $dependent = $em->getRepository("AppBundle:AssistanceProfile")->find((int)$request->get('dependentProfileId'));
         }

         if($client){
            $contactNo = !empty($request->get('contactNo')) ? $request->get('contactNo') : $client->getContactNo();
            $entity->setClientName($client->getFullname());
            $entity->setClientVoterName($client->getVoterName());
            $entity->setClientGeneratedIdNo($client->getGeneratedIdNo());
            $entity->setContactNo($contactNo);
            $entity->setMunicipalityNo($client->getMunicipalityNo());
            $entity->setMunicipalityName($client->getMunicipalityName());
            $entity->setBarangayNo($client->getBarangayNo());
            $entity->setBarangayName($client->getBarangayName());
            $entity->setDistrict($client->getDistrict());
            $entity->setPurok($client->getPurok());
         }else{
            return new JsonResponse(['clientProfileId' => 'Client  name cannot be empty.'], 400);
         }

         if($dependent){
            $entity->setDependentName($dependent->getFullname());
            $entity->setDependentVoterName($dependent->getVoterName());
            $entity->setDependentGeneratedIdNo($dependent->getGeneratedIdNo());
            $entity->setDependentAddress($dependent->getPurok() . ', ' . $dependent->getBarangayName() . ',' . $dependent->getMunicipalityName());
         }else{
            return new JsonResponse(['dependentProfileId' => 'Dependent not found.'], 400);
         }

         $group = $em->getRepository("AppBundle:GroupAssistance")->find((int)$request->get('groupId'));

        if(!$group){
            return new JsonResponse(['groupId' => 'Group not found.'], 400);
         }

         $entity->setGroupId($group->getHdrId());
         $entity->setClientProfileId($request->get('clientProfileId'));
         $entity->setDependentProfileId($request->get('dependentProfileId'));

         $entity->setOccupation(trim(strtoupper($request->get('occupation'))));
         $entity->setMonthlyIncome($request->get('monthlyIncome'));
         $entity->setTypeOfId(trim(strtoupper($request->get('typeOfId'))));

         $entity->setDependentEducLevel(trim(strtoupper($request->get('dependentEducLevel'))));
         $entity->setDependentMaidenName(trim(strtoupper($request->get('dependentMaidenName'))));

         $entity->setTransType(trim(strtoupper($group->getAssistType())));
         $entity->setTransDate($group->getBatchDate());
         $entity->setCreatedAt(new \DateTime());
         $entity->setCreatedBy($user->getUsername());
         $entity->setUpdatedAt(new \DateTime());
         $entity->setUpdatedBy($user->getUsername());
         $entity->setRemarks($request->get('remarks'));
         $entity->setStatus("A");

 
         $validator = $this->get('validator');
         $violations = $validator->validate($entity,null, ['groupCreate']);
 
         $errors = [];
 
         if (count($violations) > 0) {
             foreach ($violations as $violation) {
                 $errors[$violation->getPropertyPath()] = $violation->getMessage();
             }
             return new JsonResponse($errors, 400);
         }
 
         $client->setOccupation($entity->getOccupation());
         $client->setMonthlyIncome($entity->getMonthlyIncome());
         $client->setContactNo($entity->getContactNo());

         $em->persist($entity);
         $em->flush();
 
         $em->clear();
         $serializer = $this->get('serializer');
 
         return new JsonResponse($serializer->normalize($entity));
     }
}
