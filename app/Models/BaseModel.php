<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * App\Models\BaseModel
 *
 * @method static Builder|BaseModel newModelQuery()
 * @method static Builder|BaseModel newQuery()
 * @method static Builder|BaseModel query()
 * @mixin \Eloquent
 */
class BaseModel extends Model
{
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * 是否使用软删除
     * 子模型可以设置：
     * protected static bool $useSoftDeletes = false;
     */
    protected static bool $useSoftDeletes = true;

    /**
     * 软删除字段名
     */
    public const DELETED_AT = 'deleted_at';

    /**
     * Booted 方法，动态应用软删除作用域
     */
    protected static function booted()
    {
        if (static::$useSoftDeletes) {
            static::addGlobalScope('softDeletes', function (Builder $builder) {
                // 自动加表前缀，避免 SQL 歧义
                $builder->whereNull($builder->qualifyColumn(static::DELETED_AT));
            });
        }
    }

    /**
     * 覆盖 newModelQuery()，在不使用软删除时去掉自定义全局作用域
     */
    public function newModelQuery()
    {
        $query = parent::newModelQuery();

        if (!static::$useSoftDeletes) {
            $query->withoutGlobalScope('softDeletes');
        }

        return $query;
    }

    /**
     * 获取 deleted_at 字段名称
     */
    public function getDeletedAtColumn(): string
    {
        return static::DELETED_AT;
    }

    /**
     * 删除模型
     * 支持可选软删除
     */
    public function delete()
    {
        if ($this->exists && static::$useSoftDeletes) {
            $this->{$this->getDeletedAtColumn()} = Carbon::now();
            return $this->save();
        }

        return parent::delete();
    }

    /**
     * 强制删除（绕过软删除）
     */
    public function forceDelete()
    {
        return parent::delete();
    }

    /**
     * 创建新实例
     */
    public static function new()
    {
        return new static();
    }

    /**
     * 重写 toArray 方法，下划线转驼峰并过滤 null
     */
    public function toArray()
    {
        $items = parent::toArray();
        $items = array_filter($items, fn($item) => !is_null($item));
        $keys = array_map(fn($key) => lcfirst(Str::studly($key)), array_keys($items));
        $values = array_values($items);
        return array_combine($keys, $values);
    }

    /**
     * 乐观锁更新（compare and save）
     *
     * @return bool|int
     * @throws Throwable
     */
    public function cas()
    {
        throw_if(!$this->exists, \Exception::class, 'model not exists when cas');

        $dirty = $this->getDirty();
        if (empty($dirty)) return 0;

        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
            $dirty = $this->getDirty();
        }

        $diff = array_diff(array_keys($dirty), array_keys($this->original));
        throw_if(!empty($diff), \Exception::class, 'key [ ' . implode(',', $diff) . ' ] is not exist');

        if ($this->fireModelEvent('casing') === false) return 0;

        $query = $this->newModelQuery()->where($this->getKeyName(), $this->getKey());

        foreach ($dirty as $key => $value) {
            $query->where($key, $this->getOriginal($key));
        }

        $rows = $query->update($dirty);
        if ($rows > 0) {
            $this->syncChanges();
            $this->fireModelEvent('cased', false);
            $this->syncOriginal();
        }

        return $rows;
    }

    /**
     * Register a casing model event
     */
    public static function casing($callback)
    {
        static::registerModelEvent('casing', $callback);
    }

    /**
     * Register a cased model event
     */
    public static function cased($callback)
    {
        static::registerModelEvent('cased', $callback);
    }
}
