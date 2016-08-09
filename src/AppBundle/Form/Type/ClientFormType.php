<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use AppBundle\Entity\Client;

/**
 * @author Oleg Proshin <oleg.prosheen@opensoftdev.ru>
 */
class ClientFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['required' => true])
                ->add('surname', TextType::class, ['required' => true])
                ->add('phone', TextType::class, ['required' => true])
            ->add('status', ChoiceType::class, [
               'choices' => [
                   Client::STATUS_NEW => Client::STATUS_NEW,
                   Client::STATUS_REGISTERED => Client::STATUS_REGISTERED,
                   Client::STATUS_DENIED => Client::STATUS_DENIED,
                   Client::STATUS_UNAVAILABLE => Client::STATUS_UNAVAILABLE
                  ]
                ])
                ->add('datetime', DateTimeType::class, array(
                'required' => true
            ));

    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'clients';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Client::class]);
    }
}

