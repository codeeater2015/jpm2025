<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\GroupAssistance;
use AppBundle\Entity\ProjectVoter;

/**
 * @Route("/group-assistance")
 */

class GroupAssistanceController extends Controller
{
    const STATUS_ACTIVE = 'A';
    const MODULE_MAIN = "GROUP_ASSISTANCE_COMPONENT";

    /**
     * @Route("", name="group_assistance_index", options={"main" = true})
     */

    public function indexAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        return $this->render('template/group-assistance/index.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/ajax_group_assist_type_select2",
     *       name="ajax_group_assist_type_select2",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxGroupAssistTypeSelect2(Request $request)
     {
         $searchText = trim(strtoupper($request->get('searchText')));
         $searchText = '%' . strtoupper($searchText) . '%';
 
         $em = $this->getDoctrine()->getManager();
 
         $sql = "SELECT DISTINCT c.assist_type FROM tbl_group_assistance c
                 WHERE  (c.assist_type LIKE ? OR ? IS NULL) AND c.assist_type IS NOT NULL ORDER BY c.assist_type ASC LIMIT 30 ";
 
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
     * @Route("/ajax_post_jpm_group_assistance", 
     * 	name="ajax_post_jpm_group_assistance",
     *	options={"expose" = true}
     * )
     * @Method("POST")
     */

     public function ajaxPostGroupAssistanceAction(Request $request)
     {
         $user = $this->get('security.token_storage')->getToken()->getUser();
         $em = $this->getDoctrine()->getManager();
 
         $entity = new GroupAssistance();
         $entity->setMunicipalityName($request->get('municipalityName'));
         $entity->setAssistType(trim(strtoupper($request->get('assistType'))));
         $entity->setBatchDate($request->get('batchDate'));
         $entity->setBatchLabel(trim(strtoupper($request->get('batchLabel'))));
         $entity->setCreatedAt(new \DateTime());
         $entity->setCreatedBy($user->getUsername());
         $entity->setUpdatedAt(new \DateTime());
         $entity->setUpdatedBy($user->getUsername());
         $entity->setRemarks(trim(strtoupper($request->get('remarks'))));
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
     * @Route("/ajax_get_datatable_group_assistance", 
     * name="ajax_get_datatable_group_assistance", options={"expose"=true})
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetDatatableGroupAssistanceAction(Request $request)
    {
        $columns = array(
            0 => "h.hdr_id",
            1 => "h.municipality_name",
            2 => "h.assist_type",
            3 => "h.batch_date",
            4 => "h.batch_label",
            5 => "h.remarks"
        );

        $sWhere = "";

        $select['h.municipality_name'] = $request->get('municipalityName');
        $select['h.assist_type'] = $request->get('assistType');
        $select['h.batch_date'] = $request->get('batchDate');
        $select['h.batch_label'] = $request->get('batchLabel');
        $select['h.remarks'] = $request->get('remarks');

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

        $sql = "SELECT COALESCE(count(h.hdr_id),0) FROM tbl_group_assistance h WHERE 1 ";

        $stmt = $em->getConnection()->query($sql);
        $recordsTotal = $stmt->fetchColumn();

        $sql = "SELECT COALESCE(COUNT(h.hdr_id),0) FROM tbl_group_assistance h WHERE 1 ";

        $sql .= $sWhere . " " . $sOrder;
        $stmt = $em->getConnection()->query($sql);
        $recordsFiltered = $stmt->fetchColumn();

        $sql = "SELECT h.*
                FROM tbl_group_assistance h 
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
     * @Route("/ajax_delete_group_assistance_hdr/{id}", 
     * 	name="ajax_delete_group_assistance_hdr",
     *	options={"expose" = true}
     * )
     * @Method("DELETE")
     */

    public function ajaxDeleteGroupAssistanceHdrAction($id)
    {
        $em = $this->getDoctrine()->getManager();
    
        $entity = $em->getRepository("AppBundle:GroupAssistance")->find($id);

        if(!$entity)
            return new JsonResponse(null, 404);

        $em->remove($entity);
        $em->flush();

        return new JsonResponse(null, 200);
    }

    
     /**
     * @Route("/ajax_get_group_assistance/{id}",
     *       name="ajax_get_group_assistance",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxGetGroupAssistance($id)
     {
         $em = $this->getDoctrine()->getManager();
         $entity = $em->getRepository("AppBundle:GroupAssistance")
             ->find($id);
 
         if (!$entity) {
             return new JsonResponse(['message' => 'not found']);
         }
 
         $serializer = $this->get("serializer");
         $entity = $serializer->normalize($entity);
 
         return new JsonResponse($entity);
     }

     /**
     * @Route("/ajax_get_datatable_group_assistance_detail", 
     * name="ajax_get_datatable_group_assistance_detail", options={"expose"=true})
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxGetDatatableGroupAssistanceDetailAction(Request $request)
    {
        $columns = array(
            0 => "h.id",
            1 => "h.client_name",
            2 => "h.municipality_name",
            3 => "h.barangay_name",
            4 => "h.occupation",
            5 => "h.monthly_income",
            6 => "h.type_of_id",
            7 => "h.dependent_name"
        );

        $sWhere = "";

        $select['h.municipality_name'] = $request->get('municipalityName');
        $select['h.assist_type'] = $request->get('assistType');
        $select['h.batch_date'] = $request->get('batchDate');
        $select['h.batch_label'] = $request->get('batchLabel');
        $select['h.group_id'] = $request->get('groupId');
        $select['h.remarks'] = $request->get('remarks');

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

}