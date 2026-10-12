<?php

use App\Models\Clinic;
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
