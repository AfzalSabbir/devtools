<?php

namespace KDA\Backpack\Models\Traits;


trait ExtendsCast {

    public function initializeExtendsCast(): void
    {
        
        if(property_exists($this,'recasts')){
            foreach($this->recasts as $key => $cast){
                $this->casts[$key] = $cast;

            }
        }
       
    }
    
}