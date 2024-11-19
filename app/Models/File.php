<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @method static create(array|string[] $array_merge)
 * @property string $name
 * @property string $type
 * @property string $path
 * @property string $size
 */
class File extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'type',
        'path',
        'size'
    ];

    protected static function booted()
    {
        static::creating(function ($file) {
            $file->file_key = Uuid::uuid4()->toString();
        });
    }
}
