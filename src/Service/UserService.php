<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Command;
use App\Entity\DetailsCommand;
use App\Repository\ProductRepository;
use App\Repository\CommandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserService
{

    private EntityManagerInterface $em;
    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $passwordHasher;
    private MailerInterface $mailer;
    private JWTTokenManagerInterface $jwtManager;
    private ProductRepository $productRepository;
    private CommandRepository $commandRepository;

    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer,
        JWTTokenManagerInterface $jwtManager,
        ProductRepository $productRepository,
        CommandRepository $commandRepository
    ) {
        $this->em = $em;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
        $this->jwtManager = $jwtManager;
        $this->productRepository = $productRepository;
        $this->commandRepository = $commandRepository;
    }


    /**
     * login compte client
     * 
     * @param array $data Les données du client (email,password)
     * @return User L'utilisateur créé
     * @throws \Exception Si les données sont invalides ou si l'email existe déjà
     */

    public function executeLogin(array $data)
    {
        // Démarrage de la transaction pour assurer la cohérence des données
        $this->em->beginTransaction();
        try {

            // Obtention du user
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

            // Vérifications
            if ($user && !$user->getisActive()) {
                throw new \Exception('Impossible de se connecter, ce compte est désactivé');
            }

            if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
                throw new \Exception('Email ou mot de passe invalide');
            }

            // Générer le jwt
            $token = $this->jwtManager->create($user);

            return [
                $token,
                $user,
            ];
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
    /**
     * Crée un nouveau compte client
     * 
     * @param array $data Les données du client (email, firstName, lastName, address, password)
     * @return User L'utilisateur créé
     * @throws \Exception Si les données sont invalides ou si l'email existe déjà
     */
    public function executeCreateCustomer(array $data): User
    {
        // Démarrage de la transaction pour assurer la cohérence des données
        // Si une erreur survient pendant la création ou l'envoi de l'email, 
        // toutes les opérations seront annulées
        $this->em->beginTransaction();
        try {
            // Définition des règles de validation pour l'email
            $constraints = new Assert\Collection([
                'email' => [
                    new Assert\NotBlank([
                        'message' => 'L\'email est obligatoire.'
                    ]),
                    new Assert\Email([
                        'message' => 'L\'adresse email n\'est pas valide.'
                    ]),
                    new Assert\Length([
                        'max' => 180,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ]);

            // Validation de l'email
            $emailViolations = $this->validator->validate(['email' => $data['email']], $constraints);
            if (count($emailViolations) > 0) {
                throw new \Exception($emailViolations[0]->getMessage());
            }

            //----- Vérification de l'unicité de l'email
            $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

            if ($existingUser) {
                // Si l'utilisateur existe mais n'est pas de type CUSTOMER, on le met à jour
                if ($existingUser->getTypeUser() === 'NO_CUSTOMER') {
                    $existingUser->setFirstName($data['firstName'])
                        ->setLastName($data['lastName'])
                        ->setAddress($data['address'])
                        ->setNumeroTel($data['numero']);

                    
                    // Génération du code et expiration
                    $code = $this->generateValidationCode();
                    $expiresAt = (new \DateTime())->add(new \DateInterval('PT10M'));

                    //--- Hashage du mot de passe
                    $passwordHashed = $this->passwordHasher->hashPassword($existingUser, $data['password']);
                    $existingUser->setPassword($passwordHashed)
                        ->setValidationCode($code)
                        ->setCodeExpiresAt($expiresAt)
                        ->setTypeUser('CUSTOMER')
                        ->setRoles(['ROLE_CUSTOMER']);

                    $this->em->flush(); // Sauvegarde des modifications
                    $this->em->commit(); // Validation de la transaction

                    // Envoi de l'email
                    $this->sendValidationEmail($data['email'], $existingUser->getValidationCode(), $data['firstName'] . ' ' . $data['lastName']);

                    return $existingUser;
                }

                throw new \Exception('Inscription impossible, l\'email existe déjà.');
            }

            //-----Validation des données restantes
            $user = new User();
            $user->setFirstName($data['firstName'])
                ->setLastName($data['lastName'])
                ->setEmail($data['email'])
                ->setAddress($data['address'])
                ->setNumeroTel($data['numero'])
                ->setRoles(['ROLE_CUSTOMER'])
                ->setTypeUser('CUSTOMER');

            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                throw new \Exception('Données invalides');
            }

            //---Hashage du mot de passe
            $passwordHashed = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($passwordHashed);

            // Génération du code et expiration
            $code = $this->generateValidationCode();
            $expiresAt = (new \DateTime())->add(new \DateInterval('PT10M'));

            // Mise à jour de l'utilisateur
            $user->setValidationCode($code)
                ->setCodeExpiresAt($expiresAt);

            //----Sauvegarde dans la bd
            $this->em->persist($user);
            $this->em->flush();


            // Envoi de l'email
            $this->sendValidationEmail($data['email'], $code, $data['firstName'] . ' ' . $data['lastName']);

            // Si tout s'est bien passé, on valide la transaction
            $this->em->commit();

            return $user;
        } catch (\Exception $e) {
            // En cas d'erreur, on annule toutes les modifications
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * Met à jour les informations d'un utilisateur existant
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @param array $data Les nouvelles données (email, firstName, lastName, address)
     * @return User L'utilisateur mis à jour
     * @throws \Exception Si l'utilisateur n'existe pas ou si les données sont invalides
     */
    public function executeUpdateUser(int $id, array $data): User
    {
        $this->em->beginTransaction();
        try {
            $user = $this->em->getRepository(User::class)->find($id);
            if (!$user) {
                throw new \Exception('Utilisateur non trouvé');
            }

            // Définition des règles de validation pour l'email
            $constraints = new Assert\Collection([
                'email' => [
                    new Assert\NotBlank([
                        'message' => 'L\'email est obligatoire.'
                    ]),
                    new Assert\Email([
                        'message' => 'L\'adresse email n\'est pas valide.'
                    ]),
                    new Assert\Length([
                        'max' => 180,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ]);


            $user->setFirstName($data['firstName'])
                ->setLastName($data['lastName'])
                ->setEmail($data['email'])
                ->setAddress($data['address'])
                ->setNumeroTel($data['numero']);

            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                throw new \Exception('Données invalides');
            }

            //---Hashage du mot de passe
            $passwordHashed = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($passwordHashed);

            $this->em->flush();
            $this->em->commit();

            return $user;
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * Supprime un utilisateur
     * 
     * @param int $id L'identifiant de l'utilisateur à supprimer
     * @throws \Exception Si l'utilisateur n'existe pas
     */
    public function executeDeleteUser(int $id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new \Exception('Utilisateur non trouvé');
        }

        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * Récupère la liste de tous les utilisateurs
     * 
     * @return array Liste des utilisateurs avec leurs informations de base
     */
    public function executeListUsers(): array
    {
        $users = $this->em->getRepository(User::class)->findAll();

        $usersArray = [];
        foreach ($users as $user) {
            $usersArray[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'address' => $user->getAddress(),
                'roles' => $user->getRoles(),
                'numero' => $user->getNumeroTel(),
                'typeUser' => $user->getTypeUser(),
            ];
        }

        return $usersArray;
    }

    /**
     * Génère un code de validation aléatoire à 6 chiffres
     * 
     * @return string Le code de validation généré
     */
    private function generateValidationCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    /**
     * Envoie un email contenant le code de validation
     * 
     * @param string $email L'adresse email du destinataire
     * @param string $code Le code de validation
     * @param string $names Le nom complet du destinataire
     */
    private function sendValidationEmail(string $email, string $code, string $names): void
    {
        // Détermination du moment de la journée
        $hour = (int) (new \DateTime())->format('H');
        $greeting = ($hour >= 6 && $hour < 18) ? 'Bonjour' : 'Bonsoir'; // Condition pour choisir "Bonjour" ou "Bonsoir"

        // Construction de l'email
        $emailMessage = (new Email())
            ->from('zigiservice8@gmail.com') // L'expéditeur
            ->to($email) // Destinataire
            ->subject('Votre code d\'activation')
            ->html("
                <div style='max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; border: 1px solid #ddd; border-radius: 8px;'>
                    <div style='padding: 20px; text-align: center; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>
                        <h2 style='margin: 0; font-size: 18px; color: #333;'>$greeting $names,</h2>
                        <p style='margin: 5px 0; font-size: 14px; color: #800080;'>Votre code d'activation</p>
                        <p style='margin: 10px 0; font-size: 32px; font-weight: bold; color: #333;'>$code</p>
                        <p style='margin: 5px 0; font-size: 12px; color: #666;'>Ce code expire 10 minutes après son envoi</p>
                    </div>
                    <div style='padding: 20px; font-size: 12px; color: #666;'>
                        <p style='margin: 0;'>
                            ZIGI Services ne vous adressera jamais d'e-mail pour vous demander de divulguer ou 
                            de vérifier votre mot de passe, votre numéro de carte de crédit ou de compte bancaire. Si vous 
                            recevez un e-mail suspect comportant un lien pour mettre à jour les informations de votre compte, 
                            ne cliquez pas sur le lien. Au lieu de cela, signalez cet e-mail à HomeLander Services afin que nous 
                            puissions l'examiner.
                        </p>
                        <p style='margin-top: 10px;'>À bientôt.</p>
                    </div>
                </div>
            ");

        // Envoi de l'email
        $this->mailer->send($emailMessage);
    }

    /**
     * Active le compte d'un utilisateur avec le code de validation
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @param string $code Le code de validation fourni
     * @throws \Exception Si l'utilisateur n'existe pas, si le code est invalide ou expiré
     */
    public function executeActivationAccount(int $id, string $code): void
    {
        //Verifier l'existence du user
        $existingUser = $this->em->getRepository(User::class)->find($id);

        if (!$existingUser) {
            throw new \Exception('L\'utilisateur n\'existe pas');
        }

        //Verification de la validite du code
        $expiresAt = $existingUser->getCodeExpiresAt();
        $validCode = $existingUser->getValidationCode();

        // Vérification si le code n'a pas expiré
        $timezone = new \DateTimeZone(date_default_timezone_get()); // Utilise le fuseau horaire par défaut de PHP
        $now = new \DateTime();

        if ($now > $expiresAt) {
            throw new \Exception('Le code d\'activation a expiré. Veuillez demander un nouveau code.');
        }

        // Vérification si le code fourni correspond
        if ($code !== $validCode) {
            throw new \Exception('Le code d\'activation est incorrect.');
        }

        // Si tout est valide, activer le compte
        $existingUser->activate();

        // Sauvegarder les modifications
        $this->em->flush();
    }

    /**
     * Génère et envoie un nouveau code de validation à l'utilisateur
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @throws \Exception Si l'utilisateur n'existe pas ou si le compte est déjà activé
     */
    public function executerequestNewCode(int $id): void
    {
        $this->em->beginTransaction();
        try {
            $user = $this->em->getRepository(User::class)->find($id);

            if (!$user) {
                throw new \Exception('L\'utilisateur n\'existe pas');
            }

            if ($user->getisActive()) {
                throw new \Exception('Le compte est déjà activé');
            }

            $code = $this->generateValidationCode();
            $expiresAt = (new \DateTime())->add(new \DateInterval('PT10M'));

            $user->setValidationCode($code)
                ->setCodeExpiresAt($expiresAt);

            $this->em->flush();

            $this->sendValidationEmail(
                $user->getEmail(),
                $code,
                $user->getFirstName() . ' ' . $user->getLastName()
            );

            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }


    public function countTotalUsers(): int
    {
        return $this->em->getRepository(User::class)->count();
    }

    public function countUsersByType(): array
    {
        return $this->em->createQueryBuilder()
            ->select('u.typeUser, COUNT(u.id) as count')
            ->from(User::class, 'u')
            ->groupBy('u.typeUser')
            ->getQuery()
            ->getResult();
    }

    /*public function countRegistrations(string $period): array
    {
        $intervalSpec = match ($period) {
            'day' => 'P1D',
            'week' => 'P1W',
            'month' => 'P1M',
            default => throw new \InvalidArgumentException('Invalid period'),
        };
    
        $startDate = (new \DateTime())->sub(new \DateInterval($intervalSpec))->format('Y-m-d H:i:s');
    
        $results = $this->em->createQueryBuilder()
            ->select("SUBSTRING(u.createdAt, 1, 10) as date, COUNT(u.id) as count")
            ->from(User::class, 'u')
            ->where('u.createdAt >= :startDate')
            ->setParameter('startDate', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->getQuery()
            ->getResult();
    
        // Reformater les résultats
        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[$result['date']] = (int) $result['count'];
        }
    
        return [
            'period' => $period,
            'data' => $formattedResults,
        ];
    }
    
*/
    public function countValidatedAccounts(): int
    {
        return $this->em->getRepository(User::class)->count(['isActive' => true]);
    }
}
