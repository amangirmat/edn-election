<?php
namespace Botble\EdnElection\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\EdnElection\Models\Woreda;
use Botble\EdnElection\Models\Zone;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;

class WoredaForm extends FormAbstract
{
    public function buildForm(): void
    {
        $zones = Zone::query()->pluck('name', 'id')->all();

        $this
            ->setupModel(new Woreda())
            ->add('name', TextField::class, [
                'label' => 'Woreda Name',
                'required' => true,
            ])
            ->add('zone_id', SelectField::class, [
                'label' => 'Zone',
                'required' => true,
                'choices' => $zones,
                'attr' => ['class' => 'select-search-full'], // Makes it searchable
            ])
            ->add('total_voters', NumberField::class, [
                'label' => 'Expected Total Voters',
                'attr' => ['placeholder' => '0']
            ]);
    }
}