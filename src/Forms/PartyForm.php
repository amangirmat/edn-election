<?php

namespace Botble\EdnElection\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\EdnElection\Models\Party;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\MediaImageField;

class PartyForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->setupModel(new Party())
            ->withCustomFields()
            ->add('name', TextField::class, [
                'label' => 'Party Name',
                'required' => true,
                'attr' => ['placeholder' => 'Prosperity Party']
            ])
            ->add('abbreviation', TextField::class, [
                'label' => 'Abbreviation',
                'attr' => ['placeholder' => 'PP']
            ])
            ->add('color', ColorField::class, [
                'label' => 'Party Color (for Map)',
                'default_value' => '#000000'
            ])
            ->add('logo', MediaImageField::class, [
                'label' => 'Party Logo'
            ]);
    }
}