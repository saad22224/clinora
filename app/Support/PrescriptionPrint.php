<?php

namespace App\Support;

use App\Models\Clinic;
use App\Models\Prescription;

class PrescriptionPrint
{
    /**
     * @return list<string>
     */
    public static function layouts(): array
    {
        return ['classic', 'minimal', 'modern', 'compact', 'detailed', 'elegant'];
    }

    /**
     * @return array<string, string>
     */
    public static function layoutLabels(): array
    {
        return [
            'classic' => 'كلاسيكي طبي',
            'minimal' => 'بسيط ونظيف',
            'modern' => 'عصري احترافي',
            'compact' => 'مدمج',
            'detailed' => 'تفصيلي سريري',
            'elegant' => 'أنيق بعلامة العيادة',
        ];
    }

    public static function normalizeLayout(?string $layout): string
    {
        $layout = $layout ?: 'classic';

        return in_array($layout, self::layouts(), true) ? $layout : 'classic';
    }

    public static function normalizeLocale(?string $locale): string
    {
        return $locale === 'en' ? 'en' : 'ar';
    }

    /**
     * @return array<string, string>
     */
    public static function labels(string $locale): array
    {
        if ($locale === 'en') {
            return [
                'print' => 'Print prescription',
                'close' => 'Close',
                'patient' => 'Patient',
                'age' => 'Age',
                'years' => 'years',
                'date' => 'Date',
                'notes' => 'General notes / recommendations',
                'signature' => 'Doctor signature',
                'wish' => 'Wishing you a speedy recovery.',
                'phone' => 'Phone',
                'address' => 'Address',
                'clinic' => 'Clinic',
                'medications' => 'Medications',
                'dosage' => 'Dosage',
                'instructions' => 'Instructions',
                'preview_sample' => 'Sample preview — not a real prescription',
            ];
        }

        return [
            'print' => 'طباعة الروشتة',
            'close' => 'إغلاق',
            'patient' => 'اسم المريض',
            'age' => 'العمر',
            'years' => 'سنة',
            'date' => 'التاريخ',
            'notes' => 'ملاحظات عامة / توصيات',
            'signature' => 'توقيع الطبيب',
            'wish' => 'ألف سلامة، مع تمنياتنا بالشفاء العاجل.',
            'phone' => 'الهاتف',
            'address' => 'العنوان',
            'clinic' => 'العيادة',
            'medications' => 'الأدوية',
            'dosage' => 'الجرعة',
            'instructions' => 'التعليمات',
            'preview_sample' => 'معاينة تجريبية — ليست روشتة حقيقية',
        ];
    }

    /**
     * Single-doctor Online clinics: prefer clinic branding + owner name.
     */
    public static function doctorDisplayName(Clinic $clinic, ?Prescription $prescription = null): string
    {
        $clinic->loadMissing('users');

        $owner = $clinic->users->firstWhere('role', 'owner');
        $name = $owner?->name
            ?? $clinic->users->firstWhere('role', 'doctor')?->name
            ?? $clinic->name;

        $title = trim((string) ($clinic->doctor_title ?? ''));

        return trim(($title !== '' ? $title.' ' : '').$name);
    }
}
