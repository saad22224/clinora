<?php

use App\Models\Clinic;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unique slug accounts for soft deleted clinics', function () {
    $clinic = Clinic::create([
        'name' => 'saad',
        'slug' => 'saad',
        'is_active' => true,
    ]);

    $clinic->delete();

    expect(Clinic::uniqueSlug('saad'))->toBe('saad-1');
});
