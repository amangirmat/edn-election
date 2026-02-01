<?php

namespace Botble\EdnElection\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\EdnElection\Models\Candidate;
use Botble\EdnElection\Models\Woreda;
use Botble\EdnElection\Models\Party;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\MediaImageField;

class CandidateForm extends FormAbstract
{
    public function buildForm(): void
    {
        $parties = Party::query()->pluck('name', 'id')->all();

        // Fetch Woredas with Zone and Region info
        $allWoredas = Woreda::with(['zone.region'])->get();
        
        $groupedWoredas = [];
        foreach ($allWoredas as $woreda) {
            $regionName = $woreda->zone->region->name ?? 'Unknown Region';
            $zoneName = $woreda->zone->name ?? 'Unknown Zone';
            $groupLabel = "{$regionName} — {$zoneName}";
            
            $groupedWoredas[$groupLabel][$woreda->id] = $woreda->name;
        }

        $this
            ->setupModel(new Candidate())
            ->add('name', TextField::class, [
                'label' => 'Candidate Name',
                'required' => true,
            ])
            ->add('party_id', SelectField::class, [
                'label' => 'Political Party',
                'required' => true,
                'choices' => $parties,
            ])
            ->add('image', MediaImageField::class, [
                'label' => 'Candidate Photo'
            ])
            /* We changed MultiCheckListField to SelectField */
            ->add('woredas[]', SelectField::class, [
                'label' => 'Assign to Woredas',
                'choices' => $groupedWoredas,
                'value' => $this->getModel()->woredas()->pluck('edn_woredas.id')->all(),
                'attr' => [
                    'class' => 'select-search-full',
                    'multiple' => 'multiple', // This makes it a multi-select
                ],
                'help_block' => [
                    'text' => 'You can select multiple districts.',
                ],
            ]);
    }
}