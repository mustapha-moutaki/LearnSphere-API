<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Interfaces\TagRepositoryInterface;
class TagRepository implements TagRepositoryInterface
{
    public function getAllTags()
    {
        return Tag::all();
    }

    public function getTagById($id)
    {
        return Tag::findOrFail($id);
    }

    public function createTag($data)
    {
        return Tag::create($data);
    }

    public function updateTag($id, $data)
    {
        $tag = Tag::findOrFail($id);
        $tag->update($data);
        return $tag;
    }

    public function deleteTag($id)
    {
        $tag = Tag::findOrFail($id);
        return $tag->delete();
    }
}
