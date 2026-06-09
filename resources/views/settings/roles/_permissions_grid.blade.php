{{-- resources/views/settings/roles/_permissions_grid.blade.php --}}
@foreach($permissions as $group => $perms)
    @php
        $groupSlug = strtolower(str_replace([' ', '/'], ['-', '-'], $group));
    @endphp
    <div style="margin-bottom:20px">

        {{-- Group header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;
                margin-bottom:8px;padding-bottom:6px;
                border-bottom:1px solid #e5e7eb">
            <p style="font-size:12px;font-weight:600;color:#374151;
                   text-transform:capitalize">
                {{ ucfirst($group) }}
            </p>
            <div style="display:flex;gap:8px">
                <button type="button"
                        onclick="toggleGroup('{{ $groupSlug }}', true)"
                        class="text-xs text-blue-500 hover:underline">
                    All
                </button>
                <button type="button"
                        onclick="toggleGroup('{{ $groupSlug }}', false)"
                        class="text-xs text-gray-400 hover:underline">
                    None
                </button>
            </div>
        </div>

        {{-- Permission checkboxes --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
            @foreach($perms as $permission)
                <label style="display:flex;align-items:center;gap:8px;padding:7px 10px;
                       border:1px solid #e5e7eb;border-radius:7px;cursor:pointer;
                       transition:all .15s;background:#fff"
                       onmouseover="this.style.borderColor='#2563eb';
                            this.style.background='#eff6ff'"
                       onmouseout="this.style.borderColor='#e5e7eb';
                           this.style.background=this.querySelector('input').checked?'#eff6ff':'#fff'">
                    <input type="checkbox"
                           name="permissions[]"
                           value="{{ $permission->name }}"
                           class="group-{{ $groupSlug }}"
                           {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                           onchange="this.closest('label').style.background=this.checked?'#eff6ff':'#fff';
                             this.closest('label').style.borderColor=this.checked?'#2563eb':'#e5e7eb'"
                           style="width:15px;height:15px;accent-color:#2563eb;
                          flex-shrink:0;cursor:pointer">
                    <span style="font-size:11px;color:#374151;line-height:1.3">
                {{ $permission->name }}
            </span>
                </label>
            @endforeach
        </div>

    </div>
@endforeach
