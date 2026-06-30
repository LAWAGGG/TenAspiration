<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('can submit keluh-kesah', function () {
    $response = $this->post(route('aspiration_keluhkesah.store'), [
        'keluh_kesah' => 'Saya merasa kurang nyaman dengan fasilitas kelas',
        'phone_number' => '081234567890',
    ]);
    
    $response->assertSessionHasNoErrors();
});

