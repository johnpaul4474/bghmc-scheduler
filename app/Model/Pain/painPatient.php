<?php

namespace App\Model\Pain;

use Illuminate\Database\Eloquent\Model;

class painPatient extends Model
{
    protected $table = 'nora.paul.pain_patients';
    protected $primaryKey ='id';
    public $timestamps = false ;

    protected $fillable = [
        'id','enccode', 'patientNoraHpercode', 'patlast', 'patfirst','patmiddle', 'patage', 'patsex', 'patward', 'adm_date', 'adm_time', 'entry_by'
    ];

    public function reservation() {
        return $this->hasMany('App\Model\Reservation','id');
    }
}
