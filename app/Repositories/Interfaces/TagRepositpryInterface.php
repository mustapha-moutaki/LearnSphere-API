<?php

namespace App\Repositories\Interfaces;

interface TagRepositoryInterface
{
    public function getAllTags();
    public function getTagById($id);
    public function createTag($data);
    public function updateTag($id, $data);
    public function deleteTag($id);
}
