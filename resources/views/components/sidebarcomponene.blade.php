@if (!empty($value->submenu))
    <ul class="nav nav-treeview">
        @foreach ($value->submenu as $key => $submenu)
            @if (in_array($submenu->uniqueName, $submenuAccess))
                <li class="nav-item">
                    @foreach ($submenu->childMenu as $route)
                        @if ($route->navigate_status == 1)
                            <a href="{{ route($route->route) }}" id="{{ $route->route }}" class="nav-link ">
                                <p>{{ $submenu->label }}</p>
                            </a>
                        @endif
                    @endforeach
                </li>
            @endif
        @endforeach
    </ul>
@endif
