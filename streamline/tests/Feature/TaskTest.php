<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /**
     * Tests task index GET request without userID
     */
    public function testIndex() {
        $response = $this->json('GET', 'api/tasks/');
        $response
            ->assertStatus(404)
            ->assertSeeText('Missing userID');
    }

    /**
     * Tests task index GET request with userID
     */
    public function testIndexWithID() {
        $response = $this->json('GET', 'api/tasks/?userID=1');
        $response
            ->assertStatus(200);
    }

    /**
     * Tests valid create POST request
     */
    public function testCreateValid() {
        $response = $this->json('POST', 'api/tasks/', [
            'title' => 'Test Valid Create',
            'body' => 'Test Body',
            'expDuration' => 2700,
            'estimatedMin' => 45,
            'estimatedHour' => 0,
            'userID' => 1
        ]);

        $id =  $response->getData() -> id;

        $response
            ->assertStatus(201)
            ->assertSeeText('id');

        $this->json('DELETE', "api/tasks/{$id}");
    }

    /**
     * Tests invalid create POST request
     */
    public function testCreateInvalid() {
        //TODO: Add Variants for full Coverage
        $response = $this->json('POST', 'api/tasks/', [
            'title' => 'Test Invalid Create',
            'body' => 'Test Body',
            'expDuration' => 2700,
            'estimatedMin' => 45,
            'estimatedHour' => 0
        ]);

        $response
            ->assertStatus(500);
    }

    /**
     * Tests valid GET request of Task object on created Task
     */
    public function testValidRead() {
        $createResp = $this->json('POST', 'api/tasks/', [
            'title' => 'Test Valid Read',
            'body' => 'Test Body',
            'expDuration' => 2700,
            'estimatedMin' => 45,
            'estimatedHour' => 0,
            'userID' => 1
        ]);

        $id =  $createResp->getData() -> id;

        $route = 'api/tasks/%s';
        $fullRoute = sprintf($route, $id);

        $response = $this->json('GET', $fullRoute);
        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Test Valid Read',
                'body' => 'Test Body',
                'expDuration' => 2700,
                'estimatedMin' => 45,
                'estimatedHour' => 0
            ]);

        $this->json('DELETE', "api/tasks/{$id}");
    }

    /**
     * Tests invalid GET request on nonexistant Task object
     */
    public function testInvalidRead() {
        $response = $this->json('GET', 'api/tasks/99');

        $response 
            ->assertStatus(404);
    }

    /**
     * Test a valid UPDATE request on an existing Task object
     */
    public function testValidUpdate() {
        $createResp = $this->json('POST', 'api/tasks/', [
            'title' => 'Test Valid Read',
            'body' => 'Test Body',
            'expDuration' => 2700,
            'estimatedMin' => 45,
            'estimatedHour' => 0,
            'userID' => 1
        ]);

        $id =  $createResp->getData() -> id;

        // Execute Update
        $updateResp = $this->json('PUT', "api/tasks/{$id}", [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
            'expDuration' => 4,
            'estimatedMin' => 3,
            'estimatedHour' => 2,
            'workedDuration' => 1
        ]);

        // Validate Successful Update
        $updateResp
            ->assertStatus(204);

        // Validate Successful Update of Content with Read 
        $readResp = $this->json('GET', "api/tasks/{$id}");
        $readResp
            ->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Title',
                'body' => 'Updated Body',
                'expDuration' => 4,
                'estimatedMin' => 3,
                'estimatedHour' => 2,
                'workedDuration' => 1
            ]);

        // Cleanup
        $this->json('DELETE', "api/tasks/{$id}");
    }

    /**
     * Tests a valid DELETE request on an existing object
     */
    public function testValidDelete() {
        $createResp = $this->json('POST', 'api/tasks/', [
            'title' => 'Test Title',
            'body' => 'Test Body',
            'expDuration' => 2700,
            'estimatedMin' => 45,
            'estimatedHour' => 0,
            'userID' => 1
        ]);

        $id =  $createResp->getData() -> id;

        $response = $this->json('DELETE', "api/tasks/{$id}");

        $response->assertStatus(204);
    }

    /**
     * Tests a valid DELETE request on a nonexistent object
     */
    public function testInvalidDelete() {
        $response = $this->json('DELETE', "api/tasks/99");
        $response->assertStatus(404);
    }

    /**
     * Test simple control flow:
     * 1. Create Task
     * 2. Start Task
     * 3. Stop Task
     * 4. Finish Task
     */
    public function testSimpleControlFlow() {
        $createResp = $this->json('POST', 'api/tasks/', [
            'title' => 'Test Valid Read',
            'body' => 'Test Body',
            'expDuration' => 2700,
            'estimatedMin' => 45,
            'estimatedHour' => 0,
            'userID' => 1
        ]);

        $id =  $createResp->getData() -> id;

        $startResp = $this->json('POST', "api/tasks/{$id}/start");
        $startResp->assertStatus(204);

        $stopResp = $this->json('POST', "api/tasks/{$id}/stop");
        $stopResp->assertStatus(204);

        $finishResp = $this->json('POST', "api/tasks/{$id}/finish");
        $finishResp->assertStatus(204);

        $response = $this->json('DELETE', "api/tasks/{$id}");
    }

    /**
     * Test Start/Stop Validation
     * 1. A started Task cannot be started again
     * 2. A stopped Task cannot be stopped again
     */
    public function testStartStopValidationFlow() {
        $createResp = $this->json('POST', 'api/tasks/', [
            'title' => 'Test Valid Read',
            'body' => 'Test Body',
            'expDuration' => 2700,
            'estimatedMin' => 45,
            'estimatedHour' => 0,
            'userID' => 1
        ]);

        $id =  $createResp->getData() -> id;

        $startResp = $this->json('POST', "api/tasks/{$id}/start");
        $startResp->assertStatus(204);

        $startResp = $this->json('POST', "api/tasks/{$id}/start");
        $startResp->assertStatus(409);

        $stopResp = $this->json('POST', "api/tasks/{$id}/stop");
        $stopResp->assertStatus(204);

        $stopResp = $this->json('POST', "api/tasks/{$id}/stop");
        $stopResp->assertStatus(409);

        $response = $this->json('DELETE', "api/tasks/{$id}");
    }

    public function testFinishValidationFlow() {
        $createResp = $this->json('POST', 'api/tasks/', [
            'title' => 'Test Valid Read',
            'body' => 'Test Body',
            'expDuration' => 2700,
            'estimatedMin' => 45,
            'estimatedHour' => 0,
            'userID' => 1
        ]);

        $id =  $createResp->getData() -> id;

        $finishResp = $this->json('POST', "api/tasks/{$id}/finish");
        $finishResp->assertStatus(204);

        $startResp = $this->json('POST', "api/tasks/{$id}/start");
        $startResp->assertStatus(409);

        $stopResp = $this->json('POST', "api/tasks/{$id}/stop");
        $stopResp->assertStatus(409);

        $finishResp = $this->json('POST', "api/tasks/{$id}/finish");
        $finishResp->assertStatus(409);

        $response = $this->json('DELETE', "api/tasks/{$id}");
    }


}
