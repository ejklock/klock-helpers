<?php

namespace KlockTecnologia\KlockHelpers\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use KlockTecnologia\KlockHelpers\Models\Media;

abstract class AbstractModelService
{

    /** @var Model */
    protected $model;


    protected $diskName;


    public function __construct()
    {

        $this->diskName  = 'public';

        $this->model = $this->model();
    }

    public abstract function model(): Model;

    public function getModel(): Model
    {

        return $this->model;
    }


    public function setDisk($diskName)
    {

        $this->diskName = $diskName;
    }


    public function getAll(): Builder
    {
        return $this->model()->whereNull('deleted_at');
    }

    public function findOne($id)
    {
        return $this->model()->whereId($id)->firstOrFail();
    }

    protected function handleAttachModel($attach)
    {
        if ($attach) {
            foreach ($attach as $key => $value) {
                foreach ($value as $k => $v) {
                    $this->model->$key()->attach($v);
                }
            }
        }
    }

    protected function storeOnDisk(Media $media)
    {
        try {

            Storage::disk($this->diskName)->put($media->getGeneratedFileName(), $media->getFilePath());
        } catch (\Throwable $th) {
            throw new \Exception("Erro ao Salvar no Disco: " . $th->getMessage());
        }
    }

    protected function handleMedia(Media $media = null)
    {
        if ($media) {
            if ($this->diskName && !$media->getMediaCollection()) {
                $this->storeOnDisk($media);
            } else {;
                $this->model->addMedia($media->getFilePath())->usingFileName($media->getGeneratedFileName())->toMediaCollection($media->getMediaCollection());
            }
        }
    }

    protected function handleSyncModel($sync)
    {
        if ($sync) {
            foreach ($sync as $key => $value) {
                foreach ($value as $k => $v) {
                    $this->model()->$key()->sync($v);
                }
            }
        }
    }

    public function store(array $data, Media $media = null, $attach = null, $sync = null): Model
    {

        try {
            DB::beginTransaction();
            $this->model = $this->model()->create($data);
            $this->handleMedia($media);
            $this->handleAttachModel($attach);
            $this->handleSyncModel($sync);
            DB::commit();
            return $this->model();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new \Exception("Erro ao Salvar: " . $th->getMessage());
        }
    }

    public function update(array $data, $id, Media $media = null, $attach = null, $sync = null): Model
    {
        try {
            DB::beginTransaction();
            $this->model = $this->findOne($id);
            $this->model->fill($data);

            $this->handleMedia($media);
            $this->handleAttachModel($attach);
            $this->handleSyncModel($sync);
            $this->model->save();
            DB::commit();
            return $this->model;
        } catch (\Throwable $th) {

            DB::rollBack();
            throw new \Exception("Erro ao Atualizar: " . $th->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->model = $this->findOne($id);
            $this->model->delete();
        } catch (\Throwable $th) {
            throw new \Exception("Erro ao Deletar: " . $th->getMessage());
        }
    }

    public function destroy(Model $model)
    {
        try {
            $model->delete();
        } catch (\Throwable $th) {
            throw new \Exception("Erro ao Deletar: " . $th->getMessage());
        }
    }
}
