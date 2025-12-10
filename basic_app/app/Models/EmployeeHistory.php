<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    protected $fillable = [
        'employee_id',
        'changed_by',
        'is_active',
        'action',
        'name',
        'email',
        'avatar_path',
        'permissions_snapshot',
        'meta',
    ];

    protected $casts = [
        'permissions_snapshot' => 'array',
        'meta' => 'array',
    ];

    public static function snapshotFromUser(User $employee): array
    {
        return [
            'name'                 => $employee->name,
            'email'                => $employee->email,
            'avatar_path'          => $employee->avatar_path,
            'permissions_snapshot' => $employee->permissions()->pluck('permissions.id')->values()->all(),
        ];
    }

    public static function log(User $employee, string $action, array $meta = [],bool $is_active=false): void
    {
        $payload = array_merge(self::snapshotFromUser($employee), [
            'employee_id' => $employee->id,
            'changed_by'  => optional(auth()->user())->id,
            'action'      => $action,
            'meta'        => $meta ?: null,
            'is_active'=>$is_active
        ]);

        self::create($payload);
    }
}
