<?php

namespace HelgeSverre\Chromadb\Resource;

use HelgeSverre\Chromadb\Requests\Items\AddItems;
use HelgeSverre\Chromadb\Requests\Items\CountItems;
use HelgeSverre\Chromadb\Requests\Items\DeleteItems;
use HelgeSverre\Chromadb\Requests\Items\GetItems;
use HelgeSverre\Chromadb\Requests\Items\QueryItems;
use HelgeSverre\Chromadb\Requests\Items\UpdateItems;
use HelgeSverre\Chromadb\Requests\Items\UpsertItems;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Items extends BaseResource
{
    public function add(string $collectionId): Response
    {
        return $this->connector->send(new AddItems($collectionId));
    }

    public function update(string $collectionId): Response
    {
        return $this->connector->send(new UpdateItems($collectionId));
    }

    public function upsert(string $collectionId): Response
    {
        return $this->connector->send(new UpsertItems($collectionId));
    }

    public function get(string $collectionId): Response
    {
        return $this->connector->send(new GetItems($collectionId));
    }

    public function delete(string $collectionId): Response
    {
        return $this->connector->send(new DeleteItems($collectionId));
    }

    public function count(string $collectionId): Response
    {
        return $this->connector->send(new CountItems($collectionId));
    }

    public function getNearestNeighbors(string $collectionId): Response
    {
        return $this->connector->send(new QueryItems($collectionId));
    }
}
