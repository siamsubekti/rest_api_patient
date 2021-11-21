<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';

    protected $fillable = [
        'patient_id',
        'statuses_id'
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'patient_id', 'id');
    }

    public function search($name)
    {
        $person = new Person();
        $find_person = $person->getPersonName($name);
        $single_person = $person->singlePerson($find_person);
        return Patient::query()
            ->where('person_id', '=', "$single_person->id")
            ->get();
    }

    public function searchStatuses($status)
    {
        return Patient::query()
            ->where('statuses', '=', "$status")
            ->get();
    }
}
