<?php
namespace CodeProject\Entities;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
class Client extends Model implements Transformable
{
    use TransformableTrait;
    protected $fillable = [
        'name',
        'responsible',
        'email',
        'phone',
        'address',
        'obs'
    ];
    public function projects()
    {
        return$this->hasMany(Project::class);
    }
}