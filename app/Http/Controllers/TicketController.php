<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // User biasa bisa melihat tiket mereka
    public function index()
    {
        $tickets = auth()->user()->role === 'admin'
            ? Ticket::with('user', 'comments.user')->get()
            : Ticket::where('user_id', auth()->id())->with('comments.user')->get();

        return response()->json($tickets);
    }

    // User bisa membuat tiket baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $ticket = auth()->user()->tickets()->create($validated);
        return response()->json(['message' => 'Ticket created successfully', 'ticket' => $ticket], 201);
    }

    // Melihat detail tiket
    public function show(Ticket $ticket)
    {
        if (auth()->user()->role !== 'admin' && auth()->id() !== $ticket->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($ticket->load('user', 'comments.user'));
    }

    // Admin bisa mengupdate status tiket
    public function updateStatus(Request $request, Ticket $ticket)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        $ticket->update(['status' => $request->status]);
        return response()->json(['message' => 'Ticket status updated', 'ticket' => $ticket]);
    }

    // Menambahkan komentar ke tiket (user & admin)
    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate(['comment' => 'required|string']);

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment
        ]);

        return response()->json(['message' => 'Comment added', 'comment' => $comment]);
    }

    // Admin bisa menghapus tiket
    public function destroy(Ticket $ticket)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted']);
    }
}
