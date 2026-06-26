<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public static array $permissionsList = [
        'manage_appointments' => [
            'name' => 'manage_appointments',
            'label' => 'إدارة المواعيد والحجوزات',
            'desc' => 'إضافة وتعديل وحذف مواعيد المرضى وتأكيد الحجوزات الإلكترونية',
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
            'label' => 'إدارة الفواتير والمالية',
            'desc' => 'إنشاء الفواتير وعرض المدفوعات والحسابات الخاصة بالعيادة',
        ],
        'manage_settings' => [
            'name' => 'manage_settings',
            'label' => 'إدارة إعدادات العيادة',
            'desc' => 'تعديل بيانات العيادة، أوقات العمل، وسعر وأنواع الكشف والاستشارات',
        ],
    ];

    public function index()
    {
        $currentUser = Auth::user();
        $staff = User::where('clinic_id', $currentUser->clinic_id)
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

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'clinic_id' => $currentUser->clinic_id,
            'role' => 'secretary',
        ]);

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
        abort_if($user->clinic_id !== $currentUser->clinic_id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
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
        abort_if($user->clinic_id !== $currentUser->clinic_id, 403);
        abort_if($user->id === $currentUser->id, 403, 'لا يمكنك حذف حسابك الشخصي.');

        $user->delete();

        return redirect()->route('clinic.staff.index')
            ->with('success', 'تم حذف الموظف من النظام بنجاح.');
    }
}
