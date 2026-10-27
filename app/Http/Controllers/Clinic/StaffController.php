<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public static array $permissionsList = [
        'manage_appointments' => [
            'name' => 'manage_appointments',
            'label' => 'إدارة المواعيد والدور والمناداة',
            'desc' => 'الحجوزات، طابور اليوم، أرقام الدور، مناداة المرضى صوتيًا، إيقاف/استئناف المناداة',
        ],
        'use_staff_messages' => [
            'name' => 'use_staff_messages',
            'label' => 'تواصل الفريق والرسائل الداخلية',
            'desc' => 'إرسال واستقبال رسائل الطبيب والسكرتارية والتنبيهات الفورية على الشاشة',
        ],
        'manage_patients' => [
            'name' => 'manage_patients',
            'label' => 'إدارة ملفات المرضى',
            'desc' => 'إضافة وتعديل بيانات المرضى وملفاتهم وسجلهم الصحي الأساسي',
        ],
        'manage_prescriptions' => [
            'name' => 'manage_prescriptions',
            'label' => 'إدارة الوصفات والروشتات الطبية',
            'desc' => 'إنشاء وطباعة وتعديل الوصفات الطبية للمرضى',
        ],
        'manage_records' => [
            'name' => 'manage_records',
            'label' => 'إدارة السجلات والملفات الطبية',
            'desc' => 'رفع وإدارة التحاليل والأشعة والتقارير المرفقة في ملف المريض',
        ],
        'manage_billing' => [
            'name' => 'manage_billing',
            'label' => 'إدارة المالية والتقارير',
            'desc' => 'تسجيل الإيرادات والمصروفات وعرض تقارير العيادة المالية',
        ],
        'manage_settings' => [
            'name' => 'manage_settings',
            'label' => 'إعدادات العيادة والصفحة العامة',
            'desc' => 'بيانات العيادة، أوقات العمل، الخدمات، والصفحة العامة للحجز الإلكتروني',
        ],
    ];

    /**
     * @return list<string>
     */
    public static function permissionNames(): array
    {
        return array_keys(self::$permissionsList);
    }

    public static function ensurePermissionsExist(): void
    {
        foreach (self::permissionNames() as $name) {
            Permission::findOrCreate($name, 'web');
        }
    }

    public function index()
    {
        $currentUser = Auth::user();
        abort_unless(in_array($currentUser->role, ['owner', 'doctor', 'superadmin'], true), 403);

        self::ensurePermissionsExist();

        $staff = User::where('clinic_id', $this->currentClinicId())
            ->where('id', '!=', $currentUser->id)
            ->with('permissions', 'roles')
            ->latest()
            ->get();

        return view('clinic.staff', [
            'staff' => $staff,
            'permissionsList' => self::$permissionsList,
        ]);
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        abort_unless(in_array($currentUser->role, ['owner', 'doctor', 'superadmin'], true), 403);

        self::ensurePermissionsExist();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(self::permissionNames())],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'clinic_id' => $this->currentClinicId(),
            'role' => 'secretary',
        ]);

        Role::firstOrCreate(['name' => 'secretary', 'guard_name' => 'web']);
        $user->assignRole('secretary');

        if (! empty($validated['permissions'])) {
            $user->syncPermissions($validated['permissions']);
        }

        return redirect()->route('clinic.staff.index')
            ->with('success', 'تم إضافة الموظف بنجاح وتعيين الصلاحيات.');
    }

    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();
        abort_unless(in_array($currentUser->role, ['owner', 'doctor', 'superadmin'], true), 403);
        abort_if((int) $user->clinic_id !== $this->currentClinicId(), 403);
        abort_if($user->role === 'owner' && ! in_array($currentUser->role, ['owner', 'superadmin'], true), 403, 'لا يمكن تعديل حساب مالك العيادة.');

        self::ensurePermissionsExist();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(self::permissionNames())],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('clinic.staff.index')
            ->with('success', 'تم تحديث بيانات الموظف وصلاحياته بنجاح.');
    }

    public function destroy(User $user)
    {
        $currentUser = Auth::user();
        abort_unless(in_array($currentUser->role, ['owner', 'doctor', 'superadmin'], true), 403);
        abort_if((int) $user->clinic_id !== $this->currentClinicId(), 403);
        abort_if($user->id == $currentUser->id, 403, 'لا يمكنك حذف حسابك الشخصي.');
        abort_if($user->role === 'owner', 403, 'لا يمكن حذف الحساب الرئيسي لمالك العيادة.');

        $user->delete();

        return redirect()->route('clinic.staff.index')
            ->with('success', 'تم حذف الموظف من النظام بنجاح.');
    }
}
