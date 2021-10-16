<?php

use Illuminate\Database\Seeder;
use App\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::insert([ ["title" => "Inefficiency","company_id" => 0],
        ["title" => "Blocker", "company_id" => 0],
         ["title" => "Duplication", "company_id" => 0],
        ["title" => "Alignment", "company_id" => 0],
        ["title" => "Communication", "company_id" => 0],
        ["title" => "Planning", "company_id" => 0],
        ["title" => "Strategy", "company_id" => 0],
        ["title" => "Lost Opportunity", "company_id" => 0],
        ["title" => "Opportunity Cost", "company_id" => 0],
        ["title" => "Time Wasted", "company_id" => 0] ]);
    }
}
