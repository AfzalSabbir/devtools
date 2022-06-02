@php echo "<?php"
@endphp


namespace {{ $namespace }};

use Cviebrock\EloquentSluggable\Sluggable;
use {{$parentClass}} as GeneratedModels;
@if($crud==true)
use Backpack\CRUD\app\Models\Traits\CrudTrait;
@endif


class {{ $classname }} extends GeneratedModels\{{$classname}}
{
    @if($crud==true)
    use CrudTrait;
    @endif

    
}

