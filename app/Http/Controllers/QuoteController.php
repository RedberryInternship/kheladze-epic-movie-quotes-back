<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function createQuote(Request $request)
    {
        $path = request()->file('image')->store('quotes');
        Quote::create([
            'movie_id' => $request['movieId'],
            'quote' => ['en' => $request['quote_en'], 'ka' => $request['quote_ka']],
            'image' => env('STORAGE_PATH') . $path
        ]);

        return response()->json([
            'message' => 'Quote added successfully'
        ]);
    }
    public function updateQuote(Request $request)
    {
        $image = request()->file('image');
        if ($image) {
            $path = $image->store('quote');
            $image = env('STORAGE_PATH') . $path;
        } else {
            $image = $request['image'];
        }
        $quote = Quote::where('id', $request['quoteId'])->first();

        $quote->update([
            'quote' => ['en' => $request['quote_en'], 'ka' => $request['quote_ka']],
            'image' => $image
        ]);

        return response()->json([
            'message' => 'Quote updates successfully',
            'quote' => $quote
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
