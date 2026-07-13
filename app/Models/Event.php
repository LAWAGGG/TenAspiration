<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['name', 'description', 'date', 'is_hidden'];

    protected function casts(): array
    {
        return ['is_hidden' => 'boolean'];
    }

    public function aspiration(){
        return $this->hasMany(AspirationEvent::class);
    }

    public function questions(){
        return $this->hasMany(FormQuestion::class, 'entity_id')->where('form_type', 'event');
    }

    public function getCustomQuestionsAttribute(){
        return FormQuestion::getForForm('event', $this->id);
    }
}
