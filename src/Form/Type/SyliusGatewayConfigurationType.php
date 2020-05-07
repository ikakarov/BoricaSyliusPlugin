<?php


namespace Vanssa\BoricaSyliusPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
class SyliusGatewayConfigurationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {



        $builder
            ->add('borica_terminal', NumberType::class, [
                'label' => 'sylius.form.gateway_configuration.borica.borica_terminal',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('has_demo', CheckboxType::class, [
                'label' => 'sylius.form.gateway_configuration.borica.sandbox',
            ])
            ->add('borica_etlog_certificate', TextareaType::class, [
                'label' => 'sylius.form.gateway_configuration.borica.borica_etlog_certificate',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('borica_etlog_private_key', TextareaType::class, [
                'label' => 'sylius.form.gateway_configuration.borica.borica_etlog_private_key',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('borica_real_certificate', TextareaType::class, [
                'label' => 'sylius.form.gateway_configuration.borica.borica_real_certificate',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('borica_real_private_key', TextareaType::class, [
                'label' => 'sylius.form.gateway_configuration.borica.borica_real_private_key',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('borica_test_certificate', TextareaType::class, [
                'label' => 'sylius.form.gateway_configuration.borica.borica_test_certificate',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('borica_test_private_key', TextareaType::class, [
                'label' => 'sylius.form.gateway_configuration.borica.borica_test_private_key',
                'constraints' => [
                    new NotBlank(),
                ],
            ]) ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $data['payum.http_client'] = '@sylius.payum.http_client';
            });


    }
}
