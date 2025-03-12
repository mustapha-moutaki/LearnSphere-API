<?php

namespace App\Services;

use App\Repositories\TagRepository;

class TagService
{
    protected $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getAllTags()
    {
        return $this->tagRepository->getAllTags();
    }

    public function getTagById($id)
    {
        return $this->tagRepository->getTagById($id);
    }

    public function createTag($data)
    {
        return $this->tagRepository->createTag($data);
    }

    public function updateTag($id, $data)
    {
        return $this->tagRepository->updateTag($id, $data);
    }

    public function deleteTag($id)
    {
        return $this->tagRepository->deleteTag($id);
    }
}
