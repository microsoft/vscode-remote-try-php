<?php
namespace App\Repositories;
use App\Enums\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Enum;

abstract class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    public function getList(){
        return $this->model->paginate(Base::page);
    }
    public function getAll(){
        return $this->model->all();
    }
    public function create(array $data){
        return $this->model->create($data);
    }
    public function delete($data){
        return $this->model->findOrFail($data)->delete();
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function update($id, $attributes = [])
    {
        return $this->findOrFail($id)->update($attributes);
    }
}


