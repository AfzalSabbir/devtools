@php echo "<?php"
@endphp


namespace {{ $namespace }};

use {{$parentClass}} as GeneratedFactories;


class {{ $classname }} extends GeneratedFactories\{{$classname}}
{
    {{$model}}
    
}

