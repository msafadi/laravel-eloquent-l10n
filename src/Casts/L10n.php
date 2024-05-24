<?php
 
namespace Safadi\Eloquent\L10n\Casts;
 
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
 
class L10n implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     * @return string
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): string
    {
        $json = json_decode($value, true);
        return $json[$model->getLocale()] ?? null;
    }
 
    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        $data = json_decode($model->getRawOriginal($key, '[]'), true);
        $dirty = $model->getDirty();
        $data = array_merge($data, json_decode($dirty[$key] ?? '[]', true));
        
        if (!is_array($value)) {
            $data[$model->getLocale()] = $value;
        } else {
            $data = $value;
        }
        return json_encode($data);
    }
}