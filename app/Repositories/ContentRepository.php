<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\Content;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class ContentRepository
{
    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);

        try {

            $contents = Content::with(["createdBy:id,username", "updatedBy:id,username"])
                ->orderBy('created_at', 'desc')
                ->when($searchKey, fn($query) => $query->where("title", "like", "%$searchKey%"))
                ->paginate($paginateSize);

            return $contents;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $content = new Content();

            $content->title            = $request->title;
            $content->type             = $request->type;
            $content->content          = $request->content;
            $content->status           = $request->status;
            $res                       = $content->save();
            if ($res) {
                //update image
                if ($request->hasFile('image')) {
                    Helper::uploadFile($content, $request->image, $content->uploadPath, null, 1080, 720);
                }
            }

            DB::commit();

            return $content;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $content = Content::with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$content) {
                throw new CustomException("Content not found");
            }

            return $content;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $content = Content::find($id);
            if (!$content) {
                throw new CustomException("content Not found");
            }

            $content->title   = $request->title;
            $content->type    = $request->type;
            $content->content = $request->content;
            $content->status  = $request->status;
            $res              = $content->save();
            if ($res) {
                if ($request->hasFile('image')) {
                    //update image
                    Helper::uploadFile($content, $request->image, $content->uploadPath, $content->img_path, 1080, 720);
                }
            }

            DB::commit();

            return $content;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $content = Content::find($id);

            if (!$content) {
                throw new CustomException("Content not found");
            }
            //  Delete old image
            if ($content->img_path) {
                Helper::deleteFile($content->img_path);
            }

            return $content->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
