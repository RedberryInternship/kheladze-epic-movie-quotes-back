<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function allQuotes(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 3;

        $searchTerm = $request->input('search');

        if ($searchTerm && Str::length($searchTerm) > 1) {
            $quotesWithMovies = Quote::with('movies.users', 'comments.user', 'likes')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $quotesWithMovies = Quote::with('movies.users', 'comments.user', 'likes')
                ->orderBy('created_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
        }


        $modifiedCollection = $quotesWithMovies->map(function ($quote) use ($searchTerm) {
            $quote_image = $quote->image;

            if (strpos($quote_image, 'storage') == false) {
                $quote_image = env("STORAGE_PATH") . ($quote->image);
            }
            $quote->image = $quote_image;

            if ($quote->movies) {
                $movie_image = $quote->movies->image;
                if (strpos($movie_image, 'storage') == false) {
                    $movie_image = env("STORAGE_PATH") . ($quote->movies->image);
                }
                $quote->movies->image = $movie_image;
                if ($quote->movies->users) {
                    $user_image = $quote->movies->users->image;
                    if (strpos($user_image, 'storage') == false && $quote->movies->users->google_id == null) {
                        $user_image = env("STORAGE_PATH") . ($quote->movies->users->image);
                    }
                    $quote->movies->users->image = $user_image;
                }
            }

            $quote->comments->map(function ($comment) {
                if ($comment->user) {
                    if (strpos($comment->user->image, 'storage') == false && $comment->user->google_id == null) {
                        $comment->user->image = env("STORAGE_PATH") . ($comment->user->image);
                    }
                }
                return $comment;
            });

            return $quote;
        });

        if ($searchTerm && Str::length($searchTerm) > 1) {
            $searchedCollection = $modifiedCollection->filter(function ($quote) use ($searchTerm) {
                if (Str::contains($searchTerm, '@')) {
                    $movieSearchTerm = substr($searchTerm, 1);
                    if (Str::contains($quote->movies->name, $movieSearchTerm)) {
                        return $quote;
                    }
                } elseif (Str::contains($searchTerm, '#')) {
                    $quoteSearchTerm = substr($searchTerm, 1);
                    if (Str::contains($quote->quote, $quoteSearchTerm)) {
                        return $quote;
                    }
                }
            });
            $numericArray = $searchedCollection->values()->all();
            return response()->json($numericArray);
        }
        return response()->json($modifiedCollection);
    }

    public function createQuote(Request $request)
    {
        $path = request()->file('image')->store('quotes');
        Quote::create([
            'movie_id' => $request['movieId'],
            'quote' => ['en' => $request['quote_en'], 'ka' => $request['quote_ka']],
            'image' => $path
        ]);
        return response()->json([
            'message' => 'Quote added successfully',
        ]);
    }
    public function updateQuote(Request $request)
    {
        $image = request()->file('image');
        if ($image) {
            $path = $image->store('quote');
            $image = $path;
        } else {
            $image = Str::remove(env("STORAGE_PATH"), $request['image']);
        }
        $quote = Quote::where('id', $request['quoteId'])->first();

        $quote->update([
            'quote' => ['en' => $request['quote_en'], 'ka' => $request['quote_ka']],
            'image' => $image
        ]);

        return response()->json([
            'message' => 'Quote updates successfully',
        ]);
    }

    public function deleteQuote(Request $request)
    {
        $movie = Quote::where('id', $request['quoteId'])->first();

        $movie->delete();

        return response()->json([
            'message' => 'Quote Deleted Successfully'
        ]);
    }
}
