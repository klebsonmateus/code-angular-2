<?php

namespace CodeProject\Http\Controllers;

use CodeProject\Http\Requests;
use CodeProject\Repositories\ProjectRepository;
use CodeProject\Services\ProjectService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use LucaDegasperi\OAuth2Server\Exceptions\NoActiveAccessTokenException;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;
use Prettus\Validator\Exceptions\ValidatorException;

class ProjectController extends Controller
{
    /**
     * @var ProjectRepository
     */
    private $repository;

    /**
     * @var ProjectTaskRepository
     */
    private $taskRepository;

    /**
     * @var ProjectService
     */
    private $service;


    public function __construct(ProjectRepository $repository, ProjectService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('check.project.owner', ['except' => ['index', 'store', 'show']]);
        $this->middleware('check.project.permission', ['except' => ['index','store', 'update', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
        public function index()
        {
            try
            {
                return $this->repository->findWithOwnerAndMember(\Authorizer::getResourceOwnerId());
            }
            catch(NoActiveAccessTokenException $e){
                return $this->erroMsgm('Usuário não está logado.');
            }
            catch(\Exception $e){
                return $this->erroMsgm('Ocorreu um erro ao listar os projetos. Erro: '.$e->getMessage());
            }
        }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            return $this->service->create($request->all());
        }
        catch(NoActiveAccessTokenException $e){
            return $this->erroMsgm('Usuário não está logado.');
        }
        catch(ValidatorException $e){
            $error = $e->getMessageBag();
            return [
                'error' => true,
                'message' => "Erro ao cadastrar o projeto, alguns campos são obrigatórios!",
                'messages' => $error->getMessages(),
            ];
        }
        catch(\Exception $e){
            return $this->erroMsgm('Ocorreu um erro ao cadastrar o projeto.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            if(!$this->service->checkProjectPermissions($id)){
                return $this->erroMsgm("O usuário não tem acesso a esse projeto");
            }
            return $this->repository->with(['owner','client'])->find($id);
        }
        catch(ModelNotFoundException $e){
            return $this->erroMsgm('Projeto não encontrado.');
        }
        catch(NoActiveAccessTokenException $e){
            return $this->erroMsgm('Usuário não está logado.');
        }
        catch(\Exception $e){
            return $this->erroMsgm('Ocorreu um erro ao exibir o projeto.');
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try
        {
            if(!$this->service->checkProjectOwner($id)){
                return $this->erroMsgm("O usuário não é owner desse projeto");
            }
            return $this->service->update($request->all(), $id);
        }
        catch(ModelNotFoundException $e){
            return $this->erroMsgm('Projeto não encontrado.');
        }
        catch(NoActiveAccessTokenException $e){
            return $this->erroMsgm('Usuário não está logado.');
        }
        catch(ValidatorException $e){
            $error = $e->getMessageBag();
            return [
                'error' => true,
                'message' => "Erro ao atualizar o projeto, alguns campos são obrigatórios!",
                'messages' => $error->getMessages(),
            ];
        }
        catch(\Exception $e){
            return $this->erroMsgm('Ocorreu um erro ao atualizar o projeto.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            if(!$this->service->checkProjectOwner($id)){
                return $this->erroMsgm("O usuário não é owner desse projeto");
            }
            $this->repository->skipPresenter()->find($id)->delete();
        }
        catch(QueryException $e){
            return $this->erroMsgm('Projeto não pode ser apagado pois existe um ou mais clientes vinculados a ele.');
        }
        catch(ModelNotFoundException $e){
            return $this->erroMsgm('Projeto não encontrado.');
        }
        catch(NoActiveAccessTokenException $e){
            return $this->erroMsgm('Usuário não está logado.');
        }
        catch(\Exception $e){
            return $this->erroMsgm('Ocorreu um erro ao excluir o projeto.');
        }
    }


    public function members($id)
    {
        try {

            if(!$this->service->checkProjectOwner($id)){
                return $this->erroMsgm("O usuário não é owner desse projeto");
            }

            $members = $this->repository->skipPresenter()->find($id)->members()->get();

            if (count($members)) {
                return $members;
            }
            return $this->erroMsgm('Esse projeto ainda não tem membros.');

        } catch (ModelNotFoundException $e) {
            return $this->erroMsgm('Projeto não encontrado.');
        } catch (QueryException $e) {
            return $this->erroMsgm('Cliente não encontrado.');
        } catch (\Exception $e) {
            return $this->erroMsgm('Ocorreu um erro ao exibir os membros do projeto.');
        }

    }

    public function addMember($project_id, $member_id)
    {
        try {
            if(!$this->service->checkProjectOwner($project_id)){
                return $this->erroMsgm("O usuário não é owner desse projeto");
            }
            return $this->service->addMember($project_id, $member_id);
        } catch (ModelNotFoundException $e) {
            return $this->erroMsgm('Projeto não encontrado.');
        } catch (QueryException $e) {
            return $this->erroMsgm('Cliente não encontrado.');
        } catch (\Exception $e) {
            return $this->erroMsgm('Ocorreu um erro ao inserir o membro.');
        }
    }

    public function removeMember($project_id, $member_id)
    {
        try {
            if(!$this->service->checkProjectOwner($project_id)){
                return $this->erroMsgm("O usuário não é owner desse projeto");
            }
            return $this->service->removeMember($project_id, $member_id);
        } catch (ModelNotFoundException $e) {
            return $this->erroMsgm('Projeto não encontrado.');
        } catch (QueryException $e) {
            return $this->erroMsgm('Cliente não encontrado.');
        } catch (\Exception $e) {
            return $this->erroMsgm('Ocorreu um erro ao remover o membro.');
        }
    }


    private function erroMsgm($mensagem)
    {
        return [
            'error' => true,
            'message' => $mensagem,
        ];
    }
}