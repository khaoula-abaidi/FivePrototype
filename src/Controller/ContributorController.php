<?php


namespace App\Controller;


use App\Entity\Contributor;
use App\Entity\Decision;
use App\Form\ConnexionContributorType;
use App\Form\ContributorType;
use App\Form\DecisionsNotTakenType;
use App\Form\DecisionType;
use App\Repository\ContributorRepository;
use App\Repository\DecisionRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contributor")
 * Class ContributorController
 * @package App\Controller
 */

class ContributorController extends  AbstractController
{
    /**
     * @Route("/index/{id}", name="contributor_index_id")
     * @param ContributorRepository $contributorRepository
     * @return Response
     */
    public function index(EntityManagerInterface $manager,ContributorRepository $contributorRepository, $id,DecisionRepository $decisionRepository,Request $request): Response{
        $contributor = $contributorRepository->find($id);

        $decisions = $contributor->getDecisions()->filter(function ($decision){
            return $decision->getIsTaken()==false;
        });
            $contributor->setDecisionsNT($decisions);
        /**
         * Formulaire reliés aux décisions à prendre par le contributor
         *  1* Changement des décisions que par ceux non prises
         *  2* Affichage du formulaire
         *  3* Traitement des résultats relatifs  aux décisions
         */
        $form = $this->createForm(ContributorType::class,$contributor);
        $form->handleRequest($request);

         // After the Submission of the Form
        if($form->isSubmitted()){
            /**
             * Updating each decision with Form data
             */
            foreach ($decisions as $decision) {
                /**
                 * Access Roles Validation
                 */
                //$this->denyAccessUnlessGranted('update',$contributor);
                switch ($decision->getDeposit()) {
                                                    case 'oui' :
                                                                $decision->setIsTaken(true);
                                                                $decision->setContent('Dépôt');
                                                                break;
                                                    case 'non' :
                                                                $decision->setIsTaken(true);
                                                                $decision->setContent('Refus Dépôt');
                                                                break;
                                                    default    : //$decision->setIsTaken(null);
                                                                $decision->setContent('STILL WAITING FOR DEPOSIT DECISION');
                                                                break;
                                            }
            }
            /**
             * Saving the contributor's decisions
             */
            $manager->flush();
            $this->addFlash('success','Les nouvelles décisions sont prises en compte');
            return $this->redirectToRoute('contributor_index_id',[
                                                                        'id' => $contributor->getId()
                                                    ]);
        }
        return $this->render('contributor/index.html.twig',
                            [
                                'contributor' => $contributor,
                                'decisions' => $decisions,
                                'form' => $form->createView(),

                            ]);
    }


    /**
     * @Route("/login", name = "contributor_connexion")
     */
    public function connexion(ContributorRepository $repository, Request $request): Response
    {

        $form = $this->createForm(ConnexionContributorType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $data = $form->getData();
            $login = $data->getLogin();
            $pwd = $data->getPwd();
            /**
             *Searching for contributor's having login && Pwd into the database
             * @var $contributor Contributor
             */
            $contributor = $repository->findOneBy([
                'login' => $login,
                'pwd'   => $pwd
            ]);
            if($contributor!==null){
                $this->addFlash('success','Authentification réussite');
                return $this->redirectToRoute('contributor_index_id',[
                    'id' => $contributor->getId()
                ]);
            }
        }
        return $this->render('contributor/connexion.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="contributor_logout")
     * @return Response
     */
    public function deconnexion(): Response{
        $this->addFlash('success','Vous êtes déconnecté');
        return $this->render('home/index.html.twig');
    }
}