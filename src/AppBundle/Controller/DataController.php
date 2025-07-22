<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Route("/data")
 */

class DataController extends Controller
{
    const STATUS_ACTIVE = 'A';
    const CATEGORY_POLITICS = 'POLITICS';

    /**
     * @Route("/transfer_rescue_itd",
     *       name="ajax_trasfer_rescue_itd",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxTransferRescueItd(Request $request)
    {
        $em = $this->getDoctrine()->getManager("rescue");

        return new JsonResponse(['message' => 'ok'], 200);
    }

    /**
     * @Route("/transfer_rescue_opcen",
     *       name="ajax_trasfer_rescue_opcen",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxTransferRescueHousehold(Request $request)
    {
        $em = $this->getDoctrine()->getManager("rescue_opcen");

        return new JsonResponse(['message' => 'ok'], 200);
    }

    /**
     * @Route("/transfer_scholar",
     *       name="ajax_trasfer_scholar",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxTransferScholar(Request $request)
    {
        $em = $this->getDoctrine()->getManager("rescue");

        return new JsonResponse(['message' => 'ok'], 200);
    }

    /**
     * @Route("/transfer_voter",
     *       name="ajax_trasfer_voter",
     *        options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxTransferVoter(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get("security.token_storage")->getToken()->getUser();

        $response = new StreamedResponse();
        $response->headers->set("Cache-Control", "no-cache, must-revalidate");
        $response->headers->set("X-Accel-Buffering", "no");
        $response->setStatusCode(200);

        $response->setCallback(function () use ($em, $user) {
            $batchSize = 1000;
            $batchCount = 0;
            $totalRecords = 0;
            $start = 0;

            $sql = "SELECT COUNT(*) FROM tbl_voter";
            $stmt = $em->getConnection()->query($sql);
            $totalRecords = $stmt->fetchColumn();

            $batchCount = (int) ($totalRecords / $batchSize);
            $remainder = $totalRecords - $batchCount * $batchSize;

            if ($remainder > 0) {
                $batchCount++;
            }

            $counter = 0;

            $sql = "DELETE FROM tbl_merged_profile WHERE category = ?";
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, self::CATEGORY_POLITICS);
            $stmt->execute();

            for ($i = 0; $i <= $batchCount; $i++) {

                $start = $i * $batchSize;
                $sql = "SELECT * FROM tbl_voter LIMIT {$batchSize} OFFSET {$start}";
                $stmt = $em->getConnection()->query($sql);

                $rows = [];

                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $counter++;
                    $sql2 = "INSERT INTO tbl_merged_profile(profile_name,category)VALUES(?,?)";
                    $stmt2 = $em->getConnection()->prepare($sql2);
                    $stmt2->bindValue(1, $row['voter_name']);
                    $stmt2->bindValue(2, self::CATEGORY_POLITICS);
                    $stmt2->execute();

                    // $entity = new MergedProfile();
                    // $entity->setProfileName($row['voter_name']);
                    // $entity->setProvinceCode($row['province_code']);
                    // $entity->setMunicipalityNo($row['municipality_no']);
                    // $entity->setBrgyNo($row['brgy_no']);
                    // $entity->setAddress($row['address']);
                    // $entity->setCellphoneNo($row['cellphone_no']);
                    // $entity->setPrecinctNo($row["precinct_no"]);
                    // $entity->setCategory(self::CATEGORY_POLITICS);
                    // $entity->setRemarks($row['remarks']);
                    // $entity->setStatus(self::STATUS_ACTIVE);
                    // $entity->setCreatedBy($user->getUsername());
                    // $entity->setCreatedAt(new \DateTime());

                    // $em->persist($entity);

                    echo $counter . '. ' . $row['voter_name'] . "<br/>";
                    ob_flush();
                    flush();
                }

                // foreach($rows as $row){
                //     $counter++;
                //     $entity = new MergedProfile();
                //     $entity->setProfileName($row['voter_name']);
                //     $entity->setProvinceCode($row['province_code']);
                //     $entity->setMunicipalityNo($row['municipality_no']);
                //     $entity->setBrgyNo($row['brgy_no']);
                //     $entity->setAddress($row['address']);
                //     $entity->setCellphoneNo($row['cellphone_no']);
                //     $entity->setPrecinctNo($row["precinct_no"]);
                //     $entity->setCategory(self::CATEGORY_POLITICS);
                //     $entity->setRemarks($row['remarks']);
                //     $entity->setStatus(self::STATUS_ACTIVE);
                //     $entity->setCreatedBy($user->getUsername());
                //     $entity->setCreatedAt(new \DateTime());

                //     $em->persist($entity);

                //     echo  $counter . '. ' . $row['voter_name'] . "<br/>";
                //     ob_flush();
                //     flush();
                // }

                // $em->flush();
                // $em->clear();
            }

        });

        return $response;

        //return new JsonResponse(['message' => 'ok'],200);
    }

    /**
     * @Route("/ajax_migrate_member/{municipalityNo}",
     *       name="ajax_migrate_member",
     *       options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxMigrateMember($municipalityNo)
    {
        $srcEm = $this->getDoctrine()->getManager("voter2023");
        $destEm = $this->getDoctrine()->getManager("electPrep2024");
        
        $response = new StreamedResponse();
        $response->headers->set("Cache-Control", "no-cache, must-revalidate");
        $response->headers->set("X-Accel-Buffering", "no");
        $response->setStatusCode(200);

        $response->setCallback(function () use ($srcEm, $destEm, $municipalityNo) {

            $sql = "SELECT pv.* FROM tbl_project_voter pv WHERE pv.elect_id = ? AND pv.pro_id = ? AND pv.municipality_no = ? AND to_migrate = 1 AND is_transfered <> 1 AND is_migration_failed <> 1  ORDER BY pv.voter_name ASC LIMIT 4000";

            echo "Query : " . $sql;
            echo "<br/><br/>";
            echo "<br/><br/>";

            $stmt = $srcEm->getConnection()->prepare($sql);
            $stmt->bindValue(1, 4);
            $stmt->bindValue(2, 3);
            $stmt->bindValue(3, $municipalityNo);
            $stmt->execute();

            $voters = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $counter = 0;

            $echoText = "Starting<br/>";

            foreach ($voters as $row) {
                $counter++;
                $echoText = "";

                $sql = "SELECT * FROM tbl_project_voter pv WHERE pv.elect_id = ? AND pv.municipality_no = ? AND pv.voter_name = ? ";
                $stmt = $destEm->getConnection()->prepare($sql);
                $stmt->bindValue(1, 423);
                $stmt->bindValue(2, $row['municipality_no']);
                $stmt->bindValue(3, $row['voter_name']);
                $stmt->execute();

                $destRecord = $stmt->fetch(\PDO::FETCH_ASSOC);
                $recordFound = $destRecord == null ? false : true;

                if ($recordFound) {
                    $echoText .= $counter . '. Dest record found : ' . $destRecord['voter_name'];

                    if($destRecord['has_new_photo'] == 1 ){
                        // new photo has been taken on field
                        // just update limited fields
                        $echoText .= "New photo existed. minimal update.";
                           
                        $sql = "UPDATE tbl_project_voter pv
                        SET pv.has_id = ? , pv.old_cellphone = ?,
                        pv.old_voter_group = ? 
                        WHERE pv.elect_id = ? AND pv.pro_id = ? AND pv.pro_voter_id = ? ";

                        $stmt = $destEm->getConnection()->prepare($sql);
                        $stmt->bindValue(1, $row['has_id']);
                        $stmt->bindValue(2, $row['cellphone']);
                        $stmt->bindValue(3, $row['voter_group']);
                        $stmt->bindValue(4, $destRecord['elect_id']);
                        $stmt->bindValue(5, $destRecord['pro_id']);
                        $stmt->bindValue(6, $destRecord['pro_voter_id']);
                        $stmt->execute();
                        
                        $destEm->flush();

                        $echoText .= "Saving.<br/>";
                    }else{
                        //record has not been updated

                        $echoText .= "No new photo. Updating.";

                        $sql = "UPDATE tbl_project_voter pv
                        SET pv.pro_id_code = ?, pv.generated_id_no = ?, pv.date_generated = ?,
                        pv.has_photo = ? , pv.has_id = ? , pv.photo_at = ? , pv.old_cellphone = ?,
                        pv.old_voter_group = ? 
                        WHERE pv.elect_id = ? AND pv.pro_id = ? AND pv.pro_voter_id = ? ";

                        $stmt = $destEm->getConnection()->prepare($sql);
                        $stmt->bindValue(1, $row['pro_id_code']);
                        $stmt->bindValue(2, $row['generated_id_no']);
                        $stmt->bindValue(3, $row['date_generated']);
                        $stmt->bindValue(4, $row['has_photo']);
                        $stmt->bindValue(5, $row['has_id']);
                        $stmt->bindValue(6, $row['photo_at']);
                        $stmt->bindValue(7, $row['cellphone']);
                        $stmt->bindValue(8, $row['voter_group']);
                        $stmt->bindValue(9, $destRecord['elect_id']);
                        $stmt->bindValue(10, $destRecord['pro_id']);
                        $stmt->bindValue(11, $destRecord['pro_voter_id']);
                        $stmt->execute();

                        $destEm->flush();

                        $echoText .= "Saving.<br/>";
                    }

                    $sql = "UPDATE tbl_project_voter pv
                            SET pv.is_transfered =  1
                            WHERE pv.elect_id = ? AND pv.pro_id = ? AND pv.pro_voter_id = ? ";

                    $stmt = $srcEm->getConnection()->prepare($sql);
                    $stmt->bindValue(1, $row['elect_id']);
                    $stmt->bindValue(2, $row['pro_id']);
                    $stmt->bindValue(3, $row['pro_voter_id']);
                    $stmt->execute();

                    $srcEm->flush();

                } else {

                    $sql = "UPDATE tbl_project_voter pv
                            SET pv.is_migration_failed =  1
                            WHERE pv.elect_id = ? AND pv.pro_id = ? AND pv.pro_voter_id = ? ";

                    $stmt = $srcEm->getConnection()->prepare($sql);
                    $stmt->bindValue(1, $row['elect_id']);
                    $stmt->bindValue(2, $row['pro_id']);
                    $stmt->bindValue(3, $row['pro_voter_id']);
                    $stmt->execute();

                    $srcEm->flush();
                }

                echo $echoText;
                $srcEm->clear();
                $destEm->clear();

                flush();
            }
        });

        $srcEm->clear();
        $destEm->clear();

        return $response;
    }

    /**
     * @Route("/ajax_fill_municipality_precinct_total/{proId}/{electId}",
     *     name="ajax_fill_municipality_precinct_total",
     *    options={"expose" = true}
     * )
     * @Method("GET")
     */

