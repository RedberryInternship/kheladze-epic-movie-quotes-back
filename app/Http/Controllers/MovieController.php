<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

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
            'image' => "http://127.0.0.1:8000/storage/" . $path
        ]);

        return response()->json([
            'message' => 'Movie added successfully'
        ]);
    }

    public function allMovies()
    {
        return response()->json(Movie::with('quotes')->get());
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
            $image = "http://127.0.0.1:8000/storage/" . $path;
        } else {
            $image = $request['image'];
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
            'image' => $image
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
