<?php
it('accepts sanctum token for heartbeat', function () {
    $user = \App\Models\User::factory()->create();
    $token = $user->createToken('ping')->plainTextToken;
    $this->withHeader('Authorization', 'Bearer '.$token)
         ->postJson('/api/v1/attendance/heartbeat', ['lat'=>25.2,'lng'=>55.27,'accuracy'=>10])
         ->assertNoContent(); // 204
});
