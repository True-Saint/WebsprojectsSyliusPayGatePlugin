<?php

declare(strict_types=1);

namespace WebsProjects\PayGatePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SyliusGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('encryption_key', TextType::class)
            ->add('Paygate_id', TextType::class)
            ->add('reference', TextType::class)
        ;

    }
}
