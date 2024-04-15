<?php

namespace Database\Seeders;

use App\Models\ReadingGroup;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReadingGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 5 readingGroups
        ReadingGroup::factory(5)->create();
    }
}
