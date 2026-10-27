<?php

use App\Models\Clinic;
use App\Models\VisitType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public clinic page works without a clinic owner user', function () {
    $clinic = Clinic::create([
        'name' => 'فرع بدون مالك',
        'slug' => 'branch-no-owner',
        'is_active' => true,
        'doctor_title' => 'أخصائي عام',
    ]);

    $this->get(route('clinic.public.show', $clinic->slug))
        ->assertOk()
        ->assertSee('فرع بدون مالك')
        ->assertDontSee('Attempt to read property');
});

test('public clinic page renders unified UI/UX, theme toggle, full doctor bio, about us, social links, and working hours', function () {
    $clinic = Clinic::create([
        'name' => 'عيادة د. طارق للقلب والأوعية الدموية',
        'slug' => 'dr-tarek-cardio',
        'is_active' => true,
        'doctor_title' => 'استشاري أمراض القلب والقسطرة',
        'hero_title' => 'رعاية متقدمة لصحة القلب',
        'hero_subtitle' => 'تشخيص دقيق ومتابعة مستمرة بأحدث البروتوكولات الطبية.',
        'doctor_bio' => 'خبرة واسعة في تشخيص وعلاج أمراض الشرايين التاجية وضغط الدم.',
        'about_us' => 'عيادة مجهزة بأحدث أجهزة رسم القلب والموجات الصوتية (الإيكو).',
        'phone' => '01012345678',
        'address' => 'القاهرة، مدينة نصر، شارع عباس العقاد',
        'social_links' => [
            'facebook' => 'https://facebook.com/drtarekclinic',
            'instagram' => 'https://instagram.com/drtarekclinic',
        ],
        'working_hours' => [
            'saturday' => [
                'enabled' => true,
                'periods' => [
                    ['open' => '10:00', 'close' => '14:00'],
                    ['open' => '18:00', 'close' => '21:00'],
                ],
            ],
            'sunday' => [
                'enabled' => true,
                'open' => '11:00',
                'close' => '16:00',
            ],
        ],
    ]);

    VisitType::create([
        'clinic_id' => $clinic->id,
        'name' => 'كشف قلب ورسم قلب',
        'description' => 'فحص شامل مع تخطيط كهربائية القلب',
        'price' => 450,
    ]);

    $this->get(route('clinic.public.show', $clinic->slug))
        ->assertOk()
        ->assertSee('clinora-grid-bg', false)
        ->assertSee('clinora-card', false)
        ->assertSee('clinora-input', false)
        ->assertSee('data-theme-toggle', false)
        ->assertSee('رعاية متقدمة لصحة القلب')
        ->assertSee('تشخيص دقيق ومتابعة مستمرة بأحدث البروتوكولات الطبية.')
        ->assertSee('خبرة واسعة في تشخيص وعلاج أمراض الشرايين التاجية وضغط الدم.')
        ->assertSee('عيادة مجهزة بأحدث أجهزة رسم القلب والموجات الصوتية (الإيكو).')
        ->assertSee('https://facebook.com/drtarekclinic', false)
        ->assertSee('https://instagram.com/drtarekclinic', false)
        ->assertSee('10:00 – 14:00')
        ->assertSee('11:00 – 16:00')
        ->assertSee('كشف قلب ورسم قلب')
        ->assertSee('450 ج.م');
});
