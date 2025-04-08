<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    
    protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'user_status',
        'role_name',
        'name',
        'manager',

    ];

    protected $primaryKey = 'user_id';
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getRole(): string
    {
        return $this->role_name;
    }
    /**
     * Get subordinate user IDs.
     *
     * @return int[] An array of subordinate user IDs.
     */
    public function getSubordinateIds(): array
    {
        $role = $this->getRole();
        if ($role === 'ceo') {
            return DB::table('users')
            ->where('role_name', '!=', 'ceo')
            ->where('user_id', '!=', $this->user_id)
            ->pluck('user_id')
            ->toArray();
        }
        return DB::table('users')
            ->where('manager', '=', $this->user_id)
            ->where('user_id', '!=', $this->user_id)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * Get subordinate user IDs.
     *
     * @return int[] An array of branches under user's supervision.
     */
    public function getBranches(): array
    {
        // we need to check for branches under the user's supervision
        // sales can care for multiple branches
        // supervisor can care for multiple sales
        // ceo care for all
        $branches = [];
        $role = $this->getRole();
        if ($role === 'ceo') {
            $branches = DB::table('branch_stores')->get()->toArray();
        } elseif ($role === 'supervisor') {
            // get all sales under this supervisor
            // get all branches under these sales
            // and plus all branches directly under this supervisor
            $sales = $this->getSubordinateIds();
            $saleBranches = DB::table('branch_stores')->whereIn('bs_manager', $sales)->get()->toArray();
            $supervisorBranches = DB::table('branch_stores')->where('bs_manager', '=', $this->user_id)->get()->toArray();

            $branches = array_merge($saleBranches, $supervisorBranches);

        } elseif ($role === 'sale') {
            $branches = DB::table('branch_stores')->where('bs_manager', '=', $this->user_id)->get()->toArray();
        }

        return $branches;
    }
}
