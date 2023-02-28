<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    public function createMovie(Request $request)
    {
        $path = request()->file('image')->store('movies');

        $movie = Movie::create([
            'user_id' => $request['userId'],
            'name' => ['en' => $request['name_en'], 'ka' => $request['name_ka']],
            'genres' => $request['genres'],
            'director' => ['en' => $request['director_en'], 'ka' => $request['director_ka']],
            'budget' => $request['budget'],
            'year' => $request['year'],
            'description' => ['en' => $request['description_en'], 'ka' => $request['description_ka']],
            'image' => $path
        ]);

        return response()->json([
            'message' => 'Movie added successfully'
        ]);
    }

    public function allMovies()
    {
        $moviesWithQuotes = Movie::with('quotes.comments.user', 'quotes.likes')->get();
        $modifiedCollection = $moviesWithQuotes->map(function ($movie) {
            $movie->image = env("STORAGE_PATH") . ($movie->image);

            $quotes = $movie->quotes->map(function ($quote) {
                $quote->image = env("STORAGE_PATH") . ($quote->image);
                $quote->comments->map(function ($comment) {
                    if (strpos($comment->user->image, 'storage') == false && $comment->user->google_id == null) {
                        $comment->user->image = env("STORAGE_PATH") . ($comment->user->image);
                    }
                    return $comment;
                });
                return $quote;
            });

            $movie->quotes = $quotes;
            return $movie;
        });
        return response()->json($modifiedCollection);
    }

    public function deleteMovie(Request $request)
    {
        $movie = Movie::where('id', $request['movieId'])->first();

        $movie->delete();

        return response()->json([
            'message' => 'Movie Deleted Successfully'
        ]);
    }

    public function updateMovie(Request $request)
    {
        $image = request()->file('image');
        if ($image) {
            $path = $image->store('movie');
            $image =  $path;
        } else {
            $image = Str::remove(env("STORAGE_PATH"), $request['image']);
        }
        $movie = Movie::where('id', $request['movieId'])->first();

        $movie->update([
            'name' => ['en' => $request['name_en'], 'ka' => $request['name_ka']],
            'genres' => $request['genres'],
            'director' => ['en' => $request['director_en'], 'ka' => $request['director_ka']],
            'budget' => $request['budget'],
            'year' => $request['year'],
            'description' => ['en' => $request['description_en'], 'ka' => $request['description_ka']],
            'image' => $image
        ]);

        return response()->json([
            'message' => 'Movie updates successfully',
        ]);
    }

    public function genres()
    {

        $genres = [
            'en' => [
                ['id' => 1, 'genre' => 'Action'],
                ['id' => 2, 'genre' => 'Comedy'],
                ['id' => 3, 'genre' => 'Drama'],
                ['id' => 4, 'genre' => 'Fantasy'],
                ['id' => 5, 'genre' => 'Horror'],
                ['id' => 6, 'genre' => 'Sci-Fi'],
                ['id' => 7, 'genre' => 'Romance'],
                ['id' => 8, 'genre' => 'Thriller'],
                ['id' => 9, 'genre' => 'Western'],
            ],
            'ka' => [
                ['id' => 1, 'genre' => 'მოქმედებითი'],
                ['id' => 2, 'genre' => 'კომედია'],
                ['id' => 3, 'genre' => 'დრამა'],
                ['id' => 4, 'genre' => 'ფანტასტიკა'],
                ['id' => 5, 'genre' => 'ჰორორი'],
                ['id' => 6, 'genre' => 'სქაიფაი'],
                ['id' => 7, 'genre' => 'რომანტიული'],
                ['id' => 8, 'genre' => 'თრილერი'],
                ['id' => 9, 'genre' => 'ვესტერნი'],
            ]
        ];

        return response()->json($genres);
    }
}
