<?php

namespace App\Controller;

use App\Api\MoodleApi;
use App\Entity\Candidate;
use App\Entity\Criteria;
use App\Entity\Quiz;
use App\Entity\Result;
use App\Entity\User;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class MoodleApiController extends AbstractController
{
    /**
     * @Route("/quiz", name="quiz")
     */
    public function postQuiz(MoodleApi $moodleApi)
    {

        $user = $this->getUser();
        $userEmail = $user->getEmail();

        $teacher = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $userEmail]);

        $idTeacher = $teacher->getMoodleId();

        $courses = $moodleApi->getCourses($idTeacher);

        $idCourses = $courses['groups'][0]['courseid'];

        $quizzess = $moodleApi->getQuiz($idCourses);
        if (count($quizzess['quizzes']) > 1) {
            foreach ($quizzess['quizzes'] as $quizzes) {

                $nameQuiz = $quizzes[0]['name'];
                $idQuiz = $quizzes[0]['id'];

                $quizDb = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['moodleId' => $idQuiz]);
                if (!$quizDb) {
                    $quizDb = new Quiz;
                    $quizDb->setName($nameQuiz);
                    $quizDb->setMoodleId($idQuiz);
                    $quizDb->setTeacher($teacher);

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($quizDb);
                    $manager->flush();
                }
                $quizDb->setName($nameQuiz);
                $quizDb->setMoodleId($idQuiz);
                $quizDb->setTeacher($teacher);

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($quizDb);
                $manager->flush();

            }
        }

        $nameQuiz = $quizzess["quizzes"][0]['name'];
        $idQuiz = $quizzess["quizzes"][0]['id'];

        $quizDb = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['moodleId' => $idQuiz]);
        if (!$quizDb) {
            $quizDb = new Quiz;
            $quizDb->setName($nameQuiz);
            $quizDb->setMoodleId($idQuiz);
            $quizDb->setUser($teacher);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($quizDb);
            $manager->flush();
        }

        $quizDb->setName($nameQuiz);
        $quizDb->setMoodleId($idQuiz);
        $quizDb->setUser($teacher);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($quizDb);
        $manager->flush();

        return $this->json([
            'success' => true,
        ]);
    }


    /**
     * @Route("/candidate", name="candidate")
     */
    public function postCandidate(MoodleApi $moodleApi)
    {

        $user = $this->getUser();
        $userEmail = $user->getEmail();

        $teacher = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $userEmail]);

        $idTeacher = $teacher->getMoodleId();

        $courses = $moodleApi->getCourses($idTeacher);
        $idCourses = $courses['groups'][0]['courseid'];
        $idGroup = $courses['groups'][0]['id'];


        $users = $moodleApi->getUsers($idCourses);
        foreach ($users as $user) {

            $roles = $user['roles'][0]['roleid'];
            $group = $user['groups'];

            if (!empty($group)) {
                $groupIdCandidate = $group[0]['id'];
            }

            if ($roles === 5 && $groupIdCandidate === $idGroup) {
                $idCandidate = $user['id'];
                $fullnameCandidate = $user['fullname'];
                $avatarCandidate = $user['profileimageurl'];

                $emailCandidate = $user['email'];

                $candidateDb =  $this->getDoctrine()->getRepository(Candidate::class)->findOneBy(['moodleId' => $idCandidate]);

                if (!$candidateDb) {
                    $candidateDb = new Candidate;
                    $candidateDb->setFullname($fullnameCandidate);
                    $candidateDb->setMoodleId($idCandidate);
                    $candidateDb->setEmail($emailCandidate);
                    $candidateDb->setUser($teacher);
                    $candidateDb->setAvatar($avatarCandidate);

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($candidateDb);
                    $manager->flush();
                }

                $candidateDb->setFullname($fullnameCandidate);
                $candidateDb->setMoodleId($idCandidate);
                $candidateDb->setEmail($emailCandidate);
                $candidateDb->setUser($teacher);
                $candidateDb->setAvatar($avatarCandidate);

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($candidateDb);
                $manager->flush();

            }

        }
        return $this->json([
            'success' => true,

        ]);
    }

    /**
     * @Route("/criteria", name="criteria")
     */
    public function postCriteria(MoodleApi $moodleApi)
    {

        $user = $this->getUser();
        $userEmail = $user->getEmail();

        $teacher = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $userEmail]);

        $idTeacher = $teacher->getMoodleId();

        $courses = $moodleApi->getCourses($idTeacher);
        $idCourses = $courses['groups'][0]['courseid'];

        $quizzess = $moodleApi->getQuiz($idCourses);
        if (count($quizzess['quizzes']) > 1) {
            foreach ($quizzess as $quizzes) {

                $idQuiz = $quizzes[0]['id'];

                $candidateDb =  $this->getDoctrine()->getRepository(Candidate::class)->findOneBy(['user' => $teacher ]);
                $idMoodleCandidate = $candidateDb->getMoodleId();

                $attempt = $moodleApi->getAttempsUser($idQuiz, $idMoodleCandidate);
                $idAttempt = end($attempt['attempts'])['id'];

                $attemptreview = $moodleApi->getAttempsReview($idAttempt);
                $questions = $attemptreview['questions'];

                $quizCriteria =  $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['moodleId' => $idQuiz]);

                foreach ($questions as $question) {
                    $nameQuestion = $moodleApi->getNameQuestion($question['html']);

                    $criteriaDb =  $this->getDoctrine()->getRepository(Criteria::class)->findOneBy(['name' => $nameQuestion]);
                    if (!$criteriaDb) {
                        $criteriaDb = new Criteria;
                        $criteriaDb->setName($nameQuestion);
                        $criteriaDb->setQuiz($quizCriteria);
                        $manager = $this->getDoctrine()->getManager();
                        $manager->persist($criteriaDb);
                        $manager->flush();
                    }
                    $criteriaDb->setName($nameQuestion);
                    $criteriaDb->setQuiz($quizCriteria);
                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($criteriaDb);
                    $manager->flush();
                }
            }
        }
        $idQuiz = $quizzess["quizzes"][0]['id'];


        $candidateDb =  $this->getDoctrine()->getRepository(Candidate::class)->findOneBy(['user' => $teacher ]);
        $idMoodleCandidate = $candidateDb->getMoodleId();

        $attempt = $moodleApi->getAttempsUser($idQuiz, $idMoodleCandidate);
        $idAttempt = end($attempt['attempts'])['id'];

        $attemptreview = $moodleApi->getAttempsReview($idAttempt);
        $questions = $attemptreview['questions'];

        $quizCriteria =  $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['moodleId' => $idQuiz]);

        foreach ($questions as $question) {
            $nameQuestion = $moodleApi->getNameQuestion($question['html']);

            $criteriaDb =  $this->getDoctrine()->getRepository(Criteria::class)->findOneBy(['name' => $nameQuestion]);
            if (!$criteriaDb) {
                $criteriaDb = new Criteria;
                $criteriaDb->setName($nameQuestion);
                $criteriaDb->setQuiz($quizCriteria);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($criteriaDb);
                $manager->flush();
            }
            $criteriaDb->setName($nameQuestion);
            $criteriaDb->setQuiz($quizCriteria);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($criteriaDb);
            $manager->flush();
        }

        return $this->json([
            'success' => true,

        ]);
    }

    /**
     * @Route("/result", name="result")
     */
    public function postResult(MoodleApi $moodleApi)
    {

        $user = $this->getUser();
        $userEmail = $user->getEmail();

        $teacher = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $userEmail]);

        $idTeacher = $teacher->getMoodleId();

        $courses = $moodleApi->getCourses($idTeacher);
        $idCourses = $courses['groups'][0]['courseid'];

        $quizzess = $moodleApi->getQuiz($idCourses);
        if (count($quizzess['quizzes']) > 1) {
            foreach ($quizzess as $quizzes) {

                $idQuiz = $quizzes[0]['id'];
                $dbCandidates =  $this->getDoctrine()->getRepository(Candidate::class)->findBy(['user' => $teacher ]);


                foreach ($dbCandidates as $dbCandidate) {

                    $idMoodleCandidate = $dbCandidate->getMoodleId();
                    $idCandidateDb = $dbCandidate->getId();


                    $attempt = $moodleApi->getAttempsUser($idQuiz, $idMoodleCandidate);
                    $idAttempt = end($attempt['attempts'])['id'];

                    $attemptreview = $moodleApi->getAttempsReview($idAttempt);
                    $questions = $attemptreview['questions'];

                    foreach ($questions as $question) {

                        $testNote = intval($question['mark']);
                        $nameQuestion = $moodleApi->getNameQuestion($question['html']);


                        $nameCriteria =  $this->getDoctrine()->getRepository(Criteria::class)->findOneBy(['name' => $nameQuestion]);
                        $idNameCriteria = $nameCriteria->getId();


                        $testNoteDb =  $this->getDoctrine()->getRepository(Result::class)->findOneBy([
                            'candidate' => $idCandidateDb,
                            'criteria' => $idNameCriteria
                        ]);
                        if (!$testNoteDb) {
                            $testNoteDb = new Result;
                            $testNoteDb->setCandidate($dbCandidate);
                            $testNoteDb->setCriteria($nameCriteria);
                            $testNoteDb->setTestreview($testNote);
                            $manager = $this->getDoctrine()->getManager();
                            $manager->persist($testNoteDb);
                            $manager->flush();
                        }
                        $testNoteDb->setCandidate($dbCandidate);
                        $testNoteDb->setCriteria($nameCriteria);
                        $testNoteDb->setTestreview($testNote);
                        $manager = $this->getDoctrine()->getManager();
                        $manager->persist($testNoteDb);
                        $manager->flush();
                    }
                }
            }
        }
        $idQuiz = $quizzess["quizzes"][0]['id'];
        $dbCandidates =  $this->getDoctrine()->getRepository(Candidate::class)->findBy(['user' => $teacher ]);

        foreach ($dbCandidates as $dbCandidate) {

            $idMoodleCandidate = $dbCandidate->getMoodleId();
            $idCandidateDb = $dbCandidate->getId();

            $attempt = $moodleApi->getAttempsUser($idQuiz, $idMoodleCandidate);
            $idAttempt = end($attempt['attempts'])['id'];

            $attemptreview = $moodleApi->getAttempsReview($idAttempt);
            $questions = $attemptreview['questions'];

            foreach ($questions as $question) {

                $testNote = intval($question['mark']);
                $nameQuestion = $moodleApi->getNameQuestion($question['html']);


                $nameCriteria =  $this->getDoctrine()->getRepository(Criteria::class)->findOneBy(['name' => $nameQuestion]);
                $idNameCriteria = $nameCriteria->getId();


                $testNoteDb =  $this->getDoctrine()->getRepository(Result::class)->findOneBy([
                    'candidate' => $idCandidateDb,
                    'criteria' => $idNameCriteria
                ]);
                if (!$testNoteDb) {
                    $testNoteDb = new Result;
                    $testNoteDb->setCandidate($dbCandidate);
                    $testNoteDb->setCriteria($nameCriteria);
                    $testNoteDb->setTest($testNote);
                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($testNoteDb);
                    $manager->flush();
                }
                $testNoteDb->setCandidate($dbCandidate);
                $testNoteDb->setCriteria($nameCriteria);
                $testNoteDb->setTest($testNote);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($testNoteDb);
                $manager->flush();
            }
        }
        return $this->json([
            'success' => true,

        ]);
    }
    /**
     * @Route("/coef", name="coef")
     */
    public function postcoef(MoodleApi $moodleApi)
    {

        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['criteria' => 1]);

        foreach ($results as $result) {
            $result->setCoeforal(0.77);
            $result->setCoeftest(0.23);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($result);
        }

        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['criteria' => 2]);
        foreach ($results as $result) {
            $result->setCoeforal(0.11);
            $result->setCoeftest(0.89);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($result);
        }


        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['criteria' => 3]);
        foreach ($results as $result) {
            $result->setCoeforal(0.48);
            $result->setCoeftest(0.52);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($result);
        }


        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['criteria' => 4]);
        foreach ($results as $result) {
            $result->setCoeforal(0.66);
            $result->setCoeftest(0.34);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($result);
        }

        $manager->flush();
        return $this->json([
            'success' => true,

        ]);

    }

    /**
     * @Route("/teacher", name="teacher")
     */
    public function postTeacher(MoodleApi $moodleApi, string $email)
    {
        $user = $moodleApi->getTeacher($email);
        if (!$user["users"]) {
            return[
                'success' => false,
                'message' => "L'email n'exite pas dans moodle."
            ];
        }
        $idUser = $user["users"][0]["id"];
        $courses = $moodleApi->getCourses($idUser);
        $idCourses = $courses['groups'][0]['courseid'];
        $users = $moodleApi->getUsers($idCourses);
        foreach ($users as $user) {
            $role = $user["roles"][0]["roleid"];
            if ($user["id"] === $idUser && $role === 3) {
                return [
                    'success' => true,
                    'message' => 'Éxaminateur ajouter',
                    'fullname' => $user["fullname"],
                    'idMoodle' => $user["id"]
                ];
            }

        }

        return[
            'success' => false,
            'message' => "L'email ne correspond pas à un examinateur."
        ];
    }


}
