<?php

namespace App\Controller;

use PHPExcel;
use Dompdf\Dompdf;
use App\Entity\User;
use App\Entity\BaseFile;
use App\Entity\Resultat;
use App\Entity\Employeur;
use App\Entity\Population;
use App\Entity\Fournisseur;
use App\Entity\TypePopulation;
use App\Security\FileUploader;
use App\Repository\UserRepository;
use App\Repository\BaseFileRepository;
use App\Repository\ResultatRepository;
use App\Repository\EmployeurRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Repository\PopulationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FournisseurRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repository\TypePopulationRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ang3\Component\Serializer\Encoder\ExcelEncoder;
use App\Entity\ActeGestion;
use App\Repository\ActeGestionRepository;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_login")
     */
    public function index(): Response
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        //for deconnection
    }

    /**
     * @Route("/home", name="app_home")
     */
    public function home(FournisseurRepository $fournisseurRepository, EmployeurRepository $employeurRepository, ResultatRepository $resultatRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            //total commercialsateurs
            $tCommercialisateurs = $fournisseurRepository->count(array());

            //total employeurs
            $tEmployeurs = $employeurRepository->count(array());

            //les 5 denniers commercialisateurs
            $commercilisateurs = array();
            $dbResultCommercialisateur = $fournisseurRepository->findBy(array(),array('id'=>'DESC'),5);
            if($dbResultCommercialisateur){
                $commercilisateurs = $dbResultCommercialisateur;
            }
            //les 5 derniers employeurs
            $employeurs = array();
            $dbResultEmployeurs = $employeurRepository->findBy(array(),array('id'=>'DESC'),5);
            if($dbResultEmployeurs){
                $employeurs = $dbResultEmployeurs;
            }
            //les 5 derniers resultats comparateurs
            $resultatsComparateur = array();
            $dbResultResultat = $resultatRepository->findBy(array(),array('id'=>'DESC'),5);
            if($dbResultResultat){
                $resultatsComparateur = $dbResultResultat;
            }
            return $this->render(
                'home.html.twig',
                [
                    'pname'=>'Accueil',
                    'tEmployer'=>$tEmployeurs,
                    'tPensionner'=>0,
                    'tCommercialisateurs'=>$tCommercialisateurs,
                    'commercilisateurs'=>$commercilisateurs,
                    'employeurs'=>$employeurs,
                    'resultatsComparateur'=>$resultatsComparateur,
                ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/base_file", name="app_upload_base_file")
     */
    public function baseFileUpload(): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            return $this->render('base_file.html.twig',['pname'=>'Import fichier de base']);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/uploadBaseFile", name="app_uploadBaseFile")
     */
    public function uploadBaseFile(
        BaseFileRepository $baseFileRepository, 
        FileUploader $fileUploader,
        EntityManagerInterface $em, 
        Request $request, 
        Filesystem $filesystem,
        string $targetDirectory,
        PopulationRepository $populationRepository,
        EmployeurRepository $employeurRepository, 
        FournisseurRepository $fournisseurRepository, 
        TypePopulationRepository $typePopulationRepository, 
    ): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $error = '';
            $file = $request->files->get('file');
            $departement = $request->request->get('departement');

            if($file->getClientOriginalExtension() == 'csv' || $file->getClientOriginalExtension() == 'xlsx' || $file->getClientOriginalExtension() == 'xls'){
                //on créer l'objet file
                $fileName = 'baseFile';
                
                //on supprime le fichier courant pour
                $filesystem->remove($targetDirectory.'/'.$fileName);
                $basefileObj = $baseFileRepository->findOneBy(array('type'=>$fileName));
                if($basefileObj){
                    $basefileObj->setName(' ');
                    $basefileObj->setType($fileName);
                    $em->persist($basefileObj);
                    $em->flush();
                }else{
                    $basefileObj = new BaseFile();
                    $basefileObj->setName(' ');
                    $basefileObj->setType($fileName);
                    $em->persist($basefileObj);
                    $em->flush();
                }
                //on upload le fichier
                $baseFileName = $fileUploader->upload($file,$targetDirectory.'/'.$fileName);

                //mise à jour de l'entité
                $basefileObj->setName($baseFileName); 
                $em->persist($basefileObj);
                $em->flush();

                //on sauvegarde les populations de pensionné et de salarié

                $data = $this->getArrayFromCsvFile($targetDirectory.'/'.$fileName.'/'.$basefileObj->getName());
                if($file->getClientOriginalExtension() == 'xlsx' || $file->getClientOriginalExtension() == 'xls'){
                    $temp = [];
                    foreach ($data as $value) {
                        $temp[] = $value;
                    }
                    $data = $temp[0];
                }
                foreach ($data as $value) {
                    if(isset($value['NNI'])){
                        if($value['NNI'] != ''){
                            $populationObj = $populationRepository->findOneBy(array('nni'=>$value['NNI']));
                        }
                    }
                    if(isset($value['Matricule'])){
                    if($value['Matricule'] != ''){
                            $populationObj = $populationRepository->findOneBy(array('matricule'=>$value['Matricule']));
                        }
                    }
                    if(!isset($populationObj)){
                        $populationObj = new Population();
                    }
                    foreach ($value as $key => $val) {
                        $d = utf8_encode($key);
                        $dateAdd = new \DateTime('now');
                        switch ($d) {
                            case 'Nom usuel':
                                $populationObj->setNomUsuel($val);
                                break;
                            case 'Nom de naissance':
                                $populationObj->setNomNaissance($val);
                                break;
                            case 'Prénom':
                                $populationObj->setPrenom($val);
                                break;
                            case 'NNI':
                                $populationObj->setNni($val);
                                break;
                            case 'Matricule':
                                $populationObj->setMatricule($val);
                                break;
                            case 'Date début résidence':
                                $populationObj->setDebutPeriodeConso(new \DateTime($val));
                                break;
                            case 'Date fin résidence':
                                $populationObj->setFinPeriodeConso(new \DateTime($val));
                                break;
                            case 'Montant des taxes sur l\'énergie hors TVA':
                                $populationObj->setTaxe($val);
                                break;
                            case 'Montant total HT':
                                $populationObj->setMontant($val);
                                break;
                            case 'Type de population':
                                $typePopulation = $typePopulationRepository->findOneBy(array('libelle'=>ucfirst(strtolower($val))));
                                if($typePopulation){
                                    $populationObj->setTypePopulation($typePopulation);
                                }else{
                                    $typePopulationObj = new TypePopulation();
                                    $typePopulationObj->setLibelle(ucfirst(strtolower($val)));
                                    $em->persist($typePopulationObj);
                                    $em->flush();
                                    $populationObj->setTypePopulation($typePopulationObj);
                                }
                                break;
                            case 'Commercialisateur PDL 1':
                                if(!empty($val)){
                                    $fournData = explode('-',trim($val));
                                    $commercialisateur = $fournisseurRepository->findOneBy(array('num_fournisseur'=>$fournData['0']));
                                    if($commercialisateur){
                                        $populationObj->setCommercialisateurPdl1($commercialisateur);
                                    }else{
                                        $codeCommercialisateur = $fournData['0'];
                                        unset($fournData['0']);
                                        $nomCommercialisateur = '';
                                        foreach ($fournData as $k => $value) {
                                            $nomCommercialisateur .= trim($value).'-';
                                        }
                                        $nomCommercialisateur = rtrim($nomCommercialisateur,'-');

                                        $commercialisateurObj1 = new Fournisseur();
                                        $commercialisateurObj1->setName($nomCommercialisateur);
                                        $commercialisateurObj1->setDateAdd($dateAdd);
                                        $commercialisateurObj1->setNumFournisseur($codeCommercialisateur);
                                        $em->persist($commercialisateurObj1);
                                        $em->flush();
                                        $populationObj->setCommercialisateurPdl1($commercialisateurObj1);
                                    }
                                }
                                break;
                            case 'Commercialisateur PDL 2':
                                if(!empty($val)){
                                    $fournData = explode('-',trim($val));
                                    $commercialisateur = $fournisseurRepository->findOneBy(array('num_fournisseur'=>$fournData['0']));
                                    if($commercialisateur){
                                        $populationObj->setCommercialisateurPdl2($commercialisateur);
                                    }else{
                                        $codeCommercialisateur = $fournData['0'];
                                        unset($fournData['0']);
                                        $nomCommercialisateur = '';
                                        foreach ($fournData as $k => $value) {
                                            $nomCommercialisateur .= trim($value).'-';
                                        }
                                        $nomCommercialisateur = rtrim($nomCommercialisateur,'-');
                                        
                                        $commercialisateurObj2 = new Fournisseur();
                                        $commercialisateurObj2->setName($nomCommercialisateur);
                                        $commercialisateurObj2->setNumFournisseur($codeCommercialisateur);
                                        $commercialisateurObj2->setDateAdd($dateAdd);
                                        $em->persist($commercialisateurObj2);
                                        $em->flush();
                                        $populationObj->setCommercialisateurPdl2($commercialisateurObj2);
                                    }
                                }
                                break;
                            case 'Commercialisateur PDL 3':
                                if(!empty($val)){
                                    $fournData = explode('-',trim($val));
                                    $commercialisateur = $fournisseurRepository->findOneBy(array('num_fournisseur'=>$fournData['0']));
                                    if($commercialisateur){
                                        $populationObj->setCommercialisateurPdl3($commercialisateur);
                                    }else{
                                        $codeCommercialisateur = $fournData['0'];
                                        unset($fournData['0']);
                                        $nomCommercialisateur = '';
                                        foreach ($fournData as $k => $value) {
                                            $nomCommercialisateur .= trim($value).'-';
                                        }
                                        $nomCommercialisateur = rtrim($nomCommercialisateur,'-');
                                        
                                        $commercialisateurObj3 = new Fournisseur();
                                        $commercialisateurObj3->setName($nomCommercialisateur);
                                        $commercialisateurObj3->setNumFournisseur($codeCommercialisateur);
                                        $commercialisateurObj3->setDateAdd($dateAdd);
                                        $em->persist($commercialisateurObj3);
                                        $em->flush();
                                        $populationObj->setCommercialisateurPdl3($commercialisateurObj3);
                                    }
                                }
                                break;
                            case 'Employeur':
                                if(!empty($val)){
                                    $employeur = $employeurRepository->findOneBy(array('libelle'=>$val));
                                    if($employeur){
                                        $populationObj->setEmployeur($employeur);
                                    }else{
                                        $employeurObj = new Employeur();
                                        $employeurObj->setRaisonSociale($val);
                                        $employeurObj->setLibelle($val);
                                        $em->persist($employeurObj);
                                        $em->flush();
                                        $populationObj->setEmployeur($employeurObj);
                                    }
                                }
                                break;
                            case 'Dernier employeur':
                                if(!empty($val)){
                                    $employeur = $employeurRepository->findOneBy(array('libelle'=>$val));
                                    if($employeur){
                                        $populationObj->setDernierEmployeur($employeur);
                                    }else{
                                        $employeurObj = new Employeur();
                                        $employeurObj->setRaisonSociale($val);
                                        $employeurObj->setLibelle($val);
                                        $em->persist($employeurObj);
                                        $em->flush();
                                        $populationObj->setDernierEmployeur($employeurObj);
                                    }
                                }
                                break;
                        }
                    }
                    $populationObj->setDepartement($departement);
                    $em->persist($populationObj);
                    $em->flush();
                }
                $error = '';
    
                $output = array(
                    'error'  => $error,
                    'data' => array('message'=>'ok'),
                );
    
            }else{
                $error = '2';
    
                $output = array(
                    'error'  => $error,
                    'data' => array(),
                );
            }
            return new JsonResponse($output);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @param string $filePath the path of the file 
     * @return array of data file
     */
    private function getArrayFromCsvFile(string $filePath = ""): array
    {
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $normalizers = [new ObjectNormalizer()];
        $encoders = [
            new CsvEncoder(),
            new ExcelEncoder()
        ];
        $serializer = new Serializer($normalizers, $encoders);

        $fileString = file_get_contents($filePath);

        $data = $serializer->decode($fileString, $fileExtension, array(CsvEncoder::DELIMITER_KEY =>";"));
        
        if(!empty($data)){
            return $data; 
        }else{
            return array(); 
        }
    }

    

    /**
     * @Route("/camparator", name="app_comparator")
     */
    public function camparator(Request $request,FournisseurRepository $fournisseurRepository, EmployeurRepository $employeurRepository, TypePopulationRepository $typePopulationRepository, ResultatRepository $resultatRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            //on recupere l'id de l'utilisateur connecté
            $userConnected = $this->get('security.token_storage')->getToken()->getUser();

            $response = [];
            $commercilisateurs = $fournisseurRepository->findAll();
            $employeurs = $employeurRepository->findAll();
            $typePopulations = $typePopulationRepository->findAll();

            //on recherche le flag dans la session pour l'affichage des resultat
            $flag = $request->getSession()->get('resultComparator-'.$userConnected->getId());
            $request->getSession()->set('resultComparator-'.$userConnected->getId(),'');
            if($flag == 'flagID'.$userConnected->getId()){
                $result = $resultatRepository->findOneBy(array('admin'=>$userConnected),array('id'=>'DESC'));
                $date = $result->getDateAdd();
                $response = $resultatRepository->findBy(array('date_add'=>$date),array('id'=>'DESC'));
            }
            return $this->render(
                'camparator.html.twig',
                [
                    'pname'=>'Comparateur',
                    'commercilisateurs'=>$commercilisateurs,
                    'employeurs'=>$employeurs,
                    'typePopulations'=>$typePopulations,
                    'response'=>$response,
                ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/resultat", name="app_resultat")
     */
    public function resultat(
        Request $request, 
        FournisseurRepository $fournisseurRepository, 
        EmployeurRepository $employeurRepository,
        string $donwloadDirectory, 
        FileUploader $fileUploader,
        BaseFileRepository $baseFileRepository, 
        EntityManagerInterface $em, 
        Filesystem $filesystem,
        string $targetDirectory, 
        TypePopulationRepository $typePopulationRepository, 
        PopulationRepository $populationRepository
    ): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $niveau = $request->request->get("niveau");
            $commercialisateur = $request->request->get("commercialisateurs");
            $dernirEmployeurs = $request->request->get("employeurs");
            $type = $request->request->get("type");
            $departement = $request->request->get("departement");
            $file = $request->files->get('file-input');

            
            if($file->getClientOriginalExtension() == 'csv' || $file->getClientOriginalExtension() == 'xlsx' || $file->getClientOriginalExtension() == 'xls'){
                
                $dateAdd = new \DateTime('now');

                //on recupere l'id de l'utilisateur connecté
                $userConnected = $this->get('security.token_storage')->getToken()->getUser();

                //on supprime le fichier courant pour
                //$filesystem->remove($targetDirectory.'/comparator/'.$userConnected->getId());

                //on upload le fichier
                $baseFileName = $fileUploader->upload($file,$targetDirectory.'/comparator/'.$userConnected->getId());
                //on sauvegarde le nom du fichier dans la session
                $request->getSession()->set('fileNameComparator-'.$userConnected->getId(),$baseFileName);

                //on construit les données du fichier en tableau
                $filePath = $targetDirectory.'/comparator/'.$userConnected->getId().'/'.$request->getSession()->get('fileNameComparator-'.$userConnected->getId());
                $data = $this->getArrayFromCsvFile($filePath);
                if($file->getClientOriginalExtension() == 'xlsx' || $file->getClientOriginalExtension() == 'xls'){
                    $temp = [];
                    foreach ($data as $value) {
                        $temp[] = $value;
                    }
                    $data = $temp[0];
                }

                //on recherche la population correspondante en BD
                $filter = [
                    'commercialisateur' => ($commercialisateur != ''?$commercialisateur:''),
                    'dernirEmployeurs' => ($dernirEmployeurs != ''?$dernirEmployeurs:''),
                    'employeur' => ($dernirEmployeurs != ''?$dernirEmployeurs:''),
                    'type' => ($type != ''?$type:''),
                    'departement' => ($departement != ''?$departement:''),
                ];

                //debut de la comparaison
                $result = [];

                //on sauvegarde l'acte de gestion de l'utilisateur
                $commercialisateurObj = $fournisseurRepository->find($commercialisateur);
                $employeurObj = $employeurRepository->find($dernirEmployeurs);
                $typePopulationObj = $typePopulationRepository->find($type);

                $acteGestionObj = new ActeGestion();
                $acteGestionObj->setGestionnaire($userConnected);
                $acteGestionObj->setDateAdd($dateAdd);
                $acteGestionObj->setDepartement($departement);
                if($commercialisateurObj){
                    $acteGestionObj->setCommercialisateur($commercialisateurObj);
                }
                if($employeurObj){
                    $acteGestionObj->setEmployeur($employeurObj);
                }
                if($typePopulationObj){
                    $acteGestionObj->setTypePopulation($typePopulationObj);
                }
                $em->persist($acteGestionObj);
                $em->flush();

                foreach ($data as $value) {
                    $populations = [];
                    if(isset($value['NNI'])){
                        if($value['NNI'] != ''){
                            $populations = $populationRepository->findOneByFilterData($filter,$value['NNI'],null,null,null,null);
                        }
                    }
                    if (isset($value['Matricule']) && empty($populations)) {
                        if($value['Matricule'] != ''){
                            $populations = $populationRepository->findOneByFilterData($filter,null,$value['Matricule'],null,null,null);
                        }
                    }
                    if(isset($value['Nom usuel']) && isset($value['Nom de naissance']) && isset($value[utf8_decode('Prénom')]) && empty($populations)){
                        $populations = $populationRepository->findOneByFilterData($filter,null,null,$value['Nom usuel'],$value['Nom de naissance'],$value[utf8_decode('Prénom')]);
                    }
                    if($populations){
                        if($niveau == '1' || $niveau == null){
                            $result = [
                                'nom'=>'<b>valeur fichier de base :</b>'.($populations->getNomNaissance()!=''?$populations->getNomNaissance():'---').'<br><b>valeur fichier de comparaison :</b>'.($value['Nom de naissance']!=''?$value['Nom de naissance']:'---'),
                                'prenom'=>'<b>valeur fichier de base :</b>'.($populations->getPrenom()!=''?$populations->getPrenom():'---').'<br><b>valeur fichier de comparaison :</b>'.($value[('Prénom')]!=''?$value[('Prénom')]:'---'),
                                'nni'=>'<b>valeur fichier de base :</b>'.($populations->getNni()!=''?$populations->getNni():'---').'<br><b>valeur fichier de comparaison :</b>'.($value['NNI']!=''?$value['NNI']:'---'),
                                'matricule'=>'<b>valeur fichier de base :</b>'.($populations->getMatricule()!=''?$populations->getMatricule():'---').'<br><b>valeur fichier de comparaison :</b>'.($value['Matricule']!=''?$value['Matricule']:'---'),
                                'employeur'=>'<b>valeur fichier de base :</b>'.($populations->getEmployeur()?$populations->getEmployeur()->getLibelle():'---').'<br><b>valeur fichier de comparaison :</b>'.($value['Employeur']!=''?$value['Employeur']:'---'),
                                'dernier_employeur'=>'<b>valeur fichier de base :</b>'.($populations->getDernierEmployeur()?$populations->getDernierEmployeur()->getLibelle():'---').'<br><b>valeur fichier de comparaison :</b>'.($value['Dernier employeur']!=''?$value['Dernier employeur']:'---'),
                            ];
                        }
                        if($niveau == '2' || $niveau == null){
                            $result['debut_periode'] = '<b>valeur fichier de base :</b>'.(!empty($populations->getDebutPeriodeConso())?$populations->getDebutPeriodeConso()->format('Y-m-d'):'---').'<br><b>valeur fichier de comparaison :</b>'.($value[('Date début résidence')]!=''?$value[('Date début résidence')]:'---');
                            $result['fin_periode'] = '<b>valeur fichier de base :</b>'.($populations->getFinPeriodeConso()!=''?$populations->getFinPeriodeConso()->format('Y-m-d'):'---').'<br><b>valeur fichier de comparaison :</b>'.($value[('Date fin résidence')]!=''?$value[('Date fin résidence')]:'---');
                            $result['montant'] = '<b>valeur fichier de base :</b>'.($populations->getMontant()!=''?$populations->getMontant():'---').'<br><b>valeur fichier de comparaison :</b>'.($value['Montant total HT']!=''?$value['Montant total HT']:'---');
                            $result['taxe'] = '<b>valeur fichier de base :</b>'.($populations->getTaxe()!=''?$populations->getTaxe():'---').'<br><b>valeur fichier de comparaison :</b>'.($value[('Montant des taxes sur l\'énergie hors TVA')]!=''?$value[utf8_decode('Montant des taxes sur l\'énergie hors TVA')]:'---');
                        }
                    }else{
                        $result = [
                            'nom'=>'Pas de correspondance',
                            'prenom'=>'Pas de correspondance',
                            'nni'=>'Pas de correspondance',
                            'matricule'=>'Pas de correspondance',
                            'employeur'=>'Pas de correspondance',
                            'dernier_employeur'=>'Pas de correspondance',
                            'debut_periode'=>'Pas de correspondance',
                            'fin_periode'=>'Pas de correspondance',
                            'montant'=>'Pas de correspondance',
                            'taxe'=>'Pas de correspondance',
                        ];
                    }

                    //on sauvegarde le resultat
                    $resultatObj = new Resultat();
                    $resultatObj->setNni($result['nni']);
                    $resultatObj->setNom($result['nom']);
                    $resultatObj->setPrenom($result['prenom']);
                    $resultatObj->setMatricule($result['matricule']);
                    $resultatObj->setEmployeur($result['employeur']);
                    $resultatObj->setDernierEmployeur($result['dernier_employeur']);
                    $resultatObj->setDebutPeriode($result['debut_periode']);
                    $resultatObj->setFinPeriode($result['fin_periode']);
                    $resultatObj->setMontant($result['montant']);
                    $resultatObj->setTaxe($result['taxe']);
                    $resultatObj->setDateAdd($dateAdd);
                    $resultatObj->setAdmin($userConnected);
                    if(!empty($populations)){
                        $resultatObj->setPensionner($populations);
                    }
                    $resultatObj->setActeGestion($acteGestionObj);
                    $em->persist($resultatObj);
                    $em->flush();

                    //on sauvegarde un flag pour la recherche ulterieur des resultat
                    $request->getSession()->set('resultComparator-'.$userConnected->getId(),'flagID'.$userConnected->getId());

                    $error = '';
        
                    $output = array(
                        'error'  => $error,
                        'data' => array('message'=>'ok'),
                    );
                }
            }else{
                $error = '2';
    
                $output = array(
                    'error'  => $error,
                    'data' => array(),
                );
            }
            return new JsonResponse($output);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/utilisateur", name="app_utilisateur")
     */
    public function utilisateur(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $em): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isMethod('POST')) {
                    $name = $request->request->get("name");
                    $surname = $request->request->get("surname");
                    $email = $request->request->get("email");
                    $username = $request->request->get("username");
                    $password = $request->request->get("password");
                    $roles = ['ROLE_ADMIN'];

                    $findUser = $userRepository->findOneBy(array('username'=>$username));
                    if(!$findUser){
        
                        $userObj = new User();
                        $userObj->setName($name);
                        $userObj->setSurname($surname);
                        $userObj->setEmail($email);
                        $userObj->setUsername($username);
                        $userObj->setPassword($passwordEncoder->hashPassword($userObj, $password));
                        $userObj->setDateUpdate(new \DateTime('now'));
                        $userObj->setDateAdd(new \DateTime('now'));
                        $userObj->setRoles($roles);
                        $em->persist($userObj);
                        $em->flush();
                        $request->getSession()->getFlashBag()->add('success','Utilisateur enregistré avec succés !');
                        return $this->redirectToRoute('app_listutilisateurs');
                    }else{
                        $request->getSession()->getFlashBag()->add('danger','Utilisateur enregistré avec déja ce login !');
                    }
            }
            return $this->render('utilisateur.html.twig',['pname'=>'Utilisateurs']);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/liste-utilisateurs", name="app_listutilisateurs")
     */
    public function liste_utilisateurs(UserRepository $userRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $users = $userRepository->findBy(array(),array('id'=>'DESC'));
            return $this->render('liste-utilisateurs.html.twig',['pname'=>'liste des utilisateurs','users'=>$users]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/update-utilisateur/{id}", name="app_update_utilisateur")
     */

    public function update_utilisateurs(int $id, Request $request, EntityManagerInterface $em, UserRepository $userRepository, UserPasswordHasherInterface $passwordEncoder): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user =  $userRepository->find($id);
            if ($request->isMethod('POST')) {
                $name = $request->request->get("name");
                $surname = $request->request->get("surname");
                $email = $request->request->get("email");
                $username = $request->request->get("username");
                $password = $request->request->get("password");
        
                $userObj = $user;
                $userObj->setName($name);
                $userObj->setSurname($surname);
                $userObj->setEmail($email);
                $userObj->setUsername($username);
                $userObj->setDateUpdate(new \DateTime('now'));
                if($password != ""){
                    $userObj->setPassword($passwordEncoder->hashPassword($userObj, $password));
                }
                $em->persist($userObj);
                $em->flush();
                $request->getSession()->getFlashBag()->add('success','Utilisateur enregistré avec succés !');
                return $this->redirectToRoute('app_listutilisateurs');
            }
            return $this->render('utilisateur.html.twig',['pname'=>'Modifier un utilisateur','user'=>$user]);
        }else{
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * @Route("/commercialisateurs", name="app_commercialisateurs")
     */
    public function commercialisateurs(Request $request, FournisseurRepository $fournisseurRepository, EntityManagerInterface $em): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isMethod('POST')) {
                    $name = $request->request->get("name");
                    $num_commercialisateur = $request->request->get("num_commercialisateur");
                    $telephone = $request->request->get("telephone");
                    $couriel = $request->request->get("couriel");
                    $adresse = $request->request->get("adresse");
                    $code_postal = $request->request->get("code_postal");
                    $ville = $request->request->get("ville");
                    $departement = substr($code_postal, 0,2);

                    $fournisseur = $fournisseurRepository->findOneBy(array('num_fournisseur'=>$num_commercialisateur));
                    if(!$fournisseur){
        
                        $fournisseurObj = new Fournisseur();
                        $fournisseurObj->setName($name);
                        $fournisseurObj->setNumFournisseur($num_commercialisateur);
                        $fournisseurObj->setTelephone($telephone);
                        $fournisseurObj->setCouriel($couriel);
                        $fournisseurObj->setAdresse($adresse);
                        $fournisseurObj->setCodePostal($code_postal);
                        $fournisseurObj->setDepartement($departement);
                        $fournisseurObj->setVille($ville);
                        $fournisseurObj->setDateAdd(new \DateTime('now'));
                        $em->persist($fournisseurObj);
                        $em->flush();
                        $request->getSession()->getFlashBag()->add('success','Commercialisateur enregistré avec succés !');
                        return $this->redirectToRoute('app_listcommercialisateurs');
                    }else{
                        $request->getSession()->getFlashBag()->add('danger','Un commercialisateur enregistré avec déja ce numéro !');
                    }
            }
            return $this->render('commercialisateur.html.twig',['pname'=>'Commercialisateurs']);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/liste-commercialisateurs", name="app_listcommercialisateurs")
     */
    public function liste_commercialisateurs(FournisseurRepository $fournisseurRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $fournisseurs = $fournisseurRepository->findBy(array(),array('id'=>'DESC'));
            return $this->render('liste-fournisseurs.html.twig',['pname'=>'liste des commercialisateurs','fournisseurs'=>$fournisseurs]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/update-commercialisateur/{id}", name="app_update_commercialisateur")
     */

    public function update_commercialisateur(int $id, Request $request, EntityManagerInterface $em, FournisseurRepository $fournisseurRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $fournisseur =  $fournisseurRepository->find($id);
            if ($request->isMethod('POST')) {
                $name = $request->request->get("name");
                $num_commercialisateur = $request->request->get("num_commercialisateur");
                $telephone = $request->request->get("telephone");
                $couriel = $request->request->get("couriel");
                $adresse = $request->request->get("adresse");
                $code_postal = $request->request->get("code_postal");
                $ville = $request->request->get("ville");
                $departement = substr($code_postal, 0,2);
        
                $fournisseurObj = $fournisseur;
                $fournisseurObj->setName($name);
                $fournisseurObj->setNumFournisseur($num_commercialisateur);
                $fournisseurObj->setTelephone($telephone);
                $fournisseurObj->setCouriel($couriel);
                $fournisseurObj->setAdresse($adresse);
                $fournisseurObj->setCodePostal($code_postal);
                $fournisseurObj->setDepartement($departement);
                $fournisseurObj->setVille($ville);
                $em->persist($fournisseurObj);
                $em->flush();
                $request->getSession()->getFlashBag()->add('success','Commercialisateur enregistré avec succés !');
                return $this->redirectToRoute('app_listcommercialisateurs');
            }
            return $this->render('commercialisateur.html.twig',['pname'=>'Modifier un commercialisateur','fournisseur'=>$fournisseur]);
        }else{
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * @Route("/employeurs", name="app_employeurs")
     */
    public function employeur(Request $request, EmployeurRepository $employeurRepository, EntityManagerInterface $em): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isMethod('POST')) {
                    $siren = $request->request->get("siren");
                    $siret = $request->request->get("siret");
                    $raison_sociale = $request->request->get("raison_sociale");
                    $libelle = $request->request->get("libelle");
                    $adresse = $request->request->get("adresse");
                    $code_postal = $request->request->get("code_postal");
                    $ville = $request->request->get("ville");
                    $departement = substr($code_postal, 0,2);

                    $employeur = $employeurRepository->findOneBy(array('siren'=>$siren,'siret'=>$siret));
                    if(!$employeur){
        
                        $employeurObj = new Employeur();
                        $employeurObj->setSiren($siren);
                        $employeurObj->setSiret($siret);
                        $employeurObj->setRaisonSociale($raison_sociale);
                        $employeurObj->setLibelle($libelle);
                        $employeurObj->setAdresse($adresse);
                        $employeurObj->setCodePostal($code_postal);
                        $employeurObj->setDepartement($departement);
                        $employeurObj->setVille($ville);
                        $employeurObj->setDateAdd(new \DateTime('now'));
                        $em->persist($employeurObj);
                        $em->flush();
                        $request->getSession()->getFlashBag()->add('success','Employeur enregistré avec succés !');
                        return $this->redirectToRoute('app_listemployeurs');
                    }else{
                        $request->getSession()->getFlashBag()->add('danger','Cet Employeur existe déja !');
                    }
            }
            return $this->render('employeur.html.twig',['pname'=>'employeur']);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/liste-employeurs", name="app_listemployeurs")
     */
    public function liste_employeurs(EmployeurRepository $employeurRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $employeurs = $employeurRepository->findBy(array(),array('id'=>'DESC'));
            return $this->render('liste-employeurs.html.twig',['pname'=>'liste des employeurs','employeurs'=>$employeurs]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/update-employeur/{id}", name="app_update_employeur")
     */

    public function update_employeur(int $id, Request $request, EntityManagerInterface $em, EmployeurRepository $employeurRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $employeur =  $employeurRepository->find($id);
            if ($request->isMethod('POST')) {
                $siren = $request->request->get("siren");
                $siret = $request->request->get("siret");
                $raison_sociale = $request->request->get("raison_sociale");
                $libelle = $request->request->get("libelle");
                $adresse = $request->request->get("adresse");
                $code_postal = $request->request->get("code_postal");
                $ville = $request->request->get("ville");
                $departement = substr($code_postal, 0,2);
        
                $employeurObj = $employeur;
                $employeurObj->setSiren($siren);
                $employeurObj->setSiret($siret);
                $employeurObj->setRaisonSociale($raison_sociale);
                $employeurObj->setLibelle($libelle);
                $employeurObj->setAdresse($adresse);
                $employeurObj->setCodePostal($code_postal);
                $employeurObj->setDepartement($departement);
                $employeurObj->setVille($ville);
                $em->persist($employeurObj);
                $em->flush();
                $request->getSession()->getFlashBag()->add('success','Employeur enregistré avec succés !');
                return $this->redirectToRoute('app_listemployeurs');
            }
            return $this->render('employeur.html.twig',['pname'=>'Modifier un employeur','employeur'=>$employeur]);
        }else{
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * @Route("/type-population", name="app_typePopulation")
     */
    public function typePopulation(Request $request, TypePopulationRepository $typePopulationRepository, EntityManagerInterface $em): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isMethod('POST')) {
                    $libelle = $request->request->get("libelle");

                    $typePopulation = $typePopulationRepository->findOneBy(array('libelle'=>$libelle));
                    if(!$typePopulation){
        
                        $typePopulationObj = new TypePopulation();
                        $typePopulationObj->setLibelle(ucfirst(strtolower($libelle)));
                        $em->persist($typePopulationObj);
                        $em->flush();
                        $request->getSession()->getFlashBag()->add('success','Type de population enregistré avec succés !');
                        return $this->redirectToRoute('app_listetypepopulation');
                    }else{
                        $request->getSession()->getFlashBag()->add('danger','Cet type de population existe déja !');
                    }
            }
            return $this->render('type_population.html.twig',['pname'=>'Type de population']);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/liste-type-population", name="app_listetypepopulation")
     */
    public function liste_type_population(TypePopulationRepository $typePopulationRepository,PopulationRepository $populationRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $rep = [];
            $typePopulations = $typePopulationRepository->findBy(array(),array('id'=>'DESC'));
            foreach ($typePopulations as $value) {
                $total = $populationRepository->count(array('type_population'=>$value));
                $rep[] = [
                    'id'=>$value->getId(),
                    'libelle'=>$value->getLibelle(),
                    'total'=>$total,
                ];
            }
            return $this->render('liste-type-population.html.twig',['pname'=>'liste des types de population','typePopulations'=>$rep]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/update-type-population/{id}", name="app_update_type_population")
     */

    public function update_type_population(int $id, Request $request, EntityManagerInterface $em, TypePopulationRepository $typePopulationRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $typePopulation =  $typePopulationRepository->find($id);
            if ($request->isMethod('POST')) {
                $libelle = $request->request->get("libelle");
        
                $typePopulationObj = $typePopulation;
                $typePopulationObj->setLibelle(ucfirst(strtolower($libelle)));
                $em->persist($typePopulationObj);
                $em->flush();
                $request->getSession()->getFlashBag()->add('success','Type de population enregistré avec succés !');
                return $this->redirectToRoute('app_listetypepopulation');
            }
            return $this->render('type_population.html.twig',['pname'=>'Modifier un type de population','typePopulation'=>$typePopulation]);
        }else{
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * @Route("generateFournExcel", name="app_generateFournExcel")
     */
    public function generateFournExcel(Request $request, FournisseurRepository $fournisseurRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $dataToImport = [];
            if($request->request->get("checkbox")){
            // $idToImport = $request->request->get("checkbox");
                $dataToImport =  $fournisseurRepository->findBy(array('id'=>$request->request->get("checkbox")),array('id'=>'DESC'));
            // dd($dataToImport);
            }else{
                $dataToImport = $fournisseurRepository->findBy(array(),array('id'=>'DESC'));
            }
            if($dataToImport){
                $data = [];
                foreach ($dataToImport as $value) {
                    $data[] = [
                        'Nom'=> $value->getName(),
                        'Numéro fournisseur'=> $value->getNumFournisseur(),
                    ];
                }
                //dd($idToImport);
        
                $spreadsheet = new Spreadsheet();
                
                /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
                $sheet = $spreadsheet->getActiveSheet()->fromArray($data,null,'A2');
                $sheet->setCellValue('A1', 'Nom fournisseur');
                $sheet->setCellValue('B1', 'Numéro fournisseur');
                $sheet->setTitle("Commercialisateurs");
                
                // Create your Office 2007 Excel (XLSX Format)
                $writer = new Xlsx($spreadsheet);
                
                // Create a Temporary file in the system
                $fileName = 'liste_des_frounisseurs.xlsx';
                $temp_file = tempnam(sys_get_temp_dir(), $fileName);
                
                // Create the excel file in the tmp directory of the system
                $writer->save($temp_file);
                
                // Return the excel file as an attachment
                return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
            }else{
                return $this->redirectToRoute('app_listfournisseurs');
            }
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("generateEmployeurExcel", name="app_generateEmployeurExcel")
     */
    public function generateEmployeurExcel(Request $request, EmployeurRepository $employeurRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $dataToImport = [];
            if($request->request->get("checkbox")){
            // $idToImport = $request->request->get("checkbox");
                $dataToImport =  $employeurRepository->findBy(array('id'=>$request->request->get("checkbox")),array('id'=>'DESC'));
            // dd($dataToImport);
            }else{
                $dataToImport = $employeurRepository->findBy(array(),array('id'=>'DESC'));
            }
            if($dataToImport){
                $data = [];
                foreach ($dataToImport as $value) {
                    $data[] = [
                        'Siren'=> $value->getSiren(),
                        'Siret'=> $value->getSiret(),
                        'Raison Sociale'=> $value->getRaisonSociale(),
                        'Libellé'=> $value->getLibelle(),
                        'Département'=> $value->getDepartement(),
                        'Adresse'=> $value->getAdresse().' '.$value->getCodePostal().' '.$value->getVille(),
                    ];
                }
                //dd($idToImport);
        
                $spreadsheet = new Spreadsheet();
                
                /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
                $sheet = $spreadsheet->getActiveSheet()->fromArray($data,null,'A2');
                $sheet->setCellValue('A1', 'Siren');
                $sheet->setCellValue('B1', 'Siret');
                $sheet->setCellValue('C1', 'Raison Sociale');
                $sheet->setCellValue('D1', 'Libellé');
                $sheet->setCellValue('E1', 'Département');
                $sheet->setCellValue('F1', 'Adresse');
                $sheet->setTitle("Employeurs");
                
                // Create your Office 2007 Excel (XLSX Format)
                $writer = new Xlsx($spreadsheet);
                
                // Create a Temporary file in the system
                $fileName = 'liste_des_employeurs.xlsx';
                $temp_file = tempnam(sys_get_temp_dir(), $fileName);
                
                // Create the excel file in the tmp directory of the system
                $writer->save($temp_file);
                
                // Return the excel file as an attachment
                return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
            }else{
                return $this->redirectToRoute('app_listemployeurs');
            }
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("generateBaseFileExcel", name="app_generateBaseFileExcel")
     */
    public function generateBaseFileExcel(Request $request, PopulationRepository $populationRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $dataToImport = [];
            $dataToImport = $populationRepository->findAll();

            if($dataToImport){
                $data = [];
                foreach ($dataToImport as $value) {
                    $data[] = [
                        'Nom usuel'=>$value->getNomUsuel(),
                        'Nom de naissance'=>$value->getNomNaissance(),
                        'Prénom'=>$value->getPrenom(),
                        'NNI'=>$value->getNni(),
                        'Matricule'=>$value->getMatricule(),
                        'Date début résidence'=>$value->getDebutPeriodeConso()->format('Y-m-d'),
                        'Date fin résidence'=>$value->getFinPeriodeConso()->format('Y-m-d'),
                        'Montant des taxes sur l\'énergie hors TVA'=>$value->getTaxe(),
                        'Montant total HT'=>$value->getMontant(),
                        'Type de population'=>($value->getTypePopulation()?$value->getTypePopulation()->getLibelle():''),
                        'Employeur'=>($value->getEmployeur()?$value->getEmployeur()->getLibelle():''),
                        'Dernier employeur'=>($value->getDernierEmployeur()?$value->getDernierEmployeur()->getLibelle():''),
                        'Commercialisateur Pdl 1'=>($value->getCommercialisateurPdl1()?$value->getCommercialisateurPdl1()->getNumFournisseur().'-'.$value->getCommercialisateurPdl1()->getName():''),
                        'Commercialisateur Pdl 2'=>($value->getCommercialisateurPdl2()?$value->getCommercialisateurPdl2()->getNumFournisseur().'-'.$value->getCommercialisateurPdl2()->getName():''),
                        'Commercialisateur Pdl 3'=>($value->getCommercialisateurPdl3()?$value->getCommercialisateurPdl3()->getNumFournisseur().'-'.$value->getCommercialisateurPdl3()->getName():''),
                        'Departement'=>$value->getDepartement(),
                    ];
                }
        
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet()->fromArray($data,null,'A2');
                $sheet->setCellValue('A1', 'Nom usuel');
                $sheet->setCellValue('B1', 'Nom de naissance');
                $sheet->setCellValue('C1', 'Prénom');
                $sheet->setCellValue('D1', 'NNI');
                $sheet->setCellValue('E1', 'Matricule');
                $sheet->setCellValue('F1', 'Date début résidence');
                $sheet->setCellValue('G1', 'Date fin résidence');
                $sheet->setCellValue('H1', 'Montant des taxes sur l\'énergie hors TVA');
                $sheet->setCellValue('I1', 'Montant total HT');
                $sheet->setCellValue('J1', 'Type de population');
                $sheet->setCellValue('K1', 'Employeur');
                $sheet->setCellValue('L1', 'Dernier employeur');
                $sheet->setCellValue('M1', 'Commercialisateur Pdl 1');
                $sheet->setCellValue('N1', 'Commercialisateur Pdl 2');
                $sheet->setCellValue('O1', 'Commercialisateur Pdl 3');
                $sheet->setCellValue('P1', 'Departement');
                $sheet->setTitle("FELD");
                
                // Create your Office 2007 Excel (XLSX Format)
                $writer = new Xlsx($spreadsheet);
                
                // Create a Temporary file in the system
                $fileName = 'fichier_de_base.xlsx';
                $temp_file = tempnam(sys_get_temp_dir(), $fileName);
                
                // Create the excel file in the tmp directory of the system
                $writer->save($temp_file);
                
                // Return the excel file as an attachment
                return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
            }else{
                return $this->redirectToRoute('app_upload_base_file');
            }
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("deleteBaseFile", name="app_deleteBaseFile")
     */
    public function deleteBaseFile(Request $request, PopulationRepository $populationRepository): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isMethod('POST')) {
                $populationRepository->deleteAll();
                $request->getSession()->getFlashBag()->add('success','Fichier de base supprimé!');
            }
            return $this->redirectToRoute('app_upload_base_file');
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("generatePdf", name="app_generatePdf")
     */
    public function generatePdf(Request $request, ResultatRepository $resultatRepository,string $targetDirectory): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isMethod('POST')) {
                //on recupere l'id de l'utilisateur connecté
                $userConnected = $this->get('security.token_storage')->getToken()->getUser();
                $result = $resultatRepository->findOneBy(array('admin'=>$userConnected),array('id'=>'DESC'),1);
                $dateOfResultCreation = $result->getDateAdd();
                $results = $resultatRepository->findBy(array('admin'=>$userConnected,'date_add'=>$dateOfResultCreation),array('id'=>'DESC'));

                $html = $this->renderView('PDF/resultat_comparateur.html.twig', [
                    'specifications' => $results,
                    'urlImg' => base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/images/enedisgrdf.PNG')),
                    'results' => $results
                ]);

                $dompdf = new Dompdf();
 
                $dompdf->loadHtml($html);
         
                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper('A3', 'landscape');
         
                // Render the HTML as PDF
                $dompdf->render();
                // dd($dompdf);
         
                // Output the generated PDF to Browser
                $dompdf->stream("Resultat_comparaison.pdf", [
                    "Attachment" => false,
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true
                ]);

                return new Response('', 200, [
                    'Content-Type' => 'application/pdf',
                ]);
            }else{
                return $this->redirectToRoute('app_comparator');
            }
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("acte-gestion", name="app_acteGestion")
     */
    public function acteGestion(
        Request $request, 
        TypePopulationRepository $typePopulationRepository,
        FournisseurRepository $fournisseurRepository,
        EmployeurRepository $employeurRepository,
        UserRepository $userRepository,
        ActeGestionRepository $acteGestionRepository
    ): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $filter = $data = [];

            if ($request->isMethod('POST')) {
                $data['gestionnaires'] = $gestionnairesId = $request->request->get("gestionnaires");
                $data['dateDebut'] = $dateDebut = $request->request->get("dateDebut");
                $data['dateFin'] = $dateFin = $request->request->get("dateFin");
                $data['commercialisateurs'] = $commercialisateursId = $request->request->get("commercialisateurs");
                $data['employeurs'] =  $employeursId = $request->request->get("employeurs");
                $data['type'] = $typeId = $request->request->get("type");
                $data['departement'] = $departement = $request->request->get("departement");
                
                if($dateDebut != '' && $dateFin != ''){
                    $dateDebut = new \DateTime($dateDebut);
                    $dateFin = new \DateTime($dateFin);
                    $filter['dateDebut'] = $dateDebut;
                    $filter['dateFin'] = $dateFin;
                }
                if($gestionnairesId != ''){
                    $filter['gestionnaires'] = $gestionnairesId;
                }
                if($employeursId != ''){
                    $filter['employeurs'] = $employeursId;
                }
                if($commercialisateursId != ''){
                    $filter['commercialisateurs'] = $commercialisateursId;
                }
                if($departement != ''){
                    $filter['departement'] = $departement;
                }
                if($typeId != ''){
                    $filter['type'] = $typeId;
                }
                $acteGestionListe = $acteGestionRepository->findByFilter($filter);
            }else{
                $acteGestionListe = $acteGestionRepository->findBy(array(),array('id'=>'DESC'),100);
            }

            $commercilisateurs = $fournisseurRepository->findAll();
            $employeurs = $employeurRepository->findAll();
            $typePopulations = $typePopulationRepository->findAll();
            $users = $userRepository->findAll();
            return $this->render('acte_gestion.html.twig',[
                'pname'=>'Acte de gestion',
                'commercilisateurs'=>$commercilisateurs,
                'employeurs'=>$employeurs,
                'typePopulations'=>$typePopulations,
                'users'=>$users,
                'acteGestionListe'=>$acteGestionListe,
                'data'=>$data,
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }
    /**
     * @Route("historique", name="app_historique")
     */
    public function historique(
        Request $request, 
        TypePopulationRepository $typePopulationRepository,
        FournisseurRepository $fournisseurRepository,
        EmployeurRepository $employeurRepository,
        UserRepository $userRepository,
        ActeGestionRepository $acteGestionRepository,
        ResultatRepository $resultatRepository
    ): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $filter = $data = [];
            $dataResult = null;
            
            if ($request->isMethod('POST')) {
                $data['gestionnaires'] = $gestionnairesId = $request->request->get("gestionnaires");
                $data['dateDebut'] = $dateDebut = $request->request->get("dateDebut");
                $data['dateFin'] = $dateFin = $request->request->get("dateFin");
                $data['commercialisateurs'] = $commercialisateursId = $request->request->get("commercialisateurs");
                $data['employeurs'] =  $employeursId = $request->request->get("employeurs");
                $data['type'] = $typeId = $request->request->get("type");
                $data['departement'] = $departement = $request->request->get("departement");      
                
                //on recherche les actes de gestions correspondant
                if($dateDebut != '' && $dateFin != ''){
                    $dateDebut = new \DateTime($dateDebut);
                    $dateFin = new \DateTime($dateFin);
                    $filter['dateDebut'] = $dateDebut;
                    $filter['dateFin'] = $dateFin;
                }
                if($gestionnairesId != ''){
                    $filter['gestionnaires'] = $gestionnairesId;
                }
                if($employeursId != ''){
                    $filter['employeurs'] = $employeursId;
                }
                if($commercialisateursId != ''){
                    $filter['commercialisateurs'] = $commercialisateursId;
                }
                if($departement != ''){
                    $filter['departement'] = $departement;
                }
                if($typeId != ''){
                    $filter['type'] = $typeId;
                }
                $acteGestionListe = $acteGestionRepository->findByFilter($filter);
                if($acteGestionListe){
                    $dataResult = $resultatRepository->findByFilterInArray($acteGestionListe);
                }
            }
            
            $commercilisateurs = $fournisseurRepository->findAll();
            $employeurs = $employeurRepository->findAll();
            $typePopulations = $typePopulationRepository->findAll();
            $users = $userRepository->findAll();
            return $this->render('historique.html.twig',[
                'pname'=>'Historique',
                'commercilisateurs'=>$commercilisateurs,
                'employeurs'=>$employeurs,
                'typePopulations'=>$typePopulations,
                'users'=>$users,
                'data'=>$data,
                'dataResult'=>$dataResult,
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }   

   /**
    * @Route("generatePdfHistorique", name="app_generatePdfHistotiqueResult")
    */
   public function generatePdfHistotiqueResult(
        Request $request, 
        TypePopulationRepository $typePopulationRepository,
        FournisseurRepository $fournisseurRepository,
        EmployeurRepository $employeurRepository,
        UserRepository $userRepository,
        ActeGestionRepository $acteGestionRepository,
        ResultatRepository $resultatRepository
    ): Response
   {
       if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
           if ($request->isMethod('POST')) {

                $gestionnairesId = $request->request->get("gestionnaires");
                $dateDebut = $request->request->get("dateDebut");
                $dateFin = $request->request->get("dateFin");
                $commercialisateursId = $request->request->get("commercialisateurs");
                $employeursId = $request->request->get("employeurs");
                $typeId = $request->request->get("type");
                $departement = $request->request->get("departement");        
                
                //on recherche les actes de gestions correspondant
                if($dateDebut != '' && $dateFin != ''){
                    $dateDebut = new \DateTime($dateDebut);
                    $dateFin = new \DateTime($dateFin);
                    $filter['dateDebut'] = $dateDebut;
                    $filter['dateFin'] = $dateFin;
                }
                if($gestionnairesId != ''){
                    $filter['gestionnaires'] = $gestionnairesId;
                }
                if($employeursId != ''){
                    $filter['employeurs'] = $employeursId;
                }
                if($commercialisateursId != ''){
                    $filter['commercialisateurs'] = $commercialisateursId;
                }
                if($departement != ''){
                    $filter['departement'] = $departement;
                }
                if($typeId != ''){
                    $filter['type'] = $typeId;
                }
                $acteGestionListe = $acteGestionRepository->findByFilter($filter);
                $results = null;
                if($acteGestionListe){
                    $results = $resultatRepository->findByFilterInArray($acteGestionListe);
                }

               $html = $this->renderView('PDF/resultat_comparateur.html.twig', [
                   'specifications' => $results,
                   'urlImg' => base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/images/enedisgrdf.PNG')),
                   'results' => $results
               ]);

               $dompdf = new Dompdf();

               $dompdf->loadHtml($html);
        
               // (Optional) Setup the paper size and orientation
               $dompdf->setPaper('A3', 'landscape');
        
               // Render the HTML as PDF
               $dompdf->render();
               // dd($dompdf);
        
               // Output the generated PDF to Browser
               $dompdf->stream("Resultat_comparaison.pdf", [
                   "Attachment" => false,
                   'isHtml5ParserEnabled' => true,
                   'isRemoteEnabled' => true
               ]);

               return new Response('', 200, [
                   'Content-Type' => 'application/pdf',
               ]);
            }else{
               return $this->redirectToRoute('app_generatePdfHistotiqueResult');
            }
       }else{
           return $this->redirectToRoute('app_login');
       }
   }
}
