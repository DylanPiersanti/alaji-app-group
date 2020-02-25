<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Candidate;
use App\Entity\Result;
use App\Api\MoodleApi;



class CandidateController extends AbstractController
{

     /**
     * @Route("/quiz/candidates", name="candidates")
     */

    public function candidat()
    {

       $user = $this->getUser();

       return $this->render('candidate/candidates.html.twig', [
           'teacher' => $user,
       ]);
    }


    /**
     * @Route("/quiz/candidates/{id}", name="candidate_profil")
     */
    public function index(int $id, MoodleApi $moodleApi, Request $request)
    {

        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->find($id);

        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('token');
            if ($this->isCsrfTokenValid('addCriteria', $submittedToken)) {
                $oral1 = intval($_POST['criteria1']);
                $oral2 = intval($_POST['criteria2']);
                $oral3 = intval($_POST['criteria3']);
                $oral4 = intval($_POST['criteria4']);

                $results = $this->getDoctrine()->getRepository(Result::class)->findOneBy(['candidate' => $candidate, 'criteria' => 1]);
                $results->setOral($oral1);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($results);


                $results = $this->getDoctrine()->getRepository(Result::class)->findOneBy(['candidate' => $candidate, 'criteria' => 2]);
                $results->setOral($oral2);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($results);


                $results = $this->getDoctrine()->getRepository(Result::class)->findOneBy(['candidate' => $candidate, 'criteria' => 3]);
                $results->setOral($oral3);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($results);


                $results = $this->getDoctrine()->getRepository(Result::class)->findOneBy(['candidate' => $candidate, 'criteria' => 4]);
                $results->setOral($oral4);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($results);

                $manager->flush();

            }
            $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);
            foreach ($results as $result) {

                $oral = $result->getOral();
                $test = $result->getTest();
                $coeftest = $result->getCoeftest();
                $coefOral = $result->getCoeforal();
                $average  = $moodleApi->averageResult($test, $coeftest, $oral, $coefOral);

                $acquis = $moodleApi->acquis($average);


                $result->setAverage($average);
                $result->setAcquis($acquis);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($result);
                $manager->flush();

            }
        }

        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);

        return $this->render('candidate/index.html.twig', [
            'candidate' => $candidate,
            'results' => $results
        ]);
    }

    /**
     * @Route("quiz/candidates/{id}/addoral", name="student_form")
     */
    public function getFormCandidate(int $id, Request $request)
    {
        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->find($id);
        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);

        return $this->render('candidate/addoral.html.twig', [
            'candidate' => $candidate,
            'results' => $results
        ]);
    }

    /**
     * @Route("/quiz/candidates/{id}/summary", name="summary_candidate")
     */
    public function getSummary(int $id , Request $request)
    {

        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->find($id);
        $name = $candidate->getFullname();
        $strippedName = str_replace(' ', '', $name);

        //https://ourcodeworld.com/articles/read/799/how-to-create-a-pdf-from-html-in-symfony-4-using-dompdf Pour le pdf
        if ($request->isMethod('POST')) {
            //Configure Dompdf according to your needs
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('candidate/pdf.html.twig', [
                'candidate' => $candidate
            ]);
            // Load HTML to Dompdf
            $dompdf->loadHtml($html);
            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');
            // Render the HTML as PDF
            $dompdf->render();
            // Output the generated PDF to Browser (force download)
            $dompdf->stream("Récapitulatif_$strippedName.pdf", [
                "Attachment" => true
            ]);
        }
        return $this->render('candidate/summary.html.twig', [
            'candidate' => $candidate,
        ]);
    }

    /**
     * @Route("/quiz/candidates/{id}/summary/pdf", name="pdf_summary_candidate")
     */
    public function getSummaryPdf(int $id, Request $request)
    {
        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->find($id);
        return $this->render('candidate/pdf.html.twig', [
            'candidate' => $candidate,
        ]);
    }

}
