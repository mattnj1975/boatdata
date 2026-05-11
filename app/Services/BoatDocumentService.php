<?php

namespace App\Services;

use App\Models\BoatFile;
use App\Models\BoatNotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoatDocumentService
{
    public function addNote(int $boatId, string $note): BoatNotes
    {
        return BoatNotes::create([
            'boat_id' => $boatId,
            'user_id' => Auth::id(),
            'note' => $note,
        ]);
    }

    public function updateNote(int $noteId, string $note): ?BoatNotes
    {
        $boatNote = BoatNotes::find($noteId);

        if (!$boatNote) {
            return null;
        }

        $boatNote->user_id = Auth::id();
        $boatNote->note = $note;
        $boatNote->save();

        return $boatNote;
    }

    public function addFile(Request $request): BoatFile
    {
        $boatFile = new BoatFile();
        $boatFile->boat_id = $request->boat_id;
        $boatFile->user_id = Auth::id();

        $this->storeUploadedFile($request, $boatFile);

        $boatFile->save();

        return $boatFile;
    }

    public function updateFile(Request $request): ?BoatFile
    {
        $boatFile = BoatFile::find($request->file_id);

        if (!$boatFile) {
            return null;
        }

        $boatFile->user_id = Auth::id();

        $this->storeUploadedFile($request, $boatFile);

        $boatFile->save();

        return $boatFile;
    }

    public function deleteFile(int $id): bool
    {
        $file = BoatFile::find($id);

        if (!$file) {
            return false;
        }

        if ($file->file && file_exists($file->file)) {
            unlink($file->file);
        }

        $file->delete();

        return true;
    }

    private function storeUploadedFile(Request $request, BoatFile $boatFile): void
    {
        if (!$request->hasFile('file')) {
            return;
        }

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $filename = time() . '.' . $ext;
        $originalFilename = $file->getClientOriginalName();

        $file->move('boat_files', $filename);

        $boatFile->file = 'boat_files/' . $filename;
        $boatFile->file_name = $originalFilename;
    }
}