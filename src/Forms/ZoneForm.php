<?php
namespace Botble\EdnElection\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\EdnElection\Models\Zone;
use Botble\EdnElection\Models\Region;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\SelectField;

class ZoneForm extends FormAbstract
{
    public function buildForm(): void
    {
        $regions = Region::query()->pluck('name', 'id')->all();

        $this
            ->setupModel(new Zone())
            ->add('name', TextField::class, [
                'label' => 'Zone Name',
                'required' => true,
            ])
            ->add('region_id', SelectField::class, [
                'label' => 'Region',
                'required' => true,
                'choices' => $regions,
            ]);
    }
}