<?php

namespace LaravelEnso\Avatars\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Core\App\Models\User;
use LaravelEnso\Files\App\Contracts\Attachable;
use LaravelEnso\Files\App\Traits\HasFile;

class Avatar extends Model implements Attachable
{
    use HasFile;

    public const Width = 250;
    public const Height = 250;

    protected $fillable = ['user_id', 'original_name', 'saved_name'];

    protected $hidden = ['user_id', 'created_at', 'updated_at'];

    protected $casts = ['user_id' => 'int'];

    protected $optimizeImages = true;

    protected $resizeImages = [
        'width' => self::Width,
        'height' => self::Height,
    ];

    protected $mimeTypes = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'];

    protected $folder = 'avatars';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store(UploadedFile $file)
    {
        return DB::transaction(function () use ($file) {
            $this->delete();
            $avatar = self::create(['user_id' => Auth::user()->id]);

            return tap($avatar)->upload($file);
        });
    }
}
