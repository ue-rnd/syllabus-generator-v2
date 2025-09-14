<?php

namespace Tests\Feature;

use App\Models\Syllabus;

$allSyllabi = Syllabus::all();

// Print out all records
dump('All Syllabus Records:');
$allSyllabi->each(function ($syllabus) {
    dump($syllabus);
});