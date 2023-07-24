<?php

namespace KlockTecnologia\KlockHelpers\Traits;

use Rappasoft\LaravelLivewireTables\Views\Column;

trait LiveWireSortableRows
{
    public $sortableConditions = [];
    public $routeParams = [];
    public $model;


    abstract function modelPath(): string;

    public function setSortableConditions($conditions)
    {
        $this->sortableConditions = $conditions;
    }

    public function sortableColumnsArray(): array
    {
        return [
            Column::make('Ordenação')
                ->format(function ($value, $column, $row) {
                    if ($row) {
                        return '<a style="padding:5px" wire:click="moveUp(\'' . $row->id . '\')" href="#"><i class="fas fa-arrow-up"></i></a>'
                            . '<a  style="padding:5px" wire:click="moveDown(\'' . $row->id . '\')" href="#"><i class="fas fa-arrow-down"></i></a>';
                    }

                    return '';
                })
                ->asHtml(),
        ];
    }


    public function moveUp($id)
    {
        $modelInstance = app($this->modelPath());

        $model = $modelInstance::find($id);

        $query = $modelInstance::query();
        foreach ($this->sortableConditions as $field => $value) {
            $query = $query->where($field, $value);
        }
        $previousModel = $query->where('order', '<', $model->order)->orderBy('order', 'desc')->first();

        if ($previousModel) {
            $tempOrder = $model->order;
            $model->order = $previousModel->order;
            $previousModel->order = $tempOrder;

            $model->save();
            $previousModel->save();
        }

        $this->emit('refreshLivewireTable');
    }

    public function moveDown($id)
    {
        $modelInstance = app($this->modelPath());

        $model = $modelInstance::find($id);

        $query = $modelInstance::query();
        foreach ($this->sortableConditions as $field => $value) {
            $query = $query->where($field, $value);
        }

        $nextModel = $query->where('order', '>', $model->order)->orderBy('order')->first();

        if ($nextModel) {
            $tempOrder = $model->order;
            $model->order = $nextModel->order;
            $nextModel->order = $tempOrder;

            $model->save();
            $nextModel->save();
        }


        $this->emit('refreshLivewireTable');
    }
}
