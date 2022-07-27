<?php

namespace App\Domains\Dummy\Controllers;

use App\Domains\Dummy\Models\Dummy;
use App\Domains\Dummy\Services\DummyService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DummyController extends Controller
{
    private $dummyService;

    public function __construct(DummyService $dummyService)
    {

        $this->dummyService = $dummyService;
    }

    public function index()
    {

        return view('dummy::index');
    }

    public function create()
    {

        return view('dummy::create')->with(
            [
                'title' => 'Dummy',
                'useStatus' => true,
                'actionName' => 'Criar',
                'route' => 'admin.dummy.store',
                'rules' => []
            ]
        );
    }

    public function edit()
    {

        return view('dummy::edit')->with(
            [
                'title' => 'Dummy',
                'useStatus' => true,
                'actionName' => 'Criar',
                'route' => 'admin.dummy.update',
                'rules' => []
            ]
        );
    }

    public function store(Request $req)
    {
        try {
            $this->dummyService->store($req->all());
            return redirect()->route('admin.dummy.index')->withFlashSuccess('Registro criado com sucesso');
        } catch (\Throwable $th) {
            return redirect()->back()->withFlashDanger('Erro ao criar o registro');
        }
    }

    public function update(Dummy $dummy, Request $req)
    {

        try {

            $this->dummyService->update($req->all(), $dummy->id);
            return redirect()->route('admin.dummy.index')->withFlashSuccess('Registro atualizado com sucesso');
        } catch (\Throwable $th) {
            return redirect()->back()->withFlashDanger('Erro ao atualizar o registro');
        }
    }

    public function destroy(Dummy $dummy)
    {
        try {
            $this->dummyServiceInstance->delete($dummy->id);
            return redirect()->route('admin.dummy.index')->withFlashSuccess('Registro removido com sucesso');
        } catch (\Throwable $th) {
            return redirect()->back()->withFlashDanger('Falha ao remover o registro');
        }
    }
}
