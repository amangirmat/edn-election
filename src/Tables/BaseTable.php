<?php

namespace Botble\EdnElection\Tables;

use Botble\Table\Abstracts\TableAbstract;

abstract class BaseTable extends TableAbstract
{
    protected function addImportExportButtons(string $exportTable): self
    {
        return $this->addHeaderActions([
            \Botble\Table\HeaderActions\HeaderAction::make('import_election')
                ->label('Import')
                ->url('#')
                ->attributes(['class' => 'btn-trigger-import'])
                ->icon('fas fa-file-import')
                ->color('info'),

            \Botble\Table\HeaderActions\HeaderAction::make('export_election')
                ->label('Export')
                ->url(route('edn.election.export', ['table' => $exportTable]))
                ->icon('fas fa-file-export')
                ->color('success'),
        ]);
    }

    protected function injectImportAssets(string $previewRouteName): void
    {
        // Fix: Ensure we get the string class name
        $modelClass = is_object($this->model) ? get_class($this->model) : $this->model;

        add_filter(BASE_FILTER_TABLE_AFTER_RENDER, function ($html, $table) use ($previewRouteName, $modelClass) {
            // Check if this is the correct table and not an ajax request
            if ($table instanceof self && !request()->ajax()) {
                
                $previewUrl = route($previewRouteName);
                $csrfToken = csrf_token();

                $hiddenAssets = <<<HTML
                    <form id="hidden-import-form" action="{$previewUrl}" method="POST" enctype="multipart/form-data" style="display:none;">
                        <input type="hidden" name="_token" value="{$csrfToken}">
                        <input type="hidden" name="model_class" value="{$modelClass}">
                        <input type="file" id="real-file-input" name="file" accept=".csv, .xlsx">
                    </form>
                    <script>
                        $(document).ready(function() {
                            // Use delegated events so it survives table refreshes
                            $(document).off('click', '.btn-trigger-import').on('click', '.btn-trigger-import', function(e) {
                                e.preventDefault();
                                $('#real-file-input').click();
                            });

                            $(document).off('change', '#real-file-input').on('change', '#real-file-input', function() {
                                if (this.files && this.files.length > 0) {
                                    console.log('File selected, submitting to: {$previewUrl}');
                                    if (window.Botble) Botble.showLoading();
                                    $('#hidden-import-form').submit();
                                }
                            });
                        });
                    </script>
HTML;
                return $html . $hiddenAssets;
            }
            return $html;
        }, 999, 2);
    }
}