<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'name',
        'phone',
        'address'
    ];

    public function personOfPatient(): HasMany
    {
        return $this->hasMany(Patient::class, 'patient_id', 'id');
    }

    public function getPersonName($name)
    {
        return Person::query()
            ->where('name', 'LIKE', "$name")
            ->get();
    }

    public function singlePerson($persons): Person
    {
        $new_person = new Person();
        foreach ($persons as $person)
        {
            $new_person = $person;
        }

        return $new_person;
    }
}
