<?php


namespace Vanssa\BoricaSyliusPlugin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class SyliusGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('epay_min', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.epay.min',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('secret', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.epay.secret',
                'help' => 'This is secret key',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('epay_seller_email', EmailType::class, [
                'label' => 'sylius.form.gateway_configuration.epay.seller_email',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('has_demo', CheckboxType::class, [
                'label' => 'sylius.form.gateway_configuration.epay.sandbox',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $data['payum.http_client'] = '@sylius.payum.http_client';
            });


    }
}
