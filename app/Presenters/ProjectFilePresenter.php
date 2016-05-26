<?php

namespace CodeProject\Presenters;

use CodeProject\Transformers\ProjectFileTransformer;
use Prettus\Repository\Presenter\FractalPresenter;


class ProjectNotePresenter extends FractalPresenter
{

    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ProjectFileTransformer();
    }
}