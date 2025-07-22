<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
* @Route("/field")
*/

class FieldController extends Controller 
{   
   
	/**
    * @Route("/uploads", name="field_index", options={"main" = true})
    */

    public function uploadsAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $hostIp = $this->getParameter('host_ip');
        $imgUrl = $this->getParameter('img_url');

        return $this->render('template/field/uploads.html.twig',[ 'user' => $user, 'hostIp' => $hostIp , 'imgUrl' => $imgUrl ]);
    } 

     /**
     * @Route("/ajax_m_get_field_photos_for_cropping",
     *       name="ajax_m_get_field_photos_for_cropping",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxGetFieldPhotosForCropping(Request $request)
     {
         $em = $this->getDoctrine()->getManager();
 
         $municipalityNo = $request->get('municipalityNo');
         $brgyNo = $request->get("brgyNo");
         $pageSize = empty($request->get("pageSize")) ? 10 : $request->get("pageSize");

         $imgUrl = $this->getParameter('img_url');
 
         $sql = "SELECT pv.* FROM tbl_project_voter pv
                 WHERE (pv.municipality_no = ? OR ? IS NULL ) AND (pv.brgy_no = ? OR ? IS NULL ) AND pv.elect_id = ? 
                 AND pv.has_new_photo = 1 AND (pv.cropped_photo <> 1 OR pv.cropped_photo IS NULL ) ";
 
         $sql .= " ORDER BY pv.voter_name ASC LIMIT {$pageSize} ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $municipalityNo);
         $stmt->bindValue(2, empty($municipalityNo) ? null :  $municipalityNo);
         $stmt->bindValue(3, $brgyNo);
         $stmt->bindValue(4, empty($brgyNo) ? null : $brgyNo );
         $stmt->bindValue(5, 423);
         $stmt->execute();
 
         $data = [];
 
         while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
             $row['imgUrl'] = $imgUrl . '3_' . $row['generated_id_no'] . '?' . strtotime((new \DateTime())->format('Y-m-d H:i:s'));
             $row['cellphone_no'] = $row['cellphone'];
             $data[] = $row;
         }
 
         return new JsonResponse($data);
     }
     
    /**
     * @Route("/ajax_m_get_field_photos_remaining",
     *       name="ajax_m_get_field_photos_remaining",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxGetFieldPhotosRemaining(Request $request)
     {
         $em = $this->getDoctrine()->getManager();
 
         $municipalityNo = $request->get('municipalityNo');
         $brgyNo = $request->get("brgyNo");

         $imgUrl = $this->getParameter('img_url');
 
         $sql = "SELECT count(*) AS total_remaining_photos FROM tbl_project_voter pv
                 WHERE (pv.municipality_no = ? OR ? IS NULL ) AND (pv.brgy_no = ? OR ? IS NULL ) AND pv.elect_id = ? 
                 AND pv.has_new_photo = 1 AND (pv.cropped_photo <> 1 OR pv.cropped_photo IS NULL OR pv.cropped_photo = 0 ) ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $municipalityNo);
         $stmt->bindValue(2, empty($municipalityNo) ? null :  $municipalityNo);
         $stmt->bindValue(3, $brgyNo);
         $stmt->bindValue(4, empty($brgyNo) ? null : $brgyNo );
         $stmt->bindValue(5, 423);
         $stmt->execute();
 
         $data = $stmt->fetch(\PDO::FETCH_ASSOC);
 
         return new JsonResponse($data);
     }

     /**
     * @Route("/ajax_m_get_field_photos_remaining_per_municipality",
     *       name="ajax_m_get_field_photos_remaining_per_municipality",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxGetFieldPhotosRemainingpPerMunicipality(Request $request)
     {
         $em = $this->getDoctrine()->getManager();
 
         $municipalityNo = $request->get('municipalityNo');
         $brgyNo = $request->get("brgyNo");

         $imgUrl = $this->getParameter('img_url');
 
         $sql = "SELECT count(*) AS total_remaining_photos, pv.municipality_name as label_name,
                 COALESCE(SUM( CASE WHEN (pv.cropped_photo = 0 OR pv.cropped_photo IS NULL ) THEN 1 ELSE 0 END),0) AS total_uncropped,
                 COALESCE(SUM( CASE WHEN pv.cropped_photo = 1 THEN 1 ELSE 0 END),0) AS total_cropped
                 FROM tbl_project_voter pv
                 WHERE  pv.elect_id = ? 
                 AND pv.has_new_photo = 1  
                 GROUP BY pv.municipality_name 
                 ORDER BY pv.municipality_name";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, 423);
         $stmt->execute();
 
         $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
 
         return new JsonResponse($data);
     }


    /**
     * @Route("/ajax_m_get_field_photos_remaining_per_barangay",
     *       name="ajax_m_get_field_photos_remaining_per_barangay",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxGetFieldPhotosRemainingpPerBarangay(Request $request)
     {
         $em = $this->getDoctrine()->getManager();
 
         $municipalityNo = $request->get('municipalityNo');

         $imgUrl = $this->getParameter('img_url');
 
         $sql = "SELECT count(*) AS total_remaining_photos, pv.municipality_name, pv.barangay_name as label_name,
                 COALESCE(SUM( CASE WHEN (pv.cropped_photo = 0 OR pv.cropped_photo IS NULL ) THEN 1 ELSE 0 END),0) AS total_uncropped,
                 COALESCE(SUM( CASE WHEN pv.cropped_photo = 1 THEN 1 ELSE 0 END),0) AS total_cropped
                 FROM tbl_project_voter pv
                 WHERE pv.municipality_no = ? AND pv.elect_id = ? 
                 AND pv.has_new_photo = 1  
                 GROUP BY pv.municipality_name , pv.barangay_name
                 ORDER BY pv.municipality_name,pv.barangay_name";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $municipalityNo);
         $stmt->bindValue(2, 423);
         $stmt->execute();
 
         $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
 
         return new JsonResponse($data);
     }

}
