<?php

namespace App\Model\Common;

use App\Model\G7\ReceiptVoucher;
use App\Model\Uptek\G7Info;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Auth;
use App\Model\Common\File;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;

    protected $guard_name = 'admin';

    public const SUPER_ADMIN = 1;
    public const QUAN_TRI_VIEN = 2;
    public const G7 = 3;
    public const NHOM_G7 = 4;
    public const NHAN_VIEN_G7 = 5;

    public CONST USER_TYPES = [
        [
            'id' => 1,
            'name' => 'Super Admin',
        ],
        [
            'id' => 2,
            'name' => 'Quản trị viên',
        ],
    ];

    public CONST STATUSES = [
        [
            'id' => 1,
            'name' => 'Hoạt động',
            'type' => 'success'
        ],
        [
            'id' => 0,
            'name' => 'Khóa',
            'type' => 'danger'
        ]
    ];

    public function getTypeUser($type)
    {
       foreach(self::USER_TYPES as $item) {
           if($item['id'] == $type) {
               return $item['name'];
               break;
           }
       }
    }

    protected $fillable = [
        'name', 'account_name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            if (Auth::user() && !$model->created_by) $model->created_by = Auth::user()->id;
            if (Auth::user()) $model->updated_by = Auth::user()->id;
        });

        self::saving(function($model){
            if (Auth::user()) $model->updated_by = Auth::user()->id;
        });
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
        ];
    }

    public function canEdit()
    {

        return true;
    }

    public function canDelete()
    {
        return true;
    }


    public function user_create ()
    {
        return $this->belongsTo('App\Model\Common\User', 'created_by','id');
    }

    public function user_update ()
    {
        return $this->belongsTo('App\Model\Common\User', 'updated_by','id');
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function employee()
    {
        return $this->belongsTo('App\Model\G7\G7Employee','employee_id','id');
    }

    // public function recipient()
    // {
    //     return $this->morphOne(PaymentVoucher::class, 'recipientale');
    // }

    public function receiptVoucher()
    {
        return $this->morphOne(ReceiptVoucher::class, 'payer');
    }

    public function g7Info()
    {
        return $this->belongsTo(G7Info::class,'g7_id','id');
    }

    public function getIsSuperAdminAttribute() {
        return $this->type == self::SUPER_ADMIN;
    }

    public function getAccessTypes() {
        if ($this->type == self::SUPER_ADMIN) return [self::QUAN_TRI_VIEN, self::G7, self::NHOM_G7];
        if ($this->type == self::QUAN_TRI_VIEN) return [self::G7, self::NHOM_G7];
        if ($this->type == self::G7) return [self::NHAN_VIEN_G7];
        return [];
    }

    public function canDo($permission_name) {
        if ($this->is_super_admin) return true;
        $permission = Permission::where('name', $permission_name)->first();
        $types = PermissionHasType::where('permission_id', $permission->id)->pluck('type');
        if (!$permission) return false;
        return in_array($this->type, $types) && $this->can($permission_name);
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'roles',
                'image',
            ])
            ->firstOrFail();
    }

    public static function searchByFilter($request) {
        $result = self::with([
            'employee'
        ]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->email)) {
            $result = $result->where('email', 'like', '%'.$request->email.'%');
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        if (empty($request->get('order'))) {
            $result = $result->orderBy('created_at', 'DESC');
        }

        return $result;
    }

    public static function getForSelect() {
        return self::select(['id', 'name'])
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getMembers() {
        $result = self::select(['id', 'name']);

        if (Auth::user()->type == self::G7) {
            $result = $result->where('g7_id', Auth::user()->g7_id);
        }

        $result = $result->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();
        return $result;
    }

	public function getG7IdsAttribute() {
		return UserG7Info::where('user_id', $this->id)->pluck('g7_id')->toArray();
	}
}
