<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Artwork;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * @param  \App\Models\Artwork  $artwork
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Artwork $artwork): JsonResponse
    {
        $comments = $artwork->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $comments
        ]);
    }

    /**
     * @param  \App\Http\Requests\Comment\StoreCommentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCommentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $comment = Comment::create($validated);
        $comment->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Комментарий успешно добавлен',
            'data' => $comment
        ], 201);
    }

    /**
     * @param  \App\Http\Requests\Comment\StoreCommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreCommentRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $comment->update([
            'content' => $request->input('content')
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Комментарий успешно обновлен',
            'data' => $comment->load('user')
        ]);
    }

    /**
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->replies()->delete();
        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Комментарий успешно удален'
        ]);
    }

    /**
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getReplies(Comment $comment): AnonymousResourceCollection
    {
        $replies = $comment->replies()
            ->with(['user', 'artwork'])
            ->withCount('replies')
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return CommentResource::collection($replies);
    }
}
