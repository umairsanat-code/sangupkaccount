<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaUmrahBooking;
use App\Models\VisaUmrahDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VisaUmrahDocumentController extends Controller
{
    public function store(Request $request, $bookingId)
    {
        $booking = VisaUmrahBooking::findOrFail($bookingId);

        $data = $request->validate([
            'label' => 'nullable|string',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        $file = $request->file('document');
        $dir = 'visa_umrah/booking_' . $booking->id;
        $path = $file->store($dir, 'public');

        VisaUmrahDocument::create([
            'booking_id' => $booking->id,
            'label' => $data['label'] ?? $file->getClientOriginalName(),
            'file_path' => $path,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Document uploaded.']);
    }

    public function destroy($bookingId, $documentId)
    {
        $document = VisaUmrahDocument::where('booking_id', $bookingId)->findOrFail($documentId);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted.']);
    }
}