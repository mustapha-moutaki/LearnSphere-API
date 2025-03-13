<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});


describe("testing" , function(){
    test('can see tags list', function () {

        $res = $this->get('/api/Tag');
        $res->assertStatus(200);
        $res->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                ],
            ],
        ]);
    });


    test('can create a tag and delete it', function () {
        $tag = [
            'name' => 'developement',
        ];

        $res = $this->post('/api/Tag', $tag); 
        $res->assertStatus(201);

        $tag = $res->json('data');

        $this->assertDatabaseHas('tags', [
            'name' => $tag['name'],
        ]);

        $res = $this->delete("/api/Tag/{$tag['id']}"); 
        $res->assertStatus(200);

        $this->assertDatabaseMissing('tags', [
            'id' => $tag['id'],
        ]);
    });


    test('can update a tag', function () {
        $tag = [
            'name' => 'evelopement',
        ];


        $res = $this->post('/api/Tag', $tag); 
        $tag = $res->json('data');

        $update = [
            'name' => 'tagName1',
        ];


        $res = $this->put("/api/Tag/{$tag['id']}", $update); 
        $res->assertStatus(200);

    });


    test('cannot update a non-existent tag', function () {
        $update = ['name' => 'newTagName'];

        $res = $this->put('/api/Tag/9999', $update);

        $res->assertStatus(404);
    });

});

