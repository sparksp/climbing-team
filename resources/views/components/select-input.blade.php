@props(['options', 'value', 'lang' => ':name', 'except' => []])

@php
    // old('value') is not automatically cast to the Enum
    if (is_string($value)) {
        $value = $options::tryFrom($value);
    }
@endphp

<select {!! $attributes->merge([
    'class' =>
        'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm',
    'style' => 'color-scheme: light dark',
]) !!}>
    @foreach ($options::cases() as $option)
        @unless (in_array($option, $except))
            <option value="{{ $option->value }}" @selected($value == $option)>
                {{ __(
                    Str::swap(
                        [
                            ':name' => $option->name,
                            ':value' => $option->value,
                        ],
                        $lang,
                    ),
                ) }}
            </option>
        @endunless
    @endforeach
</select>