<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest('publish_date')->get();

        if ($articles->isEmpty()) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data empty',
            ], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json([
                'data' => $articles->map(function($article){
                    return [
                        'id' => $article->id,
                        'title' => $article->title,
                        'content' => $article->content,
                        'publish_date' => $article->publish_date,
                    ];
                }),
                'status' => Response::HTTP_OK,
                'message' => 'List article',
            ], Response::HTTP_OK);
        }

        
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'publish_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            Article::create([
                'title' => $request->title,
                'content' => $request->content,
                'publish_date' => Carbon::create($request->publish_date)->toDateString(),
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Data store to db',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log:error('Error storing data : ' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed store data to db',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $article = Article::where('id', $id)->first();

        if ($article) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'data' => [
                    'title' => $article->title,
                    'content' => $article->content,
                    'publish_date' => $article->publish_date,
                ],
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data not found',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'publish_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $article->update([
                'title' => $request->title,
                'content' => $request->content,
                'publish_date' => Carbon::create($request->publish_date)->toDateString(),
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Data updated',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log:error('Error update data : ' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed updated data',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data not found',
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $article->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Data deleted',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log:error('Error delete data : ' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed delete data',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
