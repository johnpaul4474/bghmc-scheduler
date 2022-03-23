<?php

namespace App\Model\Nora;

use Illuminate\Database\Eloquent\Model;

class noraPatient extends Model
{
    protected $table = 'nora.paul.nora_patients';
    protected $primaryKey ='id';
    public $timestamps = false ;

    protected $fillable = [
        'id','enccode', 'patientNoraHpercode', 'patlast', 'patfirst','patmiddle', 'patage', 'patsex', 'patward', 'adm_date', 'adm_time', 'entry_by'
    ];

    public function reservation() {
        return $this->hasMany('App\Model\Reservation','id');
    }
}