    public function ajaxFillMunicipalityPrecinctTotalAction($proId, $electId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $sql = "SELECT m.municipality_code , m.name, COUNT(DISTINCT precinct_no) AS total_precinct_no
                FROM tbl_project_voter pv , psw_municipality m
                WHERE pv.municipality_no = m.municipality_no
                AND m.province_code = ?
                AND pv.elect_id = ?
                AND pv.pro_id = ?
                AND pv.precinct_no IS NOT NULL
                AND pv.precinct_no <> ''
                GROUP BY pv.municipality_name";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, 53);
        $stmt->bindValue(2, $electId);
        $stmt->bindValue(3, $proId);
        $stmt->execute();

        $municipalities = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($municipalities as $municipality) {
            $sql = "UPDATE psw_municipality SET total_precincts = ? WHERE municipality_code = ? ";
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $municipality['total_precinct_no']);
            $stmt->bindValue(2, $municipality['municipality_code']);
            $stmt->execute();
        }

        return new JsonResponse(['message' => "done"]);
    }

    /**
     * @Route("/ajax_fill_barangay_precinct_total/{proId}/{electId}/{municipalityCode}/{municipalityNo}",
     *     name="ajax_fill_barangay_precinct_total",
     *    options={"expose" = true}
     * )
     * @Method("GET")
     */

    public function ajaxFillBarangayPrecinctTotalAction($proId, $electId, $municipalityCode, $municipalityNo, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $sql = "SELECT * FROM psw_barangay WHERE municipality_code = ? ";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, $municipalityCode);
        $stmt->execute();

        $barangays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($barangays as $barangay) {
            $sql = 'SELECT COUNT(DISTINCT precinct_no) as total_precincts
                    FROM tbl_project_voter
                    WHERE elect_id = ? AND pro_id = ? AND municipality_no = ? AND brgy_no = ? ';

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $electId);
            $stmt->bindValue(2, $proId);
            $stmt->bindValue(3, $municipalityNo);
            $stmt->bindValue(4, $barangay['brgy_no']);
            $stmt->execute();

            $totalPrecincts = $stmt->fetch(\PDO::FETCH_ASSOC)['total_precincts'];

            $sql = "UPDATE psw_barangay SET total_precincts = ? WHERE brgy_code = ? ";
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $totalPrecincts);
            $stmt->bindValue(2, $barangay['brgy_code']);
            $stmt->execute();
        }

        return new JsonResponse(['message' => "done"]);
    }

    /**
     * @Route("/ajax_generate_member_summary/{proId}/{electId}/{municipalityNo}",
     *     name="ajax_generate_member_summary",
     *    options={"expose" = true}
     * )
     * @Method("GET")
     */

    public function ajaxGenerateMemberSummary($proId, $electId, $municipalityNo, Request $request)
    {
        $this->generateMemberSummary($proId, $electId, $municipalityNo);

        return new JsonResponse(['message' => "done"]);
    }

    /**
     * @Route("/ajax_generate_member_summary_all/{proId}/{electId}",
     *     name="ajax_generate_member_summary_all",
     *    options={"expose" = true}
     * )
     * @Method("GET")
     */

    public function ajaxGenerateMemberSummaryAll($proId, $electId, Request $request)
    {
        $em = $this->getDoctrine()->getManager("remote");

        $sql = "SELECT * FROM psw_municipality WHERE province_code = ? AND municipality_no <> 16 ";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, 53);
        $stmt->execute();

        $municipalities = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($municipalities as $municipality) {
            $this->generateMemberSummary($proId, $electId, $municipality['municipality_no']);
        }

        return new JsonResponse(['message' => "done"]);
    }

    private function generateMemberSummary($proId, $electId, $municipalityNo)
    {

        $emRemote = $this->getDoctrine()->getManager("remote");
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $sql = "SELECT * FROM psw_municipality WHERE municipality_code = ? ";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, 53 . $municipalityNo);
        $stmt->execute();

        $municipality = $stmt->fetch(\PDO::FETCH_ASSOC);

        $sql = "SELECT * FROM psw_barangay WHERE municipality_code = ? ";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, 53 . $municipalityNo);
        $stmt->execute();

        $barangays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $currDate = date('Y-m-d');

        $sql = "DELETE FROM tbl_project_member_summary WHERE generated_at = ? AND municipality_no  = ? ";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, $currDate);
        $stmt->bindValue(2, $municipalityNo);
        $stmt->execute();

        foreach ($barangays as $barangay) {
            $sql = 'SELECT COUNT(DISTINCT precinct_no) as total_precincts
                    FROM tbl_project_voter
                    WHERE elect_id = ? AND pro_id = ? AND municipality_no = ? AND brgy_no = ? AND precinct_no IS NOT NULL AND precinct_no <> "" ';

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $electId);
            $stmt->bindValue(2, $proId);
            $stmt->bindValue(3, $municipalityNo);
            $stmt->bindValue(4, $barangay['brgy_no']);
            $stmt->execute();

            $totalPrecincts = $stmt->fetch(\PDO::FETCH_ASSOC)['total_precincts'];

            $sql = 'SELECT count(*) as total_voters
                    FROM tbl_project_voter
                    WHERE elect_id = ? AND pro_id = ? AND municipality_no = ? AND brgy_no = ?  ';

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $electId);
            $stmt->bindValue(2, $proId);
            $stmt->bindValue(3, $municipalityNo);
            $stmt->bindValue(4, $barangay['brgy_no']);
            $stmt->execute();

            $totalVoters = $stmt->fetch(\PDO::FETCH_ASSOC)['total_voters'];

            $sql = 'SELECT
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LGC" THEN 1 END),0) AS total_lgc,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LGO" THEN 1 END),0) AS total_lgo,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LOPP" THEN 1 END),0) AS total_lopp,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP" THEN 1 END),0) AS total_lppp,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP1" THEN 1 END),0) AS total_lppp1,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP2" THEN 1 END),0) AS total_lppp2,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP3" THEN 1 END),0) AS total_lppp3,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "JPM" THEN 1 END),0) AS total_jpm
            FROM tbl_project_voter pv
            WHERE elect_id = ? AND pro_id = ? AND municipality_no = ? AND brgy_no = ? AND has_photo = 1 ';

            $stmt = $emRemote->getConnection()->prepare($sql);
            $stmt->bindValue(1, $electId);
            $stmt->bindValue(2, $proId);
            $stmt->bindValue(3, $municipalityNo);
            $stmt->bindValue(4, $barangay['brgy_no']);
            $stmt->execute();

            $memberSum = $stmt->fetch(\PDO::FETCH_ASSOC);

            $sql = 'SELECT
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LGC" THEN 1 END),0) AS total_lgc,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LGO" THEN 1 END),0) AS total_lgo,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LOPP" THEN 1 END),0) AS total_lopp,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP" THEN 1 END),0) AS total_lppp,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP1" THEN 1 END),0) AS total_lppp1,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP2" THEN 1 END),0) AS total_lppp2,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP3" THEN 1 END),0) AS total_lppp3,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "JPM" THEN 1 END),0) AS total_jpm
            FROM tbl_project_voter pv
            WHERE elect_id = ? AND pro_id = ? AND municipality_no = ? AND brgy_no = ? AND is_non_voter = 1 AND has_photo = 1 ';

            $stmt = $emRemote->getConnection()->prepare($sql);
            $stmt->bindValue(1, $electId);
            $stmt->bindValue(2, $proId);
            $stmt->bindValue(3, $municipalityNo);
            $stmt->bindValue(4, $barangay['brgy_no']);
            $stmt->execute();

            $memberSumNonVoter = $stmt->fetch(\PDO::FETCH_ASSOC);


            $sql = 'SELECT
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LGC" THEN 1 END),0) AS total_lgc,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LGO" THEN 1 END),0) AS total_lgo,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LOPP" THEN 1 END),0) AS total_lopp,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP" THEN 1 END),0) AS total_lppp,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP1" THEN 1 END),0) AS total_lppp1,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP2" THEN 1 END),0) AS total_lppp2,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP3" THEN 1 END),0) AS total_lppp3,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "JPM" THEN 1 END),0) AS total_jpm
            FROM tbl_project_voter pv
            WHERE elect_id = ? AND pro_id = ? AND municipality_no = ? AND brgy_no = ? AND house_form_sub = 1 AND has_photo = 1';

            $stmt = $emRemote->getConnection()->prepare($sql);
            $stmt->bindValue(1, $electId);
            $stmt->bindValue(2, $proId);
            $stmt->bindValue(3, $municipalityNo);
            $stmt->bindValue(4, $barangay['brgy_no']);
            $stmt->execute();

            $memberRecFormSub = $stmt->fetch(\PDO::FETCH_ASSOC);


            $sql = 'SELECT
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LGC" THEN 1 END),0) AS total_lgc,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LGO" THEN 1 END),0) AS total_lgo,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LOPP" THEN 1 END),0) AS total_lopp,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP" THEN 1 END),0) AS total_lppp,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP1" THEN 1 END),0) AS total_lppp1,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP2" THEN 1 END),0) AS total_lppp2,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP3" THEN 1 END),0) AS total_lppp3,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "JPM" THEN 1 END),0) AS total_jpm
            FROM tbl_project_voter pv
            WHERE elect_id = ? AND pro_id = ? AND municipality_no = ? AND brgy_no = ? AND has_photo = 1  AND cellphone IS NOT NULL AND cellphone <> "" ';

            $stmt = $emRemote->getConnection()->prepare($sql);
            $stmt->bindValue(1, $electId);
            $stmt->bindValue(2, $proId);
            $stmt->bindValue(3, $municipalityNo);
            $stmt->bindValue(4, $barangay['brgy_no']);
            $stmt->execute();

            $memberWithCp = $stmt->fetch(\PDO::FETCH_ASSOC);


            $sql = 'SELECT
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LGC" THEN 1 END),0) AS total_lgc,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LGO" THEN 1 END),0) AS total_lgo,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LOPP" THEN 1 END),0) AS total_lopp,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP" THEN 1 END),0) AS total_lppp,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP1" THEN 1 END),0) AS total_lppp1,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP2" THEN 1 END),0) AS total_lppp2,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "LPPP3" THEN 1 END),0) AS total_lppp3,
            COALESCE(COUNT(CASE WHEN pv.voter_group = "JPM" THEN 1 END),0) AS total_jpm
            FROM tbl_project_voter pv
            WHERE elect_id = ? AND pro_id = ? AND municipality_no = ? AND brgy_no = ? AND has_photo = 1 AND (has_id = 0 or has_id IS NULL OR has_id = "" ) ';

            $stmt = $emRemote->getConnection()->prepare($sql);
            $stmt->bindValue(1, $electId);
            $stmt->bindValue(2, $proId);
            $stmt->bindValue(3, $municipalityNo);
            $stmt->bindValue(4, $barangay['brgy_no']);
            $stmt->execute();

            $memberForPrint = $stmt->fetch(\PDO::FETCH_ASSOC);

            $sql = "INSERT INTO tbl_project_member_summary(
                generated_at,
                municipality_no,
                municipality_name,
                barangay_no,
                barangay_name,
                total_precincts,
                total_voters,
                total_lgc,
                total_lgo,
                total_lopp,
                total_lppp,
                total_lppp1,
                total_lppp2,
                total_lppp3,
                total_jpm,

                total_lgc_non_voter,
                total_lgo_non_voter,
                total_lopp_non_voter,
                total_lppp_non_voter,
                total_lppp1_non_voter,
                total_lppp2_non_voter,
                total_lppp3_non_voter,
                total_jpm_non_voter,

                total_lgc_rec_sub,
                total_lgo_rec_sub,
                total_lopp_rec_sub,
                total_lppp_rec_sub,
                total_lppp1_rec_sub,
                total_lppp2_rec_sub,
                total_lppp3_rec_sub,
                total_jpm_rec_sub,

                total_lgc_with_cp,
                total_lgo_with_cp,
                total_lopp_with_cp,
                total_lppp_with_cp,
                total_lppp1_with_cp,
                total_lppp2_with_cp,
                total_lppp3_with_cp,
                total_jpm_with_cp,

                total_lppp_for_print,
                total_lppp1_for_print,
                total_lppp2_for_print,
                total_lppp3_for_print,
                total_jpm_for_print,

                created_at,
                created_by,
                status
                )VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $currDate);
            $stmt->bindValue(2, $municipality['municipality_no']);
            $stmt->bindValue(3, $municipality['name']);
            $stmt->bindValue(4, $barangay['brgy_no']);
            $stmt->bindValue(5, $barangay['name']);
            $stmt->bindValue(6, $totalPrecincts);
            $stmt->bindValue(7, $totalVoters);
            $stmt->bindValue(8, $memberSum['total_lgc']);
            $stmt->bindValue(9, $memberSum['total_lgo']);
            $stmt->bindValue(10, $memberSum['total_lopp']);
            $stmt->bindValue(11, $memberSum['total_lppp']);
            $stmt->bindValue(12, $memberSum['total_lppp1']);
            $stmt->bindValue(13, $memberSum['total_lppp2']);
            $stmt->bindValue(14, $memberSum['total_lppp3']);
            $stmt->bindValue(15, $memberSum['total_jpm']);
            $stmt->bindValue(16, $memberSumNonVoter['total_lgc']); //non-voter
            $stmt->bindValue(17, $memberSumNonVoter['total_lgo']);
            $stmt->bindValue(18, $memberSumNonVoter['total_lopp']);
            $stmt->bindValue(19, $memberSumNonVoter['total_lppp']);
            $stmt->bindValue(20, $memberSumNonVoter['total_lppp1']);
            $stmt->bindValue(21, $memberSumNonVoter['total_lppp2']);
            $stmt->bindValue(22, $memberSumNonVoter['total_lppp3']);
            $stmt->bindValue(23, $memberSumNonVoter['total_jpm']);
            $stmt->bindValue(24, $memberRecFormSub['total_lgc']);
            $stmt->bindValue(25, $memberRecFormSub['total_lgo']);
            $stmt->bindValue(26, $memberRecFormSub['total_lopp']);
            $stmt->bindValue(27, $memberRecFormSub['total_lppp']);
            $stmt->bindValue(28, $memberRecFormSub['total_lppp1']);
            $stmt->bindValue(29, $memberRecFormSub['total_lppp2']);
            $stmt->bindValue(30, $memberRecFormSub['total_lppp3']);
            $stmt->bindValue(31, $memberRecFormSub['total_jpm']);
            $stmt->bindValue(32, $memberWithCp['total_lgc']);
            $stmt->bindValue(33, $memberWithCp['total_lgo']);
            $stmt->bindValue(34, $memberWithCp['total_lopp']);
            $stmt->bindValue(35, $memberWithCp['total_lppp']);
            $stmt->bindValue(36, $memberWithCp['total_lppp1']);
            $stmt->bindValue(37, $memberWithCp['total_lppp2']);
            $stmt->bindValue(38, $memberWithCp['total_lppp3']);
            $stmt->bindValue(39, $memberWithCp['total_jpm']);

            $stmt->bindValue(40, $memberForPrint['total_lppp']);
            $stmt->bindValue(41, $memberForPrint['total_lppp1']);
            $stmt->bindValue(42, $memberForPrint['total_lppp2']);
            $stmt->bindValue(43, $memberForPrint['total_lppp3']);
            $stmt->bindValue(44, $memberForPrint['total_jpm']);

            $stmt->bindValue(45, date('Y-m-d H:i:s'));
            $stmt->bindValue(46, $user->getUsername());
            $stmt->bindValue(47, 'A');
            $stmt->execute();
        }

        return true;
    }

    /**
     * @Route("/ajax_archieve_failed_migrate/{electId}/{proId}/{municipalityNo}",
     *       name="ajax_archieve_failed_migrate",
     *       options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxArchieveFailedMigrate($electId, $proId, $municipalityNo)
    {
        $em = $this->getDoctrine()->getManager();

        $response = new StreamedResponse();
        $response->headers->set("Cache-Control", "no-cache, must-revalidate");
        $response->headers->set("X-Accel-Buffering", "no");
        $response->setStatusCode(200);

        $response->setCallback(function () use ($em, $electId, $proId, $municipalityNo) {

            $sql = "SELECT pv.* FROM tbl_project_voter pv WHERE pv.elect_id = ? AND pv.pro_id = ? AND pv.municipality_no = ?  AND is_migration_failed = 1  ORDER BY pv.voter_name ASC";

            echo "Query : " . $sql;
            echo "<br/><br/>";
            echo "<br/><br/>";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $electId);
            $stmt->bindValue(2, $proId);
            $stmt->bindValue(3, $municipalityNo);
            $stmt->execute();

            $voters = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $counter = 0;

            foreach ($voters as $row) {
                $counter++;

                $sql = "INSERT INTO tbl_project_voter_archieve(
                    pro_voter_id,
                    pro_id,
                    pro_id_code,
                    elect_id,
                    brgy_no,
                    precinct_no,
                    province_code,
                    municipality_no,
                    address,
                    voter_id,
                    voter_no,
                    voter_name,
                    voter_group,
                    cellphone,
                    birthdate,
                    has_id,
                    has_photo,
                    photo_at,
                    voted_2016,
                    created_at,
                    created_by,
                    updated_at,
                    updated_by,
                    remarks,
                    status,
                    barangay_name,
                    municipality_name,
                    is_jpm,
                    purok,
                    is_others,
                    is_bisaya,
                    is_cuyonon,
                    is_tagalog,
                    is_ilonggo,
                    is_catholic,
                    is_inc,
                    is_islam,
                    others_specify,
                    firstname,
                    middlename,
                    lastname,
                    ext_name,
                    gender,
                    civil_status,
                    bloodtype,
                    occupation,
                    religion,
                    dialect,
                    ip_group,
                    is_non_voter,
                    generated_id_no,
                    date_generated,
                    did_changed,
                    is_migrated,
                    is_transfered,
                    to_migrate,
                    is_migration_failed,
                    position
                )VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                $stmt = $em->getConnection()->prepare($sql);
                $stmt->bindValue(1, $row['pro_voter_id']);
                $stmt->bindValue(2, $row['pro_id']);
                $stmt->bindValue(3, $row['pro_id_code']);
                $stmt->bindValue(4, $row['elect_id']);
                $stmt->bindValue(5, $row['brgy_no']);
                $stmt->bindValue(6, $row['precinct_no']);
                $stmt->bindValue(7, $row['province_code']);
                $stmt->bindValue(8, $row['municipality_no']);
                $stmt->bindValue(9, $row['address']);
                $stmt->bindValue(10, $row['voter_id']);
                $stmt->bindValue(11, $row['voter_no']);
                $stmt->bindValue(12, $row['voter_name']);
                $stmt->bindValue(13, $row['voter_group']);
                $stmt->bindValue(14, $row['cellphone']);
                $stmt->bindValue(15, $row['birthdate']);
                $stmt->bindValue(16, $row['has_id']);
                $stmt->bindValue(17, $row['has_photo']);
                $stmt->bindValue(18, $row['photo_at']);
                $stmt->bindValue(19, $row['voted_2016']);
                $stmt->bindValue(20, $row['created_at']);
                $stmt->bindValue(21, $row['created_by']);
                $stmt->bindValue(22, $row['updated_at']);
                $stmt->bindValue(23, $row['updated_by']);
                $stmt->bindValue(24, $row['remarks']);
                $stmt->bindValue(25, $row['status']);
                $stmt->bindValue(26, $row['barangay_name']);
                $stmt->bindValue(27, $row['municipality_name']);
                $stmt->bindValue(28, $row['is_jpm']);
                $stmt->bindValue(29, $row['purok']);
                $stmt->bindValue(30, $row['is_others']);
                $stmt->bindValue(31, $row['is_bisaya']);
                $stmt->bindValue(32, $row['is_cuyonon']);
                $stmt->bindValue(33, $row['is_tagalog']);
                $stmt->bindValue(34, $row['is_ilonggo']);
                $stmt->bindValue(35, $row['is_catholic']);
                $stmt->bindValue(36, $row['is_inc']);
                $stmt->bindValue(37, $row['is_islam']);
                $stmt->bindValue(38, $row['others_specify']);
                $stmt->bindValue(39, $row['firstname']);
                $stmt->bindValue(40, $row['middlename']);
                $stmt->bindValue(41, $row['lastname']);
                $stmt->bindValue(42, $row['ext_name']);
                $stmt->bindValue(43, $row['gender']);
                $stmt->bindValue(44, $row['civil_status']);
                $stmt->bindValue(45, $row['bloodtype']);
                $stmt->bindValue(46, $row['occupation']);
                $stmt->bindValue(47, $row['religion']);
                $stmt->bindValue(48, $row['dialect']);
                $stmt->bindValue(49, $row['ip_group']);
                $stmt->bindValue(50, $row['is_non_voter']);
                $stmt->bindValue(51, $row['generated_id_no']);
                $stmt->bindValue(52, $row['date_generated']);
                $stmt->bindValue(53, $row['did_changed']);
                $stmt->bindValue(54, $row['is_migrated']);
                $stmt->bindValue(55, $row['is_transfered']);
                $stmt->bindValue(56, $row['to_migrate']);
                $stmt->bindValue(57, $row['is_migration_failed']);
                $stmt->bindValue(58, $row['position']);
                $stmt->execute();

                echo $counter . '. Voter name : ' . $row['voter_name'];

                flush();
            }
        });

        $em->clear();

        return $response;
    }

    /**
     * @Route("/ajax_sms_update_sender_name/{proId}",
     *       name="ajax_sms_update_sender_name",
     *       options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxSmsUpdateSenderName($proId)
    {
        $em = $this->getDoctrine()->getManager();

        $sql = "SELECT * FROM tbl_received_sms WHERE IsProcessed <> 1 ";
        $stmt = $em->getConnection()->query($sql);

        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($messages as $message) {

            $sql = "SELECT * FROM tbl_project_voter pv WHERE pv.cellphone = ? AND pv.pro_id = ? ";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, str_replace('+63', '0', $message['MessageFrom']));
            $stmt->bindValue(2, 3);
            $stmt->execute();

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row) {
                $sql = "UPDATE tbl_received_sms SET VoterName = ? , ProVoterId = ?, Municipality = ? , Barangay = ? , IsProcessed = 1 WHERE Id = ? ";
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->bindValue(1, $row['voter_name']);
                $stmt->bindValue(2, $row['pro_voter_id']);
                $stmt->bindValue(3, $row['municipality_name']);
                $stmt->bindValue(4, $row['barangay_name']);
                $stmt->bindValue(5, $message['Id']);
                $stmt->execute();
            } else {
                $sql = "UPDATE tbl_received_sms SET IsProcessed = 1 WHERE Id = ? ";
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->bindValue(1, $message['Id']);
                $stmt->execute();
            }
        }

        return new JsonResponse('done', 200);
    }

    /**
     * @Route("/ajax_sync_household_submission/{proId}",
     *       name="ajax_sync_household_submission",
     *       options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxSyncHouseholdSubmission($proId)
    {
        $em = $this->getDoctrine()->getManager();

        $sql = "SELECT * FROM tbl_household_hdr ";
        $stmt = $em->getConnection()->query($sql);

        $data = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        foreach ($data as $row) {
            $sql = "UPDATE tbl_project_voter pv SET pv.house_form_encoded = 1 WHERE pv.pro_id_code = ? AND pv.house_form_encoded <> 1 ";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $row['pro_id_code']);
            $stmt->execute();
        }

        return new JsonResponse('done', 200);
    }

    /**
     * @Route("/ajax_sync_recruitment_submission/{proId}",
     *       name="ajax_sync_recruitment_submission",
     *       options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxSyncRecruitmentSubmission($proId)
    {
        $em = $this->getDoctrine()->getManager();

        $sql = "SELECT * FROM tbl_recruitment_hdr ";
        $stmt = $em->getConnection()->query($sql);

        $data = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        foreach ($data as $row) {
            $sql = "UPDATE tbl_project_voter pv SET pv.rec_form_encoded = 1 WHERE pv.pro_id_code = ? AND pv.rec_form_encoded <> 1 ";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $row['pro_id_code']);
            $stmt->execute();
        }

        return new JsonResponse('done', 200);
    }


    /**
     * @Route("/update_araceli_voter_no",
     *       name="update_araceli_voter_no",
     *       options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function updateAraceliVoterNo()
    {
        $em = $this->getDoctrine()->getManager();

        $response = new StreamedResponse();
        $response->headers->set("Cache-Control", "no-cache, must-revalidate");
        $response->headers->set("X-Accel-Buffering", "no");
        $response->setStatusCode(200);

        $response->setCallback(function () use ($em) {

            $sql = "SELECT pv.* FROM tbl_project_voter pv WHERE pv.elect_id = ? AND pv.pro_id = ? AND pv.municipality_name = ? and pv.voter_no IS NULL ORDER BY pv.voter_name ASC LIMIT 2000";

            echo "Query : " . $sql;
            echo "<br/><br/>";
            echo "<br/><br/>";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, 4);
            $stmt->bindValue(2, 3);
            $stmt->bindValue(3, 'LINAPACAN');
            $stmt->execute();

            $voters = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $counter = 0;

            foreach ($voters as $row) {
                $counter++;

                $sql = "SELECT * FROM tbl_project_voter_araceli pv WHERE pv.municipality_name = ? AND pv.voter_name = ? AND pv.precinct_no = ? ";
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->bindValue(1, $row['municipality_name']);
                $stmt->bindValue(2, $row['voter_name']);
                $stmt->bindValue(3, $row['precinct_no']);
                $stmt->execute();

                $sourceRecord = $stmt->fetch(\PDO::FETCH_ASSOC);
                $recordFound = $sourceRecord == null ? false : true;

                if ($recordFound) {
                    $sql = "UPDATE tbl_project_voter pv
                            set pv.voter_no = ? 
                            WHERE pv.elect_id = ? AND pv.pro_id = ? AND pv.pro_voter_id = ? ";

                    $stmt = $em->getConnection()->prepare($sql);
                    $stmt->bindValue(1, $sourceRecord['voter_no']);
                    $stmt->bindValue(2, $row['elect_id']);
                    $stmt->bindValue(3, $row['pro_id']);
                    $stmt->bindValue(4, $row['pro_voter_id']);
                    $stmt->execute();
                }

                echo $counter . '. Voter name : ' . $row['voter_name'] . ' Has found : ' . ($recordFound ? "YES" : "NO") . '<br/>';

                flush();
            }
        });

        $em->clear();

        return $response;
    }

    /**
     * @Route("/ajax_compare_member/{municipalityNo}",
     *       name="ajax_compare_member",
     *       options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxCompareMember($municipalityNo)
    {
        $em = $this->getDoctrine()->getManager();
        $electPrep = $this->getDoctrine()->getManager("electPrep2024");

        $response = new StreamedResponse();
        $response->headers->set("Cache-Control", "no-cache, must-revalidate");
        $response->headers->set("X-Accel-Buffering", "no");
        $response->setStatusCode(200);

        $response->setCallback(function () use ($em, $electPrep, $municipalityNo) {

            $sql = "SELECT pv.* FROM tbl_project_voter pv WHERE pv.elect_id = 4 AND pv.pro_id = 3 AND pv.municipality_no = ? AND to_process = 1 AND is_processed <> 1 ORDER BY pv.voter_name ASC LIMIT 2000";

            echo "Query : " . $sql;
            echo "<br/><br/>";
            echo "<br/><br/>";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $municipalityNo);
            $stmt->execute();

            $voters = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $counter = 0;

            foreach ($voters as $row) {
                $counter++;

                $sql = "SELECT * FROM tbl_project_voter pv WHERE pv.pro_id = 3 AND pv.elect_id = 423 AND pv.municipality_no = ? AND pv.voter_name = ? AND pv.precinct_no = ? ";
                $stmt = $electPrep->getConnection()->prepare($sql);
                $stmt->bindValue(1, $row['municipality_no']);
                $stmt->bindValue(2, $row['voter_name']);
                $stmt->bindValue(3, $row['precinct_no']);
                $stmt->execute();

                $destRecord = $stmt->fetch(\PDO::FETCH_ASSOC);
                $recordFound = $destRecord == null ? false : true;

                if ($recordFound) {
                    $sql = "UPDATE tbl_project_voter pv
                             SET pv.is_processed = 1, is_found = 1
                             WHERE pv.elect_id = 4 AND pv.pro_id = 3 AND pv.pro_voter_id = ? ";

                    $stmt = $em->getConnection()->prepare($sql);

                    $stmt->bindValue(1, $row['pro_voter_id']);
                    $stmt->execute();

                    $sql = "UPDATE tbl_project_voter pv
                             SET pv.is_jpm =  1,
                              pro_id_code = ? ,
                              has_id = ? , 
                              has_photo = ? , 
                              photo_at = ? , 
                              is_non_voter = ? , 
                              generated_id_no = ? , 
                              date_generated = ? , 
                              voter_group = ? , 
                              is_kalaban = ?,
                              cellphone = ? ,
                              birthdate = ?
                             WHERE pv.pro_voter_id = ? AND elect_id = 423 ";

                    $stmt = $electPrep->getConnection()->prepare($sql);

                    $stmt->bindValue(1, $row['pro_id_code']);
                    $stmt->bindValue(2, $row['has_id']);
                    $stmt->bindValue(3, $row['has_photo']);
                    $stmt->bindValue(4, $row['photo_at']);
                    $stmt->bindValue(5, $row['is_non_voter']);
                    $stmt->bindValue(6, $row['generated_id_no']);
                    $stmt->bindValue(7, $row['date_generated']);
                    $stmt->bindValue(8, $row['voter_group']);
                    $stmt->bindValue(9, $row['is_kalaban']);
                    $stmt->bindValue(10, $row['cellphone']);
                    $stmt->bindValue(11, $row['birthdate']);
                    $stmt->bindValue(12, $destRecord['pro_voter_id']);

                    $stmt->execute();

                } else {

                    $sql = "UPDATE tbl_project_voter pv
                             SET pv.is_processed =  1, pv.is_not_found = 1
                             WHERE pv.elect_id = 4 AND pv.pro_id = 3 AND pv.pro_voter_id = ? ";

                    $stmt = $em->getConnection()->prepare($sql);
                    $stmt->bindValue(1, $row['pro_voter_id']);
                    $stmt->execute();
                }

                echo $counter . '. Voter name : ' . $row['voter_name'] . ' Has found : ' . ($recordFound ? "YES" : "NO") . '<br/>';

                flush();
            }
        });

        $em->clear();

        return $response;
    }

    /**
     * @Route("/ajax_fix_narra_poblacion",
     *     name="ajax_fix_narra_poblacion",
     *    options={"expose" = true}
     * )
     * @Method("GET")
     */

    public function ajaxFixNarraction()
    {
        $em = $this->getDoctrine()->getManager("electPrep2024");
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $sql = "SELECT voter_name, count(*) as total_count from tbl_project_voter PV 
                where elect_id = 423 and municipality_name = 'NARRA' AND brgy_no = '014'
                GROUP BY voter_name  
                HAVING total_count > 1";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        while ($voter = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $projectVoters = $em->getRepository("AppBundle:ProjectVoter")
                ->findBy(['electId' => 423, 'voterName' => $voter['voter_name'], 'municipalityName' => 'NARRA', 'brgyNo' => '014']);

            if (count($projectVoters) > 1) {

                foreach ($projectVoters as $projectVoter) {
                    if ($projectVoter->getHasNewPhoto() == 1 && $projectVoter->getCroppedPhoto() == 1) {
                        // do nothing
                    } else {
                        // remove project voter
                        $projectVoter->setToRemove(1);
                        $em->flush();
                    }
                }
            }
        }

        $em->clear();

        return new JsonResponse(['message' => 'done']);
    }


    /**
     * @Route("/ajax_copy_election_result/{municipalityCode}/{municipalityName}",
     *     name="ajax_copy_election_result",
     *    options={"expose" = true}
     * )
     * @Method("GET")
     */

     public function ajaxCopyElectionResult($municipalityCode,$municipalityName)
     {
         $em = $this->getDoctrine()->getManager("electPrep2024");
         $electResultEm = $this->getDoctrine()->getManager("tupad");

         $user = $this->get('security.token_storage')->getToken()->getUser();
 
         $sql = "SELECT * FROM psw_barangay where municipality_code = ? ORDER BY name ASC ";
 
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->bindValue(1, $municipalityCode);
         $stmt->execute();
 
         $barangays = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
         foreach($barangays as $barangay){

            $sql = "SELECT municipality_name,barangay_name,candidate_position,candidate_name,SUM(total_turnout) as b_turnout,SUM(reg_voters) AS b_reg_voters,SUM(total_votes) AS b_total_votes
                    FROM tbl_clustered_precinct_result 
                    WHERE candidate_position IN ('GOVERNOR','VICE GOVERNOR','CONGRESSMAN') AND municipality_name = ? AND barangay_name = ? 
                    GROUP BY municipality_name,barangay_name,candidate_position,candidate_name";

            $stmt = $electResultEm->getConnection()->prepare($sql);
            $stmt->bindValue(1, $municipalityName);
            $stmt->bindValue(2, $barangay['name']);
            $stmt->execute();

            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sql = "DELETE FROM tbl_election_result_2022 where municipality_name = ? AND barangay_name = ? ";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue(1, $municipalityName);
            $stmt->bindValue(2, $barangay['name']);
            $stmt->execute();

            foreach($data as $result){
                
                $sql ="INSERT INTO tbl_election_result_2022(municipality_name,barangay_name,candidate_name,candidate_position,total_turnout,reg_voters,total_votes)VALUES(?,?,?,?,?,?,?)";
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->bindValue(1, $result['municipality_name']);
                $stmt->bindValue(2, $result['barangay_name']);
                $stmt->bindValue(3, $result['candidate_name']);
                $stmt->bindValue(4, $result['candidate_position']);
                $stmt->bindValue(5, $result['b_turnout']);
                $stmt->bindValue(6, $result['b_reg_voters']);
                $stmt->bindValue(7, $result['b_total_votes']);
                $stmt->execute();
            }
         }

         $em->clear();
 
         return new JsonResponse(['message' => 'done']);
     }


       /**
     * @Route("/ajax_tag_cullion2025",
     *       name="ajax_tag_cullion2025",
     *       options={ "expose" = true }
     * )
     * @Method("GET")
     */

    public function ajaxTagCullion2025()
    {
        $em = $this->getDoctrine()->getManager("electPrep2024");

        $response = new StreamedResponse();
        $response->headers->set("Cache-Control", "no-cache, must-revalidate");
        $response->headers->set("X-Accel-Buffering", "no");
        $response->setStatusCode(200);

        $response->setCallback(function () use ($em) {

            $sql = "SELECT pv.* FROM tbl_project_voter pv WHERE  pv.elect_id = 423 AND pv.pro_id = 3 AND pv.municipality_name ='CULION' AND has_photo = 1 AND (has_new_photo is null or has_new_photo = 0 ) ORDER BY pv.voter_name ASC LIMIT 2000";

            echo "Query : " . $sql;
            echo "<br/><br/>";
            echo "<br/><br/>";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();

            $voters = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $counter = 0;

            foreach ($voters as $row) {
                $counter++;

                $sql = "SELECT * FROM tbl_project_voter_temp pv WHERE pv.voter_name = ? AND pv.com_tag = 'O' ";
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->bindValue(1, $row['voter_name']);
                $stmt->execute();

                $destRecord = $stmt->fetch(\PDO::FETCH_ASSOC);
                $recordFound = $destRecord == null ? false : true;

                if ($recordFound) {
                    $sql = "UPDATE tbl_project_voter pv
                             SET  pv.cul_member = 1
                             WHERE pv.elect_id = 423 AND pv.pro_id = 3 AND pv.pro_voter_id = ? ";

                    $stmt = $em->getConnection()->prepare($sql);

                    $stmt->bindValue(1, $row['pro_voter_id']);
                    $stmt->execute();
                }

                echo $counter . '. Voter name : ' . $row['voter_name'] . ' Has found : ' . ($recordFound ? "YES" : "NO") . '<br/>';

                flush();
            }
        });

        $em->clear();

        return $response;
    }
     /**
     * @Route("/ajax_fill_voters_2025/{municipalityNo}",
     *       name="ajax_fill_voters_2025",
     *       options={ "expose" = true }
     * )
     * @Method("GET")
     */

     public function ajaxFillVoters2025($municipalityNo)
     {
         $em = $this->getDoctrine()->getManager();
         $electPrep = $this->getDoctrine()->getManager("electPrep2024");
 
         $response = new StreamedResponse();
         $response->headers->set("Cache-Control", "no-cache, must-revalidate");
         $response->headers->set("X-Accel-Buffering", "no");
         $response->setStatusCode(200);
 
         $response->setCallback(function () use ($em, $electPrep, $municipalityNo) {
 
             $sql = "SELECT pv.* FROM tbl_project_voter pv WHERE  pv.elect_id = 423 AND pv.pro_id = 3 AND pv.municipality_no = ? AND to_process = 1 AND is_processed <> 1 ORDER BY pv.voter_name ASC LIMIT 2000";
 
             echo "Query : " . $sql;
             echo "<br/><br/>";
             echo "<br/><br/>";
 
             $stmt = $em->getConnection()->prepare($sql);
             $stmt->bindValue(1, $municipalityNo);
             $stmt->execute();
 
             $voters = $stmt->fetchAll(\PDO::FETCH_ASSOC);
             $counter = 0;
 
             foreach ($voters as $row) {
                 $counter++;
 
                 $sql = "SELECT * FROM tbl_project_voter pv WHERE pv.pro_id = 3 AND pv.elect_id = 25 AND pv.municipality_no = ? AND pv.voter_name = ? ";
                 $stmt = $electPrep->getConnection()->prepare($sql);
                 $stmt->bindValue(1, $row['municipality_no']);
                 $stmt->bindValue(2, $row['voter_name']);
                 $stmt->execute();
 
                 $destRecord = $stmt->fetch(\PDO::FETCH_ASSOC);
                 $recordFound = $destRecord == null ? false : true;
 
                 if ($recordFound) {
                     $sql = "UPDATE tbl_project_voter pv
                              SET pv.is_processed = 1, is_found = 1
                              WHERE pv.elect_id = 423 AND pv.pro_id = 3 AND pv.pro_voter_id = ? ";
 
                     $stmt = $em->getConnection()->prepare($sql);
 
                     $stmt->bindValue(1, $row['pro_voter_id']);
                     $stmt->execute();
 
                     $sql = "UPDATE tbl_project_voter pv
                              SET pv.is_jpm =  1,
                               pro_id_code = ? ,
                               has_id = ? , 
                               has_photo = ? , 
                               photo_at = ? , 
                               generated_id_no = ? , 
                               date_generated = ? , 
                               voter_group = ? , 
                               is_kalaban = ?,
                               cellphone = ? ,
                               birthdate = ?,
                               has_claimed = ?,
                               claimed_at = ?,
                               old_voter_group = ?,
                               has_new_photo = ?,
                               has_new_id = ? ,
                               updated_at = ?,
                               cropped_photo = ?,
                               has_attended = ?,
                               position = ?  
                              WHERE pv.pro_voter_id = ? AND elect_id = 25 ";
 
                     $stmt = $electPrep->getConnection()->prepare($sql);
 
                     $stmt->bindValue(1, $row['pro_id_code']);
                     $stmt->bindValue(2, $row['has_id']);
                     $stmt->bindValue(3, $row['has_photo']);
                     $stmt->bindValue(4, $row['photo_at']);
                     $stmt->bindValue(5, $row['generated_id_no']);
                     $stmt->bindValue(6, $row['date_generated']);
                     $stmt->bindValue(7, $row['voter_group']);
                     $stmt->bindValue(8, $row['is_kalaban']);
                     $stmt->bindValue(9, $row['cellphone']);
                     $stmt->bindValue(10, $row['birthdate']);
                     $stmt->bindValue(11, $row['has_claimed']);
                     $stmt->bindValue(12, $row['claimed_at']);
                     $stmt->bindValue(13, $row['old_voter_group']);
                     $stmt->bindValue(14, $row['has_new_photo']);
                     $stmt->bindValue(15, $row['has_new_id']);
                     $stmt->bindValue(16, $row['updated_at']);
                     $stmt->bindValue(17, $row['cropped_photo']);
                     $stmt->bindValue(18, $row['has_attended']);
                     $stmt->bindValue(19, $row['position']);
                     $stmt->bindValue(20, $destRecord['pro_voter_id']);
 
                     $stmt->execute();
                     
                     // $sql = "UPDATE tbl_project_voter pv
                     //          SET pv.is_jpm =  1,
                     //           pro_id_code = ? ,
                     //           has_id = ? , 
                     //           has_photo = ? , 
                     //           photo_at = ? , 
                     //           generated_id_no = ? , 
                     //           date_generated = ? , 
                     //           voter_group = ? , 
                     //           is_kalaban = ?,
                     //           cellphone = ? ,
                     //           birthdate = ?,
                     //           has_claimed = ?,
                     //           claimed_at = ?,
                     //           old_voter_group = ?,
                     //           has_new_photo = ?,
                     //           updated_at = ?,
                     //           has_attended = ?,
                     //           position = ?  
                     //          WHERE pv.pro_voter_id = ? AND elect_id = 25 ";
 
                     // $stmt = $electPrep->getConnection()->prepare($sql);
 
                     // $stmt->bindValue(1, $row['pro_id_code']);
                     // $stmt->bindValue(2, $row['has_id']);
                     // $stmt->bindValue(3, $row['has_photo']);
                     // $stmt->bindValue(4, $row['photo_at']);
                     // $stmt->bindValue(5, $row['generated_id_no']);
                     // $stmt->bindValue(6, $row['date_generated']);
                     // $stmt->bindValue(7, $row['voter_group']);
                     // $stmt->bindValue(8, $row['is_kalaban']);
                     // $stmt->bindValue(9, $row['cellphone']);
                     // $stmt->bindValue(10, $row['birthdate']);
                     // $stmt->bindValue(11, $row['has_claimed']);
                     // $stmt->bindValue(12, $row['claimed_at']);
                     // $stmt->bindValue(13, $row['old_voter_group']);
                     // $stmt->bindValue(14, $row['has_new_photo']);
                     // $stmt->bindValue(15, $row['updated_at']);
                     // $stmt->bindValue(16, $row['has_attended']);
                     // $stmt->bindValue(17, $row['position']);
                     // $stmt->bindValue(18, $destRecord['pro_voter_id']);
 
                     // $stmt->execute();
 
                 } else {
 
                     $sql = "UPDATE tbl_project_voter pv
                              SET pv.is_processed =  1, pv.is_not_found = 1
                              WHERE pv.elect_id = 423 AND pv.pro_id = 3 AND pv.pro_voter_id = ? ";
 
                     $stmt = $em->getConnection()->prepare($sql);
                     $stmt->bindValue(1, $row['pro_voter_id']);
                     $stmt->execute();
                 }
 
                 echo $counter . '. Voter name : ' . $row['voter_name'] . ' Has found : ' . ($recordFound ? "YES" : "NO") . '<br/>';
 
                 flush();
             }
         });
 
         $em->clear();
 
         return $response;
     }
}
