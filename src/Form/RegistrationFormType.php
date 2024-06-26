<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email; // Cette classe est utilisée pour vérifier que la valeur d'un champ est une adresse e-mail valide.
use Symfony\Component\Validator\Constraints\File; // Cette classe est utilisée pour valider les fichiers téléchargés via un champ de type FileType. Cette contrainte spécifie la taille maximale du fichier et les types MIME autorisés (PNG et JPEG dans cet exemple). Elle générera un message d'erreur si les conditions ne sont pas respectées.
use Symfony\Component\Validator\Constraints\IsTrue; // Utilisée pour valider les cases à cocher où l'utilisateur doit accepter des conditions spécifiques.
use Symfony\Component\Validator\Constraints\Length; // Cette classe est utilisée pour spécifier des contraintes sur la longueur d'une chaîne de caractères
use Symfony\Component\Validator\Constraints\NotBlank; // Cette classe est utilisée pour spécifier qu'un champ ne doit pas être vide.

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('email', EmailType::class, [
                'label' => "Email",
                'attr' => ['placeholder' => 'Votre email'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre email doit être renseigné']),
                    new Email(['message' => 'Veuillez renseigner un email valide!']),
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez accepter les conditions.',
                    ]),
                ],
            ])

            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            ->add('picture', FileType::class, [
                'label' => 'Image de profil',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier de type pgn ou jpeg',
                    ])
                ],
            ])

            ->add('firstName', TextType::class, [
                'label' => "Prénom",
                'attr' => ['placeholder' => 'Votre prénom'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre prénom doit être renseigné']),
                    new Length(['min' => 2, 'minMessage' => 'Votre prénom doit faire au minimum 2 caractères'])
                ],
            ])

            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre nom doit être renseigné']),
                    new Length(['min' => 2, 'minMessage' => 'Votre nom doit faire au minimum 2 caractères' ])],
            ])

            ->add('phoneNumber', TelType::class, [
                'label' => "Numéro de téléphone",
                'attr' => ['placeholder' => 'Votre numéro de téléphone'],
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez ajouter un numéro de téléphone']),
                    new Length(['min' => 10, 'minMessage' => 'Votre numéro de téléphone doit être composé de 10 caractères',
                                'max' => 10, 'maxMessage' => 'Votre numéro de téléphone doit être composé de 10 caractères'])
                ],
            ])

            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Votre adresse'],
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Votre adresse doit être renseignée']),],
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => ['placeholder' => 'Votre ville'],
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Votre ville doit être renseignée']),],
            ])

            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr' => ['placeholder' => 'Votre pays'],
                'required' => true,
            ])

            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['placeholder' => 'Votre code postal'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre code postal doit être renseigné']),
                    new Length(['min' => 5, 'minMessage' => 'Votre code postal doit faire 5 caractères',
                                'max' => 5, 'maxMessage' => 'Votre code postal doit faire 5 caractères'])],                                 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}