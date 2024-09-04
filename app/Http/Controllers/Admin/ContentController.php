<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\ContentRepository;
use App\Http\Requests\Admin\ContentRequest;
use App\Http\Resources\Admin\ContentResource;
use App\Http\Resources\Admin\ContentCollection;

class ContentController extends BaseController
{
    protected $repository;

    public function __construct(ContentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission("contents-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $contents = $this->repository->index($request);

            $contents = new ContentCollection($contents);

            return $this->sendResponse($contents, "Content list", 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(ContentRequest $request)
    {
        if (!$request->user()->hasPermission("contents-create")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $content = $this->repository->store($request);

            $content = new ContentResource($content);

            return $this->sendResponse($content, "Content created successfully", 201);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('contents-read')) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $content = $this->repository->show($id);

            $content = new ContentResource($content);

            return $this->sendResponse($content, "Content single view", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(ContentRequest $request, $id)
    {
        if (!$request->user()->hasPermission("contents-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $content = $this->repository->update($request, $id);

            $content = new ContentResource($content);

            return $this->sendResponse($content, "Content updated successfully", 201);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission("contents-delete")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $content = $this->repository->delete($id);

            return $this->sendResponse($content, "Content deleted successfully", 200);
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage(), 404);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
