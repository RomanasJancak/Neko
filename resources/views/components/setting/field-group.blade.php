@props(['settings', 'values', 'fullDefinition', 'prefix'])

@foreach($settings as $key => $setting)
    @php
        $currentKey = $prefix ? $prefix . '.' . $key : $key;
        $fullSetting = data_get($fullDefinition, $currentKey);
        $label = $fullSetting['label'] ?? null;
        $type = $fullSetting['type'] ?? 'text';
    @endphp

    @if(is_array($fullSetting) && isset($fullSetting['label']))
        {{-- Leaf node with a label: render setting --}}
        <div class="mb-3">
            <label for="{{ $currentKey }}" class="form-label font-semibold">{{ $label }}</label>

            @if($type === 'select')
                <select
                    name="{{ str_replace('.', '_', $currentKey) }}"
                    id="{{ $currentKey }}"
                    class="form-control"
                >
                    @foreach($fullSetting['options'] ?? [] as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}"
                            @selected(old(str_replace('.', '_', $currentKey), $values[$currentKey] ?? '') == $optionValue)>
                            {{ $optionLabel }}
                        </option>
                    @endforeach
                </select>
            @elseif($type === 'select-muiltiple')
                <?php
                // Handle multiple select with options
               
                $decoded = [];
                if (isset($values[$currentKey]) && is_string($values[$currentKey])) {
                    $decoded = json_decode($values[$currentKey], true);
                    $values[$currentKey] = is_array($decoded) ? $decoded : [];
                } else {
                    $values[$currentKey] = is_array($values[$currentKey] ?? null) ? $values[$currentKey] : [];
                }
                 //dd($fullSetting,$currentKey,$values,$decoded);
                ?>
                <select
                    name="{{ str_replace('.', '_', $currentKey) }}[]"
                    id="{{ $currentKey }}"
                    multiple
                    class="form-control"
                >
                    @foreach($fullSetting['options'] ?? [] as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}"
                            @if(in_array($optionValue, old(str_replace('.', '_', $currentKey), $values[$currentKey] ?? [])))
                                selected
                            @endif>
                            {{ $optionLabel }}
                        </option>
                    @endforeach
                </select>
            @else
                <input
                    type="{{ $type }}"
                    name="{{ str_replace('.', '_', $currentKey) }}"
                    id="{{ $currentKey }}"
                    value="{{ old(str_replace('.', '_', $currentKey), $values[$currentKey] ?? '') }}"
                    class="form-control"
                />
            @endif

            @error(str_replace('.', '_', $currentKey))
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    @elseif(is_array($setting))
        {{-- Still nested array, recurse to find more settings --}}
        <x-setting.field-group
            :settings="$setting"
            :values="$values"
            :fullDefinition="$fullDefinition"
            :prefix="$currentKey"
        />
    @endif
@endforeach
