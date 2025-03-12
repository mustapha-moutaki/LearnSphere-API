<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});


test(" can list tags",function(){

    $responce = $this->get("api/v1/Tag");
    $responce->assertStatus(200);
    $responce->assertJsonStructure([
        "data" => [
            "*" => [
                'name',
            ],
        ],
    ]);
});

test("can add Tag", function(){
    $tag = [
       "name" => "yara"
    ];

    $responce = $this->post("api/v1/Tag",$tag);
    $responce->assertStatus(201);
    $tag = $responce->json('data');

    $this->assertDatabaseHas('tags',['name' => $tag['name']]);

});