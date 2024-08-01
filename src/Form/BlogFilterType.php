<?php

namespace App\Form;

use App\Entity\Category;
use App\Filter\BlogFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('title', TextType::class, ['required' => false])
            ->add('text', TextType::class, ['required' => false])
            ->add('content', TextType::class, ['required' => false, 'mapped' => false])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event): void {
                $user = $event->getData();
                $form = $event->getForm();

                if (!$user) {
                    return;
                }

                // checks whether the user has chosen to display their email or not.
                // If the data was submitted previously, the additional value that is
                // included in the request variables needs to be removed.
                if (isset($user['showEmail']) && $user['showEmail']) {
                    $form->add('email', EmailType::class);
                } else {
                    unset($user['email']);
                    $event->setData($user);
                }
            }) ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => BlogFilter::class,
            'csrf_protection' => false
        ]);
    }
}
