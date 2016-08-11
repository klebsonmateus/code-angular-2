<?php
namespace CodeProject\Http\Controllers;
use CodeProject\Http\Requests;
use CodeProject\Repositories\ProjectFileRepository;
use CodeProject\Services\ProjectFileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Contracts\FileSystem\Factory;
use LucaDegasperi\OAuth2Server\Exceptions\NoActiveAccessTokenException;
class ProjectFileController extends Controller
{
    /**
     * @var ProjectFileRepository
     */
    private $repository;
    /**
     * @var ProjectFileService
     */
    private $service;


    /**
     * @var \Illuminate\Contracts\FileSystem\Factory
     */
    private $storage;

    public function __construct(ProjectFileRepository $repository, ProjectFileService $service, Factory $storage)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->storage = $storage;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        return $this->repository->findWhere(['project_id' => $id]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('file');
        if (!$file) {
            return $this->erroMsgm("O arquivo é obrigatório!");
        }
        $extension = $file->getClientOriginalExtension();
        $data['file'] = $file;
        $data['extension'] = $extension;
        $data['name'] = $request->name;
        $data['description'] = $request->description;
        $data['project_id'] = $request->project_id;
        return $this->service->create($data);
    }
    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $fileId)
    {

        $result = $this->repository->findWhere(['project_id'=>$id, 'id'=>$fileId]);
        if(isset($result['data']) && count($result['data'])==1) {
            $result = [
            'data' => $result['data'][0]
            ];
        }
        return $result;

        //return $this->repository->find($fileId);
    }
    public function showFile($projectId, $id)
    {
        $model = $this->repository->skipPresenter()->find($id);
        $filePath = $this->service->getFilePath($id);
        $fileContent = file_get_contents($filePath);
        $file64 = base64_encode($fileContent);
        return [
        'file' => $file64,
        'size' => filesize($filePath),
        'name' => $this->service->getFileName($id),
        'mime_type' => $this->storage->mimeType($model->getFileName())
        ];
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $fileId)
    {
        try {

            $data = $request->all();
            $data['project_id'] = $id;
            return $this->service->update($data, $fileId);

            //return $this->service->update($request->all(), $fileId);
        } catch (ModelNotFoundException $e) {
            return $this->erroMsgm('Projeto não encontrado.');
        } catch (NoActiveAccessTokenException $e) {
            return $this->erroMsgm('Usuário não está logado.');
        } catch (\Exception $e) {
            return $this->erroMsgm('Ocorreu um erro ao atualizar o projeto.');
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $fileId)
    {

        if($this->repository->skipPresenter()->find($fileId)->delete()){
            return ['success'=>true, 'message'=>'Arquivo '.$fileId.' excluído com sucesso!'];
        }
        return ['error'=>true, 'message'=>'Não foi possível excluir o arquivo '.$fileId];


        /*
        $this->service->delete($id);
        return ['error'=>false,'Arquivo deletado com sucesso'];
        */
    }
    private function erroMsgm($mensagem)
    {
        return [
        'error' => true,
        'message' => $mensagem,
        ];
    }
}