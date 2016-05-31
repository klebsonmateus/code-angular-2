<?php

use Illuminate\Database\Seeder;

class ProjectNoteTableSeeder extends Seeder
{
    public function run()
    {
        // \CodeProject\Entities\Project::truncate();
        factory(\CodeProject\Entities\ProjectNote::class, 100)->create();
    }
}
