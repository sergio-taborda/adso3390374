<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pet = new \App\Models\Pet;
        $pet->name = 'Pocho';
        $pet->kind = 'Dog';
        $pet->weight = 10.4;
        $pet->age = 3;
        $pet->breed = 'Corgi';
        $pet->location = 'Manizales';
        $pet->description = 'A friendly and loyal dog';
        $pet->save();

    $pet = new \App\Models\Pet;
    $pet->name = 'Michi';
    $pet->kind = 'Cat';
    $pet->weight = 4.5;
    $pet->age = 2;
    $pet->breed = ' Maine Coon';
    $pet->location = 'Bogota';
    $pet->description = 'A playful and curious cat';
    $pet->save();

    $pet = new \App\Models\Pet;
    $pet->name = 'Pepo';
    $pet->kind = 'Pig';
    $pet->weight = 25.2;
    $pet->age = 5;
    $pet->breed = 'Mini';
    $pet->location = 'Cali';
    $pet->description = 'A calm and affectionate pig';
    $pet->save();
    }
}
