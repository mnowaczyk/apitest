<?php

namespace ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label'=>'title',
            'constraints'=>[
                new NotBlank(),
                new Length(['max'=>'255']),
            ]
        ]);
        $builder->add('content', TextType::class, [
            'label'=>'title',
            'constraints'=>[
                new NotBlank(),
                new Length(['max'=>'1000']),
            ]
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=>'ApiBundle\Entity\Message',
            'csrf_protection' => false
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'message';
    }
}
