<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LoginHistory extends Model
{
    use HasFactory;

    /**
     * Tabulka asociovaná s modelem.
     *
     * @var string
     */
    protected $table = 'login_history';

    /**
     * Atributy, které jsou hromadně přiřaditelné.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'location',
        'city',
        'country',
        'is_suspicious',
        'notified',
        'status',
    ];

    /**
     * Atributy, které by měly být konvertovány na nativní typy.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_suspicious' => 'boolean',
        'notified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Vztah k uživateli.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pro filtrování podezřelých přihlášení.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    /**
     * Scope pro filtrování neoznámených přihlášení.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotNotified($query)
    {
        return $query->where('notified', false);
    }

    /**
     * Scope pro filtrování podle stavu.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Zjistí, zda je přihlášení podezřelé.
     *
     * @return bool
     */
    public function isSuspicious()
    {
        return $this->is_suspicious;
    }

    /**
     * Označí přihlášení jako oznámené.
     *
     * @return void
     */
    public function markAsNotified()
    {
        $this->notified = true;
        $this->save();
    }
} 