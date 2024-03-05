<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AutomationMenu extends Model
{
    public function child()
    {
        return $this->hasMany(AutomationMenu::class, "parent_id", "id");
    }
}
