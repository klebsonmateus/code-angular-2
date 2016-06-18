<?php

namespace CodeProject\Transformers;

use CodeProject\Entities\ProjectTask;
use League\Fractal\TransformerAbstract;

class ProjectTaskTransformer extends TransformerAbstract
{
    public function transform(ProjectTask $task)
    {
        return [
            'id' => $task->id,
            'project_id' => $task->project_id,
            'name' => $task->name,
            'status' => $task->status,
            'start_date' => $task->start_date,
            'due_date' => $task->due_date,
        ];
    }

}